@extends('layouts.default')
@section('title', '注册')

@section('content')
<div class="offset-md-2 col-md-8">
  <div class="card ">
    <div class="card-header">
      <h5>注册</h5>
    </div>
    <div class="card-body">

      @include('shared._errors')

      <form method="POST" action="{{ route('users.store') }}">
        {{-- 指向 web 路由文件中定义的 POST、PUT 或 DELETE 路由的任何 HTML 表单都应该包含一个 CSRF 令牌字段，否则，这个请求将会被拒绝 --}}
        {{ csrf_field() }}

        <div class="form-group">
          <label for="name">名称：</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}">
        </div>

        <div class="form-group">
          <label for="email">邮箱：</label>
          <input type="text" name="email" class="form-control" value="{{ old('email') }}">
        </div>

        <div class="form-group">
          <label for="password">密码：</label>
          <input type="password" name="password" class="form-control" value="{{ old('password') }}">
        </div>

        <div class="form-group">
          <label for="password_confirmation">确认密码：</label>
          <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
        </div>

        <button type="submit" class="btn btn-primary">注册</button>
      </form>
    </div>
  </div>
</div>
@stop
