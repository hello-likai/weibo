<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordController extends Controller
{
    // 在构造函数里，使用限流中间件
    public function __construct()
    {
        $this->middleware('throttle:2,1', [
            // 表示只针对 控制器方法：showLinkRequestForm 来限流
            'only' => ['showLinkRequestForm']
        ]);

        // 限流发送邮件次数：10分钟只能发3次邮件
        $this->middleware('throttle:3,10', [
            'only' => ['sendResetLinkEmail']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);

        // 限流 10 分钟十次
        $this->middleware('throttle:10,10', [
            'only' => ['store']
        ]);

        // 限流 一个小时内只能提交 10 次注册请求；
        $this->middleware('throttle:10,60', [
            'only' => ['store']
        ]);
    }


    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    // 发送链接到邮箱
    public function sendResetLinkEmail(Request $request)
    {
        // 1. 验证邮箱
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // 2. 获取对应用户
        $user = User::where("email", $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 生成 Token，会在视图 emails.reset_link 里拼接链接
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        // 5. 入库，使用 updateOrInsert 来保持 Email 唯一
        DB::table('password_resets')->updateOrInsert(['email' => $email], [
            'email' => $email,
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);

        // 6. 将 Token 链接发送给用户
        Mail::send('emails.reset_link', compact('token'), function ($message) use ($email) {
            $message->to($email)->subject("忘记密码");
        });

        session()->flash('success', '重置邮件发送成功，请查收');
        return redirect()->back();
    }

    // 返回重置密码的视图
    public function showResetForm(Request $request)
    {
        $token = $request->route()->parameter('token');
        return view('auth.passwords.reset', compact('token'));
    }

    // 重置密码视图，点击提交，通过路由找到这里，进行重置密码
    public function reset(Request $request)
    {
        // 1. 验证数据是否合规
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        $email = $request->email;
        $token = $request->token;
        // 找回密码链接的有效时间
        $expires = 60 * 10;

        // 2. 获取对应用户
        $user = User::where("email", $email)->first();

        // 3. 如果不存在
        if (is_null($user)) {
            session()->flash('danger', '邮箱未注册');
            return redirect()->back()->withInput();
        }

        // 4. 读取重置的记录
        $record = (array) DB::table('password_resets')->where('email', $email)->first();

        // 5. 记录存在
        if ($record) {
            // 5.1. 检查是否过期
            if (Carbon::parse($record['created_at'])->addSeconds($expires)->isPast()) {
                session()->flash('danger', '链接已过期，请重新尝试');
                return redirect()->back();
            }

            // 5.2. 检查是否正确
            if ( ! Hash::check($token, $record['token'])) {
                session()->flash('danger', '令牌错误');
                return redirect()->back();
            }

            // 5.3. 一切正常，更新用户密码
            $user->update(['password' => bcrypt($request->password)]);

            // 5.4. 提示用户更新成功
            session()->flash('success', '密码重置成功，请使用新密码登录');
            return redirect()->route('login');
        }

        // 6. 记录不存在
        session()->flash('danger', '未找到重置记录');
        return redirect()->back();
    }
}
