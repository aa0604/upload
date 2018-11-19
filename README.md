# 文件上传
## 优点
1、支持YII2，和阿里云OSS

2、支持上传base64字符串的图片，支持上传图片

3、所有驱动使用interface规范

# 目录
* [安装](#安装)
* [配置](#配置)
    * [原生代码配置](#原生代码配置)
    * [YII框架配置](#YII框架配置)
* 上传
    * [原生代码上传](#原生代码上传)
    * [yii框架上传](#yii框架上传)
* [返回数据说明](#返回数据说明)
* [前端访问](#前端访问)
* [删除图片](#删除图片)

# 安装
composer require xing.chen/upload dev-master

# 配置
### 原生代码配置
```php
<?php
// 上传到自有服务器
$config =  [
  'class' => 'xing\upload\UploadYii',
  'uploadPathRoot' => '/var/www/', # 网站目录
  'relativePath' => 'upload/', # 上传目录
  'maxSize' => 2048000, ## 上传大小限制
  'domain' => 'http://images.xxx.com/', # 访问域名
];

// 上传到OSS
$config = [
    'uploadPathRoot' => '/var/www/', # 网站目录
    'relativePath' => 'upload/',
    'maxSize' => 2048000, ## 上传大小限制
    'domain' => 'http://images.xxx.com/', # 访问域名
    'config' => [
    
      'OSS_ACCESS_ID' => 'oos id',
      'OSS_ACCESS_KEY' => 'OSS_ACCESS_KEY',
      'OSS_ENDPOINT' => $_SERVER["REMOTE_ADDR"] === '127.0.0.1' ? 'oss-cn-shanghai.aliyuncs.com' : 'oss-cn-shanghai-internal.aliyuncs.com',
      'UploadBucket' => 'bucket',            //上传到云存储服务器的bucket名字
      'UploadDomain' => 'xxx.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
    ]
];
```
### YII框架配置
```php
<?php

# yii单一配置
'components' => [
    'class' => 'xing\upload\UploadYii',
    'uploadPathRoot' => '@api/web/', # 网站根目录
    'relativePath' => 'upload/', # 上传根目录
    'maxSize' => 2048000, ## 上传大小限制
    'domain' => 'http://xxx.com/', # 访问域名
];
```

### OSS配置
yii框架中可以同时支持上传至oss和自有服务器
```php
<?php


'components' => [
    'upload' => [
        'class' => 'xing\upload\core\YiiFactory',
        # 默认使用驱动
        'driveName' => 'ali',
        'config' =>[
            // oss配置
            'ali' => [
                'OSS_ACCESS_ID' => 'OSS_ACCESS_ID',
                'OSS_ACCESS_KEY' => 'OSS_ACCESS_KEY',
                'OSS_ENDPOINT' => 'oss 里的ENDPOINT',
                'UploadBucket' => 'Bucket名称',            //上传到云存储服务器的bucket名字
                'UploadDomain' => 'xxx.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
                'domain' => 'http://xxx.com/',
                'relativePath' => 'upload/',
            ],
            // 上传到自有服务器配置
            'yii' => [
                'uploadPathRoot' => '@api/web/',
                'maxSize' => 2048000,
                'domain' => 'http://xxx.com/',
                'relativePath' => 'upload/',
            ],
        ],
    ]
    ];
```


# 原生代码上传
```php
<?php
$drive = '驱动:如 ali 或 yii';
$upload = \xing\upload\core\UploadFactory::getInstance($drive)->config('驱动的简单配置，详细参考单一配置');

# 文件上传
$r = $upload->upload($postFieldName, '图片分类目录，如user');
# base64编码上传
$r = $upload->uploadBase64('base64编码', '图片分类目录，如user');

```


### yii框架上传
```php
<?php
# 文件上传
$driveName = '填ali 或 yii，留空则使用默认配置中的';
$postFieldName = '上传表单名，如：model[image]或image';

$upload = Yii::$app->upload->getDrive($driveName);
$r = $upload->upload($postFieldName, '图片分类目录，如user');
# base64 编码上传
$r = $upload->uploadBase64('base64编码', '图片分类目录，如user');


```
## 返回数据说明
//返回为数据为：
["url" => "http://xxx.com/upload/user/20171229140622184.jpg", // 完整url
"saveUrl" => "user/20171229140622184.jpg" // 相对url，保存到数据库里使用这个
]


### 前端访问
```php

<?php
# 相对url补全 （ 保存到数据库为 $r['saveUrl']，数据取出时需补全）
$upload->getFileUrl($r['saveUrl']); // 图片完整访问url
# 删除
```

### 删除图片
```php

<?php
$upload->delete($r['saveUrl']); // 删除图片
?>
```