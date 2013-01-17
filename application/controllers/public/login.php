<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (is_logged())
		{
			redirect(base_url());
		}
	}

	public function index()
	{
		$data['SYS_MetaTitle'] = 'Login :: Sistema de Tickets';
		$data['mensaje_login'] = 'Ingrese al sistema';

		$this->load->view('public/login_view', $data);
	}

}

/* End of file login.php */
/* Location: ./application/controllers/public/login.php */
