# 文件上传
使用各种框架对接各种平台，本地服务器上传文件。
目前支持上传至阿里云

##YII2 上传至本地，再上传至阿里云示例
### YII配置
```$xslt
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
### 代码示例
````
上传到本地
$uploadYii = Yii::$app->upload->getDrive();
$file = $uploadYii->upload($postFieldName, '', ['path' => $module]);
$newFilename = $uploadYii->getFilePath($file['saveUrl']);

Yii::$app->upload->getDrive('ali')->upload($uploadYii->relativePath.$file['saveUrl'], $newFilename);
# 上传后删除
$fullPath = $uploadYii->getDir() . $file['saveUrl'];
unlink($fullPath);
````