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
        $current = Carbon::now('Asia/Kolkata');
        $diffInMinutes = $current->diffInMinutes(Carbon::parse($log['timestamp']));
        if ($diffInMinutes < $this->TIME_LIMIT)
            return true;

        return false;
    }

    function lastSeen()
    {
        $log = Log::where('mac_address',$this['mac_address'])->orderBy('timestamp','desc')->first();
        $current = Carbon::now('Asia/Kolkata');
        return $current->diffForHumans(Carbon::parse($log['timestamp']));   
    }
}
