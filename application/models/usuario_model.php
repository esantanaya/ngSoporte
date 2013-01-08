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

	public function get_current_id_usuario()
	{
		$this->db->select_max('id_usuario');
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() == 0)
		{
			return 1;
		}
		return $query;
	}

	public function insert_usuario($data) 
	{
		$this->db->insert($this->tablas['usuarios'], $data);
		return $this->db->insert_id();
	}

	public function get_usuario_tipo($tipo)
	{
		$this->db->where('id_tipo_usuario', $tipo);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1) 
		{
		 	return $query;
		 }
		 return NULL; 
	}

	public function get_usuario_clave($clave)
	{
		$this->db->where('cod_usuario', $clave);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1)
		{
			return $query;
		}
		return null;
	}

	public function update_usuario_id($id, $valores)
	{
		$this->db->where('id_usuario', $id);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >=  1) 
		{
			$data = array(
				'cod_usuario' => $valores['username'],
				'id_departamento_usuario' => $valores['dept'],
				'id_grupo_usuario' => $valores['grupo'],
				'nombre_usuario' => $valores['nombre'],
				'apellido_paterno' => $valores['paterno'],
				'apellido_materno' => $valores['materno'],
				'email_usuario' => $valores['mail'],
				'tel_usuario' => $valores['tel_usuario'],
				'ext_usuario' => $valores['ext_usuario'],
				'cel_usuario' => $valores['cel_usuario'],
				'firma_usuario' => $valores['firma'],
				'cambia_pass' => $valores['cambia_pass'],
				'activo' => $valores['activo'],
				'admin' => $valores['admin'],
				'visible' => $valores['visible'],
				'vacacion' => $valores['vaciones']);

			$this->db->where('id_usuario', $id);
			$this->db->update($this->tablas['usuarios'], $data);

			return true;
		}
		return false;
	}

	public function update_password_usuario($id, $new_password)
	{
		$this->db->where('id_usuario', $id);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1)
		{
			$this->db->where('id_usuario', $id);
			$this->db->update($this->tablas['usuarios'], $new_password);

			return true;
		}
		return false;
	}

	public function delete_usuario($id)
	{
		$this->db->where('id_usuario', $id);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1)
		{
			$this->db->where('id_usuario', $id);
			$this->db->delete($this->tablas['usuarios']);

			return true;
		}
		return false;
	}

	public function get_current_grupo()
	{
		$this->db->select_max('group_id');
		$query = $this->db->get($this->tablas['grupos']);

		if ($query->num_rows() == 0)
		{
			return 1;
		}

		return $query;
	}

	public function get_grupo_name($nombre)
	{
		$this->db->where('group_name', $nombre);
		$query = $this->db->get($this->tablas['grupos']);

		if ($query->num_rows() >= 1) 
		{
			return $query;
		}
		return null;
	}

	public function insert_grupo($data)
	{

		$nombre = $data['group_name'];
		$array = get_grupo_name($nombre);

		if ($array >= 1) 
		{
			return null;
		}
		else
		{
			$this->db->insert($this->tablas['grupos'], $data);
			return $this->db->insert_id();
		}
	}

	public function update_grupo_id($id, $valores)
	{
		$this->db->where('group_id', $id);
		$query = $this->db->get($this->tablas['grupos']);

		if ($query->num_rows() >= 1)
		{
			$data = array(
				'group_name' => $valor['nombre'],
				'dept_access' => $valor['departamento'],
				'can_create_tickets' => $valor['crea_tickets'],
				'can_edit_ticekts' => $valor['edita_ticets'],
				'can_delete_tickets' => $valor['borra_tickets'],
				'can_close_tickets' => $valor['cierra_tickets'],
				'can_transfer_tickets' => $valor['transfer_tickets'],
				'can_ban_emails' => $valor['ban_mails']);

			$this->db->where('group_id', $id);
			$this->db->update($this->tablas['grupos'], $data);
			return true;
		}
		
		return false;
	}

	public function delete_grupo($id)
	{
		$this->db->where('group_id', $id);
		$query = $this->db->get($this->tablas['grupos']);

		if ($query->num_rows() >= 1)
		{
			$this->db->where('group_id', $id);
			$this->db->delete($this->tablas['grupos']);

			return true;
		}
		return false;
	}

	public function insert_departamento($data)
	{
		$this->db->insert($this->tablas['departamentos'], $data);
	}
}

/* End of file usuario_model.php */
/* Location: ./application/models/usuario_model.php */