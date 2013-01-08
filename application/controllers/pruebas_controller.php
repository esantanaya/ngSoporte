<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pruebas_controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuario_model');
	}

	public function index()
	{

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$data['SYS_metaTitle'] = 'Pruebas';
		$data['exito'] = 'indesiso';
		$data['num'] = rand(100000,999999);

		if ($this->form_validation->run() == FALSE) 
		{
			$data['exito'] = 'un fracaso';
			$this->load->view('prueba_view', $data);
		}
		else
		{
			$data['exito'] = 'un exito';
			$this->load->view('prueba_view', $data);
		}
	}

	public function manda_dato()
	{
		$tipo = $this->input->post('tipo_cliente');
		$this->usuario_model->get_usuario_tipo($tipo);
	}
}

/* End of file pruebas_controller.php */
/* Location: ./application/controllers/pruebas_controller.php */
