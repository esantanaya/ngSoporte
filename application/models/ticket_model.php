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

	public function insert_adjunto($data)
	{
		$this->db->insert($this->tablas['adjuntos'], $data);

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

	public function get_ticket_ticketID($ticketID)
	{
		$this->db->where('ticketID', $ticketID);
		$query = $this->db->get($this->tablas['ticket'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$ticket_id = $row->ticket_id;
			return $ticket_id;
		}
		return null;
	}

	public function get_vista_ticket($ticketID)
	{
		$query = $this->db->query('SELECT A.status, B.dept_name, A.created, 
			C.nombre_usuario, C.apellido_paterno, C.email_usuario, 
			C.tel_usuario, A.subject
			FROM tk_ticket A
			INNER JOIN us_departamentos B ON B.dept_id = A.dept_id
			INNER JOIN us_usuarios C ON C.cod_usuario = A.cod_staff
			WHERE A.ticketID = ' . $ticketID . ' LIMIT 1');
		
		if ($query->num_rows() > 0)
		{
			return $query->result_array();	
		}
		
		return null;
	}

	public function get_historial_mensaje($ticket_id)
	{
		$query = $this->db->query('SELECT A.msg_id, A.created, 
					C.nombre_usuario AS nombre_staff, 
					C.apellido_paterno AS apellido_staff, 
					D.nombre_usuario AS nombre_cliente, 
					D.apellido_paterno AS apellido_cliente,A.message
					FROM tk_mensaje A
					INNER JOIN tk_ticket B ON A.ticket_id = B.ticket_id
					INNER JOIN us_usuarios C ON B.usuario_id = C.id_usuario
					INNER JOIN us_usuarios D ON A.usuario_id = D.id_usuario
					WHERE A.ticket_id = ' . $ticket_id . ' 
					ORDER BY A.msg_id ASC;');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}

		return null;
	}

	public function get_historial_respuesta($ticket_id)
	{
		$query = $this->db->query('SELECT A.msg_id, A.created, 
				C.nombre_usuario, C.apellido_paterno, A.response,
				A.response_id
				FROM tk_respuesta A
				INNER JOIN tk_ticket B ON A.ticket_id = B.ticket_id
				INNER JOIN us_usuarios C ON B.usuario_id = C.id_usuario
				WHERE A.ticket_id = ' . $ticket_id . ' 
				ORDER BY A.msg_id ASC;');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}

		return null;
	}

	public function get_adjunto_mensaje($mensaje_id, $ticket_id)
	{
		$this->db->select('file_name');
		$this->db->where('ref_id', $mensaje_id);
		$this->db->where('ticket_id', $ticket_id);
		$query = $this->db->get($this->tablas['adjuntos'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->file_name;

			return $data;
		}

		return null;
	}

	public function get_ticket_usuario($usuario_id, $order)
	{
		$query = $this->db->query('SELECT A.ticket_id, A.ticketId, A.created, 
				A.status, A.subject, B.nombre_usuario, B.apellido_paterno
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON A.cod_staff = B.cod_usuario
				WHERE usuario_id = ' . $usuario_id . ' ORDER BY ' . $order);
	}
}

/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */
