<?php
/**
 * Created by PhpStorm.
 * User: redredmaple
 * Date: 18-8-24
 * Time: 上午9:59
 */
use SERVICE\AgentService;
class Task_dispatch extends Hilton_Controller
{

    private $service;

    public function __construct()
    {
        $this->Data['PageTitle'] = '派单管理';
        parent::__construct();
        $this->admin_init();
        $this->load->library('taskcachemanager');
    }

    public function index()
    {
        $t = $this->input->get('task_type', TRUE); //TASK_TYPE_DF、TASK_TYPE_LL、TASK_TYPE_PDD
        $this->Data['data'] = $this->taskcachemanager->task_range($t);
        $this->Data['task_type'] = $t;
        $this->Data['TargetPage'] = 'task/index';
        $this->load->view('frame_main', $this->Data);
    }

}
