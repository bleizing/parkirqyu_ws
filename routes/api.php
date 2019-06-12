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

Route::group(['prefix' => 'admin', 'middlware' => 'admin'], function () {
	Route::group(['prefix' => 'employee'], function () {
	    Route::get('/get', 'Admin\EmployeeController@get');
	});
    // Route::get('/get_employee', '');
});
