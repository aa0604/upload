<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:27
 */

namespace xing\upload;

use Yii;
use OSS\OssClient;

/**
 * Class UploadAli
 * @package xing\upload
 *
 * @property \OSS\OssClient $drive
 */
class UploadAli extends \xing\upload\core\BaseUpLoad implements \xing\upload\core\UploadInterface
{

    public $drive;

    public $config;



    public function upload($fieldName, $module = '')
    {
        if (!isset($_FILES[$fieldName])) throw new \Exception('请上传文件');
        $info = $_FILES[$fieldName] ?? null;
        if (empty($info)) throw new \Exception('没有获取到文件');

        $file = $info['tmp_name'];
        $extension = $this->getFileExtension($info['name']);
        $saveFilename = $this->getRelativePath(date('YmdHis') . rand(100, 999) . '.' . $extension, $module);
        $r = $this->drive->uploadFile($this->config['UploadBucket'], $this->relativePath . $saveFilename, $file);
        if (!isset($r['oss-request-url']) || empty($r['oss-request-url'])) throw new \Exception('上传至云端失败');

        return [
            'url' => $this->getFileUrl($saveFilename),
            'saveUrl' => $saveFilename,
        ];
    }

    public function uploadBase64(& $base64, $module = '')
    {
        $newFilename = $this->createBase64Filename($base64);
        #  相对路径
        $saveFilename = $this->getRelativePath($newFilename, $module);
        $r = $this->drive->putObject($this->config['UploadBucket'], $this->relativePath . $saveFilename,  $this->getBase64Decode($base64));
        if (!isset($r['oss-request-url']) || empty($r['oss-request-url'])) throw new \Exception('上传至云端失败');
        return [
            'url' => $this->getFileUrl($saveFilename),
            'saveUrl' => $saveFilename,
        ];
    }


    public function delete($file)
    {
        return $this->drive->deleteObject($this->config['UploadBucket'], $file);
    }

    /**
     * @param $config
     * @return $this
     */
    public function config($config)
    {
        parent::config($config);
        $this->config = $config;

        $this->drive = new OssClient(
            $config['OSS_ACCESS_ID'],
            $config['OSS_ACCESS_KEY'],
            $config['OSS_ENDPOINT']
        );
        return $this;
    }
}