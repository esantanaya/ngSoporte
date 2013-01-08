<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ticket_model extends CI_Model {

	var $tablas = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->config('tables', true);
		$this->tablas = $this->config->item('tablas', 'tables');
	}

	public function get_current_ticket()
	{
		$this->db->select_max('ticket_id');
		$query = $this->db->get($this->tablas['ticket']);

		if ($query->num_rows() == 0)
		{
			return 1;
		}
		return $query;
	}

	public function insert_ticket_usuario($valores)
	{
		$ticketID = rand(100000,999999);
		$data = array(
				'' => , );
		$this->db->insert($this->tablas['ticket'], $object);
	}

}

/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */
