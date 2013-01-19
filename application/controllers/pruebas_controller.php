<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pruebas_controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('usuario_model');
		$this->load->library('form_validation');
		$this->load->helper('date');
	}

	public function index()
	{

		$this->load->helper(array('form', 'url'));

		$datestring = "%Y%m%d %h:%i:%s";
		$time = time();

		$data['SYS_metaTitle'] = 'Pruebas';
		$data['num'] = rand(100000,999999);
		$data['id_usuario'] = '';
		$data['temp_id_usuario'] = $this->usuario_model->
									get_current_id_usuario();
		$data['fecha'] = mdate($datestring, $time);

		$this->load->view('prueba_view', $data);

	}

	public function manda_dato()
	{

		$id_usuario = $this->usuario_model->get_current_id_usuario();
		$id_usuario = intval($id_usuario) + 1;
		$clave_usuario = $this->input->post('usuario');
		
		$repite = $this->usuario_model->get_usuario_clave($clave_usuario);

		if ($repite != null)
		{
			$data['id_usuario'] = 'El usuario ya existe';
			$this->load->view('prueba_view', $data);
		}
		else
		{
			

			$data = array('id_usuario' => $id_usuario, 
				'id_tipo_usuario' => $this->input->post('tipo_usuario'),
				'cod_usuario' =>  $clave_usuario,
				'nombre_usuario' => $this->input->post('nombre'),
				'apellido_paterno' => $this->input->post('apellido_paterno'),
				'apellido_materno' => $this->input->post('apellido_materno'),
				'pass_usuario' => $this->input->post('password'),
				'email_usuario' => $this->input->post('mail'),
				'tel_usuario' => $this->input->post('tel'),
				'movil_usuario' => $this->input->post('movil'),
				'firma_usuario' => $this->input->post('firma'),
				);

			$id = $this->usuario_model->insert_usuario($data);
			$datos['id_usuario'] = 'ahhh si tu id de usuario es ' . $id;
			$this->load->view('prueba_view', $datos);
		}
	}
}

/* End of file pruebas_controller.php */
/* Location: ./application/controllers/pruebas_controller.php */
