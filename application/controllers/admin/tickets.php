<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
		{
			redirect(base_url() . 'login');
		}
	}
	public function index()
	{
		
	}

}

/* End of file tickets.php */
/* Location: ./application/controllers/admin/tickets.php */