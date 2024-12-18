<form class="form-entry">
	{if $row.id|default:false}
	<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_input enabled=true field='transaction_type' value=$row.transaction_type|default:''}
	</div>
</form>
