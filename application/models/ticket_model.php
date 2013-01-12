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

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->ticket_id;

			return $data;
		}
		return 1;
	}

	public function get_current_id($ticket_id)
	{
		$this->db->select('ticketID');
		$this->db->where('ticket_id', $ticket_id);
		$query = $this->db->get($this->tablas['ticket']);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->ticketID;

			return $data;
		}

		return null;
	}

	public function create_ticket_usuario()
	{
		$bool = false;

		do 
		{
			$ticketID = rand(100000,999999);
			$this->db->where('ticketID', $ticketID);
			$query = $this->db->get($this->tablas['ticket']);

			if (! $query->num_rows() > 0) $bool = true;
		} 
		while (! $bool);

		return $ticketID;
	}

	public function insert_ticket($ticket, $mensaje)
	{
		$this->db->insert($this->tablas['ticket'], $ticket);
		if ($mensaje != null)
		{
			$this->db->insert($this->tablas['mensaje'], $mensaje);	
		}

		return $this->get_current_id($this->db->insert_id());
	}


	//TODO SEME DERRITE EL CEREBRO ACA!!!
	public function get_elegido($staff)
	{
		$elegido = '';
		$max = 0;
		$min = 0;
		$indice = 1;
		$staff = $staff->result_array();

		foreach ($staff as $row => $miembro) 
		{
			
			$miembro_actual = $miembro['cod_usuario'];
			$this->db->where('cod_staff', $miembro_actual);
			$this->db->select('cod_staff');
			$query = $this->db->get($this->tablas['ticket']);

			if ($query->num_rows() == 0) 
			{
				$elegido = $miembro_actual;
			}
			if ($indice == 1)
			{
				$max = $query->num_rows();
				$min = $query->num_rows();
			}
			if ($query->num_rows() > $max) 
			{
				$max = $query->num_rows();
			}
			if ($query->num_rows() < $min) 
			{
				$min = $query->num_rows();
				$elegido = $miembro_actual;
			}
			if ($query->num_rows() === $min && $query->num_rows() === $max)
			{
				$elegido = $miembro_actual;
			}

			$indice = $indice++;
		}

		return $elegido;
	}
}

/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */
