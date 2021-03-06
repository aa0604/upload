<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:28
 */
namespace xing\upload\core;


use xing\helper\resource\HttpHelper;

class BaseUpload
{

    public $allowExtend = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];

    public $fileTypes = [
        'image/bmp' => 'bmp',
        'image/gif' => 'gif',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'audio/x-aac' => 'aac',
        ''
    ];

    public $uploadPathRoot;
    public $domain;

    public $relativePath;

    /**
     * 网络图片转为base64编码
     * @param $url
     * @param string $mime
     * @return string
     */
    public static function base64EncodeImage ($url, $mime = 'image/jpeg') {
        $img = HttpHelper::getFile($url);
        $base64 = 'data:' . $mime . ';base64,' . chunk_split(base64_encode($img));
        return $base64;
    }

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
        if (empty($contentType)) throw new \Exception('无法识别出该文件的类型，请重试');

        // 检查
        $this->checkFileType($contentType);
        return isset($this->fileTypes[$contentType]) ? $this->fileTypes[$contentType] : null;
    }

    protected function checkFileType($contentType)
    {

        // 全空为不限制
        if (empty($this->allowExtend)) return true;
        $extension = isset($this->fileTypes[$contentType]) ? $this->fileTypes[$contentType] : null;
        if (is_null($extension) || !in_array($extension, $this->allowExtend))
            throw new \Exception('您不允许上传您的这种文件类型：' + $contentType);
    }

    /**
     * 获取文件扩展名
     * @param $filename
     * @return bool|string
     */
    public function getFileExtension($filename)
    {
        return substr($filename, strrpos($filename, '.') + 1);
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
        if (empty($relativePath)) return $relativePath;
        return $this->getPrefixUrl($relativePath);
    }

    public function getPrefixUrl($relativePath)
    {
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
    /**
     * 获取文件类型
     * @param $filename
     * @return bool|string
     */
    public static function getFileType($fileName) {
        if (function_exists("finfo_open")) {
            $handle   = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($handle, $fileName);// Return information about a file
            finfo_close($handle);
        } else {
            //TODO:: 若没有启用扩展 fileinfo 采用此方式获取类型，待完善
            $file = fopen($fileName, 'rb');
            $bin  = fread($file, 2); //只读2字节
            fclose($file);
            $strInfo  = @unpack('C2chars', $bin);
            $typeCode = intval($strInfo['chars1'] . $strInfo['chars2']);
            switch ($typeCode) {
                case 255216:
                    $fileType = 'image/jpeg';
                    break;
                case 7173:
                    $fileType = 'image/gif';
                    break;
                case 13780:
                    $fileType = 'image/png';
                    break;
                default:
                    $fileType = "application/octet-stream";
            }
            //Fix
            if ($strInfo['chars1'] == '-1' && $strInfo['chars2'] == '-40') {
                return 'image/jpeg';
            }
            if ($strInfo['chars1'] == '-119' && $strInfo['chars2'] == '80') {
                return 'image/png';
            }
        }

        return $fileType;
    }
}