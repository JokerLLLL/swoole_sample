<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 17:37
 */

//异步客户端模拟机器
//$cli = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);

//同步客户端 模拟网页
$cli = new swoole_client(SWOOLE_SOCK_TCP);
if($cli->connect('127.0.0.1',9500,10)) {
    $cli->send('{"test":"test1"}');
    $response = $cli->recv();
    echo $response.PHP_EOL;
}
$cli->close();

