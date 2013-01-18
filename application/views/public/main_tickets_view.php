<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
		<meta name="keywords" content="<?=$SYS_metaKeyWords?>">
        <meta name="description" content="><?=$SYS_metaDescription?>">
		<link rel="stylesheet" href="<?=base_url()?>css/tickets.css" type="text/css">
		<link rel="shortcut icon" href="../imagenes/iconos/logo.png">
	</head>
	<body>
		<div id="wrapper">
			<div id="top">
				<?php 
					$usuario_loggeado = $this->session->userdata('nombre');
				?>
				<ul>
					<li>
						Bienvenido, <strong><?=$usuario_loggeado?></strong>
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
					<li><a href="<?=base_url()?>tickets_usuario">INICIO</a></li>
					<li><a href="<?=base_url()?>tickets_usuario/nuevo">NUEVO TICKET</a></li>
					<li><a href="">ESTADO DE TICKET</a></li>
				</ul>
			</div>
			<div id="content">
				<?php 
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