<form class="form-entry">
	{if $row.id|default:false}
		<input type="hidden" name="id" value="{$row.id}">
	{/if}

	<div class="row">
		<div class="col-md-6">
			<div class="mb-3">
				{form_select enabled=true field='doctor_id|Doctor' options=$doctors|default:[] value=$row.doctor_id|default:'' required=false}
			</div>
		</div>
		<div class="col-md-3">
			<div class="mb-3" doctors>
				{form_input enabled=true field='appointment_date' value=$row.appointment_date|default:''}
			</div>
		</div>
		<div class="col-md-3">
			<div class="mb-3" doctors>
				{form_input enabled=true field='appointment_time' value=$row.appointment_time|default:'' type='time'}
			</div>
		</div>
	</div>

	<div class="mb-3">
		{form_select enabled=true field='status' options=[STATUS_PENDING, STATUS_APPROVED, STATUS_DISAPPROVED] value=$row.status|default:''}
	</div>

	<div class="mb-3">
		{form_textarea enabled=true field='status_remarks' value=$row.status_remarks|default:'' required=false}
	</div>
</form>