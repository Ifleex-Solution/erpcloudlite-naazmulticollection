<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Payment/Receipt Type List</span>
                    <span class="padding-lefttitle">
                        <?php if ($this->permission1->method('payment_receipt_type_form', 'create')->access()) { ?>
                        <a href="<?php echo base_url('payment_receipt_type_form'); ?>" class="btn btn-primary m-b-5 m-r-2">
                            <i class="ti-plus"></i> Add Payment/Receipt Type
                        </a>
                        <?php } ?>
                    </span>
                </div>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="prtList">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    "use strict";
    var csrf_test_name = $('#CSRF_TOKEN').val();
    var base_url       = $('#base_url').val();

    $('#prtList').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        serverMethod: 'post',
        aaSorting: [[0, 'asc']],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
        buttons: [
            { extend: 'copy',  exportOptions: { columns: [0,1,2,3] }, className: 'btn-sm prints' },
            { extend: 'csv',   exportOptions: { columns: [0,1,2,3] }, title: 'Payment Receipt Type List', className: 'btn-sm prints' },
            { extend: 'excel', exportOptions: { columns: [0,1,2,3] }, title: 'Payment Receipt Type List', className: 'btn-sm prints' },
            { extend: 'pdf',   exportOptions: { columns: [0,1,2,3] }, title: 'Payment Receipt Type List', className: 'btn-sm prints' },
            { extend: 'print', exportOptions: { columns: [0,1,2,3] }, title: '<center>Payment Receipt Type List</center>', className: 'btn-sm prints' }
        ],
        ajax: {
            url:  base_url + 'check_payment_receipt_type_list',
            data: { csrf_test_name: csrf_test_name }
        },
        columns: [
            { data: 'sl'     },
            { data: 'name'   },
            { data: 'type'   },
            { data: 'status' },
            { data: 'button', orderable: false }
        ]
    });
});
</script>
