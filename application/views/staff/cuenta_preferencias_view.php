<div id="tituloPreferencias">
	<h1><span class="rojo">>></span>PREFERENCIAS</h1>
</div>
<div class="linea"></div>
<div id="contentPreferencias">
	<?php echo form_open('staff/cuenta/guarda_preferencias'); ?>
	<input type="radio" class="horario" name="horario" id="13" value="13"
	<?=$horario['uno']?>><strong>13:00:00 a 15:00:00</strong>
	<br>
	<input type="radio" class="horario" name="horario" id="35" value="35"
	<?=$horario['dos']?>><strong>15:00:00 a 17:00:00</strong>
	<br>
	<input type="submit" class="boton" value="GUARDAR">
	<br>
</div>