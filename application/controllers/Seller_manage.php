<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Seller_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['user_name'] = $this->input->get('user_name', TRUE);
            $this->Data['reg_date'] = $this->input->get('reg_date', TRUE);
            $this->Data['auth_status'] = $this->input->get('auth_status', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['user_type'] = USER_TYPE_SELLER;

            $this->Data['data'] = $this->hiltoncore->get_member_list($this->Data);
        }

        $this->Data['TargetPage'] = 'page_sellers';
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

        if ($act == 'set_black') {
            if ($this->hiltoncore->set_account_ban($this->input->post('member_id', true))) {
                echo build_response_str(CODE_SUCCESS, "设置黑名单成功");
                return;
            }
        } else if ($act == 'unset_black') {
            if ($this->hiltoncore->unset_account_ban($this->input->post('member_id', true))) {
                echo build_response_str(CODE_SUCCESS, "取消黑名单成功");
                return;
            }
        } else if ($act == 'set_commission_discount') {
            $member_id = $this->input->post('member_id', true);
            $commission_discount = $this->input->post('commission_discount', true);

            if (empty($member_id) || !is_numeric($commission_discount) || $commission_discount < MIN_COMMISSION_DISCOUNT || $commission_discount > 100) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if ($this->hiltoncore->set_commission_discount($member_id, $commission_discount)) {
                echo build_response_str(CODE_SUCCESS, "冻结余额成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }
}