<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
	function index()
	{
		$user = Auth::user();
		if($user === null)
			return view('loginregister', ['message'=>'', 'login_error'=> 'Login First -_-']);


		return view('dashboard',['user' => $user]);
	}

	function logout()
	{
		$user = Auth::user();
		Auth::logout($user);

		return redirect('/welcome');
	}
}
