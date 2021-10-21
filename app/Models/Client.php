<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
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
		'name',
		'email',
		'cpf',
		'rg',
		'issuing_organ',
		'nationality',
		'birthday',
		'gender',
		'civil_status',
        'telephone',
        'cellphone',
        'cep',
		'street',
		'number',
		'complement',
		'district',
		'state',
		'city',
        'advocate_user_id'
	];

	public function user()
	{
		return $this->hasOne(ClientUser::class);
	}
}
