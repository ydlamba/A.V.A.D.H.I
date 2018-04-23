<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use App\Log;
use Carbon\Carbon;

class User extends Authenticatable
{
	use Notifiable;

	private $TIME_LIMIT = 5;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'password',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	function isOnline()
	{
		$log = Log::where('mac_address',$this['mac_address'])->orderBy('timestamp','desc')->first();
		if($log == null)
			return false;
		$current = Carbon::now();
		// dd(Carbon::parse($log['timestamp'])->setTimezone('Asia/Kolkata'));
		$diffInMinutes = $current->diffInMinutes(Carbon::parse($log['timestamp']));
		// dd($current);
		if ($diffInMinutes < $this->TIME_LIMIT)
			return true;

		return false;
	}

	function lastSeen()
	{
		$log = Log::where('mac_address',$this['mac_address'])->orderBy('timestamp','desc')->first();
		if($log == null)
		{
			return "No Activity, Go do some work!";
		}
		$current = Carbon::now('Asia/Kolkata');
		$time = Carbon::parse($log['timestamp']);
		return $time->diffForHumans($current);   
	}

	function hoursActiveInADay($day)
	{
		$temp = clone $day;
		$day_before = $temp->subDays(1);

		$logs = Log::whereBetween('timestamp',[$day_before,$day])->where('mac_address',$this->mac_address)->get();

		return (count($logs)/60);
	} 

	function hoursActiveInAWeek($day)
	{
		$today = clone $day;	

		$time_week = array();

		for ($i=0; $i<20; $i++){
			$temp = clone $today;
			$temp2 = clone $temp;
			$temp_before = $temp2->subDays(1);
			$logs = Log::whereBetween('timestamp',[$temp_before,$temp])->where('mac_address',$this->mac_address)->get();
			$today->subDays(1);
			array_push($time_week,count($logs)/60);
		}

		return $time_week;
	}

	function daysOfAWeek()
	{
		$now = Carbon::today('Asia/Kolkata');
		$week = array();
		for($i=0; $i<20; $i++)
		{
			array_push($week, $now->format('d/m'));
			$now->subDays(1);
		}

		return $week;
	}
}
