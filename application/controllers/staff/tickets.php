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
		$data['modulo'] = 'staff/tickets_listado_view.php';
		$data['listado'] = $listado;
		$data['error'] = '';

		$this->load->view('staff/main_staff_view', $data);
	}
}

/* End of file tickets.php */
/* Location: ./application/controllers/admin/tickets.php */