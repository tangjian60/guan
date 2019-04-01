<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notice extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('notice_model');
    }

    public function index()
    {
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['keywords'] = $this->input->get('keywords', TRUE);
        $this->Data['start_time'] = $this->input->get('start_time', TRUE);
        $this->Data['end_time'] = $this->input->get('end_time', TRUE);
        $this->Data['data'] = $this->hiltoncore->get_notice_list($this->Data);
        $this->Data['TargetPage'] = 'page_notice';
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

        if ($act == 'delete_notice') {
            if ($this->hiltoncore->delete_notice($this->input->post('notice_id', true))) {
                echo build_response_str(CODE_SUCCESS, "删除公告成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function top_handle()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $act = trim($this->input->post('act', true));
        if (empty($act)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $notice_id = trim($this->input->post('notice_id', true));
        $is_top = 0;

        if ($act == 'do') {
            $is_top = 1;
            $msg = '置顶成功';
        }else if ($act == 'undo'){
            $is_top = 0;
            $msg = '取消置顶成功';
        }

        if ($this->notice_model->top_notice($notice_id, $is_top)){
            echo build_response_str(CODE_SUCCESS, $msg);
            return;
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

}