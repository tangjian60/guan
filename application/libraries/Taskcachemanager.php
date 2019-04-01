<?php

class Taskcachemanager
{

    private static $iredis = null;

    function __construct()
    {
    }

    public function connect()
    {

        if (!is_object(self::$iredis) && !self::$iredis) {
            self::$iredis = new Redis();
            self::$iredis->connect(REDIS_SERVER, REDIS_PORT, REDIS_TIME_OUT);
        }
    }

    public function push_dianfu_tasks($task_info)
    {
        if(empty($task_info) || !is_array($task_info)){
            return false;
        }

        $this->connect();

        //shuffle($task_info);
        $taskIds = [];
        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred != NOT_AVAILABLE){
                if (self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_DF, json_encode($v))){
                    $taskIds[] = encode_id($v->id);
                }
            }
        }

        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred == NOT_AVAILABLE){
                if (self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_DF, json_encode($v))){
                    $taskIds[] = encode_id($v->id);
                }
            }
        }

        self::$iredis->expire(TASK_PREFIX . TASK_TYPE_DF, REDIS_TTL);

        error_log('Task_ids('.count($taskIds).'): ' . implode(',', $taskIds));
    }

    /**
     * @name 拼多多订单入缓存
     * @param $task_info
     * @return bool
     * @author chen.jian
     */
    public function push_pinduoduo_tasks($task_info){
        if(empty($task_info) || !is_array($task_info)){
            return false;
        }

        $this->connect();

        //shuffle($task_info);
        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred != NOT_AVAILABLE){
                self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_PDD, json_encode($v));
            }
        }

        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred == NOT_AVAILABLE){
                self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_PDD, json_encode($v));
            }
        }

        self::$iredis->expire(TASK_PREFIX . TASK_TYPE_PDD, REDIS_TTL);
    }

    public function push_liuliang_tasks($task_info)
    {
        if(empty($task_info) || !is_array($task_info)){
            return false;
        }

        $this->connect();

        //shuffle($task_info);
        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred != NOT_AVAILABLE){
                self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_LL, json_encode($v));
            }
        }

        foreach ($task_info as $v){
            if (!empty($v->is_preferred) && $v->is_preferred == NOT_AVAILABLE){
                self::$iredis->rPush(TASK_PREFIX . TASK_TYPE_LL, json_encode($v));
            }
        }

        self::$iredis->expire(TASK_PREFIX . TASK_TYPE_LL, REDIS_TTL);
    }

    public function clean_task_pool()
    {
        $this->connect();
        self::$iredis->del(TASK_PREFIX . TASK_TYPE_LL);
        self::$iredis->del(TASK_PREFIX . TASK_TYPE_DF);
        self::$iredis->del(TASK_PREFIX . TASK_TYPE_PDD);
    }

    public function task_range($task_type)
    {
        $this->connect();
        return self::$iredis->lrange(TASK_PREFIX .$task_type, 0, -1);
    }

    public function get_task_by_index($task_type, $index)
    {
        $this->connect();
        return self::$iredis->lIndex(TASK_PREFIX .$task_type, $index);
    }



}