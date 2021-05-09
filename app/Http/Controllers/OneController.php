<?php

/**
 * 这是单控制器的模拟代码
 * namespace 表示命名空间，利用命名空间来区分归类不同的代码功能，避免引起变量名或函数名的冲突
 *
 * use 来引用在 PHP 文件中要使用的类，引用之后便可以对其进行调用。
 *
 * 单行为控制器，路由定义就不需要指定特定的方法，指定控制器即可；
 * 单行为控制器只是语义上的单行为，并没有限制创建更多方法访问；
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OneController extends Controller
{
    public function __invoke(){
        return '单控制器行为';
    }

}
