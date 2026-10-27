<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">

            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <span id="title"><?php echo display('manage_stock_adjustment'); ?></span>
                    <span class="padding-lefttitle">
                        <table>
                            <tr>
                                <td style="padding-left: 20px;">
                                    <button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
                                        <i class="ti-filter"></i> Filter
                                    </button>
                                </td>
                                <td style="padding-left: 5px;">
                                    <?php if ($this->permission1->method('new_stock_adjustment', 'create')->access()) { ?>
                                        <a href="<?php echo base_url('newstockadjustment_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_stock_adjustment') ?> </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        </table>
                    </span>

                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Id</th>
                                <th>Date</th>
                                <th>Stock Type</th>
                                <th>Incident Type</th>
                                <th><?php echo display('action') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <input type="hidden" id="total_product" value="<?php echo $total_product; ?>" name="">
    </div>
</div>
<div id="filterModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Filter Options</h4>
            </div>

            <div class="modal-body">

                <?php
                date_default_timezone_set('Asia/Colombo');

                $date = date('Y-m-d'); ?>


                <div class="form-group">
                    <label>From Date</label>
                    <input type="text" required tabindex="2" class="form-control datepicker" name="fdate" value="<?php echo $date; ?>" id="fdate" />
                </div>

                <div class="form-group">
                    <label>To Date</label>
                    <input type="text" required tabindex="2" class="form-control datepicker" name="tdate" value="<?php echo $date; ?>" id="tdate" />
                </div>

                <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password">
                </div>




            </div>


            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="filter()">Apply Filter</button>
            </div>

        </div>

    </div>
</div>

<?php
echo "<script>";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let password_enable=" . json_encode($this->session->userdata('password_enable')) . ";";
echo "</script>";
?>
<script>
    let type2 = ""
    $(document).ready(function() {
        "use strict";

        if (usertype == 3) {
            document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            const title = document.getElementById('title');
            title.style.color = 'blue';
            type2 = "B"

        } else {
            type2 = "A"

        }
        if (password_enable == "0") {
            getInvoiceData(null, null)
        }
    });

    function filter() {
        $('#stockdisposalnote').DataTable().destroy();

        if (document.getElementById('fdate').value == '') {
            alert("From date shouldn't be empty")
            return

        }
        if (document.getElementById('tdate').value == '') {
            alert("To date shouldn't be empty")
            return

        }

        if (password_enable == "1") {
            if (document.getElementById('password').value == '') {
                alert("Password shouldn't be empty")
                return

            }

            $.ajax({
                url: $('#base_url').val() + 'dashboard/setting/checkpassword',
                type: 'POST',
                data: {
                    password: document.getElementById('password').value,
                },
                success: function(response) {
                    var count = JSON.parse(response);
                    if (count == 1) {
                        document.getElementById('password').value = ''
                        getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
                        $('#filterModal').modal('hide');

                    } else {
                        alert("Wrong password")
                        return
                    }



                },
                error: function(error) {
                    console.log(error);
                }
            });


        } else {
            getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
            $('#filterModal').modal('hide');
            return

        }



    }

    function getInvoiceData(fdate, tdate) {
        var csrf_test_name = $('#CSRF_TOKEN').val();
        var base_url = $('#base_url').val();
        var total_product = $("#total_product").val();
        $('#stockdisposalnote').DataTable({
            responsive: true,

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": false,
                    "aTargets": [0, 1, 2, 3]
                },

            ],
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
                    columns: [0, 1, 2, 3] //Your Colume value those you want
                },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Stocks",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Stocks",
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Stocks",
                className: "btn-sm prints"
            }, {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "<center>Stocks</center>",
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'stock/stock/checkadjstock',
                data: {
                    csrf_test_name: csrf_test_name,
                    fdate: fdate,
                    tdate: tdate
                }
            },
            'columns': [{
                    data: 'sl'
                },
                {
                    data: 'id'
                },
                {
                    data: 'date'
                },
                {
                    data: 'stocktype'
                },
                {
                    data: 'type'
                },
                {
                    data: 'button'
                },
            ],

        });
    }
</script>