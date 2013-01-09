<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* 
*/
class Usuario {
	
	var $id_usuario;
	var $id_tipo_usuario;
	var $id_grupo_usuario;
	var $id_departamento_usuario;
	var $cod_usuario;
	var $nombre_usuario;
	var $apellido_paterno;
	var $apellido_materno;
	var $pass_usuario;
	var $email_usuario;
	var $tel_usuario;
	var $ext_usuario;
	var $movil_usuario;
	var $firma_usuario;
	var $activo;
	var $admin;
	var $visible;
	var $vacacion;
	var $agrega_firma;
	var $cambia_pass;
	var $creado;
	var $ultima_entrada;
	var $actualizado;

	public function __construct()
	{
		//parent::__construct();
		
	}

}

/* End of file Usuario.php */
/* Location: ./application/libraries/Usuario.php */