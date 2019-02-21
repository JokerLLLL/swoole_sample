<?php

/** 调度器
 * Created by PhpStorm.
 * User: JokerL
 * Date: 2018/11/18
 * Time: 0:13
 */
class Scheduler
{
    protected $taskList = [];
    protected $timeTicker = 1*100;

    public function newTask(Task $task)
    {
        $this->taskList[] = $task;
    }

    public function ticker()
    {
        // 调度器的关键 run 方法里有send
        swoole_timer_tick($this->timeTicker,function () {
            // echo 'checked'.PHP_EOL;
            foreach ($this->taskList as $key => $value) {
                /* @var $value Task */
                if($value->isFinished()) {
                    unset($this->taskList[$key]);
                }
                $value->run();
            }
        });
    }

}