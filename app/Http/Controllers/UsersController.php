<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UsersController extends Controller
{
    // 对象创建之前就会调用构造器， 通过构造函数调用中间件方法，类似Java中的过滤器
    public function __construct()
    {
        $this->middleware('auth', [
            // except 方法来设定 指定动作 不使用 Auth 中间件进行过滤
            'except' => ['show', 'create', 'store', 'index']
        ]);

        // 只让未登录用户访问注册页面：
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

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

        // 注册后自动登陆
        Auth::login($user);

        # 鉴于 HTTP的无状态，Laravel 提供了一种用于临时保存用户数据的方法 - 会话（Session）
        # 存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法，键用来在 页面的循环中，根据它来取值
        # @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        return redirect()->route('users.show', [$user]);
    }

    # 打开用户信息页面
    public function edit(User $user)
    {
        // 最开始忘记了这里，结果，使用id是1登陆的时候， weibo.test/users/2/edit任然可以访问
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    // update 第一个参数为 id 对应的用户实例对象，第二个则为更新用户表单的输入数据
    public function update(User $user, Request $request)
    {
        // 添加授权策略之后，在这里就可以使用authorize()方法
        $this->authorize('update', $user);

        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        // 更新成功之后发送消息提示
        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user);
    }

    // 列出所有用户，这个是不需要授权的，因此在上面的构造函数中，添加了排除
    public function index()
    {
        // 这里使用Eloquent 用户模型将所有用户的数据一下子完全取出来了，后面需要优化，否则影响性能
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }
}
