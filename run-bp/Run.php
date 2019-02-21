<?php
/** 日志 + 处理数据
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 20:37
 */
require_once('DataHandle.php');
require_once('LogHandle.php');
require_once ('MysqlLong.php');
//require_once (__DIR__.'/Scheduler/test.php');
// mysql连接池
require_once(__DIR__.'/MysqlPool/MysqlPool.php');
require_once(__DIR__.'/Scheduler/Scheduler.php');
require_once(__DIR__.'/Scheduler/Task.php');

class Run
{
    /* @var Scheduler */
    //调度器
     static $scheduler;

    /** 数据收到处理
     * @param $serv
     * @param $fd
     * @param $workerId
     * @param $data
     */
    public function runReceive($serv, $fd, $workerId, $data)
    {
        DataHandle::receive($serv,$fd,$data);
    }

    /** 进程关闭处理
     * @param $serv
     * @param $fd
     * @param $workerId
     */
    public function runClose($serv, $fd, $workerId)
    {
        DataHandle::close($serv,$fd);
    }


    public function runTask($serv, $taskId, $fromId, $data)
    {
        return DataHandle::task($serv,$data);
    }

    public function runFinish($serv, $taskId, $data)
    {
        DataHandle::finish($serv,$data);
    }
}