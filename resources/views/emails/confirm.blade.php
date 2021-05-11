<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>注册确认链接</title>
</head>
<body>
  <h1>感谢您在 Weibo App 网站进行注册！</h1>
{{-- 在 Laravel 中，我们使用视图来构建邮件模板，在用户查收邮件时，该模板将作为内容展示视图 --}}
  <p>
    请点击下面的链接完成注册：
    <a href="{{ route('confirm_email', $user->activation_token) }}">
      {{ route('confirm_email', $user->activation_token) }}
    </a>
  </p>

  <p>
    如果这不是您本人的操作，请忽略此邮件。
  </p>
</body>
</html>
