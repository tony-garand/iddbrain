<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use GrahamCampbell\Bitbucket\Facades\Bitbucket;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Cache;
use Webpatser\Uuid\Uuid;

class ToolController extends Controller {

	public function __construct() {
		$this->middleware('auth');

		$all_records = 0;
		$bb_page = 1;
		$step = 0;
		$usable_repos_brain = array();
		$usable_repos_plugin = array();
		$usable_repos_root = array();

		if ( (Cache::has('usable_repos_brain')) && (Cache::has('usable_repos_plugin')) && (Cache::has('usable_repos_root')) ) {
			$this->usable_repos_brain = Cache::get('usable_repos_brain');
			$this->usable_repos_plugin = Cache::get('usable_repos_plugin');
			$this->usable_repos_root = Cache::get('usable_repos_root');
		} else {

			while ($all_records < 1) {
				$bitbucket = @Bitbucket::Repositories()->users('id-digital')->list(['pagelen' => env('BB_PAGELEN', 100), 'page' => $bb_page]);
				foreach ($bitbucket['values'] as $bb) {
					if ($bb['project']['key'] == "BRAIN") {
						$usable_repos_brain[] = $bb['name'];
					}
					if ($bb['project']['key'] == "BP") {
						$usable_repos_plugin[] = $bb['slug'];
					}
					if ($bb['project']['key'] == "BR") {
						$usable_repos_root[] = $bb['slug'];
					}
				}
				$step = $step + env('BB_PAGELEN', 100);
				$bb_page++;
				if ($bitbucket['size'] <= $step) {
					$all_records = 1;
				}
			}

			Cache::put('usable_repos_brain', $usable_repos_brain, 30);
			Cache::put('usable_repos_plugin', $usable_repos_plugin, 30);
			Cache::put('usable_repos_root', $usable_repos_root, 30);

			$this->usable_repos_brain = $usable_repos_brain;
			$this->usable_repos_plugin = $usable_repos_plugin;
			$this->usable_repos_root = $usable_repos_root;
		}

	}

	public function index() {
	}

	public function import_routes() {
	}

	public function ajax_plugins_by_bbsource(Request $request) {

		$arr_plugins = array();

		$repo = DB::table('repos')->where('bb_source', $request->bb_source)->first();
		if ($repo) {
			$plugins = DB::table('plugins')->where('repo_type_id', $repo->repo_type_id)->get();
			foreach ($plugins as $plugin) {
				$tmp = array();
				$tmp['plugin_id'] = $plugin->id;
				$tmp['bb_source'] = $plugin->bb_source;
				$tmp['plugin_name'] = $plugin->name;
				$arr_plugins[] = $tmp;
			}
		}

		$json = json_encode($arr_plugins);
		echo $json;
		exit;

	}

	public function ajax_ms_by_client(Request $request) {

		$services = array();

		$client = DB::table('clients')->where('id', $request->client_id)->first();
		if ($client) {
			$mss = DB::table('messaging_services')->where('client_id', $client->id)->get();
			foreach ($mss as $ms) {
				$tmp = array();
				$tmp['ms_id'] = $ms->id;
				$tmp['ms_sid'] = $ms->sid;
				$tmp['ms_name'] = $ms->name;
				$services[] = $tmp;
			}
		}

		$json = json_encode($services);
		echo $json;
		exit;

	}

	// roles ////////////////////////////////////////////////////////////////////////

	public function roles_index() {
		$roles = DB::table('roles')
					->orderBy('display_name')
					->simplePaginate(15);
		return view('tools.roles.index', ['roles' => $roles]);
	}

	public function roles_view(Request $request, $id) {
		$role = DB::table('roles')->where('id', $id)->first();
		return view('tools.roles.view', ['role' => $role]);
	}

	public function roles_save(Request $request) {

		$this->validate($request, [
			'name' => 'required|unique:roles,name',
			'display_name' => 'required',
			'description' => 'required'
		]);

		$role_id = DB::table('roles')->insertGetId(
			[
				'name' => $request->get('name'),
				'display_name' => $request->get('display_name'),
				'description' => $request->get('description'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Role created successfully!");
		return redirect('/tools/roles');
	}

	public function roles_update(Request $request, $id) {

		DB::table('roles')
			->where('id', $id)
			->update([
				'name' => $request->get('name'),
				'display_name' => $request->get('display_name'),
				'description' => $request->get('description'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Role updated successfully!");
		return redirect('/tools/roles');
	}

	// /roles ///////////////////////////////////////////////////////////////////////


	// plugins //////////////////////////////////////////////////////////////////////////

	public function plugins_index() {

		$repo_types = DB::table('repo_types')
					->orderBy('name')
					->get();

		$plugins = DB::table('plugins')
					->join('repo_types', 'plugins.repo_type_id', '=', 'repo_types.id')
					->select('plugins.*', 'repo_types.name AS repo_type_name')
					->orderBy('bb_source')
					->get();

		return view('tools.plugins.index', ['plugins' => $plugins, 'repo_types' => $repo_types, 'usable_repos' => $this->usable_repos_plugin]);

	}

	public function plugins_view(Request $request, $id) {

		$repo_types = DB::table('repo_types')
					->orderBy('name')
					->get();

		$plugin = DB::table('plugins')->where('id', $id)->first();

		return view('tools.plugins.view', ['plugin' => $plugin, 'repo_types' => $repo_types, 'usable_repos' => $this->usable_repos_plugin]);
	}

	public function plugins_save(Request $request) {

		$this->validate($request, [
			'bb_source' => 'required|unique:plugins,bb_source',
			'repo_type_id' => 'required',
			'name' => 'required'
		]);

		$plugin_id = DB::table('plugins')->insertGetId(
			[
				'bb_source' => $request->get('bb_source'),
				'repo_type_id' => $request->get('repo_type_id'),
				'name' => $request->get('name'),
				'description' => $request->get('description'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Plugin created successfully!");
		return redirect('/tools/plugins');
	}

	public function plugins_update(Request $request, $id) {

		DB::table('plugins')
			->where('id', $id)
			->update([
				'bb_source' => $request->get('bb_source'),
				'repo_type_id' => $request->get('repo_type_id'),
				'name' => $request->get('name'),
				'description' => $request->get('description'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Plugin updated successfully!");
		return redirect('/tools/plugins');
	}

	// /plugins /////////////////////////////////////////////////////////////////////////


	// repos ////////////////////////////////////////////////////////////////////////////

	public function repos_index() {

		$repo_types = DB::table('repo_types')
					->orderBy('name')
					->get();

		$repos = DB::table('repos')
					->join('repo_types', 'repos.repo_type_id', '=', 'repo_types.id')
					->select('repos.*', 'repo_types.name AS repo_type_name')
					->orderBy('bb_source')
					->get();

		return view('tools.repos.index', ['repos' => $repos, 'repo_types' => $repo_types, 'usable_repos' => $this->usable_repos_brain, 'root_sources' => $this->usable_repos_root]);

	}

	public function repos_view(Request $request, $id) {

		$repo_types = DB::table('repo_types')
					->orderBy('name')
					->get();

		$repo = DB::table('repos')->where('id', $id)->first();

		return view('tools.repos.view', ['repo' => $repo, 'repo_types' => $repo_types, 'usable_repos' => $this->usable_repos_brain, 'root_sources' => $this->usable_repos_root]);
	}

	public function repos_save(Request $request) {

		$this->validate($request, [
			'bb_source' => 'required|unique:repos,bb_source',
			'repo_type_id' => 'required'
		]);

		$repo_id = DB::table('repos')->insertGetId(
			[
				'bb_source' => $request->get('bb_source'),
				'repo_type_id' => $request->get('repo_type_id'),
				'wrap_ups' => $request->get('wrap_ups'),
				'root_source' => $request->get('root_source'),
				'theme_name' => $request->get('theme_name'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Repo created successfully!");
		return redirect('/tools/repos');
	}

	public function repos_update(Request $request, $id) {

		DB::table('repos')
			->where('id', $id)
			->update([
				'bb_source' => $request->get('bb_source'),
				'repo_type_id' => $request->get('repo_type_id'),
				'wrap_ups' => $request->get('wrap_ups'),
				'root_source' => $request->get('root_source'),
				'theme_name' => $request->get('theme_name'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Repo updated successfully!");
		return redirect('/tools/repos');
	}

	// /repos ///////////////////////////////////////////////////////////////////////////


	// repo types ///////////////////////////////////////////////////////////////////////

	public function repo_types_index() {

		$repo_types = DB::table('repo_types')
					->orderBy('name')
					->get();
		return view('tools.repo_types.index', ['repo_types' => $repo_types]);

	}

	public function repo_types_view(Request $request, $id) {
		$repo_type = DB::table('repo_types')->where('id', $id)->first();
		return view('tools.repo_types.view', ['repo_type' => $repo_type]);
	}

	public function repo_types_save(Request $request) {

		$this->validate($request, [
			'name' => 'required|unique:repo_types,name'
		]);

		$repo_type_id = DB::table('repo_types')->insertGetId(
			[
				'name' => $request->get('name'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Repo Type created successfully!");
		return redirect('/tools/repo_types');
	}

	public function repo_types_update(Request $request, $id) {

		DB::table('repo_types')
			->where('id', $id)
			->update([
				'name' => $request->get('name'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Repo Type updated successfully!");
		return redirect('/tools/repo_types');
	}

	// /repo types //////////////////////////////////////////////////////////////////////


	// messaging services ///////////////////////////////////////////////////////////////

	public function messaging_services_index() {
		$clients = DB::table('clients')->orderBy('name')->where('status', 1)->get();
		$messaging_services = DB::table('messaging_services')->leftJoin('clients', 'clients.id', '=', 'messaging_services.client_id')->select('messaging_services.*', 'clients.name as client_name')->orderBy('name', 'asc')->get();
		return view('tools.messaging_services.index', ['messaging_services' => $messaging_services, 'clients' => $clients]);
	}

	public function messaging_services_view(Request $request, $id) {
		$clients = DB::table('clients')->orderBy('name')->where('status', 1)->get();
		$messaging_service = DB::table('messaging_services')->where('id', $id)->first();
		$messaging_service_numbers = DB::table('messaging_service_numbers')->where('messaging_service_id', $messaging_service->id)->get();
		return view('tools.messaging_services.view', ['messaging_service' => $messaging_service, 'messaging_service_numbers' => $messaging_service_numbers, 'clients' => $clients]);
	}

	public function messaging_services_save(Request $request) {

		$this->validate($request, [
			'name' => 'required',
			'client_id' => 'required'
		]);

		$client = DB::table('clients')->where('id', $request->get('client_id'))->first();
		if ($client) {

			$twilio = new Client(env('SMS_SID'), env('SMS_TOKEN'));
			$ms_name = $client->name . " - " . $request->get('name');
			$add_service = $twilio->messaging->v1->services->create($ms_name, array('InboundRequestUrl' => 'https://brain.iddigital.us/sms/incoming_sms'));
			$ms_id = DB::table('messaging_services')->insertGetId(
				[
					'client_id' => $request->get('client_id'),
					'sid' => $add_service->sid,
					'name' => $request->get('name'),
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);

			$lines = $request->get('add_nums');

			if ($lines > 0) {
				$numbers = $twilio->availablePhoneNumbers('US')->local->read(array("areaCode" => $request->get('area_code')));
				for ($i = 1; $i <= $lines; $i++) {
					if (@$numbers[$i-1]->phoneNumber) {
						$new_number = $twilio->incomingPhoneNumbers->create(array("phoneNumber" => $numbers[$i-1]->phoneNumber));
						if (@$new_number->sid) {
							$added_phone = $twilio->messaging->v1->services($add_service->sid)->phoneNumbers->create($new_number->sid);
							$phone_id = DB::table('messaging_service_numbers')->insertGetId(
								[
									'messaging_service_id' => $ms_id,
									'number' => $numbers[$i-1]->phoneNumber,
									'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
									'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
								]
							);
						}
					}
				}
			}

		}

		$request->session()->flash("status", "Messaging Service created successfully!");
		return redirect('/tools/messaging_services');
	}

	public function messaging_services_update(Request $request, $id) {

		DB::table('messaging_services')
			->where('id', $id)
			->update([
				'client_id' => $request->get('client_id'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "Messaging Service updated successfully!");
		return redirect('/tools/messaging_services');
	}

	public function messaging_services_add_number(Request $request) {

		if ($request->get('area_code')) {
			$messaging_service = DB::table('messaging_services')->where('id', $request->get('messaging_service_id'))->first();

			$twilio = new Client(env('SMS_SID'), env('SMS_TOKEN'));
			$numbers = $twilio->availablePhoneNumbers('US')->local->read(array("areaCode" => $request->get('area_code')));
			if (@$numbers[0]->phoneNumber) {
				$new_number = $twilio->incomingPhoneNumbers->create(array("phoneNumber" => $numbers[0]->phoneNumber));
				if (@$new_number->sid) {
					$added_phone = $twilio->messaging->v1->services($messaging_service->sid)->phoneNumbers->create($new_number->sid);
					$phone_id = DB::table('messaging_service_numbers')->insertGetId(
						[
							'messaging_service_id' => $messaging_service->id,
							'number' => $numbers[0]->phoneNumber,
							'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
							'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
						]
					);
				}

				$request->session()->flash("status", "Phone Number added successfully!");
				return redirect('/tools/messaging_services/view/'.$request->get('messaging_service_id'));

			} else {
				// couldnt add it, throw a message..
				$request->session()->flash("status", "ERROR - Could not find phone number in area code provided.");
				return redirect('/tools/messaging_services/view/'.$request->get('messaging_service_id'));
			}
		}
		exit;

	}

	// /messaging services //////////////////////////////////////////////////////////////


	// sms convo ////////////////////////////////////////////////////////////////////////

	public function sms_conversations_index() {
		$clients = DB::table('clients')->orderBy('name')->where('status', 1)->get();
		$messaging_services = DB::table('messaging_services')->orderBy('name', 'asc')->get();
		$sms_conversations = DB::table('sms_convos')->leftJoin('clients', 'clients.id', '=', 'sms_convos.client_id')->select('sms_convos.*', 'clients.name as client_name')->orderBy('id', 'desc')->get();

		foreach ($sms_conversations as &$sms_conversation) {
			$count_threads = DB::table('sms_convo_threads')->where('sms_convo_id', $sms_conversation->id)->count();
			$count_client_users = DB::table('sms_data')->where('client_id', $sms_conversation->client_id)->where('status', 1)->count();
			$sms_conversation->count_threads = $count_threads;
			$sms_conversation->count_client_users = $count_client_users;
		}

		return view('tools.sms_conversations.index', ['clients' => $clients, 'messaging_services' => $messaging_services, 'sms_conversations' => $sms_conversations]);
	}

	public function sms_conversation_threads(Request $request, $id) {
		$sms_convo = DB::table('sms_convos')->where('id', $id)->first();
		$client = DB::table('clients')->where('id', $sms_convo->client_id)->first();
		$threads = DB::table('sms_convo_threads')->where('sms_convo_id', $sms_convo->id)->get();

		return view('tools.sms_conversations.threads', ['client' => $client, 'sms_convo' => $sms_convo, 'threads' => $threads]);
	}

	public function sms_conversation_thread_view(Request $request, $id) {
		$thread = DB::table('sms_convo_threads')->where('id', $id)->first();
		$sms_convo = DB::table('sms_convos')->where('id', $thread->sms_convo_id)->first();
		$client = DB::table('clients')->where('id', $sms_convo->client_id)->first();

		$out = array();
		$cnt = 0;

		$scripts = DB::table('sms_convo_scripts')->where('sms_convo_id', $thread->sms_convo_id)->orderBy('step', 'asc')->get();
		foreach ($scripts as $script) {
			$tmp = array();
			$script_reply = DB::table('sms_convo_thread_replies')->where('sms_convo_thread_id', $thread->id)->where('sms_convo_script_id', $script->id)->first();
			$tmp['q'] = $script->script_body;
			$out[] = $tmp;
			if (@$script_reply->reply_body) {
				$out[$cnt-1]['r'] = $script_reply->reply_body;
				$out[$cnt-1]['r_ts'] = $script_reply->created_at;
			}
			$cnt++;
		}

		return view('tools.sms_conversations.thread_view', ['client' => $client, 'sms_convo' => $sms_convo, 'thread' => $thread, 'out' => $out]);
	}

	public function sms_conversation_users(Request $request, $id) {
		$sms_convo = DB::table('sms_convos')->where('id', $id)->first();
		$client = DB::table('clients')->where('id', $sms_convo->client_id)->first();
		$users = DB::table('sms_data')->where('client_id', $sms_convo->client_id)->get();

		return view('tools.sms_conversations.users', ['client' => $client, 'sms_convo' => $sms_convo, 'users' => $users]);
	}

	public function sms_conversations_edit_script(Request $request, $id) {
		$sms_script = DB::table('sms_convo_scripts')->where('id', $id)->first();
		$sms_convo = DB::table('sms_convos')->where('id', $sms_script->sms_convo_id)->first();
		return view('tools.sms_conversations.edit_script', ['sms_script' => $sms_script, 'sms_convo' => $sms_convo]);
	}

	public function sms_conversations_view(Request $request, $id) {
		$clients = DB::table('clients')->orderBy('name')->where('status', 1)->get();
		$selected_ms = array();
		$sms_convo = DB::table('sms_convos')->where('id', $id)->first();
		if ($sms_convo) {
			$sms_convo_messaging_services = DB::table('sms_convo_messaging_services')->where('sms_convo_id', $sms_convo->id)->get();
			foreach ($sms_convo_messaging_services as $sms_convo_messaging_service) {
				$selected_ms[] = $sms_convo_messaging_service->messaging_service_id;
			}
		} else {
			$request->session()->flash("status", "SMS Convo not found!");
			return redirect('/tools/sms_conversations');
		}
		$messaging_services = DB::table('messaging_services')->where('client_id', $sms_convo->client_id)->orderBy('name', 'asc')->get();

		// get scripts
		$sms_convo_scripts = DB::table('sms_convo_scripts')->where('sms_convo_id', $sms_convo->id)->orderBy('step', 'asc')->get();

		return view('tools.sms_conversations.view', ['clients' => $clients, 'sms_convo_scripts' => $sms_convo_scripts, 'selected_ms' => $selected_ms, 'messaging_services' => $messaging_services, 'sms_convo' => $sms_convo, 'sms_convo_messaging_services' => $sms_convo_messaging_services]);
	}

	public function sms_conversations_update_script(Request $request, $id) {
		$this->validate($request, [
			'script_body' => 'required'
		]);

		$sms_script = DB::table('sms_convo_scripts')->where('id', $id)->first();
		$sms_convo = DB::table('sms_convos')->where('id', $sms_script->sms_convo_id)->first();

		DB::table('sms_convo_scripts')
			->where('id', $id)
			->update([
				'script_body' => $request->get('script_body'),
				'data_destination' => $request->get('data_destination'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "SMS Conversation Script updated successfully!");
		return redirect('/tools/sms_conversations/view/'.$sms_convo->id.'?'.rander(10));
	}

	public function sms_conversations_delete(Request $request, $id) {
		DB::table('sms_convos')->where('id', $id)->delete();
		$request->session()->flash("status", "SMS Conversation deleted successfully!");
		return redirect('/tools/sms_conversations/?'.rander(10));
	}

	public function sms_conversations_delete_script(Request $request, $id) {
		$sms_script = DB::table('sms_convo_scripts')->where('id', $id)->first();
		$sms_convo = DB::table('sms_convos')->where('id', $sms_script->sms_convo_id)->first();

		DB::table('sms_convo_scripts')->where('id', $id)->delete();
		$request->session()->flash("status", "SMS Conversation Script deleted successfully!");
		return redirect('/tools/sms_conversations/view/'.$sms_convo->id.'?'.rander(10));
	}

	public function sms_conversations_save_script(Request $request) {
		$this->validate($request, [
			'script_body' => 'required'
		]);

		// get last step in this chain..
		$last_script_step = DB::table('sms_convo_scripts')->where('sms_convo_id', $request->get('sms_convo_id'))->orderBy('step', 'desc')->first();
		if ($last_script_step) {
			$next_step = $last_script_step->step + 1;
		} else {
			$next_step = 1;
		}

		$sms_convo_script_id = DB::table('sms_convo_scripts')->insertGetId(
			[
				'sms_convo_id' => $request->get('sms_convo_id'),
				'step' => $next_step,
				'script_body' => $request->get('script_body'),
				'data_destination' => $request->get('data_destination'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		$request->session()->flash("status", "SMS Conversation Script added successfully!");
		return redirect('/tools/sms_conversations/view/'.$request->get('sms_convo_id').'?'.rander(10));
	}

	public function sms_conversations_save(Request $request) {

		$this->validate($request, [
			'trigger' => 'required'
		]);

		$all_locs = 0;
		foreach ($request->get('messaging_services') as $msid) {
			if ($msid == -1) {
				$all_locs = 1;
			}
		}

		$uuid = Uuid::generate()->string;

		if ($all_locs == 1) {
			$sms_convo_id = DB::table('sms_convos')->insertGetId(
				[
					'uuid' => $uuid,
					'client_id' => trim(strtolower($request->get('client_id'))),
					'trigger' => trim(strtolower($request->get('trigger'))),
					'welcome' => trim(strtolower($request->get('welcome'))),
					'all_locations' => 1,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
		} else {
			$sms_convo_id = DB::table('sms_convos')->insertGetId(
				[
					'uuid' => $uuid,
					'client_id' => trim(strtolower($request->get('client_id'))),
					'trigger' => trim(strtolower($request->get('trigger'))),
					'welcome' => trim(strtolower($request->get('welcome'))),
					'all_locations' => 0,
					'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
			foreach ($request->get('messaging_services') as $msid) {
				$sms_convo_messaging_service_id = DB::table('sms_convo_messaging_services')->insertGetId(
					[
						'sms_convo_id' => $sms_convo_id,
						'messaging_service_id' => $msid,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			}
		}

		$request->session()->flash("status", "SMS conversation created successfully!");
		return redirect('/tools/sms_conversations');
	}

	public function sms_conversations_update(Request $request, $id) {

		$this->validate($request, [
			'trigger' => 'required'
		]);

		$all_locs = 0;
		foreach ($request->get('messaging_services') as $msid) {
			if ($msid == -1) {
				$all_locs = 1;
			}
		}

		DB::table('sms_convo_messaging_services')->where('sms_convo_id', $id)->delete();

		$uuid = Uuid::generate()->string;
		$uuid_check = DB::table('sms_convos')->where('id', $id)->first();
		if (!$uuid_check->uuid) {
			DB::table('sms_convos')
				->where('id', $id)
				->update([
					'uuid' => $uuid,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
		}

		if ($all_locs == 1) {
			DB::table('sms_convos')
				->where('id', $id)
				->update([
					'trigger' => $request->get('trigger'),
					'welcome' => $request->get('welcome'),
					'all_locations' => 1,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
		} else {
			DB::table('sms_convos')
				->where('id', $id)
				->update([
					'trigger' => $request->get('trigger'),
					'welcome' => $request->get('welcome'),
					'all_locations' => 0,
					'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
				]
			);
			foreach ($request->get('messaging_services') as $msid) {
				$sms_convo_messaging_service_id = DB::table('sms_convo_messaging_services')->insertGetId(
					[
						'sms_convo_id' => $id,
						'messaging_service_id' => $msid,
						'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
						'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
					]
				);
			}
		}

		$request->session()->flash("status", "SMS Conversation updated successfully!");
		return redirect('/tools/sms_conversations/view/'.$id.'?'.rander(10));
	}

	// /sms convo //////////////////////////////////////////////////////////////////////

}
