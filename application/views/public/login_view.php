<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<link rel="stylesheet" href="<?=base_url()?>css/login.css" type="text/css">
		<link rel="shortcut icon" href="<?=base_url()?>imagenes/ic_nuevoticket.png">
	</head>
	<body>
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
				<?php echo validation_errors(); ?>
				<?php echo form_open('sesion/login/login/login'); ?>
				<label for="usuario">Usuario:</label>
				<input type="text" name="usuario" class="texto">
				<br>
				<label for="pass">Contrase&ntilde;a:</label>
				<input type="password" name="pass" class="texto">
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
	</body>
</html>