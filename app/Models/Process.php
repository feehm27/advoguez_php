<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'number',
		'labor_stick',
        'petition',
        'status',
        'file',
        'start_date',
        'end_date',
        'observations',
        'client_id',
        'advocate_user_id'
	];

    public function client()
	{
		return $this->hasMany('App\Models\Client', 'id', 'client_id');
	}
}
