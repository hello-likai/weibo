<?php

#use Illuminate\Routing\Route; 经过查证，应该使用下面这个
use Illuminate\Support\Facades\Route;

Route::get('/', 'StaticPagesController@home')->name('home');
Route::any('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');

# 路由器的另外一种配置方式
Route::get('test', function(){
    return "this is a test";
});
