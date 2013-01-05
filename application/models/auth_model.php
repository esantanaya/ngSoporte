<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model {

	var $tablas = array();
	var $saltLength = 10; //longitud del salt para las contraseñas y authkeys
	var $authKeyLength = 20; //longitud del authkey

	function __construct() {
		parent::__construct();
		$this -> load -> config('tables', TRUE);
		$this -> tablas = $this -> config -> item('tablas', 'tables');
		//cargamos librerias y helpers necesarios en el constructor
		$this -> load -> helper('cookie');
		$this -> load -> library('user_agent');
	}

	function getSalt($saltLength) {
		// Obtenemos un salt
		return substr(md5(uniqid(rand(), true)), 0, $saltLength);
	}

	function getNewAuthKey($emailUsuario, $authKeyLength) {
		//Obtenemos una nueva AuthKey para un usuario, cifrando su nombre de usuario o email
		return substr(strtoupper(sha1(substr(md5(uniqid(rand(), true)), 0, 35) . md5($emailUsuario))), 0, $this -> authKeyLength);
	}

	function hashPassword($plainpassword, $dbsalt = null) {
		//Hasheamos una contrase�a

		if ($dbsalt == null) {
			//Si no tenemos un salt de la base de datos, creamos un nuevo hash para insertar en DB
			$salt = $this -> getSalt($this -> saltLength);
			$hashedpassword = $salt . sha1($salt . md5($plainpassword));
		} else {
			//Si tenemos un salt de la DB, hasheamos el salt con la contrase�a
			$hashedpassword = $dbsalt . sha1($dbsalt . md5($plainpassword));
		}
		return $hashedpassword;
	}

	function setAuthKey($authKey = null, $emailUsuario) {
		//Funci�n que inserta un nuevo authkey. Esto se debe hacer cada que el usuario inicia sesi�n y cada que haya un logout
		if($authKey!=null)
			$authKey = $this -> getNewAuthKey($emailUsuario, $this -> authKeyLength);
		$arrUpdate = array('authKey' => $authKey);
		$this -> db -> where('emailUsuario', $emailUsuario);
		$this -> db -> update($this->tablas['usuario'],$arrUpdate);
		return true;
	}

	function setUserData($idUsuario){
		//obtenemos user agent, ip del usuario y un timestamp para guardar en la db
		$arrUpdate = array(
			'useragent' => $this->agent->agent_string(),
			'last_ip_access' => $this->input->ip_address(),
			'last_access' => date('Y-m-d H:i:s', time())
		);
		$this->db->where('idUsuario',$idUsuario);
		$this->db->update($this->tablas['usuario'],$arrUpdate);
		return true;
	}
	
	function setCookies($idUsuario, $authKey) {
		//funcion que crea las cookies para recordar un usuario
		//esta cookie almacena un hash con el aget user del usuario, y con ese hash crea otro con sha1 para almacenar en la DB
		$authId = md5($this->agent->agent_string());		
		setcookie('AuthID',$authId,time() + 60 * 60 * 24 * 30,'/');
		$this->db->where('idUsuario',$idUsuario);
		$this->db->update($this->tablas['usuario'],array('authId' => sha1($authId)));
		//esta cookie almacena el authkey del usuario, debe almacenarse el authkey renovado, no el viejo
		setcookie('AuthKey',$authKey,time() + 60 * 60 * 24 * 30,'/');
	}

	function deleteCookies() {
		//funci�n que borra las cookies del usuario. Se deben borrar cada que haya un logout y antes de crear nuevas cookies
		$cookieID = array('name' => 'AuthID', 'value' => '', 'expire' => '', 'path' => '/', 'secure' => FALSE);
		$cookieKey = array('name' => 'AuthKey', 'value' => '', 'expire' => '', 'path' => '/', 'secure' => FALSE);

		$this -> input -> set_cookie($cookieID);
		$this -> input -> set_cookie($cookieKey);
	}

	function login($emailUsuario, $contrasenaUsuario, $recordar) {//funcion de loggeo
		/*
		 REGRESA:
		 1: accede
		 9: datos incorrectos
		 0: usuario inactivo
		 -2: usuario baneado
		 */
		//Buscamos el usuario o email introducido
		$this -> db -> where('emailUsuario', $emailUsuario);
		$this -> db -> limit(1);
		$this -> db -> or_where('usuario', $emailUsuario);
		$query = $this -> db -> get($this -> tablas['usuario']);
		if ($query -> num_rows() == 1) {
			//si tenemos UN SOLO RESULTADO, es que el usuario existe
			$result = $query -> row();
			$saltdb = substr($result -> contrasenaUsuario, 0, $this -> saltLength);
			//recuperamos el salt del password de la DB
			if ($this -> hashPassword($contrasenaUsuario, $saltdb) == $result -> contrasenaUsuario) {
				//comparamos si el hash de la contrase�a introducida es igual al de la DB
				switch($result->status) {
					case 0 :
						return 0;
						exit ;
						break;
					case 2 :
						return -2;
						exit ;
						break;
					case 1 :
					//si todo va bien, el usuario accesde
						$this -> iniciarsesion($result, $recordar);
						return 1;
						//wwelcome
						break;
				}
			} else {
				return 9;
				//:(
			}
		} else {
			return 9;
			//:((
		}
	}

	

	function iniciarsesion($result, $cookie) {
		$authKey = $this -> getNewAuthKey($result -> emailUsuario, $this -> authKeyLength);
		$this -> session -> set_userdata(array(
		//Creamos variables de sesi�n
			'logged' => true, 
			'idUsuario' => $result -> idUsuario, 
			'nombreUsuario' => $result -> usuario, 
			'emailUsuario' => $result -> emailUsuario, 
			'nombre' => $result -> nombre,
			'apellidos' => $result -> apellidos,
			'nivel' => $result -> nivel,
			'rol' => $result -> idRol,
			'authKey' => $authKey
		));
		// obtenemos un nuevo authKey
		if ($cookie == 'true') {
			//si el usuario eligi� recordar, limpiamos cookies existentes y renovamos con el nuevo authkey
			// $this -> deleteCookies();
			$this -> setCookies($result->idUsuario, $authKey);
		}
		$this -> setAuthKey($authKey, $result -> emailUsuario);
		//actualizamos authkey en la DB con el ya generado
	}

	function isThatMySession() {
		//funci�n que verifica si existe una sesi�n activa del usuario
		if ($this -> session -> userdata('logged') == true && $this -> session -> userdata('emailUsuario') != '') {
			return true;
		} else {
			return false;
		}
	}

	function isThatMyCookie() {
		//funci�n que verifica si las cookies almacenadas son correctas, para loggear al usuario
		if ($this -> input -> cookie('AuthID') != false && $this -> input -> cookie('AuthKey') != false) {//existen las cookies?
			$user = sha1($this -> input -> cookie('AuthID'));
			//obtenemos el salt del usuario apartir de la cookie AuthID
			$this -> db -> where('authId',$user);
			$query = $this -> db -> get($this -> tablas['usuario']);
			//buscamos
			if ($query -> num_rows() == 1) {
				//si existe el salt
				$row = $query -> row();
				if ($this -> input -> cookie('AuthKey') == $row -> authKey) {//comparamos el authKey de la cookie con el de la DB
					$this -> iniciarsesion($row, true);
					//si coinciden, accede
					return true;
				} else {
					return false;
					// no coincide el authkey
				}
			} else {
				return false;
				//no existe en la db
			}
		} else {
			return false;
			//no hay cookies
		}
	}
}

/* End of file auth_model.php */
/* Location: ./application/models/auth_model.php */
