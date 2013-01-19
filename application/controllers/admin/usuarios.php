<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
		{
			redirect(base_url() . 'login');
		}

		$this->load->model('usuario_model');
	}

	public function nuevo()
	{
		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) 
		{
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$datos = $this->usuario_model->get_roles();

		foreach ($datos as $key => $value) 
		{
			$niveles[$value['idRol']] = $value['nombreRol'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Inicio';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel principal';
		$data['modulo'] = 'admin/nuevo_usuario_view';
		$data['depts'] = $select;
		$data['niveles'] = $niveles;
		$data['error'] = '';

		$this->load->view('public/main_tickets_view', $data);
	}

	public function crea_usuario()
	{

		$cod_usuario = $this->input->post('cod_usuario');
		$repite = $this->usuario_model->get_usuario_clave($cod_usuario);
		$clave = $this->input->post('pass_usuario');
		$clave_conf = $this->input->post('confirma_pass');

		if ($repite != null)
		{
			$data['id_usuario'] = 'El usuario ya existe';
			$this->load->view('', $data);
		}
		else
		{
			if ($clave != $clave_conf)
			{

			}
			$data = array('cod_usuario' =>  $cod_usuario,
				'id_departamento_usuario' => 
										$this->input->post('departamento'),
				'id_nivel_usuario' => $this->input->post('nivel'),
				'nombre_usuario' => $this->input->post('nombre_usuario'),
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

/* End of file usuarios.php */
/* Location: ./application/controllers/admin/usuarios.php */