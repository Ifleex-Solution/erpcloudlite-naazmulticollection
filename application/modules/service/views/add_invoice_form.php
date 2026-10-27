<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>


<style>
    /* ── Desktop column widths (1025px+) ── */
    @media (min-width: 1025px) {
        .col-big     { width: 20% !important; }
        .col-total   { width: 20% !important; }
        .col-medium  { width: 11% !important; }
        .col-medium2 { width: 15% !important; }
        .vathidden   { width: 11% !important; }
        .vatshow     { width: 15% !important; }
        .col-small   { width:  7% !important; }
    }

    /* Panel */
    .inv-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }

    /* ── Locked state: cancelled invoice ── */
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
    .invoice-locked-banner i { margin-right: 6px; }

    /* Header flex layout */
    .inv-header { padding: 12px 18px !important; }
    .inv-header .panel-title {
        display: flex; align-items: center;
        justify-content: space-between; flex-wrap: wrap; gap: 8px;
    }
    .inv-header .padding-lefttitle { display: flex; gap: 6px; flex-wrap: wrap; }
    .inv-header .padding-lefttitle table { margin: 0; }
    .inv-header .padding-lefttitle td { padding-left: 0 !important; }

    /* Mobile labels hidden on desktop */
    .td-mobile-label { display: none; }

    /* ── Select2 dropdown redesign — matches Sale screen ── */
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background: #F8FAFC !important;
        height: 34px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        font-size: 13px !important;
        line-height: 32px !important;
        padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow b {
        border-color: #64748B transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
        margin-top: -1px !important;
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
    .select2-results__option {
        font-size: 13px !important;
        padding: 7px 12px !important;
        color: #374151 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #16A34A !important;
        color: #fff !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #F0FDF4 !important;
        color: #16A34A !important;
    }

    /* ── Tablet 768–1024px ── */
    @media (min-width: 768px) and (max-width: 1024px) {
        .inv-form-section > .row {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 12px 20px; margin: 0;
        }
        .inv-form-section > .row > [class*="col-sm"] {
            width: 100% !important; float: none !important; padding: 0;
        }
        .table-responsive { overflow: visible !important; }
        #saleTable { display: block; width: 100%; }
        #saleTable thead { display: none; }
        #saleTable tbody {
            display: block; width: 100%; padding: 4px;
            background: #f4f6f8; border-radius: 8px;
        }
        #saleTable tfoot { display: block; }
        .td-mobile-label {
            display: block; font-size: 10px; font-weight: 700;
            color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px;
        }
        #saleTable tbody tr {
            display: grid; grid-template-columns: 1fr 1fr;
            width: 100%; box-sizing: border-box; margin-bottom: 20px;
            border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] {
            display: grid !important; grid-template-columns: 1fr 1fr;
        }
        #saleTable tbody td {
            display: block; width: 100%; box-sizing: border-box;
            padding: 8px 12px; border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important; white-space: normal;
        }
        #saleTable tbody td.vathidden[style*="table-cell"] { display: block !important; width: 100% !important; }
        #saleTable tbody td:nth-child(even) { border-right: none !important; }
        #saleTable tbody td:last-child {
            grid-column: 1 / -1;
            border-bottom: none !important; border-right: none !important; padding: 0;
        }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button,
        #saleTable tbody td:last-child .btn { width: 100%; border-radius: 0; margin: 0; display: block; }
        #saleTable tbody td .form-control,
        #saleTable tbody td select { width: 100% !important; box-sizing: border-box; }
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
            font-size: 13px; font-weight: 600; height: 34px; text-align: right;
        }
        #saleTable tfoot td[data-label].vathidden { display: none !important; }
        #saleTable tfoot td[data-label].vathidden[style*="table-cell"] { display: block !important; }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px; font-weight: 700; }
        #saleTable tfoot td.tfoot-btn-cell {
            display: block !important; padding: 0; border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td.tfoot-btn-cell .btn {
            display: block; width: 100%; border-radius: 0; margin: 0; padding: 11px; font-size: 16px;
        }
    }

    /* ── Mobile ≤767px ── */
    @media (max-width: 767px) {
        .inv-form-section > .row > [class*="col-sm"] {
            width: 100% !important; float: none; margin-bottom: 8px;
        }
        .col-sm-6.table-bordered,
        .col-sm-3.table-bordered { width: 100% !important; float: none; box-sizing: border-box; }
        .table-responsive { overflow: visible !important; }
        #saleTable { display: block; width: 100%; }
        #saleTable thead { display: none; }
        #saleTable tbody {
            display: block; width: 100%; padding: 4px;
            background: #f4f6f8; border-radius: 8px;
        }
        #saleTable tfoot { display: block; }
        #saleTable tbody tr {
            display: block; width: 100%; box-sizing: border-box; margin-bottom: 16px;
            border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] { display: block !important; width: 100% !important; }
        #saleTable tbody td {
            display: block; width: 100%; box-sizing: border-box;
            padding: 6px 10px; border: none !important;
            border-bottom: 1px solid #f0f0f0 !important; white-space: normal;
        }
        #saleTable tbody td.vathidden[style*="table-cell"] { display: block !important; width: 100% !important; }
        #saleTable tbody td:last-child { border-bottom: none !important; padding: 0; }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button,
        #saleTable tbody td:last-child .btn { width: 100%; border-radius: 0; margin: 0; display: block; }
        .td-mobile-label {
            display: block; font-size: 10px; font-weight: 700;
            color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px;
        }
        #saleTable tbody td .form-control,
        #saleTable tbody td select { width: 100% !important; box-sizing: border-box; }
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
            font-size: 13px; font-weight: 600; height: 34px; text-align: right;
        }
        #saleTable tfoot td[data-label].vathidden { display: none !important; }
        #saleTable tfoot td[data-label].vathidden[style*="table-cell"] { display: block !important; }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px; font-weight: 700; }
        #saleTable tfoot td.tfoot-btn-cell {
            display: block !important; padding: 0; border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td.tfoot-btn-cell .btn {
            display: block; width: 100%; border-radius: 0; margin: 0; padding: 11px; font-size: 16px;
        }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header" id="style12">
                <div class="panel-title has-goback">
                    <span id="title"><?php echo $title; ?></span>
                    <span class="padding-lefttitle">
                        <table>
                            <tr>
                                <td style="padding-left: 20px;">
                                    <button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#customerModel">
                                        <i class="fa fa-user-plus"></i> Add Customer
                                    </button>
                                    <button type="button" class="btn btn-info m-b-5 m-r-2" onclick="openAddServiceModal()">
                                        <i class="fa fa-plus"></i> Add Service
                                    </button>
                                </td>
                            </tr>
                        </table>



                    </span>
                
<a href="<?php echo base_url('manage_service_invoice') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>

            </div>

            <div class="panel-body inv-form-section">


                <div class="row">



                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Service Date
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="sale_date" value="<?php echo $date; ?>" id="date" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="eod_date" class="col-sm-4 col-form-label">EOD Date
                            </label>
                            <div class="col-sm-6">
                                <input type="text" tabindex="2" class="form-control datepicker" name="eod_date" value="<?php echo $date; ?>" id="eod_date" />
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="branch" required name="branch" tabindex="3" onchange="getSalesOrderDropdown()">


                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6" id="showorderno">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Service Order No
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="sales_order_no" required name="sales_order_no" tabindex="3" onchange="getSalesOrderDetails()">


                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6" id="showorderno2">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Service Order No
                            </label>
                            <div class="col-sm-6">
                                <input type="hidden" tabindex="2" class="form-control" value="" id="sales_order_no_convert" />
                                <input type="text" tabindex="2" class="form-control" value="" id="sales_order_no1" readonly />

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="invoicetype" class="col-sm-4 col-form-label">Invoice Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="invoicetype" required name="invoicetype" tabindex="3" onchange="incidetTypechange()">
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
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Customer
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select name="customer_id" id="customer_id" class="form-control " required="" tabindex="1">
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
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Employee
                            </label>
                            <div class="col-sm-6">
                                <select name="employee_id" id="employee_id" class="form-control " tabindex="1">
                                    <option value="">Select an option</option>
                                    <option value="1">N/A</option>
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
                                <th class="text-center  col-big">Service<i
                                        class="text-danger">*</i></th>

                                <th class="text-center col-medium vathidden">Qty</th>
                                <th class="text-center col-medium2 vatshow">Qty</th>

                                <th class="text-center col-medium vathidden">Price val</th>
                                <th class="text-center col-medium2 vatshow">Price val</th>



                                <th class="text-center col-medium vathidden">Discount</th>
                                <th class="text-center col-medium vathidden">Dis.val</th>

                                <th class="text-center col-medium2 vatshow">Discount</th>
                                <th class="text-center col-medium2 vatshow">Dis.val</th>

                                <th class="text-center col-medium vathidden">VAT </th>
                                <th class="text-center col-medium vathidden">VAT.val</th>

                                <th class="text-center col-medium vathidden">Total</th>
                                <th class="text-center col-medium2 vatshow">Total</th>

                                <th class="text-center col-medium"><?php echo display('action') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">
                                    <span class="td-mobile-label">Service</span>
                                    <select name="product[]" class="form-control" id="product1" tabindex="1" onchange="product_search(1, 'product')">
                                        <option value="">Select Product</option>
                                        <?php foreach ($services as $service) { ?>
                                            <option value="<?php echo $service['service_id']; ?>"><?php echo $service['service_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </td>

                                <td class="qty">
                                    <span class="td-mobile-label">Qty</span>
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />
                                </td>
                                <td class="rate">
                                    <span class="td-mobile-label">Price</span>
                                    <input type="text" name="product_rate[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="product_rate1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                </td>

                                <td class="qty">
                                    <span class="td-mobile-label">Discount %</span>
                                    <input type="text" name="discount_per[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="discount1" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                </td>
                                <td class="rate">
                                    <span class="td-mobile-label">Dis. Value</span>
                                    <input type="text" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->
                                <td class="qty vathidden">
                                    <span class="td-mobile-label">VAT %</span>
                                    <input type="text" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                </td>
                                <td class="rate vathidden">
                                    <span class="td-mobile-label">VAT Value</span>
                                    <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>
                                <!-- VAT  end-->
                                <td class="product_field">
                                    <span class="td-mobile-label">Total</span>
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price1" value="0.00" readonly="readonly" />
                                    <input type="hidden" id="total_discount1" class="" />
                                    <input type="hidden" id="all_discount1" class="total_discount dppr" name="discount_amount[]" />
                                </td>

                                <td>
                                </td>

                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>">
                                    <td class="product_field">
                                        <span class="td-mobile-label">Service</span>
                                        <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="product_search(<?php echo $i; ?>, 'product')">
                                            <option value="">Select Product</option>
                                            <?php foreach ($services as $service) { ?>
                                                <option value="<?php echo $service['service_id']; ?>"><?php echo $service['service_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>





                                    <td class="qty">
                                        <span class="td-mobile-label">Qty</span>
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />
                                    </td>

                                    <td class="rate">
                                        <span class="td-mobile-label">Price</span>
                                        <input type="text" name="product_rate[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="product_rate<?php echo $i; ?>" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                    </td>

                                    <td class="qty">
                                        <span class="td-mobile-label">Discount %</span>
                                        <input type="text" name="discount_per[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="discount<?php echo $i; ?>" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                        <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    </td>

                                    <td class="rate">
                                        <span class="td-mobile-label">Dis. Value</span>
                                        <input type="text" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT start -->
                                    <td class="qty vathidden">
                                        <span class="td-mobile-label">VAT %</span>
                                        <input type="text" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                    </td>
                                    <td class="rate vathidden">
                                        <span class="td-mobile-label">VAT Value</span>
                                        <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>
                                    <!-- VAT end -->

                                    <td class="product_field">
                                        <span class="td-mobile-label">Total</span>
                                        <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price<?php echo $i; ?>" value="0.00" readonly="readonly" />
                                        <input type="hidden" id="total_discount<?php echo $i; ?>" class="" />
                                        <input type="hidden" id="all_discount<?php echo $i; ?>" class="total_discount dppr" name="discount_amount[]" />
                                    </td>

                                    <td>
                                        <button class='btn btn-danger' type='button' value='Delete' onclick='deleteRow(<?php echo $i; ?>)'>
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right vathidden" colspan="7"><b><?php echo display('total') ?>:</b></td>
                                <td class="text-right vatshow" colspan="5"><b><?php echo display('total') ?>:</b></td>
                                <td class="text-right" data-label="Total">
                                    <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />
                                </td>
                                <td class="tfoot-btn-cell">
                                    <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item"
                                        onClick="addInputField('addinvoiceItem');" tabindex="9"><i class="fa fa-plus"></i></button>
                                    <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td class="text-right vathidden" colspan="7"><b>Service Discount:</b></td>
                                <td class="text-right vatshow" colspan="5"><b>Service Discount:</b></td>
                                <td class="text-right" data-label="Service Discount">
                                    <input type="text" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(1)" name="discount" placeholder="0.00" value="" />
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-right vathidden" colspan="7"><b><?php echo display('total_discount') ?>:</b></td>
                                <td class="text-right vatshow" colspan="5"><b><?php echo display('total_discount') ?>:</b></td>
                                <td class="text-right" data-label="Total Discount">
                                    <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                                </td>
                                <td> </td>
                            </tr>
                            <tr>
                                <td class="text-right vathidden" colspan="7"><b><?php echo display('ttl_val') ?>:</b></td>
                                <td class="text-right vathidden" data-label="Total VAT">
                                    <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />
                                </td>
                                <td class="vathidden"> </td>
                            </tr>
                            <tr>
                                <td class="text-right vathidden" colspan="7"><b><?php echo display('grand_total') ?>:</b></td>
                                <td class="text-right vatshow" colspan="5"><b><?php echo display('grand_total') ?>:</b></td>
                                <td class="text-right" data-label="Grand Total">
                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                                </td>
                                <td> </td>
                            </tr>
                        </tfoot>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-6 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="payments" class="col-form-label"><?php echo display('payment_type'); ?> <i class="text-danger">*</i></label>
                                        <select name="multipaytype[]" class="form-control" id="your_dropdown_id" tabindex="1">
                                            <option value="">Select an option</option>
                                            <?php foreach ($all_pmethod as $services) { ?>
                                                <option value="<?php echo $services['id']; ?>"><?php echo $services['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="details" class="col-form-label"><?php echo display('details'); ?></label>
                                        <textarea class="form-control" tabindex="4" id="details" name="sale_details" placeholder="<?php echo display('details'); ?>" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <button id="save_add" class="btn btn-success" name="add-invoice" onclick="save()">
                            <?php
                            echo empty($id)
                                ? display('save')
                                : (empty($pagetype) ? display('update') : display('save'));
                            ?></button>
                        <?php if (!empty($id) && empty($pagetype)): ?>
                        <button type="button" id="cancel_invoice_btn" class="btn btn-danger" onclick="cancelInvoice()">
                            <i class="fa fa-ban"></i> Cancel Invoice
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="customerModel" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add New Customer</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input type="text" class="form-control" id="customer_name" name="customer_name" />
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" id="customer_phone" name="customer_phone" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="quick_customer_save_btn" class="btn btn-primary" onclick="save_customer()">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    wireDuplicateCheck({
        input: '#customer_name',
        url: $('#base_url').val() + 'customer/customer/check_duplicate_customer',
        saveButtons: '#quick_customer_save_btn'
    });
    wireDuplicateCheck({
        input: '#as_service_name',
        url: $('#base_url').val() + 'service/service/check_duplicate_service',
        saveButtons: '#as_save_btn'
    });
});
</script>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-weight:600;">Add New Service</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label style="font-weight:600;">Service Name <span class="text-danger">*</span></label>
                    <input type="text" id="as_service_name" class="form-control" placeholder="Enter service name">
                </div>
                <div class="form-group">
                    <label style="font-weight:600;">Charge <span class="text-danger">*</span></label>
                    <input type="number" id="as_charge" class="form-control" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label style="font-weight:600;">VAT %</label>
                    <input type="number" id="as_service_vat" class="form-control" placeholder="0.00" min="0" step="0.01">
                </div>
                <div class="form-group">
                    <label style="font-weight:600;">Description</label>
                    <textarea id="as_description" class="form-control" placeholder="Description" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="as_save_btn" onclick="saveNewService()">Save Service</button>
            </div>
        </div>
    </div>
</div>
<script>
function openAddServiceModal() {
    $('#as_service_name').val('');
    $('#as_charge').val('');
    $('#as_service_vat').val('');
    $('#as_description').val('');
    $('#as_save_btn').prop('disabled', false).text('Save Service');
    $('#addServiceModal').modal('show');
}
function saveNewService() {
    var name   = $('#as_service_name').val().trim();
    var charge = $('#as_charge').val().trim();
    if (!name)   { alert('Service name is required.'); return; }
    if (charge === '') { alert('Charge is required.'); return; }
    $('#as_save_btn').prop('disabled', true).text('Saving...');
    $.ajax({
        url: $('#base_url').val() + 'service/service/insert_service_quick',
        type: 'POST',
        data: {
            service_name: name,
            charge:       charge,
            service_vat:  $('#as_service_vat').val().trim(),
            description:  $('#as_description').val().trim()
        },
        dataType: 'json',
        success: function(r) {
            $('#as_save_btn').prop('disabled', false).text('Save Service');
            if (r.status === 'Success') {
                // Add the new service to all product dropdowns
                var newOption = $('<option>').val(r.id).text(r.service_name);
                $('select[name="product[]"]').append(newOption.clone());
                // Select it in the first visible empty row
                for (var i = 1; i <= 20; i++) {
                    var row = document.getElementById('myRow' + i);
                    var sel = document.getElementById('product' + i);
                    if (sel && row && row.style.display !== 'none' && sel.value === '') {
                        sel.value = r.id;
                        product_search(i, 'product');
                        break;
                    }
                }
                $('#addServiceModal').modal('hide');
            } else {
                alert(r.message || 'Failed to save service.');
            }
        },
        error: function() {
            $('#as_save_btn').prop('disabled', false).text('Save Service');
            alert('Failed to save service. Please try again.');
        }
    });
}
</script>

<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let services=" . json_encode($service_list) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let pagetype=" . json_encode($pagetype) . ";";

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
    var $customerDropdown = $('#customer_id');
    $customerDropdown.empty();
    $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
    $.each(customers, function(index, customer) {
        $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
    });
    $customerDropdown.val(1)

    var $paymentDropdown = $('#your_dropdown_id');
    $paymentDropdown.empty();
    $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
    $.each(pmethods, function(index, supplier) {
        $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
    });
    $paymentDropdown.val(1)
    let count = 2
    getActiveProduct(0, 1)


    document.getElementById("showorderno2").style.display = "none";


    function clearDetails2() {
        for (let i = 1; i < 20; i++) {
            var $productDropdown = $('#product' + i);
            $productDropdown.empty();
            $productDropdown.append('<option value="" disabled selected>Select Product</option>');
            $.each(services, function(index, s) {
                $productDropdown.append('<option value="' + s.service_id + '">' + s.service_name + '</option>');
            });

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
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
        document.getElementById('discount').value = "";
        document.getElementById('total_discount_ammount').value = "";
        document.getElementById('total_vat_amnt').value = "";
        document.getElementById('grandTotal').value = "";
        document.getElementById('Total').value = "";
    }

    function incidetTypechange() {
        clearDetails2();

        var invtype = document.getElementById('invoicetype').value;
        if (invtype === 'cash_vat' || invtype === 'credit_vat') {
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

        applyPaymentTypeFreeze(invtype);
    }

    // Credit / Credit VAT service sales aren't paid at the point of sale, so
    // the Payment Type must be the dedicated "Credit" payment method, not
    // whatever cash/bank method was last selected — same logic as the Sale
    // and Purchase screens.
    function applyPaymentTypeFreeze(invtype) {
        var $dd = $('#your_dropdown_id');
        if (invtype === 'credit' || invtype === 'credit_vat') {
            $dd.empty();
            $dd.append('<option value="" disabled>Select an option</option>');
            var creditId = null;
            $.each((typeof pmethods !== 'undefined' ? pmethods : []), function(index, method) {
                $dd.append('<option value="' + method.id + '">' + method.name + '</option>');
                if (method.name === 'Credit') {
                    creditId = method.id;
                }
            });
            if (creditId !== null) {
                $dd.val(creditId);
            } else {
                console.warn('Payment Type: no "Credit" entry found in pmethods to freeze to.');
            }
            $dd.prop('disabled', true);
        } else {
            $dd.prop('disabled', false);
        }
    }

    $(document).ready(function() {
        $('select.form-control').select2({ placeholder: 'Select option', allowClear: true });

        document.querySelectorAll('.vathidden').forEach(el => {
            el.style.display = 'none';
        });

        if (id == null) {
            document.getElementById('invoicetype').value = 'cash';
        }

        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }

        var $employeeDropdown = $('#employee_id');
        $employeeDropdown.empty();
        $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
        $employeeDropdown.append('<option value="1">N/A</option>');
        $.each(employees, function(index, employee) {
            $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
        });
        $employeeDropdown.val(1).trigger('change')



        if (id != null) {

            if (pagetype == "") {

                $.ajax({
                    url: $('#base_url').val() + 'service/service/getServiceById',
                    type: 'POST',
                    data: {
                        id: id,
                        type2: type2
                    },
                    success: function(response) {

                        var sales = JSON.parse(response);
                        document.getElementById('date').value = sales[0].date;
                        document.getElementById('eod_date').value = sales[0].eod_date;
                        document.getElementById('details').value = sales[0].details;

                        getBranchDropdown(sales[0].branch);

                        document.getElementById("showorderno").style.display = "none";
                        document.getElementById("showorderno2").style.display = "block";
                        document.getElementById('sales_order_no1').value = sales[0].service_order_id;




                        var $customerDropdown = $('#customer_id');
                        $customerDropdown.empty();
                        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                        $.each(customers, function(index, customer) {
                            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                        });
                        $customerDropdown.val(sales[0].customer_id).trigger('change')

                        var $employeeDropdown = $('#employee_id');
                        $employeeDropdown.empty();
                        $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                        $employeeDropdown.append('<option value="1">N/A</option>');
                        $.each(employees, function(index, employee) {
                            $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                        });
                        $employeeDropdown.val(sales[0].employee_id).trigger('change')

                        var $paymentDropdown = $('#your_dropdown_id');
                        $paymentDropdown.empty();
                        $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>');
                        $.each(pmethods, function(index, supplier) {
                            $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                        });
                        $paymentDropdown.val(sales[0].payment_type).trigger('change');

                        var $invoiceTypeDropdown = $('#invoicetype');
                        $invoiceTypeDropdown.empty();
                        $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                        $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                        $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                        $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                        $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                        $invoiceTypeDropdown.val(sales[0].invoicetype).trigger('change');
                        $invoiceTypeDropdown.prop('disabled', true);
                        incidetTypechange();

                        if (parseInt(sales[0].status) === 2) {
                            $('#cancel_invoice_btn').hide();
                            freezeInvoiceFormForCancel('This invoice has been cancelled. Editing is locked.');
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
                            getActiveProduct(sales[i].service, a);

                            document.getElementById('qty' + a).value = sales[i].quantity;
                            document.getElementById('product_rate' + a).value = sales[i].product_rate;
                            document.getElementById('discount' + a).value = sales[i].discount2;
                            document.getElementById('discount_value' + a).value = sales[i].discount_value;

                            if (vtinfo.ischecked == 1) {
                                document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                            }
                            document.getElementById('vat_value' + a).value = sales[i].vat_value;
                            document.getElementById('total_price' + a).value = sales[i].total_price;
                            document.getElementById('total_discount' + a).value = sales[i].total_discount;
                            document.getElementById('all_discount' + a).value = sales[i].all_discount;



                            count = count + 1;
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            } else if (pagetype == "convert") {
                getSalesOrderDetailsconvert(id)
            }
        } else {
            getBranchDropdown(0);
            getSalesOrderDropdown();

        }
    });

    function getSalesOrderDetailsconvert(id) {
        clearDetails()

        $.ajax({
            url: $('#base_url').val() + 'service/service/getServiceOrderById',
            type: 'POST',
            data: {
                id: id,
                type2: "C"
            },
            success: function(response) {
                var sales = JSON.parse(response);

                getBranchDropdown(sales[0].branch);
                document.getElementById("showorderno").style.display = "none";
                document.getElementById("showorderno2").style.display = "block";
                document.getElementById('sales_order_no_convert').value = id
                document.getElementById('sales_order_no1').value = sales[0].service_order_id;

                document.getElementById('date').value = new Date().toISOString().split('T')[0];
                document.getElementById('eod_date').value = sales[0].eod_date;
                document.getElementById('details').value = sales[0].details;


                var $customerDropdown = $('#customer_id');
                $customerDropdown.empty();
                $customerDropdown.append('<option value="" disabled selected>Select Customer</option>');
                $.each(customers, function(index, customer) {
                    $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
                $customerDropdown.val(sales[0].customer_id).trigger('change');

                var $employeeDropdown = $('#employee_id');
                $employeeDropdown.empty();
                $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>');
                $employeeDropdown.append('<option value="1">N/A</option>');
                $.each(employees, function(index, employee) {
                    $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                });
                $employeeDropdown.val(sales[0].employee_id).trigger('change');

                var $invoiceTypeDropdown = $('#invoicetype');
                $invoiceTypeDropdown.empty();
                $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                $invoiceTypeDropdown.val(sales[0].invoicetype).trigger('change');
                $invoiceTypeDropdown.prop('disabled', false);
                incidetTypechange();

                document.getElementById('total_discount_ammount').value = sales[0].total_discount_ammount;
                document.getElementById('total_vat_amnt').value = sales[0].total_vat_amnt;
                document.getElementById('grandTotal').value = sales[0].grandTotal;
                document.getElementById('Total').value = sales[0].total;
                document.getElementById('discount').value = sales[0].discount;

                var $paymentDropdown = $('#your_dropdown_id');
                $paymentDropdown.empty();
                $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                $.each(pmethods, function(index, supplier) {
                    $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                });
                $paymentDropdown.val(1).trigger('change')
                // Re-assert the freeze: the rebuild above just reset Payment
                // Type to the plain default, which would silently undo it if
                // this sale is Credit/Credit VAT.
                applyPaymentTypeFreeze(sales[0].invoicetype);

                // count = 1;
                for (let i = 0; i < sales.length; i++) {
                    let a = i + 1;
                    document.getElementById('myRow' + a).style.display = 'table-row';
                    getActiveProduct(sales[i].service, a);

                    document.getElementById('qty' + a).value = sales[i].quantity;
                    document.getElementById('product_rate' + a).value = sales[i].product_rate;
                    document.getElementById('discount' + a).value = sales[i].discount2;
                    document.getElementById('discount_value' + a).value = sales[i].discount_value;

                    if (sales[0].invoicetype === 'cash_vat' || sales[0].invoicetype === 'credit_vat') {
                        document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                    } else {
                        document.getElementById('vat_percent' + a).value = 0;
                    }
                    document.getElementById('vat_value' + a).value = sales[i].vat_value;
                    document.getElementById('total_price' + a).value = sales[i].total_price;
                    document.getElementById('total_discount' + a).value = sales[i].total_discount;
                    document.getElementById('all_discount' + a).value = sales[i].all_discount;



                    count = count + 1;
                }
            },
            error: function(error) {
                console.log(error);
            }
        });

    }

    function getSalesOrderDropdown() {
        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "service/service/getservicesorderidbybranch",
            data: {
                type2: type2,
                branch: document.getElementById("branch").value
            },
            success: function(data) {

                var salesorder = JSON.parse(data);
                console.log(salesorder)
                var $branchDropdown = $('#sales_order_no');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Sales Order</option>'); // Add default option

                $.each(salesorder, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.service_order_id + '</option>');

                });




            }
        });
    }

    function getSalesOrderDetails() {
        clearDetails()

        $.ajax({
            url: $('#base_url').val() + 'service/service/getServiceOrderById',
            type: 'POST',
            data: {
                id: document.getElementById('sales_order_no').value,
                type2: "C"
            },
            success: function(response) {
                var sales = JSON.parse(response);
                document.getElementById('date').value = sales[0].date;
                document.getElementById('details').value = sales[0].details;

                var $customerDropdown = $('#customer_id');
                $customerDropdown.empty();
                $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                $.each(customers, function(index, customer) {
                    $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
                $customerDropdown.val(sales[0].customer_id).trigger('change')

                var $employeeDropdown = $('#employee_id');
                $employeeDropdown.empty();
                $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                $employeeDropdown.append('<option value="1">N/A</option>');
                $.each(employees, function(index, employee) {
                    $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + " " + employee.last_name + '</option>');
                });
                $employeeDropdown.val(sales[0].employee_id).trigger('change')

                var $paymentDropdown = $('#your_dropdown_id');
                $paymentDropdown.empty();
                $paymentDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
                $.each(pmethods, function(index, supplier) {
                    $paymentDropdown.append('<option value="' + supplier.id + '">' + supplier.name + '</option>');
                });
                $paymentDropdown.val(1).trigger('change')


                document.getElementById('total_discount_ammount').value = sales[0].total_discount_ammount;
                document.getElementById('total_vat_amnt').value = sales[0].total_vat_amnt;
                document.getElementById('grandTotal').value = sales[0].grandTotal;
                document.getElementById('Total').value = sales[0].total;
                document.getElementById('discount').value = sales[0].discount;

                // count = 1;
                for (let i = 0; i < sales.length; i++) {
                    let a = i + 1;
                    document.getElementById('myRow' + a).style.display = 'table-row';
                    getActiveProduct(sales[i].service, a);

                    document.getElementById('qty' + a).value = sales[i].quantity;
                    document.getElementById('product_rate' + a).value = sales[i].product_rate;
                    document.getElementById('discount' + a).value = sales[i].discount2;
                    document.getElementById('discount_value' + a).value = sales[i].discount_value;

                    if (vtinfo.ischecked == 1) {
                        document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                    }
                    document.getElementById('vat_value' + a).value = sales[i].vat_value;
                    document.getElementById('total_price' + a).value = sales[i].total_price;
                    document.getElementById('total_discount' + a).value = sales[i].total_discount;
                    document.getElementById('all_discount' + a).value = sales[i].all_discount;



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
        getActiveProduct(0, count)
        count = count + 1;

    }

    function product_search(item, name) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            document.getElementById('qty' + item).value = "";
            document.getElementById('product_rate' + item).value = services.find(s => s.service_id === document.getElementById('product' + item).value).charge;

            if (vtinfo.ischecked == 1) {
                document.getElementById('vat_percent' + item).value = services.find(s => s.service_id === document.getElementById('product' + item).value).service_vat;
            }
        }
    }



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

        var quantity = $("#qty" + sl).val();
        var discount = $("#discount" + sl).val();
        var dis_type = $("#discount_type").val();
        var price_item = $("#product_rate" + sl).val();
        var invoicetype = document.getElementById('invoicetype').value;
        var isVatType = (invoicetype === 'cash_vat' || invoicetype === 'credit_vat');
        var vat_percent = isVatType ? (parseFloat($("#vat_percent" + sl).val()) || 0) : 0;
        if (!isVatType) {
            $("#vat_value" + sl).val(0);
        }
        var avqty = $("#avqty" + sl).val();


        // if (parseInt(quantity) > parseInt(avqty)) {
        //     $("#qty" + sl).val("");
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

        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        $(".discount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (dis += parseFloat(this.value))
        });
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
        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

        $.each(services, function(index, product) {
            $productDropdown.append('<option value="' + product.service_id + '">' + product.service_name + '</option>');
        });

        if (productId > 0) {
            {
                $productDropdown.val(productId)
            }
        }
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
                console.log(branches)
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id).trigger('change')
                        getSalesOrderDropdown();
                    }
                });

                if (branchId > 0) {
                    {
                        $branchDropdown.val(branchId).trigger('change')
                    }
                }



            }
        });
    }


    function freezeInvoiceFormForCancel(message) {
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
        banner.innerHTML = '<i class="fa fa-lock"></i> ' + (message || 'Editing is locked for this invoice.');
    }

    function cancelInvoice() {
        if (!confirm('This will permanently zero out this invoice\'s amounts, cancel it, and it can no longer be edited, deleted, or reprinted. This cannot be undone. Continue?')) {
            return;
        }
        var $b = $('#cancel_invoice_btn');
        $b.prop('disabled', true);
        $.ajax({
            type: 'post',
            url: $('#base_url').val() + 'service/service/cancel_service',
            data: {
                id: id,
                csrf_test_name: $('[name="csrf_test_name"]').val()
            },
            success: function(resp) {
                var data = JSON.parse(resp);
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = $('#base_url').val() + 'manage_service_invoice';
                } else {
                    alert(data.message);
                    $b.prop('disabled', false);
                }
            },
            error: function() {
                alert('Something went wrong while cancelling this invoice');
                $b.prop('disabled', false);
            }
        });
    }

    function save() {
        arrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('customer_id').value == "" || document.getElementById('customer_id').value === " ") {
                    alert("Customer shouldn't be empty")
                    return
                } else if (document.getElementById('invoicetype').value == "") {
                    alert("Invoice Type shouldn't be empty")
                    return
                } else if (document.getElementById('your_dropdown_id').value == "") {
                    alert("Payment Type shouldn't be empty")
                    return
                } else if (document.getElementById('branch').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Service shouldn't be empty")
                    return
                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else
                if (document.getElementById('product_rate' + i).value == "") {
                    alert("Price shouldn't be empty")
                    return
                } else {
                    var dropdown = document.getElementById('product' + i);


                    arrItem.push({
                        service: document.getElementById('product' + i).value,
                        product_name: dropdown.options[dropdown.selectedIndex].text,
                        quantity: document.getElementById('qty' + i).value,
                        product_rate: document.getElementById('product_rate' + i).value ? document.getElementById('product_rate' + i).value : "0",
                        discount: document.getElementById('discount' + i).value ? document.getElementById('discount' + i).value : "0",
                        discount_value: document.getElementById('discount_value' + i).value ? document.getElementById('discount_value' + i).value : "0",
                        vat_percent: document.getElementById('vat_percent' + i).value ? document.getElementById('vat_percent' + i).value : "0",
                        vat_value: document.getElementById('vat_value' + i).value ? document.getElementById('vat_value' + i).value : "0",
                        total_price: document.getElementById('total_price' + i).value ? document.getElementById('total_price' + i).value : "0",
                        total_discount: document.getElementById('total_discount' + i).value ? document.getElementById('total_discount' + i).value : "0",
                        all_discount: document.getElementById('all_discount' + i).value ? document.getElementById('all_discount' + i).value : "0",
                    });
                }
            }

        }

        var paymentdropdown = document.getElementById('your_dropdown_id');
        $("#save_add").hide();

        if (id > 0 && pagetype == "") {
            $.ajax({
                url: $('#base_url').val() + 'service/service/update_service',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    discount: document.getElementById('discount').value,
                    type2: type2,
                    invoicetype: document.getElementById('invoicetype').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    eod_date: document.getElementById('eod_date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    employee_id: document.getElementById('employee_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    branch: document.getElementById('branch').value
                },
                success: function(response) {
                    // alert("Invoice Details Updated Successfully")
                    // window.location.href = $('#base_url').val() + 'invoice_list';

                    datas = JSON.parse(response);
                    if (datas.status === 'error') {
                        $("#save_add").show();
                        alert(datas.message);
                        return;
                    }
                    clearDetails()
                    $("#save_add").show();

                    alert("Service Details Updated Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.invoiceno);


                },
                error: function(error) {
                    console.log(error)
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'service/service/save_service',
                type: 'POST',
                data: {
                    items: arrItem,
                    type2: type2,
                    invoicetype: document.getElementById('invoicetype').value,
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    eod_date: document.getElementById('eod_date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    payment_type: document.getElementById('your_dropdown_id').value,
                    payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    employee_id: document.getElementById('employee_id').value,
                    branch: document.getElementById('branch').value,
                    service_order_no: pagetype == "convert" ? document.getElementById('sales_order_no_convert').value : document.getElementById('sales_order_no').value,



                },
                success: function(response) {
                    datas = JSON.parse(response);
                    if (datas.status === 'error') {
                        $("#save_add").show();
                        alert(datas.message);
                        return;
                    }
                    clearDetails()
                    $("#save_add").show();

                    alert("Service Details saved Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.invoiceno);
                },
                error: function(error) {
                    console.log(error)
                }
            });

        }







    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
            var $productDropdown = $('#product' + i);
            $productDropdown.empty();
            $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

            $.each(services, function(index, product) {
                $productDropdown.append('<option value="' + product.service_id + '">' + product.service_name + '</option>');
            });


            document.getElementById('myRow' + i).style.display = 'none';

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
        document.getElementById('date').value = ""
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

    function save_customer() {
        var name = document.getElementById('customer_name').value.trim();
        var phone = document.getElementById('customer_phone').value.trim();

        if (!name) {
            alert("Customer name shouldn't be empty");
            return;
        }

        $.ajax({
            url: $('#base_url').val() + 'invoice/invoice/save_customer',
            type: 'POST',
            data: {
                customer_name: name,
                customer_phone: phone
            },
            success: function(response) {
                var result = JSON.parse(response);
                customers = result.all_customer;

                var $customerDropdown = $('#customer_id');
                $customerDropdown.empty();
                $customerDropdown.append('<option value="" disabled selected>Select Customer</option>');
                $.each(customers, function(index, customer) {
                    $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                });
                $customerDropdown.val(result.inserted_id).trigger('change');

                alert("Customer saved successfully");
                $('#customerModel').modal('hide');
                document.getElementById('customer_name').value = '';
                document.getElementById('customer_phone').value = '';
            },
            error: function(error) {
                console.log(error);
            }
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
            document.title = 'serviceinvoice_' + voucherNo + '_' + pdfDateStamp();
        }

        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.href = $('#base_url').val() + 'add_service_invoice';
            })
        });
    }
</script>