<div id="tituloPass" class="titulo">
	<h1><span class="rojo">>></span>CAMBIAR CONTRASE&Ntilde;A</h1>
</div>
<div class="linea"></div>
<div id="formPass">
	<?php 
		echo form_open('staff/cuenta/guarda_pass');  
		if (!empty($error))
		{
			echo '<span class="error">' . $error 
				 . '</span><br />';
		}
	?>
	<table id="tablaPass" class="noAdorno">
		<tbody>
			<tr>
				<td class="titulo">
					<label for="old_pass">Contrase&ntilde;a actual:</label>
				</td>
				<td>
					<input type="password" class="texto" name="old_pass" id="old_pass"><span class="rojo">*</span>
					<?php 
						if ($pass_error != '')
							echo $pass_error;
						echo form_error('old_pass');
					?>
				</td>
			</tr>
			<tr>
				<td class="titulo">
					<label for="pass">Nueva contrase&ntilde;a:</label>
				</td>
				<td>
					<input type="password" name="pass" class="texto" id="pass"><span class="rojo">*</span>
					<?php 
						if ($pass_conf != '')
							echo $pass_conf;
						echo form_error('pass');
					?>
				</td>
			</tr>
			<tr>
				<td class="titulo">
					<label for="confirma">Repetir contrase&ntilde;a:</label>
				</td>
				<td>
					<input type="password" name="confirma" class="texto" id="confirma"><span class="rojo">*</span>
					<?=form_error('confirma')?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="CAMBIAR" class="boton" name="envia"></td>
			</tr>
		</tbody>
	</table>
	
	
	<br>
	
	
	<br>
	
</div>