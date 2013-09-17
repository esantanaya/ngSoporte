<?php 
	$mensaje = '<div id="mensajes"><p>' . $id_empresa . '<img src="' 
				. base_url() 
				. 'imagenes/iconos/staff/IC_exitoso.png"></p></div>';
	if (! empty($id_empresa))
		echo $mensaje;
?>
<div id="form_container">
	<?php echo form_open($ruta); ?>
	<table id="tabla_empresa" cellspacing="0" cellpadding="0" border="0">
		<thead>
			<tr>
				<th colspan="2" class="header">Empresas</th>
			</tr>
			<tr>
				<th colspan="2" class="subHeader">Información</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="table_title">
					<label for="codigo_empresa">Código:</label>
				</td>
				<td>
					<input type="text" class="texto_chico" name="codigo_empresa" id="codigo_empresa" value="<?php

							if (isset($cod_empresa))
								echo trim($cod_empresa);

							echo set_value('codigo_empresa');
						?>">
					<span class="rojo">*</span>
					<?php
						echo form_error('codigo_empresa');

						if (isset($errorCod))
							echo '<span class="error">' . $errorCod . 
								 '</span>';
					?>
				</td>
			</tr>
			<tr>
				<td class="table_title">
					<label for="nombre_empresa">Nombre:</label>
				</td>
				<td>
					<input type="text" class="texto" name="nombre_empresa" id="nombre_empresa" value="<?php

							if (isset($nom_empresa)) 
								echo $nom_empresa;

							echo set_value('nombre_empresa');
						?>"><span class="rojo">*</span>
					<?php 
						echo form_error('nombre_empresa');

						if (isset($errorNombre))
							echo '<span class="error">' . $errorNombre . 
								 '</span>';
					?>
				</td>
			</tr>
			<tr>
				<td class="table_title">
					<label for="correo_empresa">Correo:</label>
				</td>
				<td>
					<input type="text" class="texto" name="correo_empresa" id="correo_empresa" value="<?php

							if (isset($cor_empresa))
								echo $cor_empresa;

							echo set_value('correo_empresa');
						?>">
					<?=form_error('correo_empresa');?>
				</td>
			</tr>
			<tr>
				<td class="table_title curva_izquierda">
					<label>Estado:</label>
				</td>
				<td>
					<input type="checkbox" name="activa" id="activa" 
						<?php 
							if (isset($checkActiva))
								echo $checkActiva;
						?> 
					/>
					<label for="activa"><strong>Activa</strong></label>
					<input type="checkbox" name="soporte" id="soporte" 
						<?php 
							if (isset($checkSoporte))
								echo $checkSoporte;
						?> 
					/>
					<label for="soporte"><strong>Soporte</strong></label>
				</td>
			</tr>
		</tbody>
	</table>	
	<input type="submit" value="GUARDAR" class="boton">
	<input type="reset" value="LIMPIAR" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>
<script type="text/javascript">
	$('.boton').last().on('click', function(event) {
		document.location.href = '<?=base_url()?>admin/panel'
	});
</script>