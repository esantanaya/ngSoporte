<html>
	<head>
		<title><?=$SYS_metaTitle?></title>
	</head>
	<body id="cuerpo">
		<h1>Formulario de Pruebas</h1>
		<?php echo validation_errors(); ?>
		<?php echo form_open('pruebas_controller/manda_dato/'); ?>
		<label for="tipo_cliente">Tipo de Cliente:</label>
		<input type="text" name="tipo_cliente">

		<div><input type="submit" value="Probar"></div>
		<p>El formulario fue <?=$exito?></p>
		<div>
			<table></table>
		</div>
	</body>
</html>