<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientReport extends Model
{
    use HasFactory;

     /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'birthday',
		'registration_date',
        'gender',
        'civil_status',
        'link_report',
        'report_id'
	];
}
