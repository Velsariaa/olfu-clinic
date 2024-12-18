<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	{* <div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_select enabled=true field='specialty_id|Specialty' options=$specialties|default:[] value=$row.specialty_id|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_select enabled=true field='doctor_id|Doctor' options=$doctors|default:[] value=$row.doctor_id|default:'' required=false}
			</div>
		</div>
	</div> *}

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_input enabled=true field='appointment_date|Prefered Date' value=$row.appointment_date|default:''}
			</div>
		</div>
		<div class="col-md-6">
			<div class="mb-3" doctors>
				{form_input enabled=true field='appointment_time|Prefered Time' value=$row.appointment_time|default:'' type='time'}
			</div>
		</div>
	</div>

	<div class="mb-3">
		{form_textarea enabled=true field='reason' value=$row.reason|default:''}
	</div>
</form>