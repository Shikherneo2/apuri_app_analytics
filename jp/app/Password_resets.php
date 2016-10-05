<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Password_resets extends Model {

	protected $table = 'password_resets';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['userid', 'token','created_at'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public function login_info(){
		return $this->belongsTo("App\Login",'userid','userid');
	}
}
