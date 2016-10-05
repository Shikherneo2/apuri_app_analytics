<?php namespace App\Http\Middleware;

use Closure;
use Request;
use App\Login;
use App\Password_resets;

class resetPassword {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$data = $request->all();
		$id = $data["key"];

		$reset_table = Password_resets::where("token", $id)->first();
		if( !is_null($reset_table) ){
			$reset_table->delete();
			return $next($request);
		}
		else{
			return redirect()->to("404notfound");
		}
		
	}
}
