{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "My Transactions"}

{block name='content'}
<div class="card">
	<div class="table-responsive">
		<table class="table dt datatable-basic dataTable">
			<thead>
				<tr>
					<th class="transaction_type">Transaction Type</th>
					<th class="doctor">Doctor</th>
					<th class="remarks">Remarks</th>
					<th class="weight">Weight (kg)</th>
					<th class="height">Height (cm)</th>
					<th class="date_updated">Date Modified</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>
{/block}

{block name='styles'}
{/block}

{block name='scripts'}
{#JS_JQUERY#|assets}
{#JS_DATATABLES#|assets}

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/2.1.3/sorting/datetime-moment.js"></script>

<script type="text/javascript">
	$(() => 
	{
		$.extend( $.fn.dataTable.defaults, {
			autoWidth: false,
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
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
			{ data: 'transaction_type' },
			{ data: 'doctor' },
			{ data: 'remarks' },
			{ data: 'weight' },
			{ data: 'height' },
			{ data: 'date_updated' },
			],
			columnDefs: [
			{ targets: ['date_updated'], width: '180' },
			],
			order: [],
		});
	});
</script>
{/block}