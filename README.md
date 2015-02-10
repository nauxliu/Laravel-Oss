# Laravel-Oss
专用于Laravel的阿里云OSS扩展包

##安装
####添加依赖
添加
```
"naux/laravel-oss": "1.*"
```
到你的`composer.json`文件的`require`中,
执行`composer install` 或 `composer update`。

####注册Provider
在你的 `config/app.php`文件 `providers`数组中中添加`'Naux\LaravelOss\LaravelOssServiceProvider'`。

####配置
执行命令`php artisan config:publish naux/laravel-oss`  
然后到`app/config/packages/naux/laravel-oss/config.php`文件中按照注释修改配置。

##实例
```php
//删除bucket foo下的bar对象
OSS::bucket('foo')->delete('bar');

//上面选择了bucket，后面的操作都不用重复

//上传请求中的文件
OSS::upload('foobar', Input::file('image'));

//取得directory目录下的所有对象信息
foreach(OSS::files('directory')){
	//do something
}
```
##使用
####获取原生OSSClient对象
```
OSS::getClient();
```
####选择bucket
```php
OSS::bucket('foo');
```
>如果在配置文件中设置了`default`字段，就不需要选择

####删除bucket
```php
OSS::bucket('foo')->destroy();
```
####创建新的bucket
```php
OSS::create('foo');

//创建bucket同时设置权限
OSS::create('foo', 'public');
```
权限选项：  
1. `private`私有（默认）  
2. `public-read`公共读  
3. `public-read-write` 公共读写  

####获取所有bucket
```php
//当前账号拥有的所有bucket
OSS::buckets();
```
####上传文件
```php
//根据路径上传文件
OSS::upload('foobar', '/temps/file');

//上传文件,同时设置其他信息
OSS::upload('foobar', '/temps/file', [
	'Expires' => new \DateTime("+5 minutes"),
	'Content-Type' => 'foo',
	//...
]);

//上传请求中的文件
OSS::update('foobar', Input::file('foobar'));
```
>使用`Input::file()`上传的文件，会被自动设置`ContentType`

####删除object
```php
OSS::delete('object_key');

//同时删除多个
OSS::delete(['object_key1', 'object_key2']);
```

####拷贝Object
```php
OSS::copy('foo', 'bar');

//从当前的bucket拷贝到其他bucket
OSS::copy('foo', 'bar', 'another_bucket');
```

####移动Object
```php
OSS::move('foo', 'bar');

//从当前的bucket其他bucket并重命名
OSS::move('foo', 'bar', 'another_bucket');
```

####获取所有Object列表
```php
OSS::objects();

//方法声明
//public function objects($start = 0, $limit = 100, $prefix = '', $delimiter = ''){}
```
参数 [参考文档](http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html#id7)

####获取指定目录下Object列表
```php
//获得temps目录下所有object列表
OSS::files('temps');
```
