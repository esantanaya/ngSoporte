<div id="nuevoTitulo">
	<h1><span class="rojo">>></span>NUEVO TICKET</h1>
	<p>Por favor ingrese en el formuario los datos de su ticket</p>
</div>
<div id="linea"></div>
<div id="formContent">
	<?php 
		$this->load->model('usuario_model');
		$datos = $this->usuario_model->get_departamentos_id();

		foreach ($datos as $depas => $valor) {
			$select[$valor['dept_id']] = $valor['dept_name'];
		}	
	?>
	<?php echo validation_errors(); ?>
	<form action="<?=base_url()?>tickets_usuario/crea_ticket" method="post" enctype="">
		<table>
			<tr>
				<td>Departamento:</td>
				<td>
					<div id="alargaCelda">
						<div class="select">
							<?php 
								echo form_dropdown('departamento', $select, 
									'1'); 
							?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>Asunto:</td>
				<td><input type="text" name="asunto" class="texto" maxlength="50"></td>
			</tr>
			<tr>
				<td class="arriba">Mensaje:</td>
				<td>
					<textarea name="mensaje" cols="65" rows="10" class="mensaje" maxlength="1000"></textarea>
				</td>
			</tr>
			<tr>
				<td>Adjunto:</td>
				<td>
					<?php echo $error; ?>
					<input type="file" name="adjunto" size="20">
				</td>
			</tr>
			<tr>
				<td><div id="espacio"></div></td>
				<td>
					<input type="submit" value="ENVIAR TICKET" class="boton">
					<input type="reset" value="RESET" class="boton">
					<input type="button" value="CANCELAR" class="boton">
				</td>
			</tr>
		</table>
	</form>
</div>