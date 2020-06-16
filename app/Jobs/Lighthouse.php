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
use Symfony\Component\Process\Process;

class Lighthouse implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $ada_id;
	public $timeout = 300;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($ada_id) {
		$this->ada_id = $ada_id;
	}

	public function handle() {

		set_time_limit(0);
		$ada = DB::table('adas')->where('id', $this->ada_id)->first();

		if ($ada) {

			Log::info('Jobs\Lighthouse : starting lighthouse request... ');

			$domain = $ada->domain;
			$shortcode = $ada->key;

			$process = new Process(env('LIGHTHOUSE_BIN', 'lighthouse') . ' --chrome-flags="--no-sandbox --headless" ' . $domain . ' --output json --output html --output-path ' . env('LIGHTHOUSE_STORAGE') . '/' . $shortcode . '.json');
			$process->setTimeout(3600);
			$process->run();

			if ($process->isSuccessful()) {
				DB::table('adas')
					->where('id', $ada->id)
					->update([
						'status' => 1,
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			}

			Log::info('Jobs\Lighthouse :' . $process->getOutput());

		}


	}
}
