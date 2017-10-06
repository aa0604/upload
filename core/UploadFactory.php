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

    # 默认驱动
    public static $drive = 'ali';

    /**
     *
     * @return \xing\upload\uploadYii|\xing\upload\uploadAli
     * @param string $drive
     * @return mixed
     */
    public static function getInstance($drive = '')
    {
        return new static::$class[$drive ?: static::$drive];
    }
}