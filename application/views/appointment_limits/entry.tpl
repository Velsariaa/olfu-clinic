<form class="form-entry">
	{if $row.id|default:false}
	<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_input enabled=true field='_date|Date' value=$row._date|default:''}
	</div>

	<div class="mb-3">
		{form_input enabled=true field='_limit|Limit' value=$row._limit|default:'' type='number'}
	</div>
</form>
