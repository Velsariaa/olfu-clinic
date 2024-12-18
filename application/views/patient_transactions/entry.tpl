<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<input type="hidden" name="patient_id" value="{$patient.id}">

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_select enabled=true field='transaction_type_id|Transaction Type' options=$transaction_types|default:[] value=$row.transaction_type_id|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_select enabled=true field='doctor_id|Doctor' options=$doctors|default:[] value=$row.doctor_id|default:''}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='weight|Weight (kg)' value=$row.weight|default:'' type="number" step="0.01"}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3">
				{form_input enabled=true field='height|Height (kg)' value=$row.height|default:'' type="number" step="0.01"}
			</div>
		</div>
	</div>

	<div class="mb-3">
		{form_textarea enabled=true field='remarks' value=$row.remarks|default:''}
	</div>
</form>