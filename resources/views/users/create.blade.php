@extends('layouts.default')
@section('title', '登录')

@section('content')
<div class="offset-md-2 col-md-8">
  <div class="card ">
    <div class="card-header">
      <h5>登录</h5>
    </div>
    <div class="card-body">

        <!-- 引入通用错误页面，这样提交等操作，报错的时候，就会使用这个页面来展示错误信息 -->
      @include('shared._errors')

      <form method="POST" action="{{ route('login') }}">
          {{-- Blade 模板为我们提供的 csrf_field 方法，功能相当于令牌token --}}
          {{ csrf_field() }}

          <div class="form-group">
            <label for="email">邮箱：</label>
            {{--  Laravel 提供了全局辅助函数 old 来帮助我们在 Blade 模板中显示旧输入数据。这样当我们信息填写错误，
                页面进行重定向访问时，输入框将自动填写上最后一次输入过的数据 --}}
            <input type="text" name="email" class="form-control" value="{{ old('email') }}">
          </div>

          <div class="form-group">
            <label for="password">密码（<a href="{{ route('password.request') }}">忘记密码</a>）：</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}">
          </div>

          <div class="form-group">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" name="remember" id="exampleCheck1">
              <label class="form-check-label" for="exampleCheck1">记住我</label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary">登录</button>
      </form>

      <hr>

      <p>还没账号？<a href="{{ route('signup') }}">现在注册！</a></p>
    </div>
  </div>
</div>
@stop
