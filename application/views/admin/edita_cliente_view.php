<div id="titNewUser">	
	<h1><span class="rojo">>></span>EDICI&Oacute;N CLIENTES</h1>
</div>
<div class="linea"></div>
<?php 
	$mensaje = '<div id="mensajes"><p>' . $id_usuario . '<img src="' 
				. base_url() 
				. 'imagenes/iconos/staff/IC_exitoso.png"></p></div>';
	if (! empty($id_usuario))
		echo $mensaje;
?>
<div id="form_container">
	<?php echo form_open('admin/usuarios/guarda_cambios_cliente'); ?>
	<input type="hidden" name="cod_usuario" value="<?=$cod_usuario?>">
	<table cellspacing="0" cellpadding="0" border="0" class="ticket_form">
		<tbody>
			<tr>
				<td colspan="2" class="header">Cuenta de Cliente</td>
			</tr>
			<tr>
				<td colspan="2" class="subHeader">Informaci&oacute;n</td>
			</tr>
			<tr>
				<td class="table_title">Empresa:</td>
				<td>
					<div class="alarga_celda">
						<div class="select">
							<?php 
								echo form_dropdown('empresa', $empresas,
									 				$empresaActual); 
							?>
						</div>	
					</div>
				</td>
			</tr>
			<tr>
				<td class="table_title">Nombre(s):</td>
				<td>
					<input type="text" class="texto" name="nombre_usuario" value="<?=$nombre?>">
					<span class="rojo">*</span>
					<?php echo form_error('nombre_usuario'); ?>
			</tr>
			<tr>
				<td class="table_title">Apellido Paterno:</td>
				<td>
					<input type="text" class="texto" name="apellido_paterno" value="<?=$apPaterno?>">
					<span class="rojo">*</span>
					<?php echo form_error('apellido_paterno'); ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Apellido Materno:</td>
				<td>
					<input type="text" class="texto" name="apellido_materno" value="<?=$apMaterno?>">
					<span class="rojo">*</span>
					<?php echo form_error('apellido_materno'); ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Correo:</td>
				<td>
					<input type="text" class="texto" name="mail_usuario" value="<?=$correo?>">
					<span class="rojo">*</span>
					<?php echo form_error('mail_usuario') ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Tel&eacute;fono:</td>
				<td>
					<input type="text" class="texto" name="tel_usuario" value="<?=$tel?>">
					&nbsp; 
					<label for="ext_tel">Ext</label>
					<input type="text" class="texto_chico" name="ext_tel" value="<?=$ext?>">
				</td>
			</tr>
			<tr>
				<td class="table_title">Movil:</td>
				<td>
					<input type="text" class="texto" name="cel_usuario" value="<?=$cel?>">
				</td>
			</tr>
			<tr>
				<td class="table_title">Contrase&ntilde;a:</td>
				<td>
					<input type="password" class="texto" name="pass_usuario">

					<?php
						 echo '&nbsp; <span class="error">' . $clave 
							. '</span>';
					?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Confirma:</td>
				<td>
					<input type="password" class="texto" name="confirma_pass">
				</td>
			</tr>
			<tr>
				<td class="table_title">Forzar cambio de contrase&ntilde;a:</td>
				<td class="centrado">
					<input type="checkbox" name="cambia_pass" <?=$cambioPass?>>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="header">Permisos y configuraci&oacute;n de la cuenta</td>
			</tr>
			<tr>
				<td colspan="2" class="subHeader">Permisos</td>
			</tr>
			<tr>
				<td class="table_title">Estado:</td>
				<td class="centrado">
					<input type="radio" class="circulo" name="activo" id="activo" value="activo" <?=$activoCheck?>>
					<strong>Activo</strong>
					&nbsp;
					<input type="radio" class="circulo" name="activo" id="bloqueado" value="bloqueado" <?=$bloqueadoCheck?>>
					<strong>Bloqueado</strong>
				</td>
			</tr>
			<tr>
			</tr>
		</tbody>
	</table>
	<input type="submit" value="GUARDAR" class="boton">
	<input type="reset" value="LIMPIAR" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>