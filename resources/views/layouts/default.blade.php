<!DOCTYPE html>
<html>
  <head>

    <!--  指令section定义了片段的内容，而 yield 指令则用来显示片段的内容 ，这里的section注释不能加 @,不知道为什么-->
    <title>@yield('title', 'Weibo App') - Laravel 入门教程</title>

    <!-- 导入bootstrap后，通过命令npm run watch-poll编译的css文件 Laravel 在运行时，
        是以 public 文件夹为根目录的，因此可以直接使用css/app.css这种相对路径-->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}"> <!-- {{ mix('css/app.css') }}用来动态加载样式代码 -->
  </head>

  <body>
    @include('layouts._header')

    <div class="container">
      <div class="offset-md-1 col-md-10">
        @include('shared._messages')

        @yield('content')  <!--  这里的content，由继承的子类来定义，比如static_pages里面的几个页面，里面的section('content')中包含的内容，就是要展示在这里的 -->

        @include('layouts._footer')
      </div>
    </div>
  </body>
</html>
