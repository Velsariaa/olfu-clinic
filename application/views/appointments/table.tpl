{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Appointments"}

{block name='content'}
<div class="card">
	<div class="table-responsive">
		<table class="table dt datatable-basic dataTable">
			<thead>
				<tr>
					<th class="action">#</th>
					<th class="user_name">Student</th>
					{* <th class="specialty">Requested Specialty</th>
					<th class="doctor">Prefered Doctor</th> *}
					<th class="date_appointment">Prefered Date</th>
					<th class="time_appointment">Prefered Time</th>
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

{capture name="filters"}
	{strip}
		<form action="" method="GET">
			<div class="justify-content-start d-flex">
				<div class="dataTables_filter">
					<div class="input-group">
						<span class="input-group-text"><i class="ph-calendar"></i></span>
						<input type="text" class="form-control daterange-basic"
							value="{$smarty.get.date_from|default:now|dateformat:'M. d, Y'} - {$smarty.get.date_to|default:now|dateformat:'M. d, Y'}">
					</div>
				</div>
				<input type="hidden" name="date_from" value="{$smarty.get.date_from|default:now|dateformat:'Y-m-d'}">
				<input type="hidden" name="date_to" value="{$smarty.get.date_to|default:now|dateformat:'Y-m-d'}">
			</div>
		</form>
	{/strip}
{/capture}

{/block}

{block name='styles'}
<style type="text/css">
	.datepicker {
	z-index: 99999;
	}
	.no-border-bottom {
	border-bottom: 0 !important;
	}

	.w100 {
	width: 100%;
}</style>
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

<script src="{$assets_url}/js/vendor/ui/moment/moment.min.js"></script>
<script src="{$assets_url}/js/vendor/pickers/daterangepicker.js"></script>	

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
            // dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
			dom: '<"datatable-header justify-content-start"f<"custom-toolbar"><"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
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
			{ data: 'user_name' },
			{* { data: 'specialty' },
			{ data: 'doctor' }, *}
			{ data: 'date_appointment' },
			{ data: 'time_appointment' },
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
			order: []
		});

		var toolbar = '{$smarty.capture.filters|trim}';

		$('.custom-toolbar').addClass('ms-sm-3').html(toolbar);

		$('.daterange-basic').daterangepicker({
			parentEl: '.content-inner',
			locale: {
				format: 'MMM. DD, YYYY'
			}
		});

		$('.daterange-basic').on('apply.daterangepicker', function(ev, picker) 
		{
			$(this).closest('form').find('input[name="date_from"]').val(picker.startDate.format('YYYY-MM-DD'));
  			$(this).closest('form').find('input[name="date_to"]').val(picker.endDate.format('YYYY-MM-DD'));
			this.closest('form').submit();
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
			$('#modal-entry .modal-title').html(($('#modal-entry input[name="id"]').length > 0 ? 'Edit' : 'New') + ' Appointment');

			$('#modal-entry .modal-body select').select2({
				minimumResultsForSearch: -1
			});

			const dpTodayButtonElement = $('input[name="appointment_date"]')[0];
        
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
		});

		$(document).on('shown.bs.modal', '#modal-entry', function () 
		{
		});

		{include "`$smarty.current_dir`/../base/default_crud.tpl"}

		$(document).on('click', '.btn-appointment-status', function () 
		{
			let _this = $(this);

			var tr = _this.closest('tr');
			var table = $('.dt').DataTable();
			var row = table.row(tr);
			var data = row.data();

			var formData = new FormData();
			formData.append("id", data.id);
			formData.append("status", _this.attr('data-status'));

			$.ajax({
				url: data.action_update,
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(result) {
					if (result.status || false) {
						var table = $('.dt').DataTable();
						table.row(row.index()).data(result.data).draw(false);

						swalInit.fire('Success!', 'Record saved.', 'success');
					} else {
						swalInit.fire('Failed!', result.message || 'Something went wrong.',
							'error');
					}
				},
				error: function(result) {
					swalInit.fire('Failed!', 'Something went wrong.', 'error');
				},
			});
		});
	});
</script>
{/block}