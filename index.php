<?php
date_default_timezone_set("PRC");
require_once(__DIR__.'/run/Run.php');
$config = require(__DIR__.'/config/main.php');

class Server
{
    private $_serv;
    /* @var Run */
    private $_run;

    public function __construct($config)
    {
        $this->_serv = new swoole_server($config['listen_ip'], $config['listen_port']);
        $this->_serv->set($config['swoole_set']);
        $this->_serv->on('Connect', [$this, 'onConnect']);
        $this->_serv->on('WorkerStart', [$this, 'onWorkerStart']);
        $this->_serv->on('Receive', [$this, 'onReceive']);
        $this->_serv->on('Close', [$this, 'onClose']);
        $this->_serv->on('Task',[$this,'onTask']);
        $this->_serv->on('Finish',[$this,'onFinish']);
        $this->_serv->on('WorkerExit',[$this,'onWorkerExit']);

        // table 全局变量
        // $table = new swoole_table(1024);
        // $table->column('fd', swoole_table::TYPE_INT);
        // $table->column('from_id', swoole_table::TYPE_INT);
        // $table->column('d_id', swoole_table::TYPE_STRING, 64);
        // $table->create();
        // $this ->_serv->table = $table;

    }

    //加载回调执行
    public function onWorkerStart($serv, $workerId)
    {
        //连接池
        go(function(){
            MysqlPool::getInstance()->init();
            RedisPool::getInstance()->init();
        });
        //逻辑程序
        $this->_run = new Run;
    }

    public function onWorkerExit($serv,$workerId)
    {
        // 资源回收
        go(function(){
            MysqlPool::getInstance()->destruct();
            RedisPool::getInstance()->destruct();
        });
    }

    public function onConnect($serv, $fd, $workerId)
    {
        echo date('Y-m-d H:i:s >') . 'fd_connect:' . $fd . PHP_EOL;
    }


    public function onReceive($serv, $fd, $workerId, $data)
    {
        //echo date('Y-m-d H:i:s >').'fd_receive:fd_' . $fd . ';data_' . $data . PHP_EOL;
        $this->_run->runReceive($serv, $fd, $workerId, $data);
    }


    public function onClose($serv, $fd, $workerId)
    {
        $this->_run->runClose($serv, $fd, $workerId);
        echo date('Y-m-d H:i:s >') . 'fd_close:' . $fd . PHP_EOL;
    }

    public function onTask($serv, $taskId, $fromId, $data)
    {
        echo date('Y-m-d H:i:s >') . 'start_task_id:' . $taskId . ';from_id:'.$fromId.PHP_EOL;
        //onTask 有返回值才能触发finish回调函数
        return $this->_run->runTask($serv, $taskId, $fromId, $data);
    }

    public function onFinish($serv, $taskId, $data)
    {
        echo date('Y-m-d H:i:s >') . 'finish_task_id:' . $taskId . PHP_EOL;
        $this->_run->runFinish($serv, $taskId, $data);
    }

    public function start()
    {
        $this->_serv->start();
    }

}

$server = new Server($config);

$server->start();
