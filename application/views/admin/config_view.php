<div id="content">
	<?=form_open(base_url() . 'admin/config/guardaHorario');?>
	<label for="inicio">Inicio:</label>
	<input type="text" name="inicio" id="inicio" class="texto" value="<?=$inicio?>">
	<label for="fin">Fin:</label>
	<input type="text" name="fin" id="fin" class="texto" value="<?=$fin?>">
	<input type="submit" value="Guardar" class="boton">
</div>
<script type="text/javascript">
	var ini;
	$('#inicio').timepicker({'timeFormat':'H:i:s'});
	
	$('#fin').focusin(function() {
		ini = $('#inicio').val();
		$('#fin').timepicker({
		'timeFormat':'H:i:s',
		'minTime' : ini,
		'showDuration' : true
	});
	});

	
</script>