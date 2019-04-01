<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Staff_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        $this->Data['data'] = $this->hiltoncore->get_admin_list();
        $this->Data['TargetPage'] = 'page_staffs';
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

        if ($act == 'disable_account') {
            if ($this->hiltoncore->disable_admin_account($this->input->post('staff_id', true))) {
                echo build_response_str(CODE_SUCCESS, "禁用员工账号成功");
                return;
            }
        } else if ($act == 'reset_account_passwd') {
            if ($this->hiltoncore->reset_admin_passwd($this->input->post('staff_id', true))) {
                echo build_response_str(CODE_SUCCESS, "重置账号密码成功");
                return;
            }
        } else if ($act == 'add_new_staff') {
            $user_name = $this->input->post('user_name', true);
            $real_name = $this->input->post('real_name', true);
            $account_role = $this->input->post('account_role', true);

            if (empty($user_name) || empty($real_name) || empty($account_role)) {
                die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
            }

            if ($this->hiltoncore->add_admin_account($user_name, $real_name, $account_role)) {
                echo build_response_str(CODE_SUCCESS, "添加员工账号成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }

    public function channel()
    {
        $this->Data['TargetPage'] = 'staff/channel';
        $this->load->view('frame_main', $this->Data);
    }

    public function save()
    {
        echo build_response_str(CODE_SUCCESS, '更新成功');
    }
}