<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advocate extends Model
{
    use HasFactory;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'name',
		'cpf',
		'nationality',
		'civil_status',
		'register_oab',
		'register_oab',
		'email',
		'cep',
		'street',
		'number',
		'complement',
		'district',
		'state',
		'city',
		'agency',
		'account',
		'bank',
		'user_id',
	];
}
