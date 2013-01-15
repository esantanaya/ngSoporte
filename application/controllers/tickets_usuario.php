<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets_usuario extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ticket_model');
		$this->load->model('usuario_model');
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

		$this->load->helper('date');
		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
		$dept = $this->input->post('departamento');
		$date_string = (string) mdate($date_string, $time);

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
		$ticket['usuario_id'] = $this->session->userdata('idUsuario');

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

		if (! $this->form_validation->run())
		{
			$data['SYS_MetaTitle'] = 'Tickets :: Nuevo';
			$data['SYS_metaKeyWords'] = 'nuevo ticket';
			$data['SYS_metaDescription'] = 'Generar nuevo ticket';
			$data['modulo'] = 'public/nuevo_ticket_view';
			$data['error'] = '';

			$this->load->view('public/main_tickets_view', $data);
			return false;
		}

		$ticket_id = $this->ticket_model->insert_ticket($ticket);
		$ticketID = $this->ticket_model->get_current_id($ticket_id);

		$mensaje['message'] = $this->input->post('mensaje');
		$mensaje['ticket_id'] = $ticket_id;
		$mensaje['created'] = $date_string;

		$this->ticket_model->insert_mensaje($mensaje);

		$data['SYS_MetaTitle'] = 'Tickets :: Confirmaci&oacute;n';
		$data['SYS_metaKeyWords'] = 'nuevo ticket';
		$data['SYS_metaDescription'] = 'Generar nuevo ticket';
		$data['modulo'] = 'public/ticket_ok_view';
		$data['ticketID'] = $ticketID;
		$data['name_staff'] = $ticket['cod_staff'];

		$this->load->view('public/main_tickets_view', $data);
		return true;
	}
	public function do_upload()
	{
		$config['upload_path'] = base_url() . 'adjuntos/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '100';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());

			$this->load->view('upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

			$this->load->view('upload_success', $data);
		}
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/public/tickets.php */
