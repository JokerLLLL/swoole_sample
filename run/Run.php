<?php
/** 日志 + 处理数据
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/15
 * Time: 20:37
 */
require_once('DataHandle.php');
require_once('LogHandle.php');
require_once (__DIR__. '/pool/MysqlLong.php');
require_once (__DIR__. '/pool/RedisLong.php');
// mysql连接池
require_once(__DIR__ . '/pool/MysqlPool.php');
// redis连接池
require_once(__DIR__ . '/pool/RedisPool.php');
//解析类
require_once(__DIR__. '/helps/Analysis.php');
require_once(__DIR__. '/helps/LogicDeal.php');
require_once(__DIR__. '/helps/Helper.php');
//逻辑层
require_once(__DIR__. '/services/ClientService.php');
require_once(__DIR__. '/services/DeviceService.php');

class Run
{
    const SERVER_INFO = 'SERVER_INFO';
    /** 数据收到处理
     * @param $serv
     * @param $fd
     * @param $workerId
     * @param $data
     */
    public function runReceive($serv, $fd, $workerId, $data)
    {
        LogHandle::file_out('all.log','_fd:'.$fd.'_data:'.$data);
        if($data == self::SERVER_INFO){
            LogHandle::server_info($serv, $fd, $workerId, $data);
        }else{
            DataHandle::receive($serv,$fd,$data);
        }
    }

    /** 进程关闭处理
     * @param $serv
     * @param $fd
     * @param $workerId
     */
    public function runClose($serv, $fd, $workerId)
    {
        LogHandle::close($serv,$fd);
        DataHandle::close($serv,$fd);
    }

    /** 进程任务开始
     * @param $serv
     * @param $taskId
     * @param $fromId
     * @param $data
     * @return null
     */
    public function runTask($serv, $taskId, $fromId, $data)
    {
        go(function()use($serv, $taskId, $fromId, $data){
            LogHandle::task($serv,$taskId,$data);
        });
        return DataHandle::task($serv,$data);
    }

    /** 进程任务结束
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public function runFinish($serv, $taskId, $data)
    {
        go(function()use($serv, $taskId,$data){
            LogHandle::finish($serv,$taskId,$data);
        });
        DataHandle::finish($serv,$data);
    }
}