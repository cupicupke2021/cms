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

Route::get('/', [App\Http\Controllers\Start::class, 'index']);
Route::get('/test', [App\Http\Controllers\Start::class, 'index']);

Route::get('/system/{sys?}/{subsys?}/{id?}/{mode?}/{subsyspart?}',[App\Http\Controllers\System::class, 'index'])->name('system');
Route::post('/system/{sys?}/{subsys?}/{id?}/{mode?}/{subsyspart?}',[App\Http\Controllers\System::class, 'index'])->name('system');
//Route::post('/system/{sys?}/{subsys?}/{id?}/{mode?}/{subsyspart?}','System@index')->name('system');

Route::post('/login', [App\Http\Controllers\Start::class, 'index']);
