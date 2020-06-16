<?php

namespace brain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Yext implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;
	protected $lead_id;
	protected $name;
	protected $address;
	protected $city;
	protected $state;
	protected $zip;
	protected $phone;
	protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lead_id=null, $name="Michael S. Mead, Jr. DDS Inc.", $address="12981 Cleveland Ave NW", $city="Uniontown", $state="OH", $zip="44685", $phone="3306992523", $url="")
    {
		$this->lead_id = $lead_id;
		$this->name = $name;
		$this->address = $address;
		$this->city = $city;
		$this->state = $state;
		$this->zip = $zip;
		$this->phone = $phone;
		$this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
	public function handle()
	{

		Log::info('Jobs\Yext: queuing request.. ');

		$merged_address = $this->address . ", " . $this->city . ", " . $this->state . " " . $this->zip;

		$uri = "https://api.yext.com/v2/accounts/me/scan?api_key=" . env('YEXT_API_KEY') . "&v=20161012";
		$client = new \GuzzleHttp\Client(['headers' => [ 'Content-Type' => 'application/json' ]]);
		$result = $client->post($uri, [
				'body' => json_encode([
				'name' => $this->name,
				'address' => $merged_address,
				'phone' => $this->phone
			])
		]);
		$data = json_decode($result->getBody());

		$job_id = $data->response->jobId;

		$analysis_busname = preg_replace("/[^A-Za-z0-9 ]/", '', strtolower($this->name));
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

		if ($this->url) {
			// TODO: scrub the URL..
		}

		if ($this->lead_id) {

			DB::table('leads')
				->where('id', $this->lead_id)
				->update([
					'scan_job_id' => $job_id,
					'name' => $this->name,
					'address' => $this->address,
					'city' => $this->city,
					'state' => $this->state,
					'zip' => $this->zip,
					'phone' => $this->phone,
					'yext_scan_status' => 1,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);

		} else {

			$lead_id = DB::table('leads')->insertGetId(
				[
					'scan_job_id' => $job_id,
					'name' => $this->name,
					'url' => $this->url,
					'address' => $this->address,
					'city' => $this->city,
					'state' => $this->state,
					'zip' => $this->zip,
					'phone' => $this->phone,
					'analysis_key' => $analysis_key,
					'overall_status' => 0,
					'yext_scan_status' => 1,
					'google_scan_status' => (($this->url) ? 1 : 0),
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
			$this->lead_id = $lead_id;

		}

		foreach ($data->response->sites as $site) {
			$lead_listing_id = DB::table('lead_listings')->insertGetId(
				[
					'lead_id' => $this->lead_id,
					'is_missing' => 1,
					'listing_type' => $site->name,
					'listing_site_id' => $site->siteId,
					'status' => 0,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
		}

		// results
		$this->dispatch(new YextResults($job_id));

		// if we have a url, we need to queue those up..
		if ($this->url) {
//			$this->dispatch(new GooglePageSpeed($lead_id));
		}

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

}
