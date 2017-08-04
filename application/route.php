<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;
// 给publish控制器设置快捷路由
Route::rule('user/register','index/index/register');
Route::rule('user/get_location','index/index/get_location');
Route::rule('user/signup','index/index/signup');
Route::rule('user/message','index/publish/send_message');
Route::rule('user/login','index/publish/login');
Route::rule('user/logout','index/publish/logout');
Route::rule('user/forget','index/publish/forget');

// 给Account控制器设置快捷路由
Route::rule('account/','index/user/index');
Route::rule('account/information','index/account/information');
Route::rule('account/personal_info','index/user/personal_info');
Route::rule('account/alter','index/user/alter');
Route::rule('account/check','index/user/check');
Route::rule('account/auth','index/user/auth');
Route::rule('user/phone','index/user/setTel');
Route::rule('user/send_email','index/publish/send_email');
Route::rule('user/email','index/user/setEmail');
Route::rule('user/alipay','index/user/alipay');
Route::rule('user/bank','index/user/bank');
Route::rule('user/city','index/user/city');
Route::rule('user/address','index/user/address');
Route::rule('user/zip_code','index/user/zip_code');
Route::rule('account/message','index/user/message');
Route::rule('account/income','index/income/index');


// 给Account控制器设置快捷路由
Route::rule('account/product','index/product/index');
Route::rule('account/has','index/product/has');




