<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Panel extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('date');
		$this->load->library('table');
		$this->load->model('usuario_model');
		$this->load->model('ticket_model');

		if (!is_logged())
			redirect(base_url() . 'login');

		if (! is_authorized(array(0, 1), null, $this->session->userdata(
			'nivel'), $this->session->userdata('rol')))
			redirect(base_url() . 'login');

		if ($this->usuario_model->get_cambia_pass($this->session->userdata(
			'idUsuario')))
			redirect(base_url() . 'cambia_pass');

		error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
	}

	public function index()
	{
		$this->listaEmpresas();
	}

	public function listaEmpresas()
	{
		$listado = $this->usuario_model->get_listado_empresas();
		$tmpl = array('table_open' => '<table border="0" cellpadding="4"
				cellspacing="0" class="listado_table">');
		$this->table->set_template($tmpl);

		$data['SYS_MetaTitle'] = 'Tickets :: Panel';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Listado de Empresas del sistema';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/lista_empresas_view';
		$data['encabezado'] = 'EMPRESAS';
		$data['listado'] = $listado;
		$data['js'] = array('jQuery'); 

		$this->load->view('admin/main_admin_view', $data);
	}

	public function nuevaEmpresa($error_nombre = null, $error_cod = null, 
		$mensaje = null)
	{
		$data['SYS_MetaTitle'] = 'Tickets :: Empresas';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Crear empresa nueva';
		$data['ruta'] = 'admin/panel/nueva_empresa_do';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/nueva_empresa_view';
		$data['checkActiva'] = 'checked';
		$data['checkSoporte'] = 'checked';
		$data['encabezado'] = 'NUEVA EMPRESA';
		$data['js'] = array('jQuery'); 

		if (isset($error_nombre))
			$data['errorNombre'] = $error_nombre;

		if (isset($error_cod))
			$data['errorCod'] = $error_cod;

		if (isset($mensaje))
			$data['id_empresa'] = $mensaje;

		$this->load->view('admin/main_admin_view', $data);
	}

	public function nueva_empresa_do()
	{
		$codigoEmpresa = $this->input->post('codigo_empresa');
		$nombreEmpresa = $this->input->post('nombre_empresa');
		$nombreEmpresa = strtoupper($nombreEmpresa);
		$correoEmpresa = $this->input->post('correo_empresa');
		$activaEmpresa = 1;
		$soporteEmpresa = 1;

		if(!isset($_POST['activa']))
			$activaEmpresa = 0;
		if(!isset($_POST['soporte']))
			$soporteEmpresa = 0;

		$is_cod = $this->usuario_model->get_empresa_cod($codigoEmpresa);
		$is_nombre = $this->usuario_model->get_empresa_nombre($nombreEmpresa);

		$this->form_validation->set_rules('codigo_empresa', 'C&oacute;digo', 
			'trim|required|xss_clean|is_natural_no_zero');
		$this->form_validation->set_rules('nombre_empresa', 'Nombre', 'trim|required|xss_clean');
		$this->form_validation->set_rules('correo_empresa', 'Correo', 'trim|xss_clean|valid_email');
		$this->form_validation->set_message('required', 
			'Ingrese el "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('is_natural_no_zero', 
			'Ingrese un c&oacute;digo v&aacute;lido por favor');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (!$this->form_validation->run())
		{
			$this->nuevaEmpresa();
				
			return false;
		}

		if ($is_cod || $is_nombre)
		{
			if ($is_cod)
				$errCod = 'Ingrese un código diferente por favor';

			if ($is_nombre)
				$errNom = 'Ingrese un nombre diferente por favor';

			$this->nuevaEmpresa($errNom, $errCod);

			return false;
		}

		$data = array(
				'cod_empresa' => $codigoEmpresa,
				'nombre_empresa' => $nombreEmpresa,
				'activa' => $activaEmpresa,
				'soporte' => $soporteEmpresa
			);

		if (isset($correoEmpresa))
			$data['correo'] = $correoEmpresa;

			$idEmpresa = $this->usuario_model->insert_empresa($data);
			$idEmpresa = 'Se creó la empresa con el id ' . $idEmpresa;
				
			$this->nuevaEmpresa(null, null, $idEmpresa);

		return true;
	}

	public function edita_empresa_do()
	{
		$codigoEmpresa = $this->input->post('codigo_empresa');
		$nombreEmpresa = $this->input->post('nombre_empresa');
		$nombreEmpresa = strtoupper($nombreEmpresa);
		$correoEmpresa = $this->input->post('correo_empresa');
		$activaEmpresa = 1;
		$soporteEmpresa = 1;

		$idEmpresa = $this->usuario_model->get_id_empresa($codigoEmpresa);

		if(!isset($_POST['activa']))
			$activaEmpresa = 0;
		if(!isset($_POST['soporte']))
			$soporteEmpresa = 0;

		$this->form_validation->set_rules('nombre_empresa', 'Nombre', 
			'trim|required|xss_clean');
		$this->form_validation->set_rules('correo_empresa', 'Correo', 
			'trim|xss_clean|valid_email');
		$this->form_validation->set_message('required', 
			'Ingrese el "%s" por favor');
		$this->form_validation->set_message('xss_clean', 
			'El campo "%s" contiene un posible ataque XSS');
		$this->form_validation->set_message('is_natural_no_zero', 
			'Ingrese un c&oacute;digo v&aacute;lido por favor');
		$this->form_validation->set_message('valid_email', 
			'Ingrese un correo v&aacute;lido por favor');
		$this->form_validation->set_error_delimiters('<span class="error">', 
			'</span>');

		if (!$this->form_validation->run())
		{
			$this->editaEmpresa($idEmpresa);
				
			return false;
		}

		$data = array(
				'cod_empresa' => $codigoEmpresa,
				'nombre_empresa' => $nombreEmpresa,
				'activa' => $activaEmpresa,
				'soporte' => $soporteEmpresa
			);

		if (isset($correoEmpresa))
			$data['correo'] = $correoEmpresa;

		
		$mensaje = 'Se modificó la empresa con el id ' . $idEmpresa;
		$this->usuario_model->update_empresa($idEmpresa, $data);
		$this->editaEmpresa($idEmpresa, $mensaje);

		return true;
	}

	public function editaEmpresa($idEmpresa, $mensaje = null,
	 $error_cod = null, $error_nombre = null)
	{
		$empresa = $this->usuario_model->get_empresa_edicion($idEmpresa);

		$codEmpresa = $empresa[0]->cod_empresa;
		$nombreEmpresa = $empresa[0]->nombre_empresa;
		$correoEmpresa = $empresa[0]->correo;
		$activaEmpresa = $empresa[0]->activa;
		$soporteEmpresa = $empresa[0]->soporte;

		$data['SYS_MetaTitle'] = 'Tickets :: Empresas';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Editar empresa';
		$data['ruta'] = 'admin/panel/edita_empresa_do';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/edita_empresa_view'; 
		$data['cod_empresa'] = $codEmpresa;
		$data['nom_empresa'] = $nombreEmpresa;
		$data['cor_empresa'] = $correoEmpresa;

		if ($activaEmpresa == 1)
			$data['checkActiva'] = 'checked';

		if ($soporteEmpresa == 1)
			$data['checkSoporte'] = 'checked';

		$data['encabezado'] = 'EDITA EMPRESA';

		if (isset($error_nombre))
			$data['errorNombre'] = $error_nombre;

		if (isset($error_cod))
			$data['errorCod'] = $error_cod;

		if (isset($mensaje))
			$data['id_empresa'] = $mensaje;

		$data['js'] = array('jQuery');

		$this->load->view('admin/main_admin_view', $data);
	}

	public function reporte()
	{
		
		$data['SYS_MetaTitle'] = 'Tickets :: Reporte';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Reportes del sistema';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/reporte_empresas_view';
		$data['encabezado'] = 'REPORTES';

		$empresas = $this->usuario_model->get_empresas();

		foreach ($empresas as $depas => $valor) 
		{
			$select[$valor['empresa_id']] = $valor['nombre_empresa'];
		}

		$select['1001'] = 'TODAS';
		$data['empresas'] = $select;

		$estados = array(
				'1' => 'TODOS',
				'2' => 'abierto',
				'3' => 'esperando',
				'4' => 'cerrado'
			);

		$data['estados'] = $estados;
		$selectUsu['1001'] = 'TODOS';

		$usuarios = $this->usuario_model->get_staff_asignados();

		foreach ($usuarios as $key => $value) 
		{
			if (strlen($value['cod_staff'])>0)
				$selectUsu[$value['cod_staff']] = $value['cod_staff'];
		}

		$data['usuarios'] = $selectUsu;
		$data['js'] = array('jQuery', 'jQueryUI');

		$this->load->view('admin/main_admin_view', $data);
	}

	public function reporte_do()
	{
		$empresa = $this->input->post('empresas');
		$estado = $this->input->post('estados');
		$usuario = $this->input->post('usuarios');
		$fechaIni = $this->input->post('fechaInicial');
		$fechaFin = $this->input->post('fechaFinal');

		switch ($estado) 
		{
			case 2:
				$estado = 'abierto';
				break;
			case 3:
				$estado = 'esperando';
				break;
			case 4:
				$estado = 'cerrado';
				break;
			default:
				$estado = null;
				break;
		}

		// var_dump($empresa, $estado, $usuario, $fechaIni, $fechaFin);

		$listado = $this->ticket_model->get_reporte($empresa, $estado, 
			$usuario, $fechaIni, $fechaFin);
		// echo '<br>';
		// var_dump($listado->result_array()[0]["EMPRESA"]);

		if (isset($listado))
			$this->get_reporte($listado->result_array());

		/*$tmpl = array('table_open' => '<table border="0" cellpadding="4"
				cellspacing="0" class="listado_table">');
		$this->table->set_template($tmpl);*/

		$data['SYS_MetaTitle'] = 'Tickets :: Reporte';
		$data['SYS_metaKeyWords'] = 'sistema ticket n&g';
		$data['SYS_metaDescription'] = 'Reportes del sistema';
		$data['subMenu'] = 'admin/submenu_panel_view';
		$data['modulo'] = 'admin/reporte_empresas_view';
		$data['encabezado'] = 'REPORTES';
		// $data['listado'] = $listado;

		$this->load->view('admin/main_admin_view', $data);
	}

	private function get_reporte($arreglo)
	{
		set_include_path(base_url());
		require_once  'Classes/PHPExcel.php';

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Sistema de Tickets N&G")
							 ->setLastModifiedBy("Sistema de Tickets N&G")
							 ->setTitle("Reporte")
							 ->setSubject("Reporte")
							 ->setDescription("Reporte")
							 ->setKeywords("reporte")
							 ->setCategory("Reporte");

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'EMPRESA')
					->setCellValue('B1', 'ID')
					->setCellValue('C1', 'CREACION')
					->setCellValue('D1', 'REPORTA')
					->setCellValue('E1', 'ASUNTO')
					->setCellValue('F1', 'DETALLE')
					->setCellValue('G1', 'RESPUESTA')
					->setCellValue('H1', 'ATIENDE')
					->setCellValue('I1', 'ESTADO');

		$objPHPExcel->getActiveSheet()->fromArray($arreglo, null, 'A2');

		$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->
			getActiveSheet()->calculateWorksheetDimension());

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->
			setWidth(13.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->
			setWidth(6.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->
			setWidth(17.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->
			setWidth(21.57);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->
			setWidth(24.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->
			setWidth(24.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->
			setWidth(17.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->
			setWidth(15.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Is ')->
			setWidth(9.57);

		header('Content-Type: application/vnd.openxmlformats-officedocument
			.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="reporte.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 
			'Excel2007');
		$objWriter->save('php://output');
	}
}

/* End of file panel.php */
/* Location: ./application/controllers/admin/panel.php */