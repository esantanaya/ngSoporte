<div id="cuentaTitulo" class="titulo">
	<h1><span class="rojo">>></span>MI PERFIL</h1>
</div>
<div class="linea"></div>
<div id="formularioPerfil">
	<?php echo form_open('staff/cuenta/guarda_cambios'); ?>
	<table class="noAdorno">
		<tbody>
			<tr>
				<td>Nombre(s):</td>
				<td>
					<input type="text" class="texto" name="nombre_usuario" value="<?=$datos['nombre']?>"><span class="rojo">*</span>
					<?=form_error('nombre_usuario')?>
				</td>
			</tr>
			<tr>
				<td>Apellido Paterno:</td>
				<td>
					<input type="text" class="texto" name="apellido_paterno" value="<?=$datos['apPaterno']?>"><span class="rojo">*</span><?=form_error('apellido_paterno')?>
				</td>
			</tr>
			<tr>
				<td>Apellido Materno:</td>
				<td>
					<input type="text" class="texto" name="apellido_materno" value="<?=$datos['apMaterno']?>"><span class="rojo">*</span><?=form_error('apellido_materno')?>
				</td>
			</tr>
			<tr>
				<td>Correo:</td>
				<td>
					<input type="text" class="texto" name="mail_usuario" value="<?=$datos['correo']?>"><span class="rojo">*</span>
					<?=form_error('mail_usuario')?>
				</td>
			</tr>
			<tr>
				<td>Tel&eacute;fono:</td>
				<td>
					<input type="text" class="texto" name="tel_usuario" 
					value="<?=$datos['tel']?>">
					<label for="ext_tel">Ext:</label>
					<input class="texto_chico" type="text" name="ext_tel" value="<?=$datos['ext']?>">
				</td>
			</tr>
			<tr>
				<td>Firma:</td>
				<td>
					<textarea class="mensaje" name="firma_usuario"><?=
					$datos['firma']?></textarea>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="submit" class="boton" value="GUARDAR">
					<input type="reset" class="boton" value="LIMPIAR">
				</td>
			</tr>
		</tbody>
	</table>
</div>