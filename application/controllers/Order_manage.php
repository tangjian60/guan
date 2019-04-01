<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Order_manage extends Hilton_Controller
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
            $this->Data['order_id'] = $this->input->get('order_id', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['data'] = $this->taskengine->get_task_parent_orders($this->Data);
        }

        $this->Data['TargetPage'] = 'page_orders';
        $this->load->view('frame_main', $this->Data);
    }
}