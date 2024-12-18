{extends "`$smarty.current_dir`/../base/base.tpl"}

{$header_title = "Patients"}

{block name='content'}
	<div class="card">
		<div class="table-responsive">
			<table class="table dt datatable-basic dataTable">
				<thead>
					<tr>
						<th class="action">#</th>
						<th class="first_name">First Name</th>
						<th class="middle_name">Middle Name</th>
						<th class="last_name">Last Name</th>
						<th class="gender">Gender</th>
						<th class="date_birth">Birth Date</th>
						<th class="birth_place">Birth Place</th>
						<th class="address">Address</th>
						<th class="contact">Contact</th>
						<th class="email">Email</th>
						<th class="user_name">User</th>
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
		<script>
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

				$.extend($.fn.dataTable.defaults, {
					autoWidth: false,
					dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
					language: {
						search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
						searchPlaceholder: 'Type to filter...',
						lengthMenu: '<span class="me-3">Show:</span> _MENU_',
						paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' :
								'&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' }
					}
				});

				$.fn.dataTable.moment( 'MMM. D, YYYY' );
				
				$(document).on('click', '.records-row', function() 
				{
					let _this = $(this);
					let tr = _this.closest('tr');
					let table = $('.dt').DataTable();
					let data = table.row(tr).data();
					let row = table.row(tr);

					let closeFunction = function () {
						if (row.child.isShown()) {
							row.child.hide();
							return true;
						}
						return false;
					}

					if (closeFunction()) {
						return;
					}

					$.ajax({
						url: data.action_records,
						type: 'GET',	
						success: function(result) 
						{
							if (result.status || false)
							{
								row.child(result.html).show();

								load_transactions(data, row, closeFunction);

								tr.addClass('shown');
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
				});

				function load_datatable(args)
				{
					let options = {
						ajax: {
							url: args.data_route,
							dataSrc: 'data'
						},
						order: [],
						buttons: {
							dom: {
								button: {
									className: 'btn'
								}
							},
							buttons: [{
								text: '<i class="ph-plus"></i> Add New '+args.button_title,
								className: 'btn-teal',
								action: function(e, dt, node, config) {
									$.ajax({
										url: args.new_route,
										type: 'GET',
										success: function(result) {
											if (result.status || false) {
												let btn_create = $('#modal-entry .btn-save');
												btn_create.attr('data-action', 'create-record');
												btn_create.attr('data-action-create', args.create_route);
												btn_create.data('dt-table', args.table);

												$('#modal-entry .modal-body').empty().append(result.html);
												
												let modal = $('#modal-entry');
												modal.data('modal-title', args.button_title);
												modal.modal('show');
											} else {
												swalInit.fire('Failed!', result.message || 'Something went wrong.', 'error');
											}
										},
										error: function(result) {
											swalInit.fire('Failed!', 'Something went wrong.', 'error');
										},
									});
								}
							},
							{
								text: '<i class="ph-x-circle"></i> Close',
								className: 'btn-success',
								action: function (e, dt, node, config) {
									args.close_function();
								}
							}]
						}
					}

					$.extend(options, args.options || {});

					args.table.DataTable(options);
				}

				function load_transactions(parent_data, parent_row, closeFunction)
				{
					let table = $('#dt-transactions-'+parent_data.id);

					load_datatable({
						table: table,
						data_route: parent_data.action_transactions,
						button_title: 'Transaction',
						new_route: parent_data.action_new_transaction,
						create_route: parent_data.action_create_transaction,
						close_function: closeFunction,
						options: {
							columns: [
								{ data: 'action' },
								{ data: 'transaction_type' },
								{ data: 'doctor' },
								{ data: 'weight' },
								{ data: 'height' },
								{ data: 'remarks' },
							],
							columnDefs: [
								{ targets: ['action'], className: 'text-center' },
								{ targets: ['action'], width: '50' },
								{ targets: ['action'], orderable: false },
							],
						}
					});
				}

				{include "`$smarty.current_dir`/crud.js"}
			})
		</script>
	{/capture}
	{$js_captures[$capture_name] = $smarty.capture.$capture_name}
{/block}

{block name='styles'}
	<style type="text/css">
		.datepicker {
			z-index: 99999;
		}

		.height-100 {
			height: 100%;
		}

		.td-no-wrap {
			white-space: nowrap;
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

			$.extend($.fn.dataTable.defaults, {
				autoWidth: false,
				dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"datatable-scroll-wrap"t><"datatable-footer"ip>',
				language: {
					search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
					searchPlaceholder: 'Type to filter...',
					lengthMenu: '<span class="me-3">Show:</span> _MENU_',
					paginate: { 'first': 'First', 'last': 'Last', 'next': document.dir == "rtl" ? '&larr;' :
							'&rarr;', 'previous': document.dir == "rtl" ? '&rarr;' : '&larr;' }
				}
			});

			$.fn.dataTable.moment( 'MMM. D, YYYY' );

			$('.dt').DataTable({
				data: {$table_data},
				columns: [
					{ data: 'action' },
					{ data: 'first_name' },
					{ data: 'middle_name' },
					{ data: 'last_name' },
					{ data: 'gender' },
					{ data: 'date_birth' },
					{ data: 'birth_place' },
					{ data: 'address' },
					{ data: 'contact' },
					{ data: 'email' },
					{ data: 'user_name' },
					{ data: 'date_updated' },
				],
				columnDefs: [
					{ targets: ['action'], className: 'text-center' },
					{ targets: ['action'], width: '50' },
					{ targets: ['action'], orderable: false },
					{ targets: ['date_updated'], width: '180' },
					{ targets: ['date_birth', 'date_updated'], className: 'td-no-wrap' },
				],
				order: [],
				buttons: {
					dom: {
						button: {
							className: 'btn'
						}
					},
					buttons: [{
						text: '<i class="ph-plus"></i> Add New',
						className: 'btn-success',
						action: function(e, dt, node, config) {
							$.ajax({
								url: '{"`$parent_route`.new"|route}',
								type: 'GET',
								success: function(result) {
									if (result.status || false) {
										$('#modal-entry .modal-body').empty().append(
											result.html);
										$('#modal-entry .btn-save').attr('data-action',
											'create');
										$('#modal-entry').modal('show');
									} else {
										swalInit.fire('Failed!', result.message ||
											'Something went wrong.', 'error');
									}
								},
								error: function(result) {
									swalInit.fire('Failed!', 'Something went wrong.',
										'error');
								},
							});
						}
					}]
				}
			});

			$(document).on('show.bs.dropdown', '.actions', function() {
				$('.datatable-scroll-wrap').css('overflow-x', 'unset');
			});

			$(document).on('hidden.bs.dropdown', '.actions', function() {
				$('.datatable-scroll-wrap').css('overflow-x', 'auto');
			});

			$(document).on('hidden.bs.modal', '#modal-entry', function() {
				$('#modal-entry .modal-body').empty();
			});

			$(document).on('show.bs.modal', '#modal-entry', function() {
				let _this = $(this);
			
				if (_this.data('modal-title'))
				{
					$('#modal-entry .modal-title').html(($('#modal-entry input[name="id"]').length > 0 ? 'Edit' : 'New') + ' ' + _this.data('modal-title'));
				}
				else
				{
					$('#modal-entry .modal-title').html(($('#modal-entry input[name="id"]').length > 0 ? 'Edit' : 'New') + ' Patient');
				}

				$('#modal-entry .modal-body select').select2({
					minimumResultsForSearch: -1
				});

				const el_birth_date = $('input[name="birth_date"]')[0];

				if (el_birth_date) {
					const birth_date = new Datepicker(el_birth_date, {
						container: '.content-inner',
						buttonClass: 'btn',
						prevArrow: document.dir == 'rtl' ? '&rarr;' : '&larr;',
						nextArrow: document.dir == 'rtl' ? '&larr;' : '&rarr;',
						todayBtn: true,
						format: 'yyyy-mm-dd'
					});
				}	

				const el_scheduled_at = $('input[name="scheduled_at"]')[0];

				if (el_scheduled_at) {
					const scheduled_at = new Datepicker(el_scheduled_at, {
						container: '.content-inner',
						buttonClass: 'btn',
						prevArrow: document.dir == 'rtl' ? '&rarr;' : '&larr;',
						nextArrow: document.dir == 'rtl' ? '&larr;' : '&rarr;',
						todayBtn: true,
						format: 'yyyy-mm-dd'
					});
				}

				const el_administered_at = $('input[name="administered_at"]')[0];

				if (el_administered_at) {
					const administered_at = new Datepicker(el_administered_at, {
						container: '.content-inner',
						buttonClass: 'btn',
						prevArrow: document.dir == 'rtl' ? '&rarr;' : '&larr;',
						nextArrow: document.dir == 'rtl' ? '&larr;' : '&rarr;',
						todayBtn: true,
						format: 'yyyy-mm-dd'
					});
				}
			});

			$(document).on('shown.bs.modal', '#modal-entry', function() 
			{
			});

			{include "`$smarty.current_dir`/../base/default_crud.tpl"}
		});
	</script>
{/block}