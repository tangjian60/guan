<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_details extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('taskengine');
    }

    public function index()
    {
        $task_type = $this->input->get('task_type', TRUE);
        $task_id = $this->input->get('task_id', TRUE);

        if (empty($task_type) || empty($task_id) || !is_numeric($task_id)) {
            $this->Data['TargetPage'] = 'forbidden';
            $this->load->view('frame_main', $this->Data);
            return;
        } else if ($task_type == TASK_TYPE_LL) {
            $this->Data['TargetPage'] = 'page_details_liuliang';
            $this->Data['data'] = $this->taskengine->get_liuliang_task_info($task_id);
        } else if ($task_type == TASK_TYPE_DF) {
            $this->Data['TargetPage'] = 'page_details_dianfu';
            $this->Data['data'] = $this->taskengine->get_dianfu_task_info($task_id);
        } else if ($task_type == TASK_TYPE_DT) {
            $this->Data['TargetPage'] = 'page_details_duotian';
            $aData = $this->taskengine->get_duotian_task_info($task_id);
            $this->Data['data'] = $aData['detail'];
            $this->Data['show_data'] = $aData['show_data'];


        } else if ($task_type == TASK_TYPE_PDD) {
            $this->Data['TargetPage'] = 'page_details_pinduoduo';
            $this->Data['data'] = $this->taskengine->get_pinduoduo_task_info($task_id);
        } else {
            $this->Data['TargetPage'] = 'forbidden';
        }

        $this->load->view('frame_main', $this->Data);
    }
}