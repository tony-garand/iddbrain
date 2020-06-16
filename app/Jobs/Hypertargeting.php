<?php

namespace brain\Jobs;

use brain\Http\HypertargetingConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SSH;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Storage;

class Hypertargeting implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $hypertargeting_id;
	protected $deploy_type;

	const DEPLOY_TYPE_NEW = 1;
	const DEPLOY_TYPE_UPDATE = 2;
	const DEPLOY_TYPE_DELETE = 3;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($hypertargeting_id, $deploy_type=1) {
		$this->hypertargeting_id = $hypertargeting_id;
		$this->deploy_type = $deploy_type;
	}

	private function storeLocalConfig() {
		Storage::disk('local')->put(
			'json/data_' . $this->hypertargeting_id . '.json',
			HypertargetingConfig::export($this->hypertargeting_id));
	}

	private function deleteLocalConfig() {
		Storage::disk('local')->delete('json/data_' . $this->hypertargeting_id . '.json');
	}

	private function sshCloneRepo($template, $repo_name) {
		SSH::into('hypertargeting')->run([
			'git clone -b master ' . $template->repo_url . ' ' . $template->server_path . strtolower($repo_name)
		], function($line) {
			Log::info('Server\SSH: ' . $line.PHP_EOL);
		});
	}

	private function sshPutConfig($template, $repo_name) {
		SSH::into('hypertargeting')->put(
			Storage::disk('local')->path('json/data_' . $this->hypertargeting_id . '.json'), $template->server_path . strtolower($repo_name) . '/config.json');
	}

	private function sshRemoveConfig($template, $repo_name) {
		SSH::into('hypertargeting')->run([
			'rm ' . $template->server_path . strtolower($repo_name) . '/config.json'
		], function($line) {
			Log::info('Server\SSH: ' . $line.PHP_EOL);
		});
	}

	private function sshPullRepo($template, $repo_name) {
		SSH::into('hypertargeting')->run([
			'cd ' . $template->server_path . strtolower($repo_name),
			'/usr/bin/git pull'
		], function($line) {
			Log::info('Server\SSH: ' . $line.PHP_EOL);
		});
	}

	private function sshRunBuild($template, $repo_name) {
		SSH::into('hypertargeting')->run([
			'cd ' . $template->server_path . strtolower($repo_name),
			'/bin/npm install',
			'/bin/npm run prod'
		], function($line) {
			Log::info('Server\SSH: ' . $line.PHP_EOL);
		});
	}

	private function sshRunServerCommand($commandNumber, $template, $repo_name) {
		$commandLabel = 'server_command_' . $commandNumber;
		if ($template->$commandLabel) {
			SSH::into('hypertargeting')->run([
				'cd ' . $template->server_path . strtolower($repo_name),
				$template->$commandLabel
			], function($line) {
				Log::info('Server\SSH: ' . $line.PHP_EOL);
			});
		}
	}

	private function sshRunAllServerCommands($template, $repo_name) {
		for ($i = 1; $i <= 4; $i++) {
			$this->sshRunServerCommand($i, $template, $repo_name);
		}
	}

	private function sshDeploy($template, $repo_name) {
		$this->sshCloneRepo($template, $repo_name);
		$this->sshPutConfig($template, $repo_name);
		$this->sshRunBuild($template, $repo_name);
		$this->sshRunAllServerCommands($template, $repo_name);
	}

	private function sshUpdate($template, $repo_name) {
		$this->sshRemoveConfig($template, $repo_name);
		$this->sshPutConfig($template, $repo_name);
		$this->sshPullRepo($template, $repo_name);
		$this->sshRunBuild($template, $repo_name);
		$this->sshRunAllServerCommands($template, $repo_name);
	}

	private function updateInstanceStatus($status) {
		DB::table('hypertargeting')
			->where('id', $this->hypertargeting_id)
			->update([
				'status' => $status,
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);
	}

	private function sshDelete($template, $repo_name) {
		SSH::into('hypertargeting')->run([
			'rm -rf ' . $template->server_path . strtolower($repo_name)
		], function($line) {
			Log::info('Server\SSH: ' . $line.PHP_EOL);
		});
	}

	private function logIncorrectDeployType() {
		Log::info('Hypertargeting Job: Wrong Deploy type: ' . $this->deploy_type);
	}

	private function doUpdateOrDeploy($template, $repo_name) {
		$this->updateInstanceStatus(HypertargetingConfig::STATUS_DEPLOYING);
		$this->storeLocalConfig();
		if ($this->deploy_type == self::DEPLOY_TYPE_NEW) {
			$this->sshDeploy($template, $repo_name);
		} else {
			$this->sshUpdate($template, $repo_name);
		}
		$this->updateInstanceStatus(HypertargetingConfig::STATUS_DEPLOYED);
		$this->deleteLocalConfig();
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {

		set_time_limit(0);

		$repo_name = HypertargetingConfig::getProperty($this->hypertargeting_id, 'repo_name');
		$template = HypertargetingConfig::getTemplateByInstance($this->hypertargeting_id);
		if ($repo_name && $template) {
			switch($this->deploy_type) {
				case self::DEPLOY_TYPE_NEW:
					$this->doUpdateOrDeploy($template, $repo_name);
					break;
				case self::DEPLOY_TYPE_UPDATE:
					$this->doUpdateOrDeploy($template, $repo_name);
					break;
				case self::DEPLOY_TYPE_DELETE:
					$this->sshDelete($template, $repo_name);
					HypertargetingConfig::delete($this->hypertargeting_id);
					break;
				default:
					$this->logIncorrectDeployType();
			}
		}

	}

}
