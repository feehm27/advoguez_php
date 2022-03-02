<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractReport extends Model
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
        'canceled_at',
        'status',
        'payment_day',
        'link_report',
        'report_id',
	];
}
