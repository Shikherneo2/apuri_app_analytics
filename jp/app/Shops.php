<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Shops extends Model {

	protected $table = 'shops';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'location', 'franchise_id','deleted_installs'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	public function franchise(){
		return $this->belongsTo("App\Franchise",'franchise_id','id');
	}

	public function shop_employee(){
		return $this->hasMany("App\Shop_employees","shop_id","id");
	}

	public function shopkeeper(){
		return $this->hasOne("App\Shopkeeper","shop_id","id");
	}

}
