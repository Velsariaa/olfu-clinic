<form class="form-entry">
	{if $row.id|default:false}
	<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_input enabled=true field='specialty' value=$row.specialty|default:''}
	</div>

	<div class="mb-3">
		{form_textarea enabled=true field='description' value=$row.description|default:''}
	</div>
</form>
