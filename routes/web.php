<?php

#use Illuminate\Routing\Route; 经过查证，应该使用下面这个
use Illuminate\Support\Facades\Route;

# get方法的第一个参数：访问路径，/help和help相同，
# 第二个参数：处理URL的控制器动作，对应控制器里面的函数名
# 后面的name方法，是给路由起名字，页面上可以根据这个名字来找到路由
Route::get('/', 'StaticPagesController@home')->name('home');
Route::any('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');

Route::resource('users', 'UsersController');

# 新增会话控制的路由
Route::get('login', 'SessionsController@create')->name('login');
# 用来登陆的认证
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

# 用来激活账户
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email');

// 忘记密码重置路由，填写 Email 的表单的路由
Route::get('password/reset',  'PasswordController@showLinkRequestForm')->name('password.request');
// 处理表单提交，成功的话就发送邮件，附带 Token 的链接
Route::post('password/email',  'PasswordController@sendResetLinkEmail')->name('password.email');
// 显示更新密码的表单，包含 token
Route::get('password/reset/{token}',  'PasswordController@showResetForm')->name('password.reset');
// 对提交过来的 token 和 email 数据进行配对，正确的话更新密码
Route::post('password/reset',  'PasswordController@reset')->name('password.update');

// 微博发布删除功能路由
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 注人列表和粉丝列表路由
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');

// 关注和取消关注用户
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');





# 路由器的另外一种配置方式，url后面还可以添加参数，这样在后面的闭包中，可以使用这个参数
// Route::get('test', function(){
//     return "this is a test";
// });

//一个空的分组路由Route::group([], function () {
    // Route::get('index/{id}', function ($id) { return 'index'.$id;
    // });
    // Route::get('task/{id}', function ($id) { return 'task'.$id;
    // });




// 测试重定向
Route::get('redirect-test', function(){
    // 访问weibo.test/redirect-test  就会跳转到首页
    // 这是完整形式，
    //return redirect()->to('/');
    // 这是简写形式：
    return redirect('/');
});


// 获取当前路由信息
Route::get('index', function () {
    //当前路由信息，因为这是数组，必须用dump来输出，不能使用return
    dump(Route::current());
    //返回当前路由的名称，就是下面name方法指定的名称
    return Route::currentRouteName();
    //返回当前路由指向的方法， 如果没有指定控制器controller，那么是没有输出的
    return Route::currentRouteAction();
    })->name('localhost.index');


//测试单路由用
Route::get('test-single','OneController');
// 回退路由，访问不存在的页面跳转， 需要放在路由的最下面
Route::fallback(function(){
    return view('404');
});
