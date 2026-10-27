<style>
    /* ── Panel header ── */
    #style12 .panel-title {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    #style12 .panel-title > span:first-child {
        flex: 1 1 auto;
    }
    .inv-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding-left: 0 !important;
    }
    .inv-header-actions .btn {
        margin: 0 !important;
    }

    /* ── Mobile ≤767px ── */
    @media (max-width: 767px) {
        #style12 .panel-title > span:first-child {
            flex: 1 1 100%;
            font-size: 15px;
            font-weight: 600;
        }
        .inv-header-actions { flex: 1 1 100%; }
        .inv-header-actions .btn { flex: 1 1 auto; text-align: center; }

        /* DataTable scroll */
        .table-responsive, .dataTables_wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        #inventoryTransectionTable td,
        #inventoryTransectionTable th { white-space: nowrap; font-size: 13px; padding: 6px 8px; }
        #inventoryTransectionTable td .btn-xs { padding: 4px 8px; font-size: 12px; }

        /* DataTable controls */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { float: none !important; text-align: left; margin-bottom: 8px; }
        .dataTables_wrapper .dataTables_filter input { width: 100% !important; }
        .dataTables_wrapper .dt-buttons { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
        .dataTables_wrapper .dt-buttons .btn { flex: 1 1 auto; font-size: 11px; padding: 4px 6px; }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { float: none !important; text-align: center; margin-top: 8px; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 4px 8px; font-size: 13px; }
    }

    /* ── Tablet 768–1024px ── */
    @media (min-width: 768px) and (max-width: 1024px) {
        #style12 .panel-title > span:first-child { font-size: 15px; font-weight: 600; }
        .inv-header-actions .btn { font-size: 13px; }

        .table-responsive, .dataTables_wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        #inventoryTransectionTable td,
        #inventoryTransectionTable th { font-size: 13px; padding: 7px 10px; white-space: nowrap; }
        #inventoryTransectionTable td .btn-xs { padding: 4px 10px; font-size: 12px; }

        .dataTables_wrapper .dataTables_filter input { width: auto; }
        .dataTables_wrapper .dt-buttons .btn { font-size: 12px; padding: 4px 8px; }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">

            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <span id="title">Manage Inventory Transection</span>
                    <span class="inv-header-actions padding-lefttitle">
                        <button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
                            <i class="ti-filter"></i> Filter
                        </button>
                        <?php if ($this->permission1->method('new_stock', 'create')->access()) { ?>
                            <a href="<?php echo base_url('new_inventory_transection') ?>" class="btn btn-primary m-b-5 m-r-2">
                                <i class="ti-plus"></i> New Inventory Transection
                            </a>
                        <?php } ?>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="inventoryTransectionTable">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Inventory Id</th>
                                <th>Date</th>
                                <th>Incident Type</th>
                                <th>Reason</th>
                                <th><?php echo display('action') ?></th>
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

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'inventorytransaction_' + voucherNo + '_' + pdfDateStamp();
        }
        $(view).print({
            deferred: $.Deferred().done(function() {
                window.location.reload(true);
            })
        });
    }

    function reprintInventoryTransaction(id) {
        if (!confirm("Do you want to print this record")) return;
        $.ajax({
            type: 'POST',
            url: $('#base_url').val() + 'stock/stock/printInventoryTransaction',
            data: { id: id },
            success: function(raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false }; }
                if (res.success) {
                    printRawHtml(res.details, res.voucher);
                } else {
                    alert(res.message || 'Could not load that record for printing.');
                }
            },
            error: function() {
                alert('Could not load that record for printing.');
            }
        });
    }

    function filter() {
        $('#inventoryTransectionTable').DataTable().destroy();

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
        $('#inventoryTransectionTable').DataTable({
            responsive: false,
            scrollX: true,
            scrollY: '55vh',
            scrollCollapse: true,

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [
                { "bSortable": false, "aTargets": [0, 1, 2, 3] }
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
                exportOptions: { columns: [0, 1, 2, 3] },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Inventory Transections",
                exportOptions: { columns: [0, 1, 2, 3] },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                title: "Inventory Transections",
                exportOptions: { columns: [0, 1, 2, 3] },
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                title: "Inventory Transections",
                exportOptions: { columns: [0, 1, 2, 3] },
                className: "btn-sm prints"
            }, {
                extend: "print",
                title: "<center>Inventory Transections</center>",
                exportOptions: { columns: [0, 1, 2, 3] },
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'stock/stock/check_inventory_transection',
                data: {
                    csrf_test_name: csrf_test_name,
                    fdate: fdate,
                    tdate: tdate
                }
            },
            'columns': [
                { data: 'sl' },
                { data: 'inventory_id' },
                { data: 'date' },
                { data: 'type' },
                { data: 'reason' },
                { data: 'button' },
            ],
        });
    }
</script>
