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
Route::get('/test','TestController@index');
Route::get('/test2','TestController@test2');
Route::get('/test3','TestController@test3');
Route::get('/test4','TestController@test4');
Route::get('/rank','RankController@index');
Route::get('/getNear7Date','RankController@getNear7Date');
Route::get('/getRankList','RankController@getRankList');
Route::get('/getPlatList','RankController@getPlatList');
Route::get('/getLiveAddr','RankController@getLiveAddr');
Route::get('/getLiverDetail','RankController@getLiverDetail');


Route::get('/my/getPlatList','my\RankController@getPlatList');
Route::get('/my/getRankList','my\RankController@getRankList');
Route::get('/my/getNearDay','my\RankController@getNearDay');
Route::get('/my/getLiverDetail','my\RankController@getLiverDetail');



Route::get('/imgAds','ImgAdsController@getImgAds');