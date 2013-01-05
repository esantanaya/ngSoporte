<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_model extends CI_Model {

	var $tablas = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->config('tables', TRUE);
		$this->tablas = $this->config->item('tablas', 'tables');
		$this->load->model('auth_model');
	}

	function insert_usuario($data) 
	{
		$this->db->insert($this->tablas['usuarios'], $data);
		return $this->db->insert_id();
	}

	public function get_usuario_tipo($tipo)
	{
		$this->db->where('id_tipo_usuario', $tipo);
		$this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1) 
		{
		 	return $query;
		 }
		 return NULL; 
	}


}

/* End of file usuario_model.php */
/* Location: ./application/models/usuario_model.php */
