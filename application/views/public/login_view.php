<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<link rel="stylesheet" href="<?=base_url()?>css/login.css" type="text/css">
		<link rel="shortcut icon" href="<?=base_url()?>imagenes/iconos/logo.png">
	</head>
	<body>
		<?php
        	$navegador = $this->input->user_agent();

        	$estilo = 'invisible';
        	if (strpos($navegador, 'MSIE'))
        		$estilo = '';
        ?>
		<div id="wrapper">
			<div id="logo">
			</div>
			<p id="label_login">
				<?php 
					if (isset($error) || $this->session->flashdata('error'))
					{
						echo $this->lang->line($this->session->flashdata('error'));
					}
					else
					{
						echo $mensaje_login;
					}
				?>
			</p>
			<br>
			<div id="formulario">
				<?php echo form_open('sesion/login/login/login'); ?>
				<?php 
					echo form_error('usuario'); 
					if (form_error('usuario') != '')
						echo '<br>';
				?>
				<label for="usuario">Usuario:</label>
				<input type="text" name="usuario" class="texto" value="<?php
				 	echo set_value('usuario');
				 ?>">
				<br>
				<?php 
					echo form_error('pass'); 
					if (form_error('pass') != '')
						echo '<br>';
				?>
				<label for="pass">Contrase&ntilde;a:</label>
				<input type="password" name="pass" class="texto" value="<?php
				 	echo set_value('pass');
				 ?>">
				<br>
				<div id="boton">
					<input type="submit" value="INGRESAR" class="entrar">
				</div>
			</div>
		</div>
		<div style="height: 250px;"></div>
		<div class="clear"></div>
		<div id="footer">
			Copyright &copy; 2013 N&G  All Rights Reserved.
		</div>
		<div id="navegadores" class=<?=$estilo?>>
			<h6>Optimizado para <a href="https://www.google.com/intl/es/chrome/business/browser/">Google Chrome</a> y <a href="http://www.mozilla.org/es-MX/firefox/fx/">Firefox</a></h6>
		</div>
	</body>
</html>