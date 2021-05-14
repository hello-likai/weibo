<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // bigIncrements() 的封装，此方法创建了一个 bigint unsigned 类型的自增长 id。
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();  //Email 验证时间，空的话意味着用户还未验证邮箱
            $table->string('password');
            $table->rememberToken();
            $table->timestamps(); //创建了created_at 和 updated_at 字段，分别用于保存用户的创建时间和更新时间。
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
