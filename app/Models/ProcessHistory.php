<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessHistory extends Model
{
    use HasFactory;

    
    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'modification_date',
		'status_process',
        'modification_description',
        'process_id',
	];
}
