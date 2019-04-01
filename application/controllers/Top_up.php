<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Top_up extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('paycore');
        $this->load->model('rebatecenter');
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['member_name'] = $this->input->get('member_name', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['data'] = $this->hiltoncore->get_top_up_records($this->Data);
        }

        $this->Data['TargetPage'] = 'page_top_up';
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

        if ($act == 'top_up_approve') {

            $top_up_id = $this->input->post('top_up_id', true);
            if (empty($top_up_id)) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            $top_up_record_info = $this->hiltoncore->get_top_up_info($top_up_id);
            if (empty($top_up_record_info) || $top_up_record_info->status != STATUS_CHECKING) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if (!$this->hiltoncore->approve_top_up($top_up_id, $this->get_admin_id())) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if ($this->paycore->top_up($top_up_record_info->seller_id, $top_up_record_info->transfer_amount, $this->get_admin_id()) != Paycore::PAY_CODE_SUCCESS) {
                die(build_response_str(CODE_BAD_REQUEST, "系统错误，充值资金未到账"));
            }

            $this->rebatecenter->seller_top_up_bonus($top_up_record_info);

            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        } else if ($act == 'top_up_reject') {
            if ($this->hiltoncore->reject_top_up($this->input->post('top_up_id', true), $this->get_admin_id())) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }
}