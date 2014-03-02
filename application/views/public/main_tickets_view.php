<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
		<meta name="keywords" content="<?=$SYS_metaKeyWords?>">
        <meta name="description" content="><?=$SYS_metaDescription?>">
        <?php
        	$navegador = $this->input->user_agent();

        	$estilo = 'tickets';
        	if (strpos($navegador, 'MSIE'))
        		$estilo = 'ie/tickets';
        ?>
		<link rel="stylesheet" href="<?=base_url()?>css/<?=$estilo?>.css" type="text/css">
		<link rel="shortcut icon" href="<?=base_url()?>imagenes/iconos/logo.png">
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
					<li><a href="<?=base_url()?>tickets_usuario/lista_ticket">ESTADO DE TICKET</a></li>
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