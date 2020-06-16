<?php

namespace brain\Http\Controllers\Auth;

use brain\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

	use AuthenticatesUsers;

	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest')->except('logout');
	}

	protected function authenticated(Request $request, $user)
	{
		$this->setUserSession($user);
	}

	protected function setUserSession($user)
	{

		if (in_array($user->user_role, [3, 5])) {
			session(['user_brand' => 'soar']);
		} else {
			session(['user_brand' => 'idd']);
		}

	}

}
