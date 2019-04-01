<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Huabei_review extends Hilton_Controller
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
            $this->Data['data'] = $this->hiltoncore->get_all_huabei_infos($this->Data);
            $this->Data['reject'] = \CONSTANT\Audit::getRejectText('huabei');
        }

        $this->Data['TargetPage'] = 'page_huabei_review';
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

        if ($act == 'huabei_approve') {
            if ($this->hiltoncore->approve_huabei_info($this->input->post('huabei_id', true), $this->get_admin_id())) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'huabei_reject') {
            if ($this->hiltoncore->reject_huabei_info($this->input->post('huabei_id', true), $this->get_admin_id())) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function reject()
    {
        $this->load->model('userbindinfo_model');
        $this->Data['userbind_info'] = $this->userbindinfo_model->getUserBindInfoById(trim($this->input->get('id', true)));
        $this->Data['TargetPage'] = 'huabei/reject';
        $this->load->view('frame_main', $this->Data);
    }

    public function reject_handle()
    {
        $memo = trim($this->input->post('memo', true));
        if (mb_strlen($memo) <= 0){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请填写拒绝原因");
            return;
        }

        $huabei_id = trim($this->input->post('id', true));
        $this->load->model('message_model');
        $this->load->model('userbindinfo_model');
        $userbind_info = $this->userbindinfo_model->getUserBindInfoById($huabei_id);
        if (empty($userbind_info)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "未知的错误[数据不存在]");
            return;
        }

        if (STATUS_PASSED == $userbind_info->huabei_status || $userbind_info->huabei_status == STATUS_FAILED){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请勿重复审核");
            return;
        }

        $oper_id = $this->get_admin_id();
        $title = '花呗审核--拒绝';
        $flag = $this->message_model->add($memo, $oper_id, $userbind_info->user_id, $title);

        if ($flag && $this->hiltoncore->reject_huabei_info($userbind_info->id, $oper_id)) {
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }else{
            echo build_response_str(CODE_UNKNOWN_ERROR, "系统出错，请稍后重试");
            return;
        }
    }

}