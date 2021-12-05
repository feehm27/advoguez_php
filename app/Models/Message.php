<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'sender_name',
		'recipient_name',
		'recipient_email',
		'subject',
		'message',
		'read',
		'client_sent',
		'advocate_sent',
		'user_id'
	];
}
