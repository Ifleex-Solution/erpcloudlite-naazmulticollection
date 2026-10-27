<!-- Sales report -->
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    #btn-filter {
        position: relative;
        min-width: 145px;
        transition: opacity 0.2s;
    }
    #btn-filter.btn-loading {
        color: transparent !important;
        pointer-events: none;
        opacity: 0.85;
    }
    #btn-filter.btn-loading::after {
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
    /* ── Panel card ── */
    .panel.panel-bd.lobidrag { border:none !important; box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important; border-radius:14px !important; overflow:hidden !important; }
    .panel.panel-bd.lobidrag .panel-heading { background:#ffffff !important; padding:14px 24px !important; border:none !important; border-bottom:2px solid #F1F5F9 !important; }
    .panel.panel-bd.lobidrag .panel-title { display:flex !important; align-items:center !important; justify-content:space-between !important; flex-wrap:wrap !important; gap:10px !important; margin:0 !important; }
    .panel.panel-bd.lobidrag .panel-title > span:first-child { color:#1E293B !important; font-size:15px !important; font-weight:600 !important; letter-spacing:.3px !important; }
    .panel.panel-bd.lobidrag .panel-body { padding:20px !important; background:#ffffff !important; margin-left:0 !important; }
    .panel.panel-bd.lobidrag .form-group { margin-bottom:16px !important; max-width:280px; margin-left:20px; }
    .panel.panel-bd.lobidrag label { font-size:13px !important; font-weight:600 !important; color:#374151 !important; display:block; margin-bottom:4px !important; }
    .panel.panel-bd.lobidrag .form-control { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; padding:8px 12px !important; font-size:13px !important; color:#374151 !important; background:#F8FAFC !important; height:auto !important; transition:border-color .16s,box-shadow .16s,background .16s !important; }
    .panel.panel-bd.lobidrag .form-control:focus { border-color:#8b5cf6 !important; background:#ffffff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; outline:none !important; }
    .panel.panel-bd.lobidrag .btn.btn-success { background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important; border:none !important; border-radius:8px !important; padding:9px 24px !important; font-size:13px !important; font-weight:600 !important; color:#ffffff !important; letter-spacing:.3px !important; transition:background .16s,box-shadow .16s !important; }
    .panel.panel-bd.lobidrag .btn.btn-success:hover { background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important; box-shadow:0 4px 12px rgba(139,92,246,.35) !important; }
    /* ── Select2 ── */
    .select2-container .select2-selection--single { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; background:#F8FAFC !important; height:38px !important; }
    .select2-container .select2-selection--single .select2-selection__rendered { color:#374151 !important; font-size:13px !important; line-height:36px !important; padding-left:10px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow { height:36px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single { border-color:#8b5cf6 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; }
    .select2-dropdown { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; box-shadow:0 4px 16px rgba(0,0,0,.10) !important; margin-top:2px !important; }
    .select2-search--dropdown .select2-search__field { border:1.5px solid #E2E8F0 !important; border-radius:6px !important; font-size:13px !important; padding:5px 8px !important; }
    .select2-results__option { font-size:13px !important; padding:7px 12px !important; color:#374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:#8b5cf6 !important; color:#fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background:#F3F0FE !important; color:#8b5cf6 !important; }
    .select2-selection__placeholder { color:#94A3B8 !important; }
    /* ── Vertical layout ── */
    .panel.panel-bd.lobidrag .panel-body .input-group,
    .panel.panel-bd.lobidrag .panel-body .form-control { width:100% !important; max-width:100% !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] { display:flex !important; flex-direction:row !important; flex-wrap:wrap !important; gap:14px 20px !important; max-width:800px !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div { flex:1 1 160px; max-width:240px; }
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
                    <h4><?php
                        echo $title;
                        ?></h4>
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
                    <label for="product">Batch Type</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="product" class="form-control" style="width: 250px;" id="batchtype" onchange="selectBatchType()">
                            <option value=""></option>
                            <option value="1">Single</option>
                            <option value="2">Multiple</option>
                            <!-- <option value="3">Both</option> -->

                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="productgroup">Product Group</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="productgroup" class="form-control" style="width: 250px;" id="productgroup" onchange="onProductGroupChange()">
                            <option value="">--select one--</option>
                            <?php foreach ($product_group_list as $group) { ?>
                                <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product"><?php echo display('product') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="product" class="form-control" style="width: 250px;" id="product" onchange="selectProduct()">
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
                    <label for="product">Batch Name</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="batch" class="form-control" style="width: 250px;" id="batch">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="category"><?php echo display('category') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="category" class="form-control" id="category">
                            <option value="">--select one -- </option>
                            <?php
                            foreach ($category_list as $category) {
                            ?>
                                <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $category_id) {
                                                                                            echo 'selected';
                                                                                        } ?>><?php echo $category['category_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="store"><?php echo display('store') ?></label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="store" class="form-control" id="store">
                            <option value="">--select one -- </option>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="stocktype">Stock Type</label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="stocktype" class="form-control" id="stocktype">
                            <option value="">--select one -- </option>
                            <option value="all">All</option>
                            <option value="actualstock">Actual Stock</option>
                            <option value="physicalstock">Physical Stock</option>

                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="stockunittype">Unit Type</label>
                    <div class="input-group mr-4" style="width: 250px;">

                        <select name="stockunittype" class="form-control" id="stockunittype">
                            <option value="">--select one -- </option>
                            <option value="master" selected>Master Unit</option>
                            <option value="sub">Sub Unit</option>

                        </select>
                    </div>
                </div>


                <div class="form-group" id="password-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" style="width: 250px;" autocomplete="off">
                </div>

                <div class="report-btn-row">
                    <button type="button" id="btn-filter" class="btn btn-success" onclick="onFilterButtonClick()">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<script src="<?php echo base_url('my-assets/js/admin_js/sales_report.js') ?>" type="text/javascript"></script>
<?php
echo "<script>";
echo "let password_enable=" . json_encode($this->session->userdata('password_enable')) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script>
    let type2 = '';
    $(document).ready(function() {
        type2 = (usertype == 3) ? "B" : "A";
        getStoreDropdown(0);
    });

    function selectBatchType() {
        var batchtype = document.getElementById('batchtype').value;

        if (batchtype == 1) {
            document.getElementById("category").disabled = true;
        } else {
            document.getElementById("category").disabled = false;
        }

        if (batchtype) {
            document.getElementById("productgroup").value = "";
            document.getElementById("productgroup").disabled = true;
        } else {
            document.getElementById("productgroup").disabled = false;
        }

        getProductDropdown();
        getBatchDropdown();
    }

    function onProductGroupChange() {
        var groupId = document.getElementById('productgroup').value;

        if (groupId) {
            document.getElementById("batchtype").value = "";
            document.getElementById("batchtype").disabled = true;
            document.getElementById("batch").innerHTML = '<option value="">--select one--</option>';
            document.getElementById("batch").disabled = true;

            $.ajax({
                url: $('#base_url').val() + 'report/report/getProductsByGroup',
                type: 'POST',
                data: { group_id: groupId },
                success: function(response) {
                    var $productDropdown = $('#product');
                    $productDropdown.empty();
                    $productDropdown.append('<option value="">--select one--</option>');
                    var products = JSON.parse(response);
                    $.each(products, function(index, p) {
                        $productDropdown.append('<option value="' + p.id + '">' + p.product_name + '</option>');
                    });
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            document.getElementById("batchtype").disabled = false;
            document.getElementById("batch").disabled = false;
        }
    }

    function getProductDropdown() {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/usage_productjson',
            type: 'POST',
            data: {
                batchtype: document.getElementById('batchtype').value
            },
            success: function(response2) {
                var $batchDropdown = $('#product');
                $batchDropdown.empty();
                $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option
                if (response2 != "not") {
                    let batches2 = JSON.parse(response2);
                    $.each(batches2, function(index, batch) {
                        $batchDropdown.append('<option value="' + batch.id + '">' + batch.product_name + '</option>');
                    });
                }




            },
            error: function(error) {
                console.log(error)
            }
        });




    }

    function selectProduct() {

        if (document.getElementById('batchtype').value) {
            getBatchDropdown()
        }


    }



    function getBatchDropdown() {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchbyBatchtype',
            type: 'POST',
            data: {
                batchtype: document.getElementById('batchtype').value,
                product: document.getElementById('product').value
            },
            success: function(response2) {

                var $batchDropdown = $('#batch');
                $batchDropdown.empty();
                $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option
                $batchDropdown.append('<option value="0">Default</option>');
                if (response2 != "not") {
                    let batches2 = JSON.parse(response2);
                    console.log(batches2)
                    $.each(batches2, function(index, batch) {
                        if (batch.id == 1) return; // skip Default batch (already hardcoded above)
                        var label = batch.batchid + (batch.status == 0 ? ' (Inactive)' : '');
                        $batchDropdown.append('<option value="' + batch.id + '">' + label + '</option>');
                    });
                }




            },
            error: function(error) {
                console.log(error)
            }
        });




    }

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
                var $storeDropdown = $('#store');
                $storeDropdown.empty();
                $storeDropdown.append('<option value="" disabled selected>Select Store</option>'); // Add default option

                $.each(stores, function(index, store) {
                    $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
                    if (store.default != 0) {
                        $storeDropdown.val(store.id)
                    }
                });
            }
        });
    }

    function onFilterButtonClick() {
        if (!$('#stockunittype').val()) {
            alert('Please select Unit Type');
            return;
        }
        if (document.getElementById('batchtype').value == 1) {
            if (!document.getElementById('product').value) {
                alert('Please select Product');
                return;
            }
        }

        if (password_enable == "1") {
            if (document.getElementById('password').value == '') {
                alert("Password shouldn't be empty");
                return;
            }
            $.ajax({
                url: $('#base_url').val() + 'dashboard/setting/checkpasswordReport',
                type: 'POST',
                data: { password: document.getElementById('password').value },
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result == "wrong password") { alert("Wrong Password"); return; }
                    if (type2 == "A" && result != "A") { alert("Wrong Password"); return; }
                    generateReport();
                },
                error: function(e) { console.log(e); }
            });
        } else {
            generateReport();
        }
    }

    function generateReport() {
        $('#btn-filter').addClass('btn-loading').prop('disabled', true);

        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/stock_reportdata',
            data: {
                from_date: $('#from_date').val(),
                to_date: document.getElementById('single_date_checkbox').checked ? $('#from_date').val() : $('#to_date').val(),
                istype: document.getElementById('single_date_checkbox').checked,
                product: $('#product').val(),
                product_group: $('#productgroup').val(),
                category: $('#category').val(),
                store: $('#store').val(),
                batch: $('#batch').val(),
                stocktype: $('#stocktype').val(),
            },
            error: function() {
                $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                alert('Failed to load stock data. Please try again.');
            },
            success: function(data1) {
                datas = JSON.parse(data1);
                actual = JSON.parse(data1);

                console.log(actual)
                if (datas.length != 0) {
                    if ($('#stockunittype').val() == "master") {
                        datas.forEach(data => {
                            data.avqtymain=data.avqty,
                            data.avqty = convertmasterstock(data.avqty, data.conversion_ratio, data.master, data.sub)
                            data.inqty = convertmasterstock(data.inqty, data.conversion_ratio, data.master, data.sub)
                            data.outqty = convertmasterstock(Math.abs(data.outqty), data.conversion_ratio, data.master, data.sub)
                            data.pavqty = convertmasterstock(data.pavqty, data.conversion_ratio, data.master, data.sub)
                            data.pinqty = convertmasterstock(data.pinqty, data.conversion_ratio, data.master, data.sub)
                            data.poutqty = convertmasterstock(Math.abs(data.poutqty), data.conversion_ratio, data.master, data.sub)
                            data.stockunittype = $('#stockunittype').val()
                        });
                    } else {
                        datas.forEach(data => {
                            data.avqtymain= convertsubstock(data.avqty, data.conversion_ratio, data.sub),
                            data.avqty = convertsubstock(data.avqty, data.conversion_ratio, data.sub) + " " + data.sub
                            data.inqty = convertsubstock(data.inqty, data.conversion_ratio, data.sub)  + " " + data.sub
                            data.outqty = convertsubstock(Math.abs(data.outqty), data.conversion_ratio, data.sub) + " " + data.sub
                            data.pavqty = convertsubstock(data.pavqty, data.conversion_ratio, data.sub) + " " + data.sub
                            data.pinqty = convertsubstock(data.pinqty, data.conversion_ratio, data.sub) + " " + data.sub
                            data.poutqty = convertsubstock(Math.abs(data.poutqty), data.conversion_ratio, data.sub) + " " + data.sub
                            data.stockunittype = $('#stockunittype').val()
                        });
                    }
                    console.log(datas)

                    $.ajax({
                        type: "post",
                        url: $('#baseUrl2').val() + 'report/report/set_stock_session',
                        data: {
                            datas:     JSON.stringify(datas),
                            stocktype: $('#stocktype').val() || 'all',
                            title:     '<?php echo addslashes($title); ?>',
                            from_date: $('#from_date').val(),
                            to_date:   document.getElementById('single_date_checkbox').checked ? $('#from_date').val() : $('#to_date').val(),
                        },
                        error: function() {
                            $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                            alert('Failed to prepare report. Please try again.');
                        },
                        success: function(data1) {
                            $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                            window.open(`generate_stockreport`, '_blank');
                        }
                    });

                } else {
                    $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                    alert("No data available for the selected parameters.")
                }
            }
        });

    }

    function convertmasterstock(avstock, conversion_ratio, mastername, subname) {

        if(!subname&&!conversion_ratio){
            
                return ((avstock?avstock:0) + mastername);

            

        }
        let totalcount = 0;
        let mas = conversion_ratio * avstock / conversion_ratio;
        let subcount = 0;
        let sub = conversion_ratio * avstock % conversion_ratio;

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

        if (isNaN(totalcount)) {
            totalcount = 0
        }

        if (isNaN(subcount)) {
            subcount = 0
        }

        return (totalcount + mastername + " " + subcount + subname);
    }

    function convertsubstock(avstock, conversion_ratio, subname) {
        let sub = avstock * conversion_ratio;
        let sub2 = Math.floor((sub).toLocaleString());
        if (isNaN(sub2)) {
            sub = Number(sub).toFixed(6);
            return (Math.floor(sub)).toLocaleString() 
        } else {
            return sub2 

        }
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
</script>