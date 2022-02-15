<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvocateSchedule extends Model
{
    use HasFactory;

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'date',
		'horarys',
		'time_type',
		'color',
		'advocate_user_id',
		'client_id'
	];
	
	public function client()
	{
		return $this->hasMany('App\Models\Client', 'id', 'client_id');
	}
}
