<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 16:02
 */

$redis = new swoole_redis();
$redis->connect('127.0.0.1', 6379, function(swoole_redis $redis, $result) {
    if ($result) {
        echo "连接成功" . PHP_EOL;
        $key = 'time';
        $redis->set($key, time(), function(swoole_redis $redis, $result) {
            var_dump($result);
        });
        $redis->get($key, function (swoole_redis $redis, $result) {
            var_dump($result);
            $redis->close();
        });
    } else {
        echo "连接失败" . PHP_EOL;
    }
});

echo "异步redis" . PHP_EOL;
