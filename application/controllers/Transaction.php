<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaction extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
        $this->load->model('paycore');
    }

    public function index()
    {
        $this->Data['member_id'] = $this->input->get('member_id', TRUE);
        $this->Data['TargetPage'] = 'page_transaction';
        $this->load->view('frame_main', $this->Data);
    }

    public function commit()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        $member_id      = $this->input->post('member_id', TRUE);
        $amount         = $this->input->post('amount', TRUE);
        $amount_type    = $this->input->post('amount_type', TRUE);
        if (empty($member_id) || empty($amount) || empty($amount_type)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        $user_type      = '';
        $memo           = '';
        $user           = '';
        switch ($amount_type) {
            case 1:
                $user = '会员';
                $memo .= '会员本金操作，会员ID：' . $member_id . '，';
                $user_type = USER_TYPE_BUYER;
                break;
            case 2:
                $user = '会员';
                $memo .= '会员佣金操作，会员ID：' . $member_id . '，';
                $user_type = USER_TYPE_BUYER;
                break;
            case 3:
                $user = '商家';
                $memo .= '商家金额操作，商家ID：' . $member_id . '，';
                $user_type = USER_TYPE_SELLER;
                break;
            default:
                break;
        }

        $member_id = decode_id($member_id);
        $userInfo = $this->hiltoncore->get_member_info($member_id, ['user_type', 'balance']);
        if(empty($userInfo)) {
            die(build_response_str(CODE_BAD_REQUEST, '用户不存在！'));
        }

        if ($userInfo->user_type != $user_type) {
            die(build_response_str(CODE_BAD_REQUEST, '该操作对象不是' . $user. '，重新选择操作类型'));
        }

        if ($amount < 0 && $userInfo->balance + $amount < 0) {
            die(build_response_str(CODE_BAD_REQUEST, '用户余额不足, 扣款失败！'));
        }

//        if ($this->hiltoncore->get_member_exist(['user_type' => $user_type, 'member_id' => $member_id]) <= 0) {
//            die(build_response_str(CODE_BAD_REQUEST, '该操作对象不是' . $user. '，重新选择操作类型'));
//        }

        $memo           .= $this->input->post('memo', TRUE);

        if ($this->paycore->special_trans_method($member_id, $amount, $amount_type, $memo, $this->get_admin_id()) == Paycore::PAY_CODE_SUCCESS) {
            echo build_response_str(CODE_SUCCESS, "资金操作成功，请至账单中核对");
        } else {
            echo build_response_str(CODE_DB_FAILED, "转账失败，用户余额不足或系统错误");
        }
    }

    // 商家充值校正
    public function recharge_correction()
    {
        $this->Data['member_id'] = $this->input->get('member_id', TRUE);
        $this->Data['TargetPage'] = 'member/recharge_correction';
        $this->load->view('frame_main', $this->Data);
    }

    // 商家充值校正 提交
    public function do_recharge_correction()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        $member_id      = $this->input->post('member_id', TRUE);
        $amount         = $this->input->post('amount', TRUE);
        if (empty($member_id) || empty($amount)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $memberInfo = $this->hiltoncore->get_member_info(decode_id($member_id), ['user_type','balance']);
        if (empty($memberInfo) || $memberInfo->user_type != USER_TYPE_SELLER) {
            die(build_response_str(CODE_BAD_REQUEST, '该操作对象不是商家，重新选择操作类型'));
        }

        if ($amount < 0 && $amount + $memberInfo->balance < 0) {
            die(build_response_str(CODE_DB_FAILED, '用户余额不足'));
        }

        $memo = '商家金额操作，商家ID：' . $member_id . '，' . $this->input->post('memo', TRUE);
        if ($this->paycore->recharge_correction_trans(decode_id($member_id), $amount, $memo, $this->get_admin_id()) == Paycore::PAY_CODE_SUCCESS) {
            echo build_response_str(CODE_SUCCESS, "资金操作成功，请至账单中核对");
        } else {
            echo build_response_str(CODE_DB_FAILED, "转账失败");
        }
    }


}