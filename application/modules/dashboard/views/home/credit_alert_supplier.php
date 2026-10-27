<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <style>
                .credit-status-btn {
                    cursor: default;
                    min-width: 90px;
                }
            </style>
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Credit Alert Notification <span class="label" style="background:#8b5cf6;">Supplier</span></h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="credit_alert_supplier_table" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-left"><?php echo display('sl') ?></th>
                                <th class="text-left">Supplier</th>
                                <th class="text-left">Invoice ID</th>
                                <th class="text-center">Invoice Date</th>
                                <th class="text-center">Due Date</th>
                                <th class="text-right">Due Amount (LKR)</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<script>
    $(document).ready(function () {
        $('#credit_alert_supplier_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: [
                { extend: 'copy',  className: 'btn-sm prints', exportOptions: { columns: [0,1,2,3,4,5,6] } },
                { extend: 'csv',   className: 'btn-sm prints', title: 'Credit Alert Notification - Supplier', exportOptions: { columns: [0,1,2,3,4,5,6] } },
                { extend: 'excel', className: 'btn-sm prints', title: 'Credit Alert Notification - Supplier', exportOptions: { columns: [0,1,2,3,4,5,6] } },
                { extend: 'pdf',   className: 'btn-sm prints', title: 'Credit Alert Notification - Supplier', exportOptions: { columns: [0,1,2,3,4,5,6] } },
                { extend: 'print', className: 'btn-sm prints', title: '<center>Credit Alert Notification - Supplier</center>', exportOptions: { columns: [0,1,2,3,4,5,6] } }
            ],
            serverMethod: 'post',
            ajax: {
                url: $('#baseUrl2').val() + 'supplier_credit_data',
                type: 'POST'
            },
            columnDefs: [
                { targets: [0], orderable: false }
            ],
            columns: [
                { data: 'sl',            className: 'text-left' },
                { data: 'supplier_name', className: 'text-left' },
                { data: 'invoice_no',    className: 'text-left' },
                { data: 'invoice_date',  className: 'text-center' },
                { data: 'due_date',      className: 'text-center' },
                {
                    data: 'due_amount',
                    className: 'text-right',
                    render: function (data) { return parseFloat(data).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function (data) {
                        return '<span class="btn btn-xs btn-danger credit-status-btn">' + data + '</span>';
                    }
                }
            ]
        });
    });
</script>
