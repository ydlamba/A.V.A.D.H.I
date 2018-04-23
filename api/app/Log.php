<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	protected $table="logs";

	public function user()
	{
		return $this->hasOne('App\User');
	}
}
