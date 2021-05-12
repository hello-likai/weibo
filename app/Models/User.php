<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

// Laravel 默认为我们生成了用户模型文件

class User extends Authenticatable
{
    // Notifiable:消息通知相关功能引用
    // HasFactory:模型工厂相关功能的引用
    // Authenticatable 是授权相关功能的引用。
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Laravel 在用户模型中默认为我们添加了 fillable 在过滤用户提交的字段，只有包含在该属性中的字段才能够被正常更新：
     * 防止被SQL注入等方式进行破坏
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 用户密码或其它敏感信息在用户实例通过数组或 JSON 显示时进行隐藏
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    # 使用 Gravatar的 API来获取头像信息，根据的是Email
    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }


    public static function boot()
    {
        parent::boot();

        // Eloquent 模型默认提供了多个事件，creating 用于监听模型被创建之前的事件
        static::creating(function ($user){
            $user->activation_token = Str::random(10);
        });
    }

    // 用户可以拥有多条微博，属于一对多的关系
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    // 将当前用户发布过的所有微博从数据库中取出，并根据创建时间来倒序排序
    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at', 'desc');
    }
}
