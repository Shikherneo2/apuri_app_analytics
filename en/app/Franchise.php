<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Franchise extends Model {

	protected $table = 'franchise';

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

	public function shops(){
		return $this->hasMany('App\Shops','franchise_id','id');
	}
}
