<?php namespace App\Http\Controllers;

use DB;
use Auth;
use App\Login;
use App\Admin;
use Validator;
use App\Custom\SSP;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller {
	
	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Add an Admin.
	 *
	 * @param  Request object
	 * @return web-response
	 */
	public function addAdmin(Request $request){
		if( Auth::user()->login_type ==2 ){
			$validator = $this->validator($request->all(),false, true);

			if ($validator->fails()){
				$this->throwValidationException(
					$request, $validator
				);
			}
			
			if( $this->create($request->all(), true) ){
				return redirect()->back()
				   ->with('success','The User was added Successfully');
			}
		    else{
		   		return redirect()->back()
			  	->withErrors([
						'exists' => '入力したIDのアカウントはすでに存在します。　ほかのIDを入力してください。',
					]);
			}
		}
		else{
			return redirect()->to('unauthorized');
		}
	}

	/**
	 * Delete an Admin.
	 *
	 * @param  Request object
	 * @return web-response
	 */
	public function deleteAdmin(Request $request){
		if( Auth::user()->login_type ==2 ){
			
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
					$user = Login::where( ["userid"=>$id, "login_type"=>3] );
					if( !is_null($user) && $res==true)
						$res = $user->forceDelete();
					else{
						return redirect()->back->withErrors([
							'cant delete' => 'おっとっと!　リクエスト処理中に問題が発生しました.',
						]);
					}

				}
			}
			else{
				return redirect()->back()
						->withErrors([
							'userid' => 'おっとっと!　リクエスト処理中に問題が発生しました.',
						]);
			}

			return redirect()->back()
					->with('success','ユーザは削除しました。');
		}
		else{
			return redirect()->to("unauthorized");
		}
	}

	

	/**
	 * Get a validator for an incoming registration/deletion request.
	 *
	 * @param  array  $data, int $id for delete
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	private function validator(array $data, $id){
		$data["userid"] = str_replace("H", "", $data["userid"] ); 

		if($id){
			$messages = array(
	    		'userid.required'  => 'ユーザIDが必要です。',
	    		'userid.digits'  => 'ユーザIDは 7 桁でなければなりません。'
			);
			return Validator::make($data, [
				'userid' => 'required|digits:7',
			], $messages); 
		}
		else{
			$messages = array(
	    		'email.required' => 'メール　アドレスが必要です。',
	    		'email.email' => '入力したメールは無効です。',
	    		'email.max' => 'メール　アドレスの長さは255文字以下のなければなりません。。',
	    		'name.required' => '名前が必要です。',
	    		'name.max' => '名前の長さは255文字以下のなければなりません。。',
	    		'userid.required'  => 'ユーザIDが必要です。',
	    		'userid.digits'  => 'ユーザIDは 7 桁でなければなりません。'
			);
			return Validator::make($data, [
				'name' => 'required|max:255',
				'userid' => 'required|digits:7',
				'email' => 'required|email|max:255|unique:users'
			], $messages);
		}
	}

	/**
	 * Create a new Admin in the database.
	 *
	 * @param  array $data
	 * @return Boolean 
	 */
	private function create(array $data){
		
		$tempid = "H".$data['userid'];
		$usernew = Login::firstOrNew(array('userid' => $tempid));
		
		if( is_null($usernew->id) ){
			DB::transaction(function($data) use ($data, $usernew){
				$usernew->userid = ("H".$data['userid']);
				$usernew->login_type = 3;
				$usernew->password = bcrypt("spiderman");
				$usernew->save();

				$id_for_foreign = $usernew->id;

				$info_table = new Admin;
				$info_table->id = $id_for_foreign;
				$info_table->name = $data["name"];
				$info_table->email = $data['email'];
				$info_table->save();
			});
			return true;
		}
		else
			return false;
	}

	/**
	 * Get Admin list from database.
	 *
	 * @param  None
	 * @return JSON
	 */
	public function viewAdminRecords( ){
		
		if( Auth::user()->login_type <=2 ){
			$table = 'admin';
			$primaryKey = 'id';

			$columns = array(
				array( 'db' => '`l`.`userid`', 'dt' => 0, 'field' => 'userid' ),
				array( 'db' => '`ha`.`name`',  'dt' => 1, 'field' => 'name' ),
				array( 'db' => '`ha`.`email`',   'dt' => 2, 'field' => 'email' ),
			    array( 'db' => '`ha`.`id`', 'dt' => 3, 'field' => 'id' )
			);

			$joinQuery = "FROM `{$table}` AS `ha` JOIN `login` AS `l` ON (`l`.`id` = `ha`.`id`)";
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
