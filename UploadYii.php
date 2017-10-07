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

class UploadYii extends \yii\base\Component implements \xing\upload\core\UploadInterface
{

    use \xing\upload\core\BaseUpLoad;

    public $uploadPathRoot;

    public $maxSize;

    public $allowExtend = ['jpg', 'jpeg', 'png'];

    public $domain;

    public $relativePath;



    public function upload($file, $newFilename = '', $options = [])
    {

        $file = UploadedFile::getInstanceByName($file);
        if (empty($file)) throw new \Exception('请上传文件');

        if ($this->maxSize && $file->size > $this->maxSize) throw new \Exception('文件过大');

        if (!in_array($file->getExtension(), $this->allowExtend))  throw new \Exception('不允许上传此类型的文件');

        if (empty($newFilename)) $newFilename = $this->createFilename() . '.' . $file->getExtension();

        # 创建目录
        $dir = dirname(static::getFilePath($newFilename, $options['path'] ?? ''));
        if (!is_dir($dir)) mkdir($dir,0777,true);

        if($file->saveAs($dir . '/' . $newFilename) === false)
            throw new \Exception('保存文件失败');

        $relativePath = static::getRelativePath($newFilename, $options['path'] ?? '');
        return [
            'url' => static::getFileUrl($relativePath),
            'saveUrl' => $relativePath,
        ];
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

    /**
     * 生成文件名
     * @param null $targetId
     * @return string
     */
    public static function createFilename($targetId = null)
    {
        return $targetId ?: date('YmdHis') . rand(100, 999);
    }

    /**
     * 返回文件存储的绝对路径
     * @param $filename
     * @param $module
     * @return string
     */
    public function getFilePath($filename, $module = '')
    {
        return static::getDir() . ($module ? "$module/" : '') .$filename;
    }

    public function getDir()
    {
        return \Yii::getAlias($this->uploadPathRoot) . $this->relativePath;
    }

    /**
     * 返回文件访问网址
     * @param $filePath
     * @return string
     */
    public function getFileUrl($relativePath)
    {
        return $this->domain . $this->relativePath . $relativePath;
    }
    /**
     * 返回文件存储的相对路径
     * @param $filename
     * @param $module
     * @return string
     */
    public static function getRelativePath($filename, $module = '')
    {
        return $module . '/'. $filename;
    }
}