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

use Illuminate\Support\Facades\Route;

//测试路由
Route::any('/','UploadController@index')->name('test');

//上传视频
Route::view('/video','video');
Route::post('/video','UploadController@videoUpload')->name('upload.video');

//上传图片
Route::view('/image','image');
Route::post('/image','UploadController@imageUpload')->name('upload.image');