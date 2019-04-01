<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Timertask extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    //========================================
    //=============  手动执行任务  =============
    //========================================

    // 设置充值统计数据缓存 【根据时间段参数】
    public function doSetRechargeCache()
    {
        $dateStart = $this->input->get('start', true);
        $dateEnd   = $this->input->get('end', true);

        if ($dateStart && $dateEnd) {

            if ($dateEnd < $dateStart) die('Error.');
            $today = date('Y-m-d');
            if ($dateEnd >= $today) die('Error.');

            $this->load->model('Statistics');
            $aDays = getDays($dateStart, $dateEnd);
            $this->Statistics->setRechargeDB($aDays);

            error_log('doSetRechargeCache Finished at : ' . date("Y-m-d H:i:s.u"));
            die( build_response_str(CODE_SUCCESS, 'OK') );
        }
        die('Error.');
    }


    //========================================
    //===============  定时任务  ==============
    //========================================

    // 每天凌晨00:10执行任务
    public function timer0010()
    {
        // 前一天的充值统计数据
        $this->setYesterdayRecharge();
        //执行前一天的运营统计数据
        $this->setYesterdayOperat();
        // 清除过期的取消订单的缓存数据
        $this->setCanceledTaskExpire();
        // 定时删除用户登录缓存信息
        $this->setUserLoginInfoExpire();
    }

    // 每天23:01执行任务
    public function timer2301()
    {
        // 取消并关闭超时未操作的多天任务单
        $this->closeTimeoutTaskDt();
    }

    // 每天凌晨执行前一天的系统余额统计数据
    public function setYesterdaySystem()
    {
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $this->load->model('Statistics');
        $this->Statistics->systemList([$yesterday]);

        error_log('setYesterdaySystem Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }

    // 每天凌晨执行前一天的运营统计数据
    public function setYesterdayOperat()
    {
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $this->load->model('Statistics');
        $this->Statistics->operationList([$yesterday]);

        error_log('setYesterdayOperat Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }

    // 每天凌晨执行前一天的充值统计数据
    public function setYesterdayRecharge()
    {
        $yesterday = date("Y-m-d",strtotime("-1 day"));
        $this->load->model('Statistics');
        $this->Statistics->setRechargeDB([$yesterday]);

        // testing
        //$sql = "INSERT INTO `test` VALUES (0, 200,'setYesterdayRecharge',now())";
        //$this->db->query($sql);

        error_log('setYesterdayRecharge Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }

    // 清除过期的取消订单的缓存数据
    public function setCanceledTaskExpire()
    {
        $this->load->library('redismanager');
        $this->redismanager->assignCacheKey('CANCELED_TASK_DIANFU');

        $hashList = $this->redismanager->hashGetAll();
        if(!empty($hashList)) {
            foreach ($hashList as $key => $val) {
                $val = json_decode($val, true);
                if ($val['expire'] < date('Y-m-d H:i:s')) {
                    $this->redismanager->hashDel($key);
                }
            }
        } else {
            echo('No data.') , '<br>';
        }
        error_log('setCanceledTaskExpire Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }

    // 定时删除用户登录缓存信息
    public function setUserLoginInfoExpire()
    {
        $key = 'USER_LOGIN_INFO';
        $this->load->library('redismanager');
        $this->redismanager->assignCacheKey($key);
        $this->redismanager->del($key);
        error_log('setUserLoginInfoExpire Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }

    // 取消并关闭超时未操作的多天任务单
    public function closeTimeoutTaskDt()
    {
        // testing
        $sql = "INSERT INTO `test` VALUES (0, 200,'closeTimeoutTaskDt',now())";
        $this->db->query($sql);
        $deadLine = date('Y-m-d 23:00:00');
        if (date('Y-m-d H:i:s') < $deadLine) {
            die( build_response_str(CODE_BANED, 'fail') );
        }
        $this->load->model('taskengine');
        $this->taskengine->cancel_timeout_DCZ_tasks_dt($deadLine);
        error_log('closeTimeoutTaskDt Finished at : ' . date("Y-m-d H:i:s.u"));
        echo( build_response_str(CODE_SUCCESS, 'OK') ) , '<br>';
    }


}