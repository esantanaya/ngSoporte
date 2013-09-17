<script type="text/javascript">
	$(document).ready(function() {
	    $('#busquedaAvanzada').on('click', function(event) {
	    	event.preventDefault();
	    	$('#rangoFechas').slideToggle();
	    });
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
<div id="listaTitulo">
	<?php echo form_open(base_url() . 'staff/tickets/busqueda/'); ?>
	<label for="query">Query:</label>
	<input type="text" class="texto" name="query" id="query">
	<div id="rangoFechas">
		<label for="empresas" class="izquierda labelBA">Empresa:</label>
		<div class="select izquierda" >
			<?php 
				echo form_dropdown('empresas', $empresas, '1001');
			?>
		</div>
		<label for="empresas" class="izquierda labelBA">Estado:</label>
		<div class="select selectBA">
			<?php 
				echo form_dropdown('estados', $estados, '1');
			?>
		</div>
  		<label for="fechaInicial">Fecha Inicial:</label>
  		<input class="fecha texto" id="fechaInicial" name="fechaInicial" type="text">
  		<label for="fechaFinal">Fecha Final:</label>
    	<input class="fecha texto" id="fechaFinal" name="fechaFinal" type="text">
  </div>
	<input type="submit" value="BUSCAR" class="boton">
	<a href="#" id="busquedaAvanzada">Busqueda Avanzada</a>
</div>
<div class="linea"></div>
<div id="listado">
	<div id="estadistica"></div>
	
	<?php echo $this->table->generate($listado); ?>
	
</div>