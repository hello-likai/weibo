{{-- Blade 支持所有的循环语句和条件判断语句，如 @if, @elseif, @else, @for, @foreach, @while 等等，应用在 Blade 中的表达式都需要以 @ 开头。

    Laravel 默认会将所有的验证错误信息进行闪存。当检测到错误存在时，Laravel 会自动将这些错误消息绑定到视图上，因此我们可以在所有的视图上使用 errors 变量来显示错误信息。
    需要注意的是，在我们对 errors 进行使用时，要先使用 count($errors) 检查其值是否为空。

    --}}
@if (count($errors) > 0)
  <div class="alert alert-danger">
      <ul>
          @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
      </ul>
  </div>
@endif
