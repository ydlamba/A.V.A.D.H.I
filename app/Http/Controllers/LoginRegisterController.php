<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class LoginRegisterController extends Controller
{
	function index()
	{
		return view('loginregister',['message'=>'']);
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
		return view('loginregister',['message'=>$message]);
	}
}
