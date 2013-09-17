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
		$this->db->where('status !=', 'cerrado');
		$this->db->where('status !=', 'esperando');
		$this->db->where(
			'(CURTIME() BETWEEN entrada_mat AND salida_mat OR CURTIME() 
			BETWEEN entrada_ves AND salida_ves)');
		$this->db->join($this->tablas['usuarios'], 'cod_staff = cod_usuario');
		$this->db->join($this->tablas['empHorarios'], 
			'id_staff = us_usuarios.id_usuario');
		$this->db->join($this->tablas['horarios'], 
			'sop_horarios.id_horario = us_horario.id_horario');
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
			$this->db->where('status !=', 'esperando');			
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
			WHERE A.ticket_id = ' . $ticket_id . ' ORDER BY A.msg_id ASC;');

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
			WHERE A.ticket_id = ' . $ticket_id . ' ORDER BY A.msg_id ASC;');		

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
				$order = 'A.created, EMPRESA';				
				break;			
			case 2:				
				$order = 'TICKETS, A.created';				
				break;			
			case 3:				
				$order = 'ESTADO, A.created';				
				break;						
			default:				
				$order = 'FECHAS, EMPRESA';				
				break;		
		}		

		$cadena_query = 'SELECT CONCAT(\'<a href=" ' . base_url()
			. 'staff/tickets/responde_ticket/\', '				
			. 'A.ticketID,\'">\', 				
			A.ticketID, \'</a>\') AS TICKETS, 				
			SUBSTR(A.created, 1, 10) AS FECHAS, 				
			A.status AS ESTADO, CONCAT(\'<a href=" '. base_url()
			. 'staff/tickets/responde_ticket/\', A.ticketID,\'">\', 		
			A.subject, \'</a>\') AS ASUNTO, 				
			C.nombre_empresa AS EMPRESA, D.nombre_usuario AS STAFF
			FROM tk_ticket A				
			INNER JOIN us_usuarios B ON A.usuario_id = B.id_usuario  
			INNER JOIN sop_empresas C ON B.id_empresa = C.empresa_id
			INNER JOIN us_usuarios D ON A.cod_staff = D.cod_usuario';

		if ($estado != null)		
		{			
			$cadena_query .= ' WHERE status = \'' . $estado . '\'';
		}
		else
		{
			$cadena_query .= ' WHERE status != \'cerrado\'';
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

	public function get_tickets_query($query, $fechaIni, $fechaFin, $order=1, 
		$empresa=null, $estado=null)
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

		$cadena_query = 'SELECT CONCAT(\'<a href=" ' . base_url() 
			. 'staff/tickets/responde_ticket/\', '				
			. 'A.ticketID,\'">\', 				
			A.ticketID, \'</a>\') AS TICKETS, 				
			SUBSTR(A.created, 1, 10) AS FECHAS, 				
			A.status AS ESTADO, CONCAT(\'<a href=" '. base_url()
			. 'staff/tickets/responde_ticket/\', A.ticketID,\'">\',
			A.subject, \'</a>\') AS ASUNTO, 				
			C.nombre_empresa AS EMPRESA, F.nombre_usuario AS STAFF
			FROM tk_ticket A				
			INNER JOIN us_usuarios B ON A.usuario_id = B.id_usuario
			INNER JOIN sop_empresas C ON B.id_empresa = C.empresa_id
			INNER JOIN tk_mensaje D ON A.ticket_id = D.ticket_id
			INNER JOIN tk_respuesta E ON A.ticket_id = E.ticket_id
			INNER JOIN us_usuarios F ON A.cod_staff = F.cod_usuario
			WHERE (';

		if($fechaIni != "" || $fechaFin != "") {
			$cadena_query .= 'A.created BETWEEN \'' . $fechaIni . '\' AND \''
			. $fechaFin . '\') AND(';
		}

		if (isset($empresa) && $empresa != '1001') 
			$cadena_query .= 'B.id_empresa = ' . $empresa . ') AND(';

		if (isset($estado))
			$cadena_query .= 'A.status = \'' . $estado . '\') AND (';

		$cadena_query .= 'A.ticketID LIKE \'%' . $query . '%\' 				
			OR A.subject LIKE \'%' . $query . '%\'
			OR D.message LIKE \'%' . $query . '%\'
			OR E.response LIKE \'%' . $query . '%\')
			GROUP BY A.ticketID';

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

	/*public function get_hitorial_mens_resp($ticket_id)
	{
		
	}*/

	public function get_reporte()
	{
		/*$this->db->select('sop_empresas.nombre_empresa,
			tk_ticket.ticketID,
			tk_ticket.created,
			CONCAT(
				us_usuarios.nombre_usuario,
				' ',
				us_usuarios.apellido_paterno
			) AS Reporta,
			tk_ticket.SUBJECT,
			tk_mensaje.message AS Detalle,
			tk_respuesta.created AS FechaRespuesta,
			tk_respuesta.staff_name,
			tk_ticket.status', false);
		$this->db->join($this->tablas['usuarios'], 
			'tk_ticket.usuario_id = us_usuarios.id_usuario');
		$this->db->join($this->tablas['empresas'], 
			'sop_empresas.empresa_id = us_usuarios.id_empresa');
		$this->db->join($this->tablas['mensaje'], 
			'tk_mensaje.ticket_id = tk_ticket.ticket_id 
			AND tk_mensaje.msg_id = (SELECT MIN(tk_mensaje.msg_id))');
		$this->db->join($this->tablas['respuesta'], 
			'tk_respuesta.ticket_id = tk_ticket.ticket_id');
		$this->db->group_by('sop_empresas.nombre_empresa,
			tk_tickets.ticketID,
			tk_tickets.created,
			Reporta,
			tk_tickets.SUBJECT');
		$this->db->order_by('tk_tickets.created', 'desc');
		$query = $this->db->get($this->tablas['ticket']);*/

		$query = $this->db->query('
			SELECT
				em.nombre_empresa AS EMPRESA,
				tk.ticketID AS TICKET,
				tk.created AS FECHA,
				CONCAT(
					us.nombre_usuario,
					\' \',
					us.apellido_paterno
				) AS REPORTA,
				tk.SUBJECT AS ASUNTO,
				ms.message AS DETALLE,
				rp.created AS FECHA_RESPUESTA,
				rp.staff_name AS SATFF,
				tk.status AS ESTADO
			FROM
				tk_ticket tk
			INNER JOIN us_usuarios us ON (
				tk.usuario_id = us.id_usuario
			)
			INNER JOIN sop_empresas em ON (
				em.empresa_id = us.id_empresa
			)
			INNER JOIN tk_mensaje ms ON (ms.ticket_id = tk.ticket_id AND 
				ms.msg_id = (SELECT MIN(ms.msg_id)))
			INNER JOIN tk_respuesta rp ON (rp.ticket_id = tk.ticket_id)
			GROUP BY
				em.nombre_empresa,
				tk.ticketID,
				tk.created,
				Reporta,
				tk.SUBJECT
			ORDER BY tk.created'
			);

		if($query->num_rows > 0)
			return $query->result_array();

		return null;
	}
}
/* End of file ticket_model.php */
/* Location: ./application/models/ticket_model.php */