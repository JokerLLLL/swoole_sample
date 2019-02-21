<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 17:37
 */

//异步客户端模拟机器
//$cli = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_ASYNC);
require (__DIR__.'/run/helps/Analysis.php');
//同步客户端 模拟网页
$cli = new swoole_client(SWOOLE_SOCK_TCP);
if($cli->connect('127.0.0.1',9500,600)) {
    $data = '234bc0000442020000046200000004c20000000880000000089000000008a000000038b0000807';
    $cli->send((new Analysis())->UnsetFromHexString($data));
    $response = $cli->recv();
    echo $response.PHP_EOL;
}
$cli->close();

