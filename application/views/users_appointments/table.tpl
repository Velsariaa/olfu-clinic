{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "My Appointments"}

{block name='content'}
<div class="card">
	<div class="table-responsive">
		<table class="table dt datatable-basic dataTable">
			<thead>
				<tr>
					<th class="action">#</th>
					{* <th class="specialty">Specialty</th>
					<th class="doctor">Doctor</th> *}
					<th class="date_appointment">Appointment Date</th>
					<th class="reason">Appointment Reason</th>
					<th class="status">Status</th>
					{* <th class="date_status">Status Date</th>
					<th class="status_remarks">Status Remarks</th>
					<th class="status_updated_by">Status Updated By</th> *}
					<th class="date_updated">Date Modified</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
<div id="modal-entry" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">
				
			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
				<button type="button" class="btn btn-success btn-save">Save</button>
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
			
			{* $(document).on('change', 'select[name="specialty_id"]', function (e)
			{
				let _this = $(this);
				let value = this.value;
				let form = _this.closest('form');

				$.ajax({
					url: '{"`$parent_route`.doctors"|route}',
					type: 'GET',
					data: { 
						id: form.find('input[name="id"]').val(),
						specialty_id: value,
					},
					success: function(result) 
					{
						if (result.status || false)
						{
							_this.closest('form').find('div[doctors]').empty().append(result.html);
							_this.closest('form').find('div[doctors] select').select2({
								minimumResultsForSearch: -1
							})
						}
						else
						{
							swalInit.fire('Failed!', result.message || 'Something went wrong.', 'error');
						}
					},
					error: function(result) 
					{
						swalInit.fire('Failed!', 'Something went wrong.', 'error');
					},
				});
			}); *}

			$(document).on('click', '.cancel-row', function(e) {
				let _this = $(this);
				let tr = _this.closest('tr');
				let table = $('.dt').DataTable();
				let row = table.row(tr);
				let data = row.data();

				$.ajax({
					url: data.action_cancel_form,
					type: 'GET',
					success: function(result) {
						if (result.status || false) {
							$('#modal-entry .modal-body').empty().append(result.html);
							$('#modal-entry .modal-body input[name="id"]').attr('index', row.index());
							$('#modal-entry .modal-body input[name="id"]').attr('url', data.action_cancel);
							$('#modal-entry .btn-save').attr('data-action', 'cancel');
							$('#modal-entry').data('modal-title', 'Cancel Appointment');
							$('#modal-entry').modal('show');
						} else {
							swalInit.fire('Failed!', result.message || 'Something went wrong.', 'error');
						}
					},
					error: function(result) {
						swalInit.fire('Failed!', 'Something went wrong.', 'error');
					},
				});
			});

			$(document).on('click', '.btn[data-action="cancel"]', function() {
				$('#modal-entry button').prop('disabled', true);

				$.ajax({
					url: $('#modal-entry .modal-body input[name="id"]').attr('url'),
					type: 'POST',
					data: new FormData($('#modal-entry .modal-body .form-entry')[0]),
					processData: false,
					contentType: false,
					dataType: "json",
					success: function(result) {
						$('#modal-entry button').prop('disabled', false);

						if (result.status || false) {
							var index = $('#modal-entry .modal-body input[name="id"]').attr(
								'index');

							$('#modal-entry').modal('hide');

							var table = $('.dt').DataTable();
							table.row(index).data(result.data).draw(false);

							swalInit.fire('Success!', 'Record cancelled.', 'success');
						} else {
							swalInit.fire('Failed!', result.message || 'Something went wrong.',
								'error');
						}
					},
					error: function(result) {
						$('#modal-entry button').prop('disabled', false);

						swalInit.fire('Failed!', 'Something went wrong.', 'error');
					},
				});
			});
		});
	</script>
{/capture}
{$js_captures[$capture_name] = $smarty.capture.$capture_name}

{/block}

{block name='styles'}
<style type="text/css">
	.datepicker {
		z-index: 99999;
	}
</style>
{/block}

{block name='scripts'}
{#JS_JQUERY#|assets}
{#JS_DATATABLES#|assets}
{#JS_DATATABLES_BUTTON#|assets}
{#JS_SWEETALERT#|assets}
{#JS_SELECT2#|assets}
{#JS_DATE_PICKER#|assets}

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/2.1.3/sorting/datetime-moment.js"></script>

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

		$.extend( $.fn.dataTable.defaults, {
			autoWidth: false,
            // dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
            	search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
            	searchPlaceholder: 'Type to filter...',
            	lengthMenu: '<span class="me-3">Show:</span> _MENU_',
            	paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' : '&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' }
            }
        });

		$.fn.dataTable.moment( 'MMM. D, YYYY' );

		$('.dt').DataTable({
			data: {$table_data},
			columns: [
			{ data: 'action' },
			{* { data: 'specialty' },
			{ data: 'doctor' }, *}
			{ data: 'date_appointment' },
			{ data: 'reason' },
			{ data: 'status' },
			{* { data: 'date_status' },
			{ data: 'status_remarks' },
			{ data: 'status_updated_by' }, *}
			{ data: 'date_updated' },
			],
			columnDefs: [
			{ targets: ['action'], className: 'text-center' },
			{ targets: ['action'], width: '50' },
			{ targets: ['action'], orderable: false },
			{ targets: ['date_updated'], width: '180' },
			],
			order: [],
			buttons: {
				dom: {
					button: {
						className: 'btn'
					}
				},
				buttons: [
				{
					text: '<i class="ph-plus"></i> Add New',
					className: 'btn-success',
					action: function(e, dt, node, config) 
					{
						$.ajax({
							url: '{"`$parent_route`.new"|route}',
							type: 'GET',
							success: function(result) 
							{
								if (result.status || false)
								{
									$('#modal-entry .modal-body').empty().append(result.html);
									$('#modal-entry .btn-save').attr('data-action', 'create');
									$('#modal-entry').modal('show');
								}
								else
								{
									swalInit.fire('Failed!', result.message || 'Something went wrong.', 'error');
								}
							},
							error: function(result) 
							{
								swalInit.fire('Failed!', 'Something went wrong.', 'error');
							},
						});
					}
				}]
			}
		});

		$(document).on('show.bs.dropdown', '.actions', function () 
		{
			$('.datatable-scroll-wrap').css('overflow-x', 'unset');
		});

		$(document).on('hidden.bs.dropdown', '.actions', function () 
		{
			$('.datatable-scroll-wrap').css('overflow-x', 'auto');
		});

		$(document).on('hidden.bs.modal', '#modal-entry', function () 
		{
			$('#modal-entry .modal-body').empty();

			$(this).removeData('modal-title');
		});

		$(document).on('show.bs.modal', '#modal-entry', function () 
		{
			let _this = $(this);
			
			if (_this.data('modal-title'))
			{
				$('#modal-entry .modal-title').html(_this.data('modal-title'));
			}
			else
			{
				$('#modal-entry .modal-title').html(($('#modal-entry input[name="id"]').length > 0 ? 'Edit' : 'New') + ' Appointment');
			}

			{* $('#modal-entry .modal-body select').select2({
				minimumResultsForSearch: -1
			}); 

			$('#modal-entry .modal-body select[name="specialty_id"]').trigger('change'); *}

			const dpTodayButtonElement = $('input[name="appointment_date"]')[0];
        
	        if ( dpTodayButtonElement) {
				const allowedDates = {$limit_dates|default:'[]'};

	            const dpTodayButton = new Datepicker(dpTodayButtonElement, {
	                container: '.content-inner',
	                buttonClass: 'btn',
	                prevArrow: document.dir == 'rtl' ? '&rarr;' : '&larr;',
	                nextArrow: document.dir == 'rtl' ? '&larr;' : '&rarr;',
	                todayBtn: true,
	                format: 'yyyy-mm-dd',
					beforeShowDay: function(date) {
						// const formattedDate = date.toISOString().split('T')[0];
						// let x = allowedDates.includes(formattedDate);
						// console.log(formattedDate, x);
						// return x

						const offsetDate = new Date(date.getTime() - date.getTimezoneOffset() * 60000);
						const formattedDate = offsetDate.toISOString().split('T')[0];
						let x = allowedDates.includes(formattedDate);
						// console.log(formattedDate, x);
						return x;
					}
	            });
	        }
		});

		$(document).on('shown.bs.modal', '#modal-entry', function () 
		{
		});

		{include "`$smarty.current_dir`/../base/default_crud.tpl"}
	});
</script>
{/block}