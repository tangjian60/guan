<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {

	public function index(){
		session_destroy();
		redirect( base_url(), 'refresh' );
	}
}