<?php namespace Naux\LaravelOss;

use Aliyun\OSS\Exceptions\OSSException;
use Aliyun\OSS\Models\OSSOptions;
use Aliyun\OSS\OSSClient;
use Config;
use LogicException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OSS
{
    /**
     * 当前选择的bucket名
     * @var null
     */
    private $bucket = NULL;

    /**
     * OSSClient对象实例
     * @var null
     */
    private $client = NULL;

    /**
     * AccessKey
     * @var null
     */
    private $accessKey = NULL;

    /**
     * AccessKeySecret
     * @var null
     */
    private $accessKeySecret = NULL;

    /**
     * 标示是否使用内网地址
     * @var bool
     */
    private $inner = true;

    /**
     * 构造函数，
     * 初始化配置
     */
    public function __construct()
    {
        $this->accessKey = $this->getConfig('AccessKey');
        $this->accessKeySecret = $this->getConfig('AccessKeySecret');
        $this->inner = $this->getConfig('inner');
//      选择默认bucket
        if($defult = $this->getConfig('default')){
            $this->bucket($defult);
        }
    }

    /**
     * 设置要操作的bucket
     *
     * @author Xuan
     * @param $name
     * @return $this
     */
    public function bucket($name)
    {
        if (!is_string($name)) {
            throw new LogicException('The bucket name must be a String');
        }
        $this->bucket = $name;
        $this->initialClient();
        return $this;
    }

    /**
     * 初始化OSS客户端
     *
     * @author Xuan
     */
    protected function initialClient()
    {
        $configs = array(
            'AccessKeyId' => $this->accessKey,
            'AccessKeySecret' => $this->accessKeySecret,
        );

        if ($city = $this->getConfig('buckets.' . $this->bucket)) {
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
    protected function getServerAddr($city)
    {
        $side = $this->inner ? 'inner' : 'outer';
        return $this->getConfig("endpoints.{$city}.{$side}");
    }

    /**
     * 获取OSSClient对象实例
     *
     * @author Xuan
     * @return null
     */
    public function getClient(){
        if (is_null($this->bucket)) {
            throw new LogicException('You have not selected a bucket');
        }
        return $this->client;
    }

    /**
     * 取得本扩展包的配置
     *
     * @author Xuan
     * @param $key
     * @return mixed
     */
    protected function getConfig($key)
    {
        return Config::get("laravel-oss::config.{$key}");
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                          Object操作                              *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * 上传文件到OSS
     * 文档：http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html#id14
     *
     * @author Xuan
     * @param String $key 上传到OSS的文件名
     * @param UploadedFile|String $file 文件路径或UploadedFile实例
     * @param array $options 可选参数（ContentType，UserMetadata, Expires之类的）
     */
    public function upload($key, $file, $options = array())
    {
        $info = array(
            'Bucket' => $this->bucket,
            'Key' => $key,
        );

        if ($file instanceof UploadedFile) {
            $info['Content'] = fopen($file->getRealPath(), 'r');
            $info['ContentLength'] = $file->getSize();
            $info['ContentType'] = $file->getMimeType();
        } else {
            if (!is_string($file)) {
                throw new LogicException("The second argument must be a String(File path) or instance of " . '\Symfony\Component\HttpFoundation\File\UploadedFile');
            }
            $info['Content'] = fopen(realpath($file), 'r');
            $info['ContentLength'] = filesize($file);
        }

        return $this->client->putObject(array_merge($info, $options));
    }

    /**
     * 删除指定key的object
     * 文档： http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html#id14
     *
     * @author Xuan
     * @param $keys
     */
    public function delete($keys)
    {
        foreach( (Array)$keys as $key){
            $this->client->deleteObject(array(
                'Bucket' => $this->bucket,
                'Key' => $key,
            ));
        }
    }

    /**
     * 拷贝object
     * 文档：http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html#id15
     *
     * @author Xuan
     * @param string $source_key    源object key
     * @param string $dest_key      目标key
     * @param string $dest_bucket   目标bucket名,默认当前bucket
     */
    public function copy($source_key, $dest_key, $dest_bucket = '')
    {
        if (is_null($this->bucket)) {
            throw new LogicException('You have not selected a bucket');
        }

        $this->client->copyObject(array(
            'SourceBucket' => '1hooo',
            'SourceKey' => $source_key,
            'DestBucket' => $dest_bucket ?: $this->bucket,
            'DestKey' => $dest_key,
        ));
    }

    /**
     * 移动object
     *
     * @author Xuan
     * @param String $source_key        源object key
     * @param String $dest_key          目标key
     * @param String $dest_bucket       目标bucket名,默认当前bucket
     */
    public function move($source_key, $dest_key, $dest_bucket = ''){
        $this->copy($source_key, $dest_key, $dest_bucket);
        $this->delete($source_key);
    }

    /**
     * 获取当前bucket的object列表
     * 文档： http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/object.html#id7
     *
     * @author Xuan
     * @param int $start 从几条开始取
     * @param int $limit 最多取几条数据（不能大于1000）
     * @param string $prefix object key必须以Prefix作为前缀
     * @param string $delimiter 对Object名字进行分组的字符
     * @return mixed
     */
    public function objects($start = 0, $limit = 100, $prefix = '', $delimiter = '')
    {
        return $this->client->listObjects(array(
            'Bucket' => $this->bucket,
            'Marker' => (String)$start,
            'MaxKeys' => (String)$limit,
            'Prefix' => $prefix,
            'Delimiter' => $delimiter,
        ));
    }

    /**
     * 获取指定目录下的object列表
     *
     * @author Xuan
     * @param        $directory 目录
     * @param int    $start     从几条开始取
     * @param int    $limit     最多取几条数据（不能大于1000）
     * @param string $delimiter 对Object名字进行分组的字符
     * @return mixed
     */
    public function files($directory, $start = 0, $limit = 100, $delimiter = '')
    {
        $objetListing = $this->objects($start, $limit, $directory.'/', $delimiter);
        foreach($objetListing->getObjectSummarys() as $objectSummary){
            $data = array();
            $data['key']    = $objectSummary->getKey();
            $data['size']   = $objectSummary->getSize();
            $data['last_modified']   = $objectSummary->getLastModified();
            $data['owner']   = $objectSummary->getOwner();

            yield $data;
        }
    }

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     *                          Bucket操作                              *
     * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

    /**
     * 获取用户所拥有的Bucket
     * 文档：http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/bucket.html#id3
     *
     * @author Xuan
     * @return Array
     */
    public function buckets()
    {
        if (!$this->client) {
            $this->initialClient();
        }

        return $this->client->listBuckets();
    }

    /**
     * 删除当前选择的Bucket
     * 文档：http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/bucket.html#id5
     *
     * @author Xuan
     */
    public function destroy()
    {
        $this->client->deleteBucket(array(
            'Bucket' => $this->bucket,
        ));
    }

    /**
     * 创建一个新的Bucket
     * 文档：http://aliyun_portal_storage.oss.aliyuncs.com/oss_api/oss_phphtml/bucket.html#id6
     *
     * @author Xuan
     * @param $bucket_name 要创建的bucket名称
     * @param string $acl bucket访问权限，默认为私有(public-read-write, public-read)
     * @return mixed
     */
    public function create($bucket_name, $acl = 'private')
    {
        if (is_null($this->bucket)) {
            throw new LogicException('You have not selected a bucket');
        }

        return $this->client->createBucket(array(
            'Bucket' => $bucket_name,
            'ACL' => $acl,
        ));
    }
}