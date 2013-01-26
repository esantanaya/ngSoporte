<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets_usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if (! is_logged())
			redirect(base_url() . 'login');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

		$this->load->model('ticket_model');
		$this->load->model('usuario_model');
		$this->load->model('file_model');
		$this->load->helper('date');
		$this->load->library('table');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');

		if ($this->session->userdata('nivel') < 3)
			redirect(base_url() . 'staff/tickets');
	}

	public function index()
	{
		$data['SYS_MetaTitle'] = 'Tickets :: Inicio';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel principal';
		$data['modulo'] = 'public/ticket_inicio_view';
		$data['error'] = '';

		$this->load->view('public/main_tickets_view', $data);
	}

	public function nuevo()
	{
		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) {
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Nuevo';
		$data['SYS_metaKeyWords'] = 'nuevo ticket';
		$data['SYS_metaDescription'] = 'Generar nuevo ticket';
		$data['modulo'] = 'public/nuevo_ticket_view';
		$data['error'] = '';
		$data['select'] = $select;

		$this->load->view('public/main_tickets_view', $data);
	}

	public function crea_ticket()
	{

		$envio = false;

		if ($_FILES['adjunto']['name'] != '') 
			$envio = true;

		$info = array('date' => true,
					'random' => true,
					'user_id' => null);

		$archivo = $this->file_model->uploadNonImage('tickets', $info, 
														'adjunto', $envio);

		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
		$dept = $this->input->post('departamento');
		$date_string = mdate($date_string, $time);
		$usuario_id = $this->session->userdata('idUsuario');

		$this->form_validation->set_rules('asunto', 'Asunto', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('mensaje', 
			'Mensaje', 'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		$ticket['dept_id'] = $dept;
		$ticket['subject'] = $this->input->post('asunto');
		$ticket['created'] = $date_string;
		$ticket['lastmessage'] = $date_string;
		$ticket['ticketID'] = $this->ticket_model->create_ticket_usuario();
		$ticket['usuario_id'] = $usuario_id;

		$miembros_staff = $this->usuario_model->
							get_miembros_staff($dept);

		switch ($miembros_staff->num_rows()) 
		{
			case 0:
				return 'error'; 
				//TODO MANEJAR ESTE ERROR CUANDO NO ASIGNA USUARIO
				break;

			case 1:
				$row = $miembros_staff->row();
				$otro = $row->cod_usuario;

				$ticket['cod_staff'] = $otro;
				break;
			
			default:
				$ticket['cod_staff'] = $this->ticket_model->get_elegido($dept);
				break;
		}

		if (! $this->form_validation->run() || is_array($archivo))
		{
			$data['SYS_MetaTitle'] = 'Tickets :: Nuevo';
			$data['SYS_metaKeyWords'] = 'nuevo ticket';
			$data['SYS_metaDescription'] = 'Generar nuevo ticket';
			$data['modulo'] = 'public/nuevo_ticket_view';
			
			if (is_array($archivo))
			{
				$data['error'] = $archivo['error'];
			}
			else
			{
				$data['error'] = '';
			}

			$this->load->view('public/main_tickets_view', $data);
			return false;
		}

		$ticket_id = $this->ticket_model->insert_ticket($ticket);
		$ticketID = $this->ticket_model->get_current_id($ticket_id);

		$mensaje['message'] = $this->input->post('mensaje');
		$mensaje['ticket_id'] = $ticket_id;
		$mensaje['created'] = $date_string;
		$mensaje['usuario_id'] = $this->session->userdata('idUsuario');

		$mensaje_id = $this->ticket_model->insert_mensaje($mensaje);
		$this->ticket_model->cambia_estado_ticket($ticketID, 'abierto');

		if ($envio)
		{
			$arrInsert = array('ticket_id' => $ticket_id, 
						'ref_id' => $mensaje_id,
						'file_name' => $archivo,
						'file_key' => substr($archivo, 12, 5),
						'created' => $date_string);
			$this->ticket_model->insert_adjunto($arrInsert);
		}
		
		$arreglo_staff = $this->usuario_model->get_usuario_nombre(
							$ticket['cod_staff']);
		$nombre_staff = $arreglo_staff[0]['nombre_usuario'] . ' ' .
						$arreglo_staff[0]['apellido_paterno'];
		
		$data['SYS_MetaTitle'] = 'Tickets :: Confirmaci&oacute;n';
		$data['SYS_metaKeyWords'] = 'nuevo ticket';
		$data['SYS_metaDescription'] = 'Generar nuevo ticket';
		$data['modulo'] = 'public/ticket_ok_view';
		$data['ticketID'] = $ticketID;
		$data['name_staff'] = $nombre_staff;

		$envio_correcto = $this->send_mail_usuario($usuario_id, $nombre_staff, 
													$ticketID);
		$this->send_mail_staff($ticket['cod_staff'], $usuario_id, $ticketID);
		if ($envio_correcto)
		{
			$data['mensaje_mail'] = 'Se le envi&oacute; un correo con esta
			 						 informaci&oacute;n';
		}
		else
		{
			$data['mensaje_mail'] = 'No se pudo enviar el correo, conserve la
									 informaci&oacute;n';	
		}
		$this->load->view('public/main_tickets_view', $data);
		return true;
	}

	public function edita_ticket()
	{

		$ticketID = $this->input->post('ticketID');
		$existe_ticket = $this->ticket_model->get_ticket_ticketID($ticketID);

		$this->form_validation->set_rules('ticketID', 'Ticket', 
											 'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (! $this->form_validation->run() || $existe_ticket == null)
		{
			$data['SYS_MetaTitle'] = 'Tickets :: Inicio';
			$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
			$data['SYS_metaDescription'] = 'Panel principal';
			$data['modulo'] = 'public/ticket_inicio_view';
			$data['error'] = '';
			if ($existe_ticket == null && $this->form_validation->run())
				$data['error'] = '<span class="error">Este n&uacute;mero de 
									ticket no existe</span>';

			$this->load->view('public/main_tickets_view', $data);
		}
		else
		{

			$this->entra_edita_ticket($ticketID);
		}
	}

	public function send_mail_usuario($usuario_id, $nombre_staff, $ticketID, 
									  $respuesta = false)
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
						Tu ticket <strong><a href="' . base_url() 
						. 'tickets_usuario/entra_edita_ticket/' . $ticketID 
						. '">#' . $ticketID 
						. '</a></strong> fue asignado al Ingeniero de Soporte 
						<strong>'
						 . $nombre_staff .'</strong>
					</p>
					<br />
					';

		if ($respuesta)
		{
			$mensaje = 	'
						<br>
						Hola <strong>' . $nombre . '</strong> <span> </span>'
						. $apellido . ':
						<br />
						<p>
							Su ticket ' . $ticketID . ' actualizado ha sido
							enviado correctamente!!
						</p>
						';
		}

		$enviado = $this->email_model->send_email(null, $correo, $asunto, 
									$mensaje);
		
		return $enviado;
	}

	public function send_mail_staff($cod_staff, $usuario_id, $ticketID, 
									$respuesta = false)
	{
		$this->load->model('email_model');
		$arr_nombre = $this->usuario_model->get_usuario_nombre($cod_staff);
		$nombre = $arr_nombre[0]['nombre_usuario'];
		$apellido = $arr_nombre[0]['apellido_paterno'];
		$correo = $this->usuario_model->get_usuario_mail($cod_staff);
		$array_cliente = $this->usuario_model->get_usuario_nombre(null,
						 $usuario_id);
		$nombre_cliente = $array_cliente[0]['nombre_usuario'];
		$apellido_cliente = $array_cliente[0]['apellido_paterno'];

		$asunto = 'Sistema de Tickets N&G Ticket #' . $ticketID;

		$mensaje = 	'
					<br/>
					Hola <strong>' . $nombre . '<span> </span> ' . $apellido .
					 ':</strong>
					<br />
					<p>
						Tienes un nuevo ticket con el ID: <a href="' 
						. base_url() . 'staff/tickets/responde_ticket/' 
						. $ticketID . '">'
						. $ticketID . '</a> asignado por <strong>'
						. $nombre_cliente . '<span> </span> '
						. $apellido_cliente . '</strong> por favor revisa los
						detalles en el Sistema
					</p>
					';
		if ($respuesta)
		{
			$mensaje = '
					<br/>
					Hola <strong>' . $nombre . '<span> </span> ' . $apellido .
					 ':</strong>
					<br />
					<p>
						El ticket con el ID: <a href="' 
						. base_url() . 'staff/tickets/responde_ticket/' 
						. $ticketID . '">'
						. $ticketID . '</a> ya fue contestado por favor
					  	revisa los detalles en el Sistema
					</p>
					';
		}

		$enviado = $this ->email_model->send_email(null, $correo, $asunto, 
												$mensaje);
		return $enviado;
	}

	public function agrega_mensaje()
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

			$this->entra_edita_ticket($ticketID, $data['error']);
			return false;
		}

		$usuario_id = $this->session->userdata('idUsuario');

		$respuesta = array('ticket_id' => $ticket_id,
							 'message' => $mensaje,
							 'usuario_id' => $usuario_id,
							 'created' => $date_string);
		$mensaje_id = $this->ticket_model->insert_mensaje($respuesta);
		$this->ticket_model->cambia_estado_ticket($ticketID, 'abierto');

		if ($envio)
		{
			$arrInsert = array('ticket_id' => $ticket_id, 
						'ref_id' => $mensaje_id,
						'file_name' => $archivo,
						'file_key' => substr($archivo, 12, 5),
						'created' => $date_string);
			$this->ticket_model->insert_adjunto($arrInsert);
		}
		$respuesta = true;
		$cod_staff = $this->ticket_model->get_cod_staff_ticket($ticketID);

		$this->entra_edita_ticket($ticketID);
		$this->send_mail_usuario($usuario_id, $cod_staff, $ticketID, 
								 $respuesta);
		$this->send_mail_staff( $cod_staff, $usuario_id, $ticketID, 
								 $respuesta);
	}

	public function lista_ticket($estado = null)
	{
		$usuario_id = $this->session->userdata('idUsuario');

		if ($estado != null)
		{
			$listado = $this->ticket_model->get_ticket_usuario(
												$usuario_id,null,$estado);
		}
		else
		{
			$listado = $this->ticket_model->get_ticket_usuario(
												$usuario_id);
		}

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
		$data['modulo'] = 'public/ticket_lista_view';
		$data['listado'] = $listado;

		$this->load->view('public/main_tickets_view', $data);
	}

	public function reabre($ticketID)
	{
		$this->ticket_model->cambia_estado_ticket($ticketID, 'abierto');
		$this->entra_edita_ticket($ticketID);
	}

	public function entra_edita_ticket($ticketID, $error = null)
	{
		$id_empresa = $this->usuario_model->get_empresa($this->session->
											userdata('idUsuario'));
		$id_ticketID = $this->ticket_model->get_ticketID_empresa($ticketID);
		
		if (! is_empresa($ticketID, $id_empresa, 
			$this->session->userdata('nivel')))
			redirect(base_url());

		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
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

				if ($respuesa_mensaje == $mensaje_id)
				{
					$respuesta_id = $valor['response_id'];
					$adjunto_completo_staff = $this->ticket_model->
								get_adjunto_mensaje($respuesa_id, $ticket_id);
					if ($adjunto_completo_staff != null)
						$adjunto_staff = '<div class="cuerpo"><a href="' . base_url() 
						. 'docs/tickets/' . $adjunto_completo_staff . '">' 
						. substr($adjunto_completo, 18) . '</a></div>';
					
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
		$arrDatos = $this->ticket_model->get_vista_ticket($ticketID);

		$creado = new DateTime($arrDatos[0]['created']);
		$hoy = new DateTime(mdate($date_string, $time));
		$intervalo = date_diff($creado, $hoy);
		$estado = $arrDatos[0]['status'];

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
		$data['modulo'] = 'public/ticket_view';
		$data['error'] = '';
		if ($error != null)
			$data['error'] = $error;

		$data['ticketID'] = $ticketID;
		$data['estado_ticket'] = $estado;
		$data['departamento_ticket'] = $arrDatos[0]['dept_name'];
		$data['creacion_ticket'] = $arrDatos[0]['created'];
		$data['staff_name'] = $arrDatos[0]['nombre_usuario'] . ' ' 
								. $arrDatos[0]['apellido_paterno'];
		$data['staff_correo'] = $arrDatos[0]['email_usuario'];
		$data['staff_tel'] = $arrDatos[0]['tel_usuario'];
		$data['asunto'] = $arrDatos[0]['subject'];
		$data['re-abrir'] = false;
		$data['tabla'] = $arreglo_historial;

		if ($estado == 'cerrado' AND $intervalo->days < 15)
			$data['reabrir'] = true;

		$this->load->view('public/main_tickets_view', $data);
	}
}

/* End of file tickets_usuario.php */
/* Location: ./application/controllers/tickets_usuario.php */
