{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Appointment Limits"}

{block name='content'}
<div class="row">
	<div class="col-md-6">
		<div class="card">
			<div class="table-responsive">
				<table class="table dt datatable-basic dataTable">
					<thead>
						<tr>
							<th class="action">#</th>
							<th class="date_limit">Date</th>
							<th class="_limit">Limit</th>
							<th class="date_updated">Date Modified</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="card card-body">
			<div class="cal-limits"></div>
		</div>
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
{#JS_FULLCALENDAR#|assets}

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/2.1.3/sorting/datetime-moment.js"></script>

<script type="text/javascript">
	$(() => 
	{
		// Define element
		const calendarBasicViewElement = document.querySelector('.cal-limits');

		const calendarBasicViewInit = new FullCalendar.Calendar(calendarBasicViewElement, {
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth'
			},
			initialDate: new Date(),
			navLinks: true, // can click day/week names to navigate views
			nowIndicator: true,
			weekNumberCalculation: 'ISO',
			editable: false,
			selectable: true,
			direction: document.dir == 'rtl' ? 'rtl' : 'ltr',
			dayMaxEvents: true, // allow "more" link when too many events
		});

		// Init
		calendarBasicViewInit.render();

		// Resize calendar when sidebar toggler is clicked
		document.querySelectorAll('.sidebar-control').forEach(function(sidebarToggle) {
			sidebarToggle.addEventListener('click', function() {
				calendarBasicViewInit.updateSize();
			});
		});

		function getDateLimits() {
			$.ajax({
				url: '{"appointment-limits.date-limits"|route}',
				type: 'GET',
				success: function(result) 
				{
					if (result.status || false)
					{
						calendarBasicViewInit.removeAllEvents();
						calendarBasicViewInit.addEventSource(result.data);				
					}
				}
			});
		}

		getDateLimits();

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
			{ data: 'date_limit' },
			{ data: '_limit' },
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
		});

		$(document).on('show.bs.modal', '#modal-entry', function () 
		{
			$('#modal-entry .modal-title').html(($('#modal-entry input[name="id"]').length > 0 ? 'Edit' : 'New') + ' Appointment Limit');
		});

		$(document).on('shown.bs.modal', '#modal-entry', function () 
		{
			$('#modal-entry .modal-body select').select2();

			const dpTodayButtonElement = $('input[name="_date"]')[0];
        
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

		$(document).on('click', '.btn[data-action="create"]', function() {
			var _this = $(this);

			$('#modal-entry button').prop('disabled', true);

			$.ajax({
				url: '{"`$parent_route`.create"|route}',
				type: 'POST',
				data: new FormData($('#modal-entry .modal-body .form-entry')[0]),
				processData: false,
				contentType: false,
				dataType: "json",
				success: function(result) {
					$('#modal-entry button').prop('disabled', false);

					if (result.status || false) {
						$('#modal-entry').modal('hide');

						var table = $('.dt').DataTable();

						table.row.add(result.data).draw();

						// move new row to top

						var currentPage = table.page();

						var rowCount = table.data().length - 1;
						var insertedRow = table.row(rowCount).data();
						var tempRow;

						for (var i = rowCount; i > 0; i--) {
							tempRow = table.row(i - 1).data();
							table.row(i).data(tempRow);
							table.row(i - 1).data(insertedRow);
						}

						table.page(currentPage).draw(false);

						// move new row to top

						getDateLimits();

						swalInit.fire('Success!', 'Record saved.', 'success');
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

		$(document).on('click', '.edit-row', function() {
			var _this = $(this);
			var tr = _this.closest('tr');
			var table = $('.dt').DataTable();
			var row = table.row(tr);
			var data = row.data();

			$.ajax({
				url: data.action_edit,
				type: 'GET',
				success: function(result) {
					if (result.status || false) {
						$('#modal-entry .modal-body').empty().append(result.html);
						$('#modal-entry .modal-body input[name="id"]').attr('index', row
						.index());
						$('#modal-entry .modal-body input[name="id"]').attr('url', data
							.action_update);
						$('#modal-entry .btn-save').attr('data-action', 'update');
						$('#modal-entry').modal('show');
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

		$(document).on('click', '.btn[data-action="update"]', function() {
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

						getDateLimits();

						swalInit.fire('Success!', 'Record saved.', 'success');
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

		$(document).on('click', '.delete-row', function() {
			var _this = $(this);
			var tr = _this.closest('tr');
			var table = $('.dt').DataTable();
			var data = table.row(tr).data();

			swalInit.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, cancel!',
				buttonsStyling: false,
				customClass: {
					confirmButton: 'btn btn-success',
					cancelButton: 'btn btn-danger'
				}
			}).then(function(result) {
				if (result.value) {
					$.ajax({
						url: data.action_delete,
						type: 'DELETE',
						success: function(result) {
							if (result.status || false) {
								table.row(tr).remove().draw(false);

								getDateLimits();

								swalInit.fire('Deleted!', result.message ||
									'Record has been deleted.', 'success');
							} else {
								swalInit.fire('Failed!', result.message ||
									'Something went wrong.', 'error');
							}
						},
						error: function(result) {
							swalInit.fire('Failed!', 'Something went wrong.', 'error');
						},
					});
				}
			});
		});
	});
</script>
{/block}