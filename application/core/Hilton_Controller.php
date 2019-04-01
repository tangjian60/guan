<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hilton_Controller extends CI_Controller
{
    public $Data = array();

    public function __construct()
    {
        parent::__construct();
    }

    function admin_init()
    {
        // check login status
        if (!$this->is_admin_login()) {
            redirect(base_url('login'), 'refresh');
        }

        // admin libraries
        $this->load->library('authorityenum');
        $this->load->library('permissions');

        // init environment
        $this->Data['real_name'] = $this->get_admin_name();
        $this->Data['menus'] = $this->permissions->getAuthMenus($this->get_admin_id(), $this->is_boss());

        // check permission
        if (!$this->is_boss() &&
            $this->uri->segment(1) &&
            $this->uri->segment(1) != 'forbidden' &&
            $this->uri->segment(1) != 'changepwd' &&
            !$this->permissions->checkPermission($this->get_admin_id(), Authorityenum::get_auth_code($this->uri->segment(1)))) {
            redirect(base_url('forbidden'), 'refresh');
        }
    }

    function is_admin_login()
    {
        if (!$this->session->userdata(SESSION_MANAGER_ID) ||
            !$this->session->userdata(SESSION_MANAGER_NAME)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function get_admin_id()
    {
        return $this->session->userdata(SESSION_MANAGER_ID);
    }

    function get_admin_name()
    {
        return $this->session->userdata(SESSION_MANAGER_NAME);
    }

    function is_boss()
    {
        return $this->session->userdata(SESSION_IS_BOSS) == TRUE;
    }
}