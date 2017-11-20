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

Route::get('/', function () {
    return view('welcome');
});
Route::post('/gitHubHook','GithubHookController@index');
Route::get('/test','RankController@getLiveAddr');
Route::get('/rank','RankController@index');
Route::get('/getNear7Date','RankController@getNear7Date');
Route::get('/getRankList','RankController@getRankList');
Route::get('/getPlatList','RankController@getPlatList');
Route::get('/getLiveAddr','RankController@getLiveAddr');