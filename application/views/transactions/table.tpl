{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Transactions"}

{block name='content'}
<div class="card">
	<div class="table-responsive">
		<table class="table dt datatable-basic dataTable">
			<thead>
				<tr>
					<th class="patient">Patient</th>
					<th class="transaction_type">Type</th>
					<th class="doctor">Doctor</th>
					<th class="weight">Weight (kg)</th>
					<th class="height">Height (cm)</th>
					<th class="remarks">Remarks</th>
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
			{ data: 'patient' },
			{ data: 'transaction_type' },
			{ data: 'doctor' },
			{ data: 'weight' },
			{ data: 'height' },
			{ data: 'remarks' },
			],
			columnDefs: [
			],
			order: []
		});
	});
</script>
{/block}