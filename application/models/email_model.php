<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
	}

	function send_email($from=null, $to, $asunto, $mensaje, $copia=null, 
		$adjunto=null)
	{
		// $config = array(
		// 			'mailtype' => 'html',
		// 			'protocol' => 'smtp',
		// 			'smtp_host' => 'smtp.gmail.com',
		// 			'smtp_user' => 'esantanaya@gmail.com',
		// 			'smtp_pass' => 'Ceresilla03/06/2011',
		// 			'smtp_port' => '465',
		// 			'smtp_timeout' => '60'
		// 			);

		$config = array(
					'mailtype' => 'html',
					'protocol' => 'smtp',
					'smtp_host' => 'mail.simbank.mx',
					'smtp_user' => 'notificaciones@simbank.mx',
					'smtp_pass' => 'Rocco@Jack@Camila',
					'smtp_port' => '26',
					'smtp_timeout' => '60'
					);

		$this->email->initialize($config);

		if ($from != null)
		{
			$this->email->from($from);
		}
		else
		{
			$this->email->from("esantanaya@gmail.com", 
				'Sistema de Tickets N&G');
		}
		
		$this->email->to($to);
		$this->email->subject($asunto);
		$this->email->message($mensaje);

		if ($copia != null)
			$this->email->cc($copia);

		// $this->email->bcc('esantana@nygconsulting.com.mx');

		if ($adjunto != null)
			$this->email->attach($adjunto);

		if(!$this->email->send())
			return false;

		return true;

		//$this->email->send();
	}

}



/* End of file email_model.php */

/* Location: ./application/models/email_model.php */

