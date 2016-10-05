<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Uploads extends Model {

	protected $table = 'uploads';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['userid', 'filename', 'created_at', 'updated_at'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public function login(){
		return $this->belongsTo('App\Login','userid','userid');
	}
}
