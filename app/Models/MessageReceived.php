<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageReceived extends Model
{
    use HasFactory;

    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'code_message',
		'subject',
        'message',
        'client_id',
        'advocate_user_id',
	];

    public function client()
	{
		return $this->hasMany('App\Models\Client', 'id', 'client_id');
	}

	public function advocate()
	{
		return $this->hasMany('App\Models\User', 'id', 'advocate_user_id');
	}
}
