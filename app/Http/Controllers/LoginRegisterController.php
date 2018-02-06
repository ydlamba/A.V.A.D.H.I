<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Support\Facades\Auth;

class LoginRegisterController extends Controller
{
	function index()
	{
		return view('loginregister',['message'=>'','login_error'=>'']);
	}

	function login(Request $request)
	{
		$data = $request->all();
		$validator = Validator::make($data, [
		    'username' => 'required|alpha_dash',
		    'password' => 'required',
		]);

		if($validator->fails()){
			return redirect('/welcome')->withErrors($validator);
		}

		$user = User::where('username',$data['username'])->first();
		if($user !== null)
		{
			if(Hash::check($data['password'], $user->password))
				{
					Auth::login($user,true);
					return redirect('/dashboard');
				}
		}

		return view('loginregister',['login_error'=>'Incorrect Username or Password', 'message' => '']);

	}

	function register(Request $request)
	{
		$data = $request->all();
		$message = "";
		$validator = Validator::make($data, [
		    'first_name' => 'required|alpha',
		    'last_name' => 'required|alpha',
		    'username' => 'required|alpha_dash|unique:users',
		    'password' => 'required',
		    'email' => 'required|email|unique:users',
		]);

		if($validator->fails()){
			return redirect('/welcome')->withErrors($validator);
		}

		$new_user = new User();
		$new_user->username = $data['username'];
		$new_user->password = Hash::make($data['password']);
		$new_user->name = $data['first_name']." ".$data['last_name'];
		$new_user->email = $data['email'];
		$new_user->mac_address = "";

		$new_user->save();
		$message = "Successfully Registered, proceed to login.";
		return view('loginregister',['message'=>$message, 'login_error'=>'']);
	}
}
