<div id="tituloPreferencias" class="titulo">
	<h1><span class="rojo">>></span>PREFERENCIAS</h1>
</div>
<div class="linea"></div>
<div id="contentPreferencias">
	<?php 
		echo form_open('staff/cuenta/guarda_preferencias'); 

		foreach ($horarios as $key => $value) 
		{
			if ($horario == $value['id_horario'])
			{
				echo '<input type="radio" name="horario" id="' 
				. $value['id_horario'] . '" value="' 
				. $value['id_horario'] . '"' . '" checked="checked">'
				. '<label for="' . $value['id_horario'] . '">'
				. $value['des_horario'] . '</label><br>';
			}
			else
			{
				echo '<input type="radio" name="horario" id="' 
				. $value['id_horario'] . '" value="' 
				. $value['id_horario'] .'">'
				. '<label for="' . $value['id_horario'] . '">'
				. $value['des_horario'] . '</label><br>';
			}
		}
	?>
	<input type="submit" class="boton" value="GUARDAR">
	<br>
</div>