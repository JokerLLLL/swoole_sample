<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/16
 * Time: 0:06
 */
class LogHandle
{
    const MAX_SIZE = 5*1024*1024;

    /** 文件日志
     * @param $name
     * @param $msg
     */
    public static function file_out($name,$msg) {
        if($name) {
            $file_name = dirname(__DIR__).'/log/'.$name;
            $file_handel = fopen($file_name, "a");
            $msg = date('Y-m-d H:i:s') . " > " . $msg . "\n";
            fwrite($file_handel, $msg);
            fclose($file_handel);
            // 文件大于MAX_SIZE 备份
            if(@filesize($file_name) >= self::MAX_SIZE) {
                @rename($file_name, $file_name.'-bp');
            }
        }
    }


    public static function info($msg,$category='default')
    {

    }

    public static function error($msg,$category='default')
    {

    }

    public static function debug($msg,$category='default')
    {

    }



}