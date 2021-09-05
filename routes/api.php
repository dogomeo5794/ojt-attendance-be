<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('v1')->group(function() {
    Route::post('/generate-code', 'UAMController@generateCode');
    Route::get('/generated-code', 'UAMController@getGeneratedCode');
    Route::post('/validate-init-reg', 'UAMController@validateInitReg');
    Route::post('/complete-registration', 'AccountController@completeReg');
    Route::post('/user-login', 'AccountController@userLogin');
});


