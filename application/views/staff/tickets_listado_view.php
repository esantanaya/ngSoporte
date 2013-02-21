<div id="listaTitulo">
	<?php echo form_open(base_url() . 'staff/tickets/busqueda/'); ?>
	<label for="query">Query:</label>
	<input type="text" class="texto" name="query">
	<input type="submit" value="BUSCAR" class="boton">
</div>
<div class="linea"></div>
<div id="listado">
	<div id="estadistica"></div>
	
	<?php echo $this->table->generate($listado); ?>
	
</div>