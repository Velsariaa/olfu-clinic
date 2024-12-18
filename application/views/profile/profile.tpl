{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Profile"}

{block name='content'}

<div class="card">
	<div class="card-header">
		<h5 class="mb-0">{($profile_admin.user_type|replace:'_':' ')|strProperCase} Information</h5>	
	</div>
	<div class="card-body">
		{block name='attributes'}
		{/block}
	</div>
</div>
{block name='before_account_settings'}
{/block}
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Account settings</h5>
	</div>
	<div class="card-body">
		<form id=frm-password>
		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='username' value=$user.username|default:'' required=false disabled=true}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='password|Password|••••••••' required=false type='password'}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='new_password' required=false type='password'}
				</div>
			</div>
			<div class="col-md-6">
				<div class="mb-3">
					{form_input enabled=true field='confirm_new_password' required=false type='password'}
				</div>
			</div>
		</div>
		</form>
		<div class="row">
			<div class="col-12">
				<button type="button" class="btn btn-success" password>Save</button>
			</div>
		</div>
	</div>
</div>
{/block}

{block name='styles'}
<style type="text/css">
	
</style>
{/block}

{block name='scripts'}
{#JS_JQUERY#|assets}
{#JS_SWEETALERT#|assets}
{#JS_SELECT2#|assets}
{#JS_DATE_PICKER#|assets}

<script type="text/javascript">
	$(() =>
	{
		const swalInit = swal.mixin({
			buttonsStyling: false,
			customClass: {
				confirmButton: 'btn btn-success',
				cancelButton: 'btn btn-light',
				denyButton: 'btn btn-light',
				input: 'form-control'
			}
		});

		$(document).on('click', '.btn[password]', function() {
			var _this = $(this);

			_this.prop('disabled', true);

			$.ajax({
				url: '{"`$parent_route`.password"|route}',
				type: 'POST',
				data: new FormData($('#frm-password')[0]),
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(result) {
					_this.prop('disabled', false);

					if (result.status || false) {
						$('#frm-password input[type="password"]').val("");

						swalInit.fire('Success!', 'Password updated', 'success');
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
{/block}