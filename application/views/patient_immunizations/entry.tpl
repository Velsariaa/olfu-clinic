<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<input type="hidden" name="patient_id" value="{$patient.id}">

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_select enabled=true field='vaccine_id|Vaccine' options=$vaccines|default:[] value=$row.vaccine_id|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_select enabled=true field='administered_by|Administered By' options=$doctors|default:[] value=$row.administered_by|default:'' required=false}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='scheduled_at|Schedule Date' value=$row.scheduled_at|default:'' required=false}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_input enabled=true field='administered_at|Date Administered' value=$row.administered_at|default:'' required=false}
			</div>
		</div>
	</div>
</form>