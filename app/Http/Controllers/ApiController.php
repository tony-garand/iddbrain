<?php

namespace brain\Http\Controllers;

use brain\Jobs\ProcessLead;
use brain\Jobs\Yext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Browser\Casper;
use Twilio\Rest\Client;
use Twilio\Twiml;
use GuzzleHttp\Client as GuzzleClient;

class ApiController extends Controller {

	public function __construct() {
	}

	public function index() {
	}

	private function rander($length=10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function yext_publisher_list() {
		$uri = "https://api.yext.com/v2/accounts/me/powerlistings/publishers?api_key=" . env('YEXT_API_KEY') . "&v=20161012";
		try {
			$client = new \GuzzleHttp\Client();
			$result = $client->get($uri, []);
			$data = json_decode($result->getBody());
			$keys = [];
			if ($data) {
				foreach ($data->response->publishers as $publisher) {
					if (in_array('US', $publisher->supportedCountries)) {
						$keys[] = $publisher->id;
					}
				}
			}
			return array(true, $keys);
		} catch (\GuzzleHttp\Exception\ClientException $e) {
			return array(false, null);
		}
	}

	public function yext_results($job_id="") {

		set_time_limit(600);
		$rescan = 0;

		$lead = DB::table('leads')->where('scan_job_id', $job_id)->first();
		if ($lead) {
			$lead_listings = DB::table('lead_listings')->where('lead_id', $lead->id)->where('status', 0)->get();
			foreach ($lead_listings as $lead_listing) {
				$uri = "https://api.yext.com/v2/accounts/me/scan/" . $job_id . "/" . $lead_listing->listing_site_id . "?api_key=" . env('YEXT_API_KEY') . "&v=20161012";
				try {
					$client = new \GuzzleHttp\Client();
					$result = $client->get($uri, []);
					$data = json_decode($result->getBody());
					$status = $data->response->status;

					if ($status == "LISTING_FOUND") {
						DB::table('lead_listings')
							->where('id', $lead_listing->id)
							->update([
								'status' => 1,
								'is_missing' => 0,

								'listing_name_status' => (($data->response->match_name == 1) ? 1 : 0),
								'listing_address_status' => (($data->response->match_address == 1) ? 1 : 0),
								'listing_phone_status' => (($data->response->match_phone == 1) ? 1 : 0),

								'listing_name' => $data->response->name,
								'listing_address' => $data->response->address . ', ' . $data->response->city . ', ' . $data->response->state . ' ' . $data->response->zip,
								'listing_phone' => $data->response->phone,

								'match_name' => (($data->response->match_name == 1) ? 1 : 0),
								'match_address' => (($data->response->match_address == 1) ? 1 : 0),
								'match_phone' => (($data->response->match_phone == 1) ? 1 : 0),

								'match_name_score' => $data->response->match_name_score,
								'match_address_score' => $data->response->match_address_score,
								'match_phone_score' => $data->response->match_phone_score,
								'raw_data' => serialize($data->response),
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);

					} else {

						if ($status == "SCAN_IN_PROGRESS") {

							// need to wait on this overall status..
							// things are still scanning..

							DB::table('lead_listings')
								->where('id', $lead_listing->id)
								->update([
									'status' => 0,
									'raw_data' => serialize($data->response),
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);
							$rescan = 1;

						} else {

							if ($status == "NO_MATCH") {
								$listing_status = -1;
							} elseif ($status == "SCAN_FAILED") {
								$listing_status = -2;
							} elseif ($status == "SITE_TIMEOUT") {
								$listing_status = -3;
							} else {
								$listing_status = -9;
							}

							DB::table('lead_listings')
								->where('id', $lead_listing->id)
								->update([
									'status' => $listing_status,
									'is_missing' => 1,
									'raw_data' => serialize($data->response),
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);

						}

					}

				} catch (\GuzzleHttp\Exception\ClientException $e) {
					echo "error! <br/>";
				}
			}

			// check all statuses and see if they're all non-zero - if they are, we can close this out..
			$count_waiting = DB::table('lead_listings')->where('lead_id', $lead->id)->where('status', 0)->count();
			if ($count_waiting == 0) {
				DB::table('leads')
					->where('id', $lead->id)
					->update([
						'overall_status' => 1,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			} else {
				$rescan = 1;
			}

		}

		// if rescan is set, we need to recurse..
		if ($rescan == 1) {
			echo "rescan... <br/>";
			// we should queue the job up again..
		}

		exit;
	}

	public function google_name_lookup() {

		set_time_limit(0);

		$client = new GuzzleClient();
		$businesses = DB::table('businesses')->where('status', 1)->where('name_scrubbed', 0)->limit(500)->get();
		foreach ($businesses as $business) {

			$results = $client->get('https://maps.googleapis.com/maps/api/place/findplacefromtext/json', [
				'query' => [
					'input' => $business->Company,
					'inputtype' => 'textquery',
					'fields' => 'formatted_address,name',
					'locationbias' => 'circle:2000@' . $business->lat . ',' . $business->lng,
					'key' => env('GOOGLE_PLACES_API_KEY')
				]
			]);

			$json = json_decode($results->getBody());

			if (@$json->status == "OK") {
				$scrubbed_name = $json->candidates[0]->name;
				echo "previous name = " . $business->Company . ", scrubbed_name = " . $scrubbed_name . "<br/>";
				if ($scrubbed_name) {
					DB::table('businesses')
						->where('id', $business->id)
						->update([
							'Company' => $scrubbed_name,
							'name_scrubbed' => 1,
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
						]
					);
				}
			} else {

				if (@$json->status == "ZERO_RESULTS") {
					DB::table('businesses')
						->where('id', $business->id)
						->update([
							'name_scrubbed' => -1,
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
						]
					);
				} else {
					echo "<pre>";
					print_r($json);
					echo "</pre>";
				}

			}

		}

	}

	public function yext_scan($name="Michael S. Mead, Jr. DDS Inc.", $address="12981 Cleveland Ave NW", $city="Uniontown", $state="OH", $zip="44685", $phone="3306992523") {

		// address info must be passed in..

		$businesses = DB::table('businesses')->where('status', 1)->where('URL', '<>', '')->limit(10)->get();
		foreach ($businesses as $business) {
			$this->dispatch(new Yext(null, $business->Company, $business->Address, $business->City, $business->Mailing_State, $business->Zip, $business->Phone, $business->URL));
		}

//		$this->dispatch(new Yext($name, $address, $city, $state, $zip, $phone));
//		exit;
//
//		$merged_address = $address . ", " . $city . ", " . $state . " " . $zip;
//		echo "merged_address = " . $merged_address . "<br/>";
//
//		$uri = "https://api.yext.com/v2/accounts/me/scan?api_key=" . env('YEXT_API_KEY') . "&v=20161012";
//		$client = new \GuzzleHttp\Client(['headers' => [ 'Content-Type' => 'application/json' ]]);
//		$result = $client->post($uri, [
//			'body' => json_encode([
//				'name' => $name,
//				'address' => $merged_address,
//				'phone' => $phone
//			])
//		]);
//		$data = json_decode($result->getBody());
//
//		$job_id = $data->response->jobId;
//		echo "job_id = " . $job_id . "<br/>";
//
//		$analysis_busname = preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($name));
//		$analysis_parts = explode(' ', $analysis_busname);
//		if (count($analysis_parts) > 1) {
//			$analysis_key = $analysis_parts[0] . "-" . $analysis_parts[1];
//		} else {
//			$analysis_key = $analysis_parts[0];
//		}
//		$match_analysis_key = DB::table('leads')->where('analysis_key', $analysis_key)->first();
//		if ($match_analysis_key) {
//			$analysis_key = $analysis_key . "-" . $this->rander(2);
//		}
//
//		echo "analysis_key = " . $analysis_key . "<br>";
//
//		$lead_id = DB::table('leads')->insertGetId(
//			[
//				'scan_job_id' => $job_id,
//				'name' => $name,
//				'url' => '',
//				'address' => $address,
//				'city' => $city,
//				'state' => $state,
//				'zip' => $zip,
//				'phone' => $phone,
//				'analysis_key' => $analysis_key,
//				'overall_status' => 0,
//				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
//				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
//			]
//		);
//
//		foreach ($data->response->sites as $site) {
//			echo "siteId = " . $site->siteId . "<br/>";
//			echo "name = " . $site->name . "<br/><br/>";
//			$lead_listing_id = DB::table('lead_listings')->insertGetId(
//				[
//					'lead_id' => $lead_id,
//					'is_missing' => 1,
//					'listing_type' => $site->name,
//					'listing_site_id' => $site->siteId,
//					'status' => 0,
//					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
//					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
//				]
//			);
//		}

			// get the scan id (jobId) and then call the response hook

			// https://api.yext.com/v2/accounts/me/scan/[jobId]/FACEBOOK,?api_key=9586c1e4035bd75843408bab76ed4055&v=20161012

		exit;
	}

	public function map_leads(Request $request) {

		$swlat = trim($request->get('swlat'));
		$swlng = trim($request->get('swlng'));
		$nelat = trim($request->get('nelat'));
		$nelng = trim($request->get('nelng'));

		if ( ($nelat) && ($nelng) && ($swlat) && ($swlng) ) {

			$sql = "SELECT * FROM businesses WHERE status = 1 AND 
			(CASE WHEN " . $swlat . " < " . $nelat . "
			        THEN lat BETWEEN " . $swlat . " AND " . $nelat . "
			        ELSE lat BETWEEN " . $nelat . " AND " . $swlat . "
			END)
			AND
			(CASE WHEN " . $swlng . " < " . $nelng . "
			        THEN lng BETWEEN " . $swlng . " AND " . $nelng . "
			        ELSE lng BETWEEN " . $nelng . " AND " . $swlng . "
			END) LIMIT 100";
			$result = DB::SELECT($sql);

			$encoded = json_encode($result);
			echo $encoded;

		} else {
			echo "";
		}
		exit;
	}

	public function cron_messaging_services() {

		$twilio = new Client(env('SMS_SID'), env('SMS_TOKEN'));
		$services = $twilio->messaging->v1->services->read();
		foreach ($services as $service) {

			// check if we already have this messaging_service record.. if we dont, create it. if we do, use that id for looking
			// at the phone numbers associated.

			$messaging_service = DB::table('messaging_services')->where('sid', $service->sid)->first();
			if (!$messaging_service) {
				$messaging_service_id = DB::table('messaging_services')->insertGetId(
					[
						'sid' => $service->sid,
						'name' => $service->friendlyName,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			} else {
				$messaging_service_id = $messaging_service->id;
			}

			DB::table('messaging_service_numbers')->where('messaging_service_id', $messaging_service_id)->delete();
			$phoneNumbers = $twilio->messaging->v1->services($service->sid)->phoneNumbers->read();
			foreach ($phoneNumbers as $phoneNumber) {
				$messaging_service_phone_id = DB::table('messaging_service_numbers')->insertGetId(
					[
						'messaging_service_id' => $messaging_service_id,
						'number' => $phoneNumber->phoneNumber,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			}

		}

		echo "done.";

	}

	public function incoming_yext_scan_email(Request $request) {

		set_time_limit(0);
		Log::info('API\incoming_yext_scan_email .. incoming ... ');

		$html = trim($request->get('html'));
		if ($html) {

			$dom = new DOMDocument;
			@$dom->loadHTML($html);
			$xpath = new DOMXpath($dom);

			$report_url = "";
			$links = $dom->getElementsByTagName('a');
			foreach ($links as $link) {
				if ($link->nodeValue == "here") {
					$report_url = $link->getAttribute('href');
				}
			}

			$bus_name = @$xpath->query("//p[@style='font-weight: bold; padding-top: 20px;']")[0]->nodeValue;
			$bus_phone = @$xpath->query("//p")[3]->nodeValue;
			$bus_address = @$xpath->query("//p[@style='font-weight: bold; padding-top: 20px;']/following-sibling::text()")[0]->nodeValue;
			if ($bus_phone) {
				$bus_phone = trim($bus_phone);
				$bus_phone_clean = preg_replace("/[^0-9]/", "", $bus_phone);
			}
			$bus_name = trim($bus_name);
			$bus_address = trim($bus_address);

			// now get the end uri after the email redirect..
			if ($report_url) {

				$final_url = "";
				$client = new \GuzzleHttp\Client();
				$client->request('GET', $report_url, [
					'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$final_url) {
						if (!$final_url) {
							$final_url = $stats->getHandlerStats()['redirect_url'];
						}
					}
				]);

				// find the businesses record and update the yext_scan_url field with final_url
				$match_business = DB::table('businesses')->where('Company', $bus_name)->where('Address', $bus_address)->where('Phone', $bus_phone_clean)->first();
				if ($match_business) {
					DB::table('businesses')
						->where('id', $match_business->id)
						->update([
							'yext_scan_url' => $final_url,
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
						]
					);

					// now go get the results of the scan via casper and store them..
					Log::info('API\incoming_yext_scan_email .. starting casper ... ');

					$casper = new Casper(env('CASPER', '/usr/bin/'));
					$casper->setOptions([
						'ignore-ssl-errors' => 'yes'
					]);
					$casper->start($final_url);
					$casper->wait(30000);
					$casper->run();
					$casper_html = @$casper->getHtml();

					Log::info('API\incoming_yext_scan_email .. casper received ... ');

					if ($casper_html) {
						$casper_dom = new DOMDocument;
						@$casper_dom->loadHTML($casper_html);
						$casper_xpath = new DOMXpath($casper_dom);

						$yext_overall_pct = @$casper_xpath->query("//span[@class='js-error-rate-percentage']")[0]->nodeValue;
						$yext_busname_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[0]->nodeValue;
						$yext_address_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[1]->nodeValue;
						$yext_phonenum_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[2]->nodeValue;

						if (trim($match_business->URL)) {
							$scrubbed_url = strtolower($match_business->URL);
							if (strpos($scrubbed_url,'http://') === false) {
								if (strpos($scrubbed_url,'https://') === false) {
									$scrubbed_url = 'http://' . $scrubbed_url;
								}
							}
							$has_domain = 1;
						} else {
							$has_domain = 0;
							$scrubbed_url = "";
						}

						$analysis_busname = strtolower($match_business->Company);
						$analysis_parts = explode(' ', $analysis_busname);
						if (count($analysis_parts) > 1) {
							$analysis_key = $analysis_parts[0] . "-" . $analysis_parts[1];
						} else {
							$analysis_key = $analysis_parts[0];
						}
						$match_analysis_key = DB::table('leads')->where('analysis_key', $analysis_key)->first();
						if ($match_analysis_key) {
							$analysis_key = $analysis_key . "-" . $this->rander(2);
						}

						$lead_id = DB::table('leads')->insertGetId(
							[
								'name' => $match_business->Company,
								'business_id' => $match_business->id,
								'url' => $scrubbed_url,
								'analysis_key' => $analysis_key,
								'contact_name' => $match_business->FULLNAME1,
								'contact_phone' => $match_business->Phone,
								'contact_email' => '',

								'address' => $match_business->Address,
								'city' => $match_business->City,
								'state' => $match_business->Mailing_State,
								'zip' => $match_business->Zip,
								'phone' => $match_business->Phone,

								'listing_overall_percentage' => $yext_overall_pct,
								'listing_business_name_percentage' => $yext_busname_pct,
								'listing_address_percentage' => $yext_address_pct,
								'listing_phone_number_percentage' => $yext_phonenum_pct,

								'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);

						$source_id = DB::table('lead_sources')->insertGetId(
							[
								'source_type' => 3, // yext dump
								'source_data' => serialize($casper_html),
								'lead_id' => $lead_id,
								'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);

						foreach (@$casper_xpath->query("//li[@class='js-partner']") as $n) {
							$listing_type = @$n->getAttribute('data-name');
							if ($listing_type) {

								$is_missing = 0;
								$missing_listing = @$casper_xpath->query("a/div/div/div/div[@class='js-missing-listing listing-error-text']", $n);
								foreach ($missing_listing as $m) {
									$is_missing = 1;
								}

								$listing_name_status = 0;
								$listing_name = "";

								$listing_address_status = 0;
								$listing_address = "";

								$listing_phone_status = 0;
								$listing_phone = "";

								if ($is_missing == 0) {
									$listing_name = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-name partner-location-name']", $n)[0]->nodeValue;
									$listing_address = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-address']", $n)[0]->nodeValue;
									$listing_phone = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-phone']", $n)[0]->nodeValue;

									$listing_name_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-name partner-location-name']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
									foreach ($listing_name_status_chk as $chk) {
										$listing_name_status = 1;
									}

									$listing_address_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-address']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
									foreach ($listing_address_status_chk as $chk) {
										$listing_address_status = 1;
									}

									$listing_phone_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-phone']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
									foreach ($listing_phone_status_chk as $chk) {
										$listing_phone_status = 1;
									}
								}

								$lead_listing_id = DB::table('lead_listings')->insertGetId(
									[
										'lead_id' => $lead_id,
										'is_missing' => $is_missing,
										'listing_type' => $listing_type,
										'listing_name_status' => $listing_name_status,
										'listing_address_status' => $listing_address_status,
										'listing_phone_status' => $listing_phone_status,
										'listing_name' => $listing_name,
										'listing_address' => $listing_address,
										'listing_phone' => $listing_phone,
										'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
										'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
									]
								);

							}

						}

						// now run the job for getting google info if we have a domain..
						if ($has_domain == 1) {
							$this->dispatch(new ProcessLead($lead_id));
						} else {
							DB::table('leads')
								->where('id', $lead_id)
								->update([
									'overall_status' => 1,
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);
						}

					} else {
						Log::info('API\incoming_yext_scan_email ... no data from casper?');
					}

				} else {

					// check if this is a one-off lead add..
					$match_oneoff_lead = DB::table('leads')->where('overall_status', 0)->where('name', $bus_name)->where('address', $bus_address)->where('phone', $bus_phone_clean)->first();
					if ($match_oneoff_lead) {

						// now go get the results of the scan via casper and store them..
						Log::info('API\incoming_yext_scan_email .. starting casper ... ');

						$casper = new Casper(env('CASPER', '/usr/bin/'));
						$casper->setOptions([
							'ignore-ssl-errors' => 'yes'
						]);
						$casper->start($final_url);
						$casper->wait(30000);
						$casper->run();
						$casper_html = @$casper->getHtml();

						Log::info('API\incoming_yext_scan_email .. casper received ... ');

						if ($casper_html) {
							$casper_dom = new DOMDocument;
							@$casper_dom->loadHTML($casper_html);
							$casper_xpath = new DOMXpath($casper_dom);

							$yext_overall_pct = @$casper_xpath->query("//span[@class='js-error-rate-percentage']")[0]->nodeValue;
							$yext_busname_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[0]->nodeValue;
							$yext_address_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[1]->nodeValue;
							$yext_phonenum_pct = @$casper_xpath->query("//span[@class='js-error-percentage']")[2]->nodeValue;

							if (trim($match_oneoff_lead->url)) {
								$scrubbed_url = strtolower($match_oneoff_lead->url);
								if (strpos($scrubbed_url,'http://') === false) {
									if (strpos($scrubbed_url,'https://') === false) {
										$scrubbed_url = 'http://' . $scrubbed_url;
									}
								}
								$has_domain = 1;
							} else {
								$has_domain = 0;
								$scrubbed_url = "";
							}

							// update yext overall scores; also we should store the scan url in the leads table since we dont have a business record..
							DB::table('leads')
								->where('id', $match_oneoff_lead->id)
								->update([
									'listing_overall_percentage' => $yext_overall_pct,
									'listing_business_name_percentage' => $yext_busname_pct,
									'listing_address_percentage' => $yext_address_pct,
									'listing_phone_number_percentage' => $yext_phonenum_pct,
									'yext_scan_url' => $final_url,
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);

							$source_id = DB::table('lead_sources')->insertGetId(
								[
									'source_type' => 3, // yext dump
									'source_data' => serialize($casper_html),
									'lead_id' => $match_oneoff_lead->id,
									'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);

							foreach (@$casper_xpath->query("//li[@class='js-partner']") as $n) {
								$listing_type = @$n->getAttribute('data-name');
								if ($listing_type) {

									$is_missing = 0;
									$missing_listing = @$casper_xpath->query("a/div/div/div/div[@class='js-missing-listing listing-error-text']", $n);
									foreach ($missing_listing as $m) {
										$is_missing = 1;
									}

									$listing_name_status = 0;
									$listing_name = "";

									$listing_address_status = 0;
									$listing_address = "";

									$listing_phone_status = 0;
									$listing_phone = "";

									if ($is_missing == 0) {
										$listing_name = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-name partner-location-name']", $n)[0]->nodeValue;
										$listing_address = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-address']", $n)[0]->nodeValue;
										$listing_phone = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-phone']", $n)[0]->nodeValue;

										$listing_name_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-name partner-location-name']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
										foreach ($listing_name_status_chk as $chk) {
											$listing_name_status = 1;
										}

										$listing_address_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-address']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
										foreach ($listing_address_status_chk as $chk) {
											$listing_address_status = 1;
										}

										$listing_phone_status_chk = @$casper_xpath->query("a/div/div/div/div[@class='js-partner-location-phone']/img[@src='https://www.yextstatic.com/partner/public/images/icons/icon-check.svg']", $n);
										foreach ($listing_phone_status_chk as $chk) {
											$listing_phone_status = 1;
										}
									}

									$lead_listing_id = DB::table('lead_listings')->insertGetId(
										[
											'lead_id' => $match_oneoff_lead->id,
											'is_missing' => $is_missing,
											'listing_type' => $listing_type,
											'listing_name_status' => $listing_name_status,
											'listing_address_status' => $listing_address_status,
											'listing_phone_status' => $listing_phone_status,
											'listing_name' => $listing_name,
											'listing_address' => $listing_address,
											'listing_phone' => $listing_phone,
											'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
											'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
										]
									);

								}

							}

							// now run the job for getting google info if we have a domain..
							if ($has_domain == 1) {
								$this->dispatch(new ProcessLead($match_oneoff_lead->id));
							} else {
								DB::table('leads')
									->where('id', $match_oneoff_lead->id)
									->update([
										'overall_status' => 1,
										'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
									]
								);
							}

						} else {
							Log::info('API\incoming_yext_scan_email ... no data from casper?');
						}

					} else {
						Log::info('API\incoming_yext_scan_email ... cant find a match_business record or a one-off lead record!');
					}
				}

			}

		}

		exit;

	}

}
