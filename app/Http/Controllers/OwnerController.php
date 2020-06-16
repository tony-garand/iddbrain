<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class OwnerController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {

		$owners = DB::table('owners')
					->select('owners.*')
					->orderBy('owner_name')
					->get();
		return view('owners.index', ['owners' => $owners]);

	}

	public function view(Request $request, $id) {

		$owner = DB::table('owners')
					->where('owners.id', $id)
					->first();

		$owner_servers = DB::table('servers')
					->join('repos', 'servers.bb_source', '=', 'repos.bb_source')
					->select('servers.*', 'repos.repo_type_id')
					->where('servers.owner_id', $id)
					->get();

		return view('owners.view', ['owner' => $owner, 'owner_servers' => $owner_servers]);

	}

	public function save(Request $request) {

		echo "save";
		exit;

	}

}