<?php
/**
 * User: Xuan
 * Date: 15/1/26
 * Time: 下午4:16
 */

return array(
    /**
     * 阿里云申请的AccessKey
     */
    'AccessKey'         =>  'Your AccessKey',

    /**
     * 阿里云申请的AccessKeySecret
     */
    'AccessKeySecret'   =>  'Your AccessKeySecret',

    /**
     * 使用内网链接
     */
    'inner' => true,

    /**
     * buckets配置
     */
    'buckets' => array(
        //配置你的bucket和bucket对应的地区
        'first'     =>  'beijing',
        'second'    =>  'beijing',
    ),

    /**
     * 机房域名配置，如无特殊情况，请勿修改
     */
    'endpoints' => array(
        //青岛
        'qingdao' => array(
            'inner' => 'bucketname.oss-cn-qingdao-internal.aliyuncs.com',
            'outer' => 'bucketname.oss-cn-qingdao.aliyuncs.com'
        ),
        //北京
        'beijing' => array(
            'inner' => 'bucketname.oss-cn-beijing-internal.aliyuncs.com',
            'outer' => 'bucketname.oss-cn-beijing.aliyuncs.com'
        ),
        //杭州
        'hangzhou' => array(
            'inner' => 'oss-cn-hangzhou-internal.aliyuncs.com',
            'outer' => 'oss-cn-hangzhou.aliyuncs.com'
        ),
        //香港
        'hongkong' => array(
            'inner' => 'bucketname.oss-cn-hongkong-internal.aliyuncs.com',
            'outer' => 'bucketname.oss-cn-hongkong.aliyuncs.com'
        ),
        //深圳
        'shenzhen' => array(
            'inner' => 'bucketname.oss-cn-shenzhen-internal.aliyuncs.com',
            'outer' => 'bucketname.oss-cn-shenzhen.aliyuncs.com'
        ),
    ),
);