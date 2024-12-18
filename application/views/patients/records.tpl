<div class="card card-body my-2">
    <ul class="nav nav-tabs nav-tabs-highlight mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a href="#transactions-{$row.id}" class="nav-link active" data-bs-toggle="tab" aria-selected="true"
                role="tab">
                Transactions
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane fade active show" id="transactions-{$row.id}" role="tabpanel">
            <div class="table-responsive">
                <table class="table datatable-basic dataTable" id="dt-transactions-{$row.id}" dt-transactions>
                    <thead>
                        <tr>
                            <th class="action">Action</th>
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
    </div>
</div>