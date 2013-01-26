<div id="titNewUser">	
	<h1><span class="rojo">>></span>NUEVO USUARIO</h1>
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
	<?php echo form_open('admin/usuarios/crea_usuario'); ?>
	<table cellspacing="0" cellpadding="0" border="0" class="ticket_form">
		<tbody>
			<tr>
				<td colspan="2" class="header">Cuenta de Usuario</td>
			</tr>
			<tr>
				<td colspan="2" class="subHeader">Informaci&oacute;n</td>
			</tr>
			<tr>
				<td class="table_title">Usuario:</td>
				<td>
					<input type="text" class="texto" name="cod_usuario">
					<span class="rojo">*</span>
					<?php 
						echo '&nbsp;<span class="error">' . $cod_usuario 
							. '</span>';
						echo form_error('cod_usuario');
					?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Departamento:</td>
				<td>
					<div class="alarga_celda">
						<div class="select">
							<?php 
								echo form_dropdown('departamento', $depts,
									 '1'); 
							?>
						</div>	
					</div>
				</td>
			</tr>
			<tr>
				<td class="table_title">Nivel:</td>
				<td>
					<div class="alarga_celda">
						<div class="select">
							<?php 
								echo form_dropdown('nivel', $niveles, '1'); 
							?>	
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="table_title">Nombre(s):</td>
				<td>
					<input type="text" class="texto" name="nombre_usuario">
					<span class="rojo">*</span>
					<?php echo form_error('nombre_usuario'); ?>
			</tr>
			<tr>
				<td class="table_title">Apellido Paterno:</td>
				<td>
					<input type="text" class="texto" name="apellido_paterno">
					<span class="rojo">*</span>
					<?php echo form_error('apellido_paterno'); ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Apellido Materno:</td>
				<td>
					<input type="text" class="texto" name="apellido_materno">
					<span class="rojo">*</span>
					<?php echo form_error('apellido_materno'); ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Correo:</td>
				<td>
					<input type="text" class="texto" name="mail_usuario">
					<span class="rojo">*</span>
					<?php echo form_error('mail_usuario') ?>
				</td>
			</tr>
			<tr>
				<td class="table_title">Tel&eacute;fono:</td>
				<td>
					<input type="text" class="texto" name="tel_usuario">
					&nbsp; 
					<label for="ext_tel">Ext</label>
					<input type="text" class="texto_chico" name="ext_tel">
				</td>
			</tr>
			<tr>
				<td class="table_title">Movil:</td>
				<td>
					<input type="text" class="texto" name="cel_usuario">
				</td>
			</tr>
			<tr>
				<td class="table_title">Firma:</td>
				<td>
					<textarea name="firma_usuario" class="mensaje"></textarea>
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
					<input type="checkbox" name="cambia_pass" value="Requerir cambio de contrase&ntilde;a" checked>
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
					<input type="radio" class="circulo" name="activo" id="activo" value="activo" checked>
					<strong>Activo</strong>
					&nbsp;
					<input type="radio" class="circulo" name="activo" id="bloqueado" value="bloqueado">
					<strong>Bloqueado</strong>
				</td>
			</tr>
			<tr>
				<td class="table_title">Listado:</td>
				<td>
					<input type="checkbox" name="listado" checked> El usuario es mostrado en el directorio
				</td>
			</tr>
			<tr>
				<td class="table_title curva_derecha">Modo Inactivo</td>
				<td>
					<input type="checkbox" name="vacacion"> El usuario no recibirá asignación
				</td>
			</tr>
			<tr>
			</tr>
		</tbody>
	</table>
	<input type="submit" value="CREAR" class="boton">
	<input type="reset" value="RESET" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>