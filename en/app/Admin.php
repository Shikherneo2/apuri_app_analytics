<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model {

	protected $table = 'admin';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = [];

	/**
	 * The Login Relationship.
	 *
	 * @var function
	 */
	public function login_info(){
		return $this->belongsTo("App\Login",'id','id');
	}
}
