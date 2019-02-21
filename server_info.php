<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 17:37
 */
const SERVER_INFO = 'SERVER_INFO';
$cli = new swoole_client(SWOOLE_SOCK_TCP);
if($cli->connect('127.0.0.1',9500,10)) {
    $cli->send(SERVER_INFO);
    $response = $cli->recv();
    echo $response.PHP_EOL;
}
$cli->close();

