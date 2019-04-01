<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Member_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        $params_get = [];
        if ($_GET) {
            $this->Data['i_page'] = $this->input->get('i_page', TRUE);
            $this->Data['member_id'] = $this->input->get('member_id', TRUE);
            $this->Data['user_name'] = $this->input->get('user_name', TRUE);
            $this->Data['regDate'] = $this->input->get('regDate', TRUE);
            $this->Data['auth_status'] = $this->input->get('auth_status', TRUE);
            $this->Data['status'] = $this->input->get('status', TRUE);
            $this->Data['user_type'] = USER_TYPE_BUYER;

            $params_get = $this->Data;
            unset($params_get['member_id'] ,$params_get['real_name'], $params_get['menus']);
            $params_get = $this->_build_query($params_get);

            $this->Data['data'] = $this->hiltoncore->get_member_list($this->Data);
        }
        $this->Data['admin_id'] = $this->get_admin_id();
        $this->Data['TargetPage'] = 'page_members';
        $this->Data['params_get'] = $params_get;
        $this->load->view('frame_main', $this->Data);
    }
    public function freezing()
    {
        $this->Data['member_id']    = $this->input->get('member_id', TRUE);
        $this->Data['member_data']  = $this->hiltoncore->get_member_list(['user_type' => USER_TYPE_BUYER, 'member_id' => $this->Data['member_id']]);
        $this->Data['TargetPage']   = 'page_freezing';
        $this->load->view('frame_main', $this->Data);
    }
    public function update()
    {
        $this->Data['member_id']    = $this->input->get('member_id', TRUE);
        $this->Data['i_page'] = $this->input->get('i_page', TRUE);
        $this->Data['user_name'] = $this->input->get('user_name', TRUE);
        $this->Data['regDate'] = $this->input->get('regDate', TRUE);
        $this->Data['auth_status'] = $this->input->get('auth_status', TRUE);
        $this->Data['status'] = $this->input->get('status', TRUE);

        $params_get = $this->Data;
        unset($params_get['member_id'] ,$params_get['real_name'], $params_get['menus']);
        $params_get = $this->_build_query($params_get);

        $this->Data['member_data']  = $this->hiltoncore->getMemberInfo(
            ['member_id' => $this->Data['member_id'], 'status' => STATUS_PASSED]
        );

        $this->Data['admin_id'] = $this->get_admin_id();
        $this->Data['TargetPage']   = 'page_update_member_info';
        $this->Data['params_get'] = $params_get;
        unset($params_get);
        $this->load->view('frame_main', $this->Data);
    }
    public function update_info(){
        try{
            if (!$this->input->is_ajax_request()) { throw new Exception("非法请求", CODE_BAD_REQUEST); }
            //if (empty($this->input->post('member_id', TRUE)) || !is_numeric($this->input->post('member_id', TRUE)))
            // {
            //      throw new Exception("非法请求", CODE_BAD_REQUEST);  // 此写法在PHP 5.5 以下版本报错
            // }
            $member_id = $this->input->post('member_id', TRUE);
            $user_id   = $this->input->post('user_id', TRUE);
            if (empty($member_id) || !is_numeric($member_id) || $member_id == 0) { throw new Exception("非法请求", CODE_BAD_REQUEST); }
            $info = $this->input->post(['member_id','true_name','id_card_num','qq_num','bank_province','bank_city','bank_county','bank_card_num','bank_name','bank_branch'], TRUE);
            $this->hiltoncore->update_member_cert_infos($info);
            $this->hiltoncore->add_oper_log($user_id, $this->get_admin_id(), $this->get_admin_name(), 3, 1, '修改会员银行卡信息：操作成功');
            echo build_response_str(CODE_SUCCESS, "修改成功");return;
        }catch(Exception $e){
            echo build_response_str($e->getCode(), $e->getMessage());
            return;
        }
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
        }
        //  else if ($act == 'set_freezing_amount') {
        //     $member_id = $this->input->post('member_id', true);
        //     $freezing_amount = $this->input->post('freezing_amount', true);

        //     if (empty($member_id) || !is_numeric($freezing_amount)) {
        //         die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        //     }

        //     if ($this->hiltoncore->freezing_account_balance($member_id, $freezing_amount)) {
        //         echo build_response_str(CODE_SUCCESS, "冻结余额成功");
        //         return;
        //     }
        // }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function freezing_commit(){
        $member_id                      = $this->input->post('member_id', true);
        $freezing_capital_amount        = $this->input->post('freezing_capital_amount', true);
        $freezing_commission_amount     = $this->input->post('freezing_commission_amount', true);

        if (empty($member_id)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }
        if (!is_numeric($freezing_capital_amount) && !is_numeric($freezing_commission_amount)) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        if ($this->hiltoncore->freezing_account_balance(encode_id($member_id), $freezing_capital_amount, $freezing_commission_amount)) {
            echo build_response_str(CODE_SUCCESS, "冻结余额成功");
            return;
        }
    }

    public function channel()
    {
        $this->Data['TargetPage'] = 'member/channel';
        $this->load->view('frame_main', $this->Data);
    }

    public function save()
    {
        echo build_response_str(CODE_SUCCESS, '更新成功');
    }

    private function _build_query($query_data, $encoding = false)
    {
        $res = '';
        $count = count($query_data);
        $i = 0;
        foreach ($query_data as $k => $v) {
            if ($encoding === true) {
                $v = urlencode($v);
            }
            if ($i < $count - 1) {
                $res .= $k . '=' . $v . '&';
            } else {
                $res .= $k . '=' . $v;
            }
            $i++;
        }
        return $res;
    }

}