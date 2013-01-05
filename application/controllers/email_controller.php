<?php
if(!defined('BASEPATH'))
	die();

class Email_controller extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('email');
	}
	
	function send_email($from = null, $to, $asunto, $mensaje){
		$config = array(
			'mailtype'  => 'html'
		);
		$this->email->initialize($config);
		if($from!=null){
			$this->email->from($from);
		}
		else{
			$this->email->from("CUENTA_CORREO");
		}
		$this->email->to($to);
		$this->email->subject($asunto);
		$this->email->message($mensaje);
		// if(!$this->email->send())
			// return false;
		// return true;
		$this->email->send();
	}
	
}

?>