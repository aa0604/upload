<?php
/**
 * Created by PhpStorm.
 * User: xing.chen
 * Date: 2017/9/14
 * Time: 15:45
 */

namespace xing\upload\core;


class YiiFactory extends UploadFactory
{

    public $driveName;

    public $config;

    public function init()
    {
        static::$drive = $this->driveName;
    }
    /**
     * @param $name
     * @return \xing\upload\uploadYii|\xing\upload\uploadAli
     */
    public function getDrive($driveName = '')
    {
        $drive = $driveName ?: $this->driveName;

        return (new static::$class[$drive])->config($this->config[$drive]);
    }
}