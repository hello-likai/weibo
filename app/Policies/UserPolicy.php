<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class UserPolicy
{
    // 不能放到类外面
    use HandlesAuthorization;
    // 第一个参数：根据id获取的用户实例，第二个是要进行授权的用户实例
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
