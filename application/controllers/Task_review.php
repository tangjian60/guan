<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Task_review extends Hilton_Controller
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
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['data'] = $this->taskengine->get_status_PTSH_task_list($this->Data);
        }

        $this->Data['TargetPage'] = 'page_task_review';
        $this->load->view('frame_main', $this->Data);
    }

    public function operation_handle()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $act = $this->input->post('act', true);
        if (empty($act)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if ($act == 'dianfu_task_approve') {
            if ($this->taskengine->approve_dianfu_task($this->input->post('dianfu_task_id', true), $this->get_admin_id())) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'dianfu_task_reject') {
            if ($this->taskengine->reject_dianfu_task($this->input->post('dianfu_task_id', true), $this->get_admin_id())) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }
}