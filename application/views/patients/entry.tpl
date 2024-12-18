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
				{form_input enabled=true field='birth_date' value=$row.birth_date|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='birth_place' value=$row.birth_place|default:''}
			</div>
		</div>
	</div>

	<div class="mb-3">
		{form_input enabled=true field='address' value=$row.address|default:''}
	</div>

	<div class="row">
		<div class="col-12">
			<div class="mb-3">
				{form_select enabled=true field='gender' options=['Male', 'Female'] value=$row.gender|default:''}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='contact' value=$row.contact|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='email' value=$row.email|default:''}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">
			<div class="mb-3">
				{form_select enabled=true field='user_id|User' options=$users|default:[] value=$row.user_id|default:'' required=false}
			</div>
		</div>
	</div>
</form>