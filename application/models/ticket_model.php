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

	public function insert_respuesta($mensaje)
	{
		$this->db->insert($this->tablas['respuesta'], $mensaje);

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
			$this->db->where('status !=', 'cerrado');
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
				B.nombre_usuario, B.apellido_paterno, A.response,
				A.response_id
				FROM tk_respuesta A
				INNER JOIN us_usuarios B ON A.staff_id = B.id_usuario
				WHERE A.ticket_id = ' . $ticket_id . ' 
				ORDER BY A.msg_id ASC;');

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}

		return null;
	}

	public function get_adjunto_mensaje($ref_id, $ticket_id, $tipo = 'M')
	{
		$this->db->select('file_name');
		$this->db->where('ref_id', $ref_id);
		$this->db->where('ticket_id', $ticket_id);
		$this->db->where('ref_type', $tipo);
		$query = $this->db->get($this->tablas['adjuntos'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->file_name;

			return $data;
		}

		return null;
	}

	public function get_ticket_usuario($usuario_id, $num_order = 1, 
										$estado = null)
	{

		switch ($num_order) 
		{
			case 1:
				$order = 'FECHAS';
				break;

			case 2:
				$order = 'TICKETS';
				break;

			case 3:
				$order = 'ESTADO';
				break;
			
			default:
				$order = 'FECHAS';
				break;
		}
		$cadena_query = 'SELECT CONCAT(\'<a href=" '. base_url() 
				. 'tickets_usuario/entra_edita_ticket/\', A.ticketID,\'">\', 
				A.ticketID, \'</a>\') AS TICKETS, 
				SUBSTR(A.created, 1, 10) AS FECHAS, 
				A.status AS ESTADO, CONCAT(\'<a href=" '. base_url() 
				. 'tickets_usuario/entra_edita_ticket/\', A.ticketID,\'">\', 
				A.subject, \'</a>\') AS ASUNTO, 
				CONCAT(B.nombre_usuario, \' \', B.apellido_paterno) AS STAFF
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON A.cod_staff = B.cod_usuario
				INNER JOIN us_usuarios C ON A.usuario_id = C.id_usuario
				WHERE C.id_empresa = (SELECT id_empresa FROM us_usuarios 
				WHERE id_usuario = ' . $usuario_id . ')';
		
		if ($estado != null)
		{
			$cadena_query .= ' AND status = \'' . $estado . '\'';
			if ($estado == 'cerrado')
				$cadena_query .= ' AND updated >= ADDDATE(CURDATE(), -7)';
		}
		else
		{
			$cadena_query .= ' AND (updated >= ADDDATE( CURDATE( ) , -7 )
						   AND STATUS = \'cerrado\'';
			$cadena_query .= ' OR status <> \'cerrado\')';
		}

		$cadena_query .= ' ORDER BY ' . $order;

		$query = $this->db->query($cadena_query);

		if ($query->num_rows() > 0)
		{
			return $query;
		}
		return null;
	}

	public function cambia_estado_ticket($ticketID, $estado)
	{
		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
		$date_string = mdate($date_string, $time);
		$data = array('status' => $estado,
					  'updated' => $date_string);
		$this->db->where('ticketID', $ticketID);
		$this->db->update($this->tablas['ticket'], $data);
	}

	public function get_ticketID_empresa($ticketID)
	{
		$query = $this->db->query('SELECT C.empresa_id AS empresa_id
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON (A.usuario_id = B.id_usuario)
				INNER JOIN sop_empresas C ON (B.id_empresa = C.empresa_id)
				WHERE A.ticketId = ' . $ticketID);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->empresa_id;
			return $data;
		}

		return null;
	}

	public function get_listado_staff($num_order = 1, $estado = 'abierto', 
									  $cod_usuario = null)
	{
		switch ($num_order) 
		{
			case 1:
				$order = 'FECHAS';
				break;

			case 2:
				$order = 'TICKETS';
				break;

			case 3:
				$order = 'ESTADO';
				break;
			
			default:
				$order = 'FECHAS';
				break;
		}

		$cadena_query = 'SELECT CONCAT(\'<input type="checkbox" ' 
				. 'name="ticket" value="\', A.ticketID, \'">\') AS \'\', ' 
				. 'CONCAT(\'<a href=" ' . base_url() 
				. 'staff/tickets/responde_ticket/\', '
				. 'A.ticketID,\'">\', 
				A.ticketID, \'</a>\') AS TICKETS, 
				SUBSTR(A.created, 1, 10) AS FECHAS, 
				A.status AS ESTADO, CONCAT(\'<a href=" '. base_url() 
				. 'staff/tickets/responde_ticket/\', A.ticketID,\'">\', 
				A.subject, \'</a>\') AS ASUNTO, 
				C.nombre_empresa AS EMPRESA
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON A.usuario_id = B.id_usuario
				INNER JOIN sop_empresas C ON B.id_empresa = C.empresa_id';

		if ($estado != null)
		{
			$cadena_query .= ' WHERE status = \'' . $estado . '\'';
			/*if ($estado == 'cerrado')
				$cadena_query .= ' AND updated >= ADDDATE(CURDATE(), -7)';*/
		}

		if ($cod_usuario != null AND $estado == null)
		{
			$cadena_query .= ' WHERE cod_usuario = ' . $cod_usuario;
		}
		elseif ($cod_usuario != null)
		{
			$cadena_query .= ' AND cod_staff = \'' . $cod_usuario . '\'';	
		}	

		$cadena_query .= ' ORDER BY ' . $order;

		$query = $this->db->query($cadena_query);

		if ($query->num_rows() > 0)
		{
			return $query;
		}
		return null;
	}

	public function get_vista_asigna($ticketID)
	{
		$query = $this->db->query('SELECT C.nombre_empresa, A.lastresponse, 
					A.duedate, B.nombre_usuario, B.apellido_paterno, 
					B.email_usuario, B.tel_usuario, A.subject
					FROM tk_ticket A
					INNER JOIN us_usuarios B ON A.usuario_id = B.id_usuario
					INNER JOIN sop_empresas C ON B.id_empresa = C.empresa_id
					WHERE A.ticketID = ' . $ticketID . '  LIMIT 1');

		if ($query->num_rows() > 0)
			return $query->result_array();

		return null;
	}

	public function get_msg_id($ticket_id)
	{
		$query = $this->db->query('SELECT MAX(msg_id) AS msg_id
									FROM tk_mensaje
									WHERE ticket_id = ' . $ticket_id);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->msg_id;
			return $data;
		} 

		return null;
	}

	public function get_usuario_ticket($ticketID)
	{
		$this->db->select('usuario_id');
		$this->db->where('ticketID', $ticketID);
		$query = $this->db->get($this->tablas['ticket'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$usuario_id = $row->usuario_id;

			return $usuario_id;
		}

		return null;
	}

	public function get_cod_staff_ticket($ticketID)
	{
		$this->db->select('cod_staff');
		$this->db->where('ticketID', $ticketID);
		$query = $this->db->get($this->tablas['ticket'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$cod_staff = $row->cod_staff;

			return $cod_staff;
		}

		return null;
	}

	public function get_tickets_query($query, $order = 1)
	{
		switch ($num_order) 
		{
			case 1:
				$order = 'FECHAS';
				break;

			case 2:
				$order = 'TICKETS';
				break;

			case 3:
				$order = 'ESTADO';
				break;
			
			default:
				$order = 'FECHAS';
				break;
		}

		$cadena_query = 'SELECT CONCAT(\'<input type="checkbox" ' 
				. 'name="ticket" value="\', A.ticketID, \'">\') AS \'\', ' 
				. 'CONCAT(\'<a href=" ' . base_url() 
				. 'staff/tickets/responde_ticket/\', '
				. 'A.ticketID,\'">\', 
				A.ticketID, \'</a>\') AS TICKETS, 
				SUBSTR(A.created, 1, 10) AS FECHAS, 
				A.status AS ESTADO, CONCAT(\'<a href=" '. base_url() 
				. 'staff/tickets/responde_ticket/\', A.ticketID,\'">\', 
				A.subject, \'</a>\') AS ASUNTO, 
				C.nombre_empresa AS EMPRESA
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON A.usuario_id = B.id_usuario
				INNER JOIN sop_empresas C ON B.id_empresa = C.empresa_id
				WHERE A.ticketID LIKE \'%' . $query . '%\' 
				OR A.subject LIKE \'%' . $query . '%\'';

		$cadena_query .= ' ORDER BY ' . $order;

		$query = $this->db->query($cadena_query);

		if ($query->num_rows() > 0)
		{
			return $query;
		}
		return null;
	}

	public function get_tickets_query_usuario($query, $usuario_id, $order = 1)
	{
		switch ($num_order) 
		{
			case 1:
				$order = 'FECHAS';
				break;

			case 2:
				$order = 'TICKETS';
				break;

			case 3:
				$order = 'ESTADO';
				break;
			
			default:
				$order = 'FECHAS';
				break;
		}

		$cadena_query = 'SELECT CONCAT(\'<a href=" '. base_url() 
				. 'tickets_usuario/entra_edita_ticket/\', A.ticketID,\'">\', 
				A.ticketID, \'</a>\') AS TICKETS, 
				SUBSTR(A.created, 1, 10) AS FECHAS, 
				A.status AS ESTADO, CONCAT(\'<a href=" '. base_url() 
				. 'tickets_usuario/entra_edita_ticket/\', A.ticketID,\'">\', 
				A.subject, \'</a>\') AS ASUNTO, 
				CONCAT(B.nombre_usuario, \' \', B.apellido_paterno) AS STAFF
				FROM tk_ticket A
				INNER JOIN us_usuarios B ON A.cod_staff = B.cod_usuario
				INNER JOIN us_usuarios C ON A.usuario_id = C.id_usuario
				WHERE C.id_empresa = (SELECT id_empresa FROM us_usuarios 
				WHERE id_usuario = ' . $usuario_id . ') 
				AND (A.ticketID LIKE \'%' . $query . '%\' 
				OR A.subject LIKE \'%' . $query . '%\')';

		$cadena_query .= ' ORDER BY ' . $order;

		$query = $this->db->query($cadena_query);

		if ($query->num_rows() > 0)
		{
			return $query;
		}
		return null;
	}

	public function reasigna_ticket($ticketID, $cod_usuario)
	{
		$data = array('cod_staff' => $cod_usuario);
		$this->db->where('ticketID', $ticketID);
		$this->db->update($this->tablas['ticket'], $data);
	}

	public function insert_bitacora_asignacion($data)
	{
		$this->db->insert($this->tablas['asignaciones'], $data);

		return $this->db->insert_id();
	}
	public function get_Allticket_ticketID($ticketID)
	{
		$this->db->where('ticketID', $ticketID);
		$query = $this->db->get($this->tablas['ticket'], 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row();

			return $row;
		}

		return null;
	}
}

/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */
