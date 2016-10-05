<?php namespace App\Http\Controllers;

use DB;
use Auth;
use File;
use App\Login;
use Validator;
use App\Shops;
use App\Uploads;
use App\Analytics;
use App\Custom\SSP;
use App\Http\Requests;
use App\Shop_employees;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class Shop_employeeController extends Controller {

	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}
	
	/**
	 * Show CSV upload information page
	 * @param  None
	 * @return web-response
	 */
	public function csvUploadInfo(){
		return view("project.general.csv_upload_info");
	}

	/**
	 * Add a Shop employee
	 * @param  Request object
	 * @return web-response
	 */
	public function addShop_employee(Request $request){
		if( Auth::user()->login_type == 3 || Auth::user()->login_type == 5){
			$validator = $this->validator($request->all(),false, true);

			if ($validator->fails()){
				$this->throwValidationException(
					$request, $validator
				);
			}
			
			switch( $this->create($request->all(), true) ){
				case 1: return redirect()->back()
							 ->withErrors([
								'notexists' => 'The Shop you entered does not exist in our records.',
							]);
							break;	
				case 0: return redirect()->back()
					   		->with('success','The user has been added.');
			}
		}
		else{
			return redirect()->to('unauthorized');
		}
	}

	/**
	 * Show Manage employee page
	 *
	 * @param  None
	 * @return web-response
	 */
	public function manage_Shop_employee(){
		$user_type = Auth::user()->login_type;
		if( $user_type <= 3 || $user_type == 5 ){
			$uploads = Uploads::where("userid", Auth::user()->userid)->where("type","csv")->first();
			if( is_null($uploads) )
				return view("project.manage.manage-shopemployee")->with("user_type", $user_type);
			else
				return view("project.manage.manage-shopemployee")->with("uploaded_csv",$uploads["filename"] )->with("user_type", $user_type);
		}
		else{
			return view("errors.404");
		}
	}

	/**
	 * Delete an Employee
	 *
	 * @param  Request object
	 * @return web-response
	 */
	public function deleteShop_employee(Request $request){
		if( Auth::user()->login_type == 3 || Auth::user()->login_type == 5){
			
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
					$user = Login::where( ["id"=>$id, "login_type"=>6] )->first();
					if( !is_null($user) && $res==true ){
						$res = $user->delete();
						$emp = Shop_employees::where("id",$id)->first();
						$emp->delete();
					}
					else{
						return redirect()->back()->withErrors([
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
	 * Add Shop Employees from uploaded CSV
	 *
	 * @param  None 
	 * @return web-response
	 */
	public function addEmployeeFromCSV(){
		if( Auth::user()->login_type <= 3 || Auth::user()->login_type == 5){
			$uploads = Uploads::where("userid", Auth::user()->userid)->where("type","csv")->first();
			$encoding = '';
	        if( is_null($uploads) ){
	            return redirect()->back()
						->withErrors([
							'nocsv' => 'Sorry!　There was an error processing your request.',
						]);
	        }
	        else{
	        	$csv_problem = false;
	        	$file = fopen(public_path()."/uploads/".$uploads->filename,"r");
	        	if($file!=false){
	        		$iter = 0;
	        		$data = array();
					while(($temp = fgetcsv($file)) !== FALSE ){
						$iter++;
						if($temp!=null && $temp!==false){
							if( (Auth::user()->login_type<=3 && count($temp)==3) || (Auth::user()->login_type==5 && count($temp)==2) ){
								
								if(count($temp)==3)
									$data[] = [ "name"=>$temp[0], "shop_id"=>$temp[1], "email"=>$temp[2] ];
								else
									$data[] = [ "name"=>$temp[0], "email"=>$temp[2] ];
							}
							else{
								$csv_problem = true;
								break;
							}
						}	
						else{
							$csv_problem = true;
							break;
						}
					}
					fclose($file);
					if($csv_problem===true){
						return redirect()->back()
										 ->withErrors([
										 	 'nocsv' => 'Oops! The CSV file you uploaded is invalid.',
										 ]);
					}
					else{
						$problem = false;
						foreach($data as $user){
							$validator = $this->validator($user,false, true);

							if ($validator->fails()){
								return redirect()->back()
												 ->withErrors([
												 	 'error' => 'Oops!　There is some problem with the input'.$validator->messages()->toJson(),
												 	]);
							}
							
							switch( $this->create($user, true) ){
								case 1: return redirect()->back()
														 ->withErrors([
														 	 'error' => 'Oops!　There is no such shop　－　"'.$user["shop_id"].'"',
														 	]);
							}
						}
						//delete the uploaded CSV
						File::delete(public_path()."/uploads/".$uploads->filename);
						$uploads->delete();

						return redirect()->back()
									     ->with("success",count($data)." users were added successfully");
					}	
	        	}
				else {
					return redirect()->back()->withErrors(['error' => 'Sorry!　The File could not be opened']);
				}
	    	}
	    }
	    else{
	    	return redirect()->to("unauthorized");
	    }	
	}

	/**
	 * Upload a CSV file to add users
	 *
	 * @param  Request $request  
	 * @return JSON
	 */
	public function uploadCSV( Request $request ){
        if( Auth::user()->login_type <= 3 || Auth::user()->login_type == 5){
            $destinationPath = '';
            $filename        = '';
            $uploadSuccess   = "";
            $filename        = "";
            $validator       = Validator::make(
                array('trackfile' => Input::file('csv')), 
                array('trackfile' => 'required|mimes:csv,txt')
            );
            
            if($validator->fails()){
                $return_value["error"] = "2";
                return json_encode($return_value);
            }
            else{
                $uploads = Uploads::where("userid", Auth::user()->userid)->where("type","csv")->first();

                if( is_null($uploads) ){
                    $uploads = new Uploads;
                    $uploads->userid = Auth::user()->userid;
                }
                else{
                    File::delete(public_path()."/uploads/".$uploads->filename);
                }   

                if (Input::hasFile('csv')) {
                    $file            = Input::file('csv');
                    $destinationPath = public_path().'/uploads/';
                    $filename        = str_random(25).".".pathinfo($file->getClientOriginalName() , PATHINFO_EXTENSION);
                    $uploadSuccess   = $file->move($destinationPath, $filename);
                }
                if($uploadSuccess!=""){
                    $uploads->filename = $filename;
                    $uploads->type = "csv";
                    $uploads->save();
                    $return_value["filename"] = $filename;
                    $return_value["error"] = "0";
                    $return_value["special"] = $filename;
                }   
                else
                    $return_value["error"] = "1";
                return json_encode($return_value);
            }
        }
        else{
            return redirect()->to("unauthorized");
        }
    }

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data, boolean $id 
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
		else if( Auth::user()->login_type == 5 ){
			$messages = array(
	    		'email.required' => 'Email is required',
	    		'email.email' => 'The email you entered is invalid.',
	    		'email.max' => 'Email must be smaller than 255 characters.',
	    		'name.required' => 'Name is required.',
	    		'name.max' => 'Name must be smaller than 255 characters.'
			);
			return Validator::make($data, [
				'name' => 'required|max:255',
				'email' => 'required|email|max:255|unique:users'
			], $messages);
		}
		else{
			$messages = array(
	    		'email.required' => 'Email is required',
	    		'email.email' => 'The email you entered is invalid.',
	    		'email.max' => 'Email must be smaller than 255 characters.',
	    		'name.required' => 'Name is required.',
	    		'name.max' => 'Name must be smaller than 255 characters.'
	    		'shop_id.required' => 'Shop is required.',
	    		'shop_id.string' => 'Please enter a valid Shop name.',
	    		'shop_id.max' => 'Shop name must be smaller than 255 characters.'
			);
			return Validator::make($data, [
				'name' => 'required|max:255',
				'email' => 'required|email|max:255|unique:users',
				'shop_id' => 'required||max:255'
			], $messages);
		}
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	private function create(array $data){
		
		if(Auth::user()->login_type==5)
			$shop = Shops::where( "id", Auth::user()->shopkeeper->shop_id )->first();
		else	
			$shop = Shops::where( "name", $data["shop_id"] )->first();
		if( !is_null($shop) ){

			DB::transaction(function($data) use ($data, $shop){
				$usernew = new Login;
				$usernew->login_type = 6;
				// create a random password and mail it to the user
				$usernew->password = bcrypt("spiderman");
				$usernew->save();

				$id_for_foreign = $usernew->id;
				$usernew->userid = $id_for_foreign;
				$usernew->save();

				$info_table = new Shop_employees;
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

	/**
	 * AutoComplete Function for entering Shop names
	 *
	 * @param  Request $request
	 * @return JSON
	 */
    public function autoShopComplete(Request $request){
        if (Auth::user()->login_type <= 3 || Auth::user()->login_type == 5) {
            $shop = $request->all();
            $keyword = '%' . $shop['keyword'] . '%';
            $sql = Shops::where('name', 'LIKE', $keyword)
                ->orderBy('id', 'asc')
                ->take(10)
                ->get();

            $result = '';

            foreach ($sql as $rs) {
                // put in bold the written text
                $shop_name = str_replace($shop['keyword'], '<b>' . $shop['keyword'] . '</b>', $rs->name);

                // add new option
                $result .= '<li onclick="set_item(\'' . str_replace("'", "\'", $rs['name']) . '\')">' . $shop_name . '</li>';
            }
            return $result;
        }
    }

    /**
	 * View Shop Employee Records - JSON for Data-Table
	 *
	 * @param  void
	 * @return JSON
	 */
    public function viewShop_employeeRecords(){
    	$user_type = Auth::user()->login_type;

		if( $user_type <= 5 ){
	    	$table = 'shop_employees';
			$primaryKey = 'id';

			$columns = array(
				array( 'db' => '`l`.`userid`', 'dt' => 0, 'field' => 'userid' ),
			    array( 'db' => '`s`.`name`',  'dt' => 1, 'field' => 'name' ),
			    array( 'db' => '`s`.`shop_id`', 'dt' => 2, 'field' => 'shop_id' ),
			    array( 'db' => '`sh`.`name`',  'dt' => 3, 'field' => 'shop_name','as' => 'shop_name' ),
			    array( 'db' => '`s`.`id`', 'dt' => 4, 'field' => 'id' )
			);

			if( $user_type == 4 ){
				$joinQuery = "FROM `{$table}` AS `s` JOIN `login` AS `l` ON (`l`.`id` = `s`.`id`) JOIN `shops` AS `sh` ON (`sh`.`id` =`s`.`shop_id` ) JOIN `franchise` AS `f` ON (`sh`.`franchise_id` =`f`.`id` )";
				$where = 'f.id = "'.Auth::user()->id.'"';
			}
			else if( $user_type == 5 ){
				$joinQuery = "FROM `{$table}` AS `s` JOIN `login` AS `l` ON (`l`.`id` = `s`.`id`) JOIN `shops` AS `sh` ON (`sh`.`id` =`s`.`shop_id` )";
				$where = 's.shop_id = "'.Auth::user()->shopkeeper->shop_id.'"';

				$columns = array(
					array( 'db' => '`l`.`userid`', 'dt' => 0, 'field' => 'userid' ),
				    array( 'db' => '`s`.`name`',  'dt' => 1, 'field' => 'name' ),
				    array( 'db' => '`s`.`id`', 'dt' => 2, 'field' => 'id' )
				);
			}
			else{
				$joinQuery = "FROM `{$table}` AS `s` JOIN `login` AS `l` ON (`l`.`id` = `s`.`id`) JOIN `shops` AS `sh` ON (`sh`.`id` =`s`.`shop_id` )";
				$where = "`s`.`deleted_at` is NULL";	
			}

			$ssp_object = new SSP;
			return  json_encode(
				$ssp_object->simple( $_GET, $table, $primaryKey, $columns, $joinQuery, $where)
			);
		}
    }
}