<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    .highlight {
        background-color: #8b5cf6 !important;
        color: white !important;
    }

    .product_field {
        width: 200px;
        height: 10px;
    }

    .field {
        width: 30px;
    }

    .unit {
        width: 70px;
    }

    .qty {
        width: 100px;
    }

    .rate {
        width: 150px;
    }

    /* Hidden by default (Cash/Credit, non-VAT): JS only needs to reveal these
       for VAT invoice types. Without this, if the JS toggle on page load never
       runs (or errors before reaching it), both the VAT and non-VAT footer
       label cells render at once, producing duplicated-looking Total/Sale
       Discount/Grand Total columns. */
    .vathidden {
        display: none;
    }

    /* hide mobile labels on desktop — shown only inside the media queries below */
    .td-mobile-label { display: none; }

    /* Table desktop */
    #saleTable { font-size: 12px; }
    #saleTable thead th { white-space: nowrap; font-size: 11px; padding: 8px 6px; font-weight: 700; }
    #saleTable tbody tr td { padding: 4px 5px; vertical-align: middle; }
    #saleTable tbody td .form-control { height: 30px; font-size: 12px; padding: 2px 6px; }
    #saleTable tfoot tr td { font-size: 13px; padding: 6px 8px; }
    #saleTable tfoot .form-control { height: 32px; font-size: 13px; }

    /* Action column (11th: delete button) and the trailing spacer column (12th)
       were stretching wide on large screens since nothing constrained their
       width — cap them so the table doesn't leave a big empty gap. */
    @media (min-width: 1025px) {
        #saleTable thead th:nth-child(11),
        #saleTable tbody td:nth-child(11) { width: 70px; max-width: 70px; text-align: center; }
        #saleTable thead th:nth-child(12) { width: 1px; padding: 0; }
    }

    /* ── Tablet: 768px – 1024px — 2-col card layout ── */
    @media (min-width: 768px) and (max-width: 1024px) {
        .table-responsive { overflow: visible !important; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }

        .td-mobile-label { display: block; }

        #saleTable tbody tr {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 20px;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            width: 100% !important;
        }

        #saleTable tbody td {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 8px 12px;
            border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
            white-space: normal;
        }
        #saleTable tbody td:nth-child(even) { border-right: none !important; }

        /* Action cell (delete button) — full-width bar at bottom of card */
        #saleTable tbody td:last-child {
            grid-column: 1 / -1;
            border-bottom: none !important;
            border-right: none !important;
            padding: 0;
        }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child .btn {
            width: 100%;
            border-radius: 0;
            margin: 0;
            display: block;
        }

        #saleTable tbody td.vathidden[style*="table-cell"] {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
        }

        #saleTable tbody td .form-control,
        #saleTable tbody td > div,
        #saleTable tbody td .select2-container { width: 100% !important; box-sizing: border-box; }

        /* ── Tfoot: stacked summary card ── */
        #saleTable tfoot {
            display: block;
            margin-top: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important;
            width: 100%; box-sizing: border-box;
            padding: 9px 14px;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px;
            margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] p {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: auto !important; text-align: right; margin: 0;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child p { font-size: 15px !important; font-weight: 700 !important; }

        /* Payment Type / Details box — full width on tablet */
        .col-sm-8.row.table-bordered { width: 100% !important; margin-top: 14px; }
    }

    /* ── Mobile: single-column card layout ── */
    @media (max-width: 767px) {
        .col-sm-8.row.table-bordered { width: 100% !important; float: none; box-sizing: border-box; margin-top: 14px; }

        .table-responsive { overflow: visible; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }

        #saleTable tbody tr {
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 16px;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] {
            display: block !important;
            width: 100% !important;
        }

        #saleTable tbody td {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 6px 10px;
            border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            white-space: normal;
        }

        #saleTable tbody td:last-child {
            border-bottom: none !important;
            padding: 0;
        }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child .btn {
            width: 100%;
            border-radius: 0;
            margin: 0;
            display: block;
        }

        .td-mobile-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        #saleTable tbody td .form-control,
        #saleTable tbody td > div,
        #saleTable tbody td .select2-container { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }

        #saleTable tbody td.vathidden[style*="table-cell"] {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
            padding: 6px 10px;
            border-bottom: 1px solid #f0f0f0 !important;
        }

        /* tfoot — summary card matching tbody card style */
        #saleTable tfoot {
            display: block;
            margin-top: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important;
            width: 100%;
            box-sizing: border-box;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] p {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
            font-size: 13px;
            font-weight: 600;
            height: auto !important;
            text-align: right;
            margin: 0;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child td[data-label]::before { color: #555; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child p {
            font-size: 15px !important;
            font-weight: 700 !important;
        }
    }
</style>
<style>
    .compact-row td,
    .compact-row select,
    .compact-row input {
        padding: 2px 4px !important;
        font-size: 12px !important;
        line-height: 1.1 !important;
        height: 10px !important;
    }

    .compact-row select.form-control,
    .compact-row input.form-control {
        min-height: 10px !important;
        height: 10px !important;
    }
</style>

<style>
    .compact-row1 td,
    .compact-row1 select,
    .compact-row1 input {
        padding: 7px 4px !important;
        height: 20px !important;
    }

    .compact-row1 select.form-control,
    .compact-row1 input.form-control {
        min-height: 20px !important;
        height: 20px !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title">
                    <h4 id="title"><?php echo $title; ?></h4>
                </div>
            </div>

            <div class="panel-body">


                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Sale Date
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="sale_date" value="<?php echo $date; ?>" id="date" onkeyup='handleDateKeyPress(event)' />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <!-- <select class="form-control" id="branch" required name="branch" tabindex="3">


                                </select> -->
                                <input class='form-control' type='text' id="branchInput" placeholder='Branch...' onkeyup='handleBranchKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='branchId' hidden />
                                <div id='branchResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label"> Invoice Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <!-- <select class="form-control" id="incidenttype" required name="incidenttype" tabindex="3">
                                    <option value=""></option>
                                    <option value="1">Sales</option>
                                    <option value="2">Whole Sale</option>

                                </select> -->
                                <input class='form-control' type='text' id="invoicetypeInput" placeholder='Invoice Type...' onkeyup='handleInvoicetypeKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='invoiceType' hidden />
                                <input type='number' id='pos_due_date' min='0' value='0' placeholder='Due Days' class='form-control mt-1' style='display:none;' onkeyup="if(event.key==='Enter'||event.key==='ArrowRight'){event.preventDefault();var el=document.getElementById('incidenttypeInput');el.focus();el.select();}else if(event.key==='ArrowLeft'){event.preventDefault();var el2=document.getElementById('invoicetypeInput');el2.focus();el2.select();}" />
                                <div id='invoicetypeResults1' style='margin-left: 15px;    max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <!-- <select class="form-control" id="incidenttype" required name="incidenttype" tabindex="3">
                                    <option value=""></option>
                                    <option value="1">Sales</option>
                                    <option value="2">Whole Sale</option>

                                </select> -->
                                <input class='form-control' type='text' id="incidenttypeInput" placeholder='Incident Type...' onkeyup='handleIncidenttypeKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='incidenttype' hidden />
                                <div id='incidenttypeResults1' style='margin-left: 15px;    max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>

                    </div>


                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="adress" class="col-sm-4 col-form-label">Customer
                            </label>
                            <div class="col-sm-8">

                                <input class='form-control' type='text' id="customerInput" placeholder='Customer Id...' onkeyup='handleCustomerKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='customerId' hidden />
                                <div id='customerResults1' style='margin-left: 15px;   max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Salesman
                            </label>
                            <div class="col-sm-8">
                                <input class='form-control' type='text' id="employeeInput" placeholder='Employee...' onkeyup='handleEmployeeKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='employeeId' hidden />
                                <div id='employeeResults1' style='margin-left: 15px;    max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>
                    </div>





                </div>



                <div class="row">

                    <div class="col-sm-4">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Product
                            </label>
                            <div class="col-sm-8">
                                <input class='form-control' type='text' id="productInput" placeholder='Product...' onkeyup='handleProductKeyPress(event)' autocomplete='off' />
                                <div id='productResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>
                    </div>


                </div>


                <br>


                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="saleTable">
                        <thead>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3" class="text-center">
                                    <span style="margin-left:10px"><b>Av.Qty:</b></span>
                                    <input type="hidden" name="code[]" onkeyup="product_search(0,'code');"
                                        class="total_qntt_1 form-control text-right"
                                        id="code0" placeholder="0.00" min="0" readonly />
                                    <span id='codetype0'></span>
                                </td>
                                <td colspan="5"></td>
                            </tr>
                        </thead>
                        <thead>
                            <tr>
                                <th class="text-center product_field">Product<i
                                        class="text-danger">*</i></th>
                                <th class="text-center">Store<i class="text-danger">*</i>
                                </th>
                                <th class="text-center">Batch<i class="text-danger">*</i>
                                </th>
                                <th class="text-center ">Unit<i class="text-danger">*</i> </th>
                                <!-- <th class="text-center ">Av.Qty</th> -->
                                <th class="text-center ">Qty<i
                                        class="text-danger">*</i></th>
                                <th class="text-center ">Price val <i
                                        class="text-danger"> *</i></th>
                                <th class="text-center ">Discount</th>
                                <th class="text-center ">Dis.val</th>

                                <th class="text-center vathidden">VAT.val</th>


                                <th class="text-center ">Total</th>

                                <th class="text-center"><?php echo display('action') ?></th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field" style="height: 5px;">

                                    <input type="text" name="product[]"
                                        class="total_qntt_1 form-control text-left"
                                        id="product0" value="" min="0" readonly />
                                    <input type="hidden" name="product[]"
                                        id="productId0" value="" min="0" readonly />

                                    <input type="hidden" id="mconversion_ratio0" />
                                    <input type="hidden" id="mastercost_price0" />
                                    <input type="hidden" id="defaultsaleprice0" />

                                    <input type="hidden" id="bd0" />
                                    <input type="hidden" id="ad0" />
                                    <input type="hidden" id="mrpprice0" />
                                    <input type="hidden" id="isstock0" />




                                </td>

                                <td class="rate">
                                    <!-- <select class="form-control" id="store0" name="store[]" tabindex="3" onchange="product_search(0,'store')">
                                        <option value=""></option>
                                    </select> -->
                                    <div style='position: relative; display: inline-block;'>
                                        <input class='form-control' type='text' id="storeInput" placeholder='Store...' onkeyup='handleStoreKeyPress(event)' autocomplete='off' />
                                        <input type='text' name='customer_id[]' id='storeId0' hidden />
                                        <div id='storeResults1' style='  width: 100%;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;' autocomplete='off'>

                                        </div>
                                    </div>
                                </td>
                                <td class="rate">
                                    <!-- <select class="form-control" id="store0" name="store[]" tabindex="3" onchange="product_search(0,'store')">
                                        <option value=""></option>
                                    </select> -->
                                    <div style='position: relative; display: inline-block;'>
                                        <input class='form-control' type='text' id="batchInput" placeholder='Batch...' onkeyup='handleBatchKeyPress(event)' autocomplete='off' />
                                        <input type='text' name='customer_id[]' id='batchId0' hidden />
                                        <div id='batchResults1' style='  width: 100%;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;' autocomplete='off'>

                                        </div>
                                    </div>
                                </td>

                                <td class="qty">
                                    <div style='position: relative; display: inline-block;'>

                                        <input class='form-control' type='text' id="unitInput" placeholder='Unit...' onkeyup='handleUnitKeyPress(event)' autocomplete='off' />
                                        <input type='text' name='customer_id[]' id='unitId0' hidden />
                                        <div id='unitResults1' style='width: 100%;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;' autocomplete='off'>

                                        </div>
                                    </div>
                                    <input type="hidden" id="conversionid0" />
                                    <input type="hidden" id="conversiontype0" />
                                    <input type="hidden" id="conversion_ratio0" />
                                </td>
                                <!-- <td class="qty">
                                   
                                </td> -->



                                <td class="qty">
                                    <input type="number" name="product_quantity[]" id="qty0" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(0,'qty',event);" onchange="calculate_sum(0,'qty',event);" placeholder="0.00" value="" tabindex="6" autocomplete='off' />
                                </td>
                                <td class="rate">
                                    <input type="number" name="product_rate[]" onkeyup="calculate_sum(0,'product_rate',event);" onchange="calculate_sum(0,'product_rate',event);" id="product_rate0" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" autocomplete='off' />
                                </td>

                                <td class="qty">
                                    <input type="number" name="discount_per[]" onkeyup="calculate_sum(0,'discount',event);" onchange="calculate_sum(0,'discount',event);" id="discount0" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" autocomplete='off' />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">

                                </td>
                                <td class="rate">
                                    <input type="text" name="discountvalue[]" id="discount_value0" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->

                                <td class="rate vathidden">
                                    <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(0,'vatpercent',event);" onchange="calculate_sum(0,'vatpercent',event);" id="vat_percent0" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" autocomplete='off' readonly />

                                    <input type="text" name="vatvalue[]" id="vat_value0" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  end-->
                                <td class="product_field">
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price0" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="total_discount0" class="" />
                                    <input type="hidden" id="all_discount0" class="total_discount dppr" name="discount_amount[]" />
                                </td>

                                <td>
                                </td>

                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>" style="height: 10px;" class="compact-row">
                                    <td class="product_field" style="height: 10px;">
                                        <p id="product<?php echo $i; ?>" style="padding-left: 10px; font-weight: bold;"></p>
                                        <input type="hidden" name="product[]"
                                            id="productId<?php echo $i; ?>" value="" min="0" readonly />

                                        <input type="hidden" id="mconversion_ratio<?php echo $i; ?>" />
                                        <input type="hidden" id="mastercost_price<?php echo $i; ?>" />
                                        <input type="hidden" id="bd<?php echo $i; ?>" />
                                        <input type="hidden" id="ad<?php echo $i; ?>" />
                                        <input type="hidden" id="defaultsaleprice<?php echo $i; ?>" />
                                        <input type="hidden" id="isstock<?php echo $i; ?>" />
                                    </td>

                                    <td class="rate">
                                        <input type='text' name='customer_id[]' id='storeId<?php echo $i; ?>' hidden />
                                        <p id="store<?php echo $i; ?>" style="padding-left: 10px;font-weight: bold;"></p>
                                    </td>
                                    <td class="rate">
                                        <input type='text' name='customer_id[]' id='batchId<?php echo $i; ?>' hidden />
                                        <p id="batch<?php echo $i; ?>" style="padding-left: 10px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty">
                                        <input type='text' name='customer_id[]' id='unitId<?php echo $i; ?>' hidden />
                                        <p id="unit<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                        <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                        <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                        <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />
                                    </td>



                                    <td class="qty">
                                        <p id="qty<?php echo $i; ?>" style="text-align: center;font-weight: bold;"></p>

                                    </td>

                                    <td class="rate">
                                        <p id="product_rate<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty">
                                        <p id="discount<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                    </td>

                                    <td class="rate">
                                        <p id="discountval<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td class="qty vathidden">
                                        <input type='hidden' id="vat_percent<?php echo $i; ?>" />
                                        <p id="vat_value<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                    </td>

                                    <td class="product_field">
                                        <p id="total_price<?php echo $i; ?>" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                    </td>

                                    <td>
                                        <!-- Delete button can remain -->
                                        <button class="btn btn-danger btn-xs" type="button" onclick="deleteRow(<?php echo $i; ?>)">
                                            <i class="fa fa-trash fa-xs"></i>
                                        </button>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr class="compact-row1">
                                <td colspan="9" class="text-right vathidden"><b><?php echo display('total') ?>:</b></td>
                                <td colspan="8" class="text-right vatshow"><b><?php echo display('total') ?>:</b></td>

                                <td class="text-right">
                                    <!-- <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" /> -->
                                    <p id="Total" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>


                                <td colspan="9" class="text-right vathidden"><b>Sale Discount:</b></td>
                                <td colspan="8" class="text-right vatshow"><b>Sale Discount:</b></td>

                                <td class="text-right">
                                    <input type="number" id="sale_discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(0,'sale_discount',event)" name="discount" placeholder="0.00" value="" autocomplete='off' />
                                </td>

                                <td>

                                </td>
                            </tr>
                            <tr class="compact-row1">
                                <td colspan="9" class="text-right vathidden"><b><?php echo display('total_discount') ?>:</b></td>
                                <td colspan="8" class="text-right vatshow"><b><?php echo display('total_discount') ?>:</b></td>


                                <td class="text-right">
                                    <!-- <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" /> -->
                                    <p id="total_discount_ammount" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>

                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr class="compact-row1">


                                <td class="text-right vathidden" colspan="9"><b><?php echo display('ttl_val') ?>:</b></td>

                                <td class="text-right vathidden">
                                    <p id="total_vat_amnt" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                </td>
                                <td class="text-right vathidden">

                                </td>
                            </tr>



                            <tr class="compact-row1">

                                <td colspan="9" class="text-right vathidden"><b><?php echo display('grand_total') ?>:</b></td>
                                <td colspan="8" class="text-right vatshow"><b><?php echo display('grand_total') ?>:</b></td>


                                <td class="text-right">
                                    <p id="grandTotal" style="text-align: right;padding-right: 20px;font-weight: bold;"></p>
                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <script>
                    /* Mobile card labels — runs after table HTML is parsed */
                    (function () {
                        var colLabels = ['Product','Store','Batch','Unit','Qty','Price','Discount','Dis.val','VAT','Total','Action'];

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
                            ['Total',                 'Total'],
                            ['sale_discount',          'Sale Discount'],
                            ['total_discount_ammount','Total Discount'],
                            ['total_vat_amnt',        'VAT Total'],
                            ['grandTotal',             'Grand Total']
                        ].forEach(function (pair) {
                            var el = document.getElementById(pair[0]);
                            if (el && el.parentNode) el.parentNode.setAttribute('data-label', pair[1]);
                        });
                    })();
                    </script>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <!-- <div class="col-sm-3 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row no-gutters">
                                <div class="form-group ">
                                    <label for="payments" class="col-form-label pb-2"><?php echo display('payment_type'); ?> <i class="text-danger">*</i>
                                    </label>
                                    <input class='form-control' type='text' id="paymentInput" placeholder='Payment Type...' onkeyup='handlePaymentKeyPress(event)' autocomplete='off' />
                                    <div id='paymentResults1' style='margin-left: 15px;  max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> -->

                </div>
                <div class="col-sm-8 row table-bordered p-20">

                    <!-- Payment Type -->

                    <div class="col-sm-5">
                        <div class="form-group">
                            <label for="supplier_sss" class="col-sm-5 col-form-label">Payment Type
                            </label>
                            <div class="col-sm-7">
                                <input class='form-control' type='text' id="paymentInput" placeholder='Payment Type...' onkeyup='handlePaymentKeyPress(event)' autocomplete='off' />
                                <input type='text' name='customer_id[]' id='paymentId' hidden />
                                <div id='paymentResults1' style='margin-left: 15px;   max-height: 150px;  overflow-y: auto; border: 1px solid #ddd; position: absolute;  top: 100%;  left: 0;  z-index: 1000;  background-color: #fff;border-radius: 4px;'>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Details -->
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="supplier_sss" class="col-sm-3 col-form-label">Details
                            </label>
                            <div class="col-sm-9">

                                <textarea
                                    class="form-control"
                                    tabindex="4"
                                    id="details"
                                    name="sale_details"
                                    placeholder="<?php echo display('details') ?>"
                                    rows="3"
                                    onkeyup="handleDetailKeyPress(event)">
</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <button id="save_add" class="btn btn-success" name="add-invoice" onclick="save()">
                            <?php echo (empty($id) ? display('save') : display('update')) ?></button>
                    </div>
                </div>


            </div>
        </div>

    </div>
</div>

<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
//echo "let batches=" . json_encode($batches) . ";";
echo "let units2=" . json_encode($units) . ";";

echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "</script>";
?>
<script>
    let branches = null;
    let batches = [];
    let includedStroes = []
    // $('body').addClass("sidebar-mini sidebar-collapse");
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

    function handleDateKeyPress() {
        if (event.shiftKey && event.key === "Escape") {

            const input = document.getElementById("branchInput");
            input.focus();
            input.select();

            //clearResults();
            return
        } else
        if (event.key === 'Enter' || event.key === 'ArrowRight') {
            let element2 = document.getElementById("customerInput");
            element2.focus();
            element2.select()
            clearResults();




        } else

        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        }



    }
    document.addEventListener('keydown', function(event) {

        if (event.key === 'F1') {
            let element2 = document.getElementById("productInput");
            element2.focus();

        }

        if (event.key === 'F2') {
            const input = document.getElementById("branchInput");
            input.focus();
            input.select();

        }


    });

    function handleCustomerKeyPress(event, count) {
        const inputElement = document.getElementById('customerInput');
        const query = inputElement.value;
        customers.sort((a, b) => a.customer_id - b.customer_id);
        const results = customers
            .filter(customer => customer.customer_name.toLowerCase().includes(query));
        if (event.shiftKey && event.key === "Escape") {

            const input = document.getElementById("branchInput");
            input.focus();
            input.select();

            clearResults();
            return
        } else
        if (event.key === "Escape") {
            event.preventDefault();
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();
            return


        } else
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }
        if (event.key === 'ArrowDown') {
            $("#customerId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemcustomer(currentIndex);
            }
        } else if (event.key === 'ArrowUp') {
            $("#customerId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemcustomer(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("incidenttypeInput");
            element2.focus();
            document.getElementById('incidenttypeInput').select()

        } else if (event.key === 'Tab') {
            if (document.getElementById('incidenttype').value != "") {

                let element2 = document.getElementById("customerInput");
                element2.focus();
                document.getElementById('customerInput').select()
                return


            }
            // Select the highlighted item
            if (results.length > 0) {
                if (document.getElementById('incidenttypeInput').value == "") {
                    alert("Incident Type shouldn't be empty")
                    return
                }

                document.getElementById('incidenttypeInput').value = results[currentIndex];

                document.getElementById('incidenttypeInput').select()

                if (results[currentIndex] == "Retail") {
                    document.getElementById('incidenttype').value = 1;
                } else if (results[currentIndex] == "Wholesale") {
                    document.getElementById('incidenttype').value = 2;
                }
                clearResults();
            } else {
                document.getElementById('incidenttypeInput').value = "";
                document.getElementById('incidenttype').value = "";
                let element2 = document.getElementById("incidenttypeInput");
                element2.focus();
                document.getElementById('incidenttypeInput').select()
                alert("Incident Type shouldn't be empty")
                return
            }


        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {

            if (document.getElementById('customerId').value != "") {

                let element2 = document.getElementById("employeeInput");
                element2.focus();
                document.getElementById('employeeInput').select()
                return
            }
            // Select the highlighted item
            if (results.length > 0) {
                if (results[currentIndex].customer_name == "") {
                    document.getElementById('customerInput').value = "Cash";
                    document.getElementById('customerInput').select()
                    document.getElementById('customerId').value = 1;

                    let element2 = document.getElementById("employeeInput");
                    element2.focus();
                    document.getElementById('employeeInput').select()
                    return

                }
                document.getElementById('customerInput').value = results[currentIndex].customer_name + " ";

                document.getElementById('customerInput').select()
                document.getElementById('customerId').value = results[currentIndex].customer_id;
                _posApplyCustomerDueDate();
                clearResults();
            } else {
                document.getElementById('customerInput').value = "Cash";
                document.getElementById('customerInput').select()
                document.getElementById('customerId').value = 1;

                let element2 = document.getElementById("employeeInput");
                element2.focus();
                document.getElementById('employeeInput').select()
            }


        } else if (event.key === "Backspace") {
            clearResults()
            document.getElementById('customerId').value = "";
            if (document.getElementById('customerInput').value == "") {
                document.getElementById('customerInput').value = "";
                displayResultsCustomer(customers, count);

            }
        } else {
            currentIndex = -1;
            displayResultsCustomer(results, count);

        }
    }

    function handleDetailKeyPress(event) {
        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }
        if (event.key === 'Enter' || event.key === 'ArrowRight') {
            event.preventDefault();
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()

        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();
            let element2 = document.getElementById("employeeInput");
            element2.focus();
            document.getElementById('employeeInput').select()

        }

    }

    function handleDiscountKeyPress(event) {
        if (event.key === 'Enter' || event.key === 'ArrowRight') {
            event.preventDefault();
            let element2 = document.getElementById("paymentInput");
            element2.focus();
            document.getElementById('paymentInput').select()

        }


    }



    function handleEmployeeKeyPress(event) {

        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }
        const employeeElement = document.getElementById('employeeInput');
        const query = employeeElement.value;

        let results = [];

        if (!employees) {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()

        }



        results = employees
            .filter(employee => employee.first_name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            event.preventDefault();

            $("#employeeId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            event.preventDefault();

            let element2 = document.getElementById("customerInput");
            element2.focus();
            document.getElementById('customerInput').select()

        } else if (event.key === 'ArrowUp') {
            event.preventDefault();

            $("#employeeId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {


            if (document.getElementById('employeeId').value != "") {

                let element2 = document.getElementById("productInput");
                element2.focus();
                document.getElementById('productInput').select()
                return


            }
            // Select the highlighted item
            if (results.length > 0) {


                document.getElementById('employeeInput').value = results[currentIndex].first_name;

                document.getElementById('employeeInput').select()
                document.getElementById('employeeId').value = results[currentIndex].id;
                if (document.getElementById('employeeInput').value == "" || document.getElementById('employeeInput').value == "N/A") {
                    document.getElementById('employeeInput').value = "N/A";
                    document.getElementById('employeeInput').select()
                    document.getElementById('employeeId').value = 1;
                    // let element2 = document.getElementById("productInput");
                    // element2.focus();
                    // document.getElementById('productInput').select()
                }
                clearResults();
            } else {
                document.getElementById('employeeInput').value = ""
                document.getElementById('employeeId').value = ""

                let element2 = document.getElementById("details");
                element2.focus();
                document.getElementById('details').select()
                clearResults();
            }


        } else if (event.key === "Backspace") {
            clearResults()
            event.preventDefault();
            document.getElementById('employeeId').value = "";
            if (document.getElementById('employeeInput').value == "") {
                document.getElementById('employeeInput').value = "";
                displayResultsEmployee(employees, count);

            }
        } else {
            currentIndex = -1;
            displayResultsEmployee(results, count);

        }

    }

    function handleInvoicetypeKeyPress() {

        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }
        const invoicetypeElement = document.getElementById('invoicetypeInput');
        const query = invoicetypeElement.value;

        invoiceTypes = ['Cash', 'Credit',
            'Cash VAT', 'Credit VAT'
        ]
        const results = invoiceTypes
            .filter(invoiceType => invoiceType.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#invoiceType").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemInvoicetype(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("branchInput");
            element2.focus();
            document.getElementById('branchInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#invoiceType").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemInvoicetype(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('invoiceType').value != "") {
                var _ivt = document.getElementById('invoiceType').value;
                if ((_ivt === 'credit' || _ivt === 'credit_vat') && document.getElementById('pos_due_date').style.display !== 'none') {
                    document.getElementById('pos_due_date').focus();
                    document.getElementById('pos_due_date').select();
                } else {
                    let element2 = document.getElementById("incidenttypeInput");
                    element2.focus();
                    document.getElementById('incidenttypeInput').select();
                }
                return
            }
            // Select the highlighted item
            if (results.length > 0) {
                // if (document.getElementById('invoicetypeInput').value == "") {
                //     alert("Invoice Type shouldn't be empty")
                //     return
                // }

                document.getElementById('invoicetypeInput').value = results[currentIndex];

                document.getElementById('invoicetypeInput').select()
                if (results[currentIndex] == "Cash") {
                    document.getElementById('invoiceType').value = 'cash';
                } else if (results[currentIndex] == "Credit") {
                    document.getElementById('invoiceType').value = 'credit';
                } else if (results[currentIndex] == "Cash VAT") {
                    document.getElementById('invoiceType').value = 'cash_vat';
                } else if (results[currentIndex] == "Credit VAT") {
                    document.getElementById('invoiceType').value = 'credit_vat';
                }
                if (document.getElementById('invoiceType').value == 'cash_vat' ||
                    document.getElementById('invoiceType').value == 'credit_vat' ||
                    document.getElementById('invoiceType').value == 'svat') {
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
                var _posInvType = document.getElementById('invoiceType').value;
                var _posDueDateEl = document.getElementById('pos_due_date');
                if (_posInvType === 'credit' || _posInvType === 'credit_vat') {
                    _posDueDateEl.style.display = '';
                    _posApplyCustomerDueDate();
                } else {
                    _posDueDateEl.style.display = 'none';
                    _posDueDateEl.value = 0;
                }
                clearResults();
                if (_posInvType === 'credit' || _posInvType === 'credit_vat') {
                    _posDueDateEl.focus();
                    _posDueDateEl.select();
                } else {
                    document.getElementById('incidenttypeInput').focus();
                    document.getElementById('incidenttypeInput').select();
                }
            } else {
                document.getElementById('invoicetypeInput').value = "";
                document.getElementById('invoiceType').value = "";
                alert("Invoice Type shouldn't be empty")
                return
            }


        } else if (event.key === "Backspace") {
            clearResults()
            document.getElementById('invoiceType').value = "";

            if (document.getElementById('invoicetypeInput').value == "") {
                document.getElementById('invoicetypeInput').value = "";
                displayResultsInvoiceType(invoiceTypes, count);

            }


        } else {
            document.getElementById('invoiceType').value = "";
            currentIndex = -1;
            displayResultsInvoiceType(results, count);

        }

    }

    function handleIncidenttypeKeyPress() {

        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }
        const incidenttypeElement = document.getElementById('incidenttypeInput');
        const query = incidenttypeElement.value;

        incidentTypes = ['Retail', 'Wholesale']
        const results = incidentTypes
            .filter(incidenttype => incidenttype.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#incidenttype").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemIncidenttype(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            var _ivtLeft = document.getElementById('invoiceType').value;
            if ((_ivtLeft === 'credit' || _ivtLeft === 'credit_vat') && document.getElementById('pos_due_date').style.display !== 'none') {
                document.getElementById('pos_due_date').focus();
                document.getElementById('pos_due_date').select();
            } else {
                let element2 = document.getElementById("invoicetypeInput");
                element2.focus();
                document.getElementById('invoicetypeInput').select();
            }

        } else if (event.key === 'ArrowUp') {
            $("#incidenttype").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemIncidenttype(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('incidenttype').value != "") {

                let element2 = document.getElementById("customerInput");
                element2.focus();
                document.getElementById('customerInput').select()
                return


            }
            // Select the highlighted item
            if (results.length > 0) {
                // if (document.getElementById('incidenttypeInput').value == "") {
                //     alert("Incident Type shouldn't be empty")
                //     return
                // }

                document.getElementById('incidenttypeInput').value = results[currentIndex];

                document.getElementById('incidenttypeInput').select()

                if (results[currentIndex] == "Retail") {
                    document.getElementById('incidenttype').value = 1;
                } else if (results[currentIndex] == "Wholesale") {
                    document.getElementById('incidenttype').value = 2;
                }
                clearResults();
            } else {
                document.getElementById('incidenttypeInput').value = "";
                document.getElementById('incidenttype').value = "";
                alert("Incident Type shouldn't be empty")
                return
            }


        } else if (event.key === "Backspace") {
            clearResults()
            document.getElementById('incidenttype').value = "";
            document.getElementById('incidenttypeInput').value = "";
            displayResultsIncidentType(incidentTypes, count);
        } else {
            document.getElementById('incidenttype').value = "";
            currentIndex = -1;
            displayResultsIncidentType(results, count);

        }

    }

    function handleBranchKeyPress() {
        if (event.key === "Escape") {
            event.preventDefault();

            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()
            clearResults();

            return


        } else
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()
            clearResults();
            return
        }

        const branchElement = document.getElementById('branchInput');
        const query = branchElement.value;

        // incidentTypes = ['Sale', 'Wholesale']
        const results = branches
            .filter(branch => branch.name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("date");
            element2.focus();
            document.getElementById('date').select()

        } else if (event.key === 'ArrowUp') {
            $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItememployee(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('branchId').value != "") {

                let element2 = document.getElementById("invoicetypeInput");
                element2.focus();
                document.getElementById('invoicetypeInput').select()
                return


            }
            // Select the highlighted item
            if (results.length > 0) {
                // if (document.getElementById('branchInput').value == "") {
                //     alert("Branch shouldn't be empty")
                //     return
                // }

                getBranchNature(results[currentIndex].id)
                document.getElementById('branchInput').value = results[currentIndex].name;

                document.getElementById('branchInput').select()
                document.getElementById('branchId').value = results[currentIndex].id;
                clearResults();
            } else {
                document.getElementById('branchInput').value = "";
                document.getElementById('branchId').value = "";
                alert("Branch shouldn't be empty")
                return
            }


        } else if (event.key === "Backspace") {
            clearResults()

            document.getElementById('branchInput').value == ""
            document.getElementById('branchId').value = "";
            displayResultsBranch(branches, count);

        } else {
            currentIndex = -1;
            displayResultsBranch(results, count);

        }

    }


    let productResults = [];

    function handleProductKeyPress() {

        const productElement = document.getElementById('productInput');
        const query = productElement.value;
        if (event.key === 'Escape') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults()
            return

        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('sale_discount').select()
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults();
            return
        }
        // incidentTypes = ['Sale', 'Wholesale']
        // const results = products
        //     .filter(product => product.product_name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < productResults.length - 1) {
                currentIndex++;
                highlightItemproduct(currentIndex);
            }

        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("employeeInput");
            element2.focus();
            document.getElementById('employeeInput').select()

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemproduct(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            // if (document.getElementById('branchId').value != "") {

            //     // let element2 = document.getElementById("branchId");
            //     // element2.focus();

            // }
            // Select the highlighted item
            if (productResults.length > 0) {
                // if (document.getElementById('productInput').value == "") {
                //     alert("Product shouldn't be empty")
                //     return
                // }
                document.getElementById('product0').value = productResults[currentIndex].product_name;
                document.getElementById('productId0').value = productResults[currentIndex].id;
                document.getElementById('productInput').value = "";

                // document.getElementById('branchId').value = results[currentIndex].id;
                product_search2(0, "product", productResults[currentIndex].id)
                productResults = [];
                clearResults();
            } else {
                alert("Product shouldn't be empty")
                document.getElementById('product0').value = "";
                document.getElementById('productId0').value = "";
                document.getElementById('productInput').value = "";
                return
            }


        } else if (event.key === "Backspace") {
            clearResults()

            // document.getElementById('branchId').value = "";
        } else {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getProductByName',
                type: 'POST',
                data: {
                    product_name: query,
                },
                success: function(response) {
                    var products = JSON.parse(response);

                    productResults = products
                        .filter(product => product.product_name.toLowerCase().includes(query.toLowerCase()));
                    currentIndex = -1;
                    displayResultsProduct(productResults, count);

                },
                error: function(error) {
                    console.log(error);
                }
            });

        }

    }

    function handleStoreKeyPress() {
        if (event.key === 'Escape') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('product0').value = '';
            // document.getElementById('unitId0').value = '';
            // document.getElementById('batchId0').value = '';
            // document.getElementById('storeId0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults()
            return

        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('sale_discount').select()
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults();
            return
        }
        const productElement = document.getElementById('storeInput');
        const query = productElement.value;

        const results = includedStroes
            .filter(store => store.name.toLowerCase().includes(query));
        if (event.key === 'ArrowDown') {
            $("#storeId0").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemstore(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('productInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#storeId0").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemstore(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('storeId0').value != "") {

                let element2 = document.getElementById("batchInput");
                element2.focus();
                element2.select();

                return


            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                // if (document.getElementById('storeInput').value == "") {
                //     alert("Store shouldn't be empty")
                //     return
                // }
                document.getElementById('storeInput').value = results[currentIndex].name;

                document.getElementById('storeInput').select()
                document.getElementById('storeId0').value = results[currentIndex].id;

                //avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)
                if (document.getElementById('isstock0').value == 1) {

                    avStock(0, document.getElementById('productId0').value,
                        document.getElementById('storeId0').value, document.getElementById('batchId0').value, "", "")
                }
                clearResults();
            }



        } else if (event.key === "Backspace") {
            clearResults()

            document.getElementById('storeId0').value = "";


            if (document.getElementById('storeInput').value == "") {
                document.getElementById('storeInput').value = "";
                displayResultsStore(includedStroes, count);

            }
        } else {
            currentIndex = -1;
            displayResultsStore(results, count);

        }

    }

    function handleBatchKeyPress() {

        if (event.key === 'Escape') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('product0').value = '';
            // document.getElementById('unitId0').value = '';
            // document.getElementById('batchId0').value = '';
            // document.getElementById('storeId0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults()
            return

        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('sale_discount').select()
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults();
            return
        }
        const productElement = document.getElementById('batchInput');
        const query = productElement.value;


        let results = batches
            .filter(batch => batch.batchid.toLowerCase().includes(query));
        if (document.getElementById('batchInput').value == "") {
            results = batches
        }


        if (event.key === 'ArrowDown') {
            $("#batchId0").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItembatch(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("storeInput");
            element2.focus();
            document.getElementById('storeInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#batchId0").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItembatch(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('batchId0').value != "") {

                let element2 = document.getElementById("unitInput");
                element2.focus();
                element2.select();
                return



            }


            if (results.length > 0) {
                // if (document.getElementById('batchInput').value == "") {
                //     alert("Batch shouldn't be empty")
                //     return
                // }
                document.getElementById('batchInput').value = results[currentIndex].batchid;

                document.getElementById('batchInput').select()
                document.getElementById('batchId0').value = results[currentIndex].id;

                if (document.getElementById('defaultsaleprice' + 0).value == 'mrp') {
                    getMrpPrice(0);
                }

                if (document.getElementById('isstock0').value == 1) {

                    avStock(0, document.getElementById('productId0').value,
                        document.getElementById('storeId0').value, document.getElementById('batchId0').value, "", "")
                }
                clearResults();
            } else {
                document.getElementById('batchInput').value = "";
                document.getElementById('batchId0').value = "";
                alert("Batch shouldn't be empty")
                return
            }



        } else if (event.key === "Backspace") {
            clearResults()
            // document.getElementById('batchId0').value = "";
            if (document.getElementById('batchInput').value == "") {
                document.getElementById('batchId0').value = "";
                displayResultsBatch(batches, count);

            }

        } else {
            document.getElementById('batchId0').value = "";
            currentIndex = -1;
            displayResultsBatch(results, count);

        }

    }

    function handleUnitKeyPress() {
        if (event.key === 'Escape') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('product0').value = '';
            // document.getElementById('unitId0').value = '';
            // document.getElementById('batchId0').value = '';
            // document.getElementById('storeId0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults()
            return

        }
        if (event.key === "Shift") {
            event.preventDefault();
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('sale_discount').select()
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults();
            return
        }

        const productElement = document.getElementById('unitInput');
        const query = productElement.value;



        let results = units
            .filter(unit => unit.unit_name.toLowerCase().includes(query));
        if (document.getElementById('unitInput').value == "") {
            results = units
        }
        if (event.key === 'ArrowDown') {
            $("#unitId0").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemunit(currentIndex);
            }
        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("batchInput");
            element2.focus();
            document.getElementById('batchInput').select()

        } else if (event.key === 'ArrowUp') {
            $("#unitId0").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemunit(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {
            if (document.getElementById('unitId0').value != "") {


                let element2 = document.getElementById("qty0");
                element2.focus();
                element2.select();




            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {

                // if (document.getElementById('unitInput').value == "") {
                //     alert("Unit shouldn't be empty")
                //     return
                // }
                if (document.getElementById('unitId0').value != "") {


                    let element2 = document.getElementById("qty0");
                    element2.focus();
                    element2.select();
                    return




                }
                document.getElementById('unitInput').value = results[currentIndex].unit_name;

                document.getElementById('unitInput').select()
                document.getElementById('unitId0').value = results[currentIndex].unit_id;

                // avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)
                convertion(0, document.getElementById('productId0').value, document.getElementById('unitId0').value, document.getElementById('unitInput').value)

                clearResults();
            }



        } else if (event.key === 'ArrowLeft') {
            if (document.getElementById('unitId0').value != "") {


                let element2 = document.getElementById("qty0");
                element2.focus();
                element2.select();




            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {

                // if (document.getElementById('unitInput').value == "") {
                //     alert("Unit shouldn't be empty")
                //     return
                // }
                if (document.getElementById('unitId0').value != "") {


                    let element2 = document.getElementById("qty0");
                    element2.focus();
                    element2.select();
                    return




                }
                document.getElementById('unitInput').value = results[currentIndex].unit_name;

                document.getElementById('unitInput').select()
                document.getElementById('unitId0').value = results[currentIndex].unit_id;

                // avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)
                convertion(0, document.getElementById('productId0').value, document.getElementById('unitId0').value, document.getElementById('unitInput').value)

                clearResults();
            }



        } else if (event.key === "Backspace") {
            clearResults()

            if (document.getElementById('unitInput').value == "") {
                document.getElementById('unitId0').value = "";
                displayResultsUnit(units, count);

            }

        } else {
            document.getElementById('unitId0').value = ""
            currentIndex = -1;
            displayResultsUnit(results, count);

        }

    }


    function handlePaymentKeyPress() {

        const paymentElement = document.getElementById('paymentInput');
        const query = paymentElement.value;

        // incidentTypes = ['Sale', 'Wholesale']
        const results = pmethods
            .filter(pmethod => pmethod.name.toLowerCase().includes(query));

        if (event.key === 'Escape') {
            let element2 = document.getElementById("productInput");
            element2.focus();
            document.getElementById('product0').value = '';
            document.getElementById('unitId0').value = '';
            document.getElementById('batchId0').value = '';
            document.getElementById('storeId0').value = '';
            document.getElementById('storeInput').value = '';
            document.getElementById('batchInput').value = '';
            document.getElementById('unitInput').value = '';
            document.getElementById('code0').value = '';
            document.getElementById('codetype0').innerHTML = '';
            document.getElementById('qty0').value = '';
            document.getElementById('product_rate0').value = '';
            document.getElementById('discount0').value = '';
            document.getElementById('discount_value0').value = '';
            document.getElementById('vat_percent0').value = '';
            document.getElementById('vat_value0').value = '';
            document.getElementById('total_price0').value = '';
            clearResults()
            return

        }
        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItempayment(currentIndex);
            }

        } else if (event.key === 'ArrowLeft') {
            let element2 = document.getElementById("sale_discount");
            element2.focus();
            document.getElementById('sale_discount').select()

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItempayment(currentIndex);
            }
        } else if (event.key === 'Enter' || event.key === 'ArrowRight') {

            if (document.getElementById('paymentId').value != "") {
                save()
                return

            }
            // Select the highlighted item
            if (currentIndex >= 0 && currentIndex < results.length) {
                // if (document.getElementById('paymentInput').value == "") {
                //     alert("Payment shouldn't be empty")
                //     return
                // }
                // document.getElementById('product0').value = results[currentIndex].product_name;
                document.getElementById('paymentId').value = results[currentIndex].id;
                document.getElementById('paymentInput').value = results[currentIndex].name;
                document.getElementById('paymentInput').select()
                // document.getElementById('branchId').value = results[currentIndex].id;
                clearResults();
            }


        } else if (event.key === "Backspace") {
            clearResults()
            if (document.getElementById('paymentInput').value == "") {
                document.getElementById('paymentId').value = ''
                displayResultsPayment(pmethods, count);

            }
            // document.getElementById('branchId').value = "";
        } else {
            currentIndex = -1;
            displayResultsPayment(results, count);

        }

    }

    function calculate_sum(sl, type, e) {

        if (type != '') {
            if (e.key === 'Escape') {
                let element2 = document.getElementById("productInput");
                element2.focus();
                document.getElementById('product0').value = '';
                document.getElementById('unitId0').value = '';
                document.getElementById('batchId0').value = '';
                document.getElementById('storeId0').value = '';
                document.getElementById('storeInput').value = '';
                document.getElementById('batchInput').value = '';
                document.getElementById('unitInput').value = '';
                document.getElementById('code0').value = '';
                document.getElementById('codetype0').innerHTML = '';
                document.getElementById('qty0').value = '';
                document.getElementById('product_rate0').value = '';
                document.getElementById('discount0').value = '';
                document.getElementById('discount_value0').value = '';
                document.getElementById('vat_percent0').value = '';
                document.getElementById('vat_value0').value = '';
                document.getElementById('total_price0').value = '';
                clearResults()
                return

            }
            if (e.key === "Shift") {
                event.preventDefault();
                let element2 = document.getElementById("sale_discount");
                element2.focus();
                document.getElementById('sale_discount').select()
                document.getElementById('product0').value = '';
                document.getElementById('unitId0').value = '';
                document.getElementById('batchId0').value = '';
                document.getElementById('storeId0').value = '';
                document.getElementById('storeInput').value = '';
                document.getElementById('batchInput').value = '';
                document.getElementById('unitInput').value = '';
                document.getElementById('code0').value = '';
                document.getElementById('codetype0').innerHTML = '';
                document.getElementById('qty0').value = '';
                document.getElementById('product_rate0').value = '';
                document.getElementById('discount0').value = '';
                document.getElementById('discount_value0').value = '';
                document.getElementById('vat_percent0').value = '';
                document.getElementById('vat_value0').value = '';
                document.getElementById('total_price0').value = '';
                clearResults();
                return
            }

            if (e.key === 'Enter' || event.key === 'ArrowRight') {
                e.preventDefault();

                if (type == 'qty') {

                    if (document.getElementById("qty0").value == '') {
                        alert("Please enter qty")
                        return

                    }
                    let element2 = document.getElementById("product_rate0");
                    element2.focus();
                    element2.select();




                }

                if (type == 'sale_discount') {

                    // if (document.getElementById("qty0").value == '') {
                    //     alert("Please enter qty")
                    //     return

                    // }
                    let element2 = document.getElementById("paymentInput");
                    element2.focus();
                    element2.select();
                    return




                }


                if (type == 'product_rate') {

                    if (document.getElementById("product_rate0").value == '') {
                        alert("Please enter Price")
                        return

                    }

                    let element2 = document.getElementById("discount0");
                    element2.focus();
                    element2.select();
                }

                if (type == 'discount') {


                    // if (vtinfo.ischecked == 1) {
                    //     let element2 = document.getElementById("vat_percent0");
                    //     element2.focus();
                    //     element2.select();
                    // } else {
                    if (document.getElementById("qty0").value == '') {
                        alert("Please enter qty")
                        return

                    }


                    if (document.getElementById("product_rate0").value == '') {
                        alert("Please enter Price")
                        return

                    }

                    addInputField('addinvoiceItem')
                    // }
                }


                // if (vtinfo.ischecked == 1) {

                //     if (type == 'vatpercent') {
                //         if (document.getElementById("qty0").value == '') {
                //             alert("Please enter qty")
                //             return

                //         }


                //         if (document.getElementById("product_rate0").value == '') {
                //             alert("Please enter Price")
                //             return

                //         }

                //         addInputField('addinvoiceItem')

                //     }

                // }


            }

            if (e.key === 'ArrowLeft') {
                if (type == 'qty') {
                    let element2 = document.getElementById("unitInput");
                    element2.focus();
                    element2.select();
                }
                if (type == 'product_rate') {

                    let element2 = document.getElementById("qty0");
                    element2.focus();
                    element2.select();
                }
                if (type == 'discount') {

                    let element2 = document.getElementById("product_rate0");
                    element2.focus();
                    element2.select();
                }

                // if (vtinfo.ischecked == 1) {

                //     if (type == 'vatpercent') {

                //         let element2 = document.getElementById("discount0");
                //         element2.focus();
                //         element2.select();
                //     }

                // }
            }


            if (event.key === 'Escape') {
                let element2 = document.getElementById("sale_discount");
                element2.focus();
                document.getElementById('sale_discount').select()
                document.getElementById('product0').value = '';
                document.getElementById('storeInput').value = '';
                document.getElementById('unit0').value = '';
                document.getElementById('code0').value = '';
                document.getElementById('qty0').value = '';
                document.getElementById('product_rate0').value = '';
                document.getElementById('discount0').value = '';
                document.getElementById('discount_value0').value = '';
                document.getElementById('vat_percent0').value = '';
                document.getElementById('vat_value0').value = '';
                document.getElementById('total_price0').value = '';


            }

        }


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

        var vat_percent = 0;
        if (document.getElementById('invoiceType').value == 'cash_vat' ||
            document.getElementById('invoiceType').value == 'credit_vat' ||
            document.getElementById('invoiceType').value == 'svat') {
            vat_percent = $("#vat_percent" + sl).val();



        }
        let value = $("#code" + sl).val();
        let number = parseInt(value.replace(/,/g, ''), 10);


        // if (parseInt(quantity) > parseInt(number)) {
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

        let tprice = 0;
        let tdis = 0;
        let tvat = 0;



        for (let i = 2; i < count; i++) {

            if (document.getElementById('myRow' + i).style.display != "none") {
                tprice = tprice + parseFloat(document.getElementById('total_price' + i).innerHTML);
                tdis = tdis + parseFloat(document.getElementById('discountval' + i).innerHTML);

                if (vtinfo.ischecked == 1) {
                    tvat = tvat + parseFloat(document.getElementById('vat_value' + i).innerHTML);
                }



            }
        }

        var grandtotal = tprice;


        if ($("#sale_discount").val() != "") {
            tdis = tdis + parseFloat(document.getElementById('sale_discount').value);
            grandtotal = tprice - parseFloat(document.getElementById('sale_discount').value);
        }



        // $("").val(tprice.toFixed(2, 2));

        document.getElementById('Total').innerHTML = tprice.toFixed(2, 2)


        // $("#total_discount_ammount").val(tdis.toFixed(2, 2));

        document.getElementById('total_discount_ammount').innerHTML = tdis.toFixed(2, 2)


        if (vtinfo.ischecked == 1) {
            document.getElementById('total_vat_amnt').innerHTML = tvat.toFixed(2, 2)

            grandtotal = parseFloat(grandtotal) + parseFloat(tvat);
        }

        document.getElementById('grandTotal').innerHTML = grandtotal.toFixed(2, 2)


        var purchase_edit_page = $("#purchase_edit_page").val();
        $("#add_new_payment").empty();

        $("#pay-amount").text('0');


    }

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

                    document.getElementById('conversiontype0').value = datas[0].convertiontype
                    document.getElementById('conversionid0').value = datas[0].conversionratio_id
                    document.getElementById('conversion_ratio0').value = datas[0].conversion_ratio;
                    document.getElementById('product_rate0').value = datas[0].subsell_price;

                    if (document.getElementById('defaultsaleprice' + item).value == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = datas[0].subsell_price;
                    }

                    if (document.getElementById('defaultsaleprice' + item).value == 'mrp') {
                        var _mrpV = document.getElementById('mrpprice' + item).value;
                        if (document.getElementById('mrpprice' + item).value) {
                            document.getElementById('product_rate' + item).value = (_mrpV / datas[0].conversion_ratio).toFixed(2);
                        } else {
                            document.getElementById('product_rate' + item).value = datas[0].subsell_price;
                        }
                    }

                    if (document.getElementById('isstock0').value == 1) {

                        avStock(item, document.getElementById('productId0').value, document.getElementById('storeId0').value, document.getElementById('batchId0').value,
                            datas[0].convertiontype, datas[0].conversion_ratio)
                    }
                } else {
                    // alert("Conversion not found")
                    if (document.getElementById('defaultsaleprice' + item).value == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                    }
                    if (document.getElementById('defaultsaleprice' + item).value == 'mrp') {
                        if (document.getElementById('mrpprice' + item).value) {
                            document.getElementById('product_rate' + item).value = document.getElementById('mrpprice' + item).value;
                        } else {
                            document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                        }
                    }
                    getActiveSubUnit(document.getElementById('productId0').value, item)
                    if (document.getElementById('isstock0').value == 1) {
                        avStock(item, document.getElementById('productId0').value, document.getElementById('storeId0').value, document.getElementById("batchId0").value, "", "")
                    }
                    // document.getElementById('product_rate0').value = document.getElementById('mastercost_price0').value;
                    // document.getElementById('product_rate0').value = document.getElementById('mastercost_price0').value;

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


    function product_search2(item, name, id) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('code' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('code' + item).value = "";
            document.getElementById('qty' + item).value = "";
            // getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: id,
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: id,
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
                            document.getElementById('isstock' + item).value = product[0].stock
                            document.getElementById('code' + item).value = "";
                            document.getElementById('qty' + item).value = "";
                            document.getElementById('codetype' + item).innerHTML = "";

                            if (document.getElementById('isstock0').value == 1) {

                                avStock(item, document.getElementById('productId' + item).value, product[0].store, 1, "", "")
                            } //avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")

                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                    // document.getElementById('vat_percent' + item).value = "";
                    getBatchDropdown(item, 0, id, product[0].batchtype);
                    includedStroes = [];
                    if (product[0].store == 1) {
                        includedStroes = stores;
                        includedStroes.push({
                            id: 1,
                            name: 'N/A'
                        })
                        document.getElementById('storeInput').value = 'N/A';
                        document.getElementById('storeInput').select()
                        document.getElementById('storeId0').value = 1;

                    } else {
                        includedStroes = stores;
                        const matchedStore = stores.find(store => store.id.toLowerCase().includes(product[0].store.toLowerCase()));
                        document.getElementById('storeInput').value = matchedStore ? matchedStore.name : '';
                        document.getElementById('storeInput').select()
                        document.getElementById('storeId0').value = product[0].store;

                    }





                    let element2 = document.getElementById("storeInput");
                    element2.focus();

                    //avStock2(item, id, product[0].store);

                    document.getElementById('defaultsaleprice' + item).value = product[0].defaultsaleprice;
                    document.getElementById('mastercost_price' + item).value = product[0].price;
                    document.getElementById('mrpprice' + item).value = "";

                    if (product[0].defaultsaleprice == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = product[0].price;

                    }

                    if (product[0].defaultsaleprice == 'custom') {
                        document.getElementById('product_rate' + item).value = 0;

                    }


                    if (product[0].defaultsaleprice == 'mrp') {
                        document.getElementById('product_rate' + item).value = product[0].price;
                        // setTimeout(
                        // getMrpPrice(item),1000)

                    }
                    getActiveSubUnit(id, item)
                    // if (vtinfo.ischecked == 1) {
                    document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    // }
                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


        if (name === "store") {


            avStock2(item, id)
        }
    }

    let units = []

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
                //    var $subunitDropdown = $('#unit' + item);
                document.getElementById('conversionid' + item).value = "";
                document.getElementById('conversiontype' + item).value = "";
                document.getElementById('conversion_ratio' + item).value = "";

                document.getElementById('unitInput').value = datas[0].name2;
                document.getElementById('unitId0').value = datas[0].unit;
                units = datas;
                units.push({
                    "unit_id": datas[0].unit,
                    "unit_name": datas[0].name2
                })



            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function avStock2(item, id, storeId) {
        document.getElementById('code' + item).value = "";
        document.getElementById('qty' + item).value = "";
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/avg_avstock',
            type: 'POST',
            data: {
                prodid: id,
                storeid: storeId,
                type2: type2
            },
            success: function(response) {
                let stock = JSON.parse(response);


                document.getElementById('code' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;


            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function getMrpPrice(item) {
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchPrice',
            type: 'POST',
            data: {
                product: document.getElementById('productId' + item).value.toString(),
                batch: document.getElementById('batchId' + item).value.toString(),

            },
            success: function(response2) {
                if (response2 != "") {
                    let product2 = JSON.parse(response2);
                    document.getElementById('mrpprice' + item).value = product2[0].mrp;
                    document.getElementById('product_rate' + item).value = product2[0].mrp;

                } else {
                    document.getElementById('mrpprice' + item).value = "";
                }

            },
            error: function(error) {
                console.log(error)
            }
        })
    }


    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
        calculate_sum(count, '')
    }






    function displayResultsCustomer(results, count) {
        const searchResultsDiv = document.getElementById('customerResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.customer_name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {
                    document.getElementById('customerInput').value = item.customer_name + " ";

                    document.getElementById('customerInput').select()
                    document.getElementById('customerId').value = item.customer_id;




                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemcustomer(0);

    }

    function displayResultsEmployee(results, count) {
        const searchResultsDiv = document.getElementById('employeeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.first_name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {
                    document.getElementById('employeeInput').value = item.first_name;

                    document.getElementById('employeeInput').select()
                    document.getElementById('employeeId').value = item.id;




                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItememployee(0);

    }


    function displayResultsIncidentType(results, count) {
        const searchResultsDiv = document.getElementById('incidenttypeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {



                    document.getElementById('incidenttypeInput').value = item;

                    document.getElementById('incidenttypeInput').select()

                    if (results[currentIndex] == "Retail") {
                        document.getElementById('incidenttype').value = 1;
                    } else if (results[currentIndex] == "") {
                        document.getElementById('incidenttype').value = 2;
                    }
                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemIncidenttype(0);

    }


    function displayResultsInvoiceType(results, count) {
        const searchResultsDiv = document.getElementById('invoicetypeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {



                    document.getElementById('invoicetypeInput').value = item;

                    document.getElementById('invoicetypeInput').select()
                    if (item == "Cash") {
                        document.getElementById('invoiceType').value = 'cash';
                    } else if (item == "Credit") {
                        document.getElementById('invoiceType').value = 'credit';
                    } else if (item == "Cash VAT") {
                        document.getElementById('invoiceType').value = 'cash_vat';
                    } else if (item == "Credit VAT") {
                        document.getElementById('invoiceType').value = 'credit_vat';
                    }
                    if (document.getElementById('invoiceType').value == 'cash_vat' ||
                        document.getElementById('invoiceType').value == 'credit_vat' ||
                        document.getElementById('invoiceType').value == 'svat') {
                        document.querySelectorAll('.vathidden').forEach(el => {
                            el.style.display = 'table-cell';
                        });
                        document.querySelectorAll('.vatshow').forEach(el => {
                            el.style.display = 'none';
                        });
                    }
                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemInvoicetype(0);

    }


    function displayResultsBranch(results, count) {
        const searchResultsDiv = document.getElementById('branchResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {


                    getBranchNature(item.id)

                    document.getElementById('branchInput').value = item.name;

                    document.getElementById('branchInput').select()
                    document.getElementById('branchId').value = item.id;
                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemBranch(0);

    }




    function displayResultsProduct(results, count) {
        const searchResultsDiv = document.getElementById('productResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.product_name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {


                    document.getElementById('product0').value = item.product_name;
                    document.getElementById('productId0').value = item.id;
                    document.getElementById('productInput').value = "";

                    // document.getElementById('branchId').value = results[currentIndex].id;
                    product_search2(0, "product", item.id)
                    clearResults();

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function displayResultsPayment(results, count) {
        const searchResultsDiv = document.getElementById('paymentResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {

            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {



                    document.getElementById('paymentId').value = item.id;
                    document.getElementById('paymentInput').value = item.name;
                    document.getElementById('paymentInput').select()

                    // avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)

                    clearResults();
                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItempayment(0);

    }

    function displayResultsStore(results, count) {
        const searchResultsDiv = document.getElementById('storeResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {


                    document.getElementById('storeInput').value = item.name;

                    document.getElementById('storeInput').select()
                    document.getElementById('storeId0').value = item.id;
                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function displayResultsBatch(results, count) {
        const searchResultsDiv = document.getElementById('batchResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.batchid;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {


                    document.getElementById('batchInput').value = item.batchid;

                    document.getElementById('batchInput').select()
                    document.getElementById('batchId0').value = item.id;

                    // if (document.getElementById('defaultsaleprice' + 0).value == 'mrp') {
                    //     getMrpPrice(0);
                    // }
                    if (document.getElementById('defaultsaleprice0').value == 'mrp') {
                        document.getElementById('product_rate0').value = document.getElementById('mastercost_price0').value;
                        getMrpPrice(0);
                    }
                    if (document.getElementById('isstock0').value == 0) {

                        avStock(0, document.getElementById('productId0').value,
                            document.getElementById('storeId0').value, document.getElementById('batchId0').value, "", "")
                    }
                    clearResults();
                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function displayResultsUnit(results, count) {
        const searchResultsDiv = document.getElementById('unitResults1');
        searchResultsDiv.innerHTML = ''; // Clear previous results
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<p>No results found</p>';

        } else {
            // results.forEach((item, index) => {
            //     const resultItem = document.createElement('div');
            //     resultItem.classList.add('resultItem');
            //     resultItem.textContent = item.unit_name;
            //     resultItem.setAttribute('data-index', index);
            //     searchResultsDiv.appendChild(resultItem);
            // });

            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.unit_name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {

                    document.getElementById('unitInput').value = item.unit_name;

                    document.getElementById('unitInput').select()
                    document.getElementById('unitId0').value = item.unit_id;

                    // avStock2(0, document.getElementById('productId0').value, results[currentIndex].id)
                    convertion(0, document.getElementById('productId0').value, document.getElementById('unitId0').value, document.getElementById('unitInput').value)

                    clearResults();
                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }




    function highlightItemcustomer(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItememployee(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }


    function highlightItemIncidenttype(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }


    function highlightItemInvoicetype(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemBranch(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemproduct(index) {
        const items = document.querySelectorAll('.resultItem');

        items.forEach((item, idx) => {
            if (idx === index) {

                item.classList.add('highlight');

                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });

            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemstore(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItembatch(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItemunit(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function highlightItempayment(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function clearResults() {
        // Clear the results div
        document.getElementById('customerResults1').innerHTML = '';
        document.getElementById('employeeResults1').innerHTML = '';
        document.getElementById('incidenttypeResults1').innerHTML = '';
        document.getElementById('productResults1').innerHTML = '';
        document.getElementById('branchResults1').innerHTML = '';
        document.getElementById('storeResults1').innerHTML = '';
        document.getElementById('paymentResults1').innerHTML = '';
        document.getElementById('batchResults1').innerHTML = '';
        document.getElementById('unitResults1').innerHTML = '';
        document.getElementById('invoicetypeResults1').innerHTML = '';


    }


    $(document).ready(function() {
        $('body').addClass("sidebar-mini sidebar-collapse");
        getBranchDropdown(0);
        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }
        // customers.push({
        //     customer_id: "0",
        //     customer_name: "Cash"
        // })

        employees.push({
            "id": 1,
            "first_name": "N/A"
        })

        document.querySelectorAll('.vathidden').forEach(el => {
            el.style.display = 'none';
        });

        let element2 = document.getElementById("branchInput");
        element2.focus();
        document.getElementById('branchInput').select()

        document.getElementById('invoicetypeInput').value = "Cash";
        document.getElementById('invoiceType').value = "cash";

        document.getElementById('paymentId').value = 1;
        document.getElementById('paymentInput').value = "Cash";

        document.getElementById('customerId').value = 1;
        document.getElementById('customerInput').value = "Cash";

        document.getElementById('employeeId').value = 1;
        document.getElementById('employeeInput').value = "N/A";

        getDefaultBranch();


        // if (id != null) {
        //     $.ajax({
        //         type: "post",
        //         url: $('#base_url').val() + "store/store/getbranchbyuserid",
        //         data: {
        //             // is_credit_edit: is_credit_edit,
        //             // csrf_test_name: csrf_test_name
        //         },
        //         success: function(data) {
        //             branches = JSON.parse(data);

        //             $.ajax({
        //                 url: $('#base_url').val() + 'invoice/invoice/getSaleById',
        //                 type: 'POST',
        //                 data: {
        //                     id: id,
        //                     type2: type2
        //                 },
        //                 success: function(response) {
        //                     var sales = JSON.parse(response);
        //                     document.getElementById('date').value = sales[0].date;
        //                     document.getElementById('details').value = sales[0].details;


        //                     console.log(customers)

        //                     document.getElementById('customerInput').value = customers.find(customer => customer.customer_id == sales[0].customer_id).customer_name;
        //                     document.getElementById('customerId').value = sales[0].customer_id

        //                     if (sales[0].employee_id != "0" && sales[0].employee_id) {
        //                         document.getElementById('employeeInput').value = employees.find(employee => employee.id == sales[0].employee_id).first_name + " " + employees.find(employee => employee.id == sales[0].employee_id).last_name;
        //                         document.getElementById('employeeId').value = sales[0].employee_id
        //                     }


        //                     document.getElementById('branchInput').value = branches.find(branch => branch.id == sales[0].branch).name;
        //                     document.getElementById('branchId').value = sales[0].branch

        //                     if (sales[0].incidenttype == 1) {
        //                         document.getElementById('incidenttypeInput').value = "Retail";
        //                     } else {
        //                         document.getElementById('incidenttypeInput').value = "Wholesale";
        //                     }
        //                     document.getElementById('incidenttype').value = sales[0].incidenttype

        //                     document.getElementById('paymentInput').value = pmethods.find(branch => branch.id == sales[0].payment_type).name;
        //                     document.getElementById('paymentId').value = sales[0].payment_type
        //                     document.getElementById('paymentInput').focus()
        //                     document.getElementById('paymentInput').select()



        //                     document.getElementById('total_discount_ammount').innerHTML = sales[0].total_discount_ammount;
        //                     document.getElementById('total_vat_amnt').innerHTML = sales[0].total_vat_amnt;
        //                     document.getElementById('grandTotal').innerHTML = sales[0].grandTotal;
        //                     document.getElementById('Total').innerHTML = sales[0].total;
        //                     document.getElementById('sale_discount').value = sales[0].discount;

        //                     count = 2;
        //                     for (let i = 0; i < sales.length; i++) {
        //                         document.getElementById('myRow' + count).style.display = 'table-row';

        //                         document.getElementById('product' + count).innerHTML = products.find(product => product.id == sales[i].product).product_name
        //                         document.getElementById('store' + count).innerHTML = stores.find(store => store.id == sales[i].store).name
        //                         document.getElementById('productId' + count).value = sales[i].product
        //                         document.getElementById('storeId' + count).value = sales[i].store
        //                         document.getElementById('unitId' + count).value = sales[i].unit
        //                         document.getElementById('batchId' + count).value = sales[i].batch
        //                         document.getElementById('batch' + count).innerHTML = batches.find(batch => batch.id == sales[i].batch).batchid;
        //                         document.getElementById('qty' + count).innerHTML = sales[i].quantity
        //                         document.getElementById('product_rate' + count).innerHTML = parseFloat(sales[i].product_rate).toFixed(2)
        //                         document.getElementById('unit' + count).innerHTML = units2.find(unit => unit.unit_id == sales[i].unit).unit_name;

        //                         document.getElementById('discount' + count).innerHTML = sales[i].discount2 ? sales[i].discount2 : 0
        //                         document.getElementById('discountval' + count).innerHTML = sales[i].discount_value ? parseFloat(sales[i].discount_value).toFixed(2) : 0.00

        //                         if (vtinfo.ischecked == 1) {
        //                             document.getElementById('vat_percent' + count).value = sales[i].vat_percent ? sales[i].vat_percent : 0
        //                             document.getElementById('vat_value' + count).innerHTML = sales[i].vat_value ? parseFloat(sales[i].vat_value).toFixed(2) : 0.00
        //                         }

        //                         document.getElementById('total_price' + count).innerHTML = sales[i].total_price ? parseFloat(sales[i].total_price).toFixed(2) : 0.00



        //                         count = count + 1;
        //                     }
        //                 },
        //                 error: function(error) {
        //                     console.log(error);
        //                 }
        //             });


        //         }
        //     });

        // } else {
        //     getBranchDropdown(0);

        // }
    });

    function getDefaultBranch() {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getbranchbyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var branches = JSON.parse(data)
                $.each(branches, function(index, branch) {
                    if (branch.default != 0) {
                        document.getElementById('branchInput').value = branch.name;
                        document.getElementById('branchId').value = branch.id;
                        getBranchNature(branch.id)
                    }
                });

            }
        });
    }


    function getBranchNature(branchid) {
        $.ajax({
            type: "post",
            url: $('#base_url').val() + "invoice/invoice/getBranchNature",
            data: {
                branch: branchid
            },
            success: function(data) {

                var branch = JSON.parse(data);


                if (branch[0].nature == "Retail") {
                    document.getElementById('incidenttypeInput').value = "Retail";
                    document.getElementById('incidenttype').value = 1

                } else {
                    document.getElementById('incidenttypeInput').value = "Wholesale";
                    document.getElementById('incidenttype').value = 2
                }
            }
        });
    }

    function addInputField(t) {
        // if (count < 11) {
        // getActiveStore(0, count);
        // getActiveProduct(0, count)

        for (let i = 2; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                document.getElementById('discount0').value = document.getElementById('discount0').value == "" ? 0 : document.getElementById('discount0').value
                document.getElementById('product_rate0').value = parseFloat(document.getElementById('product_rate0').value).toFixed(2)
                console.log(document.getElementById('product_rate0').value + "a" + document.getElementById('product_rate' + i).innerHTML)
                if (document.getElementById('productId0').value == document.getElementById('productId' + i).value &&
                    document.getElementById('storeId0').value == document.getElementById('storeId' + i).value &&
                    document.getElementById('batchId0').value == document.getElementById('batchId' + i).value &&
                    document.getElementById('unitId0').value == document.getElementById('unitId' + i).value &&
                    document.getElementById('discount0').value == document.getElementById('discount' + i).innerHTML &&
                    document.getElementById('product_rate0').value == document.getElementById('product_rate' + i).innerHTML
                ) {

                    //console.log("Thayaan")
                    // document.getElementById('total_price' + i).innerHTML = document.getElementById('total_price0').value ? parseFloat(document.getElementById('total_price0').value).toFixed(2) : 0.00
                    // document.getElementById('discountval' + i).innerHTML = document.getElementById('discount_value0').value ? parseFloat(document.getElementById('discount_value0').value).toFixed(2) : 0.00
                    let totalqty = parseInt(document.getElementById('qty0').value) + parseInt(document.getElementById('qty' + i).innerHTML);
                    let discountval = parseFloat(document.getElementById('discount_value0').value) + parseFloat(document.getElementById('discountval' + i).innerHTML);
                    let totalPrice = parseFloat(document.getElementById('total_price0').value) + parseFloat(document.getElementById('total_price' + i).innerHTML)
                    let totalVat = parseFloat(document.getElementById('vat_value0').value) + parseFloat(document.getElementById('vat_value' + i).innerHTML)


                    document.getElementById('qty' + i).innerHTML = totalqty
                    document.getElementById('discountval' + i).innerHTML = discountval.toFixed(2)
                    document.getElementById('total_price' + i).innerHTML = totalPrice.toFixed(2)
                    document.getElementById('vat_value' + i).innerHTML = totalVat.toFixed(2)



                    document.getElementById('product0').value = '';
                    document.getElementById('unitId0').value = '';
                    document.getElementById('batchId0').value = '';
                    document.getElementById('storeId0').value = '';

                    document.getElementById('storeInput').value = '';
                    document.getElementById('batchInput').value = '';
                    document.getElementById('unitInput').value = '';



                    document.getElementById('code0').value = '';
                    document.getElementById('codetype0').innerHTML = '';
                    document.getElementById('qty0').value = '';
                    document.getElementById('product_rate0').value = '';
                    document.getElementById('discount0').value = '';
                    document.getElementById('discount_value0').value = '';
                    document.getElementById('vat_percent0').value = '';
                    document.getElementById('vat_value0').value = '';
                    document.getElementById('total_price0').value = '';

                    let element2 = document.getElementById("productInput");
                    element2.focus();
                    return

                }


            }

        }
        document.getElementById('myRow' + count).style.display = 'table-row';



        document.getElementById('productId' + count).value = document.getElementById('productId0').value
        document.getElementById('storeId' + count).value = document.getElementById('storeId0').value
        document.getElementById('batchId' + count).value = document.getElementById('batchId0').value
        document.getElementById('unitId' + count).value = document.getElementById('unitId0').value
        document.getElementById('product' + count).innerHTML = document.getElementById('product0').value
        document.getElementById('store' + count).innerHTML = document.getElementById('storeInput').value
        document.getElementById('batch' + count).innerHTML = document.getElementById('batchInput').value
        document.getElementById('unit' + count).innerHTML = document.getElementById('unitInput').value

        document.getElementById('mconversion_ratio' + count).value = document.getElementById('mconversion_ratio0').value
        document.getElementById('mastercost_price' + count).value = document.getElementById('mastercost_price0').value
        document.getElementById('bd' + count).value = document.getElementById('bd0').value
        document.getElementById('ad' + count).value = document.getElementById('ad0').value
        document.getElementById('conversionid' + count).value = document.getElementById('conversionid0').value
        document.getElementById('conversiontype' + count).value = document.getElementById('conversiontype0').value
        document.getElementById('conversion_ratio' + count).value = document.getElementById('conversion_ratio0').value
        console.log()
        document.getElementById('qty' + count).innerHTML = document.getElementById('qty0').value
        document.getElementById('product_rate' + count).innerHTML = parseFloat(document.getElementById('product_rate0').value).toFixed(2)

        document.getElementById('discount' + count).innerHTML = document.getElementById('discount0').value ? document.getElementById('discount0').value : 0
        document.getElementById('discountval' + count).innerHTML = document.getElementById('discount_value0').value ? parseFloat(document.getElementById('discount_value0').value).toFixed(2) : 0.00

        document.getElementById('vat_percent' + count).value = document.getElementById('vat_percent0').value ? document.getElementById('vat_percent0').value : 0
        document.getElementById('vat_value' + count).innerHTML = document.getElementById('vat_value0').value ? parseFloat(document.getElementById('vat_value0').value).toFixed(2) : 0.00
        document.getElementById('total_price' + count).innerHTML = document.getElementById('total_price0').value ? parseFloat(document.getElementById('total_price0').value).toFixed(2) : 0.00
        document.getElementById('isstock' + count).value = document.getElementById('isstock0').value
        count = count + 1;

        document.getElementById('product0').value = '';
        document.getElementById('unitId0').value = '';
        document.getElementById('batchId0').value = '';
        document.getElementById('storeId0').value = '';

        document.getElementById('storeInput').value = '';
        document.getElementById('batchInput').value = '';
        document.getElementById('unitInput').value = '';



        document.getElementById('code0').value = '';
        document.getElementById('codetype0').innerHTML = '';
        document.getElementById('qty0').value = '';
        document.getElementById('product_rate0').value = '';
        document.getElementById('discount0').value = '';
        document.getElementById('discount_value0').value = '';
        document.getElementById('vat_percent0').value = '';
        document.getElementById('vat_value0').value = '';
        document.getElementById('total_price0').value = '';

        let element2 = document.getElementById("productInput");
        element2.focus();
        // calculate_sum(0, '')


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
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('code' + item).value = "";
            document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);

                    getActiveStore(product[0].store, item);
                    avStock(item);

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('product_rate' + item).value = product[0].price;

                    if (vtinfo.ischecked == 1) {
                        document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    }
                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }
    }

    function getBatchDropdown(item, value, product, batchtype) {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchInStockByProductAndBatchtype',
            type: 'POST',
            data: {
                product: product,
                batchtype: batchtype
            },
            success: function(response2) {
                batches = [];

                if (response2 != "not") {
                    batches = JSON.parse(response2);



                    let defaultBatch = batches.find(b => b.id == 1);
                    if (defaultBatch) {
                        document.getElementById('batchInput').value = "Default";
                        // document.getElementById('batchInput').select();
                        document.getElementById('batchId0').value = defaultBatch.id;
                    }



                }

            },
            error: function(error) {
                console.log(error)
            }
        });




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
                //let select = document.getElementById('unit' + item);
                let selectedText = document.getElementById("unitInput").value;


                if (convertiontype == "*") {
                    document.getElementById('code' + item).value = (stock[0].avgqty * conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty * conversion_ratio;
                    let sub2 = Math.floor((sub).toLocaleString());
                    if (isNaN(sub2)) {
                        sub = Number(sub).toFixed(6);
                        el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText
                    } else {
                        el.innerHTML = sub2 + " " + selectedText

                    }
                } else if (convertiontype == "/") {
                    document.getElementById('code' + item).value = (stock[0].avgqty / conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty / conversion_ratio)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "+") {
                    document.getElementById('code' + item).value = (stock[0].avgqty + conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty + conversion_ratio)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "-") {
                    document.getElementById('code' + item).value = (stock[0].avgqty - conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty - conversion_ratio)).toLocaleString() + " " + selectedText

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



    function getActiveProduct(productId, item) {
        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

        $.each(products, function(index, product) {
            $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
        });

        if (productId > 0) {
            {
                $productDropdown.val(productId)
            }
        }
    }




    function getActiveStore(storeId, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
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
                branches = JSON.parse(data);
                console.log(branches)
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id)
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


    let _posSaving = false;

    function save() {
        if (_posSaving) return;

        if (confirm("Are you sure you want to save this record?")) {
            if (_posSaving) return;
            _posSaving = true;
            document.getElementById('save_add').disabled = true;
            arrItem = [];
            if (document.getElementById('customerId').value == "") {
                alert("Customer shouldn't be empty")
                let element2 = document.getElementById("customerInput");
                element2.focus();
                element2.select()
                _posReleaseLock(); return
            } else if (document.getElementById('paymentId').value == "") {
                alert("Payment shouldn't be empty")
                let element2 = document.getElementById("paymentInput");
                element2.focus();
                element2.select()
                _posReleaseLock(); return
            } else if (document.getElementById('branchId').value == "") {
                alert("Branch shouldn't be empty")
                let element2 = document.getElementById("branchInput");
                element2.focus();
                element2.select()
                _posReleaseLock(); return
            } else if (document.getElementById('incidenttype').value == "") {
                alert("Incident Type shouldn't be empty")
                let element2 = document.getElementById("incidenttypeInput");
                element2.focus();
                element2.select()
                _posReleaseLock(); return
            } else if (document.getElementById('date').value == "") {
                alert("Date shouldn't be empty")
                let element2 = document.getElementById("date");
                element2.focus();
                element2.select()
                _posReleaseLock(); return
            }
            for (let i = 2; i < count; i++) {
                if (document.getElementById('myRow' + i).style.display != "none") {
                    // var dropdown = document.getElementById('productId' + i);
                    let qty = 0;
                    if (document.getElementById('conversiontype' + i).value == "+") {
                        qty = document.getElementById('qty' + i).innerHTML - document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "-") {
                        qty = document.getElementById('qty' + i).innerHTML + document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "*") {
                        qty = document.getElementById('qty' + i).innerHTML / document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "/") {
                        qty = document.getElementById('qty' + i).innerHTML * document.getElementById('conversion_ratio' + i).value
                    } else {
                        qty = document.getElementById('qty' + i).innerHTML
                    }

                    let aqty = "";
                    const qtyEl = document.getElementById('qty' + i);
                    const qtyText = qtyEl.innerHTML.trim();
                    const qtyNum = parseFloat(qtyText);
                    if (qtyNum < 0) {
                        const positive = qtyText.replace('-', '');
                        const unit = units.find(u => u.unit_id == document.getElementById('unit' + i).value);
                        aqty = '+' + positive + ' ' + (unit ? unit.unit_name : '');
                    } else {
                        const unit = units2.find(u => u.unit_id == document.getElementById('unitId' + i).value);
                        aqty = '-' + qtyText + ' ' + (unit ? unit.unit_name : '');
                    }

                    arrItem.push({
                        product: document.getElementById('productId' + i).value,
                        product_name: document.getElementById('product' + i).innerHTML,
                        store: document.getElementById('storeId' + i).value,
                        batch: document.getElementById('batchId' + i).value,
                        quantity: qty,
                        product_rate: document.getElementById('product_rate' + i).innerHTML,
                        discount: document.getElementById('discount' + i).innerHTML,
                        discount_value: document.getElementById('discountval' + i).innerHTML,
                        vat_percent: document.getElementById('vat_percent' + i).value,
                        vat_value: document.getElementById('vat_value' + i).innerHTML,
                        total_price: document.getElementById('total_price' + i).innerHTML,
                        total_discount: 0,
                        all_discount: 0,
                        unit: document.getElementById('unitId' + i).value,
                        conversionid: document.getElementById('conversionid' + i).value,
                        isstock: document.getElementById('isstock' + i).value,
                        aqty: aqty,
                    });

                }

            }

            if (arrItem.length == 0) {
                alert("There is no any data")
                _posReleaseLock(); return
            }

            // console.log(arrItem)

            // var paymentdropdown = document.getElementById('your_dropdown_id');
            // $("#save_add").hide();

            if (id > 0) {
                $.ajax({
                    url: $('#base_url').val() + 'invoice/invoice/update_sale',
                    type: 'POST',
                    data: {
                        id: id,
                        items: arrItem,
                        discount: document.getElementById('sale_discount').value,
                        type2: type2,
                        total_discount_ammount: document.getElementById('total_discount_ammount').innerHTML,
                        total_vat_amnt: document.getElementById('total_vat_amnt').innerHTML,
                        grandTotal: document.getElementById('grandTotal').innerHTML,
                        date: document.getElementById('date').value,
                        details: document.getElementById('details').value,
                        total: document.getElementById('Total').innerHTML,
                        customer_id: document.getElementById('customerId').value,
                        employee_id: document.getElementById('employeeId').value,
                        payment_type: document.getElementById('paymentId').value,
                        payment: document.getElementById('paymentId').value,
                        incidenttype: document.getElementById('incidenttype').value,
                        branch: document.getElementById('branchId').value,
                        due_date: document.getElementById('pos_due_date').value

                    },
                    success: function(response) {
                        try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                        if (datas.success === false) {
                            _posReleaseLock();
                            alert("Error: " + (datas.error || "Update failed. Please try again."));
                            return;
                        }
                        pusherNotify('updated');
                        alert("Invoice Details Updated Successfully")
                        if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                        printRawHtml(datas.details, datas.invoiceno);
                    },
                    error: function(error) {
                        _posReleaseLock();
                        alert("Error: Update failed. Please check your inputs and try again.");
                        console.log(error);
                    }
                });


            } else {

                $.ajax({
                    url: $('#base_url').val() + 'invoice/invoice/save_sale',
                    type: 'POST',
                    data: {
                        items: arrItem,
                        type2: type2,
                        discount: document.getElementById('sale_discount').value,
                        total_discount_ammount: document.getElementById('total_discount_ammount').innerHTML,
                        total_vat_amnt: document.getElementById('total_vat_amnt').innerHTML,
                        grandTotal: document.getElementById('grandTotal').innerHTML,
                        date: document.getElementById('date').value,
                        details: document.getElementById('details').value,
                        total: document.getElementById('Total').innerHTML,
                        customer_id: document.getElementById('customerId').value,
                        payment_type: document.getElementById('paymentId').value,
                        payment: document.getElementById('paymentInput').innerHTML,
                        employee_id: document.getElementById('employeeId').value,
                        incidenttype: document.getElementById('incidenttype').value,
                        branch: document.getElementById('branchId').value,
                        invoicetype: document.getElementById('invoiceType').value,
                        due_date: document.getElementById('pos_due_date').value
                    },
                    success: function(response) {
                        try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                        if (datas.success === false) {
                            _posReleaseLock();
                            alert("Error: " + (datas.error || "Save failed. Please try again."));
                            return;
                        }
                        pusherNotify('created');
                        alert("Invoice Details saved Successfully")
                        if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                        printRawHtml(datas.details, datas.invoiceno);
                    },
                    error: function(error) {
                        _posReleaseLock();
                        alert("Error: Save failed. Please check your inputs and try again.");
                        console.log(error);
                    }
                });

            }
        }








    }

    function _posReleaseLock() {
        _posSaving = false;
        var btn = document.getElementById('save_add');
        if (btn) btn.disabled = false;
    }

    function _posApplyCustomerDueDate() {
        var invType = document.getElementById('invoiceType').value;
        if (invType !== 'credit' && invType !== 'credit_vat') return;
        var custId = document.getElementById('customerId').value;
        var cust = customers.find(function(c) { return String(c.customer_id) === String(custId); });
        if (cust && cust.due_date !== undefined) {
            document.getElementById('pos_due_date').value = cust.due_date || 0;
        }
    }

    function pusherNotify(action) {
        fetch('https://fexten.com/fexten_cloud_hub/modules/pusher/trigger.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                channel: 'notifications',
                event:   'record.saved',
                data:    { module: 'Sale', action: action }
            })
        })
        .catch(function() {})
        .finally(function() {
            window.location.href = $('#base_url').val() + 'new_pos';
        });
    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
            // var $productDropdown = $('#product' + i);
            // $productDropdown.empty();
            // $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

            // $.each(products, function(index, product) {
            //     $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
            // });

            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

            $.each(stores, function(index, store) {
                $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            });

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
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

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'salesinvoice_' + voucherNo + '_' + pdfDateStamp();
        }

        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload();
            })
        });
    }
</script>