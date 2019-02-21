<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 17:37
 */

//异步客户端模拟机器
$cli = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);

$cli->on('Connect',function($cli){
    $cli->send('test1');
});

$cli->on('receive',function ($cli,$data) {
   echo $data;
});
$cli->on('error',function ($cli) {
   echo 'error happend';
});

$cli->on('close',function($cli){
    echo 'closed';
});
//同步客户端 模拟网页
//$cli = new swoole_client(SWOOLE_SOCK_TCP);
if($cli->connect('127.0.0.1',9500)) {
//    $cli->send('second');
//    $response = $cli->recv();
//    echo $response.PHP_EOL;
}
//$cli->close();

