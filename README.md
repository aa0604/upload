# 文件上传
使用各种框架对接各种平台，本地服务器上传文件。
目前支持上传至阿里云

#YII2 上传至本地，再上传至阿里云示例
````
上传到本地
$uploadYii = static::getInstance();
$file = $uploadYii->upload($postFieldName, '', ['path' => $module]);
$newFilename = $uploadYii->getFilePath($file['saveUrl']);

static::getInstance('ali')->upload($uploadYii->relativePath.$file['saveUrl'], $newFilename);
# 上传后删除
$fullPath = $uploadYii->getDir() . $file['saveUrl'];
unlink($fullPath);
````