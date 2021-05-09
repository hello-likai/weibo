const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.

    1，Laravel Mix 用来定义Webpack的编译任务
        webpack.mix.js 的解析引擎是 Node.js ，在 Node.js 中 require 关键词是对模块进行引用
 |  2，添加.version() 是用来清理缓存
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css').version()
    .sourceMaps();
