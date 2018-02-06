<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use App\User;
use App\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


	function index()
	{
		$user = Auth::user();
		dd($this->image());
	}

    function registerMAC(Request $request)
    {
    	$user = Auth::user();
    	$data = $request->all();

    	if($user == null)
    		abort(401, 'Unautorized');

    	$validator = Validator::make($data, [
		    'mac_address' => 'required|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
		    'image' => 'required|image'
		]);

		if($validator->fails()){
			dd($validator);
			return view('dashboard', ['user'=> $user, 'error' => 'Incorrect Format', 'message'=>'']);
		}	
    	$user->mac_address = $data['mac_address'];
    	$file = Input::file('image');
    	$user->profile_pic = $file->getClientOriginalName();
		$file->move(public_path().'/uploads', $file->getClientOriginalName());
    	$user->save();

    	return view('dashboard', ['user'=> $user, 'error' => '', 'message'=>'Successful']);
    }

   	function barGraph()
   	{
   		return view('dashboard_bar', ['message' => '', 'error' => '']);
   	} 

   	function image(Request $request)
   	{
   		$data = $request->all();
   		$email = $data['email'];

   		$user = User::where('email',$email)->first();

   		return $user->profile_pic;

   	}

}
