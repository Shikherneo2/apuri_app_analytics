<?php namespace App\Http\Controllers\Auth;

use DB;
use Hash;
use Auth;
use Validator;
use App\Login;
use App\Franchise;
use App\Shopkeeper;
use App\Admin;
use App\Http\Requests;
use App\Shop_employees;
use App\Password_resets;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;

class PasswordHandlerController extends Controller {

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest',["only"=>["showReset","editReset","reset"] ]);
		$this->middleware('auth',["except"=>["showReset","editReset","reset"] ]);
		$this->middleware('resetPassword',["only"=>"editChangeReset"] );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function showChange()
	{
		return view("project.login.change-password");
	}

	public function showReset()
	{
		return view("project.login.reset-password");
	}

	//The handler for the screen to be shown after reset link click 
	public function reset( $key ){
		$reset_table = Password_resets::where("token", $key)->first();
		if( is_null($reset_table) )
			return redirect()->to("404notfound");
		else{
			try{
				$user = $reset_table->login_info;
				$temp_password_cleartext = str_random(20);
				$temp_password_hash = bcrypt( $temp_password_cleartext );

				$user->password = $temp_password_hash;
				$response = $user->save();
				
				if( $response ){
					$id = $reset_table->login_info->id;
					$user = Auth::loginUsingId( $id );
					if( !$user ){
						return redirect()->to("404notfound");
					}

					//what the fuck do i do here???
					return redirect()->to("change_password")->with("reset",$key);	
				}
				else
					return redirect()->to("404notfound");
			}
			catch(Exception $e){
				return redirect()->to("404notfound");	
			}
		}
	}

	public function editChange( Request $request )
	{
		$data = $request->all();

		$validator = $this->validator($data,true);

		if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);
		}
		
		if( Hash::check( $data["old_password"], Auth::user()->password ) ){
			$this->setNewPassword($request->all(), Auth::user());
			return redirect()->back()
		   		->with('success','Password has been changed.');	
		}
		else
			return redirect()->back()
		   		->withErrors([
							'error' => 'The current password you entered is incorrect. Please try again.',
						]);
	}

	//controller for the reset password screen (Forgot password)
	public function editChangeReset( Request $request )
	{
		$data = $request->all();

		$validator = $this->validatorChangeReset($data,true);

		if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);
		}
		
		$this->setNewPassword($request->all(), Auth::user());
		return redirect()->to("home")
	   		->with('success','Password has been reset.');	
	
	}

	private function checkUser( $userid ){
		try{
			$user = Login::where("userid", $userid)->first();
			if(is_null($user))
				return 1;
			else
				return 0;
		}
		catch(Exception $exception){
			return 2;
		}
	}

	private function setNewPassword( array $data , $user ){
		
		DB::transaction(function($data) use ($data, $user){
			
			// create a random password and mail it to the user
			$user->password = bcrypt($data["new_password"]);
			$user->save();
		});
	
	 }

	//validators
	private function validator( array $data ){

		$messages = array(
            'old_password.required' => 'Current Password is required.',
            'new_password.required' => 'New Password is required',
            'new_password.alpha' => 'Please only enter alphanumeric characters',
            'new_password.max' => 'Please enter password shorter than 160 characters.'
        );

		return Validator::make($data, [
			'old_password' => 'required',
			'new_password' => 'required|confirmed|alpha|max:160',
			'new_password_confirmation' => 'required'
		], $messages); 
	}

	private function validatorChangeReset( array $data ){
		$messages = array(
            'new_password.required' => 'New Password is required',
            'new_password.alpha' => 'Please only enter alphanumeric characters',
            'new_password.max' => 'Please enter password shorter than 160 characters.'
        );
		return Validator::make($data, [
			'new_password' => 'required|confirmed|alpha|max:160',
			'new_password_confirmation' => 'required'
		], $messages); 
	}
}