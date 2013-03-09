<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
			redirect(base_url() . 'login');
		if (! is_authorized(array(0, 1), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		$this->load->model('usuario_model');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');

		$this->load->helper('date');
		$this->load->library('table');
		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index($value='')
	{
		$this->lista();
	}

	public function lista()
	{

		$listado = $this->usuario_model->get_usuarios_listado();
		$tmpl = array('table_open' => '<table border="0" cellpadding="4"
				cellspacing="0" class="listado_table">');
		$this->table->set_template($tmpl);

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Listado de Usuario del sistema';
		$data['subMenu'] = 'admin/submenu_view';
		$data['modulo'] = 'admin/lista_usuario_view';
		$data['listado'] = $listado;

		$this->load->view('admin/main_admin_view', $data);
	}

	public function nuevo($cod_usuario = null, $clave = null, $id = null)
	{
		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) 
		{
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$datos = $this->usuario_model->get_roles_staff();

		foreach ($datos as $key => $value) 
		{
			$niveles[$value['idRol']] = $value['nombreRol'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel de creación de usuarios';
		$data['subMenu'] = 'admin/submenu_view';
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

	public function edita($cod_usuario)
	{
		$generales = $this->usuario_model->get_edita_usuario($cod_usuario);
		$nivel = $generales->id_nivel_usuario;

		if ($nivel <= 2)
		{
			$this->edita_usuario($cod_usuario);
		}
		else
		{
			$this->edita_cliente($cod_usuario);	
		}

	}

	public function edita_usuario($cod_usuario, $clave = null, $id = null)
	{
		$generales = $this->usuario_model->get_edita_usuario($cod_usuario);
		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) 
		{
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$datos = $this->usuario_model->get_roles_staff();

		foreach ($datos as $key => $value) 
		{
			$niveles[$value['idRol']] = $value['nombreRol'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel de edición de usuarios';
		$data['subMenu'] = 'admin/submenu_view';
		$data['modulo'] = 'admin/edita_usuario_view';
		$data['depts'] = $select;
		$data['niveles'] = $niveles;
		$data['deptActual'] = $generales->id_departamento_usuario;
		$data['nivelActual'] = $generales->id_nivel_usuario;
		$data['nombre'] = $generales->nombre_usuario;
		$data['apPaterno'] = $generales->apellido_paterno;
		$data['apMaterno'] = $generales->apellido_materno;
		$data['correo'] = $generales->email_usuario;
		$data['nombre'] = $generales->nombre_usuario;
		$data['tel'] = $generales->tel_usuario;
		$data['ext'] = $generales->ext_usuario;
		$data['cel'] = $generales->movil_usuario;
		$data['firma'] = $generales->firma_usuario;
		$data['cambioPass'] = ($generales->cambia_pass == 1) ? 'checked' : '' ;
		$data['activoCheck'] = ($generales->activo == 1) ? 'checked': '';
		$data['bloqueadoCheck'] = ($generales->activo == 0) ? 'checked': '';
		$data['listadoCheck'] = ($generales->visible == 1) ? 'checked' : '';
		$data['vacacionCheck'] = ($generales->vacacion == 1) ? 'checked' : '';
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

	public function edita_cliente($cod_usuario)
	{
		$generales = $this->usuario_model->get_edita_cliente($cod_usuario);
		$empresas = $this->usuario_model->get_empresas();

		foreach ($empresas as $depas => $valor) 
		{
			$select[$valor['dept_id']] = $valor['dept_name'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel de edición de usuarios';
		$data['subMenu'] = 'admin/submenu_view';
		$data['modulo'] = 'admin/edita_cliente_view';
		$data['empresas'] = $select;
		$dtata['empresaActual'] = $generales->id_empresa;
		$data['nombre'] = $generales->nombre_usuario;
		$data['apPaterno'] = $generales->apellido_paterno;
		$data['apMaterno'] = $generales->apellido_materno;
		$data['correo'] = $generales->email_usuario;
		$data['nombre'] = $generales->nombre_usuario;
		$data['tel'] = $generales->tel_usuario;
		$data['ext'] = $generales->ext_usuario;
		$data['cel'] = $generales->movil_usuario;
		$data['cambioPass'] = ($generales->cambia_pass == 1) ? 'checked' : '' ;
		$data['activoCheck'] = ($generales->activo == 1) ? 'checked': '';
		$data['bloqueadoCheck'] = ($generales->activo == 0) ? 'checked': '';
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

	public function cliente($cod_usuario = null, $clave = null, $id = null)
	{
		$datos = $this->usuario_model->get_empresas();

		foreach ($datos as $depas => $valor) 
		{
			$select[$valor['empresa_id']] = $valor['nombre_empresa'];
		}

		$data['SYS_MetaTitle'] = 'Tickets :: Usuarios';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Panel de creación de usuarios';
		$data['subMenu'] = 'admin/submenu_view';
		$data['modulo'] = 'admin/nuevo_cliente_view';
		$data['empresas'] = $select;
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

	public function crea_cliente()
	{
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
			'Correo', 'trim|required|xss_clean|valid_email');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
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

			$this->cliente($repite, $clave_err);
			return false;
		}
		else
		{
			if (empty($clave))
				$clave = '123';

			$data = array('cod_usuario' =>  $cod_usuario,
				'id_departamento_usuario' => 0,
				'id_nivel_usuario' => 3,
				'id_empresa' => $this->input->post('empresas'),
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
				'visible' => 1,
				'vacacion' => 0,
				'creado' => mdate($date_string)

			);

			$id_usuario = $this->usuario_model->insert_usuario($data);
			$id_usuario = 'Se cre&oacute; el usuario con el ID ' . $id_usuario;
			
			$this->nuevo(null, null, $id_usuario);
		}	
	}

	public function crea_usuario()
	{
		
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
			'Correo', 'trim|required|xss_clean|valid_email');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
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
			if (empty($clave))
				$clave = '123';

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

			$this->db->trans_start(true);
			$id_usuario = $this->usuario_model->insert_usuario($data);
			$this->db->trans_complete();
			$id_usuario = 'Se cre&oacute; el usuario con el ID ' . $id_usuario;
			
			$this->nuevo(null, null, $id_usuario);
		}
	}

	public function guarda_cambios()
	{
		$cod_usuario = $this->input->post('cod_usuario');
		$id_usuario = $this->usuario_model->get_id_usuario($cod_usuario);
		$pass_usuario = $this->input->post('pass_usuario');
		$confirma_pass = $this->input->post('confirma_pass');
		$cambia_pass = ($this->input->post('cambia_pass') == 'on') ? 1 : 0;
		$activo = ($this->input->post('activo') == 'activo') ? 1 : 0;
		$visible = ($this->input->post('listado') == 'on') ? 1 : 0;
		$vacacion = ($this->input->post('vacacion') == 'on') ? 1 : 0;

		if ($pass_usuario != $confirma_pass)
		{
			$badPass = 'Las Contrase&ntilde;as no coinciden';
			$this->edita_usuario($cod_usuario, $badPass);
			return false;
		}
		
		$data = array('id_departamento_usuario' => $this->input->post(
											   'departamento'), 
				  'id_nivel_usuario' => $this->input->post('nivel'),
				  'nombre_usuario' => $this->input->post('nombre_usuario'),
				  'apellido_paterno' => $this->input->post(
				  						 'apellido_paterno'),
				   'apellido_materno' => $this->input->post(
				   						 'apellido_materno'),
				   'email_usuario' => $this->input->post('mail_usuario'),
				   'tel_usuario' => $this->input->post('tel_usuario'),
				   'ext_usuario' => $this->input->post('ext_tel'),
				   'movil_usuario' => $this->input->post('cel_usuario'),
				   'firma_usuario' => $this->input->post('firma_usuario'),
				   'cambia_pass' => $cambia_pass,
				   'activo' => $activo,
				   'visible' => $visible,
				   'vacacion' => $vacacion);

		if (! empty($pass_usuario))
		{
			$this->usuario_model->update_password_usuario($id_usuario, 
														  $pass_usuario);
		}
		
		$this->usuario_model->update_usuario_cuenta($cod_usuario, $data);
		$this->edita_usuario($cod_usuario);
	}

}

/* End of file usuarios.php */
/* Location: ./application/controllers/admin/usuarios.php */