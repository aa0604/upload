<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/15
 * Time: 11:10
 */

namespace xing\upload;

use xing\helper\exception\ParamsException;
use Yii;
use yii\web\UploadedFile;

class UploadYiiLogic
{

    /**
     * 删除已上传的文件
     * @param $file
     * @return bool|\OSS\OssClient
     */
    public static function delete($file)
    {
        $drive = static::getInstance('ali');
        $file = static::getInstance('yii')->relativePath . $file;
        return $drive->delete($file);
    }

    /**
     * 上传图片
     * @param $postFieldName
     * @param $module
     * @return array|\OSS\OssClient
     * @throws \Exception
     */
    public static function ApiUpload($postFieldName, $module)
    {
        if (empty($module)) throw new \Exception('module 不能为空');

        # 上传
        $return = static::getInstance()->upload($postFieldName, $module);

        # 上传后删除 注：阿里云源代码没有释放文件，可能会造成无权限操作的提示
//        $file = UploadedFile::getInstanceByName($postFieldName);
//        @unlink($file->tempName);
        return $return;
    }

    /**
     * 保存base64编码的图片
     * @param $base64
     * @param $module
     * @return array
     * @throws \Exception
     */
    public static function uploadBase64Image($base64, $module)
    {
        if (empty($base64)) throw new \Exception('请上传图片');
        if (empty($module)) throw new ParamsException('module');

        return static::getInstance()->uploadBase64($base64, $module);
    }

    /**
     * 根据数据表中的url 合成绝对路径的url
     * @param $dataUrl
     * @return string
     */
    public static function getDataUrl($dataUrl)
    {
        if (empty($dataUrl)) return $dataUrl;
        return static::getInstance()->getFileUrl($dataUrl);
    }

    /**
     * @param string $driveName
     * @return \xing\upload\UploadYii|\xing\upload\UploadAli
     */
    public static function getInstance($driveName = '')
    {
        set_time_limit(120);
        return Yii::$app->upload->getDrive($driveName);
    }

    /**
     * 对相对路径的图片输出绝对路径，并缩放
     * @param $dataUrl
     * @param int $width
     * @param int $height
     * @return string
     */
    public static function zoomDataUrl($dataUrl, $width = 200, $height = 200)
    {
        if (empty($dataUrl)) return $dataUrl;
        $url = static::getInstance()->getFileUrl($dataUrl);
        // 绝对路径的图片直接返回，以免是别站的图片，加后面的参数人工智能会出错
//        if (preg_match('/http:/i', $dataUrl)) return $url;
        return $url . '?x-oss-process=image/resize,m_lfit,h_'.$width.',w_'.$height;
    }

    /**
     * 获取人工智能识别需要要的图片地址（经过处理后比较小）
     * @param $url
     * @return string
     */
    public static function getAiUrl($url)
    {
        if (empty($url)) return $url;
        $url = static::getInstance()->getFileUrl($url);
        return $url . '?x-oss-process=style/photo-find';
    }

    /*网络图片转为base64编码*/
    public static function base64EncodeImage ($url, $mime = 'image/jpeg') {
        $img = file_get_contents($url);
        $base64 = 'data:' . $mime . ';base64,' . chunk_split(base64_encode($img));
        return $base64;
    }


}