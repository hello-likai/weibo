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

    public function create()
    {
        return view('users.create');
    }

    public function show(User $user)
    {
        // 第十章，现在添加展示个人微博的功能，同时在return中，将微博动态数据也打包进去
        $statuses = $user->statuses()
                         ->orderBy('created_at', 'desc')
                         ->paginate(10);
        /**
         * compact里面的user，就是上面的参数$user，这个函数的作用是将它的参数，生成一个关联数组；
         * view() 方法将模型数据与视图绑定，这样在 用户页面中，就可以使用 {{ $user->name }} 这种方式来取值了
         */
        return view('users.show', compact('user', 'statuses'));
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');

        // 注册后自动登陆
        // Auth::login($user);  第九章内容，注册之后发送激活邮件，而不是自动登陆了

        # 鉴于 HTTP的无状态，Laravel 提供了一种用于临时保存用户数据的方法 - 会话（Session）
        # 存入一条缓存的数据，让它只在下一次的请求内有效时，则可以使用 flash 方法，键用来在 页面的循环中，根据它来取值
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
    public function edit(User $user)
    {
        // 最开始忘记了这里，结果，使用id是1登陆的时候， weibo.test/users/2/edit任然可以访问
        $this->authorize('update', $user);
        return view('users.edit', compact('user'));
    }

    // update 第一个参数为 id 对应的用户实例对象，第二个则为更新用户表单的输入数据
    public function update(User $user, Request $request)
    {
        // 注册添加授权策略之后，在这里就可以使用authorize()方法
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

    // 列出所有用户，这个是不需要授权的，因此在上面的构造函数中，添加了排除
    public function index()
    {
        // 这里使用Eloquent 用户模型将所有用户的数据一下子完全取出来了，后面需要优化，否则影响性能
        $users = User::paginate(6);
        return view('users.index', compact('users'));
    }

    // 删除用户
    public function destroy(User $user)
    {
        // 使用 authorize 方法来对删除操作进行授权验证即可。在删除动作的授权中，我们规定只有当前用户为管理员
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

    // 确认激活邮件
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();

        $user->activated = true; //已激活
        $user->activation_token = null; //激活之后将令牌置空，防止重复激活
        $user->save(); // 保存用户状态

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }

    // 关注人和粉丝信息
    public function followings(User $user)
    {
        $users = $user->followings()->paginate(30);
        $title = $user->name . '关注的人';
        return view('users.show_follow', compact('users', 'title'));
    }

    public function followers(User $user)
    {
        $users = $user->followers()->paginate(30);
        $title = $user->name . '的粉丝';
        return view('users.show_follow', compact('users', 'title'));
    }
}
