<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// 自动授权注册
class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];


    // 自定义自动授权注册的规则
    public function boot()
    {
        $this->registerPolicies();
        // 修改策略自动发现的逻辑
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            // 动态返回模型对应的策略名称，如：// 'App\Models\User' => 'App\Policies\UserPolicy',
            return 'App\Policies\\'.class_basename($modelClass).'Policy';
        });
    }

}
