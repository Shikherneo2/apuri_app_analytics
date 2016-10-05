<?php

//Auth
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('login','MiscController@login');
Route::get('logout','Auth\AuthController@getLogout');
Route::get('reset','Auth\PasswordController@getEmail');
Route::get('change_password','Auth\PasswordHandlerController@showChange');
Route::post('change-password','Auth\PasswordHandlerController@editChange');
Route::post('change-password-reset','Auth\PasswordHandlerController@editChangeReset');
Route::get('reset_password','Auth\PasswordHandlerController@showReset');
Route::post('reset-password','Auth\PasswordHandlerController@editReset');
Route::get('reset/{key}','Auth\PasswordHandlerController@reset');

Route::get('404notfound','MiscController@notfound');
Route::get('unauthorized','MiscController@unauthorized');

Route::get('home','HomeController@home');
Route::post('addAdmin','AdminController@addAdmin');
Route::post('deleteAdmin','AdminController@deleteAdmin');
Route::get('view_admin_records','AdminController@viewAdminRecords');

Route::get('manage-franchise','FranchiseController@manage_franchise');
Route::post('addFranchise','FranchiseController@addFranchise');
Route::post('deleteFranchise','FranchiseController@deleteFranchise');
Route::get('view_franchise_records','FranchiseController@viewFranchiseRecords');

Route::get('manage-shopkeeper','ShopkeeperController@manage_shopkeeper');
Route::post('addShopkeeper','ShopkeeperController@addShopkeeper');
Route::post('deleteShopkeeper','ShopkeeperController@deleteShopkeeper');
Route::get('autoShopCompleteShopkeeper','ShopkeeperController@autoShopComplete');
Route::get('view_shopkeeper_records','ShopkeeperController@viewShopkeeperRecords');

Route::get('manage-shop_employee','Shop_employeeController@manage_Shop_employee');
Route::post('addShop_employee','Shop_employeeController@addShop_employee');
Route::post('deleteShop_employee','Shop_employeeController@deleteShop_employee');
Route::get('autoShopComplete','Shop_employeeController@autoShopComplete');
Route::post('upload_csv','Shop_employeeController@uploadCSV');
Route::get('addShop_employee_from_csv','Shop_employeeController@addEmployeeFromCSV');
Route::get('view_shop_employee_records','Shop_employeeController@viewShop_employeeRecords');
Route::get('csv_upload_info','Shop_employeeController@csvUploadInfo');

Route::get('manageshop','ShopController@manageshop');
Route::post('addShop','ShopController@addShop');
Route::post('deleteShop','ShopController@deleteShop');
Route::get('autoComplete','ShopController@autoComplete');
Route::get('view_shop_records','ShopController@viewShopRecords');

Route::get('viewapps','AppController@view');
Route::get('manage-app','AppController@manage');
Route::get('show-updateapp/{id}','AppController@showUpdate');
Route::post('addApp','AppController@add');
Route::post('editApp','AppController@editApp');
Route::post('deleteApp','AppController@delete');
Route::post('upload_logo','AppController@uploadLogo');
Route::post('upload_logo_update_app','AppController@uploadLogoUpdateApp');
Route::get('view_app_records','AppController@viewAppRecords');

Route::get('analytics','AnalyticsController@show');
Route::get('graph_analytics','AnalyticsController@showGraph');
Route::get('get_analytics','AnalyticsController@get_analytics');
Route::get('getDataForGraphs','AnalyticsController@getDataForGraphs');
Route::get('autoCompleteAnalytics','AnalyticsController@autoComplete');
Route::get('autoCompleteAnalytics1','AnalyticsController@autoComplete1');