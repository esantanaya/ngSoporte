<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
	}

	function send_email($from = null, $to, $asunto, $mensaje, $adjunto = null)
	{
		$config = array(
					'mailtype' => 'html',
					'protocol' => 'smtp',
					'smtp_host' => 'mail.nygconsulting.com.mx',
					'smtp_user' => 'enrique.santana@nygconsulting.com.mx',
					'smtp_pass' => '3691012',
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
			$this->email->from("enrique.santana@nygconsulting.com.mx", 
				'Sistema de Tickets N&G');
		}
		
		$this->email->to($to);
		$this->email->subject($asunto);
		$this->email->message($mensaje);

		if ($adjunto != null)
		{
			$this->email->attach($adjunto);
		}
		
		if(!$this->email->send())
			return false;
		return true;
		//$this->email->send();
	}
}

/* End of file email_model.php */
/* Location: ./application/models/email_model.php */
