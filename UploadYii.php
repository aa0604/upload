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



    public function upload($file, $module = '')
    {
        $file = UploadedFile::getInstanceByName($file);
        if (empty($file)) throw new \Exception('请上传文件');

        if ($this->maxSize && $file->size > $this->maxSize) throw new \Exception('文件过大');

        if (!empty($this->allowExtend) && !empty($file->getExtension()) && !in_array($file->getExtension(), $this->allowExtend))
            throw new \Exception('不允许上传此类型的文件');

        $saveFilename = $this->getRelativePath($this::createFilename(). '.' . $file->getExtension(), $module);
        if (!is_dir(dirname($this->getFilePath($saveFilename)))) mkdir(dirname($this->getFilePath($saveFilename)), 0777, true);
        if($file->saveAs($this->getFilePath($saveFilename, '', true)) === false) throw new \Exception('保存文件失败');

        return [
            'url' => $this->getFileUrl($saveFilename),
            'saveUrl' => $saveFilename,
        ];
    }

    public function uploadBase64(& $base64, $module = '')
    {

        if (empty($base64)) throw new \Exception('base64 为空');

        $newFilename = $this->createBase64Filename($base64);
        $saveFilename = $this->getRelativePath($newFilename, $module);
        #  保存图片
        $r = file_put_contents($this->getFilePath($newFilename, $module, true), $this->getBase64Decode($base64));
        if (!$r) throw new \Exception('保存文件失败');
        return [
            'url' => $this->getFileUrl($saveFilename),
            'saveUrl' => $saveFilename,
        ];

    }

    public function delete($file)
    {
        $fullFIle = static::getFilePath($file);
        return unlink($fullFIle);
    }

    /**
     * @param $config
     * @return $this
     */
    public function config($config)
    {
        parent::config($config);
        return $this;
    }
}