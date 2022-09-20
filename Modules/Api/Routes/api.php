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

// Route::middleware('auth:api')->get('/api', function (Request $request) {
//     return $request->user();
// });


Route::group(['namespace'=> '\Modules\Api\Http\Controllers'],function(){

    Route::group(['prefix'=>'v1'],function(){
        //to get app state
        Route::post('api-login',['as'=>'api.login','uses'=>'Auth\LoginController@apiLogin']);
       // Route::post('api-register', 'Auth\RegisterController@apiRegister')->name('api.register');

    });
});