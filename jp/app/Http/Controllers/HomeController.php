<?php namespace App\Http\Controllers;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class HomeController extends Controller {

	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Display home page
	 *
	 * @return Response
	 */

	public function home(){
		$user_status = 0;
		if( Auth::check() )
			$user_status = 1;
		return view("project.general.home")->with("type",Auth::user()->login_type);
	}
}
