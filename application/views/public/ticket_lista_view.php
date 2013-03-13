<div class="subMenu">
	<ul>
		<li><a href="<?=BASE_URL()?>tickets_usuario/lista_ticket/abierto" class="abierto">ABIERTOS</a></li>
		<li><a href="<?=BASE_URL()?>tickets_usuario/lista_ticket/esperando" class="espera">ESPERA</a></li>
		<li><a href="<?=BASE_URL()?>tickets_usuario/lista_ticket/cerrado" class="cerrado">CERRADOS</a></li>
		<li><a href="<?=base_url()?>tickets_usuario/lista_ticket" class="actualizar">TODOS</a></li>
	</ul>
</div>
<div id="listaTitulo">
	<?php echo form_open(base_url() . 'tickets_usuario/busqueda/'); ?>
	<input type="text" class="texto" name="query">
	<input type="submit" value="BUSCAR" class="boton">
</div>
<div class="linea"></div>
<div id="listado">
	<div id="estadistica"></div>
	<?php echo $this->table->generate($listado); ?>
</div>