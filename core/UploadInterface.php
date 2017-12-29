<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:16
 */

namespace xing\upload\core;


interface UploadInterface
{


    public function upload($file, $newFile = '', $options = []);

    public function uploadBase64($base64, $newFilename = '', $options = []);

    public function delete($file);

    public function config($config);
}