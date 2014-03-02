<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
		<meta name="keywords" content="<?=$SYS_metaKeyWords?>">
        <meta name="description" content="><?=$SYS_metaDescription?>">
		<link rel="stylesheet" href="<?=base_url()?>css/admin.css" type="text/css">
		<link rel="stylesheet" href="<?=base_url()?>css/smoothness/jquery-ui.min.css" type="text/css">
		<link rel="stylesheet" href="<?=base_url()?>css/smoothness/jquery.ui.theme.css" type="text/css">
		<link rel="shortcut icon" href="<?=base_url()?>imagenes/iconos/logo.png">
		<?php
        //css dinámicos
        if(isset($css)){
            for($i=0;$i<count($css);$i++){
                echo "<link rel=\"stylesheet\" href=\"" . base_url() . "css/" . $css[$i] . ".css\">\n";                
            }
        }
        ?>
		<?php
        //js dinámicos
        if(isset($js)){
            for($i=0;$i<count($js);$i++){
                echo "<script type=\"text/javascript\" src=\"" . base_url() . "js/" . $js[$i] . ".js\"></script>\n";                
            }
        }
        ?>
	</head>
	<body>
		<div id="wrapper">
			<div id="top">
				<?php 
					$usuario_loggeado = $this->session->userdata('nombre');
					$nivel_usuario = $this->session->userdata('nivel');
				?>
				<ul>
					<li>
						Bienvenido, <strong><?=$usuario_loggeado?></strong>
					</li>
					<li>
						<?php 
							$m_usuario = 'Staff';
							$cambio_nivel = '<a href="">' . $m_usuario 
											. '</a>';
							if ($nivel_usuario == 1 AND $m_usuario == 'Staff')
							{
								$cambio_nivel = '<a href="' . base_url() 
												. 'staff/tickets">Staff</a>';
							}	
							
							echo $cambio_nivel;
						?>
					</li>		
					<li>
						<a href="<?=base_url()?>sesion/logout/login">
							Cerrar sesi&oacute;n
						</a>
					</li>
				</ul>
			</div>
			<div id="menu">
				<ul>
					<li><a href="<?=base_url()?>admin/panel/listaEmpresas">PANEL</a></li>
					<li><a href="<?=base_url()?>admin/config">CONFIGURACI&Oacute;N</a></li>
					<li><a href="<?=base_url()?>admin/usuarios/lista">USUARIOS</a></li>
				</ul>
			</div>
			<div id="content">
				<?php 
					$this->load->view($subMenu);
					$this->load->view($modulo);
				?>
			</div>
			<div id="footer">
				Copyright &copy; 2013 N&G  All Rights Reserved.
			</div>
			<div class="clear"></div>
		</div>
	</body>
</html>