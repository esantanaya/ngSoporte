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

	public function index($value='')
	{
		# code...
	}

	public function nuevo($cod_usuario = null, $clave = null, $id = null)
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

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel de creación de usuarios';
		$data['modulo'] = 'admin/nuevo_usuario_view';
		$data['depts'] = $select;
		$data['niveles'] = $niveles;
		$data['cod_usuario'] = '';
		$data['clave'] = '';
		$data['id_usuario'] = '';

		if ($cod_usuario != null)
			$data['cod_usuario'] = $cod_usuario;
		
		if ($clave != null)
			$data['clave'] = $clave;

		if ($id != null)
			$data['id_usuario'] = $id;

		$this->load->view('admin/main_admin_view', $data);
	}

	public function crea_usuario()
	{
		$this->load->helper('date');

		$date_string = "%Y-%m-%d %h:%i:%s";
		$time = time();
		$date_string = mdate($date_string, $time);

		$cod_usuario = $this->input->post('cod_usuario');
		$repite = $this->usuario_model->get_usuario_clave($cod_usuario);
		$tel = $this->input->post('tel_usuario');
		$ext = $this->input->post('ext_tel');
		$cel = $this->input->post('cel_usuario');
		$firma = $this->input->post('firma_usuario');
		$cambia = 1;
		$activo = 1;
		$listado = 1;
		$vacacion = 0;


		if (! isset($_POST['cambia_pass']))
			$cambia = 0;
		if ($this->input->post('activo') == 'bloqueado')
			$activo = 0;
		if (! isset($_POST['listado']))
			$listado = 0;
		if (isset($_POST['vacacion']))
			$vacacion = 1;

		$this->form_validation->set_rules('cod_usuario', 'Usuario', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('nombre_usuario', 
			'Nombre', 'trim|required|xss_clean');
		$this->form_validation->set_rules('apellido_paterno', 
			'Apellido Paterno', 'trim|required|xss_clean');
		$this->form_validation->set_rules('apellido_materno', 
			'Apellido Materno', 'trim|required|xss_clean');
		$this->form_validation->set_rules('mail_usuario', 
			'Correo', 'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		$clave = $this->input->post('pass_usuario');
		$clave_conf = $this->input->post('confirma_pass');

		if (! $this->form_validation->run() || $repite != null 
			|| $clave != $clave_conf)
		{
			if ($clave != $clave_conf)
			{
				$clave_err = 'La contrase&ntilde;a no coincide';
			}
			else
			{
				$clave_err = null;
			}
				
			if ($repite != null)
				$repite = 'El usuario ' . $repite . ' ya existe';

			$this->nuevo($repite, $clave_err);
			return false;
		}
		else
		{
			$data = array('cod_usuario' =>  $cod_usuario,
				'id_departamento_usuario' => 
										$this->input->post('departamento'),
				'id_nivel_usuario' => $this->input->post('nivel'),
				'nombre_usuario' => $this->input->post('nombre_usuario'),
				'apellido_paterno' => $this->input->post('apellido_paterno'),
				'apellido_materno' => $this->input->post('apellido_materno'),
				'email_usuario' => $this->input->post('mail_usuario'),
				'pass_usuario' => $clave,
				'tel_usuario' => $tel,
				'ext_usuario' => $this->input->post('ext_tel'),
				'movil_usuario' => $this->input->post('movil'),
				'firma_usuario' => $this->input->post('firma'),
				'cambia_pass' => $cambia,
				'activo' => $activo,
				'visible' => $listado,
				'vacacion' => $vacacion,
				'creado' => mdate($date_string)

			);

			$id_usuario = $this->usuario_model->insert_usuario($data);
			$id_usuario = 'Se cre&oacute; el usuario con el ID ' . $id_usuario;
			
			$this->nuevo(null, null, $id_usuario);
		}	
	}

}

/* End of file usuarios.php */
/* Location: ./application/controllers/admin/usuarios.php */