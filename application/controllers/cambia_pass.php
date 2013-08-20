<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cambia_pass extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if (! is_logged())
			redirect(base_url() . 'login');

		$this->load->model('usuario_model');

		if (! $this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url());
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

		$this->form_validation->set_rules('pass', 'Contrase&ntilde;a', 
			'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (! $this->form_validation->run() || $clave != $confirma)
		{
			$data['error'] = '';
			if ($clave != $confirma)
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