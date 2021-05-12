<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusesTableSeeder extends Seeder
{
    // 为前三个用户生成共 100 条微博假数据。
    public function run()
    {
        Status::factory()->count(100)->create();
    }
}
