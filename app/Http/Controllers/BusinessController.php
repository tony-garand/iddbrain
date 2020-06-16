<?php

namespace brain\Http\Controllers;

use brain\Jobs\Geocoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BusinessController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		$businesses = DB::table('businesses')
					->select('businesses.*')
					->orderBy('Company')
					->get();
		return view('businesses.index', ['businesses' => $businesses]);
	}

	public function geocode() {
		echo "geocoding.. <br/>";
		$businesses = DB::table('businesses')
					->select('businesses.*')
					->whereNull('lat')
					->whereNull('lng')
					->orderBy('Company')
					->limit(5000)
					->get();
		foreach ($businesses as $b) {
			$this->dispatch(new Geocoder($b->id));
		}
		exit;
	}

	public function queue_scan(Request $request, $business_id) {
		$business = DB::table('businesses')->select('businesses.*')->where('businesses.id', $business_id)->first();
		$client = new \GuzzleHttp\Client();
		$result = $client->post('https://www.optimizelocation.com/partner/IDdigital/listing-report.html', [
			'form_params' => [
				'name' => $business->Company,
				'address' => $business->Address,
				'city' => $business->City,
				'state' => $business->Mailing_State,
				'zip' => $business->Zip,
				'phone' => $business->Phone
			]
		]);
		$request->session()->flash("status", "Business queued successfully!");
		return redirect('/businesses/view/'.$business_id);
	}

	public function add() {
	}

	public function view(Request $request, $id) {
		$business = DB::table('businesses')
					->select('businesses.*')
					->where('businesses.id', $id)
					->first();
		return view('businesses.view', ['business' => $business]);
	}

	public function update(Request $request, $id) {
		DB::table('businesses')
			->where('id', $id)
			->update([
				'Company' => $request->get('Company'),
				'Address' => $request->get('Address'),
				'City' => $request->get('City'),
				'Mailing_State' => $request->get('Mailing_State'),
				'Zip' => $request->get('Zip'),
				'PRIMARY_SIC_DESC' => $request->get('PRIMARY_SIC_DESC'),
				'CountyName' => $request->get('CountyName'),
				'FULLNAME1' => $request->get('FULLNAME1'),
				'Phone' => $request->get('Phone'),
				'URL' => $request->get('URL'),
				'status' => $request->get('status'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);
		$request->session()->flash("status", "Business updated successfully!");
		return redirect('/businesses');
	}

	public function save(Request $request) {
	}

}