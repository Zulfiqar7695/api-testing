<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', function () {
p('');


});

Route::post('user/store', 'App\Http\Controllers\Api\UserController@store');


Route::get('user/index', 'App\Http\Controllers\Api\UserController@index');

Route::delete('user/delete/{id}', 'App\Http\Controllers\Api\UserController@destroy');


Route::put('user/update/{id}', 'App\Http\Controllers\Api\UserController@update');


Route::patch('change-password/{id}', 'App\Http\Controllers\Api\UserController@changePassword');


