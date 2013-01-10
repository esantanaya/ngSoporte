<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sesion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
	}

	public function login()
	{
		
	}

}

/* End of file sesion.php */
/* Location: ./application/controllers/sesion.php */
