<style>
    .cr-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
    .cr-header { padding: 12px 18px !important; }
    .cr-header-flex { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .cr-page-title { font-size: 16px; font-weight: 600; }
    .cr-form-section { padding: 16px 18px 8px; }
    .cr-form-section .form-group { margin-bottom: 10px; }
    .cr-form-section label { font-weight: 600; font-size: 13px; }
    .cr-readonly { background: #f5f5f5 !important; font-weight: 600; color: #333 !important; }
    .cr-due-overdue { color: #dc3545 !important; font-weight: 700; }

    /* ── Select2 — matches sale screen ── */
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

    /* ── Invoice table ── */
    #crInvoiceTable {
        border-collapse: collapse !important; border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important;
        border: 1px solid #E2E8F0 !important;
    }
    #crInvoiceTable thead th {
        background: #F1F5F9 !important; color: #475569 !important;
        font-size: 11px !important; font-weight: 700 !important;
        text-transform: uppercase !important; letter-spacing: .7px !important;
        border-bottom: 2px solid #DDD6FE !important; border-top: none !important;
        padding: 11px 8px !important; white-space: nowrap !important;
    }
    #crInvoiceTable tbody td {
        padding: 6px 7px !important; font-size: 13px !important;
        color: #374151 !important; border-color: #F3F0FE !important;
        vertical-align: middle !important;
    }
    #crInvoiceTable tbody tr:hover td { background: #F3F0FE !important; }
    #crInvoiceTable tfoot td {
        background: #F8FAFC !important; border-top: 2px solid #DDD6FE !important;
        font-size: 13px !important; padding: 8px !important; font-weight: 700 !important;
    }
    #crInvoiceTable .form-control {
        height: 30px !important; font-size: 12px !important; padding: 2px 6px !important;
        border: 1px solid #E2E8F0 !important; border-radius: 6px !important;
    }
    #crInvoiceTable .form-control:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important;
    }
    /* select2 inside table rows — compact to match row height */
    #crInvoiceTable .select2-container { width: 100% !important; min-width: 160px; }
    #crInvoiceTable .select2-container .select2-selection--single {
        height: 30px !important; border-radius: 6px !important;
    }
    #crInvoiceTable .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important; font-size: 12px !important;
    }
    #crInvoiceTable .select2-container .select2-selection--single .select2-selection__arrow {
        height: 28px !important;
    }

    /* add-row button cell */
    .cr-tfoot-btn { padding: 10px 8px !important; background: #fff !important; border-top: 2px solid #DDD6FE !important; }

    /* ── Responsive card layout ── */
    .cr-td-label { display: none; }
    @media (max-width: 1024px) {
        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #crInvoiceTable { display: block; width: 100%; }
        #crInvoiceTable thead { display: none; }
        #crInvoiceTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #crInvoiceTable tbody tr {
            display: grid; grid-template-columns: 1fr 1fr;
            width: 100%; box-sizing: border-box; margin-bottom: 16px;
            border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #crInvoiceTable tbody td {
            display: block; width: 100%; box-sizing: border-box;
            padding: 8px 12px !important; border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
        }
        #crInvoiceTable tbody td:nth-child(even) { border-right: none !important; }
        #crInvoiceTable tbody td:last-child {
            grid-column: 1 / -1; border-bottom: none !important; border-right: none !important;
        }
        .cr-td-label { display: block; font-size: 10px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px; }
        #crInvoiceTable tfoot { display: block; margin-top: 10px; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; }
        #crInvoiceTable tfoot tr { display: flex; flex-direction: column; }
        #crInvoiceTable tfoot td { display: none !important; }
        #crInvoiceTable tfoot td[data-label] {
            display: block !important; width: 100%; box-sizing: border-box;
            padding: 8px 14px !important; border-bottom: 1px solid #f0f0f0 !important;
        }
        #crInvoiceTable tfoot td[data-label]::before {
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
                
<a href="<?php echo base_url('manage_customer_receive') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <div id="cr_edit_banner" style="display:none; background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing <span id="cr_edit_voucher_label"></span> — changes are saved only when you click Save.
            </div>

            <div class="cr-form-section">

                <!-- Row 1: Date | Voucher Count -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Date <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <?php date_default_timezone_set('Asia/Colombo'); ?>
                                <input type="text" class="form-control datepicker" id="cr_date" name="cr_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Voucher Count</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="cr_voucher_count" readonly value="0">
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
                                <select class="form-control" id="cr_branch" name="cr_branch"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Total Branch Due</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="cr_total_branch_due" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Customer | Total Due -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Customer <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="cr_customer" name="cr_customer">
                                    <option value="">Select Customer</option>
                                    <?php foreach ($customers as $c): ?>
                                        <option value="<?php echo $c['customer_id']; ?>"><?php echo $c['customer_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Total Due</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control cr-readonly" id="cr_total_due" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Payment Type | Source of Sale -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Payment Type <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="cr_payment_type" name="cr_payment_type">
                                    <option value="">Select Payment Type</option>
                                    <?php foreach ($all_pmethod as $pm): ?>
                                        <option value="<?php echo $pm['id']; ?>"><?php echo $pm['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Source of Sale <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="cr_source" name="cr_source">
                                    <option value="product">Sale of Product</option>
                                    <option value="service">Sale of Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <br>

                <!-- Invoice rows table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="crInvoiceTable">
                        <thead>
                            <tr>
                                <th style="min-width:160px;" id="cr_invoice_col_header">Invoice No</th>
                                <th style="min-width:110px;">Invoice Date</th>
                                <th style="min-width:110px;">Due Date</th>
                                <th style="min-width:110px;">Invoice Amt</th>
                                <th style="min-width:110px;">Returned Amt</th>
                                <th style="min-width:110px;">Paid Amt</th>
                                <th style="min-width:110px;">Due Amt</th>
                                <th style="min-width:110px;">Settle Amt</th>
                                <th style="min-width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cr_invoice_tbody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm" id="cr_add_row_btn" disabled>
                                        <i class="fa fa-plus"></i> Add Invoice
                                    </button>
                                </td>
                                <td colspan="2" class="text-right" data-label="Total"><strong>Total</strong></td>
                                <td data-label="Invoice Amt" id="cr_foot_invoice">0.00</td>
                                <td data-label="Returned Amt" id="cr_foot_returned">0.00</td>
                                <td data-label="Paid Amt" id="cr_foot_paid">0.00</td>
                                <td data-label="Due Amt" id="cr_foot_due">0.00</td>
                                <td data-label="Settle Amt" id="cr_foot_settle">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save row -->
                <div class="row" style="margin-top:16px; margin-bottom:8px;">
                    <div class="col-sm-12 text-right">
                        <span id="cr_validation_msg" class="text-danger" style="font-size:13px; margin-right:12px; display:none;"></span>
                        <button type="button" class="btn btn-success btn-save-anim" id="cr_save_btn" disabled>
                            <i class="fa fa-save" id="cr_save_btn_icon"></i> <span id="cr_save_btn_label">Save</span>
                        </button>
                    </div>
                </div>

            </div><!-- /.cr-form-section -->
        </div>
    </div>
</div>

<script>
var base_url     = "<?php echo base_url(); ?>";
var crInvoices   = [];
var crRowCount   = 0;
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
            url: base_url + 'getCustomerReceiveForEdit',
            data: { id: editId },
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false }; }

                if (!res.success) {
                    alert(res.message || 'Could not load that Customer Receive for editing.');
                    editId = 0; // fall back to a plain new-receive form
                    loadBranchDropdown(0, 0);
                    return;
                }

                pendingEditDetails = res.details || [];

                if (res.date) $('#cr_date').val(res.date);
                if (res.voucher_no) {
                    $('#cr_edit_voucher_label').text(res.voucher_no);
                    $('#cr_edit_banner').show();
                }
                if (res.payment_type) {
                    $('#cr_payment_type').val(res.payment_type).trigger('change');
                }
                // Source must be set before loadBranchDropdown fires the
                // customer "change" handler below, since that's what kicks
                // off fetchInvoiceList (which reads #cr_source).
                $('#cr_source').val(res.source || 'product').trigger('change');

                $('.cr-page-title').text('Update Customer Receive');
                $('#cr_save_btn_label').text('Update');
                $('#cr_save_btn_icon').attr('class', 'fa fa-pencil');

                loadBranchDropdown(parseInt(res.branch, 10) || 0, parseInt(res.customer, 10) || 0);
            },
            error: function () {
                alert('Could not load that Customer Receive for editing.');
                editId = 0;
                loadBranchDropdown(0, 0);
            }
        });
    } else {
        // Default Customer to "Cash" (id 1) for a new receive, same as the Sale screen.
        loadBranchDropdown(0, 1);
        // Default Payment Type to "Cash" too — matched by option text rather
        // than a hardcoded id, same convention as the freeze logic on the Sale screen.
        $('#cr_payment_type option').filter(function () {
            return $(this).text().trim() === 'Cash';
        }).prop('selected', true);
        $('#cr_payment_type').trigger('change');
    }

    /* ── Branch ── */
    function loadBranchDropdown(selectedId, selectedCustomerId) {
        $.ajax({
            type: 'POST', url: base_url + 'store/store/getbranchbyuserid',
            success: function (data) {
                var branches = JSON.parse(data);
                var $sel = $('#cr_branch');
                $sel.empty().append('<option value="">Select Branch</option>');
                $.each(branches, function (i, b) {
                    $sel.append('<option value="' + b.id + '">' + b.name + '</option>');
                    if (b.default != 0) $sel.val(b.id);
                });
                if (selectedId > 0) $sel.val(selectedId);
                $sel.trigger('change');

                if (selectedCustomerId > 0) {
                    $('#cr_customer').val(selectedCustomerId).trigger('change');
                }
            }
        });
    }

    /* ── Changing Date, Branch or Customer clears the invoice table below —
       rows already picked may no longer apply once any of those change. ── */
    function clearInvoiceTable() {
        $('#cr_invoice_tbody').empty();
        crRowCount = 0;
        resetFooter();
        $('#cr_add_row_btn').prop('disabled', crInvoices.length === 0);
    }

    /* ── Customer change ── */
    $('#cr_customer').on('change', function () {
        crInvoices = [];
        clearInvoiceTable();

        var customer_id = $(this).val();
        if (!customer_id) {
            $('#cr_voucher_count').val(0);
            $('#cr_total_branch_due').val('0.00');
            $('#cr_total_due').val('0.00');
            return;
        }
        fetchSummary(customer_id);
        fetchInvoiceList(customer_id);
    });

    /* ── Add Invoice button (document-level for safety) ── */
    $(document).on('click', '#cr_add_row_btn', function () {
        addInvoiceRow();
    });

    /* ── Branch change: Total Due stays across all branches (crInvoices is
       unfiltered) — only the table and the invoice picker's options are
       branch-scoped, so clear already-picked rows and let the user re-add
       from this branch's invoices. ── */
    $('#cr_branch').on('change', function () {
        clearInvoiceTable();
        if ($('#cr_customer').val()) updateSummaryFromInvoices();
    });

    /* ── Date change: also clears the table, same as Branch/Customer ── */
    $('#cr_date').on('change', function () {
        clearInvoiceTable();
    });

    /* ── Source of Sale change: swaps which ledger (Sale of Product vs Sale
       of Service) is being settled — clears the table and re-fetches due
       invoices for the current customer under the new source. ── */
    $('#cr_source').on('change', function () {
        var isService = $(this).val() === 'service';
        $('#cr_invoice_col_header').text(isService ? 'Service No' : 'Invoice No');
        crInvoices = [];
        clearInvoiceTable();
        var customer_id = $('#cr_customer').val();
        if (customer_id) fetchInvoiceList(customer_id);
    });

    /* ── Summary header ── (now driven from crInvoices after load) ── */
    function fetchSummary(customer_id) {
        // no-op: summary is calculated inside fetchInvoiceList via updateSummaryFromInvoices
    }

    /* ── Fetch invoice list — also computes header totals from the same data ──
       Deliberately NOT filtered by branch here: Total Due must reflect the
       customer's full due across all branches. Branch only narrows which
       invoices are selectable (see buildInvoiceOptions). ── */
    function fetchInvoiceList(customer_id) {
        $.ajax({
            type: 'POST', url: base_url + 'getCustomerDueInvoices',
            data: { customer_id: customer_id, exclude_cr_id: editId, source: $('#cr_source').val() },
            success: function (raw) {
                try {
                    var rows = typeof raw === 'string' ? JSON.parse(raw) : raw;
                    crInvoices = rows || [];
                } catch(e) {
                    console.error('Invoice list parse error:', raw);
                    crInvoices = [];
                }
                updateSummaryFromInvoices();
                $('#cr_add_row_btn').prop('disabled', crInvoices.length === 0);
                applyPendingEditDetails();
            },
            error: function (xhr) {
                console.error('Invoice list AJAX error:', xhr.responseText);
                crInvoices = [];
            }
        });
    }

    function updateSummaryFromInvoices() {
        var branchId    = String($('#cr_branch').val());
        var totalDue    = 0;
        var branchDue   = 0;
        $.each(crInvoices, function (i, inv) {
            var due = parseFloat(inv.due_amount) || 0;
            totalDue += due;
            if (String(inv.branch) === branchId) branchDue += due;
        });
        $('#cr_voucher_count').val(crInvoices.length);
        $('#cr_total_due').val(totalDue.toFixed(2));
        $('#cr_total_branch_due').val(branchDue.toFixed(2));
    }

    /* ── Re-apply the voided receive's invoice/settle rows (edit flow) ── */
    function applyPendingEditDetails() {
        if (!pendingEditDetails.length) return;
        var details = pendingEditDetails;
        pendingEditDetails = []; // consume once, so a later manual customer change doesn't replay it

        $.each(details, function (i, d) {
            var saleId = String(d.sale_id);
            var exists = false;
            $.each(crInvoices, function (j, inv) { if (String(inv.sale_id) === saleId) { exists = true; return false; } });
            if (!exists) return; // invoice no longer has a matching due row

            _doAddRow();
            var idx = crRowCount;
            $('#cr_inv_sel_' + idx).val(saleId).trigger('change');
            $('#cr_row_' + idx).find('.cr-settle-input').val(parseFloat(d.settle_amount).toFixed(2)).trigger('input');
        });

        toggleSaveBtn();
    }

    function addInvoiceRow() {
        // If invoice list not loaded yet, re-fetch then add row
        if (crInvoices.length === 0) {
            var cid = $('#cr_customer').val();
            if (!cid) return;
            $.ajax({
                type: 'POST', url: base_url + 'getCustomerDueInvoices',
                data: { customer_id: cid, exclude_cr_id: editId, source: $('#cr_source').val() },
                success: function (raw) {
                    try { crInvoices = (typeof raw === 'string' ? JSON.parse(raw) : raw) || []; } catch(e) { crInvoices = []; }
                    _doAddRow();
                }
            });
            return;
        }
        _doAddRow();
    }

    /* Invoice picker is scoped to the currently selected branch (Total Due
       itself is NOT — it's summed from the full, unfiltered crInvoices). */
    function buildInvoiceOptions(usedIds) {
        var branchId = String($('#cr_branch').val() || '');
        var opts = '<option value="">-- Select Invoice --</option>';
        $.each(crInvoices, function (i, inv) {
            if (branchId && String(inv.branch) !== branchId) return;
            if ((parseFloat(inv.due_amount) || 0) <= 0) return;
            if (usedIds.indexOf(String(inv.sale_id)) === -1) {
                opts += '<option value="' + inv.sale_id + '">' + inv.voucher_no + ' (Due: ' + inv.due_amount + ')</option>';
            }
        });
        return opts;
    }

    function _doAddRow() {
        crRowCount++;
        var idx = crRowCount;

        // Build options — exclude already-selected sale_ids, scope to branch
        var usedIds = getUsedSaleIds();
        var opts = buildInvoiceOptions(usedIds);

        var tr = '<tr id="cr_row_' + idx + '" data-idx="' + idx + '">'
            + '<td><span class="cr-td-label">Invoice No</span>'
            +   '<select class="cr-inv-select" id="cr_inv_sel_' + idx + '" data-idx="' + idx + '">' + opts + '</select>'
            + '</td>'
            + '<td><span class="cr-td-label">Invoice Date</span><span class="cr-inv-date">-</span></td>'
            + '<td><span class="cr-td-label">Due Date</span><span class="cr-inv-due-date">-</span></td>'
            + '<td><span class="cr-td-label">Invoice Amt</span><span class="cr-inv-amount">0.00</span></td>'
            + '<td><span class="cr-td-label">Returned Amt</span><span class="cr-inv-returned">0.00</span></td>'
            + '<td><span class="cr-td-label">Paid Amt</span><span class="cr-inv-paid">0.00</span></td>'
            + '<td><span class="cr-td-label">Due Amt</span><span class="cr-inv-due">0.00</span></td>'
            + '<td><span class="cr-td-label">Settle Amt</span>'
            +   '<input type="number" class="form-control cr-settle-input" min="0" step="0.01" placeholder="0.00" value="" disabled>'
            + '</td>'
            + '<td class="text-center">'
            +   '<button type="button" class="btn btn-danger btn-sm cr-delete-row" data-idx="' + idx + '">'
            +   '<i class="fa fa-trash"></i></button>'
            + '</td>'
            + '</tr>';

        $('#cr_invoice_tbody').append(tr);

        $('#cr_inv_sel_' + idx).select2({
            width: '100%',
            placeholder: '-- Select Invoice --',
            allowClear: true
        });
    } // end _doAddRow

    /* ── Invoice dropdown change → fill row ── */
    $(document).on('change', '.cr-inv-select', function () {
        var idx    = $(this).data('idx');
        var saleId = $(this).val();
        var $row   = $('#cr_row_' + idx);

        if (!saleId) {
            $row.find('.cr-inv-date').text('-');
            $row.find('.cr-inv-due-date').text('-').removeClass('cr-due-overdue');
            $row.find('.cr-inv-amount').text('0.00');
            $row.find('.cr-inv-returned').text('0.00');
            $row.find('.cr-inv-paid').text('0.00');
            $row.find('.cr-inv-due').text('0.00');
            $row.find('.cr-settle-input').val('').prop('disabled', true);
            $row.data('sale-id', '');
            recalcFooter();
            return;
        }

        var inv = null;
        $.each(crInvoices, function (i, r) {
            if (String(r.sale_id) === String(saleId)) { inv = r; return false; }
        });
        if (!inv) return;

        $row.data('sale-id', saleId);
        $row.find('.cr-inv-date').text(inv.invoice_date);
        $row.find('.cr-inv-due-date').text(inv.due_date).toggleClass('cr-due-overdue', isOverdueDate(inv.due_date));
        $row.find('.cr-inv-amount').text(inv.invoice_amount);
        $row.find('.cr-inv-returned').text(inv.returned_amount);
        $row.find('.cr-inv-paid').text(inv.paid_amount);
        $row.find('.cr-inv-due').text(inv.due_amount);
        $row.find('.cr-settle-input')
            .val('')
            .attr('max', inv.due_amount)
            .prop('disabled', false)
            .focus();

        recalcFooter();

        // Refresh options in other rows to hide newly selected invoice
        refreshAllDropdownOptions();
    });

    /* ── Settle input → recalc footer + toggle save ── */
    $(document).on('input', '.cr-settle-input', function () {
        recalcFooter();
        toggleSaveBtn();
    });

    /* ── Delete row ── */
    $(document).on('click', '.cr-delete-row', function () {
        $('#cr_row_' + $(this).data('idx')).remove();
        refreshAllDropdownOptions();
        recalcFooter();
        toggleSaveBtn();
    });

    /* ── Helpers ── */
    function isOverdueDate(dateStr) {
        if (!dateStr || dateStr === '-') return false;
        var now = new Date();
        var todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
        return dateStr < todayStr;
    }

    function getUsedSaleIds() {
        var ids = [];
        $('#cr_invoice_tbody tr').each(function () {
            var sid = $(this).data('sale-id');
            if (sid) ids.push(String(sid));
        });
        return ids;
    }

    function refreshAllDropdownOptions() {
        var usedIds = getUsedSaleIds();
        $('#cr_invoice_tbody tr').each(function () {
            var $row   = $(this);
            var $sel   = $row.find('.cr-inv-select');
            var curVal = $sel.val();

            $sel.find('option').each(function () {
                var v = $(this).val();
                if (!v) return;
                // hide if used by another row; always show current row's own selection
                if (usedIds.indexOf(v) !== -1 && v !== String(curVal)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
    }

    function recalcFooter() {
        var totInvoice = 0, totReturned = 0, totPaid = 0, totDue = 0, totSettle = 0;
        $('#cr_invoice_tbody tr').each(function () {
            totInvoice  += parseFloat($(this).find('.cr-inv-amount').text())   || 0;
            totReturned += parseFloat($(this).find('.cr-inv-returned').text())  || 0;
            totPaid     += parseFloat($(this).find('.cr-inv-paid').text())      || 0;
            totDue      += parseFloat($(this).find('.cr-inv-due').text())       || 0;
            totSettle   += parseFloat($(this).find('.cr-settle-input').val())   || 0;
        });
        $('#cr_foot_invoice').text(totInvoice.toFixed(2));
        $('#cr_foot_returned').text(totReturned.toFixed(2));
        $('#cr_foot_paid').text(totPaid.toFixed(2));
        $('#cr_foot_due').text(totDue.toFixed(2));
        $('#cr_foot_settle').text(totSettle.toFixed(2));
    }

    function resetFooter() {
        $('#cr_foot_invoice,#cr_foot_returned,#cr_foot_paid,#cr_foot_due,#cr_foot_settle').text('0.00');
    }

    function toggleSaveBtn() {
        var hasValidRow = false;
        $('#cr_invoice_tbody tr').each(function () {
            var saleId  = $(this).data('sale-id');
            var settle  = parseFloat($(this).find('.cr-settle-input').val()) || 0;
            if (saleId && settle > 0) { hasValidRow = true; return false; }
        });
        $('#cr_save_btn').prop('disabled', !hasValidRow);
    }

    /* ── Validation message helper ── */
    function showValidation(msg) {
        $('#cr_validation_msg').text(msg).show();
    }
    function clearValidation() {
        $('#cr_validation_msg').text('').hide();
    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    /* ── Print the receipt after save/update — same pattern as Sale's save()/update_sale() ── */
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

    /* ── Save ── */
    $('#cr_save_btn').on('click', function () {
        clearValidation();

        var date         = $('#cr_date').val();
        var branch       = $('#cr_branch').val();
        var customer     = $('#cr_customer').val();
        var payment_type = $('#cr_payment_type').val();
        var source       = $('#cr_source').val();

        if (!date) { showValidation('Please select a Date.'); return; }
        if (!branch) { showValidation('Please select a Branch.'); return; }
        if (!customer) { showValidation('Please select a Customer.'); return; }
        if (!payment_type) { showValidation('Please select a Payment Type.'); return; }

        var invoices = [];
        var errMsg   = '';

        $('#cr_invoice_tbody tr').each(function () {
            var saleId  = $(this).data('sale-id');
            var settle  = parseFloat($(this).find('.cr-settle-input').val()) || 0;
            var maxDue  = parseFloat($(this).find('.cr-inv-due').text()) || 0;
            var voucher = $(this).find('.cr-inv-select option:selected').text().split('(')[0].trim();

            if (!saleId) return; // skip rows with no invoice selected

            if (settle <= 0) {
                errMsg = 'Settle amount for "' + voucher + '" must be greater than 0.';
                return false;
            }
            if (settle > maxDue + 0.001) {
                errMsg = 'Settle amount for "' + voucher + '" cannot exceed Due amount (' + maxDue.toFixed(2) + ').';
                return false;
            }
            invoices.push({ sale_id: saleId, settle_amount: settle.toFixed(2) });
        });

        if (errMsg) { showValidation(errMsg); return; }
        if (invoices.length === 0) { showValidation('Please add at least one invoice with a settle amount.'); return; }

        var $btn = $(this);
        var isEdit = editId > 0;
        $btn.addClass('btn-save-anim-loading').prop('disabled', true);
        $('#cr_save_btn_icon').attr('class', 'fa fa-spinner fa-spin');
        $('#cr_save_btn_label').text(isEdit ? 'Updating...' : 'Saving...');

        function resetSaveBtn() {
            $btn.removeClass('btn-save-anim-loading').prop('disabled', false);
            $('#cr_save_btn_icon').attr('class', isEdit ? 'fa fa-pencil' : 'fa fa-save');
            $('#cr_save_btn_label').text(isEdit ? 'Update' : 'Save');
        }

        $.ajax({
            type: 'POST',
            url: base_url + 'saveCustomerReceive',
            data: {
                date:         date,
                branch:       branch,
                customer:     customer,
                payment_type: payment_type,
                source:       source,
                type2:        type2,
                invoices:     JSON.stringify(invoices),
                edit_id:      editId
            },
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch(e) { res = { success: false, message: raw }; }
                if (res.success) {
                    clearValidation();
                    // $('#cr_success_alert .cr-voucher-no').text(res.voucher_no);
                    // $('#cr_success_alert').fadeIn(300);
                    // setTimeout(function () { $('#cr_success_alert').fadeOut(400); }, 4000);

                    alert(res.message)
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    if (res.details) {
                        printRawHtml(res.details, res.voucher_no);
                    } else {
                        location.reload();
                    }

                    // Reset form
                    // $('#cr_customer').val(null).trigger('change');
                    // crInvoices = []; crRowCount = 0;
                    // $('#cr_invoice_tbody').empty();
                    // resetFooter();
                    // $('#cr_voucher_count').val(0);
                    // $('#cr_total_due,#cr_total_branch_due').val('0.00');
                    // $('#cr_add_row_btn').prop('disabled', true);
                    // $btn.prop('disabled', true).html('<i class="fa fa-save"></i> Save');
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
