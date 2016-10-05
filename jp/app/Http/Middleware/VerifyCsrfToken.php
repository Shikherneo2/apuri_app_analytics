<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\Security\Core\Util\StringUtils;

class VerifyCsrfToken extends BaseVerifier {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		return parent::handle($request, $next);
	}
	
	/**
	 * Overridden Token matching function to incorporate AJAX requests
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return boolean
	 * Author -- Shikhar Dev Gupta
	 */
	protected function tokensMatch($request){
	  $token = $request->session()->token();
	  
	  $header = $request->header('X-CSRF-TOKEN');
	  
	  if ($request->ajax()){
	  	if( $header!="" ){
	  		return StringUtils::equals($token, $header) ;
	  	} 
	  	else if( $request->has('_token') ){
	  		return StringUtils::equals($token, $request->input('_token')) ;	
	  	}
	  	else{ 
	  		return false;
	  	}
	  }
	  else{
	  	return StringUtils::equals($token, $request->input('_token')) ;
	   }
	}

}
