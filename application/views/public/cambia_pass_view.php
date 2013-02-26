<html>
	<head>
		<title>Tickets :: Cambia tu contrase&ntilde;a</title>
		<link rel="stylesheet" href="<?=base_url()?>css/pass.css">
		<link rel="shortcut icon" href="<?=base_url()?>imagenes/iconos/logo.png">
	</head>
	<body>
		<div id="wrapper">
			<div id="top"></div>
			<div id="menu"></div>
			<div id="content">
				<div id="titulo">
					<h1><span class="rojo">>></span>CAMBIAR CONTRASE&Ntilde;A</h1>
				</div>
				<div class="linea"></div>
				<div id="form">
					<?php echo form_open('cambia_pass/enviar'); ?>
					<?php 
						if (!empty($error))
						{
							echo '<span class="error">' . $error 
								 . '</span><br />';
						}
						else
						{
							echo form_error('pass') . '<br />';
						}
					?>
					<label for="pass">Nueva contrase&ntilde;a:</label>
					<input type="password" name="pass" class="texto" id="pass">
					<br>
					<label for="confirma">Repetir contrase&ntilde;a:</label>
					<input type="password" name="confirma" class="texto" id="confirma">
					<br>
					<input type="submit" value="CAMBIAR" class="boton" name="envia">
				</div>
			</div>
			<div id="footer">Copyright &copy; 2013 N&G  All Rights Reserved.</div>
		</div>
	</body>
</html>