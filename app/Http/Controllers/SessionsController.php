<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    // 登陆视图
    public function create(){
        # 返回一个登陆的视图，这个视图在resources目录下
        return view('sessions.create');
    }

    // 创建 store 动作来对用户提交的数据进行验证
    public function store(Request $request)
    {
       $credentials = $this->validate($request, [
           'email' => 'required|email|max:255',
           'password' => 'required'
       ]);
        // 增加了 $request->has('remember') 实现了登陆之后，记住我的功能
        // Laravel 默认为用户生成的迁移文件中已包含 remember_token 字段，该字段将用于保存『记住我』令牌
       if (Auth::attempt($credentials, $request->has('remember'))) {
            session()->flash('success', '欢迎回来！');
            return redirect()->route('users.show', [Auth::user()]);
        } else {
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

    }

    // 登出逻辑
    public function destroy()
    {
        // Laravel 默认提供的 Auth::logout() 方法来实现用户的退出功能
        Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
