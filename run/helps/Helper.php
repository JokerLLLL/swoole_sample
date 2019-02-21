<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/21
 * Time: 12:29
 */

class Helper
{
        /*
         *  超时10s 或 获得数据
         */
        public static function timeAfter($seconds = 10,$callBack = null)
        {
            $start_time = time();
            while(time() < $start_time + $seconds) {
                if(is_callable($callBack) && call_user_func($callBack)){
                    break;
                }
                usleep(300);
            }
        }
}