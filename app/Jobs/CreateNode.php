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
use Illuminate\Foundation\Bus\DispatchesJobs;

class CreateNode implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DispatchesJobs;

	protected $node_id;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($node_id) {
		$this->node_id = $node_id;
	}

	public function handle() {

		set_time_limit(0);

		$node = DB::table('nodes')->where('id', $this->node_id)->first();

		if ($node) {

			// do we have an IP? if not, we need to query the API to see if we have one yet, if not, we need to exit and trigger ourself in 30 seconds..
			if (!$node->ip) {

				Log::info('Jobs\CreateNode : call self w/ delay... ');
				$this->dispatch(new CreateNode($this->node_id));

			} else {

				// we have an ip and this node isnt already provisioned?

			}

		}


	}
}
