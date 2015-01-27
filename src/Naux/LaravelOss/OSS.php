<?php namespace Naux\LaravelOss;

use Aliyun\OSS\Models\OSSOptions;
use Aliyun\OSS\OSSClient;
use Config;

class OSS
{
    private $bucket = NULL;

    private $client = NULL;

    private $accessKey = NULL;

    private $accessKeySecret = NULL;

    private $inner = true;

    public function __construct()
    {
        $this->accessKey 		=   $this->getConfig('AccessKey');
        $this->accessKeySecret	=	$this->getConfig('AccessKeySecret');
        $this->inner            =   $this->getConfig('inner');
    }

    /**
     * 设置要操作的bucket
     *
     * @author Xuan
     * @param $name
     */
    public function bucket($name)
    {
        $this->bucket = $name;
        $this->initialClient();
        return this;
    }

    /**
     * 初始化OSS客户端
     *
     * @author Xuan
     */
    protected function initialClient(){
        $configs = array(
            'AccessKeyId'       =>  $this->accessKey,
            'AccessKeySecret'   =>  $this->accessKeySecret,
        );

        if($city = $this->getConfig('buckets.'.$this->bucket)){
            $configs[OSSOptions::ENDPOINT] = $this->getServerAddr($city);
        }

        $this->client = OSSClient::factory($configs);
    }

    /**
     * 根据内外网和城市获取访问地址
     *
     * @author Xuan
     * @param $city
     * @return mixed
     */
    protected function getServerAddr($city){
        $side = $this->inner ? 'inner' : 'outer';
        return $this->getConfig("endpoints.{$city}.{$side}");
    }

    /**
     * 取得本扩展包的配置
     *
     * @author Xuan
     * @param $key
     * @return mixed
     */
    protected function getConfig($key){
        return Config::get("laravel-oss::config.{$key}");
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                          Object操作                              *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
    public function upload(){

    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                          Bucket操作                              *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

}