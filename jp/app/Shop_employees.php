<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shop_employees extends Model {
    use SoftDeletes;

	protected $table = 'shop_employees';
    protected $dates = ['deleted_at'];
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'shop_id', 'email','deleted_at'];

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
