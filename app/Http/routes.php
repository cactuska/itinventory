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
});

Route::auth();
Route::get('/changePassword','HomeController@showChangePasswordForm');
Route::post('/changePassword','HomeController@changePassword')->name('changePassword');
Route::get('/home', 'HomeController@index');
