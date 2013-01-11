<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tickets extends CI_Controller {

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
		$date_string = "%Y%m%d %h:%i:%s";
		$time = time();

		$ticket['dept_id'] = $this->input->post('departamento');
		$ticket['subject'] = $this->input->post('asunto');
		$ticket['created'] = mdate($date_string, $time);
		$ticket['ticketID'] = $this->ticket_model->create_ticket_usuario();
 
		$mensaje['message'] = $this->input->post('mensaje');
		$mensaje[''];

		$miembros_staff = $this->usuario_model->get_miembros_staff();

		switch ($miembros_staff->num_rows()) 
		{
			case 0:
				return 'error'; 
				//TODO MANEJAR ESTE ERROR CUANDO NO ASIGNA USUARIO
				break;

			case 1:
				$row = $miembros_staff->row();
				$data = $row->cod_staff;

				$ticket['cod_staff'] = $data;
				break;
			
			default:
				$ticket['cod_staff'] = $this->ticket_model->get_elegido(
									$miembros_staff);
				break;
		}
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/public/tickets.php */
