<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use DB;
use Auth;
use File;
use Crypt;
use App\Custom\SSP;
use App\Apps;
use App\Uploads;
use Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AppController extends Controller {

	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Add an App.
	 *
	 * @param  Request object
	 * @return web-response
	 */
	public function add(Request $request){
		if( Auth::user()->login_type <= 3 ){
			$validator = $this->validator($request->all(),false, true);

			if ($validator->fails()){
				$this->throwValidationException(
					$request, $validator
				);
			}
			
			switch( $this->create($request->all(), true) ){
				case 1: return redirect()->back()
							 ->withErrors([
								'exists' => 'There is another app with that package name. Please try again.',
							]);
							break;	
				case 0: return redirect()->back()
					   		->with('success','The app has been added.');
			}
		}
		else{
			return redirect()->to('unauthorized');
		}
	}

	/**
	 * Display the manage app page.
	 *
	 * @param  None
	 * @return web-response
	 */
	public function manage(){
		if( Auth::user()->login_type <= 3 ){
			$uploads = Uploads::where("userid", Auth::user()->id)->where("type","img")->first();
			if( is_null($uploads) )
				return view("project.manage.manage-app");
			else
				return view("project.manage.manage-app")->with( "logo_src",$uploads["filename"] );
		}
		else{
			return view("errors.404");
		}
	}


	/**
	 * View app-list.
	 *
	 * @param None
	 * @return web-response
	 */
	public function view(){
		if( Auth::user()->login_type <= 3 )
            return view("project.app-list");
        else
            return view("errors.404");
	}

	/**
	 * Show update page for an app.
	 *
	 * @param  int app-id
	 * @return web-response
	 */
	public function showUpdate($id){
		if( Auth::user()->login_type <= 3 ){
			$app = Apps::where( "id", $id )->first();
			if( !is_null($app) ){
				return view("project.edit-app")->with('app', $app);
			}
			else{
				return redirect()->to("404notfound");
			}
		}
		else
			return view("errors.404");
	}

	/**
	 * Edit an App.
	 *
	 * @param  Request $request
	 * @return web-response
	 */
	public function editApp(Request $request){
		
		if( Auth::user()->login_type <= 3 ){
			$data = $request->all();
			$app = Apps::where( "id", $data["id"] )->first();

			if( !is_null($app) ){
				DB::transaction(function($data) use ($data, $app){

					$info_table = clone $app;
					$info_table->name = $data["name"];
					$info_table->type = $data['type'];
					$info_table->desc = $data['desc'];
					$info_table->store_link = $data['store_link'];
					$info_table->package_name = $data['package_name'];
					$info_table->save();
				});
				return redirect()->back()
						   		->with('success','The app has been updated.');
			}
			else{
				return redirect()->back()
								 ->withErrors([
									'exists' => 'This app does not exist in our records.',
								]);
			}
		}
		else
			return redirect()->to('unauthorized');
	}

	/**
	 * Delete an App.
	 *
	 * @param  Request $request
	 * @return web-response
	 */
	public function delete(Request $request){
		if( Auth::user()->login_type <= 3 ){
			
			$del_ids = Input::get("del_ids");
			
			if( is_array($del_ids) ){
				foreach($del_ids as $id){
					$validator = $this->validator(['id'=>$id], true,true);

					if ($validator->fails()){
						$this->throwValidationException(
							$request, $validator
						);
					}
				}
				$res = true;
				foreach($del_ids as $id){
					$app = Apps::where("id", $id)->first();
					if( $res == true){
						File::delete(public_path()."/uploads/".$res->filename);
						$res = $app->delete();

					}
					else{
						return redirect()->back()->withErrors([
							'cant delete' => "Sorry!　There was an error processing your request.",
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
					->with('success','The app has been deleted.');
		}
		else{
			return redirect()->to("unauthorized");
		}
	}

	/**
	 * Upload logo for an app.
	 *
	 * @param  Request $request
	 * @return web-response
	 */
	public function uploadLogo( Request $request ){
		if( Auth::user()->login_type <= 3 ){
			$destinationPath = '';
		    $filename        = '';
		    $uploadSuccess = "";
		    $filename = "";
		    $validator = Validator::make(
			    array('trackfile' => Input::file('image')), 
			    array('trackfile' => 'required|mimes:jpg,jpeg,gif,png')
			);
			
			if($validator->fails()){
				$return_value["error"] = "1";
			    return json_encode($return_value);
			}
			else{
				$uploads = Uploads::where("userid", Auth::user()->id)->where("type","img")->first();

				if( is_null($uploads) ){
					$uploads = new Uploads;
					$uploads->userid = Auth::user()->id;
				}
				else{
					File::delete(public_path()."/uploads/".$uploads->filename);
					$uploads->delete();
					$uploads = new Uploads;
					$uploads->userid = Auth::user()->id;
				}	

			    if (Input::hasFile('image')) {
			        $file            = Input::file('image');
			        $destinationPath = public_path().'/uploads/';
			        $filename        = str_random(25).".".pathinfo($file->getClientOriginalName() , PATHINFO_EXTENSION);
			        $uploadSuccess   = $file->move($destinationPath, $filename);
			    }
			    if($uploadSuccess!=""){
					$uploads->filename = $filename;
					$uploads->type = "img";
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
	 * Upload logo for an app when it is updated.
	 *
	 * @param  Request $request
	 * @return web-response
	 */
	public function uploadLogoUpdateApp( Request $request ){
		if( Auth::user()->login_type <= 3 ){
			$destinationPath = '';
		    $filename        = '';
		    $uploadSuccess = "";
		    $filename = "";
		    $validator = Validator::make(
			    array('trackfile' => Input::file('image')), 
			    array('trackfile' => 'required|mimes:jpg,jpeg,gif,png'),
			    array('trackfile' => $request->input("app_id")), 
			    array('app_id' => "required|numeric")
			);
			
			if($validator->fails()){
				$return_value["error"] = "1";
			    return json_encode($return_value);
			}
			else{
				$app = Apps::where("id",$request->input("app_id") )->first();

		        $file            = Input::file('image');
		        $destinationPath = public_path().'/uploads/';
		        $filename        = str_random(25).".".pathinfo($file->getClientOriginalName() , PATHINFO_EXTENSION);
		        $uploadSuccess   = $file->move($destinationPath, $filename);
		    
			    if($uploadSuccess!=""){
					$app->img_src = $filename;
					$app->save();
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
	 * Get a validator for an incoming add-app request.
	 *
	 * @param  array  $data, int $id - app-id for delete
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	private function validator(array $data, $id){
		$messages = array(
    		'package_name.required'  => 'Package Name is required.',
    		'package_name.max'  => 'Package name must be smaller than 160 characters.',
    		'type.required' => 'Platform field is required.',
    		'name.required' => 'Name field is required.',
    		'name.max' => 'Name must be smaller than 40 characters.',
    		'store_link.required' => 'Store Link field is required.',
    		'store_link.max' => 'Store link must be smaller than 2083 characters.'
		); 

		if($id){
			return Validator::make($data, [
				'id' => 'required|numeric',
			]); 
		}
		else{
			return Validator::make($data, [
				'package_name' => 'required|max:160',
				'type' => 'required|digits:1',
				'name' => 'required|max:40',
				'desc' => 'required|max:2700',
				'store_link' => 'required|max:2083'
			],$messages);
		}
	}

	/**
	 * Create an app in the database.
	 *
	 * @param  array $data
	 * @return int request-success
	 */
	private function create(array $data){
		
		$app = Apps::where( ["package_name" => $data["package_name"], 'type'=>$data["type"] ] )->first();
		if( is_null($app) ){
			DB::transaction(function($data) use ($data){

				$info_table = new Apps;
				$info_table->name = $data["name"];
				$info_table->type = $data['type'];
				$info_table->desc = $data['desc'];
				$info_table->store_link = $data['store_link'];
				$info_table->package_name = $data['package_name'];
				
				//get the uploaded file from uploads table
				$uploads = Uploads::where("userid", Auth::user()->id)->where("type","img")->first();
				if( !is_null($uploads) ){
					$info_table->img_src = $uploads->filename;
					$uploads->delete();
				}
			
				$info_table->save();
				
			});
			return 0;
		}
		else{
			return 1;
		}
	}

	/**
	 * Find app list from database.
	 *
	 * @param  None
	 * @return JSON or int for failure
	 */
	public function viewAppRecords(){
		if( Auth::user()->login_type <=3 ){
			$table = 'apps';
			$primaryKey = 'id';

			$columns = array(
				array( 'db' => 'id', 'dt' => 0, 'field' => 'id' ),
			    array( 'db' => 'name', 'dt' => 1, 'field' => 'name' ),
				array( 'db' => 'type',  'dt' => 2, 'field' => 'type' ),
			    array( 'db' => 'type',  'dt' => 3, 'field' => 'type' ),
			    array( 'db' => 'id', 'dt' => 4, 'field' => 'id' )
			);

			$joinQuery = false;
			$ssp_object = new SSP;
			return  json_encode(
				$ssp_object->simple( $_GET, $table, $primaryKey, $columns, $joinQuery)
			);
		}
		else{
			return -1;
		}
	}
}
