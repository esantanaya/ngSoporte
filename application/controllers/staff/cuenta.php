<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cuenta extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function index()
	{
		$this->mi_cuenta();
	}

	public function mi_cuenta()
	{
		$data['SYS_MetaTitle'] = 'Staff :: Mi Cuenta';
		$data['SYS_metaKeyWords'] = 'sistema cuentas staff n&g';
		$data['SYS_metaDescription'] = 'Perfil Staff';
		$data['subMenu'] = 'staff/cuenta_submenu_view.php';
		$data['modulo'] = 'staff/cuenta_modifica_view.php';
		$data['error'] = '';

		$this->load->view('staff/main_staff_view', $data);
	}

}

/* End of file cuenta.php */
/* Location: ./application/controllers/staff/cuenta.php */