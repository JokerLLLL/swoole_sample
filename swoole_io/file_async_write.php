<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 15:19
 */

$content = "\n一枝红艳露凝香\n云雨巫山枉断肠\n借问汉宫谁得似\n可怜飞燕倚新妆";
swoole_async_write(__DIR__.'/test3.txt',$content,1,function ($filename){
    echo '写入成功1'.PHP_EOL;
});

swoole_async_writefile(__DIR__."/test2.txt", $content, function($filename) {
    echo "写入成功2";
}, FILE_APPEND);

echo "开始写入文件" . PHP_EOL;