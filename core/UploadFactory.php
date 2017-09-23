<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 12:34
 */

namespace xing\upload\core;


class UploadFactory
{

    public static $class = [
        'yii' => '\xing\upload\UploadYii',
        'ali' => '\xing\upload\UploadAli',
    ];

    # 使用驱动
    public static $drive = 'ali';

    /**
     * @param $name
     * @return \xing\upload\uploadYii|\xing\upload\uploadAli
     */
    public static function getInstance()
    {
        return new static::$class[static::$drive];
    }
}