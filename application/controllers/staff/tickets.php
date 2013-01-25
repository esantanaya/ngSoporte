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
		$this->load->library('table');
	}

	public function index()
	{
		redirect(base_url() . 'staff/tickets/listado');
	}

	public function listado($estado = null, $atrasado = false)
	{
		if ($estado != null)
		{
			$listado = $this->ticket_model->get_listado_staff(1,$estado);
		}
		else
		{
			$listado = $this->ticket_model->get_listado_staff();
			if ($atrasado)
				$listado = $this->ticket_model->get_listado_staff(null, null, 
																  true);
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

	public function atrasa()
	{
		//TODO arregla esto!
		$arreglo = array($this->input->post('ticket'));
		var_dump($arreglo);
		foreach ($variable as $key => $value) {
			# code...
		}
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

		$this->load->view('staff/main_staff_view', $data);
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/admin/tickets.php */