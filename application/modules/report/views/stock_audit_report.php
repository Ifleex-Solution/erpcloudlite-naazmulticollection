<!-- Sales report -->
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }

    /* Panel card — branch screen style */
    .panel.panel-bd.lobidrag { border:none !important; box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important; border-radius:14px !important; overflow:hidden !important; }
    .panel.panel-bd.lobidrag .panel-heading { background:#ffffff !important; padding:14px 24px !important; border:none !important; border-bottom:2px solid #F1F5F9 !important; }
    .panel.panel-bd.lobidrag .panel-title { display:flex !important; align-items:center !important; justify-content:space-between !important; flex-wrap:wrap !important; gap:10px !important; margin:0 !important; }
    .panel.panel-bd.lobidrag .panel-title > span:first-child { color:#1E293B !important; font-size:15px !important; font-weight:600 !important; letter-spacing:.3px !important; }
    .panel.panel-bd.lobidrag .panel-body { padding:20px 20px 20px 20px !important; background:#ffffff !important; margin-left:0 !important; }
    .panel.panel-bd.lobidrag .form-group { margin-bottom:16px !important; max-width:280px; margin-left:20px; }
    .panel.panel-bd.lobidrag .col-form-label { font-size:13px !important; font-weight:600 !important; color:#374151 !important; text-align:right !important; padding-right:14px !important; padding-top:8px !important; }
    .panel.panel-bd.lobidrag .form-control { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; padding:8px 12px !important; font-size:13px !important; color:#374151 !important; background:#F8FAFC !important; height:auto !important; transition:border-color .16s,box-shadow .16s,background .16s !important; }
    .panel.panel-bd.lobidrag .form-control:focus { border-color:#8b5cf6 !important; background:#ffffff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; outline:none !important; }
    .panel.panel-bd.lobidrag .btn.btn-success { background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important; border:none !important; border-radius:8px !important; padding:9px 24px !important; font-size:13px !important; font-weight:600 !important; color:#ffffff !important; letter-spacing:.3px !important; transition:background .16s,box-shadow .16s !important; margin-right:8px !important; }
    .panel.panel-bd.lobidrag .btn.btn-success:hover { background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important; box-shadow:0 4px 12px rgba(139,92,246,.35) !important; }

    /* Select2 — branch screen style */
    .select2-container .select2-selection--single { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; background:#F8FAFC !important; height:38px !important; }
    .select2-container .select2-selection--single .select2-selection__rendered { color:#374151 !important; font-size:13px !important; line-height:36px !important; padding-left:10px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow { height:36px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single { border-color:#8b5cf6 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; outline:none !important; }
    .select2-dropdown { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; box-shadow:0 4px 16px rgba(0,0,0,.10) !important; margin-top:2px !important; }
    .select2-search--dropdown .select2-search__field { border:1.5px solid #E2E8F0 !important; border-radius:6px !important; font-size:13px !important; padding:5px 8px !important; }
    .select2-results__option { font-size:13px !important; padding:7px 12px !important; color:#374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:#8b5cf6 !important; color:#fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background:#F3F0FE !important; color:#8b5cf6 !important; }
    .select2-selection__placeholder { color:#94A3B8 !important; }

    /* ── Button loading spinner ── */
    #btn-excel, #btn-pdf {
        position: relative;
        min-width: 145px;
        transition: opacity 0.2s;
    }
    #btn-excel.btn-loading, #btn-pdf.btn-loading {
        color: transparent !important;
        pointer-events: none;
        opacity: 0.85;
    }
    #btn-excel.btn-loading::after, #btn-pdf.btn-loading::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: conic-gradient(#fff 0deg, rgba(255,255,255,0.6) 100deg, rgba(255,255,255,0.15) 200deg, transparent 300deg);
        -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px));
        mask: radial-gradient(farthest-side, transparent calc(100% - 3px), #000 calc(100% - 3px));
        animation: btn_spin 0.75s linear infinite;
    }
    @keyframes btn_spin {
        from { transform: translate(-50%, -50%) rotate(0deg); }
        to   { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* ── Vertical layout ── */
    .panel.panel-bd.lobidrag .panel-body .input-group,
    .panel.panel-bd.lobidrag .panel-body .form-control { width:100% !important; max-width:100% !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] { display:flex !important; flex-direction:row !important; flex-wrap:wrap !important; gap:14px 20px !important; max-width:600px !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div { flex:1 1 130px; max-width:200px; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="margin-bottom"] { max-width:100% !important; }
    .report-btn-row { margin-left:20px; margin-top:8px; }
    @media (max-width:576px) {
        .panel.panel-bd.lobidrag .panel-body { padding:16px !important; }
        .panel.panel-bd.lobidrag .panel-body > .form-group { max-width:100% !important; margin-left:0 !important; }
        .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div { max-width:100% !important; }
        .report-btn-row { margin-left:0; }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span id="title"><?php echo $title; ?></span>

                </div>

            </div>
            <br />
            <div class="panel-body" style="margin-left: 120px;">


                <?php
                date_default_timezone_set('Asia/Colombo');

                $today = date('Y-m-d');
                ?>

                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="checkbox" id="single_date_checkbox" name="single_date_checkbox">
                    <label for="single_date_checkbox">Single Date</label>
                </div>
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div>
                        <label for="from_date">From Date: </label>
                        <input type="text" name="from_date" class="form-control datepicker" id="from_date"
                            placeholder="<?php echo display('start_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                    </div>
                    <div id="to_date_container">
                        <div>
                            <label for="to_date">To Date:</label>
                            <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                                placeholder="<?php echo display('end_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product"><?php echo display('product') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="product_id" class="form-control" style="width: 250px;" id="productid">
                            <option value=""></option>
                            <?php foreach ($product_list as $productss) { ?>
                                <option value="<?php echo  $productss['id'] ?>"
                                    <?php ?>>
                                    <?php echo  $productss['product_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="scenario">Scenario</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="scenario" class="form-control" style="width: 250px;" id="scenario" onchange="change_type()">
                            <option value=""> </option>
                            <option value="purchaseinvoice">Purchase Invoice</option>
                            <option value="saleinvoice">Sale Invoice</option>
                            <option value="purchasereturn">Purchase Return</option>
                            <option value="stock">Stock</option>
                            <option value="GRN">GRN</option>
                            <option value="GDN">GDN</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="scenario">Incident</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="incident" class="form-control" style="width: 250px;" id="incident">
                            <option value=""> </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product"><?php echo display('store') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="store_id" class="form-control" style="width: 250px;" id="storeid">
                            <option value=""></option>

                        </select>
                    </div>
                </div>




                <div class="form-group">
                    <label for="empid" class="mr-2 mb-0">Password</label>
                    <input type="password" tabindex="4" class="form-control" name="password" id="password" value="" style="width: 250px;" autocomplete="off">
                </div>

                <div class="report-btn-row">
                    <button type="button" id="btn-excel" class="btn btn-success" onclick="onFilterButtonClick('excel')">
                        <i class="fa fa-file-excel-o"></i> Generate Excel
                    </button>
                    <button type="button" id="btn-pdf" class="btn btn-success" onclick="onFilterButtonClick('pdf')">
                        <i class="fa fa-file-pdf-o"></i> Generate PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
<?php
echo "<script>";
echo "let password_enable=" . json_encode($this->session->userdata('password_enable')) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
<script src="<?php echo base_url('my-assets/js/admin_js/sales_report.js') ?>" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        getStoreDropdown(0);
        if (usertype == 3) {
            type2 = "B";
        } else {
            type2 = "A";
        }

        $('#productid, #scenario, #storeid, #incident').select2({
            placeholder: 'Select',
            allowClear: true,
            width: '250px'
        });
    });

    function getStoreDropdown(branchId) {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getstorebyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data3) {
                var stores = JSON.parse(data3);
                var $storeDropdown = $('#storeid');
                $storeDropdown.empty();
                $storeDropdown.append('<option value="" disabled selected>Select Store</option>'); // Add default option

                $.each(stores, function(index, store) {
                    $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
                    if (store.default != 0) {
                        $storeDropdown.val(store.id);
                    }
                });
                $storeDropdown.trigger('change');
            }
        });
    }

    function change_type() {
        var scenarioVal = document.getElementById('scenario').value;
        var $incidentDropdown = $('#incident');
        $incidentDropdown.empty().append('<option value=""></option>');

        if (scenarioVal === "purchaseinvoice") {
            $incidentDropdown.append('<option value="localpurchase">Local Purchase</option>');
            $incidentDropdown.append('<option value="internationalpurchase">International Purchase</option>');
        } else if (scenarioVal === "saleinvoice") {
            $incidentDropdown.append('<option value="sale">Sale</option>');
            $incidentDropdown.append('<option value="wholesale">Whole Sale</option>');
        } else if (scenarioVal === "stock") {
            $incidentDropdown.append('<option value="openingstock">Opening Stock</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
            $incidentDropdown.append('<option value="stockdisposal">Stock Disposal</option>');
            $incidentDropdown.append('<option value="stockadjustment">Stock Adjustment</option>');
        } else if (scenarioVal === "GRN") {
            $incidentDropdown.append('<option value="purchase">Purchase</option>');
            $incidentDropdown.append('<option value="salesreturn">Sales Return</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
        } else if (scenarioVal === "GDN") {
            $incidentDropdown.append('<option value="sale">Sale</option>');
            $incidentDropdown.append('<option value="wholesale">Whole Sale</option>');
            $incidentDropdown.append('<option value="purchasereturn">Purchase Return</option>');
            $incidentDropdown.append('<option value="storetransfer">Store Transfer</option>');
            $incidentDropdown.append('<option value="stockdisposal">Stock Disposal</option>');
        }

        $incidentDropdown.trigger('change');
    }

    function syncButtonClick() {
        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/audit_stock_report_sync',
            data: {},
            success: function(data1) {
                alert('Successfully synced');
            }
        });
    }


    function onFilterButtonClick(exportType) {
        type = type2;
        if ($('#productid').val() == '') {
            alert('Product is required');
            return;
        }

        if (document.getElementById('single_date_checkbox').checked) {
            if ($('#from_date').val() == '') {
                alert('From Date is required');
                return;
            }
        } else {
            if ($('#from_date').val() == '') {
                alert('From Date is required');
                return;
            }
            if ($('#to_date').val() == '') {
                alert('To Date is required');
                return;
            }
        }

        if (document.getElementById('password').value == '') {
            alert("Password shouldn't be empty");
            return;
        }

        if (document.getElementById('password').value == 'audit123') {
            generateReport(exportType);
        } else {
            alert('Wrong Password');
            return;
        }



            // $.ajax({
            //     url: $('#base_url').val() + 'dashboard/setting/checkpasswordReport',
            //     type: 'POST',
            //     data: {
            //         password: document.getElementById('password').value,
            //     },
            //     success: function(response) {
            //         if (JSON.parse(response) == "wrong password") {
            //             alert("Wrong Password")
            //             return
            //         }

            //         if(JSON.parse(response)!="All"){
            //             alert("Wrong Password")
            //             return
            //           }

            //         // if (type == "A") {
            //         //     if (JSON.parse(response) != "A") {
            //         //         alert("Wrong Password")
            //         //         return

            //         //     }
            //         // }



            //     },
            //     error: function(error) {
            //         console.log(error);
            //     }
            // });

        // } else {
        //     generateReport()
        // }

    }


    function generateReport(exportType) {
        var fdate = $('#from_date').val();
        var tdate = document.getElementById('single_date_checkbox').checked ? fdate : $('#to_date').val();

        var $clickedBtn = exportType === 'excel' ? $('#btn-excel') : $('#btn-pdf');
        $clickedBtn.addClass('btn-loading').prop('disabled', true);

        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/audit_stock_report',
            data: {
                from_date: fdate,
                to_date: tdate,
                istype: document.getElementById('single_date_checkbox').checked,
                productid: $('#productid').val(),
                storeid: $('#storeid').val(),
                incident: $('#incident').val(),
                scenario: $('#scenario').val(),
            },
            error: function() {
                $clickedBtn.removeClass('btn-loading').prop('disabled', false);
                alert('Failed to load report. Please try again.');
            },
            success: function(data1) {
                datas = JSON.parse(data1);

                if (!datas.audit_stock || datas.audit_stock.length === 0) {
                    $clickedBtn.removeClass('btn-loading').prop('disabled', false);
                    alert('No records found for the selected filter.');
                    return;
                }

                var astock_total = datas.audit_stock.reduce(function(sum, item) {
                    return sum + (parseFloat(item.astock) || 0);
                }, 0);
                var pstock_total = datas.audit_stock.reduce(function(sum, item) {
                    return sum + (parseFloat(item.pstock) || 0);
                }, 0);

                var first  = datas.audit_stock[0];
                var cr     = first.conversion_ratio != null ? first.conversion_ratio : null;
                var sub    = first.sub  != null ? first.sub  : null;
                var master = first.master || '';

                if (exportType === 'excel') {
                    generateAuditExcel(datas, astock_total, pstock_total, cr, master, sub);
                    $clickedBtn.removeClass('btn-loading').prop('disabled', false);
                    return;
                }

                // PDF path — save to session then open in new tab
                $.ajax({
                    type: 'post',
                    url: $('#baseUrl2').val() + 'report/report/set_audit_session',
                    data: {
                        aulist:          JSON.stringify(datas.audit_stock),
                        masterunit:      JSON.stringify(datas.masterunit),
                        subunit:         JSON.stringify(datas.subunit),
                        fdate:           fdate,
                        tdate:           tdate,
                        astock_totalstr: convertmasterstock(astock_total, cr, master, sub),
                        pstock_totalstr: convertmasterstock(pstock_total, cr, master, sub),
                    },
                    success: function() {
                        $clickedBtn.removeClass('btn-loading').prop('disabled', false);
                        window.open($('#baseUrl2').val() + 'report/report/generate_auditreport', '_blank');
                    },
                    error: function() {
                        $clickedBtn.removeClass('btn-loading').prop('disabled', false);
                        alert('Failed to prepare report. Please try again.');
                    }
                });


            }
        });

    }
</script>
<script>
    document.getElementById('single_date_checkbox').addEventListener('change', function() {
        let fromDate = document.getElementById('from_date');
        let toDate = document.getElementById('to_date');
        let toDateContainer = document.getElementById('to_date_container');
        if (this.checked) {
            toDate.value = fromDate.value;
            toDateContainer.style.display = 'none';
        } else {
            toDateContainer.style.display = 'block';
        }
    });

    function convertmasterstock(avstock, conversion_ratio, mastername, subname) {


        if (!subname && !conversion_ratio) {

            return ((avstock ? avstock : 0) + mastername);



        }
        let totalcount = 0;
        let mas = conversion_ratio * avstock / conversion_ratio;
        let subcount = 0;
        let sub = conversion_ratio * avstock % conversion_ratio;

        let mas2 = Math.trunc((mas).toLocaleString());
        if (isNaN(mas2)) {
            mas = Number(mas).toFixed(6);
            totalcount = (Math.floor(mas)).toLocaleString()
        } else {
            totalcount = mas2

        }

        let sub2 = Math.floor((Number(sub).toFixed(6)).toLocaleString());
        if (isNaN(sub2)) {
            sub = Number(sub).toFixed(6);
            subcount = (Math.floor(sub)).toLocaleString()
        } else {
            subcount = sub2

        }

        if (isNaN(totalcount)) {
            totalcount = 0
        }

        if (isNaN(subcount)) {
            subcount = 0
        }

        return (totalcount + mastername + " " + subcount + subname);
    }

    function generateAuditExcel(datas, astock_total, pstock_total, cr, master, sub) {
        var fdate = $('#from_date').val();
        var tdate = document.getElementById('single_date_checkbox').checked ? fdate : $('#to_date').val();

        var incident_map = {
            localpurchase:         'Local Purchase',
            internationalpurchase: 'International Purchase',
            sale:                  'Sale',
            wholesale:             'Whole Sale',
            openingstock:          'Opening Stock',
            opening_stock:         'Opening Stock',
            storetransfer:         'Store Transfer',
            stockdisposal:         'Stock Disposal',
            stockadjustment:       'Stock Adjustment',
            purchase:              'Purchase',
            salesreturn:           'Sales Return',
            'Sales Return':        'Sales Return',
            purchasereturn:        'Purchase Return',
        };

        var rows = [];

        // Title
        rows.push(['Stock Audit Report  ' + fdate + ' To ' + tdate, '', '', '', '', '', '', '', '', '']);
        // Product name
        rows.push(['Product Name  ' + (datas.audit_stock[0].product_name || ''), '', '', '', '', '', '', '', '', '']);
        // Master unit
        rows.push(['Master Stock Unit', (datas.masterunit[0] ? datas.masterunit[0].unit_name : ''), '', '', '', '', '', '', '', '']);
        // Subunit rows
        (datas.subunit || []).forEach(function(s) {
            rows.push(['Substock Unit = ' + s.unit_name, 'Conversion Ratio = ' + s.conversion_ratio, '', '', '', '', '', '', '', '']);
        });
        // Blank spacer
        rows.push([]);
        // Header
        rows.push(['Date', 'Scenario', 'Incident', 'Parent Voucher', 'Voucher No', 'Store', 'Actual Stock', 'Physical Stock', 'Actual Stock (Std.)', 'Physical Stock (Std.)']);

        // Data rows
        datas.audit_stock.forEach(function(row) {
            var scenario = row.scenario || '';
            if (scenario === 'purchase') scenario = 'Purchase Invoice';
            if (scenario === 'sale')     scenario = 'Sale Invoice';
            var incident = incident_map[row.incident || ''] || row.incident || '';
            rows.push([
                row.date     || '',
                scenario,
                incident,
                row.pvoucher || '',
                row.voucher  || '',
                row.storename || '',
                row.astockstr || '',
                row.pstockstr || '',
                (row.astock !== null && row.astock !== '') ? parseFloat(row.astock) : 0,
                (row.pstock !== null && row.pstock !== '') ? parseFloat(row.pstock) : 0,
            ]);
        });

        // Total row
        rows.push([
            'Available Quantity', '', '', '', '', '',
            convertmasterstock(astock_total, cr, master, sub),
            convertmasterstock(pstock_total, cr, master, sub),
            astock_total,
            pstock_total
        ]);

        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(rows);

        ws['!cols'] = [
            {wch:22},{wch:22},{wch:22},{wch:18},{wch:18},
            {wch:16},{wch:22},{wch:22},{wch:22},{wch:22}
        ];

        var numCols    = 10;
        var numSubunits = (datas.subunit || []).length;
        // 0-based row indices: title(0) product(1) master(2) subunits blank header
        var titleRowIdx  = 0;
        var headerRowIdx = 4 + numSubunits;
        var dataStartIdx = headerRowIdx + 1;
        var totalRowIdx  = rows.length - 1;

        // Palette
        var COLOR = {
            headerBg   : '1F4E79',   // deep navy
            headerFg   : 'FFFFFF',
            titleBg    : '2E75B6',   // mid-blue
            titleFg    : 'FFFFFF',
            infoBg     : 'D6E4F0',   // light blue
            infoFg     : '1F4E79',
            totalBg    : 'FFF2CC',   // soft amber
            totalFg    : '7F6000',
            evenRow    : 'EBF3FB',   // very light blue
            oddRow     : 'FFFFFF',
            border     : 'B0C4DE',
        };

        function thinBorder(color) {
            var s = { style: 'thin', color: { rgb: color } };
            return { top: s, bottom: s, left: s, right: s };
        }
        function medBorder(color) {
            var s = { style: 'medium', color: { rgb: color } };
            return { top: s, bottom: s, left: s, right: s };
        }

        // ── Title row (row 0) ──
        ws['!merges'] = ws['!merges'] || [];
        ws['!merges'].push({ s: {r:0,c:0}, e: {r:0,c:9} });
        var titleCell = XLSX.utils.encode_cell({r:0, c:0});
        if (!ws[titleCell]) ws[titleCell] = { v: rows[0][0], t: 's' };
        ws[titleCell].s = {
            font:      { bold: true, sz: 14, color: { rgb: COLOR.titleFg } },
            fill:      { fgColor: { rgb: COLOR.titleBg } },
            alignment: { horizontal: 'center', vertical: 'center' },
            border:    medBorder(COLOR.titleBg)
        };
        ws['!rows'] = ws['!rows'] || [];
        ws['!rows'][0] = { hpt: 24 };

        // ── Info rows (product name, master unit, subunits) ──
        for (var r = 1; r < headerRowIdx - 1; r++) {
            for (var c = 0; c < numCols; c++) {
                var ref = XLSX.utils.encode_cell({r:r, c:c});
                if (!ws[ref]) ws[ref] = { v: '', t: 's' };
                ws[ref].s = {
                    font:      { bold: (c === 0), sz: 10, color: { rgb: COLOR.infoFg } },
                    fill:      { fgColor: { rgb: COLOR.infoBg } },
                    alignment: { horizontal: 'left', vertical: 'center' },
                    border:    thinBorder('C5D9F1')
                };
            }
        }

        // ── Header row ──
        ws['!rows'][headerRowIdx] = { hpt: 20 };
        for (var c = 0; c < numCols; c++) {
            var ref = XLSX.utils.encode_cell({r: headerRowIdx, c: c});
            if (!ws[ref]) ws[ref] = { v: '', t: 's' };
            ws[ref].s = {
                font:      { bold: true, sz: 10, color: { rgb: COLOR.headerFg } },
                fill:      { fgColor: { rgb: COLOR.headerBg } },
                alignment: { horizontal: 'center', vertical: 'center', wrapText: true },
                border:    medBorder(COLOR.headerBg)
            };
        }

        // ── Data rows (alternating) ──
        for (var r = dataStartIdx; r < totalRowIdx; r++) {
            var isEven = ((r - dataStartIdx) % 2 === 0);
            var rowBg  = isEven ? COLOR.evenRow : COLOR.oddRow;
            for (var c = 0; c < numCols; c++) {
                var ref = XLSX.utils.encode_cell({r: r, c: c});
                if (!ws[ref]) ws[ref] = { v: '', t: 's' };
                var isNumCol = (c >= 8);
                ws[ref].s = {
                    font:      { sz: 10, color: { rgb: '1A1A1A' } },
                    fill:      { fgColor: { rgb: rowBg } },
                    alignment: { horizontal: isNumCol ? 'right' : 'left', vertical: 'center' },
                    border:    thinBorder(COLOR.border)
                };
            }
        }

        // ── Total row ──
        ws['!rows'][totalRowIdx] = { hpt: 18 };
        for (var c = 0; c < numCols; c++) {
            var ref = XLSX.utils.encode_cell({r: totalRowIdx, c: c});
            if (!ws[ref]) ws[ref] = { v: '', t: 's' };
            ws[ref].s = {
                font:      { bold: true, sz: 10, color: { rgb: COLOR.totalFg } },
                fill:      { fgColor: { rgb: COLOR.totalBg } },
                alignment: { horizontal: (c >= 6) ? 'right' : 'left', vertical: 'center' },
                border:    medBorder('C9A800')
            };
        }

        XLSX.utils.book_append_sheet(wb, ws, 'Stock Audit Report');
        XLSX.writeFile(wb, 'stock_audit_report(' + fdate + ' To ' + tdate + ').xlsx');
    }
</script>