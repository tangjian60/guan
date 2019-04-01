<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Notice_edit extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        $this->Data['notice_id'] = $this->input->get('notice_id', TRUE);
        if (!empty($this->Data['notice_id'])) {
            $this->Data['notice_info'] = $this->hiltoncore->get_notice_info($this->Data['notice_id']);
        }

        if ($_POST) {
            $this->Data['notice_id'] = $this->input->post('notice_id', TRUE);
            $this->Data['title'] = $this->input->post('title', TRUE);
            $this->Data['notice_type'] = $this->input->post('notice_type', TRUE);
            $this->Data['content'] = $this->input->post('content', TRUE);
            $this->Data['expire_time'] = $this->input->post('expire_time', TRUE);
            $this->Data['sort'] = $this->input->post('sort', TRUE);
            $this->Data['oper_id'] = $this->get_admin_id();

            if (empty($this->Data['notice_id'])) {
                $this->hiltoncore->add_new_notice($this->Data);
            } else {
                $this->hiltoncore->update_notice_info($this->Data);
            }

            redirect(base_url('notice'), 'refresh');
        }

        $this->Data['TargetPage'] = 'page_notice_edit';
        $this->load->view('frame_main', $this->Data);
    }
}