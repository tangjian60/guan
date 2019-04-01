<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shop_review extends Hilton_Controller
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
            $this->Data['data'] = $this->hiltoncore->get_status_check_shop_infos($this->Data);
            $this->Data['reject'] = \CONSTANT\Audit::getRejectText('shop');
        }

        $this->Data['TargetPage'] = 'page_shop_review';
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
        $seller_id = $this->input->post('seller_id', true);
        if ($act == 'shop_approve') {
            if ($this->hiltoncore->approve_shop_info($this->input->post('shop_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($seller_id, $this->get_admin_id(), $this->get_admin_name(), 1, 3, '商家店铺ID：' . $this->input->post('shop_id', true) . '，审核成功');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        } else if ($act == 'shop_reject') {
            if ($this->hiltoncore->reject_shop_info($this->input->post('shop_id', true), $this->get_admin_id())) {
                $this->hiltoncore->add_oper_log($seller_id, $this->get_admin_id(), $this->get_admin_name(), 1, 3, '商家店铺ID：' . $this->input->post('shop_id', true) . '，拒绝通过');
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }
        $this->hiltoncore->add_oper_log($seller_id, $this->get_admin_id(), $this->get_admin_name(), 1, 3, '商家店铺ID：' . $this->input->post('shop_id', true) . '，操作失败');

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }


    public function reject()
    {
        $this->load->model('shop_model');
        $this->Data['shop_info'] = $this->shop_model->getShopInfoById(trim($this->input->get('id', true)));
        $this->Data['TargetPage'] = 'shop/reject';
        $this->load->view('frame_main', $this->Data);
    }

    public function reject_handle()
    {
        $memo = trim($this->input->post('memo', true));
        if (mb_strlen($memo) <= 0){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请填写拒绝原因");
            return;
        }

        $shop_id = trim($this->input->post('id', true));
        $this->load->model('message_model');
        $this->load->model('shop_model');
        $shop_info = $this->shop_model->getShopInfoById($shop_id);

        if (empty($shop_info)){
            echo build_response_str(CODE_UNKNOWN_ERROR, "未知的错误[数据不存在]");
            return;
        }

        if (STATUS_PASSED == $shop_info->status || $shop_info->status == STATUS_FAILED){
            echo build_response_str(CODE_UNKNOWN_ERROR, "请勿重复审核");
            return;
        }

        $oper_id = $this->get_admin_id();
        $title = '店铺认证审核--拒绝';
        $flag = $this->message_model->add($memo, $oper_id, $shop_info->seller_id, $title);

        if ($flag && $this->hiltoncore->reject_shop_info($shop_id, $oper_id)) {
            $this->hiltoncore->add_oper_log($shop_info->seller_id, $this->get_admin_id(), $this->get_admin_name(), 1, 3, '商家店铺ID：' . $shop_id . '，拒绝通过');
            echo build_response_str(CODE_SUCCESS, "操作成功");
            return;
        }else{
            $this->hiltoncore->add_oper_log($shop_info->seller_id, $this->get_admin_id(), $this->get_admin_name(), 1, 3, '商家店铺ID：' . $shop_id . '，操作失败');
            echo build_response_str(CODE_UNKNOWN_ERROR, "系统出错，请稍后重试");
            return;
        }
    }
}