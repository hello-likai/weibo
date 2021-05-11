<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{

    // 登陆和注册页面，只允许未登陆用户访问
    public function __construct()
    {
        // 通过中间件，只让未登录用户访问登录页面：
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

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

       /**
        * attempt()方法的逻辑：
        *    接收一个数组来作为第一个参数，该参数提供的值将用于寻找数据库中的用户数据；
        *    第一个字段在数据库中查找，找到了再匹配第二个字段，
        *    匹配后两个值完全一致，会创建一个『会话』给通过认证的用户。
        *    会话在创建的同时，也会种下一个名为 laravel_session 的 HTTP Cookie，以此 Cookie 来记录用户登录状态，最终返回 true

        * 增加了 $request->has('remember') 实现了登陆之后，记住我的功能
        * Laravel 默认为用户生成的迁移文件中已包含 remember_token 字段，该字段将用于保存『记住我』令牌
        */
       if (Auth::attempt($credentials, $request->has('remember'))) {
            // 第九章，添加一个判断是否激活账户的校验
            if(Auth::user()->activated){
                session()->flash('success', '欢迎回来！');
                $fallback = route('users.show', Auth::user());
                // 这里是为了保证，未登陆之前访问的页面，在登陆之后直接跳过去
                return redirect()->intended($fallback);
            }else{
                Auth::logout();
                session()->flash('warning', '你的账号未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
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
