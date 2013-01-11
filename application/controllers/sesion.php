<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sesion extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('auth_model');
	}

	public function login($redir, $failredir)
	{
		$query = $_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '';
		$redir = str_replace('-', '/', $redir);
		// $redir = str_replace('/admin-login','',$redir);	
		$failredir = str_replace('-', '/', $failredir);
		
		$this->form_validation->set_rules('usuario', 'Usuario', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('pass', 
			'Contrase&ntilde;a', 'trim|required|xss_clean');
		$this->form_validation->set_message('required', 
			'El campo "%s" es requerido');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		// Ejecuto la validacion de campos de lado del servidor
		if (!$this->form_validation->run()) 
		{
			$this->session->set_flashdata('error', 'error_4');
			redirect($failredir);
			return false;
		} else {						
			// if(!isThatMyToken('loginForm', $this->input->post('token'), 0))
			//{
				// $this->session->set_flashdata('error', 'invalidToken');
				// redirect($failredir);
				// return false;
			// }
			$usuario = $this -> input -> post('usuario');
			$password = $this -> input -> post('pass');
			$recordarme = $this -> input -> post('recordarme');
			if($failredir=='index')
				$failredir = '';
			switch($this -> auth_model -> login($usuario, $password, 
				$recordarme)) {
				case 1:
					if($query!=""){
						$redirect = $redir.$query;
						$redirect = substr($redirect, 0,
							(strlen($redirect)-12));
					}
					else{
						$redirect = $redir;
					}
					redirect($redirect);
				break;
				case 9 :
					$this->session->set_flashdata('error', 'userIncorrect');
					redirect($failredir);
				break;
				case 0 :
					$this->session->set_flashdata('error', 'inactiveUser');
					redirect($failredir);
				break;
				case -2 :
					$this->session->set_flashdata('error', 'bannedUser');
					redirect($failredir);
				break;
			}
		}
	}
}

/* End of file sesion.php */
/* Location: ./application/controllers/sesion.php */
