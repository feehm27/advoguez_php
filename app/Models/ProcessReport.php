<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessReport extends Model
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
		'end_date',
        'stage',
        'link_report',
        'report_id',
	];
}
