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

	public function insert_ticket($ticket)
	{
		$this->db->insert($this->tablas['ticket'], $ticket);

		return $this->db->insert_id();
	}

	public function insert_mensaje($mensaje)
	{
		$this->db->insert($this->tablas['mensaje'], $mensaje);

		return $this->db->insert_id();
	}

	public function get_tickets_staff($dept)
	{
		$this->db->select('cod_staff, count(cod_staff) tickets');
		$this->db->where('dept_id', $dept);
		$this->db->where('id_departamento_usuario', $dept);
		$this->db->join($this->tablas['usuarios'], 'cod_staff = cod_usuario');
		$this->db->group_by('cod_staff');
		$this->db->order_by('tickets', 'desc');
		$query = $this->db->get($this->tablas['ticket']);

		return $query->result();
	}

	public function get_elegido($dept)
	{
		
		$this->load->model('usuario_model');
		$miembros = $this->usuario_model->get_miembros_staff($dept);
		$miembros = $miembros->result_array();
		foreach ($miembros as $miembro) 
		{
			$miembro_actual = $miembro['cod_usuario'];
			$this->db->where('cod_staff', $miembro_actual);
			$this->db->select('cod_staff');
			$query = $this->db->get($this->tablas['ticket']);

			if ($query->num_rows() == 0) 
			{
				$elegido = $miembro_actual;
				return $elegido;
			}
		}

		$miembros = $this->get_tickets_staff($dept);
		$elegido = end($miembros);
		$elegido = $elegido->cod_staff;

		return $elegido;
	}
}

/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */
