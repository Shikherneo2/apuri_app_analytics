<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Request;

class MiscController extends Controller {

	//Call middleware to check for authentication for login function only
	public function __construct(){
		$this->middleware('guest',['only'=>'login']);
	}

	/**
	 * Show Login Screen.
	 *
	 * @param  None
	 * @return web-response
	 */
	public function login(){
		return view('project.login.login');
	}

	/**
	 * Show unauthorized error .
	 *
	 * @param  None
	 * @return web-response
	 */
	public function unauthorized(){
		return view('errors.unauthorized');	
	}

	/**
	 * Show a 404 error.
	 *
	 * @param  None
	 * @return web-response
	 */
	public function notfound(){
		return view('errors.404');	
	}
}
