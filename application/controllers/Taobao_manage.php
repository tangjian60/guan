<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Taobao_manage extends Hilton_Controller
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
            $this->Data['account_type'] = $this->input->get('account_type', TRUE);
            $this->Data['tb_nick'] = $this->input->get('tb_nick', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['user_name'] = $this->input->get("user_name", true);
            $this->Data['data'] = $this->hiltoncore->get_all_taobao_infos($this->Data);
        }

        $this->Data['TargetPage'] = 'page_taobao_manage';
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
        $admin_id   = $this->get_admin_id();
        $admin_name = $this->get_admin_name();
        $user_id    = $this->input->post('user_id', true);
        if ($act == 'set_black') {
            if ($this->hiltoncore->set_taobao_ban($this->input->post('taobao_id', true))) {
                $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 2, 3, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，设置黑名单成功');
                echo build_response_str(CODE_SUCCESS, "设置黑名单成功");
                return;
            }
        } else if ($act == 'unset_black') {
            if ($this->hiltoncore->unset_taobao_ban($this->input->post('taobao_id', true))) {
                $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 2, 3, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，解除黑名单成功');
                echo build_response_str(CODE_SUCCESS, "取消黑名单成功");
                return;
            }
        }
        $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 2, 3, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，黑名单操作失败');

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }
}