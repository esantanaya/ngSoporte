<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('usuario_model');

		if (!is_logged())
			redirect(base_url() . 'login');

		if (! is_authorized(array(0, 1), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');

		$this->load->model('conf_model');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index()
	{
		$horarios = $this->conf_model->get_horario();

		$data['inicio'] = $horarios[0]['horario_soporte_inicio'];
		$data['fin'] = $horarios[0]['horario_soporte_final'];
		$data['SYS_MetaTitle'] = 'Tickets :: Configuración';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Configuración del Sistema';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/config_view';
		$data['encabezado'] = 'CONFIGURACIÓN';
		$data['js'] = array('jQuery', 'jquery.timepicker.min');
		$data['css'] = array('jquery.timepicker');

		$this->load->view('admin/main_admin_view', $data);
	}

	public function guardaHorario()
	{
		$ini = $this->input->post('inicio');
		$fin = $this->input->post('fin');

		$data['horario_soporte_inicio'] = $ini;
		$data['horario_soporte_final'] = $fin;

		$this->conf_model->update_horario($data);
		$this->index();
	}
}

/* End of file config.php */
/* Location: ./application/controllers/admin/config.php */