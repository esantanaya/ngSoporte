<?php echo validation_errors(); ?>
<?php echo form_open('tickets_usuario/crea_ticket'); ?>
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
<br>
departamento: <?php var_dump($dept_id) ?>
<br>
Asunto: <?php var_dump($subject) ?>
<br>
Creado: <?php var_dump($created) ?>
<br>
ID: <?php var_dump($ticketID) ?>
<br>
Usuario: <?php var_dump($usuario_id) ?>
<br>
staff: <?php var_dump($cod_staff) ?>
<br>