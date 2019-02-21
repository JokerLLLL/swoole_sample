<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 15:19
 */

$file = __DIR__.'/test.txt';

$result = swoole_async_read($file,function ($file_name,$content){
        echo '文件名:'.$file_name.PHP_EOL;
        echo '内容:'.$content.PHP_EOL;
});
echo '开始读取文件'.PHP_EOL;