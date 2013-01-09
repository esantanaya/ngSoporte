<html>
	<head>
		<title><?=$SYS_MetaTitle?></title>
		<link rel="stylesheet" href="<?=base_url()?>css/login.css" type="text/css">
	</head>
	<body>
		<div id="wrapper">
			<div id="logo">
			</div>
			<p id="label_login">
				<?=$mensaje_login?>
			</p>
			<br>
			<div id="formulario">
				<?php echo validation_errors(); ?>
				<?php echo form_open('public/login/'); ?>
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
		<div id="footer">
			Copyright &copy; 2013 Sistema de Tickets  All Rights Reserved.
		</div>
	</body>
</html>