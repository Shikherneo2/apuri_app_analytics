<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model {

	protected $table = 'analytics';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['app_id', 'emp_id', 'counter', 'device_id','device_id'];

	
	//Model Relationships
	public function shop_employee(){
		return $this->belongsTo("App\Shop_employees",'emp_id','id');
	}
	
	public function app(){
		return $this->belongsTo("App\Apps",'app_id','id');
	}
}
