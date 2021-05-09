@extends('layouts.default')

@section('content')
  <div class="jumbotron">
    <h1>Hello Laravel</h1>
    <p class="lead">
      你现在所看到的是 <a href="https://learnku.com/courses/laravel-essential-training">Laravel 入门教程</a> 的示例项目主页。
    </p>
    <p>
      一切，将从这里开始。
    </p>
    <p>
        {{-- 链接可以使用通常的 a href="/help" 方式，也可以使用 Lavarel中的路由 ，定义在routes/web.app里面
            使用路由定义链接的好处是：只需要在路由中修改，所有引用的地方都被修改了
        --}}
      <a class="btn btn-lg btn-success" href="{{ route('signup') }}" role="button">现在注册</a>
    </p>
  </div>
@stop
