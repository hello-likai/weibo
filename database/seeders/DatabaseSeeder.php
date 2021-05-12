<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->call(UsersTableSeeder::class);
        // 指定调用微博数据填充文件
        $this->call(StatusesTableSeeder::class);
        // 为用户添加粉丝和关注人
        $this->call(FollowersTableSeeder::class);

        Model::reguard();
    }
}
