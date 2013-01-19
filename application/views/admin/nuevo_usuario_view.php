<script>
	function check()
	{
		document.getElementById("red").checked=true
	}
	function uncheck()
	{
		document.getElementById("red").checked=false
	}
</script>
<div id="usuarioContainer">
	<?php echo form_open('admin/usuarios/nuevo'); ?>
	<table>
		<tbody>
			<tr>
				<td colspan="2" class="header">Cuenta de Usuario</td>
			</tr>
			<tr>
				<td colspan="2" class="subHeader">Informaci&oacute;n</td>
			</tr>
			<tr>
				<td>Usuario:</td>
				<td>
					<input type="text" class="texto" name="cod_usuario">
					<span class="rojo">*</span>
				</td>
			</tr>
			<tr>
				<td>Departamento:</td>
				<td>
					<?php 
						echo form_dropdown('departamento', $depts, '1'); 
					?>
				</td>
			</tr>
			<tr>
				<td>Nivel:</td>
				<td>
					<?php 
						echo form_dropdown('nivel', $niveles, '1'); 
					?>
				</td>
			</tr>
			<tr>
				<td>Nombre(s):</td>
				<td>
					<input type="text" class="texto" name="nombre_usuario">
					<span class="rojo">*</span>
			</tr>
			<tr>
				<td>Apellido Paterno:</td>
				<td>
					<input type="text" class="texto" name="apellido_paterno">
					<span class="rojo">*</span>
				</td>
			</tr>
			<tr>
				<td>Apellido Materno:</td>
				<td>
					<input type="text" class="texto" name="apellido_materno">
					<span class="rojo">*</span>
				</td>
			</tr>
			<tr>
				<td>Correo:</td>
				<td>
					<input type="text" class="texto" name="mail_usuario">
					<span class="rojo">*</span>
				</td>
			</tr>
			<tr>
				<td>Tel&eacute;fono:</td>
				<td>
					<input type="text" class="texto" name="tel_usuario">
					&nbsp; 
					<label for="ext_tel">Ext</label>
					<input type="text" class="texto_chico" name="ext_tel">
				</td>
			</tr>
			<tr>
				<td>Movil:</td>
				<td>
					<input type="text" class="texto" name="cel_usuario">
				</td>
			</tr>
			<tr>
				<td>Firma:</td>
				<td>
					<textarea name="firma_usuario" class="texto_grande"></textarea>
				</td>
			</tr>
			<tr>
				<td>Contrase&ntilde;a:</td>
				<td>
					<input type="text" class="texto" name="pass_usuario">
				</td>
			</tr>
			<tr>
				<td>Confirma:</td>
				<td>
					<input type="text" class="texto" name="confirma_pass">
				</td>
			</tr>
			<tr>
				<td>Forzar cambio de contrase&ntilde;a:</td>
				<td>
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
				<td>Estado:</td>
				<td>
					<input type="radio" name="activo" id="activo" checked>
					<strong>Activo</strong>
					&nbsp;
					<input type="radio" name="activo" id="bloqueado">
					<strong>Bloqueado</strong>
				</td>
			</tr>
			<tr>
				<td>Listado:</td>
				<td>
					<input type="checkbox" name="listado" checked> El usuario es mostrado en el directorio
				</td>
			</tr>
			<tr>
				<td>Modo Inactivo</td>
				<td>
					<input type="checkbox" name="vacacion"> El usuario no recibirá asignación
				</td>
			</tr>
			<tr>
				<td colspan="2" class="botones">
					<input type="submit" value="CREAR" class="boton">
					<input type="reset" value="RESET" class="boton">
					<input type="button" value="CANCELAR" class="boton">
				</td>
			</tr>
		</tbody>
	</table>
</div>