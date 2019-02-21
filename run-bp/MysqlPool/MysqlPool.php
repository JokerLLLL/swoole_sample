<?php
/**
 * Created by PhpStorm.
 * User: jokerl
 * Date: 2018/11/16
 * Time: 19:45
 */

class MysqlPool
{
    // 连接池数组 .
    protected $connections ;

    // 已连接数
    protected $count = 0 ;

    protected $inited = false ;

    // 单例
    private static $instance ;

    //数据库配置
    protected $config  = array(
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'charset' => 'utf8mb4',
        'timeout' => 2,
    );

    //连接池配置
    protected $config_pool = [
        'min' => 3,   //连接池最小连接数
        'max' => 50,  //连接池最大连接数
        'check_live_time' => 60000, //多少毫秒检查一次连接 ms
        'check_max_time' => 20000,  //多少毫秒检查一下最大值连接 ms 关闭多余的进程
    ];

    public function __construct()
    {
        $config = require_once (dirname(dirname(__DIR__)).'/config/main.php');
        //初始mysql配置
        isset($config['mysql']) && $this->config = array_merge($this->config,$config['mysql']);
        //初始化mysql连接池配置
        isset($config['mysql_pool']) && $this->config_pool = array_merge($this->config_pool,$config['mysql_pool']);
        //初始化连接是一个Spl队列
        $this->connections = new SplQueue() ;
        // 绑定单例
        self::$instance = & $this ;
    }


    // worker启动的时候 建立 min 个连接
    public function init()
    {
        if($this->inited){
            return $this;
        }
        for($i = 0; $i < $this->config_pool['min'] ; $i ++){
            $this->generate();
        }
        return $this ;
    }

    /**
     * 维持当前的连接数不断线，并且剔除断线的链接 .
     */
    public function keepAlive()
    {
        // 1分钟检测一次连接 没个进程都会注册这个函数并调用
        swoole_timer_tick( $this->config_pool['check_live_time'] , function(){
            // 维持连接
           // echo 'micr_time:'.microtime(true).PHP_EOL;

            while ($this->connections->count() > 0 && $next = $this->connections->shift()){
                // while 取出所有的db连接 然后投递 select 1 的逻辑判断 (因外query是异步的) 取的快 投递的慢
                 //echo 'aaaa'.PHP_EOL;
                $next->query("select 1" , function($db ,$res){
                    if($res == false){
                        return ;
                    }
                    $this->connections->push($db);
                    //echo "当前连接数：" . $this->connections->count() . PHP_EOL ;
                });
            }
        });

        swoole_timer_tick($this->config_pool['check_max_time'] , function(){
            // 维持活跃的链接数在 min-max之间
            if($this->connections->count() > $this->config_pool['max']) {
                while($this->config_pool['max'] < $this->connections->count()){
                    $next = $this->connections->shift();
                    $next->close();
                    $this->count-- ;
                    // echo "关闭连接...\n" ;
                }
            }
        });
    }

    // 建立一个新的连接
    public function generate($callback = null)
    {
        $db = new swoole_mysql ;
        $db->connect($this->config , function($db , $res) use($callback) {
            if($res == false){
                throw new Exception("数据库连接错误::" . $db->connect_errno . $db->connect_error);
            }
            $this->count ++ ;
            $this->addConnections($db);
            if(is_callable($callback)){
                call_user_func($callback);
            }
        });
    }

    // 连接推进队列
    public function addConnections($db)
    {
        $this->connections->push($db);
        return $this;
    }

    // 执行数据库命令 . 会判断连接数够不够，够就直接执行，不够就新建连接执行
    public function query($query , $callback)
    {
        if($this->connections->count() == 0) {
            $this->generate(function() use($query,$callback){
                $this->exec($query,$callback);
            });
        }
        else{
            $this->exec($query,$callback);
        }
    }
    // 直接执行数据库命令并且 callback();
    private function exec($query, $callback)
    {
        $db = $this->connections->shift();
        $db->query($query ,function($db , $result) use($callback){
            $this->connections->push($db);
            $callback($result);
        });
    }


    public static function getInstance()
    {
        if(is_null(self::$instance)){
            new self();
        }
        return self::$instance;
    }

}