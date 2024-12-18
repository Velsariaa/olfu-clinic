{extends "`$smarty.current_dir`/profile.tpl"}

{block name='attributes'}
	{include "`$smarty.current_dir`/name.tpl"}
	{include "`$smarty.current_dir`/email_contact.tpl"}
{/block}

{block name='before_account_settings'}
	<div class="card">
		<div class="card-header">
			<h5 class="mb-0">Schedule & Specialties</h5>
		</div>
		<div class="card-body">
			<form id=frm-schedules>
				<input type="hidden" name="doctor_id" value="{$profile_user.id}">
				
				<div class="mb-3">
					{form_select enabled=true field='day_names_ids[]|Schedule' required=false options=$day_names|default:[] value=$doctor_day_names_ids|default:[] multiple='multiple'}
				</div>

				<div class="mb-3">
					{form_select enabled=true field='specialties_ids[]|Specialties' required=false options=$specialties|default:[] value=$doctor_specialties_ids|default:[] multiple='multiple'}
				</div>
			</form>
			<div class="row">
				<div class="col-12">
					<button type="button" class="btn btn-primary" btn-doctor-schedule>Save</button>
				</div>
			</div>
		</div>
	</div>

	{$capture_name = uuid4()}
	{capture name=$capture_name}
		<script type="text/javascript">
			$(() => {
				const swalInit = swal.mixin({
					buttonsStyling: false,
					customClass: {
						confirmButton: 'btn btn-success',
						cancelButton: 'btn btn-light',
						denyButton: 'btn btn-light',
						input: 'form-control'
					}
				});
				
				$('#frm-schedules select').select2({
					minimumResultsForSearch: -1
				});

				$(document).on('click', '.btn[btn-doctor-schedule]', function() {
					let _this = $(this);

					_this.prop('disabled', true);

					$.ajax({
						url: '{"`$parent_route`.save-doctor"|route}',
						type: 'POST',
						data: new FormData($('#frm-schedules')[0]),
						processData: false,
						contentType: false,
						dataType: "json",
						success: function(result) {
							_this.prop('disabled', false);

							if (result.status || false) {
								swalInit.fire('Success!', 'Profile Updated', 'success');
							} else {
								swalInit.fire('Failed!', result.message || 'Something went wrong.',
									'error');
							}
						},
						error: function(result) {
							_this.prop('disabled', false);

							swalInit.fire('Failed!', 'Something went wrong.', 'error');
						},
					});
				});
			});
		</script>
	{/capture}

	{$js_captures[$capture_name] = $smarty.capture.$capture_name}
{/block}