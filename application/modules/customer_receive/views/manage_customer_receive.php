<style>
/* jQuery UI datepicker renders above Bootstrap's modal backdrop/dialog */
.ui-datepicker {
    z-index: 99999 !important;
}
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
.panel.panel-bd.lobidrag .panel-body {
    background: #ffffff !important;
    padding: 20px 22px !important;
    border-radius: 0 0 14px 14px !important;
    overflow: hidden !important;
}
#ManageCustomerReceive thead th {
    background: #F1F5F9 !important; color: #475569 !important;
    font-size: 11px !important; font-weight: 700 !important;
    text-transform: uppercase !important; letter-spacing: 0.7px !important;
    border-bottom: 2px solid #DDD6FE !important; border-top: none !important;
    padding: 11px 14px !important; white-space: nowrap !important;
}
#ManageCustomerReceive tbody td {
    padding: 10px 14px !important; font-size: 13px !important;
    color: #374151 !important; border-color: #F3F0FE !important;
    vertical-align: middle !important; white-space: nowrap !important;
}
#ManageCustomerReceive tbody td:nth-child(2),
#ManageCustomerReceive tbody td:nth-child(2) a {
    color: #8b5cf6 !important; font-weight: 600 !important;
}
#ManageCustomerReceive tbody tr:hover td { background: #F3F0FE !important; }
.panel-body .table-responsive { overflow-x: auto !important; }
#ManageCustomerReceive { width: auto !important; min-width: 100% !important; }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <span id="cr_manage_title">Manage Customer Receive</span>
                    <span>
                        <button class="btn btn-success" data-toggle="modal" data-target="#crFilterModal">
                            <i class="ti-filter"></i> Filter
                        </button>
                        <?php if ($this->permission1->method('new_customer_receive', 'create')->access()): ?>
                            <a href="<?php echo base_url('new_customer_receive'); ?>" class="btn btn-primary m-b-5 m-r-2">
                                <i class="ti-plus"></i> New Customer Receive
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="ManageCustomerReceive" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voucher No</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Payment Type</th>
                                <th>Total Settled</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <th colspan="6" class="text-right">Total:</th>
                            <th class="text-right" id="cr_foot_total"></th>
                            <th></th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="crFilterModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Filter Options</h4>
            </div>
            <div class="modal-body">
                <?php date_default_timezone_set('Asia/Colombo'); $today = date('Y-m-d'); ?>
                <div class="form-group">
                    <label>Branch</label>
                    <select class="form-control" id="cr_filter_branch" style="width:100%;"></select>
                </div>
                <div class="form-group">
                    <label>From Date</label>
                    <input type="text" class="form-control datepicker" id="cr_fdate" value="<?php echo $today; ?>">
                </div>
                <div class="form-group">
                    <label>To Date</label>
                    <input type="text" class="form-control datepicker" id="cr_tdate" value="<?php echo $today; ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="cr_password">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

<?php
echo '<script>';
echo 'var usertype       = ' . json_encode($this->session->userdata('user_level2')) . ';';
echo 'var password_enable = ' . json_encode($this->session->userdata('password_enable')) . ';';
echo '</script>';
?>
<script>
var base_url;
var type2    = (usertype == 3) ? 'B' : 'A';
var crBranchId = 0;

$(document).ready(function () {
    base_url = $('#base_url').val();

    if (usertype == 3) {
        document.getElementById('style12').style.backgroundColor = '#E0E0E0';
        document.getElementById('cr_manage_title').style.color = 'blue';
    }

    loadBranches();
});

function loadBranches() {
    $.ajax({
        type: 'POST',
        url: base_url + 'store/store/getbranchbyuserid',
        success: function (data) {
            var branches = JSON.parse(data);
            var $sel = $('#cr_filter_branch');
            $sel.empty().append('<option value="" disabled selected>Select Branch</option>');
            $.each(branches, function (i, b) {
                $sel.append('<option value="' + b.id + '">' + b.name + '</option>');
                if (b.default != 0) {
                    $sel.val(b.id);
                    crBranchId = b.id;
                }
            });

            if (password_enable == '0') {
                loadTable(null, null);
            }
        }
    });
}

function applyFilter() {
    var fdate  = $('#cr_fdate').val();
    var tdate  = $('#cr_tdate').val();
    var branch = $('#cr_filter_branch').val();

    if (!fdate)  { alert("From date shouldn't be empty"); return; }
    if (!branch) { alert("Branch shouldn't be empty"); return; }
    if (!tdate)  { alert("To date shouldn't be empty"); return; }

    if (password_enable == '1') {
        var pwd = $('#cr_password').val();
        if (!pwd) { alert("Password shouldn't be empty"); return; }

        $.ajax({
            url: base_url + 'dashboard/setting/checkpassword',
            type: 'POST',
            data: { password: pwd },
            success: function (response) {
                var count = JSON.parse(response);
                if (count == 1) {
                    $('#cr_password').val('');
                    crBranchId = branch;
                    loadTable(fdate, tdate);
                    $('#crFilterModal').modal('hide');
                } else {
                    alert('Wrong password');
                }
            },
            error: function () { alert('Error checking password.'); }
        });
    } else {
        crBranchId = branch;
        loadTable(fdate, tdate);
        $('#crFilterModal').modal('hide');
    }
}

function loadTable(fdate, tdate) {
    if ($.fn.DataTable.isDataTable('#ManageCustomerReceive')) {
        $('#ManageCustomerReceive').DataTable().clear().destroy();
    }

    $('#ManageCustomerReceive').DataTable({
        responsive: false,
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        lengthMenu: [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
        dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
        buttons: [
            { extend: 'copy',  exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'btn-sm prints' },
            { extend: 'csv',   title: 'Manage Customer Receive', exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'btn-sm prints' },
            { extend: 'excel', title: 'Manage Customer Receive', exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'btn-sm prints' },
            { extend: 'pdf',   title: 'Manage Customer Receive', exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'btn-sm prints' },
            { extend: 'print', title: '<center>Manage Customer Receive</center>', exportOptions: { columns: [0,1,2,3,4,5,6] }, className: 'btn-sm prints' }
        ],
        serverMethod: 'post',
        ajax: {
            url: base_url + 'getManageCustomerReceive',
            data: {
                csrf_test_name: $('#CSRF_TOKEN').val(),
                type2:    type2,
                branchid: crBranchId,
                fdate:    fdate,
                tdate:    tdate
            }
        },
        columns: [
            { data: 'sl' },
            { data: 'voucher_no' },
            { data: 'date' },
            { data: 'customer_name' },
            { data: 'branch_name' },
            { data: 'payment_type_name' },
            { data: 'total_amount', className: 'text-right cr_total_col', render: $.fn.dataTable.render.number(',', '.', 2) },
            { data: 'button' }
        ],
        footerCallback: function (row, data, start, end, display) {
            var api  = this.api();
            var sum  = api.column('.cr_total_col', { page: 'current' }).data()
                         .reduce(function (a, b) { return (parseFloat(a) || 0) + (parseFloat(b) || 0); }, 0);
            $('#cr_foot_total').text(sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }
    });
}

function deleteCustomerReceive(id) {
    if (!confirm('Are you sure you want to delete this Customer Receive? This will restore the related customer due(s).')) {
        return;
    }
    $.ajax({
        type: 'POST',
        url: base_url + 'deleteCustomerReceive',
        data: { id: id },
        success: function (raw) {
            var res;
            try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false, message: raw }; }
            alert(res.message);
            if (res.success) {
                $('#ManageCustomerReceive').DataTable().ajax.reload(null, false);
            }
        },
        error: function () {
            alert('Delete failed. Please try again.');
        }
    });
}

function editCustomerReceive(id) {
    window.location.href = base_url + 'new_customer_receive?edit_id=' + encodeURIComponent(id);
}

function pdfDateStamp() {
    var d = new Date();
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    return dd + mm + d.getFullYear();
}

function printRawHtml(view, voucherNo) {
    if (voucherNo) {
        document.title = 'customerreceive_' + voucherNo + '_' + pdfDateStamp();
    }
    $(view).print({
        deferred: $.Deferred().done(function () {
            window.location.reload(true);
        })
    });
}

function reprintCustomerReceive(id) {
    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
    if (!confirm("Do you want to print this record")) return;
    $.ajax({
        type: 'POST',
        url: base_url + 'printCustomerReceive',
        data: { id: id },
        success: function (raw) {
            var res;
            try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false }; }
            if (res.success) {
                printRawHtml(res.details, res.voucher_no);
            } else {
                alert(res.message || 'Could not load that Customer Receive for printing.');
            }
        },
        error: function () {
            alert('Could not load that Customer Receive for printing.');
        }
    });
}
</script>
