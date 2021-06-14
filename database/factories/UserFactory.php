<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

// 模型工厂，Faker 和 Eloquent 模型工厂来为指定模型的每个字段设置随机值
// 默认的 UserFactory 符合我们的要求，不需要做修改
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * 定义模型中，每个字段的值，最后作为一个数组返回
     * $this->faker 是 PHP 函数库的实例，让我们可以在函数内部使用 Faker 方法来生成假数据并为模型的指定字段赋值。
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'activated' => true, // 更新模型工厂，将生成的假用户都设为已激活状态
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
