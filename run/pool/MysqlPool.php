<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/18
 * Time: 19:10
 */
class MysqlPool
{

    //实例
    public static $instance;

    //连接池
    public $pools;

    //mysql设置
    public $config = [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'charset' => 'utf8mb4',
        'timeout' => 5,
    ];

    //连接池设置
    public $config_pool = [
        'min'=>10,
        'max'=>200,
        'check_max_time'=>50000,
        'check_live_time'=>50000,
    ];

    /*
     * 初始化参数
     */
    public function __construct()
    {
        $main = require(dirname(dirname(__DIR__)).'/config/main.php');
        isset($main['mysql']) && $this->config = array_merge($this->config,$main['mysql']);
        isset($main['mysql_pool']) && $this->config_pool = array_merge($this->config_pool,$main['mysql_pool']);

        //初始化连接是一个Spl队列
        $this->pools = new SplQueue();
        //设置实例化
        self::$instance = &$this;
    }

    /*
     * 获取实例
     */
    public static function getInstance()
    {
         if(is_null(self::$instance)){
             new self();
         }
         return self::$instance;
    }

    /*
     * 初始化的时候建立最小连接池
     */
    public function init()
    {
        for($i = 0; $i < $this->config_pool['min'] ; $i ++){
            $this->generate();
        }
        $this->keepLive();
        return $this ;
    }


    /*
     * 验证连接是否断
     */
    public function keepLive()
    {
        swoole_timer_tick($this->config_pool['check_max_time'] , function(){
            // 维持活跃的链接数在 min-max之间
           // echo 'check'.$this->pools->count().PHP_EOL;
            if($this->pools->count() > $this->config_pool['max']) {
                while($this->config_pool['max'] < $this->pools->count()){
                    /* @var $next Swoole\Coroutine\MySQL */
                    $next = $this->pop();
                    $next->close();
                    echo "关闭Mysql连接...\n" ;
                }
            }
        });
    }

    /*
     * 创建连接
     */
    public function generate()
    {
        $db = new Swoole\Coroutine\Mysql();
        if(!$db->connect($this->config)) {
            throw new \Swoole\Mysql\Exception('connect Error:'.$db->error,$db->errno);
        }
        $this->push($db);
    }

    /*
     * 出列
     */
    public function pop()
    {
        return $this->pools->shift();
    }

    /*
     *入列
     */
    public function push($db)
    {
        $this->pools->push($db);
        return $this;
    }


    /*
     * 连接回收
     */
    public function destruct()
    {
        while ($this->pools->count() != 0) {
            $this->pop();
        }
    }

    /* 连接池使用和返回
     * sql执行
     */
    public function query($sql,$time_out = -1)
    {
        if($this->pools->count() == 0) {
            $this->generate();
        }
        $db = $this->pop();
        $result = $db->query($sql,$time_out);
        $this->push($db);
        return $result;
    }

}