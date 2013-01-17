<div id="tabla_resumen">
	<p>Ticket #<?=$ticketID?> 
		<a href="<?=base_url()?>" title="Reload">
			<span class="refresh"> </span>
		</a>
	</p>
	<div class="contenedor_resumen">
		<table class="resumen_ticket">
			<tbody>
				<tr>
					<th>Estado:</th>
					<td><?=$estado_ticket?></td>
				</tr>
				<tr>
					<th>Departamento:</th>
					<td><?=$departamento_ticket?></td>
				</tr>
				<tr>
					<th>Fecha Creaci&oacute;n:</th>
					<td><?=$creacion_ticket?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="contenedor_resumen">
		<table class="resumen_ticket">
			<tbody>
				<tr>
					<th>Asignado a:</th>
					<td><?=$staff_name?></td>
				</tr>
				<tr>
					<th>Correo:</th>
					<td><?=$staff_correo?></td>
				</tr>
				<tr>
					<th>Tel&eacute;fono:</th>
					<td><?=$staff_tel?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="Asunto">
	Asunto: <?=$asunto?>
</div>
<div id="historial">
	
</div>
<div id="contenedor_respuesta">
	<form action="<?=base_url()?>tickets_usuario/agrega_mensaje" method="post" enctype="multipart/form-data">
	<input type="hidden" name="ticketID" value="<?=$ticketID?>">
	<label for="mensaje">Mensaje: <span class="rojo">*</span></label>
	<?php echo form_error('mensaje'); ?>
	<br>
	<textarea name="mensaje" class="mensaje"></textarea>
	<br>
	<input type="file" name="adjunto" size="20">
	<br>
	<input type="submit" value="RESPUESTA" class="boton">
	<input type="reset" value="RESET" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>
<div class="clear"></div>