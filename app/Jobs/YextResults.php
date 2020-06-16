<?php

namespace brain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class YextResults implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;
    protected $job_id;
	protected $max_tries;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($job_id)
    {
		$this->job_id = $job_id;
		$this->max_tries = 5;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
	public function handle()
	{

		Log::info('Jobs\YextResults: looking at results.. ');

		set_time_limit(600);
		$rescan = 0;

		$lead = DB::table('leads')->where('scan_job_id', $this->job_id)->first();
		if ($lead) {
			$lead_listings = DB::table('lead_listings')->where('lead_id', $lead->id)->where('status', 0)->get();
			foreach ($lead_listings as $lead_listing) {
				$uri = "https://api.yext.com/v2/accounts/me/scan/" . $this->job_id . "/" . $lead_listing->listing_site_id . "?api_key=" . env('YEXT_API_KEY') . "&v=20161012";
				try {
					$client = new \GuzzleHttp\Client();
					$result = $client->get($uri, []);
					$data = json_decode($result->getBody());
					$status = $data->response->status;

					$current_tries = $lead_listing->tries;
					$new_tries = $lead_listing->tries + 1;

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

								'tries' => $new_tries,

								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);

					} else {

						if ($status == "SCAN_IN_PROGRESS") {

							// need to wait on this overall status..
							// things are still scanning..

							if ($new_tries > $this->max_tries) {
								DB::table('lead_listings')
									->where('id', $lead_listing->id)
									->update([
										'status' => -2,
										'is_missing' => 1,
										'raw_data' => serialize($data->response),
										'tries' => $new_tries,
										'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
									]
								);
							} else {
								DB::table('lead_listings')
									->where('id', $lead_listing->id)
									->update([
										'status' => 0,
										'raw_data' => serialize($data->response),
										'tries' => $new_tries,
										'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
									]
								);
								$rescan = 1;
							}

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
					Log::error('Jobs\YextResults: Guzzle error!');
				}
			}

			// check all statuses and see if they're all non-zero - if they are, we can close this out..
			$count_waiting = DB::table('lead_listings')->where('lead_id', $lead->id)->where('status', 0)->count();
			if ($count_waiting == 0) {
				DB::table('leads')
					->where('id', $lead->id)
					->update([
						'yext_scan_status' => 9,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);

				// should we rollup status?
				$lead_check = DB::table('leads')->where('id', $lead->id)->first();
				if ($lead_check) {
					if ( ($lead_check->google_scan_status == 0) || ($lead_check->google_scan_status == 9) ) {
						DB::table('leads')
							->where('id', $lead->id)
							->update([
								'overall_status' => 1,
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);
					}
				}

			} else {
				$rescan = 1;
			}

		}

		if ($rescan == 1) {
			Log::info('Jobs\YextResults: rescan, not done yet..');
			// we should queue the job up again..
			sleep(10);
			$this->dispatch(new YextResults($this->job_id));
		} else {
			Log::info('Jobs\YextResults: done..  updating totals.. ');

			$count_waiting = DB::table('lead_listings')->where('lead_id', $lead->id)->where('status', 0)->count();
			if ($count_waiting == 0) {
				DB::table('leads')
					->where('id', $lead->id)
					->update([
						'yext_scan_status' => 9,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
				// should we rollup status?
				$lead_check = DB::table('leads')->where('id', $lead->id)->first();
				if ($lead_check) {
					if ( ($lead_check->google_scan_status == 0) || ($lead_check->google_scan_status == 9) ) {
						DB::table('leads')
							->where('id', $lead->id)
							->update([
								'overall_status' => 1,
								'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
							]
						);
					}
				}
			}

			// TODO: we need to update the overall totals..
			// get total of lead_listing types..
			// get count()s for each column type: match_phone, match_address, match_name
			// update the lead record w/ the averages

			$count_listings_total = DB::table('lead_listings')->where('lead_id', $lead->id)->count();

			$count_match_phone = DB::table('lead_listings')->where('lead_id', $lead->id)->where('match_phone', 1)->count();
			$count_match_address = DB::table('lead_listings')->where('lead_id', $lead->id)->where('match_address', 1)->count();
			$count_match_name = DB::table('lead_listings')->where('lead_id', $lead->id)->where('match_name', 1)->count();

			$match_phone_score = (100-(round(($count_match_phone / $count_listings_total),2)*100));
			$match_address_score = (100-(round(($count_match_address / $count_listings_total),2)*100));
			$match_name_score = (100-(round(($count_match_name / $count_listings_total),2)*100));

			$scores = [$match_phone_score, $match_address_score, $match_name_score];
			$match_overall_average = ceil( array_sum($scores) / count($scores) );

			Log::info('Jobs\YextResults: count_listings_total = ' . $count_listings_total);

			Log::info('Jobs\YextResults: count_match_phone = ' . $count_match_phone);
			Log::info('Jobs\YextResults: match_phone_score = ' . $match_phone_score);

			Log::info('Jobs\YextResults: count_match_address = ' . $count_match_address);
			Log::info('Jobs\YextResults: match_address_score = ' . $match_address_score);

			Log::info('Jobs\YextResults: count_match_name = ' . $count_match_name);
			Log::info('Jobs\YextResults: match_name_score = ' . $match_name_score);

			Log::info('Jobs\YextResults: match_overall_average = ' . $match_overall_average);

			DB::table('leads')
				->where('id', $lead->id)
				->update([
					'listing_overall_percentage' => $match_overall_average,
					'listing_business_name_percentage' => $match_name_score,
					'listing_address_percentage' => $match_address_score,
					'listing_phone_number_percentage' => $match_phone_score,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);

		}

	}

}