<form name="start">

	<label>Arduino device type</label>
	<select name="device">
		<option value="__AVR_ATxmega384D3__">AVR ATxmega384D3</option>
	</select>

	<label>Skatch file</label>
	<input type="text" name="skatch" value="skatch.ino" class="autocomplete" data-source="skatches.php"> 
	<input type="button" value="Create" onclick="if(confirm('If the file exists it will be overwritten. Are you sure?')) emuino.createSkatch($('[name=skatch]').val());">

</form>