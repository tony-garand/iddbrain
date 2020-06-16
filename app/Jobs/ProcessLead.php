<?php

namespace brain\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class ProcessLead implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $lead_id;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($lead_id) {
		$this->lead_id = $lead_id;
	}

	private function url2png_v6($url, $args) {
		$URL2PNG_APIKEY = env('URL2PNG_APIKEY');
		$URL2PNG_SECRET = env('URL2PNG_SECRET');
		$options['url'] = urlencode($url);
		$options += $args;
		foreach($options as $key => $value) { $_parts[] = "$key=$value"; }

		$query_string = implode("&", $_parts);
		$TOKEN = md5($query_string . $URL2PNG_SECRET);

		return "https://api.url2png.com/v6/$URL2PNG_APIKEY/$TOKEN/png/?$query_string";
	}

	public function handle() {

		Log::info('Jobs\ProcessLead: starting..');

		set_time_limit(0);
		$lead = DB::table('leads')->where('id', $this->lead_id)->first();

		// screenshots ////////////////////////////////////////////////////////////////////////////

		$options['viewport'] = "1024x640";
		$ss_1 = file_get_contents($this->url2png_v6($lead->url, $options));
		file_put_contents(env('SS_UPLOAD_PATH') . '/' . $lead->id . '_1024x640.png', $ss_1);

		$options['viewport'] = "360x640";
		$ss_2 = file_get_contents($this->url2png_v6($lead->url, $options));
		file_put_contents(env('SS_UPLOAD_PATH') . '/' . $lead->id . '_360x640.png', $ss_2);

		Log::info('Jobs\ProcessLead: done with screenshots..');

		// /screenshots ///////////////////////////////////////////////////////////////////////////

		// now grab the data from google api..
		$client = new Client();

		$result_desktop = $client->get('https://www.googleapis.com/pagespeedonline/v2/runPagespeed', [
			'query' => [
				'url' => $lead->url,
				'strategy' => 'desktop',
				'key' => env('GOOGLE_API_KEY')
			]
		]);

		// add lead source..
		$source_id = DB::table('lead_sources')->insertGetId(
			[
				'source_type' => 1, // google - desktop
				'source_data' => serialize(json_decode($result_desktop->getBody())),
				'lead_id' => $lead->id,
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$result_mobile = $client->get('https://www.googleapis.com/pagespeedonline/v2/runPagespeed', [
			'query' => [
				'url' => $lead->url,
				'strategy' => 'mobile',
				'key' => env('GOOGLE_API_KEY')
			]
		]);

		// add lead source..
		$source_id = DB::table('lead_sources')->insertGetId(
			[
				'source_type' => 2, // google - mobile
				'source_data' => serialize(json_decode($result_mobile->getBody())),
				'lead_id' => $lead->id,
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		DB::table('leads')
			->where('id', $lead->id)
			->update([
				'google_scan_status' => 9,
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		Log::info('Jobs\ProcessLead: done with google scans..');

		// should we rollup status?
		$lead_check = DB::table('leads')->where('id', $lead->id)->first();
		if ($lead_check) {
			if ( ($lead_check->yext_scan_status == 0) || ($lead_check->yext_scan_status == 9) ) {
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
}
