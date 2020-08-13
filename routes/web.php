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

});

Route::get('/sp', function(){
    //1. app('Family')->testAbc();

    //2.app('app')->bind('app', 'App/Providers/FamilyServiceProvider');
    //app('Family')->testAbc();
});

Route::get('/event', function (){
    event(new \App\Events\TestEvent());
});
