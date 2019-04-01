<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Spread_relation extends Hilton_Controller
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
            $this->Data['owner_id'] = $this->input->get('owner_id', TRUE);
            $this->Data['promote_id'] = $this->input->get('promote_id', TRUE);
            $this->Data['first_reward'] = $this->input->get('first_reward', TRUE);
            $this->Data['start_time'] = $this->input->get('start_time', TRUE);
            $this->Data['end_time'] = $this->input->get('end_time', TRUE);
            $this->Data['data'] = $this->hiltoncore->get_all_promotion_infos($this->Data);
        }

        $this->Data['TargetPage'] = 'page_spread_relation';
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

        if ($act == 'delete_promotion_relation') {
            if ($this->hiltoncore->delete_promotion_relation($this->input->post('promotion_id', true))) {
                echo build_response_str(CODE_SUCCESS, "操作成功");
                return;
            }
        }

        echo build_response_str(CODE_UNKNOWN_ERROR, "操作失败");
    }
}