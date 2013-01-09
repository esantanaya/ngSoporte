<html>
	<head>
		<title><?=$SYS_metaTitle?></title>
	</head>
	<body id="cuerpo">
		<h1>Formulario de Pruebas</h1>
		<?php echo validation_errors(); ?>
		<?php echo form_open('pruebas_controller/manda_dato/'); ?>
		<label for="tipo_usuario">Tipo Usuario:</label>
		<input type="text" name="tipo_usuario">
		<br>
		<label for="usuario">Usuario:</label>
		<input type="text" name="usuario">
		<br>
		<label for="nombre">Nombre:</label>
		<input type="text" name="nombre">
		<br>
		<label for="apellido_paterno">Apellido Paterno:</label>
		<input type="text" name="apellido_paterno">
		<br>
		<label for="apellido_materno">Apellido Materno</label>
		<input type="text" name="apellido_materno">
		<br>
		<label for="password">Contrase&ntilde;a</label>
		<input type="password" name="password">
		<br>
		<label for="mail">Correo:</label>
		<input type="text" name="mail">
		<br>
		<label for="tel">Tel&eacute;fono:</label>
		<input type="text" name="tel">
		<br>
		<label for="movil">Movil:</label>
		<input type="text" name="movil">
		<br>
		<label for="firma">Firma:</label>
		<br>
		<textarea name="firma" id="" cols="30" rows="10"></textarea>

		<div><input type="submit" value="Probar"></div>
		<p>Mira un n&uacute;mero aleatorio: <?=$num?></p>
		<p>Aca va el id de usuario <?=$temp_id_usuario?></p>
		<p>La fecha es <?=$fecha?></p>
		<p><?=$id_usuario?></p>
		<div>
			<table></table>
		</div>
	</body>
</html>