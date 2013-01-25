<div class="subMenu">
	<ul>
		<li><a href="<?=base_url()?>staff/tickets">ABIERTOS</a></li>
		<li><a href="<?=base_url()?>staff/tickets/listado/esperando">ESPERA</a></li>
		<li><a href="<?=base_url()?>staff/tickets/listado/null/true">ATRASADOS</a></li>
		<li><a href="<?=base_url()?>staff/tickets/listado/cerrado">CERRADOS</a></li>
		<li><a href="">NUEVO</a></li>
	</ul>
</div>
<div id="listaTitulo">
	<label for="query">Query:</label>
	<input type="text" class="texto" name="query">
	<input type="submit" value="BUSCAR" class="boton">
</div>
<div class="linea"></div>
<div id="listado">
	<div id="estadistica"></div>
	<?php echo form_open(base_url() . 'staff/tickets/atrasa'); ?>
	<?php echo $this->table->generate($listado); ?>
	<input type="submit" value="ATRASA" class="boton">
</div>