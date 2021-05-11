@extends('layouts.default')
@section('title', '更新个人资料')

@section('content')
<div class="offset-md-2 col-md-8">
  <div class="card ">
    <div class="card-header">
      <h5>更新个人资料</h5>
    </div>
      <div class="card-body">

        @include('shared._errors')

        <div class="gravatar_edit">
            {{-- 用户头像编辑的位置使用了外部链接跳转，如果用户有更换头像的需要，则可以跳转到 Gravatar 官网上手动更改 --}}
          <a href="http://gravatar.com/emails" target="_blank">
            <img src="{{ $user->gravatar('200') }}" alt="{{ $user->name }}" class="gravatar"/>
          </a>
        </div>

        {{-- 路由到用户控制器update方法上，第二个参数是 用户id --}}
        <form method="POST" action="{{ route('users.update', $user->id )}}">
            {{-- RESTful 架构中，我们使用 PATCH 动作来更新资源，但由于浏览器不支持发送 PATCH 动作，
                因此我们需要在表单中添加一个隐藏域来伪造 PATCH 请求。 --}}
            {{ method_field('PATCH') }}
            {{ csrf_field() }}

            <div class="form-group">
              <label for="name">名称：</label>
              <input type="text" name="name" class="form-control" value="{{ $user->name }}">
            </div>

            <div class="form-group">
              <label for="email">邮箱：</label>
              {{-- 用户注册之后，邮箱不允许修改，因此添加 disabled 属性 --}}
              <input type="text" name="email" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="form-group">
              <label for="password">密码：</label>
              <input type="password" name="password" class="form-control" value="{{ old('password') }}">
            </div>

            <div class="form-group">
              <label for="password_confirmation">确认密码：</label>
              <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
            </div>

            <button type="submit" class="btn btn-primary">更新</button>
        </form>
    </div>
  </div>
</div>
@stop
