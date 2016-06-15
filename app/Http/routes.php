<?php

//use Illuminate\Support\Facades\Redirect;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
//*/
//
Route::get('/', function () {
    return Redirect::to('/order/list');
});

//

/**
 * 前端路由
 */
Route::controller('order','OrderController');

/**
 * 登录注册 重置密码
 */
Route::controller('auth', 'Auth\AuthController');
