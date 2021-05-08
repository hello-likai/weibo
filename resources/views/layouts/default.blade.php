<!DOCTYPE html>
<html>
  <head>
    <title>@yield('title', 'Weibo App') - Laravel 入门教程</title>
    <!-- 导入bootstrap后，通过命令npm run watch-poll编译的css文件 Laravel 在运行时，
        是以 public 文件夹为根目录的，因此可以直接使用css/app.css这种相对路径-->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}"> <!-- {{ mix('css/app.css') }}用来动态加载样式代码 -->
  </head>

  <body>
    @include('layouts._header')

    <div class="container">
      <div class="offset-md-1 col-md-10">
        @yield('content')
        @include('layouts._footer')
      </div>
    </div>
  </body>
</html>
