<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/user/login', 'UserController@login');
Route::get('/user/get_parkir_rate', 'UserController@get_parkir_rate');
Route::post('/user/get_user_vehicle', 'UserController@get_user_vehicle');
Route::post('/user/get_user_info', 'UserController@get_user_info');

Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {
	Route::group(['prefix' => 'employee'], function () {
	    Route::post('/get_all', 'Admin\EmployeeController@get_all');
	    Route::post('/get_by_user_id', 'Admin\EmployeeController@get_by_user_id');
	    Route::post('/create', 'Admin\EmployeeController@create');
	    Route::post('/edit', 'Admin\EmployeeController@edit');
	    Route::post('/delete', 'Admin\EmployeeController@delete');
	});

	Route::group(['prefix' => 'vehicle'], function () {
	    Route::post('/get_all', 'Admin\VehicleController@get_all');
	    Route::post('/get_by_id', 'Admin\VehicleController@get_by_id');
	    Route::post('/create', 'Admin\VehicleController@create');
	    Route::post('/edit', 'Admin\VehicleController@edit');
	    Route::post('/delete', 'Admin\VehicleController@delete');
	});

	Route::group(['prefix' => 'parkir_rate'], function () {
	    Route::post('/edit', 'Admin\ParkirRateController@edit');
	});
    // Route::get('/get_employee', '');
});
