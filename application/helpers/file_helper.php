<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	private $path;
	private $userpic = 'userpic';

	function __construct() {
		parent::__construct();
		$this -> path = 'docs/';
		$this -> userpic = $this -> path . 'userpic';
		$this -> load -> library('upload');
	}

	private function name(
		$filename, $date = false, $random = false, $user_id = null) 
	{
		$returningName = '';
		if ($date)
			$returningName .= '_' . date('Y-m-d');
		if ($random)
			$returningName .= '_' . substr(md5(uniqid(rand(), true)), 0, 5);
		if ($user_id != null)
			$returningName .= '_' . $user_id;

		$returningName .= '_' . $filename;
		return $returningName;
	}

	public function uploadItem($target, $data = false, $file, $resize) {
		/*
		 * target = directorio
		 * data = array ('
		 * 			date'=>true||false,'random'=>true||false,
		 * random => true||false
		 * 		'user_id'=>session user id||null,
		 * 		'width'=>600||null,
		 * 		'height'=>400||null);
		 * file = input que sube
		 * resize = boolean
		 * */

		$ori_name = $_FILES[$file]['name'];
		$config['upload_path'] = $this -> path . $target;
		$config['allowed_types'] = 'gif|jpg|png|jpeg|pdf';
		// $config['allowed_types'] = '*';

		// $config['encrypt_name'] = 'TRUE';
		$config['max_size'] = '5120';
		$config['max_width'] = '1024';
		$config['max_height'] = '768';

		$config['file_name'] = $this -> name($ori_name, $data['date'], 
			$data['random'], $data['user_id']);
		$this -> upload -> initialize($config);

		if (!$this -> upload -> do_upload($file)) {
			$error = $this -> upload -> display_errors();
			$nombre = null;
			$return = array();
			$return['nombre'] = null;
			$return['error'] = $error;
			return $return;
		} else {
			$imgData = $this -> upload -> data();
			if ($resize) {
				$this -> resizeImage($imgData['file_name'], $data['width'], 
					$data['height'], $target, $target);
				// $preReturningName = explode('.', $imgData['file_name']);
				$preReturningName = substr($imgData['file_name'], 0, (strlen(
					$imgData['file_name'])-4));
				$extension = substr($imgData['file_name'],(strlen(
					$imgData['file_name'])-4),4);
				return $preReturningName.'_thumb'.$extension;
			} else {
				return $imgData['file_name'];
			}
		}
	}

	public function uploadNonImage($target, $data = false, $file) {
		/*
		 * target = directorio
		 * data = array ('
		 	     date'=>true||false,
		 * 		random => true||false
		 * 		'user_id'=>session user id||null,
		 * )
		 * file = input que sube
		 * */

		$ori_name = $_FILES[$file]['name'];
		$config['upload_path'] = $this -> path . $target;
		$config['allowed_types'] = '*';
		// $config['allowed_types'] = '*';

		// $config['encrypt_name'] = 'TRUE';
		$config['max_size'] = '5120';

		$config['file_name'] = $this -> name($ori_name, $data['date'], 
			$data['random'], $data['user_id']);
		$this -> upload -> initialize($config);

		if (!$this -> upload -> do_upload($file)) {
			$error = $this -> upload -> display_errors();
			$nombre = null;
			$return = array();
			$return['nombre'] = null;
			$return['error'] = $error;
			return $return;
		} else {
			$fileData = $this -> upload -> data();
			return $fileData['file_name'];
		}

	}

	public function deleteItem($file_name, $folder) {
		if ($file_name !== null) {
			if (@unlink($this -> pagination . $folder . $file_name))
				return true;
			return false;
		} else {
			return false;
		}
	}

	private function resizeImage($imgName, $width, $height, $source, $target) {
		$this -> load -> library('image_lib');
		$config['image_library'] = 'gd2';
		$config['source_image'] = 'docs/'.$target.'/' . $imgName;
		$config['create_thumb'] = TRUE;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;

		$this -> image_lib -> initialize($config);
		if (!$this -> image_lib -> resize())
			return false;	
		return true;
	}

}

/* End of file file_helper.php */
/* Location: ./application/helpers/file_helper.php */
