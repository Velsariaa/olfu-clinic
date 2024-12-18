<form class="form-entry">
	{if $row.id|default:false}
	<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="mb-3">
		{form_input enabled=true field='first_name' value=$row.first_name|default:''}
	</div>

	<div class="mb-3">
		{form_input enabled=true field='last_name' value=$row.last_name|default:''}
	</div>

	<div class="mb-3">
		{form_input enabled=true field='username' value=$row.username|default:''}
	</div>

	<div class="mb-3">
		{form_input enabled=true field='password' type="password" required=(false|iif:($row.id|default:false):true)}
	</div>
</form>
