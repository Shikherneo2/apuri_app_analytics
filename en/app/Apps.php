<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Apps extends Model {

	protected $table = 'apps';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['package_name', 'type', 'name', 'desc', 'img_src', 'store_link'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	public function analytics(){
		return $this->hasMany('App\Analytics','app_id','id');
	}
}
