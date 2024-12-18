<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='first_name' value=$row.first_name|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='middle_name' value=$row.middle_name|default:'' required=false}
			</div>
		</div>
	</div>

	<div class="mb-3">
		{form_input enabled=true field='last_name' value=$row.last_name|default:''}
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='email' value=$row.email|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='contact' value=$row.contact|default:''}
			</div>
		</div>
	</div>

	{* <div class="mb-3">
		{form_select enabled=true field='day_names_ids[]|Schedule' required=false options=$day_names|default:[] value=$doctor_day_names_ids|default:[] multiple='multiple'}
	</div>

	<div class="mb-3">
		{form_select enabled=true field='specialties_ids[]|Specialties' required=false options=$specialties|default:[] value=$doctor_specialties_ids|default:[] multiple='multiple'}
	</div> *}

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='username' value=$admin.username|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{if ($row.id|default:false) && ($row.admin_id|default:false)}
					{form_input enabled=true field='password' type="password" required=false}
				{else}
					{form_input enabled=true field='password' type="password" required=true}
				{/if}
			</div>
		</div>
	</div>
</form>