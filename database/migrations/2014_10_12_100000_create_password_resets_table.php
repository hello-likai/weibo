<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_resets', function (Blueprint $table) {
            // index()方法是为了给数据库的字段添加索引
            $table->string('email')->index();
            $table->string('token')->index();
            $table->timestamp('created_at');
        });
    }


    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
}
