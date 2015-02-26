<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
*/

// Main routes
Route::get('/', 'TODOController@toDoList');
Route::get('/api', 'TODOController@api');

// Get Oauth token flow
Route::get('/oauth/logout', 'AuthController@logout');
Route::get('/oauth/register', 'AuthController@oauth_url');
Route::get('/oauth/callback', 'AuthController@oauth_callback');

// Handles API authorization
Route::get('/authorization', 'AuthController@getAuthorization');
Route::post('/authorization', 'AuthController@setAuthorization');

