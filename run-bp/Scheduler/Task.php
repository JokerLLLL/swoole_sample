<?php

/**
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/18
 * Time: 0:25
 */
class Task
{
    public $generator;
    public $is_finished = false;
    static $result = [];

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function run()
    {
        $key = $this->generator->key();
        echo 'key:'.$key.PHP_EOL;

        $result = $this->getResult($key);

        echo 'result:'.$result.PHP_EOL;
        if(!is_null($result)) {
            echo 'start_send'.PHP_EOL;
            $this->generator->send($result);
            // 为了让任务结束，我们判断一下迭代器里面是否还有值
            if(!$this->generator->valid()) {
                // XXX 这里的数据是共享的 所有 MysqlLong::$result_data 只是现实的一个方式 无法实际使用
                // TODO 只是测试
                //回收垃圾
                MysqlLong::$result_data = [];
                $this->is_finished = true;
            }
        }
    }

    public function getResult($key)
    {
        $result = null;
//        isset(self::$result[$key]) && $result = self::$result[$key];
        isset(MysqlLong::$result_data[$key]) && $result = MysqlLong::$result_data[$key];
        return $result;
    }

    public function isFinished()
    {
        return $this->is_finished;
    }
}