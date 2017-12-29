<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:20
 */

namespace xing\upload;

use Yii;
use yii\web\UploadedFile;

class UploadYii extends \xing\upload\core\BaseUpload implements \xing\upload\core\UploadInterface
{

    public $maxSize;



    public function upload($file, $newFilename = '', $options = [])
    {

        $file = UploadedFile::getInstanceByName($file);
        if (empty($file)) throw new \Exception('请上传文件');

        if ($this->maxSize && $file->size > $this->maxSize) throw new \Exception('文件过大');

        if (!empty($this->allowExtend) && !empty($file->getExtension()) && !in_array($file->getExtension(), $this->allowExtend))
            throw new \Exception('不允许上传此类型的文件');

        if($file->saveAs($newFilename) === false) throw new \Exception('保存文件失败');
        return true;
    }

    public function uploadBase64($base64, $newFilename = '', $options = [])
    {

        if (empty($base64)) throw new \Exception('base64 为空');

        # 创建目录
        $dir = dirname($newFilename);
        if (!is_dir($dir)) mkdir($dir,0777,true);

        #  保存图片
        return file_put_contents($newFilename, base64_decode(substr($base64, stripos($base64, ',') + 1)));

    }

    public function delete($file)
    {
        $fullFIle = static::getFilePath($file);
        return unlink($fullFIle);
    }

    public function config($config)
    {
        isset($config['uploadPathRoot']) && $this->uploadPathRoot = $config['uploadPathRoot'];
        isset($config['maxSize']) && $this->maxSize = $config['maxSize'];
        isset($config['allowExtend']) && $this->allowExtend = $config['allowExtend'];
        isset($config['domain']) && $this->domain = $config['domain'];
        isset($config['relativePath']) && $this->relativePath = $config['relativePath'];
        return $this;
    }

}