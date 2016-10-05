<?php namespace App\Services;

use App\User;
use Validator;
use Illuminate\Contracts\Auth\Registrar as RegistrarContract;

class Registrar implements RegistrarContract {

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	public function validator(array $data)
	{
		$messages = array(
    		'_user_type.required' => 'おっとっと!　リクエスト処理中に問題が発生しました.',
    		'_user_type.integer'  => 'おっとっと!　リクエスト処理中に問題が発生しました.',
    		'_user_type.between' => 'おっとっと!　リクエスト処理中に問題が発生しました.'
		);
		return Validator::make($data, [
			'name' => 'required|max:255',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
			'_user_type' => 'required|integer|between:1,6'
		], $messages);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return User
	 */
	public function create(array $data)
	{
		return User::create([
			'name' => $data['name'],
			'email' => $data['email'],
			'login_type' => $data['_user_type'],
			'password' => bcrypt($data['password']),
		]);
	}

}
