<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuenta extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (!is_logged())
			redirect(base_url() . 'login');
		
		if (! is_authorized(array(0, 1, 2), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');
		
		$this->load->model('usuario_model');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index()
	{
		$this->mi_cuenta();
	}

	public function mi_cuenta()
	{
		$cod_usuario = $this->session->userdata('nombreUsuario');
		$nombreCompleto = $this->usuario_model->get_usuario_nombre(
												$cod_usuario);
		$datos = array('nombre' => $nombreCompleto['0']['nombre_usuario'],
						'apPaterno' => $nombreCompleto['0']
						['apellido_paterno'],
						'apMaterno' => $nombreCompleto['0']
						['apellido_materno'],
						'correo' => $this->usuario_model->get_usuario_mail(
									$cod_usuario),
						'tel' => $this->usuario_model->get_usuario_tel(
								 $cod_usuario),
						'ext' => $this->usuario_model->get_usuario_ext(
								 $cod_usuario),
						'firma' => $this->usuario_model->get_usuario_firma(
								   $cod_usuario));

		$data['SYS_MetaTitle'] = 'Staff :: Mi Cuenta';
		$data['SYS_metaKeyWords'] = 'sistema cuentas staff n&g';
		$data['SYS_metaDescription'] = 'Perfil Staff';
		$data['subMenu'] = 'staff/cuenta_submenu_view.php';
		$data['modulo'] = 'staff/cuenta_modifica_view.php';
		$data['datos'] = $datos;
		$data['error'] = '';

		$this->load->view('staff/main_staff_view', $data);
	}

	public function pass($error = null, $conf = null)
	{
		$data['SYS_MetaTitle'] = 'Staff :: Cambia Contrase&ntilde;a';
		$data['SYS_metaKeyWords'] = 'sistema cuentas staff n&g';
		$data['SYS_metaDescription'] = 'Contrase&ntilde;a Staff';
		$data['subMenu'] = 'staff/cuenta_submenu_view.php';
		$data['modulo'] = 'staff/cuenta_pass_view.php';
		$data['pass_error'] = '';
		if ($error != null)
			$data['pass_error'] = $error;
		$data['pass_conf'] = '';
		if ($conf != null)
			$data['pass_conf'] = $conf;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function preferencias()
	{
		$cod_usuario = $this->session->userdata('nombreUsuario');
		$comida = $this->usuario_model->get_usuario_horario($cod_usuario);

		$uno = ($comida->salida == '13:00:00') ? 'checked' : '';
		$dos = ($comida->salida == '15:00:00') ? 'checked' : '';
		$no = ($comida->corrido == 1) ? 'checked' : '';

		$horario = array('uno' => $uno, 
						 'dos' => $dos,
						 'no' => $no);

		$data['SYS_MetaTitle'] = 'Staff :: Cambia Preferencias';
		$data['SYS_metaKeyWords'] = 'sistema cuentas staff n&g';
		$data['SYS_metaDescription'] = 'Preferencias Staff';
		$data['subMenu'] = 'staff/cuenta_submenu_view.php';
		$data['modulo'] = 'staff/cuenta_preferencias_view.php';
		$data['horario'] = $horario;

		$this->load->view('staff/main_staff_view', $data);
	}

	public function guarda_preferencias()
	{
		$horario = $this->input->post('horario');
		$salida = ($horario == '13') ? '13:00:00' : '15:00:00';
		$entrada = ($horario == '13') ? '15:00:00' : '17:00:00';
		$corrido = ($horario == 'no') ? 1 : 0;
		$cod_usuario = $this->session->userdata('nombreUsuario');
		$data = array('salida' => $salida,
					  'entrada' => $entrada,
					  'corrido' => $corrido);
		
		$this->usuario_model->update_usuario_horario($cod_usuario, $data);
		$this->preferencias();
	}

	public function guarda_pass()
	{
		$this->load->model('auth_model');
		$cod_usuario = $this->session->userdata('nombreUsuario');
		$id_usuario = $this->session->userdata('idUsuario');
		$saltdb = substr($this->usuario_model->get_usuario_hashed_pass(
						 $cod_usuario), 0, $this->auth_model->saltLength);
		$old_pass = $this->auth_model->hashPassword($this->input->post(
									   'old_pass'), $saltdb);
		$actual_pass = $this->usuario_model->get_usuario_hashed_pass(
						   					 $cod_usuario);
		$new_pass = $this->input->post('pass');
		$conf_pass = $this->input->post('confirma');

		$this->form_validation->set_rules('old_pass', 
			'Contrase&ntilde;a actual', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pass', 'Contrase&ntilde;a', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('confirma', 
			'Confirmaci&oacute;n', 'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if ($old_pass != $actual_pass || $new_pass != $conf_pass || ! 
			$this->form_validation->run())
		{
			$error = null;
			$conf = null;
			if ($old_pass != $actual_pass)
				$error = '<span class="error">La contrase&ntilde;a es
						  incorrecta</span>';

			if ($new_pass != $conf_pass)
				$conf = '<span class="error">Las contrase&ntilde;as no
						 coinciden</span>';

			$this->pass($error, $conf);
			return false;
		}

		$this->usuario_model->update_password_usuario($id_usuario, $new_pass);
		$this->pass();
	}

	public function guarda_cambios()
	{
		$cod_usuario = $this->session->userdata('nombreUsuario');

		$this->form_validation->set_rules('nombre_usuario', 
			'Nombre', 'trim|required|xss_clean');
		$this->form_validation->set_rules('apellido_paterno', 'Apellido', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('apellido_materno', 'Apellido', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('mail_usuario', 'Correo', 
			'trim|required|xss_clean|valid_email');
		$this->form_validation->set_message('required', 
			'Ingrese su "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (! $this->form_validation->run())
		{
			$this->mi_cuenta();
			return false;
		}

		$valores = array('nombre_usuario' => 
				   		  $this->input->post('nombre_usuario'), 
				   		  'apellido_paterno' => 
				   		  $this->input->post('apellido_paterno'),
				   		  'apellido_materno' => 
				   		  $this->input->post('apellido_materno'),
				   		  'email_usuario' => 
				   		  $this->input->post('mail_usuario'),
				   		  'tel_usuario' => $this->input->post('tel_usuario'),
				   		  'ext_usuario' => $this->input->post('ext_tel'),
				   		  'firma_usuario' => 
				   		  $this->input->post('firma_usuario'));

		$this->usuario_model->update_usuario_cuenta($cod_usuario, $valores);
		$this->mi_cuenta();
	}
}

/* End of file cuenta.php */
/* Location: ./application/controllers/staff/cuenta.php */