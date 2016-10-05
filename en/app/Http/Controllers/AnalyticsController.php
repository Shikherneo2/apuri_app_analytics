<?php namespace App\Http\Controllers;

use DB;
use Auth;
use Validator;
use App\Apps;
use App\Shops;
use Carbon\Carbon;
use App\Franchise;
use App\Analytics;
use App\Shopkeeper;
use App\Http\Requests;
use App\Shop_employees;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnalyticsController extends Controller {
	
	//Call middleware to check for authentication
	public function __construct(){
		$this->middleware('auth');
	}

	/**
	 * Show the analytics page.
	 *
	 * @param None
	 * @return web-response
	 */
	public function show(){
		return view("project.analytics")->with("type", Auth::user()->login_type );
	}

	/**
	 * Show the Graphs page
	 *
	 * @param None
	 * @return web-response
	 */
    public function showGraph(){
    	$temp = Carbon::now();
    	$dates["end"] = str_replace("-","/",$temp->copy()->toDateString());
    	$temp->subMonth();
    	$dates["start"] = str_replace("-","/",$temp->toDateString());
    	
        return view("project.graph_analytics")
        	 ->with("type", Auth::user()->login_type )
        	 ->with("dates", $dates);
    }

	/**
	 * Get the analytics data
	 *
	 * @param Request $request
	 * @return JSON
	 */
	public function get_analytics( Request $request ){
		$parameters = $request->all();

		$validator = $this->validator($parameters, true);
		if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);

			$response["recordsFiltered"] = 0;	
			$response["recordsTotal"] = Analytics::count();
			$response["draw"] = $draw;
			$response["data"] = [];
		}
		else{

			$filters["start"] = $parameters["start"];
			$filters["length"] = $parameters["length"];
			$draw = $parameters["draw"];
			$filters["device_type"] = $parameters["device_type"];
			$filters["group_by"] = $parameters["group_by"];
			
			if( isset($parameters["show_only"]) && $parameters["show_only"]!==""){
				$filters["show_only"] = $parameters["show_only"];
				$filters["show_only_type"] = $parameters["show_only_type"];
			}
			else{
				$filters["show_only_type"] = "all";
			}	
			$user_type = Auth::user()->login_type;

			$analytics_table = $this->customFilters($filters, $user_type);
			$totalCount  = DB::select( DB::raw("SELECT FOUND_ROWS() AS Totalcount;") );
			if( $analytics_table == "401") 
				$analytics_table = [];

			$response = array();
					
			if( count( $analytics_table ) ){
				$response["recordsFiltered"] = $totalCount[0]->Totalcount;	
				$response["recordsTotal"] = Analytics::count();
				$response["draw"] = $draw;
				
				$length = 0;
				foreach( $analytics_table as $rec ){
					if( !is_numeric($rec->last) )
						$records[$length] = [$rec->app_name,$rec->emp_name,$rec->shop_name,$rec->last];	
					else
						$records[$length] = [ $rec->name, $rec->last,"dummy","dummy"];	
			
					$length ++;
				}
				$response["data"] = $records;
			}
			else{
				$response["recordsFiltered"] = 0;	
				$response["recordsTotal"] = Analytics::count();
				$response["draw"] = $draw;
				$response["data"] = [];
			}
		}
		return json_encode( $response );
	}


	/**
	 * Filters to filter the retured analytics data
	 *
	 * @param array $filters, array $user_type
	 * @return $query
	 */
	private function customFilters( $filters ,$user_type ){
		
		//check if the combination of filters does not make sense or is unauthorized..
		if( $filters["show_only_type"] != "all" || $filters["group_by"] != "all" || $filters["show_only_type"] != "app" || $filters["group_by"] != "app" ){
			if( $filters["show_only_type"] == "shop" && $filters["group_by"] =="franchise"){
				return "401";
			}
			if( $filters["show_only_type"] == "employee" && ($filters["group_by"] =="franchise" || $filters["group_by"] =="shop") ){
				return "401";
			}
			if( $user_type==4 && ($filters["group_by"] =="franchise" || $filters["show_only_type"] =="franchise") ){
				return "401";
			}
			if( $user_type==5 ){
				if( $filters["group_by"] =="franchise" || $filters["group_by"] =="shop") 
					return "401";
				else if( $filters["show_only_type"]=="franchise" || $filters["show_only_type"]=="shop")
					return "401";
			}
		}

		$franchise_join = false;
		$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
						  ->join('shops','shop_employees.shop_id', '=', 'shops.id')
				    	  ->join('apps','analytics.app_id', '=', 'apps.id');
		
		if( $filters["group_by"] == "franchise" ){
			$query = $query->join('franchise','shops.franchise_id', '=', 'franchise.id');
			$franchise_join = true;
		}
		
		switch ( $filters["show_only_type"] ) {
			case 'shop':
				$query = $query->where("shops.name",'=', $filters["show_only"]);
				break;
			case 'app':
				$query = $query->where("apps.name",'=', $filters["show_only"]);
				break;
			
			case 'franchise':
				if( !$franchise_join ){
					$query = $query->join('franchise','shops.franchise_id', '=', 'franchise.id')
								   ->where("franchise.name",'=', $filters["show_only"]);
				}
				else{
					$query = $query->where("franchise.name",'=', $filters["show_only"]);
				}
				break;
			
			case 'employee':
				$query->where("shop_employees.name",'=', $filters["show_only"])->whereNull("deleted_at");
				break;

			default:
				//leave the query as it is
				break;
		}

		switch ($user_type) {
			case '4':
				$query = $query->join('franchise','shops.franchise_id', '=', 'franchise.id')
							   ->where("franchise.id", Auth::user()->id);
				break;
			case '5':
				$query = $query->join('shopkeeper','shops.id', '=', 'shopkeeper.shop_id')
							   ->where("shopkeeper.id", Auth::user()->id);
				break;
			case '6':
				$query = $query->where("shop_employees.id", Auth::user()->id);
				break;
			default:
				//do nothing
				break;
		}

		switch($filters["device_type"]){
			case "ios":
				$query->where("apps.type","1");
				break;
			
			case "android":	
				$query = $query->where("apps.type","2");
				break;
			
			case "all":
			default:
				//do nothing
				break;
		}

		switch ( $filters["group_by"] ) {
			case "shop":
				return $query->orderBy('last', 'desc')->groupBy("shops.id")->skip($filters["start"])->take($filters["length"])->get([DB::raw('SQL_CALC_FOUND_ROWS shops.name as name,count(*) as last')]); 
				break;
			case 'employee':
				return $query->whereNull("shop_employees.deleted_at")->orderBy('last', 'desc')->groupBy("shop_employees.id")->skip($filters["start"])->take($filters["length"])->get([DB::raw('SQL_CALC_FOUND_ROWS shop_employees.name as name,count(*) as last')]); 
				break;
			case 'app':
				return $query->orderBy('last', 'desc')->groupBy("apps.id")->skip($filters["start"])->take($filters["length"])->get([DB::raw('SQL_CALC_FOUND_ROWS apps.name as name,count(*) as last')]); 
				break;
			case 'franchise':
				return $query->orderBy('last', 'desc')->groupBy("franchise.id")->skip($filters["start"])->take($filters["length"])->get([DB::raw('SQL_CALC_FOUND_ROWS franchise.name as name,count(*) as last')]); 
				break;	
			case 'all':
			default:
				return $query->whereNull("deleted_at")->skip($filters["start"])->take($filters["length"])->orderBy("analytics.updated_at","desc")->get([DB::raw('SQL_CALC_FOUND_ROWS apps.name as app_name,shop_employees.name as emp_name, shops.name as shop_name,analytics.updated_at as last')]);
		}
	}

	/**
	 * Get the Graph data
	 * @param Request $request
	 * @return JSON
	 * 
	 */
	public function getDataForGraphs( Request $request ){
		$params= $request->all();
		
		$validator = $this->validator_graphs($params, true);
		if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);
			return "error";
		}
		else{
			$type = Auth::user()->login_type;
			$userid = Auth::user()->id;
			
			if($type <= 3){
				if( $params["graph_filter_type"]!="none" && isset($params["graph_filter_value"]) && $params["graph_filter_value"] != "" ){
					
					if( $params["graph_filter_type"] == "franchise" ){
						$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
								  ->join('shops','shop_employees.shop_id', '=', 'shops.id')
								  ->join('franchise','franchise.id', '=', 'shops.franchise_id')
								  ->where("franchise.name", $params["graph_filter_value"]);
					}
					else if( $params["graph_filter_type"] == "shop" ){
						$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
										  ->join('shops','shop_employees.shop_id', '=', 'shops.id')
										  ->where("shops.name", $params["graph_filter_value"]);
					}
					else if( $params["graph_filter_type"] == "employee" ){
						$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
										  ->where("shop_employees.name", $params["graph_filter_value"])
										  ->whereNull("shop_employees.deleted_at");	
					}
					else if( $params["graph_filter_type"] == "app" ){
						$query = Analytics::join('apps', 'analytics.app_id', '=', 'apps.id')
										  ->where("apps.name", $params["graph_filter_value"]);	
					}					
				}	
			}
			else if($type == 4){
				$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
								  ->join('shops','shop_employees.shop_id', '=', 'shops.id')
								  ->join('franchise','franchise.id', '=', 'shops.franchise_id');
								  

				if( $params["graph_filter_type"]!="none" && isset($params["graph_filter_value"]) && $params["graph_filter_value"] != "" ){
					if( $params["graph_filter_type"] == "shop" ){
						$query = $query->where("franchise.id", $userid)
									   ->where("shops.name", $params["graph_filter_value"]);
					}
					else if( $params["graph_filter_type"] == "employee" ){
						$query =$query->where("franchise.id", $userid) 
									  ->where("shop_employees.name", $params["graph_filter_value"])
									  ->whereNull("shop_employees.deleted_at");		
					}
					else if( $params["graph_filter_type"] == "app" ){
						$query =$query->join('apps','apps.id', '=', 'analytics.app_id')
									  ->where("franchise.id", $userid) 
									  ->where("apps.name", $params["graph_filter_value"]);	
					}
					else{
						return 0;
					}	
				}
				else if($params["graph_filter_value"] == ""){
					$query =$query->where("franchise.id", $userid); 
				}				  
			}
			else if($type == 5){
				$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
								  ->join('shops','shop_employees.shop_id', '=', 'shops.id')
								  ->join('shopkeeper','shopkeeper.shop_id', '=', 'shops.id');
								  
				
				if( $params["graph_filter_type"]!="none" && $params["graph_filter_value"] != ""){
					if( $params["graph_filter_type"] == "employee" ){
						$query = $query->where("shopkeeper.id", $userid)
									   ->where("shop_employees.name", $params["graph_filter_value"])
									   ->whereNull("shop_employees.deleted_at");		
					}
					else if ($params["graph_filter_type"] == "app") {
						$query = $query->join('apps','apps.id', '=', 'analytics.app_id')
									   ->where("shopkeeper.id", $userid)
									   ->where("apps.name", $params["graph_filter_value"]);
					}
					else{
						return 0;
					}

				}
				else{
					$query = $query->where("shopkeeper.id", $userid);
				}				  				  
			}
			else if($type == 6){
				if( $params["graph_filter_type"]=="app" && $params["graph_filter_value"] != ""){
					$query = Analytics::join('apps', 'analytics.app_id', '=', 'apps.id')
									  ->join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
							          ->where("shop_employees.id", $userid)
							          ->where("apps.name", $params["graph_filter_value"]);
				}
				else{
					$query = Analytics::join('shop_employees', 'analytics.emp_id', '=', 'shop_employees.id')
							          ->where("shop_employees.id", $userid);
				}
			}

			$timestamp_testing = $this->checkTimestamps($params);
			
			if( $timestamp_testing == 1 ){
				if(isset($query)){
					$query = $query->whereBetween("analytics.created_at",array(new Carbon($params["start"]), new Carbon($params["end"]) ) );
				}
				else{
					$query = Analytics::whereBetween("analytics.created_at",array(new Carbon($params["start"]), new Carbon($params["end"]) ) );
				}	
				switch($params["group_by"][0]){
					case "d":   $rec = $query->groupBy( DB::raw("day(analytics.created_at)") )
								 		     ->get(["analytics.created_at", DB::raw("count(*) as total")]);
							    break;
				
					case "w":	$rec = $query->groupBy( DB::raw("week(analytics.created_at)") )
									 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
								break;

					case "m": 	$rec = $query->groupBy( DB::raw("month(analytics.created_at)") )
									 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
								break;

					case "y":	$rec = $query->groupBy( DB::raw("year(analytics.created_at)") )
									 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
								break;
				}				
			}
			
			else if( $timestamp_testing == 0 ){
				if(isset($query)){
					switch($params["group_by"][0]){
						case "d":   $rec = $query->where( DB::raw("month(analytics.created_at)"), DB::raw("month(now())") )
									 		     ->groupBy( DB::raw("day(analytics.created_at)") )
									 		     ->get(["analytics.created_at", DB::raw("count(*) as total")]);
								    break;
					
						case "w":	$rec = $query->where( DB::raw("year(now())"), DB::raw("year(analytics.created_at)") )
										 		 ->where( DB::raw("month(now())-6"), "<=", DB::raw( "month(analytics.created_at)" ) )
										 		 ->groupBy( DB::raw("week(analytics.created_at)") )
										 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;

						case "m": 	$rec = $query->where( DB::raw("year(now())"), DB::raw("year(analytics.created_at)") )
										 		 ->groupBy( DB::raw("month(analytics.created_at)") )
										 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;

						case "y":	$rec = $query->where( DB::raw("year(now())-12"), "<=", DB::raw("year(analytics.created_at)") )
										 		 ->groupBy( DB::raw("year(analytics.created_at)") )
										 		 ->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;
					}
				}
				else{
					switch($params["group_by"][0]){
						case "d":   $rec = Analytics::where( DB::raw("month(analytics.created_at)"), DB::raw("month(now())") )
									 		     	->groupBy( DB::raw("day(analytics.created_at)") )
									 		     	->get(["analytics.created_at", DB::raw("count(*) as total")]);
								    break;
					
						case "w":	$rec = Analytics::where( DB::raw("year(now())"), DB::raw("year(analytics.created_at)") )
											 		->where( DB::raw("month(now())-6"), "<=", DB::raw( "month(analytics.created_at)" ) )
											 		->groupBy( DB::raw("week(analytics.created_at)") )
											 		->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;

						case "m": 	$rec = Analytics::where( DB::raw("year(now())"), DB::raw("year(analytics.created_at)") )
										 		    ->groupBy( DB::raw("month(analytics.created_at)") )
										 		    ->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;

						case "y":	$rec = Analytics::where( DB::raw("year(now())-12"), "<=", DB::raw("year(analytics.created_at)") )
										 		    ->groupBy( DB::raw("year(analytics.created_at)") )
										 		    ->get(["analytics.created_at", DB::raw("count(*) as total")]);
									break;
					}
				}
			}
			else if( $timestamp_testing == -1 ){
				return json_encode(["error"=>"invalid"]);
			}
			else if( $timestamp_testing == 2 ){
				return json_encode(["error"=>"exceed"]);
			}

			$data = array();
			$data["cols"] = [ ["type"=>"string"], ["type"=>"number"] ]; 
			$data["rows"] = array();

			foreach($rec as $pers){
				$data["rows"][] = [ "c"=>[ ["v"=>$this->formatDate($pers->created_at, $params['group_by'][0] )], ["v"=>$pers->total] ]  ];
			}
			
			return json_encode($data);
		}
	}

	/**
	 * Format the returned　date 
	 * @param Carbon $date, char $type
	 * @return string
	 * 
	 */
	private function formatDate( Carbon $date , $type){
		switch ($type) {
			case 'd':
				return "Month:".$date->month." Day:".$date->day;
				break;
			
			case 'w':
				return 'Week:'.$date->weekOfMonth." Month:".$date->month;
				break;

			case 'm':
				return "Month:".$date->month;
				break;

			case 'y':
				return (string)$date->year;
				break;
		}
	}

	/**
	 * Check Timestamp Validity
	 * @param array $params
	 * @return integer
	 * 
	 */
	private function checkTimestamps( $params ){
		if( isset($params["range"]) ){
			if( $params["range"] == 0 )
				return 0;
			if( !isset($params["start"]) || !isset($params["end"])  ){
				return -1;
			}
			else{
				try{
					$start = new Carbon($params["start"]);
					$end = new Carbon($params["end"]);
					if($end->isFuture())
						$end = Carbon::now();
					if( $end->lt($start) )
						return -1;

					switch($params["group_by"][0]){
						case "d": if( $end->diffInDays($start) > 40 )
								  	return 2;
								  else	
								  	return 1;
								  
								  break;
					
						case "w": if( $end->diffInWeeks($start) > 40 )
								  	return 2;
								  else	
								  	return 1;
								  
								  break;

						case "m": if( $end->diffInMonths($start) > 40 )
								  	return 2;
								  else	
								  	return 1;
								  
								  break;

						case "y": if( $end->diffInYears($start) > 40 )
								  	return 2;
								  else	
								  	return 1;	
								
								  break;
					}
				}
				catch(Exception $e){
					return -1;
				}
			}
		}
		else{
			return -1;
		}
	}

	/**
	 * Auto Complete for Data Table
	 * @param Request $request
	 * @return JSON
	 * 
	 */
	public function autoComplete(Request $request){
        
        $params = $request->all();
      	$validator = $this->validator_auto_complete($params, true);
        if ($validator->fails()){
			$this->throwValidationException(
				$request, $validator
			);
			return -1;
		}
		
      	$keyword = '%' . $params['keyword'] . '%';
      	if( isset($params["graph"]) )
      		$graph = true;
      	else
      		$graph = false;

      	if( $params["show_only_type"] != "none" ){
	        if (Auth::user()->login_type <= 3) {
	        	switch($params["show_only_type"]){
		        	case 'franchise':
			        		$query = Franchise::where("name","LIKE",$keyword);
			        		break;
		        	case 'shop':
		        			$query = Shops::where("name","LIKE",$keyword);
		        			break;
		        	case 'app':
		        			$query = Apps::where("name","LIKE",$keyword);
		        			break;
		        	case 'employee':
		        			$query = Shop_employees::where("name","LIKE",$keyword);
		        			break;
	        	}	
	        }
	        else if (Auth::user()->login_type == 4) {
	        	switch($params["show_only_type"]){
		        		case 'franchise':
			        		//cancel query
			        		break;
		        	case 'shop':
		        			$query = Shops::where("franchise_id", Auth::user()->id)
		        						  ->where("name","LIKE",$keyword);
		        			break;
		        	case 'app':
		        			$query = Apps::where("apps.name","LIKE",$keyword);
		        			break;
		        	case 'employee':
		        			$query = Shop_employees::join('shops','shop_employees.shop_id', '=', 'shops.id')
									  	           ->where("shops.franchise_id", Auth::user()->id)
												   ->where("shop_employees.name","LIKE",$keyword);
		        			break;
	        	}
	        }
	        else if (Auth::user()->login_type == 5){
	        	switch($params["show_only_type"]){
		        	case 'franchise':
			        		//cancel query
			        		break;
		        	case 'shop':
		        			//cancel query
		        			break;
		        	case 'app':
		        			$query = Apps::where("apps.name","LIKE",$keyword);
		        			break;
		        	case 'employee':
		        			$query = Shop_employees::join('shopkeeper','shop_employees.shop_id', '=', 'shopkeeper.shop_id')
		        								   ->where("shopkeeper.id", Auth::user()->id)
												   ->where("shop_employees.name","LIKE",$keyword);
		        			break;
	        	}
	        }
	        else if (Auth::user()->login_type == 6){
	        	switch($params["show_only_type"]){
		        	case 'franchise':
			        		//cancel query
			        		break;
		        	case 'shop':
		        			//cancel query
		        			break;
		        	case 'app':
		        			$query = Apps::where("apps.name","LIKE",$keyword);
		        			break;
		        
	        	}
	        }

	        $sql = $query->orderBy('id', 'asc')
		               	 ->take(10)
		               	 ->get();
	        $result = '';

	        foreach ($sql as $rs) {
	            // put in bold the written text
	            $params_name = str_replace($params['keyword'], '<b>' . $params['keyword'] . '</b>', $rs->name);
	            $params_id = str_replace($params['keyword'], '<b>' . $params['keyword'] . '</b>', $rs->id);
	            // add new option
	            if($graph === false)
	           		$result .= '<li onclick="set_item(\'' . str_replace("'", "\'", $rs['name']) . '\')">' . $params_name . '(' . ($params_id) . ')</li>';
	        	else
	        		$result .= '<li onclick="set_item1(\'' . str_replace("'", "\'", $rs['name']) . '\')">' . $params_name . '(' . ($params_id) . ')</li>';
	        }
	        return $result;
        }
    }

	/**
	 * Validator
	 * @param array $data
	 * @return Validator
	 * 
	 */
	private function validator(array $data){

		return Validator::make($data, [
			'draw' => 'required|numeric',
			'length' => 'required|numeric',
			'start' => 'required|numeric',
			'group_by' => 'required|string|max:10',
			'show_only_type' => 'string|max:10',
			'show_only' => 'string|max:100',
			'device_type' => 'string|max:8'
		]);
	}

	/**
	 * Validator for graph
	 * @param array $data
	 * @return Validator
	 * 
	 */
	private function validator_graphs(array $data){

		return Validator::make($data, [
			'graph_filter_type' => 'required|string|max:10',
			'graph_filter_value' => 'string|max:100',
			'group_by' => 'required|string|max:7'
		]);
	}

	/**
	 * Validator for auto-complete
	 * @param array $data
	 * @return Validator
	 * 
	 */
	private function validator_auto_complete(array $data){

		return Validator::make($data, [
			'show_only_type' => 'required|string|max:10',
			'keyword' => 'required|string|max:100',
		]);
	}


}