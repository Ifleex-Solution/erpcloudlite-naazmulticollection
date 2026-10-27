<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>
<style>
.panel.panel-bd.lobidrag{border:none !important;box-shadow:0 2px 12px rgba(0,0,0,.07),0 6px 24px rgba(0,0,0,.05) !important;border-radius:14px !important;overflow:hidden !important;margin-bottom:20px !important}
.panel.panel-bd.lobidrag .panel-heading{background:#fff !important;padding:14px 24px !important;border:none !important;border-bottom:2px solid #F1F5F9 !important}
.panel.panel-bd.lobidrag .panel-title{display:flex !important;align-items:center !important;justify-content:space-between !important;flex-wrap:wrap !important;gap:10px !important;margin:0 !important}
.panel.panel-bd.lobidrag .panel-title > span:first-child{color:#1E293B !important;font-size:15px !important;font-weight:600 !important;letter-spacing:.3px !important}
.panel.panel-bd.lobidrag .panel-body{padding:24px 28px !important;background:#fff !important}
.panel.panel-bd.lobidrag .form-group{margin-bottom:14px !important;align-items:center !important}
.col-form-label{font-size:13px !important;font-weight:600 !important;color:#374151 !important;padding-top:8px !important}
.panel.panel-bd.lobidrag .form-control{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;padding:8px 12px !important;font-size:13px !important;color:#374151 !important;background:#F8FAFC !important;height:auto !important;transition:border-color .16s,box-shadow .16s,background .16s !important}
.panel.panel-bd.lobidrag .form-control:focus{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.panel.panel-bd.lobidrag .btn.btn-success{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:9px 22px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;letter-spacing:.3px !important;transition:background .16s,box-shadow .16s !important}
.panel.panel-bd.lobidrag .btn.btn-success:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}
/* Add Product button */
.btn-add-product{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:8px 18px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;transition:background .16s,box-shadow .16s !important;margin-bottom:14px !important}
.btn-add-product:hover{background:#15803D !important;box-shadow:0 4px 12px rgba(22,163,74,.25) !important;color:#fff !important}
/* Product table */
#saleTable{border-collapse:collapse !important;width:100% !important;font-size:13px !important}
#saleTable thead th{background:#F1F5F9 !important;color:#475569 !important;font-size:11px !important;font-weight:700 !important;text-transform:uppercase !important;letter-spacing:.6px !important;padding:10px 14px !important;border-bottom:2px solid #DDD6FE !important;border-top:none !important;white-space:nowrap !important}
#saleTable tbody td{padding:8px 10px !important;color:#374151 !important;border-color:#F3F0FE !important;vertical-align:middle !important}
#saleTable tbody tr:nth-child(odd) td{background:#fff !important}
#saleTable tbody tr:nth-child(even) td{background:#F8FAFC !important}
#saleTable tbody tr:hover td{background:#F3F0FE !important}
#saleTable .btn-danger{background:#EF4444 !important;border:none !important;border-radius:6px !important;padding:5px 10px !important;font-size:12px !important}
/* Select2 */
.select2-container .select2-selection--single,.select2-container--default .select2-selection--single{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;background:#F8FAFC !important;height:38px !important}
.select2-container .select2-selection--single .select2-selection__rendered{color:#374151 !important;font-size:13px !important;line-height:36px !important;padding-left:10px !important}
.select2-container .select2-selection--single .select2-selection__arrow{height:36px !important}
.select2-container--default.select2-container--focus .select2-selection--single,.select2-container--default.select2-container--open .select2-selection--single{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.select2-dropdown{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;box-shadow:0 4px 16px rgba(0,0,0,.10) !important;margin-top:2px !important}
.select2-results__option{font-size:13px !important;padding:7px 12px !important;color:#374151 !important}
.select2-container--default .select2-results__option--highlighted[aria-selected]{background:#8b5cf6 !important;color:#fff !important}
.select2-container--default .select2-results__option[aria-selected=true]{background:#F3F0FE !important;color:#8b5cf6 !important}
@media(max-width:767px){
  .panel.panel-bd.lobidrag .panel-body{padding:14px !important}
  .col-form-label{text-align:left !important;padding-top:0 !important;padding-bottom:4px !important}
  .panel.panel-bd.lobidrag .btn.btn-success{width:100% !important;margin:4px 0 !important}
}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span id="title"><?php echo $title; ?></span>
                    <a href="<?php echo base_url('product_grouplist') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>

            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Product Group Id <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product_group_id" value="<?php echo $product_group_id ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Product Group Name <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product_group_name" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Invoice Grouping <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="invoicegroup" name="invoicegroup" required tabindex="-1">
                                    <option value="">Select One</option>
                                    <option value="1">Enable</option>
                                    <option value="0">Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Status <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" required tabindex="-1">
                                    <option value="">Select One</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-add-product" onclick="addInputField('addinvoiceItem')">
                    <i class="fa fa-plus" style="margin-right:6px;"></i>Add Product
                </button>

                <div class="table-responsive">
                    <table class="table table-bordered" id="saleTable">
                        <thead>
                            <tr>
                                <th>Product <i class="text-danger">*</i></th>
                                <th>Unit <i class="text-danger">*</i></th>
                                <th>Qty <i class="text-danger">*</i></th>
                                <th class="text-center">Parent</th>
                                <th class="text-center"><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">
                                    <select name="product[]" class="form-control" id="product1" tabindex="1" onchange="product_search(1,'product')">
                                        <option value="">Select Product</option>
                                        <?php foreach ($products as $services) { ?>
                                            <option value="<?php echo $services['id']; ?>"><?php echo $services['product_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" id="mconversion_ratio1" />
                                    <input type="hidden" id="bd1" />
                                    <input type="hidden" id="ad1" />
                                </td>
                                <td>
                                    <select class="form-control" id="unit1" name="unit1" onchange="product_search(1,'unit')" tabindex="3">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" id="conversionid1" />
                                    <input type="hidden" id="conversiontype1" />
                                    <input type="hidden" id="conversion_ratio1" />
                                </td>
                                <td>
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" id="primary1" onchange="primaryCheck(1)" style="transform:scale(1.5);" />
                                </td>
                                <td class="text-center"></td>
                            </tr>

                            <?php for ($i = 2; $i <= 10; $i++) { ?>
                                <tr id="myRow<?php echo $i; ?>">
                                    <td class="product_field">
                                        <select name="product[]" class="form-control" id="product<?php echo $i; ?>" tabindex="1" onchange="product_search(<?php echo $i; ?>, 'product')">
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $services) { ?>
                                                <option value="<?php echo $services['id']; ?>"><?php echo $services['product_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" id="mconversion_ratio<?php echo $i; ?>" />
                                        <input type="hidden" id="bd<?php echo $i; ?>" />
                                        <input type="hidden" id="ad<?php echo $i; ?>" />
                                    </td>
                                    <td>
                                        <select class="form-control" id="unit<?php echo $i; ?>" name="unit<?php echo $i; ?>" onchange="product_search(<?php echo $i; ?>,'unit')" tabindex="3">
                                            <option value=""></option>
                                        </select>
                                        <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                        <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                        <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />
                                    </td>
                                    <td>
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" id="primary<?php echo $i; ?>" onclick="primaryCheck(<?php echo $i; ?>)" style="transform:scale(1.3);" />
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-danger btn-xs" type="button" onclick="deleteRow(<?php echo $i; ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                </div>

                <div class="form-group row" style="margin-top:16px;">
                    <div class="col-sm-12 text-right">
                        <button id="save_add" class="btn btn-success" type="button" onclick="save()">
                            <i class="fa fa-save"></i> <?php echo empty($id) ? display('save') : (empty($pagetype) ? display('update') : display('save')); ?>
                        </button>
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
echo "let product_group_id=" . json_encode($product_group_id) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script>
    let type2 = (usertype == 3) ? "B" : "A";
    let count = 2;

    if (!id) {
        document.getElementById('product_group_id').value = product_group_id;
    }

    function addInputField(t) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveProduct(0, count);
        count = count + 1;
    }

    $(document).ready(function() {
        $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});

        wireDuplicateCheck({
            input: '#product_group_name',
            url: $('#base_url').val() + 'product/product/check_duplicate_productgroup',
            saveButtons: '#save_add',
            excludeId: id
        });

        for (let j = 2; j <= 10; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }

        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'product/product/getProductGroupById',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    var sales = JSON.parse(response);

                    var $status = $('#status');
                    $status.empty();
                    $status.append('<option value="" disabled selected>Select Status</option>');
                    $status.append('<option value="1">Active</option>');
                    $status.append('<option value="0">Inactive</option>');
                    $status.val(sales[0].status);

                    var $invoicegroup = $('#invoicegroup');
                    $invoicegroup.empty();
                    $invoicegroup.append('<option value="" disabled selected>Select Invoice Group</option>');
                    $invoicegroup.append('<option value="1">Enable</option>');
                    $invoicegroup.append('<option value="0">Disable</option>');
                    $invoicegroup.val(sales[0].invoice_group);

                    document.getElementById('product_group_id').value = sales[0].groupcode;
                    document.getElementById('product_group_name').value = sales[0].name;
                    $('#product_group_name').trigger('input');

                    count = 1;
                    for (let i = 0; i < sales.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        getActiveProduct(sales[i].product, a);
                        document.getElementById('qty' + a).value = sales[i].qty;
                        document.getElementById('unit' + a).value = sales[i].unit;
                        getActiveSubUnitEdit(sales[i].product, a, sales[i].unit);
                        document.getElementById('primary' + a).checked = sales[i].parent == 1;
                        count = count + 1;
                    }
                },
                error: function(error) { console.log(error); }
            });
        }
    });

    function primaryCheck(row) {
        for (let i = 1; i <= count; i++) {
            let checkbox = document.getElementById("primary" + i);
            if (i !== row) checkbox.checked = false;
        }
    }

    function product_search(item, name) {
        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('unit' + item).value = "";
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: { prodid: document.getElementById('product' + item).value.toString() },
                success: function(response) {
                    let product = JSON.parse(response);
                    getActiveSubUnit(document.getElementById('product' + item).value, item);
                    setTimeout($.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: { prodid: document.getElementById('product' + item).value.toString() },
                        success: function(response2) {
                            if (response2 != "null") {
                                let product2 = JSON.parse(response2);
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio;
                                document.getElementById('bd' + item).value = product[0].unit_name;
                                document.getElementById('ad' + item).value = product2[0].unit_name;
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = "";
                                document.getElementById('bd' + item).value = "";
                                document.getElementById('ad' + item).value = "";
                            }
                        },
                        error: function(error) { console.log(error); }
                    }), 1000);
                    document.getElementById('unit' + item).value = product[0].unit;
                },
                error: function(error) { console.log(error); }
            });
        }
    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
        document.getElementById('qty' + num).value = 0;
    }

    function getActiveProduct(productId, item) {
        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>');
        $.each(products, function(index, product) {
            $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
        });
        if (productId > 0) $productDropdown.val(productId);
    }

    function save() {
        arrItem = [];

        if (document.getElementById('product_group_id').value == "") {
            alert("Product Group Id is required"); return;
        }
        if (document.getElementById('product_group_name').value == "") {
            alert("Product Group Name is required"); return;
        }
        if (document.getElementById('invoicegroup').value == "") {
            alert("Invoice Grouping is required"); return;
        }
        if (document.getElementById('status').value == "") {
            alert("Status is required"); return;
        }

        if (document.getElementById('invoicegroup').value == 1) {
            let countPrimary = 0;
            for (let j = 1; j < count; j++) {
                if (document.getElementById('myRow' + j).style.display != "none") {
                    if (document.getElementById('primary' + j).checked) countPrimary++;
                }
            }
            if (countPrimary == 0) {
                alert("Please mark a Parent product when Invoice Grouping is enabled");
                return;
            }
        }

        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('product' + i).value == "") {
                    alert("Product is required in row " + i); return;
                }
                if (document.getElementById('unit' + i).value == "") {
                    alert("Unit is required in row " + i); return;
                }
                if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity is required in row " + i); return;
                }
                arrItem.push({
                    product: document.getElementById('product' + i).value,
                    unit: document.getElementById('unit' + i).value,
                    parent: document.getElementById('primary' + i).checked ? 1 : 0,
                    quantity: document.getElementById('qty' + i).value
                });
            }
        }

        $("#save_add").prop('disabled', true);

        var url = id > 0
            ? $('#base_url').val() + 'product/product/update_productgroup'
            : $('#base_url').val() + 'product/product/save_productgroup';

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                id: id,
                items: arrItem,
                invoicegroup: document.getElementById('invoicegroup').value,
                product_group_id: document.getElementById('product_group_id').value,
                product_group_name: document.getElementById('product_group_name').value,
                status: document.getElementById('status').value
            },
            success: function(response) {
                var datas = JSON.parse(response);
                $("#save_add").prop('disabled', false);
                if (datas == "already") {
                    alert("Product Group ID or Name already exists");
                } else {
                    var msg = id > 0 ? "Product Group Updated Successfully" : "Product Group Saved Successfully";
                    alert(msg);
                    window.location.reload();
                }
            },
            error: function(error) {
                console.log(error);
                $("#save_add").prop('disabled', false);
                alert("Something went wrong. Please try again.");
            }
        });
    }

    function getActiveSubUnit(productId, item) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: { product_id: productId },
            success: function(response) {
                var datas = JSON.parse(response);
                var $subunitDropdown = $('#unit' + item);
                document.getElementById('conversionid' + item).value = "";
                document.getElementById('conversiontype' + item).value = "";
                document.getElementById('conversion_ratio' + item).value = "";
                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>');
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');
                $subunitDropdown.val(datas[0].unit);
                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });
            },
            error: function(error) { console.log(error); }
        });
    }

    function getActiveSubUnitEdit(productId, item, value) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: { product_id: productId },
            success: function(response) {
                var datas = JSON.parse(response);
                var $subunitDropdown = $('#unit' + item);
                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>');
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');
                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });
                $subunitDropdown.val(value);
            },
            error: function(error) { console.log(error); }
        });
    }
</script>
