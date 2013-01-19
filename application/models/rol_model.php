<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol_model extends CI_Model {

	private $permisosDeRol = array();
	
	function rol_tiene_permiso($idPermiso, $idRol) {
		$this->db->where('idPermiso',$idPermiso);
		$this->db->where('idRol',$idRol);
		$result =  $this->db->get('rolTienePermiso');
		if($result->num_rows()==1)
			return true;
		return false;
	}
	
	function __construct() {
		parent::__construct();
	}
	
	
	function obtenerRoles(){
		$this->db->where('borrado','0');
		$query = $this->db->get('sys_rol');
		return $query->result();
	}
	
	function agregar_rol($nombreRol, $arregloPermisos) {
		//Agrego el Rol a la DB
		$data = array(
			'nombreRol' => $nombreRol ,
   			'borrado' => '0');
		$this->db->insert('rol', $data); 
		$newRolId = $this->db->insert_id();
		//Inserto permisos
		foreach($arregloPermisos as $idPermiso) {
			$dataNP = array(
				'idRol' 	=> $newRolId ,
   				'idPermiso' => $idPermiso);
   			$this->db->insert('rolTienePermiso', $dataNP);
		}
	}
	
	function eliminarRol($idRol){
		//Obtengo los permisos
		$this->db->where('idRol',$idRol);
		$result =  $this->db->get('usuario');
		if($result->num_rows() > 0){
			return false;
		}else{
			$this->db->where('idRol', $idRol);
			$this->db->delete('rol'); 	
			$this->db->where('idRol', $idRol);
			$this->db->delete('rolTienePermiso');
			return true;	
		}
	}
	
	/* GENERATE PERMISOS DEL ROL */
	function generetaPermisosRol($idRol){
		//Limpio mi arreglo
		unset($this->permisosDeRol);
		$this->permisosDeRol =array();
		
		//Obtengo los permisos
		$resultado = $this->db->get('permiso');
		
		//Agrego permisos al arreglo
		foreach($queryRoles->result() as $row):
			$this->permisosDeRol[$row->idPermiso]=$row->nombrePermiso;
		endforeach;
	}
		
	function listPermisosCheckBoxesDeRol($idRol){
		//Lo que voy a regresar
		$returnea = "";
		//Obtengo los permisos
		$resultado = $this->db->get('permiso');
		//Agrego permisos al arreglo
		foreach($resultado->result() as $row){
			if(array_key_exists($row->idPermiso, $this->permisosDeRol))
			{
				$returnea .= "<input type=\"checkbox\" 
				name=\"permisos[]\" value=\"".($row->idPermiso) 
				. "\" checked>" . ($row->nombrePermiso)."<br>\n";
			}else{
				$returnea .= "<input type=\"checkbox\" 
					name=\"permisos[]\" value=\"".($row->idPermiso) . "\">" 
					. ($row->nombrePermiso) . "<br>\n";
			}
		}
		return $returnea;
	}
	
	function listPermisosCheckBoxes(){
		//Lo que voy a regresar
		$returnea = "";
		
		//Obtengo los permisos
		$this->db->where('borrado','0');
		$resultado = $this->db->get('permiso');

		//Agrego permisos al arreglo
		foreach($resultado->result() as $row){
			$returnea .= "<input type=\"checkbox\" name=\"permisos[]\" value=
				\"".($row->idPermiso)."\">".($row->nombrePermiso)."<br>\n";
		}
		
		return $returnea;
	}

}

/* End of file rol_model.php */
/* Location: ./application/models/rol_model.php */