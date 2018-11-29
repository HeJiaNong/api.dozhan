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


//首页
Route::view('/','home')->name('home');

//用户认证
Route::view('authentications','authentications.index')->name('authentications');

//resources
Route::resources([
    'works' => 'WorksController',
]);


Route::prefix('admin')->namespace('Admin')->group(function () {

});