<a href="{{ route('users.show', $user->id) }}">
    {{-- user模型类中定义了gravatar 方法来获取头像信息，这里调用这个方法，获取url --}}
    <img src="{{ $user->gravatar('140') }}" alt="{{ $user->name }}" class="gravatar"/>
  </a>
  <h1>{{ $user->name }}</h1>
