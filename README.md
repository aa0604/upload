# 文件上传
## 优点
1、独立的YII2框架的插件驱动，上传至yii系统，支持base64格式的图片
2、独立的oss驱动（使用官方sdk封装），支持上传base64格式的图片
3、所有驱动使用interface规范

### 小注：
阿里和yii驱动都是在正式项目中正常使用的，不会有什么问题，但以下示例由于时间本人时间少，可能会出现问题，如果您使用以下例子中出现问题，请发信至877349686@qq.com，如果我有空会修正，感谢


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

    'OSS_ACCESS_ID' => 'LTAI39W47NkFkBcs',
    'OSS_ACCESS_KEY' => 'YESAHIjNUBByZmksPOdFrYxl2XhuVh',
    'OSS_ENDPOINT' => $_SERVER["REMOTE_ADDR"] === '127.0.0.1' ? 'oss-cn-shanghai.aliyuncs.com' : 'oss-cn-shanghai-internal.aliyuncs.com',
    'UploadBucket' => 'yakep',            //上传到云存储服务器的bucket名字
    'UploadDomain' => 'images.yunche5.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
]
];

# 复杂配置：一次性配置多个驱动，可在需要同时使用两个以上驱动的业务中使用，可实现自由切换，如先上传到yii系统，再上传到阿里云
'components' => [
        'upload' => [
            
            'class' => 'xing\upload\core\YiiFactory',
            # 默认使用哪个驱动
            'driveName' => 'yii',
            'maxSize' => 2048000,
            'domain' => '',
            'config' =>[
                'ali' => [
                    'OSS_ACCESS_ID' => '阿里ID',
                    'OSS_ACCESS_KEY' => '阿里KEY',
                    'OSS_ENDPOINT' => 'oss-cn-hongkong.aliyuncs.com',
                    'UploadBucket' => '',            //上传到云存储服务器的bucket名字
                    'UploadDomain' => 'xxx.com',    //上传文件的Bucket可以自定义域名，对于不同的Bucket使用不同的自定义域名
                ],
                'yii' => [
                    'uploadPathRoot' => '@api/web/',
                    'relativePath' => 'upload/',
                    'driveName' => 'yii',
                    'maxSize' => 2048000,
                    'domain' => 'http://xxx.com/',
                ],
            ],
        ]
    ];
        
```
# yii 普通上传
````php
<?php
$postFieldName = '上传表单名，如：model[image]或image';
$file = \yii\web\UploadedFile::getInstanceByName($postFieldName);

# 生成文件保存路径
$upload = Yii::$app->upload;
$newFilename = $upload->getRelativePath(date('YmdHis') . rand(100, 999) . '.' . $file->getExtension(), '图片分类目录，如user');

# 上传
$upload->upload($upload->relativePath . $newFilename, $file->tempName);

# 上传后删除
@unlink($file->tempName);
return [
    'url' => $upload->getFileUrl($newFilename),
    'saveUrl' => $newFilename
];
````
返回：
{"url": "http://xxx.com/upload/member/20171229140622184.jpg",
"saveUrl": "member/20171229140622184.jpg"}
### 前端输出
以上例返回的$file为说明
```php

前端输出：（由于保存到数据库的数据为最短字符串，所以前端输出必须使用此方法）
Yii::$app->upload->getDrive()->getFileUrl('user/123.jpg');
```
### 删除
```php
Yii::$app->upload->delete('user/123.jpg');
```

# 在yii中混合使用

```php
<?php
# 先上传到本地
$uploadYii = Yii::$app->upload->getDrive();
$file = $uploadYii->upload('文件表单名，如：model[image]或image', '', ['path' => '分类目录名，如user']);
# 上传到阿里
 
$newFilename = $uploadYii->getFilePath($file['saveUrl']);
UploadFactory::getInstance('ali')->upload($uploadYii->relativePath.$file['saveUrl'], $newFilename);
# 上传后删除
$fullPath = $uploadYii->getDir() . $file['saveUrl'];
@unlink($fullPath);
```

# 原生使用
```php
<?php

$upload = UploadFactory::getInstance('驱动:如 ali 或 yii')->config('驱动的配置，详细参考上面例子的配置');
$newFilename = $upload->getRelativePath(date('YmdHis') . rand(100, 999) . '.' . $file->getExtension(), '图片分类目录，如user');

# 上传
$upload->upload($upload->relativePath . $newFilename, $file->tempName);

# base64 上传
$newFilename = $upload->createBase64Filename($_POST['base64']);
#  相对路径
$relativePath = $upload::getRelativePath($newFilename, $module);
$fullFile = $upload->getFilePath($newFilename, $module);

if (!$upload->uploadBase64($base64, $fullFile)) throw new \Exception('保存文件失败');

```