<style>
#employeeList td,
#employeeList th {
    padding: 8px 10px;
    vertical-align: middle;
    font-size: 13px;
}

#employeeList td .btn-sm {
    padding: 3px 8px;
    font-size: 12px;
    line-height: 1.5;
}

#employeeList td .btn-sm i {
    font-size: 12px;
}

#employeeList td form {
    display: inline;
    white-space: nowrap;
}

#employeeList td form .btn {
    margin-bottom: 0;
}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('manage_employee')?></span>
                    <span class="padding-lefttitle">
                        <?php if($this->permission1->method('manage_employee','create')->access()){ ?>
                        <a href="<?php echo base_url('employee_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i
                                class="ti-plus"> </i>
                            <?php echo display('add_employee') ?> </a>
                        <?php }?>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="employeeList" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th><?php echo display('designation') ?></th>
                                <th>Contact No.</th>
                                <th><?php echo display('email') ?></th>
                                <th>State/Province</th>
                                <th>Status</th>
                                <th><?php echo display('action') ?></th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
    if ($employee_list) {
        $sl = 1;
        foreach($employee_list as $employees){?>
                            <tr>
                                <td><?php echo $sl;?></td>
                                <td><?php echo html_escape($employees['last_name']);?></td>
                                <td><?php echo html_escape($employees['first_name']);?></td>
                                <td><?php echo html_escape($employees['designation']);?></td>
                                <td><?php echo html_escape($employees['phone']);?></td>
                                <td><?php echo html_escape($employees['email']);?></td>
                                <td>
                                    <?php echo isset($employees['state']) ? html_escape($employees['state']) : '';?>
                                </td>
                                <td>
                                    <?php 
            $status = isset($employees['status']) ? $employees['status'] : 1;
            if($status == 1) {
                echo '<span class="label label-success">Active</span>';
            } else {
                echo '<span class="label label-danger">Inactive</span>';
            }
            ?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <?php if($this->permission1->method('manage_employee','update')->access()){ ?>
                                    <a href="<?php echo base_url() . 'employee_form/'.$employees['id']; ?>"
                                        class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left"
                                        title="<?php echo display('update') ?>"><i class="fa fa-pencil"
                                            aria-hidden="true"></i></a>
                                    <?php }?>
                                    <?php if($this->permission1->method('manage_employee','delete')->access()){ ?>
                                    <a href="<?php echo base_url('hrm/hrm/bdtask_delete_employee/'.$employees['id']) ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('<?php echo display('are_you_sure') ?>')"
                                        data-toggle="tooltip" data-placement="right"
                                        title="<?php echo display('delete') ?>"><i class="fa fa-trash-o"
                                            aria-hidden="true"></i></a>
                                    <?php }?>
                                    <a href="<?php echo base_url('employee_profile/'.$employees['id']);?>"
                                        class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top"
                                        title="Profile"><i class="fa fa-user"></i></a>
                                </td>
                            </tr>
                            <?php
        $sl++;
    }}
    ?>
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    "use strict";
    $('#employeeList').DataTable({
        responsive: true,
        "aaSorting": [
            [1, "asc"]
        ],
        "columnDefs": [{
            "bSortable": false,
            "aTargets": [0, 8]
        }, ],
        'lengthMenu': [
            [10, 25, 50, 100, 250, 500, 1000],
            [10, 25, 50, 100, 250, 500, 1000]
        ],
        dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
        buttons: [{
            extend: "copy",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            className: "btn-sm prints"
        }, {
            extend: "csv",
            title: "Employee List",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            className: "btn-sm prints"
        }, {
            extend: "excel",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            title: "Employee List",
            className: "btn-sm prints"
        }, {
            extend: "pdf",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            title: "Employee List",
            className: "btn-sm prints"
        }, {
            extend: "print",
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            title: "<center>Employee List</center>",
            className: "btn-sm prints"
        }],
    });
});
</script>