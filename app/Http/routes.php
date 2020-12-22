<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


use Codedge\Fpdf\Fpdf\Fpdf;

Route::get('/', function () {
    return view('welcome');
});

/***************
 *  Master files
 */

Route::group(['middleware' => 'auth'], function () {
    Route::resource('/Sites', 'SiteController');
    Route::post('/Sites/changeStatus', array('as' => 'Sites.changeStatus', 'uses' => 'SiteController@changeStatus'));
    Route::resource('/Employees', 'EmployeeController');
    Route::post('/Employees/changeStatus', array('as' => 'Employees.changeStatus', 'uses' => 'EmployeeController@changeStatus'));
    Route::resource('/Equipments', 'EqutypeController');
    Route::post('/Equipments/changeStatus', array('as' => 'Equipments.changeStatus', 'uses' => 'EqutypeController@changeStatus'));
    Route::resource('/Notifications', 'NotificationController');
    Route::resource('/Users', 'UserController');
    Route::post('/Users/renew_api/{id}', array('as' => 'Users.renew_api', 'uses' => 'UserController@renew_api'));
});

/***************
 * Inventory
 */

Route::group(['middleware' => 'auth'], function () {
    Route::resource('/Inventory', 'InventoryController');
    Route::post('/Inventory/handover', array('as' => 'Inventory.handover', 'uses' => 'InventoryController@handover'));
    Route::post('/Inventory/takeback', array('as' => 'Inventory.takeback', 'uses' => 'InventoryController@takeback'));
    Route::post('/Inventory/{serial}/logs', array('as' => 'Inventory.logs', 'uses' => 'InventoryController@logs'));
    Route::get('/Inventory/takebackdoc/{owner}/{array}', array('as' => 'Inventory.takebackdoc', 'uses' => 'InventoryController@takebackdoc'));
    Route::get('/Inventory/personal/{owner}', array('as' => 'Inventory.personal_inventory', 'uses' => 'InventoryController@personal_inventory'));
    Route::post('/Inventory/personal/sign', array('as' => 'Inventory.personal_inventory_sign', 'uses' => 'InventoryController@personal_inventory_sign'));
    Route::get('/Inventory/personal/signed/{owner}', array('as' => 'Inventory.personal_inventory_signed', 'uses' => 'InventoryController@personal_inventory_signed'));
    Route::post('/Inventory/getsoftwarelist', array('as' => 'Inventory.getsoftwarelist', 'uses' => 'InventoryController@getsoftwarelist'));
    Route::post('/Inventory/getseriallist', array('as' => 'Inventory.getseriallist', 'uses' => 'InventoryController@getseriallist'));
    Route::post('/Inventory/assignsoftware', array('as' => 'Inventory.assignsoftware', 'uses' => 'InventoryController@assignsoftware'));
    Route::post('/Inventory/unassignsoftware', array('as' => 'Inventory.unassignsoftware', 'uses' => 'InventoryController@unassignsoftware'));
    Route::resource('/Softwares', 'SoftwareController');
    Route::post('/Softwares/{serial}/getdevices', array('as' => 'Softwares.getdevices', 'uses' => 'SoftwareController@getdevices'));
    Route::post('/Softwares/getuserlist', array('as' => 'Softwares.getuserlist', 'uses' => 'SoftwareController@getuserlist'));
    Route::post('/Softwares/getdeviceperuser', array('as' => 'Softwares.getdeviceperuser', 'uses' => 'SoftwareController@getdeviceperuser'));
    Route::post('/Softwares/getserialperdevice', array('as' => 'Softwares.getserialperdevice', 'uses' => 'SoftwareController@getserialperdevice'));
});

Route::group(['prefix' => 'api/v1', 'middleware' => 'auth:api'], function () {
    Route::post('/excelupload', array('as' => 'Inventory.excelupload', 'uses' => 'InventoryController@excelupload'));
    Route::post('/excelsoftwareupload', array('as' => 'Softwares.excelsoftwareupload', 'uses' => 'SoftwareController@excelsoftwareupload'));
});


Route::auth();
Route::get('/changePassword','HomeController@showChangePasswordForm');
Route::post('/changePassword','HomeController@changePassword')->name('changePassword');
Route::get('/home', 'HomeController@index');
