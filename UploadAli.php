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



    public function upload($file, $newFile = '', $options = [])
    {

        $oss = & $this->drive;
        $oss->uploadFile($this->config['UploadBucket'], $file, $newFile, $options);
        return $oss;
    }

    public function uploadBase64($base64, $filePath = '', $options = [])
    {
        $oss = & $this->drive;
        $oss->putObject($this->config['UploadBucket'], $filePath, $base64);
        return $oss;
    }


    public function delete($file)
    {
        $oss = & $this->drive;
        $oss->deleteObject($this->config['UploadBucket'], $file);
        return $oss;
    }

    /**
     * @param $config
     * @return $this
     */
    public function config($config)
    {
        $this->config = $config;

        $this->drive = new OssClient(
            $config['OSS_ACCESS_ID'],
            $config['OSS_ACCESS_KEY'],
            $config['OSS_ENDPOINT']
        );
        return $this;
    }
}