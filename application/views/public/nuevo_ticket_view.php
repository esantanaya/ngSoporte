<script>
$(function() {
	$("#btnCancelar").click(function() {
		document.location.href='<?=base_url()?>';
	});
});
</script>
<div id="nuevoTitulo">
	<h1><span class="rojo">>></span>NUEVO TICKET</h1>
	<p>Por favor ingrese en el formuario los datos de su ticket</p>
	<?php 
		if ($errorGeneral != '')
		{
			echo '<span class="error">' . $errorGeneral . '</span>';
			echo '<br />';
		}
	 ?>
</div>
<div class="linea"></div>
<div id="formContent">
	<form action="<?=base_url()?>tickets_usuario/crea_ticket" method="post" enctype="multipart/form-data">
		<table>
			<tr>
				<td>Departamento:</td>
				<td>
					<div id="alargaCelda">
						<div class="select">
							<?php
								$js = 'id="departamento"';
								echo form_dropdown('departamento', $select, 
									'1', $js); 
							?>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td>Asunto:</td>
				<td>
					<?php 
						echo form_error('asunto'); 
						if (form_error('asunto') != '')
							echo '<br>';
					?>
					<input type="text" name="asunto" class="texto" maxlength="50" value="<?php echo set_value('asunto'); ?>">
				</td>
			</tr>
			<tr>
				<td class="arriba">Mensaje:</td>
				<td>
					<?php 
						echo form_error('mensaje'); 
						if (form_error('mensaje') != '')
							echo '<br>';
					?>
					<textarea name="mensaje" cols="65" rows="10" class="mensaje" maxlength="1000" value="<?php echo set_value('mensaje')?>"></textarea>
				</td>
			</tr>
			<tr>
				<td>Adjunto:</td>
				<td>
					<?php 
						if ($error != '')
						{
							echo '<span class="error">' . $error . '</span>';
							echo '<br />';
						}
					?>
					<input type="file" name="adjunto" size="20">
				</td>
			</tr>
			<tr>
				<td><div id="espacio"></div></td>
				<td>
					<input type="submit" value="ENVIAR TICKET" class="boton">
					<input type="reset" value="LIMPIAR" class="boton">
					<input type="button" value="CANCELAR" class="boton" id="btnCancelar">
				</td>
			</tr>
		</table>
	</form>
</div>