<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Principal extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
		{
			redirect(base_url() . 'login');
		}
		if (!is_authorized(array(0, 1, 2), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
		{
			redirect(base_url() . 'login');	
		}
	}

	public function index()
	{
		redirect(base_url());
	}

}

/* End of file principal.php */
/* Location: ./application/controllers/admin/principal.php */