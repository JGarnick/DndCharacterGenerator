<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
    // return view('welcome');
// });

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function($router){
	Route::prefix('character')->group(function($router){
		$router->get('/index', 'CharacterController@index')->name('character.index');
		$router->get('/create', 'CharacterController@create')->name('character.create');
		$router->get('/{id}/show', 'CharacterController@show')->name('character.show');
		$router->post('/{id}/update', 'CharacterController@update')->name('character.update');
	});

	Route::prefix('user')->group(function($router){
		$router->get('/index', 'UserController@index')->name('user.index');
		$router->get('/create', 'UserController@create')->name('user.create');
		$router->get('/show', 'UserController@show')->name('user.show');
	});
});


