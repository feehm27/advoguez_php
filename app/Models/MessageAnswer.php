<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageAnswer extends Model
{
    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'answer',
		'code_message',
		'response_client',
		'response_advocate',
		'message_received_id',
		'advocate_user_id',
	];

}
