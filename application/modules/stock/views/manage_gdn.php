<style>
.panel.panel-bd.lobidrag {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06) !important;
    border-radius: 14px !important;
    overflow: visible !important;
    margin-bottom: 24px !important;
}
.panel.panel-bd.lobidrag .panel-heading {
    background: #ffffff !important;
    padding: 12px 20px !important;
    border: none !important;
    border-bottom: 2px solid #F1F5F9 !important;
    border-radius: 14px 14px 0 0 !important;
    overflow: hidden !important;
}
.panel.panel-bd.lobidrag .panel-title {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 10px !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .panel-title > span:first-child {
    color: #1E293B !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    white-space: nowrap !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle,
.panel.panel-bd.lobidrag .padding-lefttitle table,
.panel.panel-bd.lobidrag .padding-lefttitle tbody,
.panel.panel-bd.lobidrag .padding-lefttitle tr {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    border: none !important;
    background: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle td {
    display: block !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn {
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    white-space: nowrap !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
    color: #fff !important;
    letter-spacing: 0.3px !important;
    transition: background 0.15s, box-shadow 0.15s !important;
    text-decoration: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary:hover {
    background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important;
    box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important;
}
.panel.panel-bd.lobidrag .panel-body {
    background: #ffffff !important;
    padding: 20px 22px !important;
    border-radius: 0 0 14px 14px !important;
    overflow: hidden !important;
}
#stockdisposalnote thead th {
    background: #F1F5F9 !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.7px !important;
    border-bottom: 2px solid #DDD6FE !important;
    border-top: none !important;
    padding: 11px 14px !important;
    white-space: nowrap !important;
}
#stockdisposalnote tbody td {
    padding: 10px 14px !important;
    font-size: 13px !important;
    color: #374151 !important;
    border-color: #F3F0FE !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
}
/* GDN ID */
#stockdisposalnote tbody td:nth-child(3),
#stockdisposalnote tbody td:nth-child(3) a {
    color: #8b5cf6 !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
}
/* Voucher No */
#stockdisposalnote tbody td:nth-child(6),
#stockdisposalnote tbody td:nth-child(6) a {
    color: #8b5cf6 !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
}
#stockdisposalnote tbody tr:hover td {
    background: #F3F0FE !important;
}
.panel.panel-bd.lobidrag .panel-body .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
}
#stockdisposalnote {
    width: auto !important;
    min-width: 100% !important;
}
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter { margin-bottom: 10px !important; }
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons { margin-bottom: 8px !important; }
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn {
    border-radius: 6px !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
}
.label-success { background:#16A34A !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
.label-danger  { background:#DC2626 !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
.label-warning { background:#F59E0B !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
#stockdisposalnote tbody td .btn,
#stockdisposalnote tbody td a.btn { padding:2px 6px !important; font-size:12px !important; line-height:1.5 !important; border-radius:3px !important; margin-left:3px !important; }
#stockdisposalnote tbody td .btn i,
#stockdisposalnote tbody td a.btn i { font-size:11px !important; }
@media (max-width: 767px) {
    .panel.panel-bd.lobidrag .panel-heading { padding: 10px 14px !important; }
    .panel.panel-bd.lobidrag .panel-title { flex-wrap: wrap !important; gap: 8px !important; }
    .panel.panel-bd.lobidrag .panel-body { padding: 10px 8px !important; }
    #stockdisposalnote thead th { font-size: 10px !important; padding: 8px 8px !important; }
    #stockdisposalnote tbody td { font-size: 12px !important; padding: 8px 8px !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter { width: 100% !important; text-align: left !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_info,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_paginate { width: 100% !important; text-align: center !important; float: none !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons { width: 100% !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn { margin-bottom: 4px !important; }
    #stockdisposalnote tbody td .btn,
    #stockdisposalnote tbody td a.btn { padding: 2px 5px !important; font-size: 11px !important; margin: 1px 1px !important; }
}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <span id="title"><?php echo display('manage_gdn') ?></span>
                    <span class="padding-lefttitle">
                        <table>
                            <tr>


                                <td style="padding-left: 20px;">
                                    <button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
                                        <i class="ti-filter"></i> Filter
                                    </button>
                                </td>
                                <td style="padding-left: 5px;">
                                    <?php if ($this->permission1->method('new_gdn', 'create')->access()) { ?>
                                        <a href="<?php echo base_url('new_gdn') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_gdn') ?> </a>
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
                                <th>Date</th>
                                <th>GDN ID</th>
                                <th>Customer</th>
                                <th>Store</th>
                                <th>Voucher No</th>
                                <th>Incident Type</th>
                                <th>Details</th>
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
                    <label>Store</label>
                    <select class="form-control " id="store" name="store" tabindex="3" style="width: 400px;">
                        <!-- options go here -->
                    </select>

                </div>


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

        if (usertype == 3) {
            type2 = "B"
            const title = document.getElementById('title');
            title.style.color = 'blue';
            document.getElementById('style12').style.backgroundColor = '#E0E0E0';


        } else {
            type2 = "A"

        }

        // getStoreDropdown(0)

        getWholeData();


    });
    let storeId = 0;

    function filter() {
        $('#stockdisposalnote').DataTable().destroy();

        if (document.getElementById('fdate').value == '') {
            alert("From date shouldn't be empty")
            return

        }

        if (document.getElementById('store').value == '' | !document.getElementById('store').value) {
            alert("Store shouldn't be empty")
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


    function getWholeData() {

        if ($.fn.DataTable.isDataTable('#stockdisposalnote')) {
            $('#stockdisposalnote').DataTable().clear().destroy();
        }

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "stock/stock/active_storegdndrop",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var storees = JSON.parse(data);
                var $storeDropdown = $('#store');
                $storeDropdown.empty();
                $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

                $.each(storees, function(index, store) {
                    $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
                    if (storeId == 0) {
                        if (store.default != 0) {
                            $storeDropdown.val(store.id)
                            storeId = store.id;
                        }
                    }

                });

                if (storeId > 0) {
                    {
                        $storeDropdown.val(storeId)
                    }
                }

				if (password_enable == "0"){
                    getInvoiceData(null, null)
                }
            }
        });


    }

    function getInvoiceData(fdate, tdate) {
        "use strict";
        var csrf_test_name = $('#CSRF_TOKEN').val();
        var base_url = $('#base_url').val();
        var total_product = $("#total_product").val();
        $('#stockdisposalnote').DataTable({
            responsive: false,

            "aaSorting": [
                [2, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": false,
                    "aTargets": [0, 1, 2, 3, 4]
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
                    columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want
                },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Manage GRN",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
                },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
                },
                title: "Manage GRN",
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
                },
                title: "Manage GRN",
                className: "btn-sm prints"
            }, {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
                },
                title: "<center>Manage GRN</center>",
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'stock/stock/checkgdnstock',
                data: {
                    csrf_test_name: csrf_test_name,
                    type2: type2,
                    storeid: document.getElementById("store").value,
                    fdate: fdate,
                    tdate: tdate

                }
            },
            'columns': [{
                    data: 'sl'
                },
                {
                    data: 'date'
                },
                {
                    data: 'gdn_id'
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'store'
                },
                {
                    data: 'voucherno'
                },
                {
                    data: 'type',
                    render: function(data, type, row) {
                        if (!data) return '';
                        var map = {
                            'Sale':             'background:#16A34A',
                            'Sales':            'background:#16A34A',
                            'Wholesale':        'background:#2563EB',
                            'Purchase Return':  'background:#DC2626',
                            'Store Transfer':   'background:#7C3AED',
                            'stockdisposal':    'background:#F59E0B',
                            'Stock Disposal':   'background:#F59E0B',
                            'GDN':              'background:#0EA5E9',
                        };
                        var style = map[data] || 'background:#64748B';
                        return '<span style="' + style + ';color:#fff;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:600;display:inline-block;white-space:nowrap">' + data + '</span>';
                    }
                },
                {
                    data: 'details'
                },
                {
                    data: 'button'
                },
            ],

        });

    }



    function reprintInvoice(invoiceId) {
    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
        if (confirm("Do you want to reprint this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'stock/stock/getgdnStockForPos',
                data: {
                    id: invoiceId,

                },
                success: function(data1) {
                    datas = JSON.parse(data1);
                    printRawHtml(datas.details, datas.invoiceno);

                }
            });
        }

    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'gdn_' + voucherNo + '_' + pdfDateStamp();
        }
        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload(true);
            })
        });
    }
</script>