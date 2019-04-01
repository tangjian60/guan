<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Certification_review extends Hilton_Controller
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
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['data'] = $this->hiltoncore->get_all_cert_infos($this->Data);
            $this->Data['reject'] = \CONSTANT\Audit::getRejectText('certificate');
        }

        $this->Data['TargetPage'] = 'page_certification';
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
        $user_id = $this->input->post('user_id', true);
        if ($act == 'cert_approve') {
            if ($this->hiltoncore->approve_certification_info($this->input->post('cert_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 1, 1, '会员实名记录ID：' . $this->input->post('cert_id', true) . '，审核通过');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'cert_reject') {
            if ($this->hiltoncore->reject_certification_info($this->input->post('cert_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 1, 1, '会员实名记录ID：' . $this->input->post('cert_id', true) . '，拒绝通过');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }
        $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 1, 1, '会员实名记录ID：' . $this->input->post('cert_id', true) . '，操作失败');

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function reject()
    {
        $this->load->model('certificate_model');
        $this->Data['cert_info'] = $this->certificate_model->getCertInfoById(trim($this->input->get('id', true)));
        $this->Data['TargetPage'] = 'certificate/reject';
        $this->load->view('frame_main', $this->Data);
    }

    public function reject_handle()
    {
        $memo = trim($this->input->post('memo', true));
        if (mb_strlen($memo) <= 0){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请填写拒绝原因");
            return;
        }

        // 是否永久禁止提交实名审核
        $assignStatus = (1 === intval(trim($this->input->post('cer_reject', true)))) ? 98 : '';

        $cert_id = trim($this->input->post('id', true));
        $this->load->model('certificate_model');
        $cert_info = $this->certificate_model->getCertInfoById($cert_id);
        if (empty($cert_info)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "未知的错误[数据不存在]");
            return;
        }

        if (STATUS_PASSED == $cert_info->status || $cert_info->status == STATUS_FAILED){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请勿重复审核");
            return;
        }

        $oper_id = $this->get_admin_id();
        $title = '实名认证审核--拒绝';
        $this->load->model('message_model');
        $flag = $this->message_model->add($memo, $oper_id, $cert_info->user_id, $title);

        if ($flag && $this->hiltoncore->reject_certification_info($cert_id, $oper_id, $assignStatus)) {
            $this->hiltoncore->add_oper_log($cert_info->user_id, $oper_id, $this->get_admin_name(), 1, 1, '会员实名记录ID：' . $cert_id . '，拒绝通过');
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }else{
            $this->hiltoncore->add_oper_log($cert_info->user_id, $oper_id, $this->get_admin_name(), 1, 1, '会员实名记录ID：' . $cert_id . '，操作失败');
            echo build_response_str(CODE_UNKNOWN_ERROR, "系统出错，请稍后重试");
            return;
        }
    }
}