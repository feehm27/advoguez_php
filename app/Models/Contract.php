<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'start_date',
		'finish_date',
        'payment_day',
        'contract_price',
        'canceled_at',
        'fine_price',
        'agency',
        'account',
        'bank',
        'advocate_id',
        'client_id',
        'link_contract',
        'advocate_user_id'
	];

    public function client()
	{
		return $this->hasMany('App\Models\Client', 'id', 'client_id');
	}
}
