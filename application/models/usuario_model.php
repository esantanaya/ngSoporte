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

	//****USUARIOS****

	public function get_current_id_usuario()
	{
		$this->db->select_max('id_usuario');
		$query = $this->db->get($this->tablas['usuarios']);
		
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->id_usuario;

			return $data;
		}

		return 1;
	}

	public function get_id_usuario($cod_usuario)
	{
		$this->db->select('id_usuario');
		$this->db->where('cod_usuario', $cod_usuario);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$id = $row->id_usuario;

			return $id;
		}

		return null;
	}

	public function insert_usuario($data) 
	{
		$data['pass_usuario'] = $this->auth_model->hashPassword(
			$data['pass_usuario'], null);
		$data['authKey'] = $this->auth_model->getNewAuthKey(
			$data['cod_usuario'], 20);
		$this->db->insert($this->tablas['usuarios'], $data);

		return $this->db->insert_id();
	}

	public function insert_horario_usuario($id_usuario)
	{
		$data['id_staff'] = $id_usuario;
		$data['id_horario'] = 1;
		$this->db->insert($this->tablas['empHorarios'], $data);

		return $this->db->insert_id();
	}

	public function get_usuario_tipo($tipo)
	{
		$this->db->where('id_tipo_usuario', $tipo);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0) 
		 	return $query->result();

		 return null; 
	}

	public function get_usuario_clave($clave)
	{
		$this->db->where('cod_usuario', $clave);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$data = $row->cod_usuario;

		 	return $data;
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

	public function update_usuario_cuenta($cod_usuario, $valores)
	{
		$this->db->where('cod_usuario', $cod_usuario);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >=  1) 
		{
			$this->db->where('cod_usuario', $cod_usuario);
			$this->db->update($this->tablas['usuarios'], $valores);

			return true;
		}

		return false;
	}

	public function update_password_usuario($id_usuario, $new_password)
	{
		$this->db->where('id_usuario', $id_usuario);
		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() >= 1)
		{
			$new_password = $this->auth_model->hashPassword($new_password, 
															null);
			$data = array('pass_usuario' => $new_password, 
						  'cambia_pass' => 0);
			$this->db->where('id_usuario', $id_usuario);
			$this->db->update($this->tablas['usuarios'], $data);

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

	public function get_miembros_staff($dept)
	{
		$this->db->where('id_departamento_usuario', $dept);
		$this->db->where('vacacion', 0);
		$this->db->where('id_nivel_usuario', 2);
		$this->db->where('activo', 1);
		$this->db->where(
			'(CURTIME() BETWEEN entrada_mat AND salida_mat OR CURTIME() 
			BETWEEN entrada_ves AND salida_ves)');
		$this->db->join($this->tablas['empHorarios'], 'id_staff = id_usuario');
		$this->db->join($this->tablas['horarios'], 
			'sop_horarios.id_horario = us_horario.id_horario');
		$this->db->select('cod_usuario');
		$staff = $this->db->get($this->tablas['usuarios']);

		return $staff;
	}

	public function get_staff_asignados()
	{
		$this->db->select('cod_staff, cod_staff');
		$query = $this->db->get($this->tablas['ticket']);

		if ($query->num_rows() > 0)
			return $query->result_array();

		return null;
	}

	public function get_staff_estados($cod_staff)
	{
		$this->db->select('vacacion, activo, entrada_mat, salida_mat, entrada_ves, salida_ves, corrido');
		$this->db->where('cod_usuario', $cod_staff);
		$this->db->join($this->tablas['empHorarios'], $this->tablas['usuarios'].'.id_usuario = '.$this->tablas['empHorarios'].'.id_staff');
		$this->db->join($this->tablas['horarios'], $this->tablas['empHorarios'].'.id_horario = '.$this->tablas['horarios'].'.id_horario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			 $row = $query->row();

			 return $row;
		}

		return null;

	}

	public function get_usuario_nombre($cod_usuario, $id_usuario = null)
	{
		if ($id_usuario == null)
		{
			$this->db->where('cod_usuario', $cod_usuario);
		}
		elseif($cod_usuario == null)
		{
			$this->db->where('id_usuario', $id_usuario);
		}

		$this->db->select('nombre_usuario, apellido_paterno, 
						   apellido_materno');
		$query = $this->db->get($this->tablas['usuarios'], 1);	

		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}

		return null;
	}

	public function get_usuario_mail($cod_usuario, $id_usuario = null)
	{

		if (!isset($id_usuario))
		{
			$this->db->where('cod_usuario', $cod_usuario);
		}
		elseif (!isset($cod_usuario))
		{
			$this->db->where('id_usuario', $id_usuario);
		}

		$this->db->select('email_usuario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$mail = $row->email_usuario;

			return $mail;
		}

		return null;
	}

	public function get_usuario_tel($cod_usuario, $id_usuario = null)

	{

		if ($id_usuario == null)

		{

			$this->db->where('cod_usuario', $cod_usuario);

		}

		elseif ($cod_usuario == null)

		{

			$this->db->where('id_usuario', $id_usuario);

		}

		$this->db->select('tel_usuario');

		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)

		{

			$row = $query->row();

			$tel = $row->tel_usuario;

			return $tel;

		}

		return null;

	}

	public function get_usuario_ext($cod_usuario, $id_usuario = null)

	{

		if ($id_usuario == null)

		{

			$this->db->where('cod_usuario', $cod_usuario);

		}

		elseif ($cod_usuario == null)

		{

			$this->db->where('id_usuario', $id_usuario);

		}

		$this->db->select('ext_usuario');

		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)

		{

			$row = $query->row();

			$ext = $row->ext_usuario;

			return $ext;

		}

		return null;

	}

	public function get_usuario_firma($cod_usuario, $id_usuario = null)
	{

		if ($id_usuario == null)
		{
			$this->db->where('cod_usuario', $cod_usuario);
		}
		elseif ($cod_usuario == null)
		{
			$this->db->where('id_usuario', $id_usuario);
		}

		$this->db->select('firma_usuario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$firma = $row->firma_usuario;

			return $firma;
		}

		return null;
	}

	public function get_empresa($id_usuario)
	{
		$this->db->select('id_empresa');
		$this->db->where('id_usuario', $id_usuario);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$id_empresa = $row->id_empresa;

			return $id_empresa;
		}

		return null;
	}

	public function get_cambia_pass($id_usuario)
	{
		$this->db->select('cambia_pass');
		$this->db->where('id_usuario', $id_usuario);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$cambio = $row->cambia_pass;

			if ($cambio == 0)
				return false;

			if ($cambio == 1)
				return true;
		}

		return null;
	}

	public function get_usuarios_nivel($nivel)

	{

		$this->db->where('vacacion', 0);

		$this->db->where('activo', 1);

		$this->db->where('id_nivel_usuario', $nivel);

		$query = $this->db->get($this->tablas['usuarios']);

		if ($query->num_rows() > 0)

			return $query->result_array();

		return null;

	}

	public function get_usuario_hashed_pass($cod_usuario)
	{
		$this->db->where('cod_usuario', $cod_usuario);
		$this->db->select('pass_usuario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$pass = $row->pass_usuario;

			return $pass;
		}

		return null;
	}

	public function get_horarios_preferencias()
	{
		$query = $this->db->get($this->tablas['horarios']);

		if ($query->num_rows() > 0) 
			return $query->result_array();

		return null;
	}

	public function get_usuario_horario($cod_usuario)
	{
		$this->db->where('cod_usuario', $cod_usuario);
		$this->db->select('sop_horarios.id_horario');
		$this->db->join($this->tablas['empHorarios'], 
			'us_horario.id_staff = us_usuarios.id_usuario');
		$this->db->join($this->tablas['horarios'], 
			'sop_horarios.id_horario = us_horario.id_horario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query == 1)
		{
			$row = $query->row();
			$horario = $row->id_horario;

			return $horario;
		}
		
		return null;
	}

	public function update_usuario_horario($id_usuario, $horario)
	{
		$this->db->where('id_staff', $id_usuario);
		$this->db->update($this->tablas['empHorarios'], $horario);

		return true;
	}

	public function get_nivel_usuario($id_usuario)

	{

		$this->db->select('id_nivel_usuario');

		$this->db->where('id_usuario', $id_usuario);

		$query = $this->db->get($this->tablas['usuarios']);

		if ($query == 1)

		{

			$row = $query->row();

			$nivel = $row->id_nivel_usuario;

			return $nivel;

		}

		return null;

	}

	//****GRUPOS****

	public function get_current_grupo()

	{

		$this->db->select_max('group_id');

		$query = $this->db->get($this->tablas['grupos']) + 1;

		if ($query->num_rows() == 0)

		{

			return 1;

		}

		return $query->result();

	}

	public function get_grupo_name($nombre)

	{

		$this->db->where('group_name', $nombre);

		$query = $this->db->get($this->tablas['grupos']);

		if ($query->num_rows() >= 1) 

		{

			return $query->result();

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

	public function get_usuarios_listado()
	{
		$cadena = 'SELECT CONCAT( \'<a href="'
			. 'edita/\', us.cod_usuario, \'">\', CONCAT('
			. 'us.nombre_usuario, \' \', us.apellido_paterno, \' \', '
			. 'us.apellido_materno ) , \'</a>\' ) AS Usuario, rl.nombreRol AS '
			. 'Nivel, em.nombre_empresa AS Empresa, dp.dept_name AS '
			. 'Departamento, us.activo AS ESTADO FROM us_usuarios us '
			. 'INNER JOIN rol rl ON us.id_nivel_usuario = rl.idRol INNER JOIN '
			. 'sop_empresas em ON us.id_empresa = em.empresa_id INNER JOIN '
			. 'us_departamentos dp ON us.id_departamento_usuario = dp.dept_id '
			. 'ORDER BY rl.nombreRol ASC';

		$query = $this->db->query($cadena);

		if ($query->num_rows() > 0)
			return $query;

		return null;
	}

	public function get_edita_usuario($cod_usuario)
	{
		$this->db->select('id_departamento_usuario, id_nivel_usuario, 
			nombre_usuario, apellido_paterno, apellido_materno, email_usuario, 
			tel_usuario, ext_usuario, movil_usuario, firma_usuario, 
			cambia_pass, activo, visible, vacacion');

		$this->db->where('cod_usuario', $cod_usuario);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() == 1)
			return $query->row();

		return null;
	}

	public function get_edita_cliente($cod_usuario)
	{
		$this->db->select('id_empresa, nombre_usuario, apellido_paterno, 
						   apellido_materno, email_usuario, 
						   tel_usuario, ext_usuario, movil_usuario, 
						   cambia_pass, activo');
		$this->db->where('cod_usuario', $cod_usuario);
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() == 1)
			return $query->row();

		return null;
	}

	//****DEPARTAMENTOS****

	public function insert_departamento($data)
	{
		$this->db->insert($this->tablas['departamentos'], $data);
	}

	public function get_departamentos_id()
	{
		$this->db->select('dept_id, dept_name');
		$this->db->where('dept_id >=', 1);
		$query = $this->db->get($this->tablas['departamentos']);

		return $query->result_array();
	}

	public function get_usuario_dep($cod_usuario)
	{
		$this->db->select('id_departamento_usuario');
		$query = $this->db->get($this->tablas['usuarios'], 1);

		if ($query->num_rows() == 1)
			return $query->row();;

		return null;
	}

	//****NIVELES****

	public function get_roles_staff()
	{
		$this->db->select('idRol, nombreRol');
		$this->db->where('idRol <', 3);
		$query = $this->db->get($this->tablas['rol']);

		return $query->result_array();
	}

	//****EMPRESAS****

	public function get_empresas()
	{
		$this->db->select('empresa_id, nombre_empresa');
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows() > 0)
			return $query->result_array();

		return null;
	}

	public function get_empresa_edicion($id_empresa)
	{
		$this->db->select('cod_empresa, nombre_empresa, correo, activa, 
			soporte');
		$this->db->where('empresa_id', $id_empresa);
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows == 1)
			return $query->result();

		return null;
	}

	public function get_id_empresa($cod_empresa)
	{
		$this->db->select('empresa_id');
		$this->db->where('cod_empresa', $cod_empresa);
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$id = $row->empresa_id;

			return $id;
		}
			
		return false;
	}

	public function get_empresa_cod($cod_empresa)
	{
		$this->db->where('cod_empresa', $cod_empresa);
		$query = $this->db->get($this->tablas['empresas']);

		if($query->num_rows() > 0)
			return true;

		return false;
	}

	public function get_empresa_nombre($nombre_empresa)
	{
		$this->db->where('nombre_empresa', $nombre_empresa);
		$query = $this->db->get($this->tablas['empresas']);

		if($query->num_rows() > 0)
			return true;

		return false;
	}

	public function get_correo_empresa($empresa_id)
	{
		$this->db->select('correo');
		$this->db->where('empresa_id', $empresa_id);
		$query =$this->db->get($this->tablas['empresas']);

		if ($query->num_rows == 1)
		{
			$row = $query->row();
			$correo = $row->correo;

			return $correo;
		}

		return null;
	}

	public function get_estado_empresa($empresa_id)
	{
		$this->db->select('soporte');
		$this->db->where('empresa_id', $empresa_id);
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows() == 1)
		{
			$row = $query->row();
			$estado = $row->soporte;

			return $estado;
		}

		return null;
	}

	public function get_listado_empresas()
	{
		$this->db->select('CONCAT(\'<a href="editaEmpresa/\',empresa_id,\'">\',cod_empresa,\'</a>\') AS CÓDIGO, 
			nombre_empresa AS EMPRESA', false);
		$this->db->order_by('cod_empresa', 'asc');
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows() > 0)
			return $query;

		return null;
	}

	public function insert_empresa($data)
	{
		$this->db->insert($this->tablas['empresas'], $data);

		return $this->db->insert_id();
	}

	public function update_empresa($id_empresa, $data)
	{
		$this->db->where('empresa_id', $id_empresa);
		$query = $this->db->get($this->tablas['empresas']);

		if ($query->num_rows() >= 1)
		{
			$this->db->where('empresa_id', $id_empresa);
			$this->db->update($this->tablas['empresas'], $data);

			return true;
		}

		return false;
	}
}

/* End of file usuario_model.php */
/* Location: ./application/models/usuario_model.php */