<script type="text/javascript">
	$(document).ready(function() {
	    $( "#fechaInicial" ).datepicker({
	      defaultDate: "+1w",
	      changeMonth: true,
	      dateFormat: "yy-mm-dd",
	      // numberOfMonths: 3,
	      onClose: function( selectedDate ) {
	        $( "#fechaFinal" ).datepicker( "option", "minDate", selectedDate );
	      }
	    });
	    $( "#fechaFinal" ).datepicker({
	      defaultDate: "+1w",
	      changeMonth: true,
	      dateFormat: "yy-mm-dd",
	      //numberOfMonths: 3,
	      onClose: function( selectedDate ) {
	        $( "#fechaInicial" ).datepicker( "option", "maxDate", selectedDate );
	      }
	    });
	  });
</script>
<div id="form_container">
	<?=form_open(base_url() . 'admin/panel/reporte_do');?>
	<label for="empresas" class="izquierda labelBA">Empresa:</label>
	<div class="select izquierda" >
		<?php 
			echo form_dropdown('empresas', $empresas, '1001');
		?>
	</div>
	<label for="estados" class="izquierda labelBA">Estado:</label>
	<div class="select selectBA">
		<?php 
			echo form_dropdown('estados', $estados, '1');
		?>
	</div>
	<label for="usuarios" class="izquierda labelBA">Staff:</label>
	<div class="select selectBA">
		<?php 
			echo form_dropdown('usuarios', $usuarios, '1001');
		?>
	</div>
		<label for="fechaInicial">Fecha Inicial:</label>
		<input class="fecha texto" id="fechaInicial" name="fechaInicial" type="text">
		<label for="fechaFinal">Fecha Final:</label>
	<input class="fecha texto" id="fechaFinal" name="fechaFinal" type="text">
	<input type="submit" class="boton" value="Generar">
</div>

<!-- <div id="listado">

	<?php echo $this->table->generate($listado); ?>
</div> -->