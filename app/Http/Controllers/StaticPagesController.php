<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPagesController extends Controller
{
    public function home()
    {
        // route()是一个助手函数，用来根据路由获取url路径
        // $url = route('help');
        return view('static_pages/home');
    }

    # 这里的函数，也就相当于是路由中配置的闭包
    public function help()
    {
        # 这里的view()是助手函数，和Route::view 中的view不是一个概念
        # 这是比较重用的输出视图的方法；
        return view('static_pages/help');
    }

    public function about()
    {
        return view('static_pages/about');
    }

}
