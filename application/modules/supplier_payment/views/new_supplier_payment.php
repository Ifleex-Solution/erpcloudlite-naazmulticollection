<style>
    .cr-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
    .cr-header { padding: 12px 18px !important; }
    .cr-header-flex { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .cr-page-title { font-size: 16px; font-weight: 600; }
    .cr-form-section { padding: 16px 18px 8px; }
    .cr-form-section .form-group { margin-bottom: 10px; }
    .cr-form-section label { font-weight: 600; font-size: 13px; }
    .cr-readonly { background: #f5f5f5 !important; font-weight: 600; color: #333 !important; }

    /* ── Select2 — matches sale/customer receive screens ── */
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
        background: #F8FAFC !important; height: 34px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important; font-size: 13px !important;
        line-height: 32px !important; padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow b {
        border-color: #64748B transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important; margin-top: -1px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #8b5cf6 transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #8b5cf6 !important; background: #fff !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important;
    }
    .select2-selection__placeholder { color: #94A3B8 !important; }
    .select2-dropdown {
        border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
        box-shadow: 0 4px 16px rgba(0,0,0,.10) !important; margin-top: 2px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #E2E8F0 !important; border-radius: 6px !important;
        font-size: 13px !important; padding: 5px 8px !important;
    }
    .select2-results__option { font-size: 13px !important; padding: 7px 12px !important; color: #374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #8b5cf6 !important; color: #fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background: #F3F0FE !important; color: #8b5cf6 !important; }

    /* ── Purchase table ── */
    #spPurchaseTable {
        border-collapse: collapse !important; border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important;
        border: 1px solid #E2E8F0 !important;
    }
    #spPurchaseTable thead th {
        background: #F1F5F9 !important; color: #475569 !important;
        font-size: 11px !important; font-weight: 700 !important;
        text-transform: uppercase !important; letter-spacing: .7px !important;
        border-bottom: 2px solid #DDD6FE !important; border-top: none !important;
        padding: 11px 8px !important; white-space: nowrap !important;
    }
    #spPurchaseTable tbody td {
        padding: 6px 7px !important; font-size: 13px !important;
        color: #374151 !important; border-color: #F3F0FE !important;
        vertical-align: middle !important;
    }
    #spPurchaseTable tbody tr:hover td { background: #F3F0FE !important; }
    #spPurchaseTable tfoot td {
        background: #F8FAFC !important; border-top: 2px solid #DDD6FE !important;
        font-size: 13px !important; padding: 8px !important; font-weight: 700 !important;
    }
    #spPurchaseTable .form-control {
        height: 30px !important; font-size: 12px !important; padding: 2px 6px !important;
        border: 1px solid #E2E8F0 !important; border-radius: 6px !important;
    }
    #spPurchaseTable .form-control:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important;
    }
    #spPurchaseTable .select2-container { width: 100% !important; min-width: 160px; }
    #spPurchaseTable .select2-container .select2-selection--single {
        height: 30px !important; border-radius: 6px !important;
    }
    #spPurchaseTable .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important; font-size: 12px !important;
    }
    #spPurchaseTable .select2-container .select2-selection--single .select2-selection__arrow {
        height: 28px !important;
    }

    .cr-tfoot-btn { padding: 10px 8px !important; background: #fff !important; border-top: 2px solid #DDD6FE !important; }

    /* ── Responsive card layout ── */
    .cr-td-label { display: none; }
    @media (max-width: 1024px) {
        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #spPurchaseTable { display: block; width: 100%; }
        #spPurchaseTable thead { display: none; }
        #spPurchaseTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #spPurchaseTable tbody tr {
            display: grid; grid-template-columns: 1fr 1fr;
            width: 100%; box-sizing: border-box; margin-bottom: 16px;
            border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #spPurchaseTable tbody td {
            display: block; width: 100%; box-sizing: border-box;
            padding: 8px 12px !important; border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
        }
        #spPurchaseTable tbody td:nth-child(even) { border-right: none !important; }
        #spPurchaseTable tbody td:last-child {
            grid-column: 1 / -1; border-bottom: none !important; border-right: none !important;
        }
        .cr-td-label { display: block; font-size: 10px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
        #spPurchaseTable tfoot { display: block; margin-top: 10px; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; }
        #spPurchaseTable tfoot tr { display: flex; flex-direction: column; }
        #spPurchaseTable tfoot td { display: none !important; }
        #spPurchaseTable tfoot td[data-label] {
            display: block !important; width: 100%; box-sizing: border-box;
            padding: 8px 14px !important; border-bottom: 1px solid #f0f0f0 !important;
        }
        #spPurchaseTable tfoot td[data-label]::before {
            content: attr(data-label); display: block;
            font-size: 10px; font-weight: 700; color: #999; margin-bottom: 2px;
        }
        .cr-tfoot-btn { display: block !important; width: 100%; }
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag cr-panel">

            <div class="panel-heading cr-header" id="style12">
                <div class="cr-header-flex has-goback">
                    <span class="cr-page-title"><?php echo $title ?></span>
                
<a href="<?php echo base_url('manage_supplier_payment') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <div id="sp_edit_banner" style="display:none; background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing <span id="sp_edit_voucher_label"></span> — changes are saved only when you click Save.
            </div>

            <div class="cr-form-section">

                <!-- Row 1: Date | Voucher Count -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <?php date_default_timezone_set('Asia/Colombo'); ?>
                                <input type="text" class="form-control datepicker" id="sp_date" name="sp_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Voucher Count</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="sp_voucher_count" readonly value="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Branch | Total Branch Due -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Branch <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sp_branch" name="sp_branch"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Total Branch Due</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="sp_total_branch_due" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Supplier | Total Due -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Supplier <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sp_supplier" name="sp_supplier">
                                    <option value="">Select Supplier</option>
                                    <?php foreach ($suppliers as $s): ?>
                                        <option value="<?php echo $s['supplier_id']; ?>"><?php echo $s['supplier_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Total Due</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="sp_total_due" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Payment Type -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Payment Type <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sp_payment_type" name="sp_payment_type">
                                    <option value="">Select Payment Type</option>
                                    <?php foreach ($all_pmethod as $pm): ?>
                                        <option value="<?php echo $pm['id']; ?>"><?php echo $pm['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <!-- Purchase rows table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="spPurchaseTable">
                        <thead>
                            <tr>
                                <th style="min-width:160px;">Purchase No</th>
                                <th style="min-width:110px;">Purchase Date</th>
                                <th style="min-width:110px;">Purchase Amt</th>
                                <th style="min-width:110px;">Returned Amt</th>
                                <th style="min-width:110px;">Paid Amt</th>
                                <th style="min-width:110px;">Due Amt</th>
                                <th style="min-width:110px;">Settle Amt</th>
                                <th style="min-width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="sp_purchase_tbody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" id="sp_add_row_btn" disabled>
                                        <i class="fa fa-plus"></i> Add Purchase
                                    </button>
                                </td>
                                <td class="text-right" data-label="Total"><strong>Total</strong></td>
                                <td data-label="Purchase Amt" id="sp_foot_purchase">0.00</td>
                                <td data-label="Returned Amt" id="sp_foot_returned">0.00</td>
                                <td data-label="Paid Amt" id="sp_foot_paid">0.00</td>
                                <td data-label="Due Amt" id="sp_foot_due">0.00</td>
                                <td data-label="Settle Amt" id="sp_foot_settle">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save row -->
                <div class="row" style="margin-top:16px; margin-bottom:8px;">
                    <div class="col-sm-12 text-right">
                        <span id="sp_validation_msg" class="text-danger" style="font-size:13px; margin-right:12px; display:none;"></span>
                        <button type="button" class="btn btn-success btn-save-anim" id="sp_save_btn" disabled>
                            <i class="fa fa-save" id="sp_save_btn_icon"></i> <span id="sp_save_btn_label">Save</span>
                        </button>
                    </div>
                </div>

            </div><!-- /.cr-form-section -->
        </div>
    </div>
</div>

<script>
var base_url     = "<?php echo base_url(); ?>";
var spPurchases  = [];
var spRowCount   = 0;
var usertype     = <?php echo json_encode($this->session->userdata('user_level2')); ?>;
var type2        = (usertype == 3) ? "B" : "A";

if (usertype == 3) {
    document.getElementById('style12').style.backgroundColor = '#E0E0E0';
    var titleEl = document.querySelector('.cr-page-title');
    if (titleEl) titleEl.style.color = 'blue';
}

$(document).ready(function () {

    $('select.form-control').select2({ placeholder: 'Select option', allowClear: true });

    var editParams = new URLSearchParams(window.location.search);
    var editId     = parseInt(editParams.get('edit_id'), 10) || 0;

    var pendingEditDetails = [];

    if (editId > 0) {
        $.ajax({
            type: 'POST',
            url: base_url + 'getSupplierPaymentForEdit',
            data: { id: editId },
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false }; }

                if (!res.success) {
                    alert(res.message || 'Could not load that Supplier Payment for editing.');
                    editId = 0;
                    loadBranchDropdown(0, 0);
                    return;
                }

                pendingEditDetails = res.details || [];

                if (res.date) $('#sp_date').val(res.date);
                if (res.voucher_no) {
                    $('#sp_edit_voucher_label').text(res.voucher_no);
                    $('#sp_edit_banner').show();
                }
                if (res.payment_type) {
                    $('#sp_payment_type').val(res.payment_type).trigger('change');
                }

                $('.cr-page-title').text('Update Supplier Payment');
                $('#sp_save_btn_label').text('Update');
                $('#sp_save_btn_icon').attr('class', 'fa fa-pencil');

                loadBranchDropdown(parseInt(res.branch, 10) || 0, parseInt(res.supplier, 10) || 0);
            },
            error: function () {
                alert('Could not load that Supplier Payment for editing.');
                editId = 0;
                loadBranchDropdown(0, 0);
            }
        });
    } else {
        loadBranchDropdown(0, 0);
        // Default Payment Type to "Cash", matched by option text rather than a
        // hardcoded id, same convention as the Sale screen's freeze logic.
        $('#sp_payment_type option').filter(function () {
            return $(this).text().trim() === 'Cash';
        }).prop('selected', true);
        $('#sp_payment_type').trigger('change');
    }

    /* ── Branch ── */
    function loadBranchDropdown(selectedId, selectedSupplierId) {
        $.ajax({
            type: 'POST', url: base_url + 'store/store/getbranchbyuserid',
            success: function (data) {
                var branches = JSON.parse(data);
                var $sel = $('#sp_branch');
                $sel.empty().append('<option value="">Select Branch</option>');
                $.each(branches, function (i, b) {
                    $sel.append('<option value="' + b.id + '">' + b.name + '</option>');
                    if (b.default != 0) $sel.val(b.id);
                });
                if (selectedId > 0) $sel.val(selectedId);
                $sel.trigger('change');

                if (selectedSupplierId > 0) {
                    $('#sp_supplier').val(selectedSupplierId).trigger('change');
                }
            }
        });
    }

    function clearPurchaseTable() {
        $('#sp_purchase_tbody').empty();
        spRowCount = 0;
        resetFooter();
        $('#sp_add_row_btn').prop('disabled', spPurchases.length === 0);
    }

    /* ── Supplier change ── */
    $('#sp_supplier').on('change', function () {
        spPurchases = [];
        clearPurchaseTable();

        var supplier_id = $(this).val();
        if (!supplier_id) {
            $('#sp_voucher_count').val(0);
            $('#sp_total_branch_due').val('0.00');
            $('#sp_total_due').val('0.00');
            return;
        }
        fetchPurchaseList(supplier_id);
    });

    $(document).on('click', '#sp_add_row_btn', function () {
        addPurchaseRow();
    });

    $('#sp_branch').on('change', function () {
        clearPurchaseTable();
        if ($('#sp_supplier').val()) updateSummaryFromPurchases();
    });

    $('#sp_date').on('change', function () {
        clearPurchaseTable();
    });

    function fetchPurchaseList(supplier_id) {
        $.ajax({
            type: 'POST', url: base_url + 'getSupplierDuePurchases',
            data: { supplier_id: supplier_id, exclude_sp_id: editId },
            success: function (raw) {
                try {
                    var rows = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    spPurchases = rows || [];
                } catch(e) {
                    console.error('Purchase list parse error:', raw);
                    spPurchases = [];
                }
                updateSummaryFromPurchases();
                $('#sp_add_row_btn').prop('disabled', spPurchases.length === 0);
                applyPendingEditDetails();
            },
            error: function (xhr) {
                console.error('Purchase list AJAX error:', xhr.responseText);
                spPurchases = [];
            }
        });
    }

    function updateSummaryFromPurchases() {
        var branchId  = String($('#sp_branch').val());
        var totalDue  = 0;
        var branchDue = 0;
        $.each(spPurchases, function (i, p) {
            var due = parseFloat(p.due_amount) || 0;
            totalDue += due;
            if (String(p.branch) === branchId) branchDue += due;
        });
        $('#sp_voucher_count').val(spPurchases.length);
        $('#sp_total_due').val(totalDue.toFixed(2));
        $('#sp_total_branch_due').val(branchDue.toFixed(2));
    }

    function applyPendingEditDetails() {
        if (!pendingEditDetails.length) return;
        var details = pendingEditDetails;
        pendingEditDetails = [];

        $.each(details, function (i, d) {
            var purchaseId = String(d.purchase_id);
            var exists = false;
            $.each(spPurchases, function (j, p) { if (String(p.purchase_id) === purchaseId) { exists = true; return false; } });
            if (!exists) return;

            _doAddRow();
            var idx = spRowCount;
            $('#sp_pur_sel_' + idx).val(purchaseId).trigger('change');
            $('#sp_row_' + idx).find('.sp-settle-input').val(parseFloat(d.settle_amount).toFixed(2)).trigger('input');
        });

        toggleSaveBtn();
    }

    function addPurchaseRow() {
        if (spPurchases.length === 0) {
            var sid = $('#sp_supplier').val();
            if (!sid) return;
            $.ajax({
                type: 'POST', url: base_url + 'getSupplierDuePurchases',
                data: { supplier_id: sid, exclude_sp_id: editId },
                success: function (raw) {
                    try { spPurchases = (typeof raw === 'string' ? JSON.parse(raw) : raw) || []; } catch(e) { spPurchases = []; }
                    _doAddRow();
                }
            });
            return;
        }
        _doAddRow();
    }

    function buildPurchaseOptions(usedIds) {
        var branchId = String($('#sp_branch').val() || '');
        var opts = '<option value="">-- Select Purchase --</option>';
        $.each(spPurchases, function (i, p) {
            if (branchId && String(p.branch) !== branchId) return;
            if ((parseFloat(p.due_amount) || 0) <= 0) return;
            if (usedIds.indexOf(String(p.purchase_id)) === -1) {
                opts += '<option value="' + p.purchase_id + '">' + p.voucher_no + ' (Due: ' + p.due_amount + ')</option>';
            }
        });
        return opts;
    }

    function _doAddRow() {
        spRowCount++;
        var idx = spRowCount;

        var usedIds = getUsedPurchaseIds();
        var opts = buildPurchaseOptions(usedIds);

        var tr = '<tr id="sp_row_' + idx + '" data-idx="' + idx + '">'
            + '<td><span class="cr-td-label">Purchase No</span>'
            +   '<select class="sp-pur-select" id="sp_pur_sel_' + idx + '" data-idx="' + idx + '">' + opts + '</select>'
            + '</td>'
            + '<td><span class="cr-td-label">Purchase Date</span><span class="sp-pur-date">-</span></td>'
            + '<td><span class="cr-td-label">Purchase Amt</span><span class="sp-pur-amount">0.00</span></td>'
            + '<td><span class="cr-td-label">Returned Amt</span><span class="sp-pur-returned">0.00</span></td>'
            + '<td><span class="cr-td-label">Paid Amt</span><span class="sp-pur-paid">0.00</span></td>'
            + '<td><span class="cr-td-label">Due Amt</span><span class="sp-pur-due">0.00</span></td>'
            + '<td><span class="cr-td-label">Settle Amt</span>'
            +   '<input type="number" class="form-control sp-settle-input" min="0" step="0.01" placeholder="0.00" value="" disabled>'
            + '</td>'
            + '<td class="text-center">'
            +   '<button type="button" class="btn btn-danger btn-sm sp-delete-row" data-idx="' + idx + '">'
            +   '<i class="fa fa-trash"></i></button>'
            + '</td>'
            + '</tr>';

        $('#sp_purchase_tbody').append(tr);

        $('#sp_pur_sel_' + idx).select2({
            width: '100%',
            placeholder: '-- Select Purchase --',
            allowClear: true
        });
    }

    $(document).on('change', '.sp-pur-select', function () {
        var idx        = $(this).data('idx');
        var purchaseId = $(this).val();
        var $row       = $('#sp_row_' + idx);

        if (!purchaseId) {
            $row.find('.sp-pur-date').text('-');
            $row.find('.sp-pur-amount').text('0.00');
            $row.find('.sp-pur-returned').text('0.00');
            $row.find('.sp-pur-paid').text('0.00');
            $row.find('.sp-pur-due').text('0.00');
            $row.find('.sp-settle-input').val('').prop('disabled', true);
            $row.data('purchase-id', '');
            recalcFooter();
            return;
        }

        var p = null;
        $.each(spPurchases, function (i, r) {
            if (String(r.purchase_id) === String(purchaseId)) { p = r; return false; }
        });
        if (!p) return;

        $row.data('purchase-id', purchaseId);
        $row.find('.sp-pur-date').text(p.purchase_date);
        $row.find('.sp-pur-amount').text(p.purchase_amount);
        $row.find('.sp-pur-returned').text(p.returned_amount);
        $row.find('.sp-pur-paid').text(p.paid_amount);
        $row.find('.sp-pur-due').text(p.due_amount);
        $row.find('.sp-settle-input')
            .val('')
            .attr('max', p.due_amount)
            .prop('disabled', false)
            .focus();

        recalcFooter();
        refreshAllDropdownOptions();
    });

    $(document).on('input', '.sp-settle-input', function () {
        recalcFooter();
        toggleSaveBtn();
    });

    $(document).on('click', '.sp-delete-row', function () {
        $('#sp_row_' + $(this).data('idx')).remove();
        refreshAllDropdownOptions();
        recalcFooter();
        toggleSaveBtn();
    });

    function getUsedPurchaseIds() {
        var ids = [];
        $('#sp_purchase_tbody tr').each(function () {
            var pid = $(this).data('purchase-id');
            if (pid) ids.push(String(pid));
        });
        return ids;
    }

    function refreshAllDropdownOptions() {
        var usedIds = getUsedPurchaseIds();
        $('#sp_purchase_tbody tr').each(function () {
            var $row   = $(this);
            var $sel   = $row.find('.sp-pur-select');
            var curVal = $sel.val();

            $sel.find('option').each(function () {
                var v = $(this).val();
                if (!v) return;
                if (usedIds.indexOf(v) !== -1 && v !== String(curVal)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
    }

    function recalcFooter() {
        var totPurchase = 0, totReturned = 0, totPaid = 0, totDue = 0, totSettle = 0;
        $('#sp_purchase_tbody tr').each(function () {
            totPurchase += parseFloat($(this).find('.sp-pur-amount').text())   || 0;
            totReturned += parseFloat($(this).find('.sp-pur-returned').text()) || 0;
            totPaid     += parseFloat($(this).find('.sp-pur-paid').text())     || 0;
            totDue      += parseFloat($(this).find('.sp-pur-due').text())      || 0;
            totSettle   += parseFloat($(this).find('.sp-settle-input').val())  || 0;
        });
        $('#sp_foot_purchase').text(totPurchase.toFixed(2));
        $('#sp_foot_returned').text(totReturned.toFixed(2));
        $('#sp_foot_paid').text(totPaid.toFixed(2));
        $('#sp_foot_due').text(totDue.toFixed(2));
        $('#sp_foot_settle').text(totSettle.toFixed(2));
    }

    function resetFooter() {
        $('#sp_foot_purchase,#sp_foot_returned,#sp_foot_paid,#sp_foot_due,#sp_foot_settle').text('0.00');
    }

    function toggleSaveBtn() {
        var hasValidRow = false;
        $('#sp_purchase_tbody tr').each(function () {
            var purchaseId = $(this).data('purchase-id');
            var settle      = parseFloat($(this).find('.sp-settle-input').val()) || 0;
            if (purchaseId && settle > 0) { hasValidRow = true; return false; }
        });
        $('#sp_save_btn').prop('disabled', !hasValidRow);
    }

    function showValidation(msg) {
        $('#sp_validation_msg').text(msg).show();
    }
    function clearValidation() {
        $('#sp_validation_msg').text('').hide();
    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'supplierpayment_' + voucherNo + '_' + pdfDateStamp();
        }
        $(view).print({
            deferred: $.Deferred().done(function () {
                window.location.reload(true);
            })
        });
    }

    $('#sp_save_btn').on('click', function () {
        clearValidation();

        var date         = $('#sp_date').val();
        var branch       = $('#sp_branch').val();
        var supplier     = $('#sp_supplier').val();
        var payment_type = $('#sp_payment_type').val();

        if (!date) { showValidation('Please select a Date.'); return; }
        if (!branch) { showValidation('Please select a Branch.'); return; }
        if (!supplier) { showValidation('Please select a Supplier.'); return; }
        if (!payment_type) { showValidation('Please select a Payment Type.'); return; }

        var invoices = [];
        var errMsg   = '';

        $('#sp_purchase_tbody tr').each(function () {
            var purchaseId = $(this).data('purchase-id');
            var settle      = parseFloat($(this).find('.sp-settle-input').val()) || 0;
            var maxDue      = parseFloat($(this).find('.sp-pur-due').text()) || 0;
            var voucher     = $(this).find('.sp-pur-select option:selected').text().split('(')[0].trim();

            if (!purchaseId) return;

            if (settle <= 0) {
                errMsg = 'Settle amount for "' + voucher + '" must be greater than 0.';
                return false;
            }
            if (settle > maxDue + 0.001) {
                errMsg = 'Settle amount for "' + voucher + '" cannot exceed Due amount (' + maxDue.toFixed(2) + ').';
                return false;
            }
            invoices.push({ purchase_id: purchaseId, settle_amount: settle.toFixed(2) });
        });

        if (errMsg) { showValidation(errMsg); return; }
        if (invoices.length === 0) { showValidation('Please add at least one purchase with a settle amount.'); return; }

        var $btn = $(this);
        var isEdit = editId > 0;
        $btn.addClass('btn-save-anim-loading').prop('disabled', true);
        $('#sp_save_btn_icon').attr('class', 'fa fa-spinner fa-spin');
        $('#sp_save_btn_label').text(isEdit ? 'Updating...' : 'Saving...');

        function resetSaveBtn() {
            $btn.removeClass('btn-save-anim-loading').prop('disabled', false);
            $('#sp_save_btn_icon').attr('class', isEdit ? 'fa fa-pencil' : 'fa fa-save');
            $('#sp_save_btn_label').text(isEdit ? 'Update' : 'Save');
        }

        $.ajax({
            type: 'POST',
            url: base_url + 'saveSupplierPayment',
            data: {
                date:         date,
                branch:       branch,
                supplier:     supplier,
                payment_type: payment_type,
                type2:        type2,
                invoices:     JSON.stringify(invoices),
                edit_id:      editId
            },
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch(e) { res = { success: false, message: raw }; }
                if (res.success) {
                    clearValidation();
                    alert(res.message)
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    if (res.details) {
                        printRawHtml(res.details, res.voucher_no);
                    } else {
                        location.reload();
                    }
                } else {
                    showValidation(res.message || 'Save failed.');
                    resetSaveBtn();
                }
            },
            error: function () {
                showValidation('Server error. Please try again.');
                resetSaveBtn();
            }
        });
    });

});
</script>
