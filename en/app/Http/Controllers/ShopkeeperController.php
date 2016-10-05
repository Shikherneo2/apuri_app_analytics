<?php namespace App\Http\Controllers;

use DB;
use Auth;
use App\Login;
use App\Shops;
use Validator;
use App\Shopkeeper;
use App\Custom\SSP;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class ShopkeeperController extends Controller {

	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Add a Shopkeeper.
	 *
	 * @param Request object
	 * @return web-response
	 */
	public function addShopkeeper(Request $request){
		if( Auth::user()->login_type == 3 || Auth::user()->login_type == 4){
			$validator = $this->validator($request->all(),false, true);

			if ($validator->fails()){
				$this->throwValidationException(
					$request, $validator
				);
			}
			
			switch( $this->create($request->all(), true) ){
				case 1: return redirect()->back()
							 ->withErrors([
								'notexists' => '入力したショップは私たちのデータベースでじゃない。',
							]);
							break;
				case 2: return redirect()->back()
							 ->withErrors([
								'exists' => 'あのショップに店主を割り当てました。　ほかのショップを入力してください。',
							]);
							break;				
				case 0: return redirect()->back()
					   		->with('success','ユーザが更新しました。');
			}
		}else{
			return redirect()->to('unauthorized');
		}
	}

	/**
	 * Manage a shopkeeper.
	 *
	 * @param  None
	 * @return web-response
	 */
	public function manage_shopkeeper(){
		if( Auth::user()->login_type <= 4)
			return view("project.manage.manage-shopkeeper");
		else
			return view("errors.404");
	}

	/**
	 * Delete a SHpokeeper.
	 *
	 * @param  Request object
	 * @return web-response
	 */
	public function deleteShopkeeper(Request $request){
		if( Auth::user()->login_type == 3 || Auth::user()->login_type == 4 ){
			
			$del_ids = Input::get("del_ids");
			
			if( is_array($del_ids) ){
				foreach($del_ids as $id){
					$validator = $this->validator(['userid'=>$id], true,true);

					if ($validator->fails()){
						$this->throwValidationException(
							$request, $validator
						);
					}

				}
				$res = true;
				foreach($del_ids as $id){
					//also check the login_type of the id that was given, so someone can not just change the id from source and delete someone else.
					$user = Login::where( ["id"=>$id, "login_type"=>5] )->first();
					if( !is_null($user) && $res==true)
						$res = $user->forceDelete();
					else{
						return redirect()->back->withErrors([
							'cant delete' => 'Sorry!　There was an error processing your request.',
						]);
					}

				}
			}
			else{
				return redirect()->back()
						->withErrors([
							'userid' => 'Sorry!　There was an error processing your request.',
						]);
			}

			return redirect()->back()
					->with('success','The User has been deleted.');
		}
		else{
			return redirect()->to("unauthorized");
		}
	}

	/**
	 * Get a validator for an incoming Shopkeeper creation/deletion request.
	 *
	 * @param  array  $data, int $id -- for delete
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	private function validator(array $data, $id){

		if($id){
			$messages = array(
    			'userid.required'  => 'User ID is required.',
    			'userid.numeric'  => 'User ID must be numeric.'
			);
			return Validator::make($data, [
				'userid' => 'required|numeric',
			], $messages); 
		}
		else{
			$messages = array(
	    		'email.required' => 'Email is required',
	    		'email.email' => 'The email you entered is invalid.',
	    		'email.max' => 'Email must be smaller than 255 characters.',
	    		'name.required' => 'Name is required.',
	    		'name.max' => 'Name must be smaller than 255 characters.',
	    		'shop_id.required' => 'Shop is required.',
	    		'shop_id.string' => 'Please enter a valid Shop name',
	    		'shop_id.max' => 'Shop name must be smaller than 255 characters.'
			);
			return Validator::make($data, [
				'name' => 'required|max:255',
				'email' => 'required|email|max:255|unique:users',
				'shop_id' => 'required|string|max:255'
			], $messages);
		}
	}

	/**
	 * Create a new Shopkeeper in the database.
	 *
	 * @param array  $data
	 * @return int -- status of request
	 */
	private function create(array $data){
		$user_type = Auth::user()->login_type;

		if( $user_type == 4 ){
            	$emp = Shopkeeper::join("shops", "shops.id", "=", "shopkeeper.shop_id")
            				->join("franchise", "shops.franchise_id", "=", "franchise.id")
			            	->where("franchise.id", Auth::user()->id)
			            	->where('shops.name', $data["shop_id"])
			                ->first();
	    }
	    else{
	    	$emp = Shopkeeper::join("shops", "shops.id", "=", "shopkeeper.shop_id")
	    				->where('shops.name', $data["shop_id"])
		                ->first();	
	    }            
		
		if( is_null($emp) ){
			$shop = Shops::where( "name", $data["shop_id"] )->first();
			if( !is_null($shop) ){

				DB::transaction(function($data) use ($data, $shop){
					$usernew = new Login;
					$usernew->login_type = 5;
					// create a random password and mail it to the user
					$usernew->password = bcrypt("spiderman");
					$usernew->save();

					$id_for_foreign = $usernew->id;
					$usernew->userid = $id_for_foreign;
					$usernew->save();

					$info_table = new Shopkeeper;
					$info_table->id = $id_for_foreign;
					$info_table->name = $data["name"];
					$info_table->email = $data['email'];
					$info_table->shop_id = $shop->id;
					$info_table->save();
				});
				return 0;
			}
			else{
				return 1;
			}
		}
		else{
			return 2;
		}
	}

	/**
	 * Auto-complete for Shop names.
	 *
	 * @param  Request object
	 * @return HTML response
	 */
    public function autoShopComplete(Request $request){
    	$user_type = Auth::user()->login_type;
        
        if ( $user_type <= 4) {
            $shop = $request->all();
            $keyword = '%' . $shop['keyword'] . '%';
            
            if( $user_type == 4 ){
            	$sql = Shops::join("franchise", "shops.franchise_id", "=", "franchise.id")
			            	->where("franchise.id", Auth::user()->userid)
			            	->where('shops.name', 'LIKE', $keyword)
			                ->orderBy('shops.id', 'asc')
			                ->take(10)
			                ->get(array("shops.*"));
		    }
		    else{
		    	$sql = Shops::where('shops.name', 'LIKE', $keyword)
			                ->orderBy('shops.name', 'asc')
			                ->take(10)
			                ->get();	
		    }            

            $result = '';

            foreach ($sql as $rs) {
                // put in bold the written text
                $shop_name = str_replace($shop['keyword'], '<b>' . $shop['keyword'] . '</b>', $rs->name);
                $shop_id = str_replace($shop['keyword'], '<b>' . $shop['keyword'] . '</b>', $rs->id);

                // add new option
                $result .= '<li onclick="set_item(\'' . str_replace("'", "\'", $rs->name) . '\')">' . $shop_name .'('. ($shop_id). ')</li>';
            }
            return $result;
        }
    }
	
	/**
	 * Get Shopkeeper records from database for Data Table.
	 *
	 * @param None
	 * @return JSON
	 */
	public function viewShopkeeperRecords(){
		$user_type = Auth::user()->login_type;

		if( $user_type <= 4 ){
			$table = 'shopkeeper';
			$primaryKey = 'id';

			$columns = array(
				array( 'db' => '`l`.`userid`', 'dt' => 0, 'field' => 'userid' ),
				array( 'db' => '`sk`.`name`',  'dt' => 1, 'field' => 'name' ),
			    array( 'db' => '`sk`.`shop_id`', 'dt' => 2, 'field' => 'shop_id' ),
			    array( 'db' => '`sh`.`name`', 'dt' => 3, 'field' => 'shop_name','as' => 'shop_name' ),
			    array( 'db' => '`sk`.`id`', 'dt' => 4, 'field' => 'id' )
			);

			if( $user_type == 4 )
	                $where = 'f.id = "'.Auth::user()->id.'"';
	        else
	            $where = '';
			
			$ssp_object = new SSP;
	   		$joinQuery = "FROM `{$table}` AS `sk` JOIN `login` AS `l` ON (`l`.`id` = `sk`.`id`) JOIN `shops` AS `sh` ON (`sh`.`id` =`sk`.`shop_id` ) JOIN `franchise` AS `f` ON (`sh`.`franchise_id` =`f`.`id` )";

	        return  json_encode(
	            $ssp_object->simple( $_GET, $table, $primaryKey, $columns, $joinQuery, $where)
	        );
    	}
    	else{
            return -1;
        }
	}
}
