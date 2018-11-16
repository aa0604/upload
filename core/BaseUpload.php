<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:28
 */

namespace xing\upload\core;


class BaseUpload
{

    public $allowExtend = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];

    public $uploadPathRoot;
    public $domain;

    public $relativePath;

    public function createBase64Filename(& $base64)
    {

        return $this->createFilename() . '.' . $this->getBase64ImageExtension($base64);
    }

    /**
     * 获取图片 base64的扩展名
     * @param $base64
     * @return mixed|null
     * @throws \Exception
     */
    protected function getBase64ImageExtension(& $base64)
    {
        # 检查
        $contentType = substr($base64, 5, stripos($base64, ';') - 5);
        if (empty($contentType)) throw new \Exception('无法识别出该文件的类型');

        $fileTypes = [
            'image/bmp' => 'bmp',
            'image/gif' => 'gif',
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
        ];

        $extension = $fileTypes[$contentType] ?? null;
        if (is_null($extension) || !in_array($extension, $this->allowExtend))
            throw new \Exception('此图片类型未被支持：'. $contentType);
        return $extension;
    }

    /**
     * 获取文件扩展名
     * @param $filename
     * @return bool|string
     */
    protected function getFileExtension($filename)
    {
        return substr($filename, strpos($filename, '.') + 1);
    }

    /**
     * 生成文件名
     * @param null $targetId
     * @return string
     */
    public static function createFilename($targetId = null)
    {
        $path = date('Ym') . '/';
        return $path . ($targetId ?: date('YmdHis') . rand(100, 999));
    }

    /**
     * 返回文件存储的绝对路径
     * @param $filename
     * @param string $module
     * @param bool $mkdir
     * @return string
     */
    public function getFilePath($filename, $module = '', $mkdir = false)
    {
        $saveFilename = static::getDir() . ($module ? "$module/" : '') .$filename;
        if ($mkdir && !is_dir(dirname($saveFilename))) {
            $dir = dirname($saveFilename);
            mkdir($dir, 0777, true);
        }
        return $saveFilename;
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
        if (empty($relativePath)) return $relativePath;
        return preg_match('/:\/\//', $relativePath) ? $relativePath : $this->domain . $this->relativePath . $relativePath;
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

    protected function config($config)
    {
        isset($config['uploadPathRoot']) && $this->uploadPathRoot = $config['uploadPathRoot'];
        isset($config['maxSize']) && $this->maxSize = $config['maxSize'];
        isset($config['allowExtend']) && $this->allowExtend = $config['allowExtend'];
        isset($config['domain']) && $this->domain = $config['domain'];
        isset($config['relativePath']) && $this->relativePath = $config['relativePath'];
        return $this;
    }

    /**
     * 获取 base64 正文
     * @param $base64
     * @return bool|string
     */
    public function getBase64Decode(& $base64)
    {
        return base64_decode(substr($base64, stripos($base64, ',') + 1));
    }
}