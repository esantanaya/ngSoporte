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

		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) {
			$select[$valor['dept_id']] = $valor['dept_name'];
		}	

		$data['departamentos'] = $select;

		$this->load->view('public/nuevo_ticket_view', $data);
	}

	public function crea_ticket()
	{
		$this->load->helper('date');
		$date_string = "%Y%m%d %h:%i:%s";
		$time = time();

		$chi['dept_id'] = $this->input->post('departamento');
		$chi['subject'] = $this->input->post('asunto');
		$chi['created'] = mdate($date_string, $time);
		$chi['ticketID'] = $this->ticket_model->create_ticket_usuario();
		$chi['usuario_id'] = $this->session->userdata('idUsuario');
 
		$mensaje['message'] = $this->input->post('mensaje');
		//$mensaje['ticket_id'] = ;

		$miembros_staff = $this->usuario_model->get_miembros_staff();

		switch ($miembros_staff->num_rows()) 
		{
			case 0:
				return 'error'; 
				//TODO MANEJAR ESTE ERROR CUANDO NO ASIGNA USUARIO
				break;

			case 1:
				$row = $miembros_staff->row();
				$data = $row->cod_usuario;

				$chi['cod_staff'] = $data;
				break;
			
			default:
				$chi['cod_staff'] = $this->ticket_model->get_elegido(
									$miembros_staff);
				break;
		}

		$this->ticket_model->insert_ticket($chi, null);
		$this->load->view('public/nuevo_ticket_view', $chi);
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/public/tickets.php */
