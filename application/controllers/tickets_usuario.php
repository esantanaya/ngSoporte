<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets_usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
		{
			redirect(base_url() . 'login');
		}

		$this->load->model('ticket_model');
		$this->load->model('usuario_model');
		$this->load->model('file_model');
		$this->load->helper('date');
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
		$data['SYS_MetaTitle'] = 'Tickets :: Nuevo';
		$data['SYS_metaKeyWords'] = 'nuevo ticket';
		$data['SYS_metaDescription'] = 'Generar nuevo ticket';
		$data['modulo'] = 'public/nuevo_ticket_view';
		$data['error'] = '';

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
				$data = $row->cod_usuario;

				$ticket['cod_staff'] = $data;
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

		$this->ticket_model->insert_mensaje($mensaje);

		if ($envio)
		{
			$arrInsert = array('ticket_id' => $ticket_id, 
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
			
			$arrDatos = $this->ticket_model->get_vista_ticket($ticketID);

			$data['SYS_MetaTitle'] = 'Tickets :: Estado';
			$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
			$data['SYS_metaDescription'] = 'Estado de un ticket';
			$data['modulo'] = 'public/ticket_view';
			$data['error'] = '';

			$data['ticketID'] = $ticketID;
			$data['estado_ticket'] = $arrDatos[0]['status'];
			$data['departamento_ticket'] = $arrDatos[0]['dept_name'];
			$data['creacion_ticket'] = $arrDatos[0]['created'];
			$data['staff_name'] = $arrDatos[0]['nombre_usuario'] . ' ' 
									. $arrDatos[0]['apellido_paterno'];
			$data['staff_correo'] = $arrDatos[0]['email_usuario'];
			$data['staff_tel'] = $arrDatos[0]['tel_usuario'];
			$data['asunto'] = $arrDatos[0]['subject'];

			$this->load->view('public/main_tickets_view', $data);	
		}
	}

	public function send_mail_usuario($usuario_id, $nombre_staff, $ticketID)
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
						Tu tucket <strong>#' . $ticketID . '</strong> fue asignado al Ingeniero de Soporte <strong>'
						 . $nombre_staff .'</strong>
					</p>
					<br />
					';

		$enviado = $this->email_model->send_email(null, $correo, $asunto, 
									$mensaje);
		
		return $enviado;
	}

	public function send_mail_staff($cod_staff, $usuario_id, $ticketID)
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
						Tienes un nuevo ticket con el ID: <strong>'
						 . $ticketID . '</strong> asignado por <strong>'
						 . $nombre_cliente . '<span> </span> '
						 . $apellido_cliente . '</strong> por favor revisa los
						  detalles en el Sistema
					</p>
					';

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

		//TODO Arreglar esto
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

			$this->load->view('public/main_tickets_view', $data);
			return false;
		}

		$respuesta = array('ticket_id' => $ticket_id,
							 'message' => $mensaje,
							 'created' => $date_string);

		$this->ticket_model->insert_mensaje($respuesta);


	}
}

/* End of file tickets_usuario.php */
/* Location: ./application/controllers/tickets_usuario.php */
