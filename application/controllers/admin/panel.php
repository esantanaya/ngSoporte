<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panel extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('date');
		$this->load->library('table');
		$this->load->model('usuario_model');

		if (!is_logged())
			redirect(base_url() . 'login');

		if (! is_authorized(array(0, 1), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index()
	{
		
	}

	public function listaEmpresas()
	{
		$listado = $this->usuario_model->get_empresas();
	}

}

/* End of file panel.php */
/* Location: ./application/controllers/admin/panel.php */