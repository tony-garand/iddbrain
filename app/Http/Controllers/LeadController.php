<?php

namespace brain\Http\Controllers;

use brain\Jobs\ProcessLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Spatie\Browsershot\Browsershot;
use brain\Jobs\Yext;

class LeadController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		$leads = DB::table('leads')
					->select('leads.*')
					->orderBy('name')
					->get();
		return view('leads.index', ['leads' => $leads]);
	}

	public function update(Request $request, $id) {

		$this->validate($request, [
			'name' => 'required',
			'analysis_key' => 'required|unique:leads,analysis_key,'.$id
		]);

		if (trim($request->get('url'))) {
			$scrubbed_url = strtolower($request->get('url'));
			if (strpos($scrubbed_url, 'http://') === false) {
				if (strpos($scrubbed_url, 'https://') === false) {
					$scrubbed_url = 'http://' . $scrubbed_url;
				}
			}
			$has_domain = 1;
		} else {
			$has_domain = 0;
			$scrubbed_url = "";
		}

		if ($request->get('run_rescan') == "on") {
			$rescan = 1;
		} else {
			$rescan = 0;
		}

		DB::table('leads')
			->where('id', $id)
			->update([
				'name' => $request->get('name'),
				'address' => $request->get('address'),
				'city' => $request->get('city'),
				'state' => $request->get('state'),
				'zip' => $request->get('zip'),
				'phone' => $request->get('phone'),
				'url' => $scrubbed_url,
				'analysis_key' => $request->get('analysis_key'),
				'contact_name' => $request->get('contact_name'),
				'contact_phone' => $request->get('contact_phone'),
				'contact_email' => $request->get('contact_email'),
				'listing_overall_percentage' => $request->get('listing_overall_percentage'),
				'listing_business_name_percentage' => $request->get('listing_business_name_percentage'),
				'listing_address_percentage' => $request->get('listing_address_percentage'),
				'listing_phone_number_percentage' => $request->get('listing_phone_number_percentage'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		if ($rescan == 1) {

			DB::table('leads')
				->where('id', $id)
				->update([
					'overall_status' => 0,
					'google_scan_status' => (($has_domain) ? 1 : 0),
					'yext_scan_status' => 0
				]
			);

			DB::table('lead_sources')->where('lead_id', $id)->whereIn('source_type', [1, 2])->delete();
			DB::table('lead_listings')->where('lead_id', $id)->delete();

			$this->dispatch(new Yext($id, $request->get('name'), $request->get('address'), $request->get('city'), $request->get('state'), $request->get('zip'), $request->get('phone'), $scrubbed_url));

			if ($has_domain) {
				$this->dispatch(new ProcessLead($id));
			}

		}

		$request->session()->flash("status", "Lead updated successfully!");
		return redirect('/leads');
	}

	public function view(Request $request, $id) {
		$lead = DB::table('leads')->where('id', $id)->first();
		$source_google_desktop = DB::table('lead_sources')->where([['lead_id', $id], ['source_type', 1]])->first();
		$source_google_mobile = DB::table('lead_sources')->where([['lead_id', $id], ['source_type', 2]])->first();
		$source_yext = DB::table('lead_sources')->where([['lead_id', $id], ['source_type', 3]])->first();
		$listings = DB::table('lead_listings')->where([['lead_id', $id]])->get();
		$business = DB::table('businesses')->where([['id', $lead->business_id]])->first();

		return view('leads.view', ['lead' => $lead, 'source_google_desktop' => $source_google_desktop, 'source_google_mobile' => $source_google_mobile, 'source_yext' => $source_yext, 'listings' => $listings, 'business' => $business]);
	}

	public function delete(Request $request, $id) {
		DB::table('lead_listings')->where('lead_id', $id)->delete();
		DB::table('lead_sources')->where('lead_id', $id)->delete();
		DB::table('leads')->where('id', $id)->delete();
		$request->session()->flash("status", "Lead deleted successfully!");
		return redirect('/leads');
	}

	public function save(Request $request) {

		$this->validate($request, [
			'name' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'zip' => 'required',
			'phone' => 'required',
			'analysis_key' => 'required|unique:leads,analysis_key'
		]);

		if (trim($request->get('url'))) {
			$scrubbed_url = strtolower($request->get('url'));
			if (strpos($scrubbed_url, 'http://') === false) {
				if (strpos($scrubbed_url, 'https://') === false) {
					$scrubbed_url = 'http://' . $scrubbed_url;
				}
			}
			$has_domain = 1;
		} else {
			$has_domain = 0;
			$scrubbed_url = "";
		}

		$lead_id = DB::table('leads')->insertGetId(
			[
				'name' => $request->get('name'),
				'url' => $scrubbed_url,

				'address' => $request->get('address'),
				'city' => $request->get('city'),
				'state' => $request->get('state'),
				'zip' => $request->get('zip'),
				'phone' => $request->get('phone'),

				'analysis_key' => $request->get('analysis_key'),
				'contact_name' => $request->get('contact_name'),
				'contact_phone' => $request->get('contact_phone'),
				'contact_email' => $request->get('contact_email'),

				'overall_status' => 0,
				'google_scan_status' => (($has_domain) ? 1 : 0),
				'yext_scan_status' => 0,

				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		if ($lead_id) {

			// get data from yext api...

			$this->dispatch(new Yext($lead_id, $request->get('name'), $request->get('address'), $request->get('city'), $request->get('state'), $request->get('zip'), $request->get('phone'), $scrubbed_url));

//			$client = new \GuzzleHttp\Client();
//			$result = $client->post('https://www.optimizelocation.com/partner/IDdigital/listing-report.html', [
//				'form_params' => [
//					'name' => $request->get('name'),
//					'address' => $request->get('address'),
//					'city' => $request->get('city'),
//					'state' => $request->get('state'),
//					'zip' => $request->get('zip'),
//					'phone' => $request->get('phone')
//				]
//			]);

			if ($has_domain) {
				$this->dispatch(new ProcessLead($lead_id));
			}

			$request->session()->flash("status", "Lead created successfully!");
			return redirect('/leads');
		} else {
			$request->session()->flash("status", "Something bad happened!");
			return redirect('/');
		}

	}

}