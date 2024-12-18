<form class="form-entry">
	{if $row.id|default:false}
	<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_input enabled=true field='title' value=$row.title|default:''}
	</div>

	<div class="mb-3">
		{form_textarea enabled=true field='details' value=$row.details|default:''}
	</div>
</form>
