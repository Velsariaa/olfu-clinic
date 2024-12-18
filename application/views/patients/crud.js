$(document).on('click', '.btn[data-action="create-record"]', function() {
    let _this = $(this);

    $('#modal-entry button').prop('disabled', true);

    $.ajax({
        url: _this.attr('data-action-create'),
        type: 'POST',
        data: new FormData($('#modal-entry .modal-body .form-entry')[0]),
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(result) {
            $('#modal-entry button').prop('disabled', false);

            if (result.status || false) {
                $('#modal-entry').modal('hide');

                var table = _this.data('dt-table').DataTable();

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

$(document).on('click', '.edit-record-row', function() {
    var _this = $(this);
    var tr = _this.closest('tr');
    var table = tr.closest('table').DataTable();
    var row = table.row(tr);
    var data = row.data();

    $.ajax({
        url: data.action_edit,
        type: 'GET',
        success: function(result) {
            if (result.status || false) {
                let btn_update = $('#modal-entry .btn-save');
                $('#modal-entry .modal-body').empty().append(result.html);
                $('#modal-entry .modal-body input[name="id"]').attr('index', row.index());
                $('#modal-entry .modal-body input[name="id"]').attr('url', data.action_update);
                $('#modal-entry').data('modal-title', data.modal_title);
                
                btn_update.attr('data-action', 'update-record');
                btn_update.data('table', tr.closest('table'));
                
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

$(document).on('click', '.btn[data-action="update-record"]', function() {
    let _this = $(this);

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
                var index = $('#modal-entry .modal-body input[name="id"]').attr('index');

                $('#modal-entry').modal('hide');

                var table = _this.data('table').DataTable();
                table.row(index).data(result.data).draw(false);

                swalInit.fire('Success!', 'Record saved.', 'success');
            } else {
                swalInit.fire('Failed!', result.message || 'Something went wrong.', 'error');
            }
        },
        error: function(result) {
            $('#modal-entry button').prop('disabled', false);

            swalInit.fire('Failed!', 'Something went wrong.', 'error');
        },
    });
});

$(document).on('click', '.delete-record-row', function() {
    let _this = $(this);
    let tr = _this.closest('tr');
    let table = tr.closest('table').DataTable();
    let data = table.row(tr).data();

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