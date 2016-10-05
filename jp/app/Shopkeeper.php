<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Shopkeeper extends Model {

	protected $table = 'shopkeeper';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'shop_id', 'email'];

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

	public function analytics(){
		return $this->hasMany('App\Analytics','emp_id','id');
	}

	public function shop(){
		return $this->belongsTo('App\Shops','shop_id','id');
	}
}
