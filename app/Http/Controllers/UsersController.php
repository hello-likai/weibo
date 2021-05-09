<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UsersController extends Controller
{
    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        /**
         * compact里面的user，就是上面的参数$user，这个函数的作用是将它的参数，生成一个关联数组；
         * view() 方法将模型数据与视图绑定，这样在 用户页面中，就可以使用 {{ $user->name }} 这种方式来取值了
         */
        return view('users.show', compact('user'));
    }

    // 注册数据验证 $request实例包含了用户注册提交的信息
    public function store(Request $request)
    {
        /**
         * validator 由 App\Http\Controllers\Controller 类中的 ValidatesRequests 进行定义
         * 'required|unique:users|max:50'  这是多种验证规则
         * 'email' => 'required|email|unique:users|max:255', 验证规则里面的Email 相当于正则表达式 验证邮箱是否合法
         */
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        # 鉴于 HTTP的无状态，Laravel 提供了一种用于临时保存用户数据的方法 - 会话（Session）
        # 存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法，键用来在 页面的循环中，根据它来取值
        # @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }
}
