<?php namespace Naux\LaravelOss;

use Aliyun\OSS\OSSClient;
use Config;

class OSS
{
    private $bucket = NULL;

    private $client = NULL;

    public function __construct($accessKeyId, $accessKeySecret)
    {
        $this->client = OSSClient::factory(array(
            'AccessKeyId' => $accessKeyId,
            'AccessKeySecret' => $accessKeySecret,
        ));
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
    }

    /**
     * 上传object
     *
     * @author Xuan
     * @param $name
     * @param $path
     */
    public function upload($name, $path)
    {

    }
}