<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authority_manage extends Hilton_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->admin_init();
    }

    public function index()
    {
        $this->Data['admin_id'] = $this->input->get('admin_id', TRUE);

        if (empty($this->Data['admin_id']) || !is_numeric($this->Data['admin_id'])) {
            $this->Data['TargetPage'] = 'forbidden';
            $this->load->view('frame_main', $this->Data);
            return;
        }

        $per_values = $this->input->post('p_value', TRUE);

        if (!empty($per_values) && is_array($per_values)) {
            $this->hiltoncore->set_account_permissions($this->Data['admin_id'], $per_values);
        }

        $this->Data['data'] = $this->hiltoncore->get_manage_permissions($this->Data['admin_id']);
        $this->Data['TargetPage'] = 'page_auth_config';
        $this->load->view('frame_main', $this->Data);
    }
}