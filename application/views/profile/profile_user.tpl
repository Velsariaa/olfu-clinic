{extends "`$smarty.current_dir`/profile.tpl"}

{block name='attributes'}
	<form id=frm-user>
		{include "`$smarty.current_dir`/name.tpl" disable_first_name=false disable_middle_name=false disable_last_name=false}
		{include "`$smarty.current_dir`/email_contact.tpl" disable_email=false disable_contact=false}

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='student_no' value=$profile_user.student_no|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='year_level' value=$profile_user.year_level|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="mb-3">
					{form_input enabled=true field='address' value=$profile_user.address|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='birth_date' value=$profile_user.birth_date|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_select enabled=true field='gender' options=['Male', 'Female'] value=$profile_user.gender|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{$options = [
						'Single',
						'Married',
						'Widowed',
						'Divorced',
						'Separated',
						'Annulled',
						'Domestic Partnership',
						'Common-Law'
					]}
					{form_select enabled=true field='civil_status' options=$options value=$profile_user.civil_status|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='religion' value=$profile_user.religion|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="mb-3">
					{form_input enabled=true field='nationality' value=$profile_user.nationality|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="mb-3">
					{form_input enabled=true field='mother_name' value=$profile_user.mother_name|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='mother_occupation' value=$profile_user.mother_occupation|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='mother_contact' value=$profile_user.mother_contact|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="mb-3">
					{form_input enabled=true field='father_name' value=$profile_user.father_name|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='father_occupation' value=$profile_user.father_occupation|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='father_contact' value=$profile_user.father_contact|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='emergency_contact_name' value=$profile_user.emergency_contact_name|default:'' required=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='emergency_contact' value=$profile_user.emergency_contact|default:'' required=true}
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="mb-3">
					{form_input enabled=true field='hospital_choice' value=$profile_user.hospital_choice|default:'' required=true}
				</div>
			</div>
		</div>
	</form>
	<div class="row">
		<div class="col-12">
			<button type="button" class="btn btn-success" user-information>Save</button>
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

				const dpTodayButtonElement = $('input[name="birth_date"]')[0];
        
				if ( dpTodayButtonElement) {
					const dpTodayButton = new Datepicker(dpTodayButtonElement, {
						container: '.content-inner',
						buttonClass: 'btn',
						prevArrow: document.dir == 'rtl' ? '&rarr;' : '&larr;',
						nextArrow: document.dir == 'rtl' ? '&larr;' : '&rarr;',
						todayBtn: true,
						format: 'yyyy-mm-dd'
					});
				}

				$('#frm-user select').select2({
					minimumResultsForSearch: -1
				});

				$(document).on('click', 'button[user-information]', function() {
					let _this = $(this);

					_this.prop('disabled', true);

					$.ajax({
						url: '{"profile.update-user"|route}',
						type: 'POST',
						data: new FormData($('#frm-user')[0]),
						processData: false,
						contentType: false,
						dataType: "json",
						success: function(result) {
							_this.prop('disabled', false);

							if (result.status || false) {
								$('#frm-password input[type="password"]').val("");

								swalInit.fire('Success!', 'User information updated', 'success');
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