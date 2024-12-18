<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_textarea enabled=true field='status_remarks|Reason'}
	</div>
</form>