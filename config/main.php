<?php
return [
    // 端口 ip
    'listen_ip'   =>  '0.0.0.0',
    'listen_port' =>  9500,

    // 服务器设置
    'swoole_set' => [
        'max_conn' => 20000,	//此参数用来设置Server最大允许维持多少个tcp连接。
        'worker_num' => 1,
        'open_tcp_keepalive' => 1,
        'daemonize' => false,//启动进程守护
        'log_file' => dirname(__DIR__). '/log/error.log',//输出从定向
        'heartbeat_check_interval' => 600,
        'heartbeat_idle_time' => 6000,
        'task_worker_num' => 3,        //设置task任务数量
        'task_async' =>true,           //启用异步任务
        'enable_coroutine'=>true,      //自动协成数
//        'max_coroutine' => 3000,       //最大协程数量
    ],

    // mysql 连接池配置
    'mysql' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'charset' => 'utf8mb4',
        'timeout' => 5,
    ],

    //mysql连接池配置
    'mysql_pool' =>[
        'min' => 10,   //连接池最小连接数
        'max' => 500,  //连接池最大连接数
        'check_max_time' => 50000,  //多少毫秒检查一下最大值连接 ms 关闭多余的进程
    ],

    //redis 配置
    'redis' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
    ],

    // redis 连接池配置
    'redis_pool' => [
        'min' => 10,   //连接池最小连接数
        'max' => 500,  //连接池最大连接数
        'check_max_time' => 50000,  //多少毫秒检查一下最大值连接 ms 关闭多余的进程
    ]
];