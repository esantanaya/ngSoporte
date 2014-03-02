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
		$data['js'] = array('jQuery', 'jQueryUI'); 

		$empresas = $this->usuario_model->get_empresas();

		foreach ($empresas as $depas => $valor) 
		{
			$select[$valor['empresa_id']] = $valor['nombre_empresa'];
		}

		$select['1001'] = 'TODAS';
		$data['empresas'] = $select;

		$estados = array(
				'1' => 'TODOS',
				'2' => 'abierto',
				'3' => 'esperando',
				'4' => 'cerrado'
			);

		$data['estados'] = $estados;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function responde_ticket($ticketID, $error = null)
	{
		$ticket_id = $this->ticket_model->get_ticket_ticketID($ticketID);
		
		if ($ticket_id == null)
			redirect(base_url());

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
						$historial['adjunto_staff'] .= $adjunto_staff;
					}
					else
					{
						$historial['adjunto_staff'] .= '';
					}

					$historial['mensaje_staff'] .= '<div class="cuerpo">' 
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

		// $arreglo_historial = array_reverse($arreglo_historial, true);

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
						  '4' => 'Reasignar (usuario)',
						  '5' => 'Reasignar (departamento)');
		$data['acciones'] = $acciones;

		$miembros = $this->usuario_model->get_usuarios_nivel(2);

		foreach ($miembros as $key => $value) 
		{
			if ($this->ticket_model->get_cod_staff_ticket($ticketID) != 
				$value['cod_usuario'])
				$lista_miembros[$value['cod_usuario']] = 
					$value['nombre_usuario'];
		}

		$departamentos = $this->usuario_model->get_departamentos_id();

		foreach ($departamentos as $depas => $valor) 
		{
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$data['miembros'] = $lista_miembros;
		$data['departamentos'] = $select;
		$data['js'] = array('jQuery'); 

		$this->load->view('staff/main_staff_view', $data);
	}

	public function agrega_respuesta()
	{
		$date_string = getFechaActualFormato();
		$mensaje = $this->input->post('mensaje');
		$ticketID = $this->input->post('ticketID');
		$cerrar = $this->input->post('cerrar');
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
			if (is_array($archivo))
			{
				$error = $archivo['error'];
			}
			else
			{
				$error = '';
			}			

			$this->responde_ticket($ticketID, $error);

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

		if ($cerrar == 'cerrar')
			$this->ticket_model->cambia_estado_ticket($ticketID, 'cerrado');
			
		$this->send_mail_usuario($usuario_id, $ticketID, $mensaje);

		redirect(base_url() . 'staff/tickets/responde_ticket/' . $ticketID);
	}

	public function busqueda()
	{
		$query = $this->input->post('query');
		$empresa = $this->input->post('empresas');
		$estado = $this->input->post('estados');
		$fechaIni = $this->input->post('fechaInicial');
		$fechaFin = $this->input->post('fechaFinal');
		$query = str_replace('\'', '', $query);
		$query = str_replace('%', '', $query);
		$query = trim($query);

		switch ($estado) 
		{
			case 2:
				$estado = 'abierto';
				break;
			case 3:
				$estado = 'esperando';
				break;
			case 4:
				$estado = 'cerrado';
				break;
			default:
				$estado = null;
				break;
		}

		if (strlen($query) >= 2)
			$listado = $this->ticket_model->get_tickets_query($query, 
				$fechaIni, $fechaFin, 1, $empresa, $estado);

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

		$empresas = $this->usuario_model->get_empresas();

		foreach ($empresas as $depas => $valor) 
		{
			$select[$valor['empresa_id']] = $valor['nombre_empresa'];
		}

		$select['1001'] = 'TODAS';
		$data['empresas'] = $select;

		$estados = array(
				'1' => 'TODOS',
				'2' => 'abierto',
				'3' => 'esperando',
				'4' => 'cerrado'
			);

		$data['estados'] = $estados;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function send_mail_usuario($usuario_id, $ticketID, $conver=null)
	{
		$this->load->model('email_model');

		$arr_nombre = $this->usuario_model->get_usuario_nombre(null, 

						$usuario_id);

		$nombre = $arr_nombre[0]['nombre_usuario'];

		$apellido = $arr_nombre[0]['apellido_paterno'];

		$correo = $this->usuario_model->get_usuario_mail(null, $usuario_id);

		$nivel = $this->usuario_model->get_nivel_usuario($usuario_id);

		$enviables = $this->ticket_model->get_Allticket_ticketID($ticketID);
		$ticket_asunto = $enviables->subject;
		$ticket_estado = $enviables->status;

		$asunto = 'Sistema de Tickets N&G Ticket #' . $ticketID . ' [' 
			. $ticket_asunto . ']';

		$mensaje = 	'
					<head>
					<style type="text/css">
						#centro{
							text-align: left;
						}
						.encabezado_staff{
							color: white;
							background-color: rgb(49, 49, 49);
						}
						.encabezado{
							color: white;
							background-color: rgb(166, 164, 164);
						}
						.comunicado{
							color: gray;
							font-size: 8.0pt;
							font-family: "Arial", "sans-serif";
							mso-fareast-language:ES-MX;
						}
					</style>
					</head>
					<body>
					<br/>

					Hola <strong>' . $nombre . '<span> </span> ' . $apellido .
					 ':</strong>
					<br />
					<p>
						Su ticket <strong><a href="' . base_url() 
						. 'tickets_usuario/entra_edita_ticket/' . $ticketID 
						. '">#' . $ticketID 
						. '</a></strong> con estado <strong>' . $ticket_estado
						. '</strong> fue actualizado, verifique!!
					</p>
					<br />
					';

		if ($conver != null)
		{
			$ticket_id = $this->ticket_model->get_ticket_ticketID($ticketID);
			$mensajes = $this->ticket_model->get_historial_mensaje($ticket_id);
			$respuestas = $this->ticket_model->get_historial_respuesta(
				$ticket_id);
			$arreglo_historial = array();
			$historial_respuestas = array();
			$x = 0;

			foreach ($mensajes as $row => $value)
			{
				$mensaje_id = $value['msg_id'];
				$encabezado = $value['created'] . ' ' 
							. $value['nombre_cliente'] . ' ' 
							. $value['apellido_cliente'];
				$mensaje_arr = $value['message'];
				$adjunto_completo = $this->ticket_model->get_adjunto_mensaje(
									$mensaje_id, $ticket_id);

				$adjunto = '';

				if ($adjunto_completo != null)
				{
					$adjunto = '<a href="' . base_url() . 'docs/tickets/' 
							. $adjunto_completo . '">' . substr(
							$adjunto_completo,
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

				$historial['mensaje'] = '<div class="cuerpo">' . $mensaje_arr
											. '</div>';

				$bandera = false;

				foreach ($respuestas as $fila => $valor) 
				{
					$respuesa_mensaje = $valor['msg_id'];
					$adjunto_staff = null;
					if ($respuesa_mensaje == $mensaje_id)
					{
						$respuesta_id = $valor['response_id'];
						$historial_respuestas['encabezado_staff'] = 
						'<div class="encabezado_staff">' 
						. $valor['created'] . ' ' . $valor[
						'nombre_usuario'] . ' ' . $valor[
						'apellido_paterno'] . ' ' . '</div>';
						$adjunto_completo_staff = $this->ticket_model->
							get_adjunto_mensaje($respuesta_id, $ticket_id,'R');

						if ($adjunto_completo_staff != null)
							$adjunto_staff = '<div class="cuerpo"><a href="' 
							. base_url() . 'docs/tickets/' 
							. $adjunto_completo_staff . '">' 
							. substr($adjunto_completo_staff, 18) 
							. '</a></div>';
						
						if ($adjunto_staff != null)
						{
							$historial_respuestas['adjunto_staff'] 
							.= $adjunto_staff;
						}
						else
						{
							$historial_respuestas['adjunto_staff'] .= '';
						}

						$historial_respuestas['mensaje_staff'] 
						.= '<div class="cuerpo">' 
						. $valor['response']
						. '</div>';

						$bandera = true;
					}
					elseif (! $bandera)
					{
						$historial_respuestas['encabezado_staff'] = '';
						$historial_respuestas['mensaje_staff'] = '';
						$historial_respuestas['adjunto_staff'] = '';
						$bandera = false;
					}
				}

				$arreglo_historial[$x] = array_merge($historial, 
					$historial_respuestas);
				$x++;
			}
			$resumen_ticket = $this->ticket_model->get_vista_ticket($ticketID);
			$resumen_asigna = $this->ticket_model->get_vista_asigna($ticketID);
			$tmpl = array('table_open' => '<table class="historial_table"
				 	cellspacing="0" cellpadding="4" border="0">', );

			$this->table->set_template($tmpl);

			foreach ($arreglo_historial as $key => $value) 
			{
				if ($value['encabezado_staff'])
					$this->table->add_row($value['encabezado_staff']);
			
				/*if ($value['adjunto_staff'])
	
					$this->table->add_row($value['adjunto_staff']);*/
	
				if ($value['mensaje_staff'])
					$this->table->add_row($conver);

				if ($value['encabezado'])
				$this->table->add_row($value['encabezado']);

				if ($value['adjunto'])
				$this->table->add_row($value['adjunto']);

				if ($value['mensaje'])
					$this->table->add_row($value['mensaje']);
			}
			$mensaje .= '<div id="centro">';
			
			$mensaje .= $this->table->generate();
			$mensaje .= '</div></body>';

			$mensaje .= '<p><strong>Soporte Tecnico</strong></p><br>
<p class="comunicado">El contenido de esta comunicacion electronica no se considera oferta o acuerdo de voluntades, salvo que sea suscrito por firma autografa del representante legal de N&G Systems Development and Consulting S.C. El contenido de esta comunicacion es confidencial para uso exclusivo del destinatario, por lo que se prohibe su divulgacion total o parcial a cualquier tercero no autorizado. Cualquier revision, retransmision, diseminacion, o cualquier otro uso o accion relacionada con esta informacion ya sea por personas o entidades distintas a los recipientes a los que ha sido dirigida, esta prohibida. Si usted recibe esta informacion por error, favor de contactar al remitente y borrar dicho material de cualquier computadora en la que se encuentre.</p><p class="comunicado">
The content of this electronic communication is not to be considered as an offer, proposal, or agreement unless it is confirmed in a document duly signed by N&G Systems Development and Consulting S.C. legal representative. The content of this communication is confidential and for the exclusive use of the address. Its total or partial disclosure to any unauthorized third party is strictly forbidden. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer</p>
';
		}

		if ($nivel == 3)
		{
			$empresa = $this->usuario_model->get_empresa($usuario_id);

			$copia = $this->usuario_model->get_correo_empresa($empresa);

			$enviado = $this->email_model->send_email(null, /*'esantana@nygconsulting.com.mx'*/$correo, $asunto, 

											$mensaje, $copia);
			return $enviado;

		}

		$enviado = $this->email_model->send_email(null, /*'esantana@nygconsulting.com.mx'*/$correo, $asunto, $mensaje);

		

		return $enviado;
	}

	public function accion_ticket()
	{
		$date_string = getFechaActualFormato();
		$accion = $this->input->post('acciones');
		$ticketID = $this->input->post('ticketID');
		$ticket_id = $this->ticket_model->get_ticket_ticketID($ticketID);
		$status =
			$this->ticket_model->get_Allticket_ticketID($ticketID)->status;
		$usuario_id = $this->ticket_model->get_usuario_ticket($ticketID);
		$staff = $this->input->post('staff');
		$dept = $this->input->post('depts');

		switch ($accion) 
		{
			case '2':
				$this->ticket_model->cambia_estado_ticket($ticketID, 
														  'abierto');
				$this->send_mail_usuario($usuario_id, $ticketID);
				break;
			case '3':
				$this->ticket_model->cambia_estado_ticket($ticketID, 
														  'cerrado');
				$enviado = $this->send_mail_usuario($usuario_id, $ticketID);
				break;
			case '4':
				$reasignacion = array('id_ticket' => $ticket_id,
									  'cod_usuario' => $staff,
									  'status' => $status,
									  'realizado' => $date_string);
				$this->ticket_model->reasigna_ticket($ticketID, $staff);
				$this->ticket_model->insert_bitacora_asignacion($reasignacion);
				$this->send_mail_staff($staff, $usuario_id, $ticketID);
				break;
			case '5':
				$staff = $this->ticket_model->get_elegido($dept);
				$reasignacion = array('id_ticket' => $ticket_id,
									  'cod_usuario' => $staff,
									  'status' => $status,
									  'realizado' => $date_string);
				$this->ticket_model->reasigna_ticket($ticketID, $staff);
				$this->ticket_model->insert_bitacora_asignacion($reasignacion);
				$this->send_mail_staff($staff, $usuario_id, $ticketID);
				break;
			default:
				# code...
				break;
		}

		redirect(base_url() . 'staff/tickets/responde_ticket/' . $ticketID);
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
}

/* End of file tickets.php */

/* Location: ./application/controllers/admin/tickets.php */