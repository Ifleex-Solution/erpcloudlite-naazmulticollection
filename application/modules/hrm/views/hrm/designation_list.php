<style>
#designationList td,
#designationList th {
    padding: 8px 10px;
    vertical-align: middle;
    font-size: 13px;
}

#designationList td .btn-sm {
    padding: 3px 8px;
    font-size: 12px;
    line-height: 1.5;
}

#designationList td .btn-sm i {
    font-size: 12px;
}

#designationList td form {
    display: inline;
    white-space: nowrap;
}

#designationList td form .btn {
    margin-bottom: 0;
}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('manage_designation')?></span>
                    <span class="padding-lefttitle">
                        <?php if($this->permission1->method('manage_designation','create')->access()){ ?>
                        <a href="<?php echo base_url('designation_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i
                                class="ti-plus"> </i>
                            <?php echo display('add_designation') ?> </a>
                        <?php }?>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="designationList">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th><?php echo display('designation') ?></th>
                                <th><?php echo display('details') ?></th>
                                <th>Status</th>
                                <th><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    "use strict";
    var csrf_test_name = $('#CSRF_TOKEN').val();
    var base_url = $('#base_url').val();

    $('#designationList').DataTable({
        responsive: true,
        "aaSorting": [
            [1, "asc"]
        ],
        "columnDefs": [{
            "bSortable": false,
            "aTargets": [0, 1, 2, 3]
        }, ],
        'processing': true,
        'serverSide': true,
        'lengthMenu': [
            [10, 25, 50, 100, 250, 500, 1000],
            [10, 25, 50, 100, 250, 500, 1000]
        ],
        dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
        buttons: [{
                extend: "copy",
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                className: "btn-sm prints"
            },
            {
                extend: "csv",
                title: "Designation List",
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                className: "btn-sm prints"
            },
            {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                title: "Designation List",
                className: "btn-sm prints"
            },
            {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                title: "Designation List",
                className: "btn-sm prints"
            },
            {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3]
                },
                title: "<center>Designation List</center>",
                className: "btn-sm prints"
            }
        ],
        'serverMethod': 'post',
        'ajax': {
            'url': base_url + 'hrm/hrm/checkDesignationList',
            data: {
                csrf_test_name: csrf_test_name,
            }
        },
        'columns': [{
                data: 'sl'
            },
            {
                data: 'designation'
            },
            {
                data: 'details'
            },
            {
                data: 'status_label'
            },
            {
                data: 'button'
            },
        ],
    });
});
</script>