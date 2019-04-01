<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Taobao_review extends Hilton_Controller
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
            $this->Data['account_type'] = $this->input->get('account_type', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['data'] = $this->hiltoncore->get_status_check_taobao_infos($this->Data);
            $this->Data['reject'] = \CONSTANT\Audit::getRejectText('taobao');
        }

        $this->Data['TargetPage'] = 'page_taobao_review';
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
        if ($act == 'taobao_approve') {
            if ($this->hiltoncore->approve_taobao_info($this->input->post('taobao_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 1, 2, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，审核通过');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'taobao_reject') {
            if ($this->hiltoncore->reject_taobao_info($this->input->post('taobao_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 1, 2, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，拒绝通过');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }

        $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 1, 2, '会员接单账户ID：' . $this->input->post('taobao_id', true) . '，审核失败');
        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function reject()
    {
        $this->load->model('userbindinfo_model');
        $this->Data['userbind_info'] = $this->userbindinfo_model->getUserBindInfoById(trim($this->input->get('id', true)));
        $this->Data['TargetPage'] = 'account/reject';
        $this->load->view('frame_main', $this->Data);
    }

    public function reject_handle()
    {
        $memo = trim($this->input->post('memo', true));
        if (mb_strlen($memo) <= 0){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请填写拒绝原因");
            return;
        }

        $bindinfo_id = trim($this->input->post('id', true));
        $this->load->model('message_model');
        $this->load->model('userbindinfo_model');
        $userbind_info = $this->userbindinfo_model->getUserBindInfoById($bindinfo_id);
        if (empty($userbind_info)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "未知的错误[数据不存在]");
            return;
        }

        if (STATUS_PASSED == $userbind_info->status || $userbind_info->status == STATUS_FAILED){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请勿重复审核");
            return;
        }

        $oper_id = $this->get_admin_id();
        $title = '买家淘宝/拼多多账号绑定审核--拒绝';
        $flag = $this->message_model->add($memo, $oper_id, $userbind_info->user_id, $title);
        $admin_id   = $this->get_admin_id();
        $admin_name = $this->get_admin_name();
        $user_id    = $userbind_info->user_id;
        if ($flag && $this->hiltoncore->reject_taobao_info($userbind_info->id, $oper_id)) {
            $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 1, 2, '会员接单账户ID：' . $userbind_info->id . '，拒绝通过');
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }else{
            $this->hiltoncore->add_oper_log($user_id, $admin_id, $admin_name, 1, 2, '会员接单账户ID：' . $userbind_info->id . '，审核失败');
            echo build_response_str(CODE_UNKNOWN_ERROR, "系统出错，请稍后重试");
            return;
        }
    }

}