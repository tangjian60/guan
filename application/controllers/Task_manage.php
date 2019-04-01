<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('taskengine');
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['task_type'] = $this->input->get('task_type', TRUE);
            $this->Data['task_id'] = $this->input->get('task_id', TRUE);
            $this->Data['order_id'] = $this->input->get('order_id', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['buyer_id'] = $this->input->get('buyer_id', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['create_start_time'] = $this->input->get('create_start_time', TRUE);
            $this->Data['create_end_time'] = $this->input->get('create_end_time', TRUE);
            $this->Data['data'] = $this->taskengine->get_task_list($this->Data);
        }

        $this->Data['TargetPage'] = 'page_tasks';
        $this->load->view('frame_main', $this->Data);
    }
}