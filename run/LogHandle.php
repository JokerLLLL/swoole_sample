<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/16
 * Time: 0:06
 */
class LogHandle
{
    const MAX_SIZE = 5*1024*1024;

    /** 文件日志
     * @param $name
     * @param $msg
     * @param $size
     */
    public static function file_out($name,$msg,$size = null) {
        if($name) {
            $file_name = dirname(__DIR__).'/log/'.$name;
            $file_handel = fopen($file_name, "a");
            $msg = date('Y-m-d H:i:s') . " > " . $msg . "\n";
            fwrite($file_handel, $msg);
            fclose($file_handel);
            // 文件大于MAX_SIZE 备份
            $size =  $size ?: self::MAX_SIZE;
            if(@filesize($file_name) >= $size) {
                @rename($file_name, $file_name.'-bp');
            }
        }
    }

    /** swoole_client 发送的信息纪录
     * @param $serv
     * @param $fd
     * @param $data
     */
    public static function client_log($serv,$fd,$data)
    {
        $time = date('Y-m-d H:i:s');
        $sql = "insert into  `swoole_client` VALUE (NULL,$fd,'$data','$time')";
        $r = MysqlLong::query($sql);
    }

    /** 数据接收保存 （包含16进制 和 json)
     * @param $serv
     * @param $fd
     * @param $row
     * @param $data
     */
    public static function device_log($serv,$fd,$row,$data)
    {
         $time = date('Y-m-d H:i:s');
         $sql = "insert into  `swoole_receive` VALUE (null,$fd,'$row','$data','$time')";
         $r = MysqlLong::query($sql);
         self::file_out('server.log',$data,100*1024*1024);

    }

    /**进程关闭时间
     * @param $serv
     * @param $fd
     */
    public static function close($serv,$fd)
    {
        $time = date('Y-m-d H:i:s');
        $sql = "insert into  `swoole_close` VALUE (null,$fd,null,'$time')";
        MysqlLong::query($sql);
    }

    /** 异步进程任务
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public static function task($serv,$taskId,$data)
    {
        $time = date('Y-m-d H:i:s');
        $sql = "insert into  `swoole_task` VALUE (null,$taskId,'$data','$time')";
        MysqlLong::query($sql);
    }

    /** 异步进程任务结束
     * @param $serv
     * @param $taskId
     * @param $data
     */
    public static function finish($serv,$taskId,$data)
    {
        $time = date('Y-m-d H:i:s');
        $sql = "insert into  `swoole_finish` VALUE (null,$taskId,'$data','$time')";
        MysqlLong::query($sql);
    }

    /* 查看服务器信息 */
    public static function server_info($serv, $fd, $workerId, $data)
    {

        $mysql_connections = MysqlPool::getInstance()->pools->count();
        $redis_connections = RedisPool::getInstance()->pools->count();
        $client_connections = count($serv->connections);
        $setting = print_r($serv->setting,true);
        $info = <<<INFO
     'setting': $setting
     'worker_id': $workerId
     'client_connections': $client_connections
     'mysql_connections': $mysql_connections
     'redis_connections': $redis_connections
INFO;
        $serv->send($fd,$info);
    }



}