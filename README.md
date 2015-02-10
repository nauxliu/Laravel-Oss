# Laravel-Oss
专用于Laravel的阿里云OSS扩展包

##安装
###添加依赖
添加
```
"naux/laravel-oss": "1.*"
```
到你的`composer.json`文件的`require`中,
执行`composer install` 或 `composer update`。

###注册Provider
在你的 `config/app.php`文件 `providers`数组中中添加`'Naux\LaravelOss\LaravelOssServiceProvider'`。

###配置
执行命令`php artisan config:publish naux/laravel-oss`  
然后到`app/config/packages/naux/laravel-oss/config.php`文件中按照注释修改配置。
