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
        'fine_price',
        'agency',
        'account',
        'bank',
        'advocate_id',
        'client_id'
	];
}
