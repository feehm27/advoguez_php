<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

     /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'type',
		'name',
        'export_format',
        'advocate_user_id'
	];

    /**
     * ClientReport
     */
    public function clientReport()
	{
		return $this->hasMany('App\Models\ClientReport', 'report_id', 'id');
	}

     /**
     * ContractReport
     */
    public function contractReport()
	{
		return $this->hasMany('App\Models\ContractReport', 'report_id', 'id');
	}

     /**
     * ProcessReport
     */
    public function processReport() 
    {
        return $this->hasMany('App\Models\ProcessReport', 'report_id', 'id');
    }
}
