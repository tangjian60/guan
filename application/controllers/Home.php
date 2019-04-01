<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends Hilton_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->admin_init();
	}

	public function index(){
		$this->Data['TargetPage'] = 'welcome';
		$this->load->view('frame_main', $this->Data);
	}
}