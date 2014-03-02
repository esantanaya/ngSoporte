<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Conf_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->config('tables', true);		
		$this->tablas = $this->config->item('tablas', 'tables');
	}

	public function get_horario()
	{
		$this->db->select('horario_soporte_inicio, horario_soporte_final');
		$query = $this->db->get($this->tablas['config'], 1);

		if ($query->num_rows() > 0)
			return $query->result_array();

		return null;
	}

	public function update_horario($data)
	{
		$this->db->select();
		$query = $this->db->get($this->tablas['config']);

		if ($query->num_rows() > 0)
		{
			$this->db->update($this->tablas['config'], $data);
		}
		else
		{
			$this->db->insert($this->tablas['config'], $data);
		}
	}
}

/* End of file conf_model.php */
/* Location: ./application/models/conf_model.php */