<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cambia_pass extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if (! is_logged())
			redirect(base_url() . 'login');
	}

	public function index()
	{
		$data['error'] = '';
		$this->load->view('public/cambia_pass_view', $data);	
	}

	public function enviar()
	{
		$this->load->model('usuario_model');
		$id_usuario = $this->session->userdata('idUsuario');
		$clave = $this->input->post('pass');
		$confirma = $this->input->post('confirma');

		if ($clave != $confirma)
		{
			$data['error'] = 'Las contrase&ntilde;as no cinciden';

			$this->load->view('public/cambia_pass_view', $data);
			return false;
		}

		$this->usuario_model->update_password_usuario($id_usuario, $clave);
		redirect(base_url() . 'login');
	}

}

/* End of file cambia_pass.php */
/* Location: ./application/controllers/cambia_pass.php */