<?php 
namespace App\Http\Controllers\Auth;

use Auth;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Registrar;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Registration & Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users, as well as the
	| authentication of existing users. By default, this controller uses
	| a simple trait to add these behaviors. Why don't you explore it?
	|
	*/

	use AuthenticatesAndRegistersUsers;

	/**
	 * Create a new authentication controller instance.
	 *
	 * @param  \Illuminate\Contracts\Auth\Guard  $auth
	 * @param  \Illuminate\Contracts\Auth\Registrar  $registrar
	 * @return void
	 */
	public function __construct(Guard $auth, Registrar $registrar)
	{
		$this->auth = $auth;
		$this->registrar = $registrar;

		$this->middleware('guest', ['except' => 'getLogout']);
	}

	// Shikher -- This function overrides the function of the same name in AuthenticatesAndRegistersUsers trait

	public function postLogin(Request $request){
	    
	    $validator = $this->validator($request->all());

	    if ($validator->fails()){
				$this->throwValidationException(
					$request, $validator
				);
		}

		$credentials = $request->only('userid', 'password');
		
		if ($this->auth->attempt($credentials, $request->has('remember'))){
			return redirect()->intended($this->redirectPath());
		}

		return redirect()->back()
						 ->withInput($request->only('userid', 'remember'))
						 ->withErrors([
						 	'userid' => 'The username-password combination does not match any records.',
						 ]);
	}

	public function postRegister(Request $request){
		
		$validator = $this->registrar->validator($request->all());

		if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);
		}
		return redirect()->back()
				   		 ->with('success','The user was added');
	}

	public function getLogin(){
		return view('project.login.login');
	}

	public function getLogout(){
		$this->auth->logout();
		return redirect('/login');
	}
	
	/**
	 * Shikher -- Check if the user has the authority to create the type of user-creation requested.
	 *
	 * @return Boolean
	 */
	private function checkUserAuthority($request){
		$this_user = Auth::user();
		$reg_wanted = $request->input("_user_type");

		if($this_user != null){
			switch($this_user->login_type){
				//We can add anyone
				case 1: 
						return true;

				//Super-Admin can only add Admins
				case 2: 
						if($reg_wanted == 3)
							return true;
						else
							return false;

				//Admins can add Franchise Owners, ShopKeepers and Shop Employees		
				case 3: 
						if( $reg_wanted == 4 || $reg_wanted == 5 || $reg_wanted == 6 )
							return true;
						else
							return false;

				//Franchise can only add Shopkeepers		
				case 4: 
						if($reg_wanted == 5)
							return true;
						else
							return false;
				
				//Shopkeepers can only add Shop Employees
				case 5: 
						if($reg_wanted == 6)
							return true;
						else
							return false;

				//Shop Employee can add no one		
				case 6: return false;
			}
		}
	}

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	private function validator(array $data){
		$messages = array(
    		'userid.required' => 'The Username is required',
    		'userid.max' => 'You exceeded the maximun limit of characters for Username',
    		'password.required' => 'Password is required',
    		'password.max' => 'You exceeded the maximun limit of characters for Password'
		);
		return Validator::make($data, [
			'userid' => 'required|max:255',
			'password' => 'required|max:255'
		], $messages);
	}
}