# 文件上传
目前成果：支持YII2，支持上传至阿里云OSS

最终目标：对接各种框架，对接各种平台。

更新速度：正式项目中使用，按需求更新（不怎么更新）
## YII2
### YII配置
```php
'components' => [
        'upload' => [
            'class' => 'xing\upload\core\YiiFactory',
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
        
```
### YII2 上传
````php
# 上传到本地
$uploadYii = Yii::$app->upload->getDrive();
$file = $uploadYii->upload('上传表单名，如：model[image]或image', '', ['path' => '分类目录名，如user']);
````
### YII2 上传返回说明
以上例返回的$file为说明
```php

$file['url'] 返回图片完整的绝对url 如：d:/www/upload/user/123.jpg
$file['saveUrl'] 返回可保存到数据库的相对url 如 user/123.jpg

前端输出：（由于保存到数据库的数据为最短字符串，所以前端输出必须使用此方法）
Yii::$app->upload->getDrive()->getFileUrl('user/123.jpg');
```
### YII2 删除
```php
Yii::$app->upload->getDrive()->delete('user/123.jpg');
```

## 阿里云OSS

### 上传到阿里云
```php
# 先上传到本地
$uploadYii = Yii::$app->upload->getDrive();
$file = $uploadYii->upload('上传表单名，如：model[image]或image', '', ['path' => '分类目录名，如user']);
 
$newFilename = $uploadYii->getFilePath($file['saveUrl']);
UploadFactory::getInstance('ali')->upload($uploadYii->relativePath.$file['saveUrl'], $newFilename);
# 上传后删除
$fullPath = $uploadYii->getDir() . $file['saveUrl'];
unlink($fullPath);
```
### 阿里云oss删除
```php
// 注意文件路径和YII2的不一样，比如yii2可以省略upload，这里的路径需要加上upload
UploadFactory::getInstance('ali')->delete('upload/user/123.jpg');
```