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

Route::group(['namespace' => '\Modules\Api\Http\Controllers'], function()
{
    Route::get('/', 'ApiController@index');

	Route::group(['prefix' => 'v1'], function () {
		// Route::get('/getdl/{token}','UserController@getUserImage');
		Route::POST('/api-login', 'Auth\LoginController@apiLogin')->name('apilogin');
		Route::get('/verify/{otp}/{mobile}','Auth\LoginController@verifyOtp');

	 
	    Route::post('/register', 'Auth\RegisterController@register')->name('register');
	    	
		Route::group(['middleware' => 'jwt.auth'], function () {
			// Route::get('/getprofile','UserController@getProfile')->name('user.getprofile');
			Route::group(['prefix'=>'profile'], function(){
				// Route::put('update','UserController@updateProfile');
			});
		});
    });
});