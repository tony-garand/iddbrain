<?php

namespace brain\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use brain\User;
use brain\Role;
use Auth;

class UserController extends Controller {

	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		$users = DB::table('users')
					->select('users.*')
					->orderBy('name')
					->get();
		$roles = DB::table('roles')->orderBy('display_name')->get();


		foreach ($users as &$user) {
			$role = Role::where('id', '=', $user->user_role)->first();
			$user->role_display_name = $role->display_name;
		}

		return view('users.index', ['users' => $users, 'roles' => $roles]);
	}

	public function update(Request $request, $id) {

		$user = User::where('id', '=', $id)->first();

		$this->validate($request, [
			'email' => 'required|email|max:255|unique:users,email,'.$user->id,
			'name' => 'required'
		]);

		$user->roles()->sync([]);
		$user->attachRole($request->get('role_id'));

		DB::table('users')
			->where('id', $id)
			->update([
				'name' => $request->get('name'),
				'email' => $request->get('email'),
				'user_role' => $request->get('role_id'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]);

		if ($request->get('password')) {
			DB::table('users')
				->where('id', $id)
				->update([
					'password' => bcrypt($request->get('password')),
				]);
		}

		$request->session()->flash("status", "User updated successfully!");
		return redirect('/users');
	}

	public function view(Request $request, $id) {
		$roles = DB::table('roles')->orderBy('display_name')->get();
		$user = DB::table('users')->where('id', $id)->first();
		return view('users.view', ['user' => $user, 'roles' => $roles]);
	}

	public function profile() {
		$user = DB::table('users')->where('id', Auth::id())->first();
		return view('users.profile', ['user' => $user]);
	}

	public function profile_update(Request $request) {

		$user = User::where('id', '=', Auth::id())->first();

		$this->validate($request, [
			'email' => 'required|email|max:255|unique:users,email,'.$user->id,
			'name' => 'required'
		]);

		DB::table('users')
			->where('id', Auth::id())
			->update([
				'name' => $request->get('name'),
				'email' => $request->get('email'),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]);

		if ($request->get('password')) {
			DB::table('users')
				->where('id', Auth::id())
				->update([
					'password' => bcrypt($request->get('password')),
				]);
		}

		$request->session()->flash("status", "Profile updated successfully!");
		return redirect('/user_profile');
	}

	public function delete(Request $request, $id) {
		DB::table('users')->where('id', $id)->delete();
		$request->session()->flash("status", "User deleted successfully!");
		return redirect('/users');
	}

	public function save(Request $request) {

		$this->validate($request, [
			'email' => 'required|email|max:255|unique:users',
			'name' => 'required'
		]);

		$user_id = DB::table('users')->insertGetId(
			[
				'name' => $request->get('name'),
				'email' => $request->get('email'),
				'password' => bcrypt($request->get('password')),
				'user_role' => $request->get('role_id'),
				'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
				'updated_at' => \Carbon\Carbon::now()->toDateTimeString()
			]
		);

		if ($user_id) {

			$user = User::where('id', '=', $user_id)->first();
			$user->attachRole($request->get('role_id'));

			$request->session()->flash("status", "User created successfully!");
			return redirect('/users');
		} else {
			$request->session()->flash("status", "Something bad happened!");
			return redirect('/');
		}

	}

}