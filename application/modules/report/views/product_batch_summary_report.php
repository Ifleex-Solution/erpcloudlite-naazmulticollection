<style>
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
    .report-btn-row { margin-left:20px; margin-top:8px; }
    @media (max-width:576px) {
        .panel.panel-bd.lobidrag .panel-body { padding:16px !important; }
        .panel.panel-bd.lobidrag .panel-body > .form-group { max-width:100% !important; margin-left:0 !important; }
        .report-btn-row { margin-left:0; }
    }
</style>


<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <br />
            <div class="panel-body" style="margin-left: 120px;">
                <div class="form-group">
                    <label for="store">Store</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="store" name="store" style="width: 250px;" tabindex="1">
                            <option value="">All Stores</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="category" name="category" style="width: 250px;" tabindex="2">
                            <option value="">All Categories</option>
                            <?php if (!empty($category_list)) { ?>
                                <?php foreach ($category_list as $category) { ?>
                                    <option value="<?php echo $category['category_id']; ?>">
                                        <?php echo $category['category_name']; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product">Product</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="product" name="product" style="width: 250px;" tabindex="3">
                            <option value="">All Products</option>
                            <?php if (!empty($product_list)) { ?>
                                <?php foreach ($product_list as $product) { ?>
                                    <option value="<?php echo $product['id']; ?>"><?php echo $product['product_name']; ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="supplier">Supplier</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="supplier" name="supplier" style="width: 250px;" tabindex="4">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="batch_type">Batch Type</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="batch_type" name="batch_type" style="width: 250px;"
                            tabindex="5">
                            <option value="">All Batch Types</option>
                            <option value="1">Single</option>
                            <option value="2">Multiple</option>
                            <option value="3">Both</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="status" name="status" style="width: 250px;" tabindex="6">
                            <option value="">All Status</option>
                            <option value="not_expired">Not Expired</option>
                            <option value="to_be_expired">To be Expired</option>
                            <option value="expired">Expired</option>
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
        getStoreDropdown();
        getSupplierDropdown();
    });

    function getStoreDropdown() {
        var base_url = $('#base_url').val();
        $.ajax({
            type: "post",
            url: base_url + "store/store/getstorebyuserid",
            success: function(data) {
                var stores = JSON.parse(data);
                var $storeDropdown = $('#store');
                $.each(stores, function(index, store) {
                    $storeDropdown.append('<option value="' + store.id + '">' + store.name +
                        '</option>');
                });
            }
        });
    }

    function getSupplierDropdown() {
        var base_url = $('#base_url').val();
        $.ajax({
            type: "get",
            url: base_url + "report/report/getSuppliersForReport",
            success: function(data) {
                var suppliers = JSON.parse(data);
                var $supplierDropdown = $('#supplier');
                $.each(suppliers, function(index, supplier) {
                    $supplierDropdown.append('<option value="' + supplier.supplier_id + '">' + supplier
                        .supplier_name + '</option>');
                });
            }
        });
    }

    function onFilterButtonClick() {
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
            url: $('#baseUrl2').val() + 'report/report/product_batch_summary_report_data',
            data: {
                store: $('#store').val(),
                category: $('#category').val(),
                product: $('#product').val(),
                supplier: $('#supplier').val(),
                batch_type: $('#batch_type').val(),
                status: $('#status').val()
            },
            error: function() {
                $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                alert('Failed to load report data. Please try again.');
            },
            success: function(data1) {
                var datas = JSON.parse(data1);
                console.log(datas)
                if (datas.length > 0) {
                    datas.forEach(data => {
                        data.avqty = convertmasterstock(data.master_stock_qty, data.conversion_ratio, data.master, data.sub)
                    });
                    console.log(datas)

                    $.ajax({
                        type: "post",
                        url: $('#baseUrl2').val() + 'report/report/set_stock_session2',
                        data: {
                            datas: JSON.stringify(datas),
                        },
                        error: function() {
                            $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                            alert('Failed to prepare report. Please try again.');
                        },
                        success: function(data1) {
                            $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                            window.open(`generate_product_batch_summary_report`, '_blank');
                        }
                    });
                } else {
                    $('#btn-filter').removeClass('btn-loading').prop('disabled', false);
                    alert('No data available for the selected parameters.');
                }
            }
        });
    }

    function convertmasterstock(avstock, conversion_ratio, mastername, subname) {

        if (!subname && !conversion_ratio) {

            return ((avstock ? avstock : 0) + mastername);



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
</script>