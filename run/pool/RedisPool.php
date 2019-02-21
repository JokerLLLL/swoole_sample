<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/18
 * Time: 19:10
 */
class RedisPool
{

    //实例
    public static $instance;

    //连接池
    public $pools;

    public $host = '127.0.0.1';

    public $port = 6379;

    public $password = '';

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
        // 配置加载
        $main = require(dirname(dirname(__DIR__)).'/config/main.php');
        isset($main['redis']['host']) && $this->host = $main['redis']['host'];
        isset($main['redis']['port']) && $this->port = $main['redis']['port'];
        isset($main['redis']['password']) && $this->password = $main['redis']['password'];
        isset($main['redis_pool']) && $this->config_pool = array_merge($this->config_pool,$main['redis_pool']);

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
     * 检查最大连接数
     */
    public function keepLive()
    {
        swoole_timer_tick($this->config_pool['check_max_time'] , function(){
            // 维持活跃的链接数在 min-max之间
            //echo 'check'.$this->pools->count().PHP_EOL;
            if($this->pools->count() > $this->config_pool['max']) {
                while($this->config_pool['max'] < $this->pools->count()){
                    /* @var $next Swoole\Coroutine\Redis */
                    $next = $this->pop();
                    $next->close();
                    echo "关闭Redis连接...\n" ;
                }
            }
        });
    }

    /*
     * 创建连接
     */
    public function generate()
    {
        $redis = new Swoole\Coroutine\Redis();
        if(!$redis->connect($this->host,$this->port) && $redis->auth($this->password)) {
            throw new RedisException('connect Error:'.$redis->errMsg,$redis->errCode);
        }
        $this->push($redis);
    }

    /*
     * 出列
     */
    public function pop()
    {
        if($this->pools->count() == 0) {
            $this->generate();
        }
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

}