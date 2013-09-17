<div class="contenedorBotones">
	<input type="button" class="boton" value="NUEVA" id="nuevaEmpresa">
</div>
<div id="listadoEmpresas">
	<?php echo $this->table->generate($listado); ?>
</div>
<script type="text/javascript">
	$('#nuevaEmpresa').on('click', function(evt) {
		document.location.href = '<?=base_url()?>admin/panel/nuevaEmpresa';
	});
</script>