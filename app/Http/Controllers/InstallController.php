<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use GrahamCampbell\Bitbucket\Facades\Bitbucket;
use SSH;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use brain\Events\NewClientActivity;

class InstallController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index(Request $request) {

		$all_records = 0;
		$bb_page = 1;
		$step = 0;
		$usable_repos = array();

		if ( (Cache::has('bb_usable_repos')) && (!$request->get('fresh')) ) {
			$usable_repos = Cache::get('bb_usable_repos');
		} else {
			while ($all_records < 1) {
				$bitbucket = Bitbucket::Repositories()->users('id-digital')->list(['pagelen' => env('BB_PAGELEN', 100), 'page' => $bb_page]);
				foreach ($bitbucket['values'] as $bb) {
					if ($bb['project']['key'] == "BRAIN") {
						$usable_repos[] = $bb['name'];
					}
				}
				$step = $step + env('BB_PAGELEN', 100);
				$bb_page++;
				if ($bitbucket['size'] <= $step) {
					$all_records = 1;
				}
			}
			$final_repos = array();

			foreach ($usable_repos as $ur) {
				if ($ur == "brain") {
					unset($ur);
				} else {
					$final_repos[] = $ur;
				}
			}
			$usable_repos = $final_repos;
			Cache::put('bb_usable_repos', $usable_repos, 1440);
		}

		$installs = DB::table('servers')
					->join('repos', 'servers.bb_source', '=', 'repos.bb_source')
					->join('owners', 'servers.owner_id', '=', 'owners.id')
                    ->leftJoin('clients', 'servers.client_id', '=', 'clients.id')
					->select('servers.*', 'owners.owner_name', 'owners.owner_email', 'repos.repo_type_id', 'clients.name as client_name')
					->orderBy('site_name')
					->get();

        $clients = DB::table('clients')
            ->select('id', 'name')
            ->get();

		return view('installs.index', ['installs' => $installs, 'usable_repos' => $usable_repos, 'clients' => $clients]);
	}

	public function view(Request $request, $id) {

		$owners = DB::table('owners')
					->orderBy('owner_name')
					->get();

		$clients = DB::table('clients')
                    ->get();

		$install = DB::table('servers')
					->join('repos', 'servers.bb_source', '=', 'repos.bb_source')
					->join('owners', 'servers.owner_id', '=', 'owners.id')
					->select('servers.*', 'owners.owner_name', 'owners.owner_email', 'repos.repo_type_id')
					->where('servers.id', $id)
					->first();
		return view('installs.view', ['install' => $install, 'owners' => $owners, 'clients' => $clients]);

	}

	public function delete(Request $request, $id) {

		$install = DB::table('servers')
					->join('repos', 'servers.bb_source', '=', 'repos.bb_source')
					->join('owners', 'servers.owner_id', '=', 'owners.id')
					->select('servers.*', 'owners.owner_name', 'owners.owner_email', 'repos.repo_type_id')
					->where('servers.id', $id)
					->first();

		if ($install) {
			// delete the files on staging
			SSH::run([
				'rm -rf /var/www/staging/' . strtolower($install->repo_name)
			], function ($line) {
				Log::info('Server\SSH (delete files): ' . $line.PHP_EOL);
			});

			// drop the database on staging
			SSH::run([
				'mysql -uroot -pSt54d9bb -e "DROP DATABASE IF EXISTS ' . strtolower($install->repo_name) . '"'
			], function ($line) {
				Log::info('Server\SSH (delete db): ' . $line.PHP_EOL);
			});

			// delete the server record on brain
			DB::table('server_plugins')->where('server_id', $id)->delete();
			DB::table('servers')->where('id', $id)->delete();
			if(!empty($install->client_id)) {
                event(new NewClientActivity($install->client_id, ' has deleted this install (' . $install->site_name . ').'));
            }
			$request->session()->flash("status", "Install deleted successfully!");
			return redirect('/installs');
		} else {
			$request->session()->flash("status", "Install not deleted successfully!");
			return redirect('/installs');
		}

	}

	public function update(Request $request, $id) {

		$this->validate($request, [
			'site_name' => 'required',
			'domain_url' => 'required'
		]);

		$owner_id = $request->get('owner_id');
		if ($owner_id == 0) {
			$this->validate($request, [
				'owner_email' => 'required',
				'owner_name' => 'required'
			]);

			$owner_id = DB::table('owners')->where('owner_email', $request->get('owner_email'))->value('id');
			if (!$owner_id) {
				$owner_id = DB::table('owners')->insertGetId(
					[
						'owner_name' => $request->get('owner_name'),
						'owner_email' => $request->get('owner_email'),
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			}
		}

		$install = DB::table('servers')->where('id', $id)->first();
		$old_client_id = $install->client_id;

		$site_name = $request->get('site_name');
		$client_id = $request->get('client_id');
		if ($client_id == 0) {
		    $client_id = null;
        }

		$success = DB::table('servers')
			->where('id', $id)
			->update([
				'site_name' => $site_name,
				'domain_url' => $request->get('domain_url'),
				'owner_id' => $owner_id,
				'client_id' => $client_id,
				'stackpath_id' => $request->get('stackpath_id'),
				'node_ip' => $request->get('node_ip'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		if($success && !empty($old_client_id) && ($client_id != $old_client_id)) {
		    if(empty($client_id)) {
                event(new NewClientActivity($old_client_id, ' has removed this install  (<a href="/installs/view/'.$id.'">'.$install->site_name.'</a>) from this client.'));
            } else {
                event(new NewClientActivity($client_id, ' has added this install (<a href="/installs/view/'.$id.'">'.$site_name.'</a>) to this client.'));
            }
        }

		$request->session()->flash("status", "Install updated successfully!");
		return redirect('/installs');
	}

	public function save(Request $request) {

		$this->validate($request, [
			'repo_name' => 'required|unique:servers,repo_name',
			'owner_email' => 'required',
			'bb_source' => 'required',
			'site_name' => 'required',
			'owner_name' => 'required',
			'domain_url' => 'required'
		]);

		// check if we already have this owner, if we do, get their id.. if not, add them..
		$owner_id = DB::table('owners')->where('owner_email', $request->get('owner_email'))->value('id');
		if (!$owner_id) {
			// add owner record..
			$owner_id = DB::table('owners')->insertGetId(
				[
					'owner_name' => $request->get('owner_name'),
					'owner_email' => $request->get('owner_email'),
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
		}

        $site_name = $request->get('site_name');
		$client_id = $request->get('client_id');

		// add server record..
		$server_id = DB::table('servers')->insertGetId(
			[
				'bb_source' => $request->get('bb_source'),
				'repo_name' => strtolower($request->get('repo_name')),
				'site_name' => $site_name,
				'domain_url' => $request->get('domain_url'),
				'node_staging' => $request->get('node_staging'),
				'node_production' => $request->get('node_production'),
				'owner_id' => $owner_id,
                'client_id' => $client_id,
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

        if($client_id > 0) {
            $client_id = null;
        } else {
            event(new NewClientActivity($client_id, ' has created a new install (<a href="/installs/view/'.$server_id.'">'.$site_name.'</a>) and added it to this client.'));
		}

		//exit early because this is all we need to do for existing/no-template installs
		if ($request->get('bb_source') === 'no-template') {
			$request->session()->flash("status", "Install created successfully - you're a pro!");
			return redirect('/installs');
		}

		// get repo type based on bb_source..
		$repo_info = DB::table('repos')->where('bb_source', $request->get('bb_source'))->first();
		if ($repo_info) {
			$repo_type_id = $repo_info->repo_type_id;
		} else {
			// assume craft..
			$repo_type_id = 1;
		}

		if ($server_id) {

			// add any plugins we have...
			if (@$request->selected_plugins) {
				foreach($request->selected_plugins as $selected_plugin) {
					$server_plugin_id = DB::table('server_plugins')->insertGetId(
						[
							'server_id' => $server_id,
							'plugin_id' => ($selected_plugin+0),
							'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
						]
					);
				}
			}

			// run local deploy...
			if ($request->get('node_staging')) {

				$process = new Process('../staging_deploy.sh "' . $request->get('site_name') . '" "' . strtolower($request->get('repo_name')) . '" "staging" "staging.iddigital.me" "' . $request->get('bb_source') . '" "' . $request->get('owner_email') . '" "' . strtolower($request->get('repo_name')) . '.staging.iddigital.me"');
				$process->setTimeout(3600);
				$process->run();
				if (!$process->isSuccessful()) {
					throw new ProcessFailedException($process);
				}

				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// craftcms deployment /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if ($repo_type_id == 1) {

					// core deployment
					SSH::run([
						'git clone -b develop git@bitbucket.org:id-digital/' . strtolower($request->get('repo_name')) . '.git /var/www/staging/' . strtolower($request->get('repo_name')),
						'git clone git@bitbucket.org:id-digital/craftcms.git /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/app',

						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/storage',
						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/cache',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/cache',

						'wget https://brain.iddigital.us/site_uploads/' . $request->get('bb_source') . '.zip -P /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/',
						'unzip /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/' . $request->get('bb_source') . '.zip -d /var/www/staging/' . strtolower($request->get('repo_name')) . '/public',
						'rm -rf /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/__MACOSX',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/uploads',
						'rm -rf /var/www/staging/' . strtolower($request->get('repo_name')) . '/public/' . $request->get('bb_source') . '.zip',

						'cp /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt-default /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'cp /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/db.php-default /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/db.php',
						'cp /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php-default /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php',

						'replace "--DBPASSWORD--" "St54d9bb" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/db.php',
						'replace "--DBNAME--" "' . strtolower($request->get('repo_name')) . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/db.php',

						'replace "--SITEPATH--" "' . strtolower($request->get('repo_name')) . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php',
						'replace "--SITENAME--" "' . strtolower($request->get('repo_name')) . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php',
						'replace "--ENVIRONMENT--" "staging" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php',
						'replace "--ENVIRONMENT_URL--" "staging.iddigital.me" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/config/general.php',

						'replace "--SITENAME--" "' . $request->get('site_name') . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'replace "--SITEPATH--" "' . strtolower($request->get('repo_name')) . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'replace "--OWNEREMAIL--" "' . $request->get('owner_email') . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'replace "--SITEURL--" "' . strtolower($request->get('repo_name')) . '.staging.iddigital.me" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'replace "--ENVIRONMENT--" "staging" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',
						'replace "--ENVIRONMENT_URL--" "staging.iddigital.me" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt',

						'mysqladmin create ' . strtolower($request->get('repo_name')) . ' -pSt54d9bb',
						'mysql -u root -pSt54d9bb ' . strtolower($request->get('repo_name')) . ' < /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt'
					], function ($line) {
						//	echo $line.PHP_EOL;
						Log::info('Server\SSH: ' . $line.PHP_EOL);
					});

					// copy plugins files
					if (@$request->selected_plugins) {
						Log::info('Server\SSH (plugin): getting plugins... ');
						foreach ($request->selected_plugins as $selected_plugin) {
							// get plugin info..
							$plugin_info = DB::table('plugins')->where('id', $selected_plugin)->first();
							if ($plugin_info) {
								SSH::run([
									'git clone git@bitbucket.org:id-digital/' . strtolower($plugin_info->bb_source) . '.git /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft/plugins/' . str_replace("-", "", strtolower($plugin_info->bb_source))
								], function ($line) {
									Log::info('Server\SSH (plugin): ' . $line.PHP_EOL);
								});
							}
						}
					}


				}

				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// craftcms 3 deployment ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				if ($repo_type_id == 3) {

					// core deployment
					SSH::run([
						'git clone -b develop git@bitbucket.org:id-digital/' . strtolower($request->get('repo_name')) . '.git /var/www/staging/' . strtolower($request->get('repo_name')),

						'chmod 755 /var/www/staging/' . strtolower($request->get('repo_name')) . '/craft',

						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/config',
						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/storage',
						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/vendor',

						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/storage/logs',
						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/storage/runtime',

						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/storage',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/vendor',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/cpresources',

						'cp /var/www/staging/' . strtolower($request->get('repo_name')) . '/.env.example /var/www/staging/' . strtolower($request->get('repo_name')) . '/.env',

						'wget https://brain.iddigital.us/site_uploads/' . $request->get('bb_source') . '.zip -P /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/',
						'unzip /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/' . $request->get('bb_source') . '.zip -d /var/www/staging/' . strtolower($request->get('repo_name')) . '/web',
						'rm -rf /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/__MACOSX',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/uploads',
						'rm -rf /var/www/staging/' . strtolower($request->get('repo_name')) . '/web/' . $request->get('bb_source') . '.zip',

						'replace "--DBPASSWORD--" "St54d9bb" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/.env',
						'replace "--DBNAME--" "' . strtolower($request->get('repo_name')) . '" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/.env',
						'replace "--SITEURL--" "http://' . strtolower($request->get('repo_name')) . '.craft3.staging.iddigital.me" -- /var/www/staging/' . strtolower($request->get('repo_name')) . '/.env',

						'/usr/bin/composer install -d /var/www/staging/' . strtolower($request->get('repo_name')),

						'/var/www/staging/' . strtolower($request->get('repo_name')) . '/craft setup/security-key',

						'mysqladmin create ' . strtolower($request->get('repo_name')) . ' -pSt54d9bb',
						'mysql -u root -pSt54d9bb ' . strtolower($request->get('repo_name')) . ' < /var/www/staging/' . strtolower($request->get('repo_name')) . '/db.txt'
					], function ($line) {
						//	echo $line.PHP_EOL;
						Log::info('Server\SSH: ' . $line.PHP_EOL);
					});


				}

				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				// wordpress deployment ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

				if ($repo_type_id == 2) {

					if (@$repo_info->theme_name) {
						$theme_name = $repo_info->theme_name;
					} else {
						$theme_name = "salient-child";
					}

					// core deployment
					SSH::run([
						'git clone git@bitbucket.org:id-digital/wordpress.git /var/www/staging/' . strtolower($request->get('repo_name')),
					], function ($line) {
					});

					// if we have a parent theme, install that next..
					if (@$repo_info->root_source) {
						SSH::run([
							'git clone git@bitbucket.org:id-digital/' . strtolower($repo_info->root_source) . '.git /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/themes/' . strtolower($repo_info->root_source),
						], function ($line) {
						});
					}

					SSH::run([

						'git clone git@bitbucket.org:id-digital/' . strtolower($request->get('repo_name')) . '.git /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/themes/' . $theme_name,

						'mkdir /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/uploads',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/uploads',
						'mysqladmin create ' . strtolower($request->get('repo_name')) . ' -pSt54d9bb',
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/plugins',
						'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')),

						'/usr/local/bin/wp core config --dbname=' . strtolower($request->get('repo_name')) . '  --dbuser=root --dbpass=St54d9bb --allow-root --path=/var/www/staging/' . strtolower($request->get('repo_name')),
						'/usr/local/bin/wp core install --url=' . strtolower($request->get('repo_name')) . '.wpstaging.iddigital.me --title=' . $request->get('site_name') . ' --admin_user=admin --admin_password=Id54d9bb --admin_email=dustinv@iddigital.us --allow-root --path=/var/www/staging/' . strtolower($request->get('repo_name')),
						'/usr/local/bin/wp theme activate ' . $theme_name . ' --allow-root --path=/var/www/staging/' . strtolower($request->get('repo_name')),
						'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/themes',
						'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')) . '/wp-content/upgrade'

					], function ($line) {
					});

					if (@$request->selected_plugins) {
						foreach ($request->selected_plugins as $selected_plugin) {
							// get plugin info..
							$plugin_info = DB::table('plugins')->where('id', $selected_plugin)->first();
							if ($plugin_info) {
								SSH::run([
									'git clone git@bitbucket.org:id-digital/' . strtolower($plugin_info->bb_source) . '.git /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/plugins/' . strtolower($plugin_info->bb_source)
								], function ($line) {
								});
							}
						}
						SSH::run([
							'/usr/local/bin/wp plugin activate --all --allow-root --path=/var/www/staging/' . strtolower($request->get('repo_name')),
							'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/plugins',
							'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')),
							'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')) . '/wp-content/upgrade'
						], function ($line) {
						});
					}

					if (@$repo_info->wrap_ups) {
						// run the wraps ups..
						SSH::run([
							$repo_info->wrap_ups,
							'chmod -R 777 /var/www/staging/' . strtolower($request->get('repo_name')) . '/wp-content/themes',
							'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')),
							'chown -R www-data:www-data /var/www/staging/'. strtolower($request->get('repo_name')) . '/wp-content/upgrade'
						], function ($line) {
						});
					}

				}

				$request->session()->flash("status", "Install created successfully - you're a pro!");
			}
		}

		return redirect('/installs');
		exit;

	}

}