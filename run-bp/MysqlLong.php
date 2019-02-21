<?php
/** 长连接池的封装
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 17:06
 */
require_once 'DataHandle.php';

class MysqlLong
{
        static $result_data = [];
        /** 数据请求和回调处理
         * @param $sql
         * @param $callback
         */
        public static function query_callback($sql,\Closure $callback)
        {
            MysqlPool::getInstance()->query($sql,$callback);
        }

        /*
         * sql请求并异步保存进行调度
         */
        public static function query($sql)
        {
            MysqlPool::getInstance()->query($sql,function ($res){
                 self::$result_data[] = $res;
            });
        }

}