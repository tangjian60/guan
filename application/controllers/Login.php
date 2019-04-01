<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->is_admin_login()) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_login';
        $this->Data['redirect_page'] = $this->input->get('redirect', TRUE);
        $this->load->view('frame_login', $this->Data);
    }

    public function login_handler()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->helper('security');

        $log_user_name = $this->input->post('user_account', TRUE);
        $log_user_passwd = $this->input->post('user_passwd', TRUE);

        if (empty($log_user_name) || empty($log_user_passwd)) {
            die(build_response_str(CODE_BAD_PASSWORD, '用户名或密码错误'));
        }

        $result = $this->hiltoncore->manage_login_verify($log_user_name, do_hash($log_user_passwd, 'sha1'));

        if (empty($result) || !$result['result']) {
            die(build_response_str(CODE_BAD_PASSWORD, '用户名或密码错误'));
        }

        if ($result['status'] != 1) {
            die(build_response_str(CODE_BANED, '该用户已经被禁止登录'));
        }

        $this->session->set_userdata(SESSION_MANAGER_ID, $result['id']);
        $this->session->set_userdata(SESSION_MANAGER_NAME, $result['realname']);
        if ($result['role'] == EMPLOYEE_ROLE_BOSS) {
            $this->session->set_userdata(SESSION_IS_BOSS, true);
        } else {
            $this->session->set_userdata(SESSION_IS_BOSS, false);
            // set no admin user permissions
            $this->load->library('permissions');
            $this->permissions->setPermissions($result['id'], $this->hiltoncore->get_manage_permissions($result['id']));
        }

        echo build_response_str(CODE_SUCCESS, '登录成功');
    }
}