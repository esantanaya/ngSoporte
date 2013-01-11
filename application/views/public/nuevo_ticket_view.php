<?php echo validation_errors(); ?>
<?php echo form_open('ticket-usuario/crea_ticket'); ?>
<label for="departamento">Departamento:</label>
<?php echo form_dropdown('departamento', $departamentos, '1'); ?>
<br>
<label for="asunto">Asunto:</label>
<input type="text" name="asunto">
<br>
<label for="mensaje">Mensaje:</label>
<textarea name="mensaje" cols="30" rows="10"></textarea>
<br>
<input type="submit" value="Crear Ticket">