<?php namespace App\Http\Controllers;

use DB;
use Auth;
use App\Shops;
use Validator;
use App\Franchise;
use App\Custom\SSP;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class ShopController extends Controller
{
    //Call middleware to check for authentication
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * View manage Shop page.
     *
     * @param  None
     * @return web-response
     */
    public function manageshop(){
        
        if( Auth::user()->login_type <= 4 )
            return view("project.manage.manage-shop")->with("user_type", Auth::user()->login_type);
        else
            return view("errors.404");
    }

    /**
     * Add a Shop.
     *
     * @param Request object
     * @return web-response
     */
    public function addShop(Request $request)
    {
        if (Auth::user()->login_type == 3 || Auth::user()->login_type ==4) {
            $validator = $this->validator($request->all(), false, true);

            if ($validator->fails()) {
                $this->throwValidationException(
                    $request, $validator
                );
            }

            switch ($this->create($request->all(), true)) {
                case 0:
                    return redirect()->back()
                        ->with('success', 'ショップが更新しました。');
                    break;

                case 1:
                    return redirect()->back()
                        ->withErrors([
                            'noid' => '入力したフランチャイズは私たちのデータベースでじゃない。',
                        ]);
                    break;
            }


        } else {
            return redirect()->to('unauthorized');
        }
    }

    /**
     * Delete a shop.
     *
     * @param  Request object
     * @return web-response
     */
    public function deleteShop(Request $request)
    {
        if (Auth::user()->login_type == 3 || Auth::user()->login_type ==4) {

            $del_ids = Input::get("del_ids");

            if (is_array($del_ids)) {
                foreach ($del_ids as $id) {
                    $validator = $this->validator(['id' => $id], true, true);

                    if ($validator->fails()) {
                        $this->throwValidationException(
                            $request, $validator
                        );
                    }
                }
                $res = true;
                foreach ($del_ids as $id) {
                    $shop = Shops::where("id", $id)->first();
                    if (!is_null($shop) && $res == true){
                        $res = $shop->delete();
                    }
                    else {
                        return redirect()->back()->withErrors([
                            'cant delete' => "おっとっと!　リクエスト処理中に問題が発生しました.",
                        ]);
                    }

                }
            } else {
                return redirect()->back()
                    ->withErrors([
                        'userid' => 'おっとっと!　リクエスト処理中に問題が発生しました.',
                    ]);
            }

            return redirect()->back()
                ->with('success', 'ショップは削除しました。');
        } else {
            return redirect()->to("unauthorized");
        }
    }

    /**
     * Get a validator for an incoming Shop registration/deletion request.
     *
     * @param  array $datam, int $id -- to delete
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, $id)
    {

        if ($id) {
            return Validator::make($data, [
                'id' => 'required|numeric',
            ]);
        } 
        else if( Auth::user()->login_type==4 ){
            $messages = array(
                'name.required' => 'ショップの名前が必要です。',
                'name.alpha' => '入力したフランチャイズの名前は無効です。',
                'name.max' => '入力したフランチャイズの名前は長いです。',
                'location.required' => '住所は必要です。'
             );
            return Validator::make($data, [
                'name' => 'required|string|max:255',
                'location' => 'required'
            ], $messages);
        }
        else {
            $messages = array(
                'franchise_id.required' => 'ショップの名前が必要です。',
                'franchise_id.alpha' => '入力したフランチャイズの名前は無効です。',
                'franchise_id.max' => '入力したフランチャイズの名前は長いです。',
                'name.required' => 'ショップの名前が必要です。',
                'name.alpha' => '入力したフランチャイズの名前は無効です。',
                'name.max' => '入力したフランチャイズの名前は長いです。',
                'location.required' => '住所は必要です。'
            );
            return Validator::make($data, [
                'franchise_id' => 'required|string|max:255',
                'name' => 'required|string|max:255',
                'location' => 'required'
            ], $messages);
        }

    }

    /**
     * Create a new Shop in the database after a valid registration.
     *
     * @param  array $data
     * @return Boolean success/failure
     */
    private function create(array $data)
    {
        if( Auth::user()->login_type == 4 )
            $franchise = Franchise::where("id", Auth::user()->id)->first();
        else
            $franchise = Franchise::where("name", $data["franchise_id"])->first();
        if (!is_null($franchise)) {
            DB::transaction(function ($data) use ($data,$franchise) {

                $info_table = new Shops;
                $info_table->name = $data["name"];
                $info_table->location = $data['location'];
                $info_table->franchise_id = $franchise->id;
                $info_table->save();
            });
            return 0;
        } else {
            return 1;
        }
    }

    /**
     * Autpocomplete for Franchise entry
     *
     * @param  Request $request
     * @return HTML result
     */
    public function autoComplete(Request $request){
        if (Auth::user()->login_type <= 4) {
            $franchise = $request->all();
            $keyword = '%' . $franchise['keyword'] . '%';
            $sql = Franchise::where('name', 'LIKE', $keyword)
                            ->orderBy('id', 'asc')
                            ->take(10)
                            ->get();

            $result = '';

            foreach ($sql as $rs) {
                // put in bold the written text
                $franchise_name = str_replace($franchise['keyword'], '<b>' . $franchise['keyword'] . '</b>', $rs->name);
                $franchise_id = str_replace($franchise['keyword'], '<b>' . $franchise['keyword'] . '</b>', $rs->id);
                // add new option
                $result .= '<li onclick="set_item(\'' . str_replace("'", "\'", $rs['name']) . '\')">' . $franchise_name . '(' . ($franchise_id) . ')</li>';
            }
            return $result;
        }
    }

     /**
     * Get Shop records from database for Data-table.
     *
     * @param None
     * @return JSON
     */
    public function viewShopRecords(){
        $user_type = Auth::user()->login_type;
        
        if( $user_type <= 4 ){
            $table = 'shops';
            $primaryKey = 'id';

            if( $user_type == 4 ){
                $columns = array(
                    array( 'db' => '`sh`.`id`', 'dt' => 0, 'field' => 'id' ),
                    array( 'db' => '`sh`.`name`',  'dt' => 1, 'field' => 'name' ),
                    array( 'db' => '`sh`.`location`',  'dt' => 2, 'field' => 'location' ),
                    array( 'db' => '`sh`.`id`', 'dt' => 3, 'field' => 'id' )
                );
            }
            else{
                $columns = array(
                    array( 'db' => '`sh`.`id`', 'dt' => 0, 'field' => 'id' ),
                    array( 'db' => '`f`.`name`',  'dt' => 1, 'field' => 'f_name','as' => 'f_name' ),
                    array( 'db' => '`sh`.`name`',  'dt' => 2, 'field' => 'name' ),
                    array( 'db' => '`sh`.`location`',  'dt' => 3, 'field' => 'location' ),
                    array( 'db' => '`sh`.`id`', 'dt' => 4, 'field' => 'id' )
                );
            }
                
            $joinQuery = "FROM `{$table}` AS `sh` JOIN `franchise` AS `f` ON (`f`.`id` = `sh`.`franchise_id`)";
            
            if( $user_type == 4 )
                $where = 'f.id = "'.Auth::user()->id.'"';
            else
                $where = '';

            $ssp_object = new SSP;
            return json_encode(
                        $ssp_object->simple( $_GET, $table, $primaryKey, $columns, $joinQuery, $where)
                    );
        }
        else{
            return -1;
        }
    }
}