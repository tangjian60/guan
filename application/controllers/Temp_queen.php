<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Temp_queen extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('taskcachemanager');
        $this->load->model('taskengine');
        $this->load->model('paycore');
        $this->load->model('rebatecenter');
        $this->load->library('YTOExpress');
    }

    public function index()
    {
        // 超时接单任务处理
        $this->taskengine->cancel_timeout_DCZ_tasks_temp();
    }

}