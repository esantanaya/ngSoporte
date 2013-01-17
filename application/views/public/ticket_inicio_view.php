<div id="tituloInicio">
	<h1><span class="rojo">>></span>BIENVENIDO AL CENTRO DE SOPORTE</h1>
	<p>
		En este portal podr&aacute;s crear y dar seguimiento a tus tickets de soporte.
	</p>
</div>
<div class="linea"></div>
<div id="contenedorInicio">
	<div id="nuevoInicio">
		<h3 class="rojo">+ Crear nuevo ticket</h3>
		<div id="logoNuevo"></div>
		<p>
			Desde aqu&iacute; puedes crear un nuevo ticket, procura anotar el n&uacute;mero de ticket que te proporciona el sistema al final de la operaci&oacute;n, aunque se te enviar&aacute; un correo con la informaci&oacute;n.
		</p>
		<br>
		<div class="boton btIni">
			<a href="<?=base_url()?>tickets_usuario/nuevo">NUEVO TICKET</a>
		</div>
	</div>
	<div id="revisaInicio">
		<h3 class="rojo">+ Revisar estado de ticket</h3>
		<div id="logoRevisa"></div>
		<p>
			Aqu&iacute; puedes consultar el estado y el historial de los tickets que contin&uacute;an abiertos.
		</p>
		<br>
		<div id="form_busca">
			<?php echo form_open('tickets_usuario/edita_ticket'); ?>
			<?php 
				echo form_error('ticketID'); 
				if (form_error('ticketID') != '')
					echo '<br>';
				if ($error != '')
				{
					echo $error;
					echo '<br>';
				}
			?>
			<label for="ticketID"># Ticket:</label>
			<input type="text" name="ticketID" class="texto" maxlength="6" 
			value="<?php echo set_value('ticketID'); ?>">
			<br>
			<input type="submit" value="REVISAR ESTADO" class="boton">
		</div>
	</div>

</div>