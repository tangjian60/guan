<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Changepwd extends Hilton_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        $this->Data['TargetPage'] = 'page_changepwd';
        $this->load->view('frame_main', $this->Data);
    }

    public function change_handler()
    {
        if (!$this->input->is_ajax_request()) {
            die(build_response_str(CODE_BAD_REQUEST, "非法请求"));
        }

        $this->load->helper('security');

        $old_passwd = $this->input->post('old_passwd', TRUE);
        $new_passwd = $this->input->post('new_passwd', TRUE);

        if (empty($old_passwd) || empty($new_passwd)) {
            die(build_response_str(CODE_BAD_PASSWORD, '密码错误'));
        }

        $code = $this->hiltoncore->change_admin_passwd($this->get_admin_id(), do_hash($old_passwd, 'sha1'), do_hash($new_passwd, 'sha1'));

        if ($code == CODE_SUCCESS) {
            echo build_response_str(CODE_SUCCESS, '密码修改成功');
        } elseif ($code == CODE_BAD_PASSWORD) {
            echo build_response_str(CODE_BAD_PASSWORD, '旧密码错误');
        } else {
            echo build_response_str($code, '密码修改失败');
        }
    }
}