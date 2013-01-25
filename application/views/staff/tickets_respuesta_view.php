<div id="tabla_estado">
	<p>Ticket #<?=$ticketID?> 
		<a href="<?=base_url()?>staff/tickets/responde_ticket/<?=$ticketID?>" title="Recargar"> 
			<img src="<?=base_url()?>imagenes/iconos/tickets/ic_refresh.png" alt="Recargar">
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
<div id="asunto">
	Asunto: <?=$asunto?>
</div>
<div id="tabla_asigna">
	<div class="contenedor_resumen">
		<table class="resumen_ticket">
			<tbody>
				<tr>
					<th>Empresa:</th>
					<td><?=$empresa?></td>
				</tr>
				<tr>
					<th>&Uacute;ltima respuesta:</th>
					<td><?=$fecha_respuesta?></td>
				</tr>
				<tr>
					<th>Fecha vecimiento:</th>
					<td><?=$fecha_vencimiento?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="contenedor_resumen">
		<table class="resumen_ticket">
			<tbody>
				<tr>
					<th>Quien asigna:</th>
					<td><?=$usuario_name?></td>
				</tr>
				<tr>
					<th>Correo:</th>
					<td><?=$usuario_correo?></td>
				</tr>
				<tr>
					<th>Tel&eacute;fono:</th>
					<td><?=$usuario_tel?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="acciones"></div>
<div id="notas"></div>
<div id="historial">
	<?php
		echo $this->table->generate();
	?>
</div>
<div id="respuesta">
	<form action="<?=base_url()?>tickets_usuario/agrega_mensaje" method="post" enctype="multipart/form-data">
	<input type="hidden" name="ticketID" value="<?=$ticketID?>">
	<label for="mensaje">Mensaje: <span class="rojo">*</span></label>
	<?php echo form_error('mensaje'); ?>
	<br>
	<textarea name="mensaje" class="mensaje"></textarea>
	<br>
	<?php echo '<span class="error">' . $error . '</span>'; ?>
	<input type="file" name="adjunto" size="20" id="adjunto">
	<br>
	<input type="submit" value="RESPUESTA" class="boton">
	<input type="reset" value="RESET" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>
