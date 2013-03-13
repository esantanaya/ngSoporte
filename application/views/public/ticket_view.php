<div id="tabla_resumen">
	<p>Ticket #<?=$ticketID?> 
		<a href="<?=base_url()?>tickets_usuario/entra_edita_ticket/<?=$ticketID?>" title="Recargar"> 
			<img src="<?=base_url()?>imagenes/iconos/tickets/ic_refresh.png" alt="Recargar">
		</a>
		<?php 
			if ($reabrir)
			{
				$cadena = base_url() . 'tickets_usuario/reabre/' . $ticketID;
				$icono = '<a href="' . $cadena . '" title="Reabrir">' 
						. '<img src="' . base_url() 
						. 'imagenes/iconos/tickets/ic_resueltos.png"' 
						. 'alt="re-abrir"></a>';

				echo '&nbsp;' . $icono;
			}
		?>
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
	<div class="historial"><p>Historial</p></div>
	<?php
		echo $this->table->generate();
	?>
</div>
<div id="contenedor_respuesta">
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
	<input type="submit" value="ENVIAR" class="boton">
	<input type="reset" value="LIMPIAR" class="boton">
	<input type="button" value="CANCELAR" class="boton">
</div>
<div class="clear"></div>