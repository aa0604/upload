# 文件上传
## 优点
1、独立的YII2框架的插件驱动，上传至yii系统，支持base64格式的图片
2、独立的oss驱动（使用官方sdk封装），支持上传base64格式的图片
3、所有驱动使用interface规范

# 安装
composer require xing.chen/upload dev-master

# YII2驱动
### YII配置
```php
<?php

# 配置：
# 简单配置直接使用 Yii::$app->upload
# 复制方式使用：Yii::$app->upload->getDrive('驱动名如 yii 或 ali');

# yii简单配置
'components' => [
    'class' => 'xing\upload\UploadYii',
    'uploadPathRoot' => '@api/web/', # 网站根目录
    'relativePath' => 'upload/', # 上传根目录
    'maxSize' => 2048000, ## 上传大小限制
    'domain' => 'http://images.xxx.com/', # 访问域名
];
# oss 简单配置
'components' => [
'class' => 'xing\upload\UploadAli',
'uploadPathRoot' => '@api/web/',
'relativePath' => 'upload/',
'maxSize' => 2048000,
'domain' => 'http://images.yunche5.com/',
'config' => [

    'OSS_ACCESS_ID' => 'oos id',
    'OSS_ACCESS_KEY' => 'OSS_ACCESS_KEY',
    'OSS_ENDPOINT' => $_SERVER["REMOTE_ADDR"] === '127.0.0.1' ? 'oss-cn-shanghai.aliyuncs.com' : 'oss-cn-shanghai-internal.aliyuncs.com',
    'UploadBucket' => 'yakep',            //上传到云存储服务器的bucket名字
    'UploadDomain' => 'images.yunche5.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
]
];

# 复杂配置：一次性配置多个驱动，可在需要同时使用两个以上驱动的业务中使用，可实现自由切换，如先上传到yii系统，再上传到阿里云
'components' => [
        'upload' => [
            'class' => 'xing\upload\core\YiiFactory',
            # 默认使用驱动
            'driveName' => 'ali',
            'config' =>[
                'ali' => [
                    'OSS_ACCESS_ID' => 'OSS_ACCESS_ID',
                    'OSS_ACCESS_KEY' => 'OSS_ACCESS_KEY',
                    'OSS_ENDPOINT' => 'oss 里的ENDPOINT',
                    'UploadBucket' => 'Bucket名称',            //上传到云存储服务器的bucket名字
                    'UploadDomain' => 'xxx.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
                    'domain' => 'http://xxx.com/',
                    'relativePath' => 'upload/',
                ],
                'yii' => [
                    'uploadPathRoot' => '@api/web/',
                    'maxSize' => 2048000,
                    'domain' => 'http://images.yunche5.com/',
                    'relativePath' => 'upload/',
                ],
            ],
        ]
    ];
        
```
### 简单配置 & yii 框架
````php
<?php
$postFieldName = '上传表单名，如：model[image]或image';
# 文件上传
$r = Yii::$app->upload->upload($postFieldName, '图片分类目录，如user');
# base64 编码上传
$r = Yii::$app->upload->uploadBase64($postFieldName, '图片分类目录，如user');
# 相对url补全 （ 保存到数据库为 $r['saveUrl']，数据取出时需补全）
Yii::$app->upload->getFileUrl($r['saveUrl']);
# 删除
Yii::$app->upload->delete($r['saveUrl']);
````
###混合配置 & yii 框架
```php
<?php
# 文件上传
$driveName = '填ali 或 yii';
$postFieldName = '上传表单名，如：model[image]或image';

Yii::$app->upload->getDrive($driveName)->upload($postFieldName, '图片分类目录，如user');
# base64 编码上传
Yii::$app->upload->getDrive($driveName)->uploadBase64($postFieldName, '图片分类目录，如user');
# 相对url补全 （ 保存到数据库为 $r['saveUrl']，数据取出时需补全）
Yii::$app->upload->getDrive($driveName)->getFileUrl($r['saveUrl']);
# 删除
Yii::$app->upload->getDrive($driveName)->delete($r['saveUrl']);
```
返回为数据为：
{"url": "http://xxx.com/upload/member/20171229140622184.jpg",
"saveUrl": "member/20171229140622184.jpg"}

# 原生代码使用
```php
<?php
$drive = '驱动:如 ali 或 yii';
$upload = \xing\upload\core\UploadFactory::getInstance($drive)->config('驱动的简单配置，详细参考上面例子的简单配置');

# 普通上传
$newFilename = $upload->upload($postFieldName, '图片分类目录，如user');
# base64
$newFilename = $upload->uploadBase64($postFieldName, '图片分类目录，如user');

# 相对url补全 （ 保存到数据库为 $r['saveUrl']，数据取出时需补全）
$upload->getFileUrl($r['saveUrl']);
# 删除
$upload->delete($r['saveUrl']);


```