<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
			redirect(base_url() . 'login');

		if (! is_authorized(array(0, 1, 2), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

		$this->load->model('ticket_model');
		$this->load->model('usuario_model');
		$this->load->library('table');
		$this->load->helper('date');
		$this->load->model('file_model');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');
	}

	public function index()
	{
		redirect(base_url() . 'staff/tickets/listado');
	}

	public function listado($estado = null, $cod_usuario = null)
	{
		if ($cod_usuario == null)
		{
			$listado = $this->ticket_model->get_listado_staff(1,$estado);
		} 
		else
		{
			$listado = $this->ticket_model->get_listado_staff(1,$estado, 
									$this->session->userdata('nombreUsuario'));
		}

		if ($listado == null)
		{
			$listado = array('Genere un ticket' => 'Usted no tiene tickets');
			$this->table->add_row($listado);
		}

		$tmpl = array('table_open' => '<table border="0" cellpadding="4"
				cellspacing="0" class="listado_table">');
		$this->table->set_template($tmpl);

		$data['SYS_MetaTitle'] = 'Staff :: Inicio';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel principal';
		$data['subMenu'] = 'staff/submenu_view.php';
		$data['modulo'] = 'staff/tickets_listado_view.php';
		$data['listado'] = $listado;
		$data['error'] = '';

		$this->load->view('staff/main_staff_view', $data);
	}

	public function responde_ticket($ticketID)
	{

		$ticket_id = $this->ticket_model->get_ticket_ticketID($ticketID);
		$mensajes = $this->ticket_model->get_historial_mensaje(
													$ticket_id);
		$respuestas = $this->ticket_model->get_historial_respuesta(
												$ticket_id);

		$arreglo_historial = array();
		$x = 0;
		foreach ($mensajes as $row => $value)
		{
			$mensaje_id = $value['msg_id'];
			$encabezado = $value['created'] . ' ' 
						. $value['nombre_cliente'] . ' ' 
						. $value['apellido_cliente'];
			$mensaje = $value['message'];
			$adjunto_completo = $this->ticket_model->get_adjunto_mensaje(
								$mensaje_id, $ticket_id);
			$adjunto = '';
			if ($adjunto_completo != null)
			{
				$adjunto = '<a href="' . base_url() . 'docs/tickets/' 
						. $adjunto_completo . '">' . substr($adjunto_completo,
						 18) . '</a>';
			}

			$historial['encabezado'] = '<div class="encabezado">' 
										. $encabezado . '</div>';
			if ($adjunto != null)
			{
				$historial['adjunto'] = '<div class="cuerpo">' . $adjunto
										. '</div>';
			}
			else
			{
				$historial['adjunto'] = '';
			}
			$historial['mensaje'] = '<div class="cuerpo">' . $mensaje
										. '</div>';

			$bandera = false;

			foreach ($respuestas as $fila => $valor) 
			{
				$respuesa_mensaje = $valor['msg_id'];
				$adjunto_staff = null;

				if ($respuesa_mensaje == $mensaje_id)
				{
					$respuesta_id = $valor['response_id'];
					$adjunto_completo_staff = $this->ticket_model->
								get_adjunto_mensaje($respuesta_id, $ticket_id,
													'R');
					if ($adjunto_completo_staff != null)
						$adjunto_staff = '<div class="cuerpo"><a href="' 
						. base_url() . 'docs/tickets/' 
						. $adjunto_completo_staff . '">' 
						. substr($adjunto_completo_staff, 18) . '</a></div>';
					
					$encabezado_staff = '<div class="encabezado_staff">' 
										. $valor['created'] . ' ' . $valor[
										'nombre_usuario'] . ' ' . $valor[
										'apellido_paterno'] . '</div>';

					$historial['encabezado_staff'] = $encabezado_staff;
					if ($adjunto_staff != null)
					{
						$historial['adjunto_staff'] = $adjunto_staff;
					}
					else
					{
						$historial['adjunto_staff'] = '';
					}
					$historial['mensaje_staff'] = '<div class="cuerpo">' 
												. $valor['response']
												. '</div>';
					$bandera = true;
				}
				elseif (! $bandera)
				{
					$historial['encabezado_staff'] = '';
					$historial['mensaje_staff'] = '';
					$historial['adjunto_staff'] = '';
					$bandera = false;
				}
			}

			$arreglo_historial[$x] = $historial;
			$x++;
		}

		$resumen_ticket = $this->ticket_model->get_vista_ticket($ticketID);
		$resumen_asigna = $this->ticket_model->get_vista_asigna($ticketID);

		$tmpl = array('table_open' => '<table class="historial_table"
				 cellspacing="0" cellpadding="4" border="0">', );
		$this->table->set_template($tmpl);

		foreach ($arreglo_historial as $key => $value) 
		{
			if ($value['encabezado'])
				$this->table->add_row($value['encabezado']);
			if ($value['adjunto'])
				$this->table->add_row($value['adjunto']);
			if ($value['mensaje'])
				$this->table->add_row($value['mensaje']);
			if ($value['encabezado_staff'])
				$this->table->add_row($value['encabezado_staff']);
			if ($value['adjunto_staff'])
				$this->table->add_row($value['adjunto_staff']);
			if ($value['mensaje_staff'])
				$this->table->add_row($value['mensaje_staff']);
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Estado';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Estado de un ticket';
		$data['subMenu'] = 'staff/submenu_view.php';
		$data['modulo'] = 'staff/tickets_respuesta_view';
		$data['error'] = '';
		if ($error != null)
			$data['error'] = $error;

		$data['ticketID'] = $ticketID;
		$data['estado_ticket'] = $resumen_ticket[0]['status'];
		$data['departamento_ticket'] = $resumen_ticket[0]['dept_name'];
		$data['creacion_ticket'] = $resumen_ticket[0]['created'];
		$data['staff_name'] = $resumen_ticket[0]['nombre_usuario'] . ' ' 
								. $resumen_ticket[0]['apellido_paterno'];
		$data['staff_correo'] = $resumen_ticket[0]['email_usuario'];
		$data['staff_tel'] = $resumen_ticket[0]['tel_usuario'];
		$data['asunto'] = $resumen_ticket[0]['subject'];

		$data['usuario_name'] = $resumen_asigna[0]['nombre_usuario'] . ' '
								. $resumen_asigna[0]['apellido_paterno'];
		$data['usuario_correo'] = $resumen_asigna[0]['email_usuario'];
		$data['usuario_tel'] = $resumen_asigna[0]['tel_usuario'];
		$data['empresa'] = $resumen_asigna[0]['nombre_empresa'];
		$data['fecha_respuesta'] = $resumen_asigna[0]['lastresponse'];
		$data['fecha_vencimiento'] = $resumen_asigna[0]['duedate'];

		$data['tabla'] = $arreglo_historial;

		$acciones = array('1' => 'SELECCIONA UNA ACCION',
						  '2' => 'Abrir',
						  '3' => 'Cerrar',
						  '4' => 'Reasignar');

		$data['acciones'] = $acciones;
		
		$miembros = $this->usuario_model->get_usuarios_nivel(2);

		foreach ($miembros as $key => $value) {
			$lista_miembros[$value['cod_usuario']] = $value['nombre_usuario'];
		}

		$data['miembros'] = $lista_miembros;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function agrega_respuesta()
	{
		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
		$date_string = mdate($date_string, $time);

		$mensaje = $this->input->post('mensaje');
		$ticketID = $this->input->post('ticketID');
		$ticket_id = $this->ticket_model->get_ticket_ticketID($ticketID);

		$envio = false;

		if ($_FILES['adjunto']['name'] != '') 
			$envio = true;

		$info = array('date' => true,
						'random' => true,
						'user_id' => null);

		$archivo = $this->file_model->uploadNonImage('tickets', $info, 
														'adjunto', $envio);

		$this->form_validation->set_rules('mensaje', 'Mensaje', 
			'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (! $this->form_validation->run() || is_array($archivo))
		{
			$arrDatos = $this->ticket_model->get_vista_ticket($ticketID);

			$data['SYS_MetaTitle'] = 'Tickets :: Estado';
			$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
			$data['SYS_metaDescription'] = 'Estado de un ticket';
			$data['modulo'] = 'public/ticket_view';
			if (is_array($archivo))
			{
				$data['error'] = $archivo['error'];
			}
			else
			{
				$data['error'] = '';
			}

			$data['ticketID'] = $ticketID;
			$data['estado_ticket'] = $arrDatos[0]['status'];
			$data['departamento_ticket'] = $arrDatos[0]['dept_name'];
			$data['creacion_ticket'] = $arrDatos[0]['created'];
			$data['staff_name'] = $arrDatos[0]['nombre_usuario'] . ' ' 
									. $arrDatos[0]['apellido_paterno'];
			$data['staff_correo'] = $arrDatos[0]['email_usuario'];
			$data['staff_tel'] = $arrDatos[0]['tel_usuario'];
			$data['asunto'] = $arrDatos[0]['subject'];

			$this->responde_ticket($ticketID, $data['error']);
			return false;
		}

		$msg_id = $this->ticket_model->get_msg_id($ticket_id);

		$respuesta = array('ticket_id' => $ticket_id,
							 'response' => $mensaje,
							 'staff_id' => $this->session->userdata(
							 								'idUsuario'),
							 'staff_name' => 
							 			$this->session->userdata('nombre'),
							 'msg_id' => $msg_id,
							 'created' => $date_string);
		$mensaje_id = $this->ticket_model->insert_respuesta($respuesta);
		$this->ticket_model->cambia_estado_ticket($ticketID, 'esperando');

		if ($envio)
		{
			$arrInsert = array('ticket_id' => $ticket_id, 
						'ref_id' => $mensaje_id,
						'ref_type' => 'R',
						'file_name' => $archivo,
						'file_key' => substr($archivo, 12, 5),
						'created' => $date_string);
			$this->ticket_model->insert_adjunto($arrInsert);
		}

		$usuario_id = $this->ticket_model->get_usuario_ticket($ticketID);
		$this->send_mail_usuario($usuario_id, $ticketID);
		redirect(base_url() . 'staff/tickets/responde_ticket/' . $ticketID);
	}

	public function busqueda()
	{
		$query = $this->input->post('query');
		$listado = $this->ticket_model->get_tickets_query($query);

		if ($listado == null)
		{
			$listado = array('Genere un ticket' => 'Usted no tiene tickets');
			$this->table->add_row($listado);
		}

		$tmpl = array('table_open' => '<table border="0" cellpadding="4"
				cellspacing="0" class="listado_table">');
		$this->table->set_template($tmpl);

		$data['SYS_MetaTitle'] = 'Tickets :: listado';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Listado de tickets';
		$data['subMenu'] = 'staff/submenu_view';
		$data['modulo'] = 'staff/tickets_listado_view';
		$data['listado'] = $listado;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function send_mail_usuario($usuario_id, $ticketID)
	{
		$this->load->model('email_model');
		$arr_nombre = $this->usuario_model->get_usuario_nombre(null, 
						$usuario_id);
		$nombre = $arr_nombre[0]['nombre_usuario'];
		$apellido = $arr_nombre[0]['apellido_paterno'];
		$correo = $this->usuario_model->get_usuario_mail(null, $usuario_id);

		$asunto = 'Sistema de Tickets N&G Ticket #' . $ticketID;

		$mensaje = 	'
					<br/>
					Hola <strong>' . $nombre . '<span> </span> ' . $apellido .
					 ':</strong>
					<br />
					<p>
						Su ticket <strong><a href="' . base_url() 
						. 'tickets_usuario/entra_edita_ticket/' . $ticketID 
						. '">#' . $ticketID 
						. '</a></strong> fue actualizado, verifique!!
					</p>
					<br />
					';

		$enviado = $this->email_model->send_email(null, $correo, $asunto, 
									$mensaje);
		
		return $enviado;
	}

	public function accion_ticket()
	{
		$accion = $this->input->post('acciones');
		$ticketID = $this->input->post('ticketID');
		$staff = $this->input->post('staff');
		
		switch ($accion) 
		{
			case '2':
				$this->ticket_model->cambia_estado_ticket($ticketID, 
														  'abierto');
				break;

			case '3':
				$this->ticket_model->cambia_estado_ticket($ticketID, 
														  'cerrado');
				break;

			case '4':
				$this->ticket_model->reasigna_ticket($ticketID, $staff);
				break;

			default:
				# code...
				break;
		}

		redirect(base_url() . 'staff/tickets/responde_ticket/' . $ticketID);
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/admin/tickets.php */