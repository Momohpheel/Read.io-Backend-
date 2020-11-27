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
Route::group(['prefix' => 'v1'], function(){
    Route::group(['prefix' => 'auth'], function(){
        Auth::routes();
        Route::post('/password', 'Auth\ResetPasswordController@changePassword')->middleware('auth:api');
    });
    Route::post('profile', 'UserController@profile');
    Route::group(['prefix' => 'article'], function () {
        Route::post('/', 'UserController@writeArticle')->middleware('auth:api');
        Route::put('{id}', 'UserController@updateArticle')->middleware('auth:api');
        Route::delete('{id}', 'UserController@deleteArticle')->middleware('auth:api');
        Route::get('{id}', 'UserController@getArticle');
        Route::get('/', 'UserController@getAllArticles');
        Route::get('you', 'UserController@getUserArticles')->middleware('auth:api');
    });

    Route::post('love/{id}', 'UserController@react')->middleware('auth:api');
    Route::post('comment/{id}', 'UserController@comment')->middleware('auth:api');
    Route::post('follow/{id}', 'UserController@follow')->middleware('auth:api');
    Route::get('filter/{type}', 'UserController@filterArticles')->middleware('auth:api');

    Route::group(['prefix' => 'book'], function () {
        Route::get('/', 'UserController@getBooks')->middleware('auth:api');
        Route::post('/', 'UserController@uploadBook')->middleware('auth:api');
        Route::get('{category}', 'UserController@filterBooks')->middleware('auth:api');
    });
});


