<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    .product_field { width: 200px; }
    .field { width: 30px; }
    .unit { width: 70px; }
    .qty { width: 100px; }
    .rate { width: 150px; }

    /* ── Locked state: this return's invoice already has a customer receive settled against it ── */
    .invoice-locked .inv-form-section,
    .invoice-locked #saleTable {
        opacity: 0.65;
        filter: grayscale(15%);
    }
    .invoice-locked-banner {
        background: #FEF3C7;
        color: #92400E;
        border: 1px solid #FBBF24;
        border-radius: 6px;
        padding: 10px 16px;
        margin: 12px 0;
        font-size: 13px;
        font-weight: 600;
    }
    .invoice-locked-banner i {
        margin-right: 6px;
    }

    /* ── Table redesign ── */
    #saleTable {
        border-collapse: collapse !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important;
        border: 1px solid #E2E8F0 !important;
    }
    #saleTable thead th {
        background: #F1F5F9 !important;
        color: #475569 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: .7px !important;
        border-bottom: 2px solid #DDD6FE !important;
        border-top: none !important;
        padding: 11px 8px !important;
        white-space: nowrap !important;
    }
    #saleTable thead th .text-danger { color: #ef4444 !important; }
    #saleTable tbody td {
        padding: 8px 8px !important;
        font-size: 13px !important;
        color: #374151 !important;
        border-color: #F3F0FE !important;
        vertical-align: middle !important;
    }
    #saleTable tbody tr:hover td { background: #F3F0FE !important; }
    #saleTable tfoot td {
        background: #F8FAFC !important;
        border-top: 2px solid #DDD6FE !important;
        border-color: #F3F0FE !important;
    }
    #saleTable tfoot tr:last-child td {
        background: #F3F0FE !important;
        border-top: 2px solid #DDD6FE !important;
        font-weight: 700 !important;
    }

    /* ── Input redesign ── */
    input.form-control {
        border: 1px solid #E2E8F0 !important;
        border-radius: 6px !important;
        color: #374151 !important;
        transition: border-color .18s, box-shadow .18s !important;
    }
    input.form-control:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
        outline: none !important;
    }

    /* ── Select2 ── */
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background: #F8FAFC !important;
        height: 34px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important; font-size: 13px !important; line-height: 32px !important; padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow b {
        border-color: #64748B transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important; margin-top: -1px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #16A34A transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #16A34A !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12) !important;
        outline: none !important;
    }
    .select2-selection__placeholder { color: #94A3B8 !important; }
    .select2-dropdown {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 16px rgba(0,0,0,.10) !important;
        margin-top: 2px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        padding: 5px 8px !important;
    }
    .select2-results__option { font-size: 13px !important; padding: 7px 12px !important; color: #374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #8b5cf6 !important; color: #fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background: #F3F0FE !important; color: #8b5cf6 !important; }

    /* ── Panel ── */
    .inv-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
    .inv-panel > .panel-heading { border-radius: 5px 5px 0 0; }
    .inv-header { padding: 12px 18px !important; }
    .inv-header-flex { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .inv-page-title { font-size: 16px; font-weight: 600; }

    /* ── Form section ── */
    .inv-form-section { padding: 16px 18px 8px; }
    .inv-form-section .form-group { margin-bottom: 10px; }
    .inv-form-section label { font-weight: 600; font-size: 13px; }

    /* ── Mobile labels (hidden on desktop) ── */
    .td-mobile-label { display: none; }

    /* Table desktop compaction */
    #saleTable { font-size: 12px; }
    #saleTable thead th { white-space: nowrap; font-size: 11px; padding: 8px 6px; font-weight: 700; }
    #saleTable tbody tr td { padding: 4px 5px; vertical-align: middle; }
    #saleTable tbody td .form-control { height: 30px; font-size: 12px; padding: 2px 6px; }
    #saleTable tbody td select.form-control { padding: 2px 4px; }
    #saleTable tfoot tr td { font-size: 13px; padding: 6px 8px; }
    #saleTable tfoot .form-control { height: 32px; font-size: 13px; }

    /* .product_field/.field/.unit/.qty/.rate above pin each column to a small
       fixed px width; on wide screens their sum is far less than the table's
       container width, leaving a large dead gap on the right. Let columns
       stretch to fill on large screens by switching those to min-widths. */
    @media (min-width: 1025px) {
        #saleTable { width: 100%; table-layout: auto; }
        #saleTable td.product_field { width: auto; min-width: 200px; }
        #saleTable td.field { width: auto; min-width: 30px; }
        #saleTable td.unit { width: auto; min-width: 70px; }
        #saleTable td.qty { width: auto; min-width: 100px; }
        #saleTable td.rate { width: auto; min-width: 150px; }
    }

    /* ── Tablet: 768px–1024px ── */
    @media (min-width: 768px) and (max-width: 1024px) {
        .inv-panel { border-radius: 10px; }
        .inv-header { padding: 14px 22px !important; }
        .inv-page-title { font-size: 17px; }
        .inv-form-section { padding: 18px 20px 14px; }
        .inv-form-section > .row:not(.form-group) {
            display: grid; grid-template-columns: 1fr 1fr; gap: 12px 20px; margin: 0;
        }
        .inv-form-section > .row:not(.form-group) > [class*="col-sm"] {
            width: 100% !important; float: none !important; padding: 0;
        }
        .inv-form-section > .row:not(.form-group) > .col-sm-6:last-child { grid-column: 1 / -1; }
        .inv-form-section .form-group.row { display: block; margin: 0; padding: 0; }
        .inv-form-section .form-group.row label.col-form-label {
            display: block; width: 100% !important; float: none !important;
            font-size: 10.5px; font-weight: 700; color: #777;
            text-transform: uppercase; letter-spacing: .4px;
            padding: 0 0 5px 0; margin: 0;
        }
        .inv-form-section .form-group.row > div[class*="col-sm"] {
            display: block; width: 100% !important; float: none !important; padding: 0;
        }
        .inv-form-section .form-group.row .form-control { width: 100%; height: 38px; font-size: 13px; border-radius: 5px; }

        .table-responsive { overflow: visible !important; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }
        .td-mobile-label { display: block; }
        #saleTable tbody tr {
            display: grid; grid-template-columns: 1fr 1fr; width: 100%; box-sizing: border-box;
            margin-bottom: 24px; border: 1px solid #ebebeb; border-radius: 10px; overflow: hidden;
            background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] { display: grid !important; grid-template-columns: 1fr 1fr; width: 100% !important; }
        #saleTable tbody td {
            display: block; width: 100%; box-sizing: border-box; padding: 8px 12px;
            border: none !important; border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important; white-space: normal;
        }
        #saleTable tbody td:nth-child(even) { border-right: none !important; }
        #saleTable tbody td:last-child {
            grid-column: 1 / -1; border-bottom: none !important; border-right: none !important; padding: 0;
        }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button, #saleTable tbody td:last-child .btn {
            width: 100%; border-radius: 0; margin: 0; display: block;
        }
        #saleTable tbody td .form-control,
        #saleTable tbody td > select,
        #saleTable tbody td > div,
        #saleTable tbody td .select2-container { width: 100% !important; box-sizing: border-box; }
        #saleTable tfoot {
            display: block; margin-top: 14px; border: 1px solid #e0e0e0;
            border-radius: 10px; background: #fff;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important; width: 100%; box-sizing: border-box;
            padding: 9px 14px; border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label); display: block;
            font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] input[type="text"] {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: 34px !important; text-align: right;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px !important; font-weight: 700 !important; }
    }

    /* ── Mobile: card layout ── */
    @media (max-width: 767px) {
        .inv-header-flex { flex-direction: column; align-items: flex-start; }
        .inv-form-section .col-sm-6 { width: 100%; float: none; }
        .table-responsive .col-sm-6.table-bordered { width: 100% !important; float: none; box-sizing: border-box; }
        .form-group.row.text-right .col-sm-12 { text-align: center; }
        .form-group.row.text-right .btn-success { width: 100%; }
        .table-responsive { overflow: visible; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }
        #saleTable tbody tr {
            display: block; width: 100%; box-sizing: border-box;
            margin-bottom: 16px; border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] { display: block !important; width: 100% !important; }
        #saleTable tbody td {
            display: block; width: 100%; box-sizing: border-box;
            padding: 6px 10px; border: none !important;
            border-bottom: 1px solid #f0f0f0 !important; white-space: normal;
        }
        #saleTable tbody td:last-child { border-bottom: none !important; padding: 0; }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button, #saleTable tbody td:last-child .btn {
            width: 100%; border-radius: 0; margin: 0; display: block;
        }
        #saleTable tbody td.vathidden[style*="table-cell"] {
            display: block !important; width: 100% !important; box-sizing: border-box;
            padding: 6px 10px; border-bottom: 1px solid #f0f0f0 !important;
        }
        .td-mobile-label {
            display: block; font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px;
        }
        #saleTable tbody td .form-control,
        #saleTable tbody td input[type="text"],
        #saleTable tbody td input[type="number"],
        #saleTable tbody td select,
        #saleTable tbody td > div,
        #saleTable tbody td .select2-container { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }
        #saleTable tfoot {
            display: block; margin-top: 14px; border: 1px solid #e0e0e0;
            border-radius: 10px; background: #fff; overflow: hidden;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important; width: 100%; box-sizing: border-box;
            padding: 8px 12px; border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label); display: block;
            font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] input[type="text"] {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: 34px !important; text-align: right;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px !important; font-weight: 700 !important; }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header" id="style12">
                <div class="inv-header-flex has-goback">
                    <span id="title" class="inv-page-title"><?php echo $title; ?></span>
                
<a href="<?php echo base_url('manage_sales_return') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <?php if (!empty($id)): ?>
            <div id="sr_edit_banner" style="background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing <span id="sr_edit_label">Sales Return #<?php echo (int)$id; ?></span> — changes are saved only when you click Update.
            </div>
            <?php endif; ?>

            <div class="inv-form-section">

                <div class="row">

                    <div class="col-sm-3">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label">Return Date <i class="text-danger">*</i></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control datepicker" name="return_date" id="rdate"
                                    value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Branch <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="branch" name="branch" required
                                    onchange="getSalesOrderDropdown()"></select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3" id="showorderno">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Invoice Id</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="invoice_id" name="invoice_id"
                                    onchange="getSalesOrderDetails()"></select>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3" id="showorderno2">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Invoice Id</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="invoice_id1" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group row">
                            <label class="col-sm-5 col-form-label">Invoice Date <i class="text-danger">*</i></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control datepicker" name="sale_date" id="sale_date"
                                    value="<?php echo date('Y-m-d'); ?>" readonly>
                            </div>
                        </div>
                    </div>

                </div>



                <div class="row">



                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Invoice Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="invoicetype" required name="invoicetype" tabindex="3" onchange="incidetTypechange()" disabled>
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>

                                    <option value="cash_vat">Cash VAT</option>
                                    <option value="credit_vat">Credit VAT</option>

                                </select>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="incidenttype" required name="incidenttype" tabindex="3" disabled>
                                    <option value=""></option>
                                    <option value="1">Retail</option>
                                    <option value="2">Wholesale</option>

                                </select>
                            </div>

                        </div>

                    </div>




                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Customer
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select name="customer_id" id="customer_id" class="form-control " required="" tabindex="1" disabled>
                                    <option value="">Select an option</option>
                                    <?php foreach ($all_customer as $customer) { ?>
                                        <option value="<?php echo $customer['customer_id'] ?>">
                                            <?php echo $customer['customer_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Salesman
                            </label>
                            <div class="col-sm-6">
                                <select name="employee_id" id="employee_id" class="form-control " tabindex="1" disabled>
                                    <option value="">Select an option</option>
                                    <?php foreach ($all_employee as $employee) { ?>
                                        <option value="<?php echo $employee['id'] ?>">
                                            <?php echo $employee['first_name'] . " " . $employee['last_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                        </div>
                    </div>







                </div>

                <div class="row">


                </div>


                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="saleTable">
                        <thead>
                            <tr>
                                <th class="text-center product_field">Product<i
                                        class="text-danger">*</i></th>
                                <th class="text-center">Store<i class="text-danger">*</i>
                                </th>
                                <th class="text-center">Batch<i class="text-danger">*</i>
                                </th>
                                <th class="text-center ">Unit </th>
                                <th class="text-center ">Av.Qty</th>
                                <th class="text-center ">Qty<i
                                        class="text-danger">*</i></th>
                                <th class="text-center ">Return Qty<i
                                        class="text-danger">*</i></th>
                                <th class="text-center ">Return Store<i
                                        class="text-danger">*</i></th>
                                <th class="text-center ">Refund%<i
                                        class="text-danger">*</i></th>


                                <th class="text-center vathidden" id="vathidden">VAT.val</th>


                                <th class="text-center ">Total</th>


                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">
                                    <input class='form-control' type='text' id="productInput1" readonly tabindex="-1" style="background:#f5f5f5;cursor:default;" />
                                    <input type='hidden' name='product[]' id='product1' />
                                    <input type="hidden" id="mconversion_ratio1" />
                                    <input type="hidden" id="mastercost_price1" />
                                    <input type="hidden" id="bd1" />
                                    <input type="hidden" id="ad1" />
                                    <input type="hidden" id="isstock1" />
                                    <input type="hidden" id="invoicedetail1" />
                                </td>

                                <td class="rate">
                                    <select class="form-control" id="store1" name="store[]" tabindex="3" onchange="product_search(1,'store')" disabled>
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td class="rate">
                                    <select class="form-control" id="batch1" name="batch[]" tabindex="3" onchange="product_search(1,'batch')" disabled>
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="qty">
                                    <select class="form-control" id="unit1" required name="unit1" onchange="product_search(1,'unit')" tabindex="3" disabled>
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" id="conversionid1" />
                                    <input type="hidden" id="conversiontype1" />
                                    <input type="hidden" id="conversion_ratio1" />
                                </td>
                                <td class="qty">
                                    <input type="hidden" name="code[]" onkeyup="product_search(1,'code');"
                                        class="total_qntt_1 form-control text-right"
                                        id="code1" placeholder="0.00" min="0" readonly />
                                    <span id='codetype1' style="margin-left:5px"></span>
                                </td>



                                <td class="qty">
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" disabled />
                                    <input type="hidden" name="product_rate[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="product_rate1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                    <input type="hidden" name="discount_per[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="discount1" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    <input type="hidden" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />

                                </td>
                                <td class="rate">


                                    <input type="text" name="product_quantity[]" id="rqty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />


                                </td>

                                <td class="qty">
                                    <select class="form-control" id="rstore1" name="store[]" tabindex="3">
                                        <option value=""></option>
                                    </select>

                                </td>
                                <td class="qty">

                                    <input type="text" name="product_quantity[]" id="rdeduction1" min="0" class="form-control text-right store_cal_1" placeholder="0.00" value="" tabindex="6" onkeyup="calculate_sum(1);" />


                                </td>


                                <!-- VAT  start-->

                                <td class="rate vathidden">
                                    <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                    <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  end-->
                                <td class="product_field">
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price1" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="total_discount1" class="" />
                                    <input type="hidden" id="all_discount1" class="total_discount dppr" name="discount_amount[]" />
                                </td>



                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>">
                                    <td class="product_field">
                                        <input class='form-control' type='text' id="productInput<?php echo $i; ?>" readonly tabindex="-1" style="background:#f5f5f5;cursor:default;" />
                                        <input type='hidden' name='product[]' id='product<?php echo $i; ?>' />
                                        <input type="hidden" id="mconversion_ratio<?php echo $i; ?>" />
                                        <input type="hidden" id="mastercost_price<?php echo $i; ?>" />
                                        <input type="hidden" id="bd<?php echo $i; ?>" />
                                        <input type="hidden" id="ad<?php echo $i; ?>" />
                                        <input type="hidden" id="isstock<?php echo $i; ?>" />
                                        <input type="hidden" id="invoicedetail<?php echo $i; ?>" />
                                    </td>



                                    <td class="rate">
                                        <select class="form-control" id="store<?php echo $i; ?>" name="store[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'store')" disabled>
                                            <option value=""></option>
                                        </select>
                                    </td>

                                    <td class="rate">
                                        <select class="form-control" id="batch<?php echo $i; ?>" name="batch[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'batch')" disabled>
                                            <option value=""></option>
                                        </select>
                                    </td>



                                    <td class="qty">
                                        <select class="form-control" id="unit<?php echo $i; ?>" required name="unit<?php echo $i; ?>" onchange="product_search(<?php echo $i; ?>,'unit')" tabindex="3" disabled>
                                            <option value=""></option>
                                        </select>
                                        <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                        <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                        <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />
                                    </td>

                                    <td class="qty">
                                        <input type="hidden" name="code[]" onkeyup="product_search(<?php echo $i; ?>, 'code');" class="total_qntt_1 form-control text-right" id="code<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                        <span id='codetype<?php echo $i; ?>' style="margin-left:5px"></span>

                                    </td>

                                    <td class="qty">
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" disabled />
                                        <input type="hidden" name="product_rate[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="product_rate<?php echo $i; ?>" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                        <input type="hidden" name="discount_per[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="discount<?php echo $i; ?>" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                        <input type="hidden" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                        <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    </td>

                                    <td class="rate">


                                        <input type="text" name="product_quantity[]" id="rqty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />


                                    </td>

                                    <td class="qty">
                                        <select class="form-control" id="rstore<?php echo $i; ?>" name="store[]" tabindex="3">
                                            <option value=""></option>
                                        </select>

                                    </td>
                                    <td class="qty">

                                        <input type="text" name="product_quantity[]" id="rdeduction<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" placeholder="0.00" value="" tabindex="6" onkeyup="calculate_sum(<?php echo $i; ?>);" />


                                    </td>

                                    <!-- VAT start -->

                                    <td class="rate vathidden">
                                        <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                        <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT end -->

                                    <td class="product_field">
                                        <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price<?php echo $i; ?>" value="0.00" readonly="readonly" />
                                        <input type="hidden" id="total_discount<?php echo $i; ?>" class="" />
                                        <input type="hidden" id="all_discount<?php echo $i; ?>" class="total_discount dppr" name="discount_amount[]" />
                                    </td>


                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>


                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('ttl_val') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('ttl_val') ?>:</b></td>


                                <td class="text-right">

                                    <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />

                                </td>
                                <td> </td>
                            </tr>

                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('grand_total') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('grand_total') ?>:</b></td>


                                <td class="text-right">
                                    <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />

                                    <input type="hidden" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_store(1)" name="discount" placeholder="0.00" value="" />

                                    <input type="hidden" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                                    <input type="hidden" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />

                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <script>
                    /* Mobile card labels — runs after table HTML is parsed */
                    (function () {
                        var colLabels = ['Product','Store','Batch','Unit','Av.Qty','Qty','Return Qty','Return Store','Refund%','VAT.val','Total'];

                        document.querySelectorAll('#saleTable tbody tr').forEach(function (row) {
                            row.querySelectorAll('td').forEach(function (td, i) {
                                if (!colLabels[i]) return;
                                var lbl = document.createElement('span');
                                lbl.className = 'td-mobile-label';
                                lbl.textContent = colLabels[i];
                                td.insertBefore(lbl, td.firstChild);
                            });
                        });

                        [
                            ['total_vat_amnt', 'VAT Total'],
                            ['grandTotal',     'Grand Total']
                        ].forEach(function (pair) {
                            var el = document.getElementById(pair[0]);
                            if (el && el.parentNode) el.parentNode.setAttribute('data-label', pair[1]);
                        });
                    })();
                    </script>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-6 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row">

                                <!-- Payment Type -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="payments" class="col-form-label">
                                            <?php echo display('payment_type'); ?> <i class="text-danger">*</i>
                                        </label>
                                        <select name="multipaytype[]" class="form-control" id="your_dropdown_id" tabindex="1">
                                            <option value="">Select an option</option>
                                            <?php foreach ($all_pmethod as $services) { ?>
                                                <option value="<?php echo $services['id']; ?>">
                                                    <?php echo $services['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Details -->
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="details" class="col-form-label">
                                            <?php echo display('details'); ?>
                                        </label>
                                        <textarea
                                            class="form-control"
                                            tabindex="4"
                                            id="details"
                                            name="purchase_details"
                                            placeholder="<?php echo display('details'); ?>"
                                            rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <button id="save_add" class="btn btn-success btn-save-anim" name="add-invoice" onclick="save()">
                            <?php echo '<i class="fa ' . (empty($id) ? 'fa-save' : 'fa-pencil') . '"></i> ' . (empty($id) ? display('save') : display('update')); ?></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let batches=" . json_encode($batches) . ";";
echo "let units=" . json_encode($units) . ";";

echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "</script>";
?>
<script>
    $('body').addClass("sidebar-mini sidebar-collapse");

    let type2 = ""
    if (usertype == 3) {
        document.getElementById('style12').style.backgroundColor = '#E0E0E0';
        const title = document.getElementById('title');
        title.style.color = 'blue';
        type2 = "B"

    } else {
        type2 = "A"

    }
    let count = 2

    $(document).ready(function() {
        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }

        document.querySelectorAll('.vathidden').forEach(el => {
            el.style.display = 'none';
        });

        document.getElementById("showorderno2").style.display = "none";

        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getSalesReturnById',
                type: 'POST',
                data: {
                    id: id,
                    type2: type2
                },
                success: function(response) {
                    var sales = JSON.parse(response);
                    console.log(sales)
                    if (sales[0].sales_return_id && document.getElementById('sr_edit_label')) {
                        document.getElementById('sr_edit_label').textContent = 'Sales Return ' + sales[0].sales_return_id;
                    }
                    document.getElementById('sale_date').value = sales[0].date;
                    document.getElementById('rdate').value = sales[0].rdate;

                    document.getElementById('details').value = sales[0].details;

                    document.getElementById("showorderno").style.display = "none";
                    document.getElementById("showorderno2").style.display = "block";
                    document.getElementById('invoice_id1').value = sales[0].sale_id;







                    getBranchDropdown(sales[0].branch);


                    var $customerDropdown = $('#customer_id');
                    $customerDropdown.empty();
                    $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                    $.each(customers, function(index, customer) {
                        $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                    });
                    $customerDropdown.val(sales[0].customer_id)

                    var $employeeDropdown = $('#employee_id');
                    $employeeDropdown.empty();
                    $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                    $.each(employees, function(index, employee) {
                        $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                    });
                    $employeeDropdown.val(sales[0].employee_id)

                    var $paymentDropdown = $('#your_dropdown_id');
                    $paymentDropdown.empty();
                    $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                    $.each(pmethods, function(index, supplier) {
                        $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                    });
                    $paymentDropdown.val(sales[0].payment_type)

                    var $incidenttypeDropdown = $('#incidenttype');
                    $incidenttypeDropdown.empty();
                    $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
                    $incidenttypeDropdown.append('<option value="1">Retail</option>');
                    $incidenttypeDropdown.append('<option value="2">Whole Sale</option>');
                    $incidenttypeDropdown.val(sales[0].incidenttype)

                    var $invoiceTypeDropdown = $('#invoicetype');
                    $invoiceTypeDropdown.empty(); // Clear existing options
                    $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                    $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                    $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                    $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                    $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                    $invoiceTypeDropdown.val(sales[0].invoicetype);

                    var _receiveCount = parseInt(sales[0].receive_count) || 0;
                    if (_receiveCount > 0) {
                        freezeSalesReturnFormForDue('A customer receive has already been settled against this invoice. Editing is locked to protect the receive record.');
                    }

                    document.getElementById('total_discount_ammount').value = sales[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').value = sales[0].total_vat_amnt;
                    document.getElementById('grandTotal').value = sales[0].grandTotal;
                    document.getElementById('Total').value = sales[0].total;
                    document.getElementById('discount').value = sales[0].discount;

                    // count = 1;
                    for (let i = 0; i < sales.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        getActiveProduct(sales[i].product, a);
                        getActiveStore(sales[i].store, sales[i].rstore, a);

                        document.getElementById('qty' + a).value = sales[i].quantity;
                        document.getElementById('rqty' + a).value = sales[i].rqty;
                        document.getElementById('rdeduction' + a).value = sales[i].rdeduction;
                        document.getElementById('unit' + a).value = sales[i].unit;
                        document.getElementById('code' + a).value = sales[i].avstock;
                        document.getElementById('product_rate' + a).value = sales[i].product_rate;
                        document.getElementById('discount' + a).value = sales[i].discount2;
                        document.getElementById('discount_value' + a).value = sales[i].discount_value;
                        document.getElementById('mastercost_price' + a).value = sales[i].cost_price;
                        document.getElementById('invoicedetail' + a).value = sales[i].invoice_detail_id;

                        // if (vtinfo.ischecked == 1) {
                        //     document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                        // }
                        if (sales[0].invoicetype == 'cash_vat' ||
                            sales[0].invoicetype == 'credit_vat' ||
                            sales[0].invoicetype == 'svat') {
                            document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                            document.querySelectorAll('.vathidden').forEach(el => {
                                el.style.display = 'table-cell';
                            });
                            document.querySelectorAll('.vatshow').forEach(el => {
                                el.style.display = 'none';
                            });
                        } else {
                            document.getElementById('vat_percent' + a).value = 0;

                        }
                        document.getElementById('vat_value' + a).value = sales[i].vat_value;
                        document.getElementById('total_price' + a).value = sales[i].total_price;
                        document.getElementById('total_discount' + a).value = sales[i].total_discount;
                        document.getElementById('all_discount' + a).value = sales[i].all_discount;

                        getActiveSubUnitEdit(sales[i].product, a, sales[i].unit, sales[i].conversion_id,
                            sales[i].conversion_ratio, sales[i].convertiontype,
                            sales[i].avstock)

                        // getBatchDropdown(batches, a, sales[i].batch)
                        getBatchDropdown(batches, a, sales[i].batch, sales[i].product, sales[i].batchtype)



                        count = count + 1;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            getBranchDropdown(0);

        }
    });

    function getSalesOrderDetails() {
        $.ajax({
            url: $('#base_url').val() + 'invoice/invoice/getSaleById',
            type: 'POST',
            data: {
                id: document.getElementById("invoice_id").value,
                type2: type2
            },
            success: function(response) {
                clearDetails()
                var sales = JSON.parse(response);
                console.log(sales)
                document.getElementById('sale_date').value = sales[0].date;
                document.getElementById('details').value = sales[0].details;

                // getBranchDropdown(sales[0].branch);


                var $customerDropdown = $('#customer_id');
                $customerDropdown.empty();
                $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                $.each(customers, function(index, customer) {
                    $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
                $customerDropdown.val(sales[0].customer_id)

                var $employeeDropdown = $('#employee_id');
                $employeeDropdown.empty();
                $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                $.each(employees, function(index, employee) {
                    $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                });
                $employeeDropdown.val(sales[0].employee_id)



                var $incidenttypeDropdown = $('#incidenttype');
                $incidenttypeDropdown.empty();
                $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
                $incidenttypeDropdown.append('<option value="1">Retail</option>');
                $incidenttypeDropdown.append('<option value="2">Whole Sale</option>');
                $incidenttypeDropdown.val(sales[0].incidenttype)

                var $invoiceTypeDropdown = $('#invoicetype');
                $invoiceTypeDropdown.empty(); // Clear existing options
                $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                $invoiceTypeDropdown.val(sales[0].invoicetype);


                var $paymentDropdown = $('#your_dropdown_id');
                $paymentDropdown.empty();
                $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                $.each(pmethods, function(index, supplier) {
                    $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                });
                $paymentDropdown.val(sales[0].payment_type)

                document.getElementById('total_discount_ammount').value = sales[0].total_discount_ammount;
                document.getElementById('total_vat_amnt').value = sales[0].total_vat_amnt;
                // document.getElementById('grandTotal').value = sales[0].grandTotal;
                // document.getElementById('Total').value = sales[0].total;
                document.getElementById('discount').value = sales[0].discount;

                // count = 1;
                for (let i = 0; i < sales.length; i++) {
                    let a = i + 1;
                    document.getElementById('myRow' + a).style.display = 'table-row';
                    getActiveProduct(sales[i].product, a);
                    getActiveStore(sales[i].store, sales[i].store, a);

                    document.getElementById('qty' + a).value = sales[i].quantity;
                    document.getElementById('unit' + a).value = sales[i].unit;
                    document.getElementById('code' + a).value = sales[i].avstock;
                    document.getElementById('product_rate' + a).value = sales[i].product_rate;
                    document.getElementById('discount' + a).value = sales[i].discount2;
                    document.getElementById('discount_value' + a).value = sales[i].discount_value;
                    document.getElementById('mastercost_price' + a).value = sales[i].cost_price;

                    // if (vtinfo.ischecked == 1) {
                    //     document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                    // }
                    if (sales[0].invoicetype == 'cash_vat' ||
                        sales[0].invoicetype == 'credit_vat' ||
                        sales[0].invoicetype == 'svat') {
                        document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                        document.querySelectorAll('.vathidden').forEach(el => {
                            el.style.display = 'table-cell';
                        });
                        document.querySelectorAll('.vatshow').forEach(el => {
                            el.style.display = 'none';
                        });
                    } else {
                        document.getElementById('vat_percent' + a).value = 0;
                        document.querySelectorAll('.vathidden').forEach(el => {
                            el.style.display = 'none';
                        });
                        document.querySelectorAll('.vatshow').forEach(el => {
                            el.style.display = 'table-cell';
                        });

                    }
                    document.getElementById('vat_value' + a).value = sales[i].vat_value;
                    document.getElementById('total_price' + a).value = sales[i].total_price;
                    document.getElementById('total_discount' + a).value = sales[i].total_discount;
                    document.getElementById('all_discount' + a).value = sales[i].all_discount;
                    document.getElementById('rdeduction' + a).value = 100;

                    getActiveSubUnitEdit(sales[i].product, a, sales[i].unit, sales[i].conversion_id,
                        sales[i].conversion_ratio, sales[i].convertiontype,
                        sales[i].avstock)

                    // getBatchDropdown(batches, a, sales[i].batch)
                    getBatchDropdown(batches, a, sales[i].batch, sales[i].product, sales[i].batchtype)



                    count = count + 1;
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, 0, count);
        getActiveProduct(0, count)
        count = count + 1;

    }

    function incidetTypechange() {

        if (document.getElementById('invoicetype').value == 'cash_vat' ||
            document.getElementById('invoicetype').value == 'credit_vat' ||
            document.getElementById('invoicetype').value == 'svat') {
            document.querySelectorAll('.vathidden').forEach(el => {
                el.style.display = 'table-cell';
            });
            document.querySelectorAll('.vatshow').forEach(el => {
                el.style.display = 'none';
            });


        } else {

            document.querySelectorAll('.vathidden').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('.vatshow').forEach(el => {
                el.style.display = 'table-cell';
            });
        }

    }

    function product_search(item, name) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('code' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            // var $storeDropdown = $('#store' + item);
            // $storeDropdown.empty();
            // document.getElementById('code' + item).value = "";
            // document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: document.getElementById('product' + item).value.toString(),
                        },
                        success: function(response2) {
                            if (response2 != "null") {
                                let product2 = JSON.parse(response2);
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product[0].unit_name
                                document.getElementById('ad' + item).value = product2[0].unit_name
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = ""
                                document.getElementById('bd' + item).value = ""
                                document.getElementById('ad' + item).value = ""
                            }

                            avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")

                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                    getActiveStore(product[0].store, 0, item);
                    getBatchDropdown(batches, item, 0, document.getElementById('product' + item).value.toString(), product[0].batchtype);
                    // avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")
                    getActiveSubUnit(document.getElementById('product' + item).value, item)

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('product_rate' + item).value = product[0].cost_price;
                    document.getElementById('mastercost_price' + item).value = product[0].cost_price;
                    // if (vtinfo.ischecked == 1) {
                    //     document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    // }
                    if (document.getElementById('invoicetype').value == 'cash_vat' ||
                        document.getElementById('invoicetype').value == 'credit_vat' ||
                        document.getElementById('invoicetype').value == 'svat') {
                        document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    } else {
                        document.getElementById('vat_percent' + item).value = 0;

                    }
                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


        if (name === "batch") {
            avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
            getActiveSubUnit(document.getElementById('product' + item).value, item)

        }


        if (name === "store") {
            avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
            getActiveSubUnit(document.getElementById('product' + item).value, item)

        }

        if (name === "unit") {

            let select = document.getElementById('unit' + item);
            let selectedText = select.options[select.selectedIndex].text;
            convertion(item, document.getElementById('product' + item).value, document.getElementById('unit' + item).value, selectedText)
            // avStock(item,document.getElementById('product' + item).value,document.getElementById('store' + item).value,0)
            // getActiveSubUnit(document.getElementById('product' + item).value,item)

        }
    }

    function avStock(item, product, store, batch, convertiontype, conversion_ratio) {
        document.getElementById('code' + item).value = "";
        document.getElementById('qty' + item).value = "";
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/avg_avstock',
            type: 'POST',
            data: {
                prodid: product,
                storeid: store,
                batch: batch
            },
            success: function(response) {
                let stock = JSON.parse(response);
                let el = document.getElementById('codetype' + item);
                el.style.color = '#8b5cf6';
                el.style.fontWeight = 'bold';
                el.innerHTML = ""
                let select = document.getElementById('unit' + item);
                let selectedText = select.options[select.selectedIndex].text;


                if (convertiontype == "*") {
                    document.getElementById('code' + item).value = (stock[0].avgqty * conversion_ratio).toFixed(2)

                    let sub = (stock[0].avgqty * conversion_ratio);
                    let sub2 = Math.floor((sub).toLocaleString());
                    if (isNaN(sub2)) {
                        sub = Number(sub).toFixed(6);
                        el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText
                    } else {
                        el.innerHTML = sub2 + " " + selectedText

                    }

                } else if (convertiontype == "/") {
                    document.getElementById('code' + item).value = (stock[0].avgqty / conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty / conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "+") {
                    document.getElementById('code' + item).value = (stock[0].avgqty + conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty + conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "-") {
                    document.getElementById('code' + item).value = (stock[0].avgqty - conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty - conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

                } else {

                    if (document.getElementById('mconversion_ratio' + item).value != "") {

                        let totalcount = 0;
                        let mas = document.getElementById('mconversion_ratio' + item).value * stock[0].avgqty / document.getElementById('mconversion_ratio' + item).value;
                        let subcount = 0;
                        let sub = document.getElementById('mconversion_ratio' + item).value * stock[0].avgqty % document.getElementById('mconversion_ratio' + item).value;


                        let mas2 = Math.floor((mas).toLocaleString());
                        if (isNaN(mas2)) {
                            mas = Number(mas).toFixed(6);
                            totalcount = (Math.floor(mas)).toLocaleString()
                        } else {
                            totalcount = mas2

                        }

                        let sub2 = Math.floor((sub).toLocaleString());
                        if (isNaN(sub2)) {
                            sub = Number(sub).toFixed(6);
                            subcount = (Math.floor(sub)).toLocaleString()
                        } else {
                            subcount = sub2

                        }

                        document.getElementById('code' + item).value = stock[0].avgqty == null ? 0 : totalcount;


                        el.innerHTML = totalcount + document.getElementById('bd' + item).value + " " + subcount + document.getElementById('ad' + item).value;
                    } else {
                        document.getElementById('code' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;
                        el.innerHTML = (Math.floor(stock[0].avgqty)).toLocaleString() + " " + selectedText

                    }
                }
            },
            error: function(error) {
                console.log(error)
            }
        });
    }
    deletedsaleDetails = []

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';

        document.getElementById('qty' + num).value = 0;
        document.getElementById('product_rate' + num).value = 0;
        document.getElementById('discount' + num).value = 0;
        document.getElementById('discount_value' + num).value = 0;
        document.getElementById('vat_percent' + num).value = 0;
        document.getElementById('vat_value' + num).value = 0;
        document.getElementById('total_price' + num).value = 0;
        document.getElementById('total_discount' + num).value = 0;
        document.getElementById('all_discount' + num).value = 0;
        calculate_sum(num)

        $.ajax({
            type: "post",
            url: $('#base_url').val() + "/stock/stock/is_grnorgdnthere",
            data: {
                id: document.getElementById('invoicedetail' + num).value,
                invoicetype: 2
            },
            success: function(data) {
                var grnlength = JSON.parse(data);

                if (grnlength.length == 0) {

                    document.getElementById('myRow' + num).style.display = 'none';

                    document.getElementById('qty' + num).value = 0;
                    document.getElementById('product_rate' + num).value = 0;
                    document.getElementById('discount' + num).value = 0;
                    document.getElementById('discount_value' + num).value = 0;
                    document.getElementById('vat_percent' + num).value = 0;
                    document.getElementById('vat_value' + num).value = 0;
                    document.getElementById('total_price' + num).value = 0;
                    document.getElementById('total_discount' + num).value = 0;
                    document.getElementById('all_discount' + num).value = 0;
                    if (document.getElementById('invoicedetail' + num).value != 0) {
                        deletedsaleDetails.push(document.getElementById('invoicedetail' + num).value)
                    }
                    calculate_sum(num)

                } else {
                    alert("grn already added to this invoice item")
                }
            }
        });
    }



    function calculate_sum(sl) {
        var p = 0;
        var v = 0;
        var gr_tot = 0;
        var dis = 0;
        var item_ctn_qty = $("#qty" + sl).val();
        var vendor_rate = $("#product_rate" + sl).val();

        var total_price = item_ctn_qty * vendor_rate;
        $("#total_price" + sl).val(total_price.toFixed(2));

        var quantity = $("#rqty" + sl).val();
        var discount = $("#discount" + sl).val();
        if (!$("#rdeduction" + sl).val()) {
            $("#rdeduction" + sl).val(100)
        }
        if ($("#rdeduction" + sl).val() > 100) {
            $("#rdeduction" + sl).val(100)
        }
        var rdeduction = $("#rdeduction" + sl).val();

        var dis_type = $("#discount_type").val();
        var price_item = $("#product_rate" + sl).val();
        var vat_percent = 0;
        if (document.getElementById('invoicetype').value == 'cash_vat' ||
            document.getElementById('invoicetype').value == 'credit_vat' ||
            document.getElementById('invoicetype').value == 'svat') {
            vat_percent = $("#vat_percent" + sl).val();



        }
        let qty = $("#qty" + sl).val();
        // let number = parseInt(value.replace(/,/g, ''), 10);


        // if (parseInt(quantity) > parseInt(qty)) {
        //     $("#rqty" + sl).val("");
        //     alert("Quantity shouldn't be greater than available quantity");
        //     return;
        // }

        if (quantity > 0 || discount > 0 || vat_percent > 0) {
            if (dis_type == 1) {
                var price = quantity * price_item;
                var disc = +(price * discount / 100);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var temp = price - disc;
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                if (rdeduction && quantity > 0) {
                    let vat1 = $("#vat_value" + sl).val();
                    var vat1rdeductionval = +(vat1 * rdeduction / 100);
                    $("#vat_value" + sl).val(vat1rdeductionval);


                }
                // product wise vat end
                var ttletax = 0;
                $("#total_price" + sl).val(temp);



            } else if (dis_type == 2) {
                var price = quantity * price_item;

                // Discount cal per product
                var disc = (discount * quantity);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);

                //Total price calculate per product
                var temp = price - disc;
                $("#total_price" + sl).val(temp);
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                var ttletax = 0;

            } else if (dis_type == 3) {
                var total_price = quantity * price_item;
                var disc = discount;
                // Discount cal per product
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var price = total_price - disc;
                $("#total_price" + sl).val(price);
                // product wise vat start
                var vat = +(price * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                $("#total_price" + sl).val(price);


                var ttletax = 0;

            }


        }


        if (rdeduction && quantity > 0) {
            let price1 = $("#total_price" + sl).val();
            var rdeductionval = +(price1 * rdeduction / 100);
            var temp = price1 - rdeductionval;
            $("#total_price" + sl).val(rdeductionval);


        }else{
            $("#total_price" + sl).val(0);

        }


        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        // $(".discount").each(function() {
        //     isNaN(this.value) || 0 == this.value.length || (dis += parseFloat(this.value))
        // });
        //Total Discount
        $(".total_discount_val").each(function() {
                isNaN(this.value) || 0 == this.value.length || (p += parseFloat(this.value))
            }),
            $("#total_discount_ammount").val(p.toFixed(2, 2)),

            $(".total_vatamnt").each(function() {
                isNaN(this.value) || 0 == this.value.length || (v += parseFloat(this.value))
            }),
            $("#total_vat_amnt").val(v.toFixed(2, 2)),

            $("#Total").val(gr_tot.toFixed(2, 2));
        var vatamnt = $("#total_vat_amnt").val();

        var gttl = gr_tot - dis;
        var grandtotal = parseFloat(gttl) + parseFloat(vatamnt);
        $("#grandTotal").val(grandtotal.toFixed(2, 2));
        // $("#pamount_by_method").val(grandtotal.toFixed(2, 2));

        // $('#paidAmount').val(grandtotal.toFixed(2, 2));

        var purchase_edit_page = $("#purchase_edit_page").val();
        $("#add_new_payment").empty();

        $("#pay-amount").text('0');
        //   $("#dueAmmount").val(0);

        if (purchase_edit_page == 1) {

            var base_url = $('#base_url').val();
            var is_credit_edit = $('#is_credit_edit').val();

            var csrf_test_name = $('[name="csrf_test_name"]').val();
            var gtotal = $(".grandTotalamnt").val();
            var url = base_url + "purchase/purchase/bdtask_showpaymentmodal";
            $.ajax({
                type: "post",
                url: url,
                data: {
                    is_credit_edit: is_credit_edit,
                    csrf_test_name: csrf_test_name
                },
                success: function(data) {


                    $('#add_new_payment').append(data);

                    //  $("#pamount_by_method").val(gtotal);
                    $("#add_new_payment_type").prop('disabled', false);
                    var card_typesl = $('.card_typesl').val();

                    if (card_typesl == 0) {
                        $("#add_new_payment_type").prop('disabled', true);
                    }

                }
            });

        }

    }

    function getActiveProduct(productId, item) {
        document.getElementById('product' + item).value = productId > 0 ? productId : '';
        var name = '';
        if (productId > 0 && typeof products !== 'undefined') {
            var match = products.find(function(p) { return p.id == productId; });
            name = match ? match.product_name : '';
        }
        document.getElementById('productInput' + item).value = name;
    }




    function getActiveStore(storeId, rstoreId, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option


        var $storeDropdown1 = $('#rstore' + item);
        $storeDropdown1.empty();
        $storeDropdown1.append('<option value="" disabled selected>Select store</option>'); // Add default option

        if (storeId == 1) {
            $storeDropdown.append('<option value="1">N/A</option>');
            $storeDropdown1.append('<option value="1">N/A</option>');

        }

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');

            $storeDropdown1.append('<option value="' + store.id + '">' + store.name + '</option>');
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
            }
        }

        if (rstoreId > 0) {
            {
                $storeDropdown1.val(rstoreId)
            }
        }
    }

    function getStoresDropdown(stores, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });
    }

    function getBranchDropdown(branchId) {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getbranchbyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var branches = JSON.parse(data);
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id)
                        getSalesOrderDropdown()
                    }
                });

                if (branchId > 0) {
                    {
                        $branchDropdown.val(branchId)
                    }
                }



            }
        });
    }


    function getSalesOrderDropdown() {

        var base_url = $('#base_url').val();

        console.log(type2)
        console.log(document.getElementById("branch").value)

        console.log()
        $.ajax({
            type: "post",
            url: base_url + "invoice/invoice/getsalesidbybranch",
            data: {
                type2: type2,
                branch: document.getElementById("branch").value
            },
            success: function(data) {

                var salesorder = JSON.parse(data);
                console.log(salesorder)
                var $branchDropdown = $('#invoice_id');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Sales Order</option>'); // Add default option

                $.each(salesorder, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.sale_id + '</option>');

                });




            }
        });
    }

    function restoreSaveBtn() {
        var $b = $('#save_add');
        $b.prop('disabled', false).removeClass('btn-save-anim-loading').html($b.data('origHtml'));
    }

    function save() {
        arrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('customer_id').value == "" || document.getElementById('customer_id').value === " ") {
                    alert("Customer shouldn't be empty")
                    return
                } else if (document.getElementById('your_dropdown_id').value == "") {
                    alert("Payment Type shouldn't be empty")
                    return
                } else if (document.getElementById('branch').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                } else if (document.getElementById('rdate').value == "") {
                    alert("Return Date shouldn't be empty")
                    return
                } else if (!document.getElementById('product' + i).value) {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('rstore' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('rqty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else
                if (document.getElementById('product_rate' + i).value == "") {
                    alert("Price shouldn't be empty")
                    return
                } else {
                    let qty = 0;
                    let rqty = 0;

                    if (document.getElementById('conversiontype' + i).value == "+") {
                        qty = document.getElementById('qty' + i).value - document.getElementById('conversion_ratio' + i).value
                        rqty = document.getElementById('rqty' + i).value - document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "-") {
                        qty = document.getElementById('qty' + i).value + document.getElementById('conversion_ratio' + i).value
                        rqty = document.getElementById('rqty' + i).value + document.getElementById('conversion_ratio' + i).value

                    } else
                    if (document.getElementById('conversiontype' + i).value == "*") {
                        qty = document.getElementById('qty' + i).value / document.getElementById('conversion_ratio' + i).value
                        rqty = document.getElementById('rqty' + i).value / document.getElementById('conversion_ratio' + i).value

                    } else
                    if (document.getElementById('conversiontype' + i).value == "/") {
                        qty = document.getElementById('qty' + i).value * document.getElementById('conversion_ratio' + i).value
                        rqty = document.getElementById('rqty' + i).value * document.getElementById('conversion_ratio' + i).value

                    } else {
                        qty = document.getElementById('qty' + i).value
                        rqty = document.getElementById('rqty' + i).value

                    }


                    arrItem.push({
                        product: document.getElementById('product' + i).value,
                        product_name: document.getElementById('productInput' + i).value,
                        store: document.getElementById('store' + i).value,
                        quantity: qty,
                        product_rate: document.getElementById('product_rate' + i).value,
                        batch: document.getElementById('batch' + i).value,
                        discount: document.getElementById('discount' + i).value,
                        discount_value: document.getElementById('discount_value' + i).value,
                        vat_percent: document.getElementById('vat_percent' + i).value,
                        vat_value: document.getElementById('vat_value' + i).value,
                        total_price: document.getElementById('total_price' + i).value,
                        total_discount: document.getElementById('total_discount' + i).value,
                        all_discount: document.getElementById('all_discount' + i).value,
                        unit: document.getElementById('unit' + i).value,
                        conversionid: document.getElementById('conversionid' + i).value,
                        rqty: rqty,
                        rstore: document.getElementById('rstore' + i).value,
                        rdeduction: document.getElementById('rdeduction' + i).value,
                        invoicedetail: document.getElementById('invoicedetail' + i).value ? document.getElementById('invoicedetail' + i).value : 0,
                        isstock:  document.getElementById('isstock' + i).value,
                        aqty:  document.getElementById('rqty' + i).value +" "+units.find(unit => unit.unit_id == document.getElementById('unit' + i).value).unit_name,
                    });
                }
            }

        }
        console.log(arrItem)

        var paymentdropdown = document.getElementById('your_dropdown_id');
        (function () {
            var $b = $('#save_add');
            if (typeof $b.data('origHtml') === 'undefined') $b.data('origHtml', $b.html());
            var isEdit = (id > 0);
            $b.prop('disabled', true).addClass('btn-save-anim-loading')
              .html('<i class="fa fa-spinner fa-spin"></i> ' + (isEdit ? 'Updating...' : 'Saving...'));
        })();

        if (id > 0) {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/update_sales_return',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    discount: document.getElementById('discount').value,
                    type2: type2,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    rdate: document.getElementById('rdate').value,
                    date: document.getElementById('sale_date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    employee_id: document.getElementById('employee_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,
                    invoice_id1:document.getElementById('invoice_id1').value,


                },
                success: function(response) {
                    try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Update failed. Please try again."));
                        return;
                    }
                    clearDetails()
                    restoreSaveBtn();
                    alert("Sales return Details updated Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.html || datas.details, datas.invoiceno);
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Update failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/save_sales_return',
                type: 'POST',
                data: {
                    items: arrItem,
                    type2: type2,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    rdate: document.getElementById('rdate').value,
                    date: document.getElementById('sale_date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    employee_id: document.getElementById('employee_id').value,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,
                    invoice_id: document.getElementById('invoice_id').value,
                },
                success: function(response) {
                    try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Save failed. Please try again."));
                        return;
                    }
                    clearDetails()
                    restoreSaveBtn();
                    alert("Sales return Details saved Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.html || datas.details, datas.invoiceno);
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Save failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });

        }







    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
            var pinp = document.getElementById('productInput' + i);
            if (pinp) pinp.value = '';
            var phid = document.getElementById('product' + i);
            if (phid) phid.value = '';

            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

            $.each(stores, function(index, store) {
                $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            });

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
            document.getElementById('rqty' + i).value = "";
            document.getElementById('rstore' + i).value = "";
            document.getElementById('rdeduction' + i).value = "";



            document.getElementById('code' + i).value = "";
            document.getElementById('unit' + i).value = "";

            document.getElementById('product_rate' + i).value = "";
            document.getElementById('discount' + i).value = "";
            document.getElementById('discount_value' + i).value = "";
            document.getElementById('vat_percent' + i).value = "";
            document.getElementById('vat_value' + i).value = "";
            document.getElementById('total_price' + i).value = "";
            document.getElementById('total_discount' + i).value = "";
            document.getElementById('all_discount' + i).value = "";

        }
        document.getElementById('myRow1').style.display = 'table-row';

        document.getElementById('discount').value = ""
        document.getElementById('total_discount_ammount').value = ""
        document.getElementById('total_vat_amnt').value = ""
        document.getElementById('grandTotal').value = ""
        document.getElementById('sale_date').value = ""
        document.getElementById('details').value = ""
        document.getElementById('Total').value = ""
        document.getElementById('customer_id').value = ""
        document.getElementById('your_dropdown_id').value = ""

        var $customerDropdown = $('#customer_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(customers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
        });

        var $paymentDropdown = $('#your_dropdown_id');
        $paymentDropdown.empty();
        $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
        $.each(pmethods, function(index, supplier) {
            $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
        });
    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'salesreturnnote_' + voucherNo + '_' + pdfDateStamp();
        }

        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload();
            })
        });
    }

    function freezeSalesReturnFormForDue(message) {
        var $panel = $('.inv-panel');
        $panel.find('input, select, textarea, button').prop('disabled', true);
        $panel.addClass('invoice-locked');
        var banner = document.getElementById('invoiceLockedBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'invoiceLockedBanner';
            banner.className = 'invoice-locked-banner';
            var header = document.querySelector('.inv-header');
            if (header) header.insertAdjacentElement('afterend', banner);
        }
        banner.innerHTML = '<i class="fa fa-lock"></i> ' + (message || 'Editing is locked for this sales return.');
    }

    function getBatchDropdown(batches, item, value, product, batchtype) {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchbyProductAndBatchtype2',
            type: 'POST',
            data: {
                product: product,
                batchtype: batchtype,
                id:id
            },
            success: function(response2) {
                var $batchDropdown = $('#batch' + item);
                $batchDropdown.empty();
                $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option
                $batchDropdown.append('<option value="0">Default</option>');
                if (response2 != "not") {
                    let batches2 = JSON.parse(response2);
                    $.each(batches2, function(index, batch) {
                        $batchDropdown.append('<option value="' + batch.id + '">' + batch.batchid + '</option>');
                    });
                }
                $batchDropdown.val(value)




            },
            error: function(error) {
                console.log(error)
            }
        });




    }

    function getActiveSubUnit(productId, item) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                // alert("Invoice Details Updated Successfully")
                // window.location.href = $('#base_url').val() + 'invoice_list';
                datas = JSON.parse(response);
                console.log(datas)
                var $subunitDropdown = $('#unit' + item);
                document.getElementById('conversionid' + item).value = "";
                document.getElementById('conversiontype' + item).value = "";
                document.getElementById('conversion_ratio' + item).value = "";


                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>'); // Add default option
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');
                $subunitDropdown.val(datas[0].unit)

                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });



            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function getActiveSubUnitEdit(productId, item, value, conversion_id, conversion_ratio, cconvertiontype, avstock) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                datas = JSON.parse(response);
                var $subunitDropdown = $('#unit' + item);
                if (conversion_id != "0") {
                    document.getElementById('conversionid' + item).value = conversion_id;
                    document.getElementById('conversiontype' + item).value = cconvertiontype;
                    document.getElementById('conversion_ratio' + item).value = conversion_ratio;
                } else {
                    document.getElementById('conversionid' + item).value = "";
                    document.getElementById('conversiontype' + item).value = "";
                    document.getElementById('conversion_ratio' + item).value = "";
                }

                document.getElementById('codetype' + item).innerHTML=""

                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>'); // Add default option
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');

                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });

                $subunitDropdown.val(value)

                document.getElementById('isstock' + item).value = datas[0].stock


                if (datas[0].stock == 0) {
                    return
                }


                let select = document.getElementById('unit' + item);
                let selectedText = select.options[select.selectedIndex].text;
                let el = document.getElementById('codetype' + item);
                el.style.color = '#8b5cf6';
                el.style.fontWeight = 'bold';
                // el.innerHTML = (Math.floor(avstock)).toLocaleString() + " " + selectedText


                let sub2 = Math.floor((parseFloat(avstock)).toLocaleString());
                if (isNaN(sub2)) {
                    avstock = Number(avstock).toFixed(6);
                    el.innerHTML = (Math.floor(avstock)).toLocaleString() + " " + selectedText
                } else {
                    el.innerHTML = sub2 + " " + selectedText

                }



                if (value == datas[0].unit) {

                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: productId,
                        },
                        success: function(response2) {
                            if (response2 != "null") {

                                let product2 = JSON.parse(response2); //console.log(adjStocks[i].actualstock*product2[0].conversion_ratio)
                                console.log(product2)
                                // document.getElementById('code' + item).value = avstock * product2[0].conversion_ratio;
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product2[0].unit2
                                document.getElementById('ad' + item).value = product2[0].unit_name
                                let el = document.getElementById('codetype' + item);
                                el.style.color = '#8b5cf6';
                                el.style.fontWeight = 'bold';
                                el.innerHTML = ""
                                // let totalcount = Math.floor(document.getElementById('mconversion_ratio' + item).value * avstock / document.getElementById('mconversion_ratio' + item).value);
                                // let subcount = (Math.floor(document.getElementById('mconversion_ratio' + item).value * avstock % document.getElementById('mconversion_ratio' + item).value)).toLocaleString();


                                let totalcount = 0;
                                let mas = document.getElementById('mconversion_ratio' + item).value * avstock / document.getElementById('mconversion_ratio' + item).value;
                                let subcount = 0;
                                let sub = document.getElementById('mconversion_ratio' + item).value * avstock % document.getElementById('mconversion_ratio' + item).value;


                                let mas2 = Math.floor((mas).toLocaleString());
                                if (isNaN(mas2)) {
                                    mas = Number(mas).toFixed(6);
                                    totalcount = (Math.floor(mas)).toLocaleString()
                                } else {
                                    totalcount = mas2

                                }

                                let sub2 = Math.floor((sub).toLocaleString());
                                if (isNaN(sub2)) {
                                    sub = Number(sub).toFixed(6);
                                    subcount = (Math.floor(sub)).toLocaleString()
                                } else {
                                    subcount = sub2

                                }
                                // if (stocktype != "both") {
                                document.getElementById('code' + item).value = avstock == null ? 0 : totalcount;
                                el.innerHTML = (totalcount + document.getElementById('bd' + item).value + " " + subcount + document.getElementById('ad' + item).value).toLocaleString();
                                // }
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = ""
                                document.getElementById('bd' + item).value = ""
                                document.getElementById('ad' + item).value = ""
                            }
                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                } else {
                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: productId,
                        },
                        success: function(response2) {

                            if (response2 != "null") {

                                let product2 = JSON.parse(response2); //console.log(adjStocks[i].actualstock*product2[0].conversion_ratio)
                                console.log(product2)
                                // document.getElementById('code' + item).value = avstock * product2[0].conversion_ratio;
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product2[0].unit2
                                document.getElementById('ad' + item).value = product2[0].unit_name
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = ""
                                document.getElementById('bd' + item).value = ""
                                document.getElementById('ad' + item).value = ""
                            }
                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                }






            },
            error: function(error) {
                console.log(error)
            }
        });
    }


    /* ── Override window.alert → toastr ── */

    /* ── Mobile card labels ── */
    (function () {
        var colLabels = ['Product','Store','Batch','Unit','Av.Qty','Qty','Ret.Qty','Ret.Store','Refund%','VAT','Total'];
        document.querySelectorAll('#saleTable tbody tr').forEach(function (row) {
            row.querySelectorAll('td').forEach(function (td, i) {
                if (!colLabels[i]) return;
                var lbl = document.createElement('span');
                lbl.className = 'td-mobile-label';
                lbl.textContent = colLabels[i];
                td.insertBefore(lbl, td.firstChild);
            });
        });
        var vatEl = document.getElementById('total_vat_amnt');
        if (vatEl && vatEl.parentNode) vatEl.parentNode.setAttribute('data-label', 'VAT Total');
        var gtEl = document.getElementById('grandTotal');
        if (gtEl && gtEl.parentNode) gtEl.parentNode.setAttribute('data-label', 'Grand Total');
    })();

    function convertion(item, product, unit, unitname) {

        // if (unitname.split("-")[1] == "S") {
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/conversion',
            type: 'POST',
            data: {
                product_id: product,
                unit: unit
            },
            success: function(response) {
                // alert("Invoice Details Updated Successfully")
                // window.location.href = $('#base_url').val() + 'invoice_list';
                if (response != "not") {
                    datas = JSON.parse(response);
                    document.getElementById('conversiontype' + item).value = datas[0].convertiontype
                    document.getElementById('conversionid' + item).value = datas[0].conversionratio_id
                    document.getElementById('conversion_ratio' + item).value = datas[0].conversion_ratio;
                    document.getElementById('product_rate' + item).value = datas[0].subcost_price;



                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value,
                        datas[0].convertiontype, datas[0].conversion_ratio)
                } else {
                    // alert("Conversion not found")
                    getActiveSubUnit(document.getElementById('product' + item).value, item)
                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
                    document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                }

            },
            error: function(error) {
                console.log(error)
            }
        });
        // } else {
        //     getActiveSubUnit(document.getElementById('product' + item).value, item)
        //     avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")

        // }


    }
</script>