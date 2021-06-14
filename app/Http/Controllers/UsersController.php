<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// 第九章内容上要使用 use Mail，但是一直报错，像这样引用就不会报错
use Illuminate\Support\Facades\Mail;


class UsersController extends Controller
{
    // 对象创建之前就会调用构造器， 通过构造函数调用中间件方法，类似Java中的过滤器
    public function __construct()
    {
        // auth是中间件的名字
        $this->middleware('auth', [
            // 要进行过滤的动作：except 排除不需要授权认证的方法
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail']
        ]);

        // 只让未登录用户访问注册页面：
        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    // 创建用户的页面
    public function create()
    {
        return view('users.create');
    }


    /**
     * show() 方法传参时声明了类型 —— Eloquent 模型 User，对应的变量名 $user 会匹配路由片段中的 {user}，
     *      Route::get('/users/{user}', 'UsersController@show')->name('users.show');
     * 这样，Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例。
     * 【隐形路由模型绑定】
     */
    // 显示用户个人信息的页面，添加展示个人微博信息
    public function show(User $user)
    {
        // 第十章，现在添加展示个人微博的功能，同时在return中，将微博动态数据也打包进去
        $statuses = $user->statuses()
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        /**
         * compact里面的user，就是Laravel 会自动注入与请求 URI 中传入的 ID 对应的用户模型实例，
         *  它的作用：将它的参数，生成一个关联数组；
         * view() 方法将模型数据与视图绑定，这样在 用户页面中，就可以使用 {{ $user->name }} 这种方式来取值了
         */
        return view('users.show', compact('user', 'statuses'));
    }


    // 创建用户， $request实例包含了用户注册提交的信息
    public function store(Request $request)
    {
        /**
         * validator 由 App\Http\Controllers\Controller 类中的 ValidatesRequests 进行定义
         *      这个方法的返回值就是它的参数组成的数组，在会话控制器中，它就返回的是一个数组
         * 'required|unique:users|max:50'  这是多种验证规则
         * 'email' => 'required|email|unique:users|max:255', 验证规则里面的Email 相当于正则表达式 验证邮箱是否合法
         */
        $this->validate($request, [
            'name' => 'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        // 直接和数据库交互，注册数据入库，这些应该是内置方法，后面还有个User::all()
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        // 发送激活邮件
        $this->sendEmailConfirmationTo($user);
        // 在网页顶部位置显示注册成功的提示信息
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

        // 注册后自动登陆
        // Auth::login($user);
        // 第九章内容，注册之后发送激活邮件，而不是自动登陆了，因此注释这块代码

        # 鉴于 HTTP的无状态，Laravel 提供了一种用于临时保存用户数据的方法 - 会话（Session）
        # 存入一条缓存的数据，【让它只在下一次的请求内有效时，则可以使用 flash 方法】，键用来在 页面的循环中，根据它来取值
        # @foreach (['danger', 'warning', 'success', 'info'] as $msg)
        // session()->flash('success', '欢迎，您将在这里开启一段新的旅程~');
        // return redirect()->route('users.show', [$user]);
    }

    /**
     * edit方法的隐藏逻辑：
     *  1，利用了 Laravel 的『隐性路由模型绑定』功能，直接读取对应 ID 的用户实例 $user，未找到则报错；
     *  2，将查找到的用户实例 $user 与编辑视图进行绑定
     *      将用户数据与视图进行绑定之后，便可以在视图上通过 $user 来访问用户对象
     */
    // 编辑用户个人资料的页面
    public function edit(User $user)
    {
        // 最开始忘记了这里，结果，使用id是1登陆的时候， weibo.test/users/2/edit任然可以访问
        $this->authorize('update', $user);
        // authorize()方法，第一个参数是：授权策略的名称，在app\Policies\UserPolicy.php中，update里面定义了授权的逻辑
        //                  第二个参数是：授权验证的数据，对应update方法的第二个参数，它的第一个参数，框架会自动加载当前登录用户


        return view('users.edit', compact('user'));
    }

    // update：更新用户    第一个参数为 Laravel框架根据id自动获取的用户实例，第二个用户表单的输入数据
    public function update(User $user, Request $request)
    {
        // 使用>authorize来授权update方法，当前用户只能更新它自己
        $this->authorize('update', $user);

        // 对用户输入数据进行校验
        $this->validate($request, [
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        // 用一个判断，避免更新空的密码到数据库里
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        // 更新成功之后发送消息提示
        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user);
    }

    // 显示所有用户列表的页面，     这个是不需要授权的，因此在上面的构造函数中，添加了排除
    public function index()
    {
        // 这里使用Eloquent 用户模型将所有用户的数据一下子完全取出来了，后面需要优化，否则影响性能
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }

    // 删除用户
    public function destroy(User $user)
    {
        // 使用 authorize 方法来授权，通过授权才能继续进行
        $this->authorize('destroy', $user);
        // Eloquent 模型提供的 delete 方法对用户资源进行删除
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }

    // 发送激活邮件
    protected function sendEmailConfirmationTo($user)
    {
        // $view = 'emails.confirm';
        // $data = compact('user');
        // $from = 'summer@example.com';
        // $name = 'Summer';
        // $to = $user->email;
        // $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        // Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
        //     $message->from($from, $name)->to($to)->subject($subject);
        // });
        // 在环境配置文件完善了邮件的发送配置，因此不再需要使用 from 方法，把上面的注释了，使用下面的
        $view = 'emails.confirm';
        $data = compact('user');
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    // 确认激活邮件，处理逻辑是，根据给定的参数去数据库查找，然后更新对应的字段数据，再保存
    public function confirmEmail($token)
    {
        // where方法两个参数：第一个是实体类的字段，第二个是它的值，根据这两个参数去数据库获取数据
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true; //已激活
        $user->activation_token = null; //激活之后将令牌置空，防止重复激活
        $user->save(); // 保存用户状态

        // 保存之后，登陆
        Auth::login($user);
        // 返回提示信息
        session()->flash('success', '恭喜你，激活成功！');
        // 跳转视图
        return redirect()->route('users.show', [$user]);
    }

    // 关注人和粉丝信息
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    // 获取关注人/粉丝
    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
