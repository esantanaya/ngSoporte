?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ticket_model');
	}

	public function index()
	{
		
	}

	public function nuevo()
	{
		# code...
	}

}

/* End of file tickets.php */
/* Location: ./application/controllers/tickets.php */
