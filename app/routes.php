<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/', 'TODOController@toDoList');
Route::get('/api', 'TODOController@api');
Route::get('/oauth/logout', 'AuthController@logout');
Route::get('/oauth/register', 'AuthController@oauth_url');
Route::get('/oauth/callback', 'AuthController@oauth_callback');
Route::get('/authorization', 'AuthController@getAuthorization');
Route::post('/authorization', 'AuthController@setAuthorization');

