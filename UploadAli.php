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
class UploadAli  implements \xing\upload\core\UploadInterface
{

    use \xing\upload\core\BaseUpLoad;
    public $drive;

    public $config;


    public function upload($file, $newFile = '', $options = [])
    {

        $oss = & $this->drive;
        $oss->uploadFile($this->config['UploadBucket'], $file, $newFile, $options);
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