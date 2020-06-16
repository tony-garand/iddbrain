<?php

namespace brain\Http\Controllers;

use brain\Jobs\Lighthouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AdaController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		$adas = DB::table('adas')
					->select('adas.*')
					->orderBy('name')
					->get();
		return view('adas.index', ['adas' => $adas]);
	}

	public function ada() {

		$domain = "https://www.iddigital.us";
		$shortcode = "iddigital";

		$process = new Process(env('LIGHTHOUSE_BIN', 'lighthouse') . ' --chrome-flags="--no-sandbox --headless" ' . $domain . ' --output json --output html --output-path ' . env('LIGHTHOUSE_STORAGE') . '/' . $shortcode . '.json');
		$process->setTimeout(3600);
		$process->run();

		// executes after the command finishes
		if (!$process->isSuccessful()) {
			throw new ProcessFailedException($process);
		}

		echo "<pre>";
		echo $process->getOutput();
		echo "</pre>";

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
				'overall_status' => 0,
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		DB::table('lead_sources')->where('lead_id', $id)->whereIn('source_type', [1, 2])->delete();

		if ($has_domain) {
			$this->dispatch(new ProcessLead($id));
		} else {
			DB::table('leads')
				->where('id', $id)
				->update([
					'overall_status' => 1,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
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
			'url' => 'required',
			'key' => 'required|unique:adas,key'
		]);

		$scrubbed_url = strtolower($request->get('url'));
		if (strpos($scrubbed_url, 'http://') === false) {
			if (strpos($scrubbed_url, 'https://') === false) {
				$scrubbed_url = 'http://' . $scrubbed_url;
			}
		}

		$ada_id = DB::table('adas')->insertGetId(
			[
				'name' => $request->get('name'),
				'domain' => $scrubbed_url,
				'key' => $request->get('key'),
				'status' => 0,
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		if ($ada_id) {
			$this->dispatch(new Lighthouse($ada_id));
			$request->session()->flash("status", "ADA lead created successfully!");
			return redirect('/adas');
		} else {
			$request->session()->flash("status", "Something bad happened!");
			return redirect('/');
		}

	}

}