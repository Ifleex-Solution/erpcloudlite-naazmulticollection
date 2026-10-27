<script src="<?php echo base_url() ?>my-assets/js/admin_js/json/product.js" type="text/javascript"></script>
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
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
.panel.panel-bd.lobidrag .form-control[readonly]{background:#F1F5F9 !important;color:#94A3B8 !important;cursor:not-allowed !important}
.panel.panel-bd.lobidrag textarea.form-control{resize:vertical !important;min-height:80px !important}
/* Buttons */
.panel.panel-bd.lobidrag .btn.btn-success,.btn-save-row{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:9px 22px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;letter-spacing:.3px !important;transition:background .16s,box-shadow .16s !important}
.panel.panel-bd.lobidrag .btn.btn-success:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}
.btn-add-substock{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:8px 18px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;transition:background .16s,box-shadow .16s !important}
.btn-add-substock:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important;color:#fff !important}
/* Substock table */
#dataTable{border-collapse:collapse !important;width:100% !important;font-size:13px !important}
#dataTable thead th{background:#F1F5F9 !important;color:#475569 !important;font-size:11px !important;font-weight:700 !important;text-transform:uppercase !important;letter-spacing:.6px !important;padding:10px 14px !important;border-bottom:2px solid #DDD6FE !important;border-top:none !important;white-space:nowrap !important}
#dataTable tbody td{padding:9px 14px !important;color:#374151 !important;border-color:#F3F0FE !important;vertical-align:middle !important}
#dataTable tbody tr:nth-child(odd) td{background:#fff !important}
#dataTable tbody tr:nth-child(even) td{background:#F8FAFC !important}
#dataTable tbody tr:hover td{background:#F3F0FE !important}
/* Modal */
.modal-content{border-radius:12px !important;border:none !important;box-shadow:0 8px 32px rgba(0,0,0,.15) !important}
.modal-header{background:#fff !important;border-bottom:2px solid #F1F5F9 !important;padding:16px 24px !important;border-radius:12px 12px 0 0 !important}
.modal-header h4{font-size:15px !important;font-weight:700 !important;color:#1E293B !important;margin:0 !important}
.modal-body{padding:20px 24px !important}
.modal-body .form-group label{font-size:13px !important;font-weight:600 !important;color:#374151 !important;margin-bottom:5px !important}
.modal-body .form-control{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;font-size:13px !important;background:#F8FAFC !important}
.modal-body .form-control:focus{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important}
.modal-footer{padding:12px 24px !important;border-top:2px solid #F1F5F9 !important;border-radius:0 0 12px 12px !important}
.modal-footer .btn-default{border-radius:8px !important;font-size:13px !important;font-weight:500 !important;padding:8px 18px !important}
.modal-footer .btn-success{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;font-size:13px !important;font-weight:600 !important;padding:8px 18px !important}
/* Select2 */
.select2-container .select2-selection--single,.select2-container--default .select2-selection--single{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;background:#F8FAFC !important;height:38px !important}
.select2-container .select2-selection--single .select2-selection__rendered{color:#374151 !important;font-size:13px !important;line-height:36px !important;padding-left:10px !important}
.select2-container .select2-selection--single .select2-selection__arrow{height:36px !important}
.select2-container--default.select2-container--focus .select2-selection--single,.select2-container--default.select2-container--open .select2-selection--single{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.select2-dropdown{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;box-shadow:0 4px 16px rgba(0,0,0,.10) !important;margin-top:2px !important}
.select2-search--dropdown .select2-search__field{border:1.5px solid #E2E8F0 !important;border-radius:6px !important;font-size:13px !important;padding:5px 8px !important}
.select2-results__option{font-size:13px !important;padding:7px 12px !important;color:#374151 !important}
.select2-container--default .select2-results__option--highlighted[aria-selected]{background:#8b5cf6 !important;color:#fff !important}
.select2-container--default .select2-results__option[aria-selected=true]{background:#F3F0FE !important;color:#8b5cf6 !important}
.select2-selection__placeholder{color:#94A3B8 !important}
/* Responsive */
@media(max-width:767px){
  .panel.panel-bd.lobidrag .panel-body{padding:14px !important}
  .col-form-label{text-align:left !important;padding-top:0 !important;padding-bottom:4px !important}
  .panel.panel-bd.lobidrag .btn.btn-success{width:100% !important;margin:4px 0 !important}
  .btn-add-substock{width:100% !important;margin-bottom:8px !important}
  #dataTable thead th{font-size:10px !important;padding:8px 8px !important;letter-spacing:.3px !important}
  #dataTable tbody td{font-size:12px !important;padding:7px 8px !important}
  .modal-dialog{margin:8px !important}
  .modal-body{padding:14px 16px !important}
}
/* Duplicate field highlight */
.field-duplicate{border-color:#DC2626 !important;background:#FFF5F5 !important}
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo $title; ?></span>
                    <a href="<?php echo base_url('product_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>

            <div class="panel-body">
                <input type="hidden" name="button" id="button" value="akakak" />

                <!-- Row 1: Barcode | Serial No -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Barcode / QR-Code</label>
                            <div class="col-sm-8">
                                <?php if (!empty($id)) { ?>
                                    <input class="form-control" name="product_id" type="text" id="product_id"
                                        placeholder="Barcode / QR-Code" tabindex="1"
                                        value="<?php echo !empty($product_id) ? $product_id : (!empty($product->product_id) ? $product->product_id : ''); ?>"
                                        readonly>
                                <?php } else { ?>
                                    <input class="form-control" name="product_id" type="text" id="product_id"
                                        placeholder="Barcode / QR-Code" tabindex="1" value="<?php echo $productId ?>">
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php echo display('serial_no') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="serial_no" name="serial_no"
                                    placeholder="111,abc,XYz" value="<?php echo $product->serial_no ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 2: Product Name | Print Name -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php echo display('product_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="product_name" type="text" id="product_name"
                                    placeholder="<?php echo display('product_name') ?>"
                                    value="<?php echo $product->product_name ?>" required tabindex="1">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Print Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="printname" name="printname"
                                    placeholder="Print Name" value="<?php echo $product->printname ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 3: Product Detail | Model -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php echo display('product_details') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="description" id="description"
                                    placeholder="<?php echo display('product_details') ?>"
                                    tabindex="2" value="<?php echo $product->product_details ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php echo display('model') ?></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="product_model" name="model"
                                    placeholder="<?php echo display('model') ?>"
                                    value="<?php echo $product->product_model ?>" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 4: Category | Subcategory -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label"><?php echo display('category') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="category_id" required name="category_id" tabindex="3"
                                    onchange="changecategory()">
                                    <option value=""></option>
                                    <?php if ($category_list) { foreach ($category_list as $categories) { ?>
                                        <option value="<?php echo $categories['category_id'] ?>"
                                            <?php if ($product->category_id == $categories['category_id']) echo 'selected'; ?>>
                                            <?php echo $categories['category_name'] ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Sub<?php echo display('category') ?></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="subcategory_id" name="subcategory_id" tabindex="3">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 5: Brand | Origin of Product -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Brand</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="brand_id" name="brand_id" tabindex="3">
                                    <option value=""></option>
                                    <?php if ($brand_list) { foreach ($brand_list as $categories) { ?>
                                        <option value="<?php echo $categories['brand_id'] ?>"
                                            <?php if ($product->brand_id == $categories['brand_id']) echo 'selected'; ?>>
                                            <?php echo $categories['brand_name'] ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Origin of Product</label>
                            <div class="col-sm-8">
                                <?php echo form_dropdown('oop_id', $country_list, isset($product->oop_id) ? $product->oop_id : '', 'id="oop_id" class="form-control" tabindex="3"') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 6: Product Type | Batch Type -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Product Type <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="product_type" name="product_type" required tabindex="3">
                                    <option value=""></option>
                                    <option value="N/A" <?php if ($product->product_type == "N/A" || (empty($product->id) && empty($product->product_type))) echo 'selected'; ?>>N/A</option>
                                    <option value="Retail Good" <?php if ($product->product_type == "Retail Good") echo 'selected'; ?>>Retail Good</option>
                                    <option value="Finished Good" <?php if ($product->product_type == "Finished Good") echo 'selected'; ?>>Finished Good</option>
                                    <option value="Ingredients" <?php if ($product->product_type == "Ingredients") echo 'selected'; ?>>Ingredients</option>
                                    <option value="Raw Material" <?php if ($product->product_type == "Raw Material") echo 'selected'; ?>>Raw Material</option>
                                    <option value="Packing Material" <?php if ($product->product_type == "Packing Material") echo 'selected'; ?>>Packing Material</option>
                                    <option value="MRO" <?php if ($product->product_type == "MRO") echo 'selected'; ?>>MRO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Batch Type <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="batchtype" name="batchtype" required tabindex="-1">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($product->batchtype == 1) ? 'selected' : ''; ?>>Single</option>
                                    <option value="2" <?php echo ($product->batchtype == 2) ? 'selected' : ''; ?>>Multiple</option>
                                    <option value="3" <?php echo ($product->batchtype == 3 || (empty($product->id) && empty($product->batchtype))) ? 'selected' : ''; ?>>Both</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 7: Default Store | Default Sale Price -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Default Store <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="store" name="store" tabindex="3">
                                    <option value="1" <?php if ($product->store == 1 || empty($product->id)) echo 'selected'; ?>>N/A</option>
                                    <?php if ($store_list) { foreach ($store_list as $categories) { ?>
                                        <option value="<?php echo $categories['id'] ?>"
                                            <?php if ($product->store == $categories['id']) echo 'selected'; ?>>
                                            <?php echo $categories['name'] ?></option>
                                    <?php } } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Default Sale Price <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="defaultsaleprice" required name="defaultsaleprice" tabindex="3">
                                    <option value=""></option>
                                    <option value="fixedprice" <?php if ($product->defaultsaleprice == "fixedprice") echo 'selected'; ?>>Fixed Price</option>
                                    <option value="mrp" <?php if ($product->defaultsaleprice == "mrp") echo 'selected'; ?>>MRP</option>
                                    <option value="custom" <?php if ($product->defaultsaleprice == "custom" || (empty($product->id) && empty($product->defaultsaleprice))) echo 'selected'; ?>>Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 8: Product VAT % | Status -->
                <div class="row">
                    <?php if ($vtinfo->ischecked == 1) { ?>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Product VAT %</label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="vat" name="vat" type="number"
                                    placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->product_vat ?>">
                            </div>
                        </div>
                    </div>
                    <?php } else { ?>
                        <input type="hidden" name="vat" id="vat" value="0.0">
                    <?php } ?>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Status <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" required tabindex="-1">
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($product->status_label == "Active" || (empty($product->id) && empty($product->status_label))) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($product->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Master Stock And Substock</span>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group row">
                            <label for="unit" class="col-xs-12 col-sm-4 col-form-label">Master Stock <?php echo display('unit') ?> <i class="text-danger">*</i></label>
                            <div class="col-xs-12 col-sm-8">
                                <select class="form-control" id="unit" name="unit" required tabindex="-1">
                                    <option value="">Select One</option>
                                    <?php if ($unit_list) { ?>
                                        <?php foreach ($unit_list as $units) { ?>
                                            <option value="<?php echo $units['unit_id'] ?>" <?php if ($product->unit == $units['unit_id']) {
                                                                                                echo 'selected';
                                                                                            } ?>>
                                                <?php echo $units['unit_name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group row">
                            <label for="cost_price" class="col-xs-12 col-sm-4 col-form-label">Fixed Purchase Price <span class="text-danger">*</span>
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input class="form-control text-right" id="cost_price" name="cost_price" type="number"
                                    placeholder="0.00" tabindex="5" min="0.01" step="any" required value="<?php echo $product->cost_price ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                        <div class="form-group row">
                            <label for="sell_price" class="col-xs-12 col-sm-4 col-form-label">Fixed Sale Price
                            </label>
                            <div class="col-xs-12 col-sm-8">
                                <input class="form-control text-right" id="sell_price" name="sell_price" type="number"
                                    placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->price ?>">
                            </div>
                        </div>
                    </div>






                </div>



                <button class="btn btn-add-substock" data-toggle="modal" data-target="#entryModal"><i class="fa fa-plus" style="margin-right:6px;"></i>Add Substock</button>
                <br />
                <br />

                <div class="table-responsive">
                <table class="table table-bordered table-striped table-condensed" id="dataTable">
                    <thead>
                        <tr>
                            <th>Substock Unit</th>
                            <th>Conversion Ratio</th>
                            <th>Sub Purchase Price</th>
                            <th>Sub Sale Price</th>
                            <th>Primary <i class="text-danger">*</i></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- New rows will be added here -->
                    </tbody>
                </table>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Stock Level And Supply Settings</span>
                </div>
            </div>
            <div class="panel-body">
                <!-- Row 1: Supplier | Stock -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_id" class="col-sm-4 col-form-label">Supplier
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="supplier_id" name="supplier_id" tabindex="3">
                                    <option value=""></option>
                                    <?php if ($supplier) { ?>
                                        <?php foreach ($supplier as $sup) { ?>
                                            <option value="<?php echo $sup['supplier_id'] ?>" <?php if ($product->supplier_id == $sup['supplier_id']) {
                                                                                                    echo 'selected';
                                                                                                } ?>>
                                                <?php echo $sup['supplier_name'] ?></option>

                                    <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="stock" class="col-sm-4 col-form-label">Stock <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="stock" name="stock" required tabindex="-1">
                                    <option value="1" <?php echo ($product->stock == "1" || (empty($product->id) && $product->stock !== "0")) ? 'selected' : ''; ?>>Enable
                                    </option>
                                    <option value="0" <?php echo ($product->stock == "0") ? 'selected' : ''; ?>>
                                        Disable
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 2: Max Stock Level | Min Stock Level -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="max_stock_level" class="col-sm-4 col-form-label">Max. Stock Level
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="max_stock_level" name="max_stock_level"
                                    type="number" step="any" min="0" placeholder="0.00" tabindex="5"
                                    value="<?php echo isset($product->max_stock_level) ? $product->max_stock_level : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="min_stock_level" class="col-sm-4 col-form-label">Min. Stock Level
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="min_stock_level" name="min_stock_level"
                                    type="number" step="any" min="0" placeholder="0.00" tabindex="5"
                                    value="<?php echo isset($product->min_stock_level) ? $product->min_stock_level : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row 3: Reorder Stock Level | Reserve Stock Level -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="reorder_stock_level" class="col-sm-4 col-form-label">Reorder Stock Level
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="reorder_stock_level" name="reorder_stock_level"
                                    type="number" step="any" min="0" placeholder="0.00" tabindex="5"
                                    value="<?php echo isset($product->reorder_stock_level) ? $product->reorder_stock_level : ''; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="reserve_stock_level" class="col-sm-4 col-form-label">Reserve Stock Level
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control text-right" id="reserve_stock_level" name="reserve_stock_level"
                                    type="number" step="any" min="0" placeholder="0.00" tabindex="5"
                                    value="<?php echo isset($product->reserve_stock_level) ? $product->reserve_stock_level : ''; ?>">
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<?php if (!empty($product_image_upload)): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Product Image</span>
                </div>
            </div>
            <div class="panel-body">

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Product Image</label>
                    <div class="col-sm-10">
                        <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                            <div id="img_preview_wrap" style="width:180px; height:180px; border:2px dashed #d0d7e6; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#f7f9fc; overflow:hidden; flex-shrink:0;">
                                <?php if (!empty($product->product_image)): ?>
                                    <img id="img_preview" src="<?php echo base_url($product->product_image); ?>" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <img id="img_preview" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                    <i class="fa fa-image" id="img_placeholder_icon" style="font-size:56px; color:#ccc;"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <input type="file" id="product_image_file" accept="image/*" style="display:none;" onchange="handleProductImage(this)">
                                <input type="hidden" id="product_image_data" name="product_image_data">
                                <button type="button" class="btn btn-default btn-sm" onclick="document.getElementById('product_image_file').click()">
                                    <i class="fa fa-upload"></i> Choose Image
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" id="btn_remove_img" onclick="removeProductImage()" style="<?php echo empty($product->product_image) ? 'display:none;' : ''; ?>">
                                    <i class="fa fa-times"></i> Remove
                                </button>
                                <p class="text-muted" style="font-size:11px; margin-top:6px; margin-bottom:0;">Auto-compressed to lightweight JPEG. Max display: 300×300px.</p>
                            </div>
                        </div>
                        <canvas id="img_canvas" style="display:none;"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-body">
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="submit" class="btn btn-success" name="save" onclick="save('save')">
                            <i class="fa fa-save"></i> <?php echo (empty($id) ? display('save') : display('update')) ?></button>
                        <?php if (empty($id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another" onclick="save('save_add')">
                                <i class="fa fa-save"></i> <?php echo display('save_and_add_another'); ?>
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="entryModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="entryModalLabel">Add Sub stock</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="nameInput">Substock Unit</label>
                    <select class="form-control" id="subunit" name="subunit" required tabindex="-1">
                        <option value="">Select One</option>
                        <?php if ($unit_list) { ?>
                            <?php foreach ($unit_list as $units) { ?>
                                <option value="<?php echo $units['unit_id'] ?>" <?php if ($product->subunit == $units['unit_name']) {
                                                                                    echo 'selected';
                                                                                } ?>>
                                    <?php echo $units['unit_name'] ?></option>

                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Conversion Ratio <small class="text-muted">(how many sub-units = 1 master unit)</small></label>
                    <input class="form-control text-right" id="conversion_ratio" name="conversion_ratio" type="number"
                        placeholder="e.g. 12" min="0" step="any">
                </div>
                <div class="form-group">
                    <label>Sub Purchase Price</label>
                    <input class="form-control text-right" id="subcost_price" name="subcost_price" type="number"
                        placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->subcost_price ?>">
                </div>
                <div class="form-group">
                    <label>Sub Sale Price</label>
                    <input class="form-control text-right" id="subsell_price" name="subsell_price" type="number"
                        placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->subsell_price ?>">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="addEntry()">Add</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="entryModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title" id="entryModalLabel">Update Sub stock</h4>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="nameInput">Substock Unit</label>
                    <select class="form-control" id="up_subunit" name="subunit" required tabindex="-1"
                        disabled>
                        <option value="">Select One</option>
                        <?php if ($unit_list) { ?>
                            <?php foreach ($unit_list as $units) { ?>
                                <option value="<?php echo $units['unit_id'] ?>" <?php if ($product->subunit == $units['unit_name']) {
                                                                                    echo 'selected';
                                                                                } ?>>
                                    <?php echo $units['unit_name'] ?></option>

                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Conversion Ratio <small class="text-muted">(how many sub-units = 1 master unit)</small></label>
                    <input class="form-control text-right" id="up_conversion_ratio" name="up_conversion_ratio" type="number"
                        placeholder="e.g. 12" min="0" step="any">
                    <small id="ratio-lock-msg" class="text-danger" style="display:none;">
                        <i class="fa fa-lock"></i> This ratio is used in stock records and cannot be changed.
                    </small>
                </div>
                <div class="form-group">
                    <label for="emailInput">Sub Purchase Price</label>
                    <input class="form-control text-right" id="up_id" name="subcost_price" type="hidden">
                    <input class="form-control text-right" id="up_subcost_price" name="subcost_price" type="text"
                        placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->subcost_price ?>">
                </div>
                <div class="form-group">
                    <label for="ageInput">Sub Sale Price</label>
                    <input class="form-control text-right" id="up_subsell_price" name="subsell_price" type="text"
                        placeholder="0.00" tabindex="5" min="0" value="<?php echo $product->subsell_price ?>">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="updateEntry()">Update</button>
            </div>




        </div>
    </div>
</div>

<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "var floorId = " . json_encode($product->floor) . ";";
echo "var storeId = " . json_encode($product->store) . ";";
echo "var unit_list = " . json_encode($unit_list) . ";";
echo "var subcategory_list = " . json_encode($subcategory_list ?: []) . ";";
echo "var subcategory_id = " . json_encode($product->subcategory_id) . ";";
echo "var category_id = " . json_encode($product->category_id) . ";";
echo "var subunit_product = " . json_encode($subunit_product) . ";";
echo "var subunit_conversions = " . json_encode($subunit_conversions) . ";";


echo "</script>";
?>
<script>
    var entries = [];
    var deletedentries = [];

    /* ── Uniqueness flags ── */
    var productNameDuplicate = false;
    var productCodeDuplicate = false;

    function checkUnique(field, value, $el, label, onResult) {
        if (!value) return onResult && onResult(false);
        $.post($('#baseUrl2').val() + 'product/product/check_product_unique', {
            field: field,
            value: value,
            exclude_id: id || 0
        }, function(res) {
            var r = JSON.parse(res);
            if (r.exists) {
                $el.addClass('field-duplicate');
                alert(label + ' already exists. Please use a different value.');
            } else {
                $el.removeClass('field-duplicate');
            }
            onResult && onResult(r.exists);
        });
    }



    $(document).ready(function() {
        $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});

        wireDuplicateCheck({
            input: '#product_name',
            url: $('#baseUrl2').val() + 'product/product/check_duplicate_product',
            saveButtons: 'button[type="submit"]',
            excludeId: (id > 0 ? id : null)
        });

        if (id > 0) {
            let subcat = (Array.isArray(subcategory_list) ? subcategory_list : []).filter(sub => sub.category_id == category_id)

            var $subunitDropdown = $('#subcategory_id');
            $subunitDropdown.empty();
            $subunitDropdown.append(
                '<option value="" disabled selected>Select subcategory</option>'); // Add default option

            $.each(subcat, function(index, store) {
                $subunitDropdown.append('<option value="' + store.subcategory_id + '">' + store
                    .subcategory_name + '</option>');
            });
            $subunitDropdown.val(subcategory_id)
            console.log(subunit_conversions)
            console.log(subunit_product)

            subunit_product.forEach(en => {
                var entry = {
                    id: en.id,
                    subunitid: en.unit_id,
                    subunit: en.unit_name,
                    subcost_price: en.subcost_price,
                    subsell_price: en.subsell_price,
                    conversion_ratio: en.conversion_ratio || '',
                    is_ratio_locked: en.is_ratio_locked == true || en.is_ratio_locked == 1,
                    selected: en.first == 1 ? true : false,
                    selectedInt: en.first
                };

                entries.push(entry);
                var index = entries.length - 1;

                var checkedAttr = entry.selected ? 'checked' : '';
                const exists = subunit_conversions.some(
                    item => item.subunit === en.unit_id
                );

                var ratioDisplay = entry.conversion_ratio
                    ? (entry.conversion_ratio + (entry.is_ratio_locked ? ' <i class="fa fa-lock text-danger" title="Used in stock — cannot change ratio"></i>' : ''))
                    : '—';

                var newRow = '<tr data-index="' + index + '">' +
                    '<td>' + entry.subunit + '</td>' +
                    '<td>' + ratioDisplay + '</td>' +
                    '<td>' + entry.subcost_price + '</td>' +
                    '<td>' + entry.subsell_price + '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' +
                    '<input type="checkbox" class="row-checkbox" ' + checkedAttr +
                    ' style="margin-right: 6px; transform: scale(1.5); vertical-align: middle;">' +
                    '</td>' +
                    '<td class="text-center" style="vertical-align: middle;">' +
                    '<button class="btn btn-info btn-xs edit-btn" ' +
                    'data-entry=\'' + JSON.stringify(entry) + '\'>' +
                    '<i class="fa fa-pencil" aria-hidden="true"></i></button>';

                if (!exists) {
                    newRow = newRow +
                        '<button class="btn btn-danger btn-xs delete-btn" style="vertical-align: middle;">' +
                        '<i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                }
                newRow = newRow + '</td>' + '</tr>';

                $('#dataTable tbody').append(newRow);
            });

        } else {
            $('#sell_price').val("")

        }

        // Uniqueness checks on blur
        $('#product_name').on('input', function() {
            $(this).removeClass('field-duplicate');
            productNameDuplicate = false;
        }).on('blur', function() {
            var val = $(this).val().trim();
            if (!val) return;
            checkUnique('product_name', val, $(this), 'Product Name', function(exists) {
                productNameDuplicate = exists;
            });
        });

        $('#product_id').on('input', function() {
            $(this).removeClass('field-duplicate');
            productCodeDuplicate = false;
        }).on('blur', function() {
            var val = $(this).val().trim();
            if (!val) return;
            checkUnique('product_id', val, $(this), 'Product Code', function(exists) {
                productCodeDuplicate = exists;
            });
        });

        // Auto-calc sub prices from master price ÷ conversion ratio (Add modal)
        $(document).on('input', '#conversion_ratio', function() {
            var ratio = parseFloat($(this).val());
            if (ratio > 0) {
                var masterCost = parseFloat($('#cost_price').val()) || 0;
                var masterSell = parseFloat($('#sell_price').val()) || 0;
                $('#subcost_price').val((masterCost / ratio).toFixed(2));
                $('#subsell_price').val((masterSell / ratio).toFixed(2));
            }
        });

        // Auto-calc sub prices from master price ÷ conversion ratio (Edit modal)
        $(document).on('input', '#up_conversion_ratio', function() {
            if (currentEditEntry && !currentEditEntry.is_ratio_locked) {
                var ratio = parseFloat($(this).val());
                if (ratio > 0) {
                    var masterCost = parseFloat($('#cost_price').val()) || 0;
                    var masterSell = parseFloat($('#sell_price').val()) || 0;
                    $('#up_subcost_price').val((masterCost / ratio).toFixed(2));
                    $('#up_subsell_price').val((masterSell / ratio).toFixed(2));
                }
            }
        });

    });

    var currentEditEntry = null;

    function editRow(entry) {
        currentEditEntry = entry;

        $('#updateModal').modal('show');

        var $subunitDropdown = $('#up_subunit');
        $subunitDropdown.empty();

        $.each(unit_list, function(index, store) {
            $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
        });

        $subunitDropdown.val(entry.subunitid);
        $('#up_id').val(entry.id);
        $('#up_subcost_price').val(entry.subcost_price);
        $('#up_subsell_price').val(entry.subsell_price);

        var $ratioField = $('#up_conversion_ratio');
        $ratioField.val(entry.conversion_ratio || '');

        if (entry.is_ratio_locked) {
            $ratioField.prop('readonly', true)
                       .css({'background-color': '#f5f5f5', 'cursor': 'not-allowed', 'border-color': '#d9534f'});
            $('#ratio-lock-msg').show();
        } else {
            $ratioField.prop('readonly', false)
                       .css({'background-color': '', 'cursor': '', 'border-color': ''});
            $('#ratio-lock-msg').hide();
        }
    }

    function updateEntry() {
        var isLocked = currentEditEntry && currentEditEntry.is_ratio_locked;
        $.ajax({
            url: $('#baseUrl2').val() + 'product/product/update_subunit',
            type: 'POST',
            data: {
                id: $('#up_id').val(),
                unit_id: currentEditEntry ? currentEditEntry.subunitid : '',
                product_id: id,
                conversion_ratio: isLocked ? 0 : ($('#up_conversion_ratio').val() || 0),
                ratio_locked: isLocked ? 1 : 0,
                subcost_price: $('#up_subcost_price').val() || 0,
                subsell_price: $('#up_subsell_price').val() || 0,
            },
            success: function(response) {
                alert("Subunit Updated Successfully");
                window.location.reload();
            },
            error: function(error) {
                console.log(error);
            }
        });
    }


    function changecategory() {
        let subcat = (Array.isArray(subcategory_list) ? subcategory_list : []).filter(sub => sub.category_id == $('#category_id').val())

        var $subunitDropdown = $('#subcategory_id');
        $subunitDropdown.empty();
        $subunitDropdown.append('<option value="" disabled selected>Select subcategory</option>'); // Add default option

        $.each(subcat, function(index, store) {
            $subunitDropdown.append('<option value="' + store.subcategory_id + '">' + store.subcategory_name +
                '</option>');
        });
    }

    function addEntry() {
        var subunitid = $('#subunit').val().trim();
        var subunit = $('#subunit option:selected').text();
        var conversion_ratio = $('#conversion_ratio').val().trim();
        var subcost_price = $('#subcost_price').val().trim();
        var subsell_price = $('#subsell_price').val().trim();

        let entry1 = entries.find(entry => entry.subunitid == subunitid);

        if (entry1) {
            alert("Substock Unit already exists");
            return;
        }
        if (subunit) {
            var entry = {
                id: 0,
                subunitid: subunitid,
                subunit: subunit,
                conversion_ratio: conversion_ratio || 0,
                subcost_price: subcost_price ? subcost_price : 0,
                subsell_price: subsell_price ? subsell_price : 0,
                selected: false,
                selectedInt: 0
            };

            entries.push(entry);
            var index = entries.length - 1;

            var newRow = '<tr data-index="' + index + '">' +
                '<td>' + entry.subunit + '</td>' +
                '<td>' + (entry.conversion_ratio || '—') + '</td>' +
                '<td>' + entry.subcost_price + '</td>' +
                '<td>' + entry.subsell_price + '</td>' +
                '<td class="text-center" style="vertical-align: middle;"><input type="checkbox" class="row-checkbox" style="margin-right: 6px; transform: scale(1.5); vertical-align: middle;"></td>' +
                '<td class="text-center" style="vertical-align: middle;"><button class="btn btn-danger btn-xs delete-btn" style="vertical-align: middle;"><i class="fa fa-trash-o" aria-hidden="true"></i></button></td>' +
                '</tr>';

            $('#dataTable tbody').append(newRow);
        } else {
            alert("Substock Unit is required");
        }
        var $subunitDropdown = $('#subunit');
        $subunitDropdown.empty();
        $subunitDropdown.append('<option value="" disabled selected>Select substock unit</option>');
        $.each(unit_list, function(index, store) {
            $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
        });
        $('#conversion_ratio').val('');
        $('#subcost_price').val('');
        $('#subsell_price').val('');
    }
    $(document).on('click', '.delete-btn', function() {
        var row = $(this).closest('tr');
        var index = row.data('index');
        if (entries[index].id != 0) {
            if (confirm("Are you sure you want to delete this subunit?")) {
                $.ajax({
                    url: $('#baseUrl2').val() + 'product/product/delete_subunit',
                    type: 'POST',
                    data: {
                        id: entries[index].id,
                    },
                    success: function(response) {
                        alert("Subunit Deleted Successfully")
                        window.location.reload();


                    },
                    error: function(error) {
                        console.log(error)
                    }
                });
                entries.splice(index, 1);
                row.remove();
                $('#dataTable tbody tr').each(function(i) {
                    $(this).attr('data-index', i);
                });


            }



        } else {
            if (confirm("Are you sure you want to delete this subunit?")) {
                entries.splice(index, 1);
                row.remove();
                $('#dataTable tbody tr').each(function(i) {
                    $(this).attr('data-index', i);
                });
            }

        }

    });

    $(document).on('click', '.edit-btn', function() {
        const entry = $(this).data('entry');
        editRow(entry);

        var $subunitDropdown = $('#unit' + item);
    });


    $(document).on('change', '.row-checkbox', function() {
        $('.row-checkbox').not(this).prop('checked', false);
        entries.forEach(function(entry) {
            entry.selected = false;
            entry.selectedInt = 0;
        });
        if ($(this).is(':checked')) {
            var row = $(this).closest('tr');
            var index = row.data('index');
            entries[index].selected = true;
            entries[index].selectedInt = 1;
        } else {
            console.log("All checkboxes are now unchecked.");
        }
    });

    function save(value) {

        if ($('#product_name').val() == "" || $('#product_name').val() == null) {
            alert("Product Name is required");
            return;
        }
        if (productNameDuplicate) {
            alert("Product Name already exists. Please use a different name.");
            $('#product_name').focus();
            return;
        }
        if (productCodeDuplicate) {
            alert("Product Code already exists. Please use a different code.");
            $('#product_id').focus();
            return;
        }

        if ($('#category_id').val() == "" || $('#category_id').val() == null) {
            alert("Category is required");
            return;
        }
        if ($('#store').val() == "" || $('#store').val() == null) {
            alert("Default Store is required");
            return;
        }
        if ($('#defaultsaleprice').val() == "" || $('#defaultsaleprice').val() == null) {
            alert("Default Sale Price is required");
            return;
        }
        if ($('#status').val() == "" || $('#status').val() == null) {
            alert("Status is required");
            return;
        }

        if ($('#unit').val() == "" || $('#unit').val() == null) {
            alert("Master Stock Unit is required");
            return;
        }
        if ($('#batchtype').val() == "" || $('#batchtype').val() == null) {
            alert("Batch Type is required");
            return;
        }
        if ($('#product_type').val() == "" || $('#product_type').val() == null) {
            alert("Product Type is required");
            return;
        }
        if ($('#stock').val() == "" || $('#stock').val() == null) {
            alert("Stock field is required");
            return;
        }
        if ($('#cost_price').val() == "" || $('#cost_price').val() == null || parseFloat($('#cost_price').val()) <= 0) {
            alert("Fixed Purchase Price is required and must be greater than 0");
            $('#cost_price').focus();
            return;
        }

        let subcat = entries.filter(ent => ent.subunitid == $('#unit').val())

        if (subcat.length > 0) {
            alert("Substock Unit cannot be the same as Master Stock Unit");
            return;
        }


        if (entries.length > 0) {
            const count = entries.filter(
                ent => ent.selected == true
            ).length;

            if (count == 0) {
                alert("Please mark one substock as Primary");
                return;
            }
        }


        if (id > 0) {
            $.ajax({
                url: $('#baseUrl2').val() + 'product/product/update_product',
                type: 'POST',
                data: {
                    id: id,
                    product_id: document.getElementById('product_id').value,
                    product_name: document.getElementById('product_name').value,
                    serial_no: document.getElementById('serial_no').value,
                    category_id: document.getElementById('category_id').value,
                    subcategory_id: document.getElementById('subcategory_id').value,
                    brand_id: document.getElementById('brand_id').value,
                    oop_id: document.getElementById('oop_id').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    product_type: document.getElementById('product_type').value,
                    store: document.getElementById('store').value,
                    vat: document.getElementById('vat').value,
                    defaultsaleprice: document.getElementById('defaultsaleprice').value,
                    product_model: document.getElementById('product_model').value,
                    description: document.getElementById('description').value,
                    unit: document.getElementById('unit').value,
                    status: document.getElementById('status').value,
                    stock: document.getElementById('stock').value,
                    max_stock_level: document.getElementById('max_stock_level').value,
                    min_stock_level: document.getElementById('min_stock_level').value,
                    reorder_stock_level: document.getElementById('reorder_stock_level').value,
                    reserve_stock_level: document.getElementById('reserve_stock_level').value,
                    cost_price: document.getElementById('cost_price').value ? document.getElementById('cost_price')
                        .value : 0,
                    sell_price: document.getElementById('sell_price').value ? document.getElementById('sell_price')
                        .value : 0,
                    batchtype: document.getElementById('batchtype').value,
                    printname: document.getElementById('printname').value,
                    ad: "",
                    bd: "",
                    entries: entries
                },
                success: function(response) {
                    let result = JSON.parse(response);

                    if (result && result.status === "Success") {
                        uploadProductImage(id).always(function() {
                            alert("Product Updated Successfully");
                            window.location.href = $('#base_url').val() + 'product_list';
                        });
                    } else {
                        alert("Error: " + (result && result.message ? result.message : JSON.stringify(result)));
                    }
                },
                error: function(error) {
                    console.log(error);
                    alert("An error occurred while updating the product");
                }
            });
        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'product/product/save_product',
                type: 'POST',
                data: {
                    product_id: document.getElementById('product_id').value,
                    product_name: document.getElementById('product_name').value,
                    serial_no: document.getElementById('serial_no').value,
                    category_id: document.getElementById('category_id').value,
                    subcategory_id: document.getElementById('subcategory_id').value,
                    brand_id: document.getElementById('brand_id').value,
                    oop_id: document.getElementById('oop_id').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    product_type: document.getElementById('product_type').value,
                    store: document.getElementById('store').value,
                    vat: document.getElementById('vat').value,
                    defaultsaleprice: document.getElementById('defaultsaleprice').value,
                    product_model: document.getElementById('product_model').value,
                    description: document.getElementById('description').value,
                    unit: document.getElementById('unit').value,
                    status: document.getElementById('status').value,
                    stock: document.getElementById('stock').value,
                    max_stock_level: document.getElementById('max_stock_level').value,
                    min_stock_level: document.getElementById('min_stock_level').value,
                    reorder_stock_level: document.getElementById('reorder_stock_level').value,
                    reserve_stock_level: document.getElementById('reserve_stock_level').value,
                    cost_price: document.getElementById('cost_price').value,
                    sell_price: document.getElementById('sell_price').value,
                    batchtype: document.getElementById('batchtype').value,
                    printname: document.getElementById('printname').value,
                    ad: "",
                    bd: "",
                    entries: entries
                },
                success: function(response) {
                    let result = JSON.parse(response);

                    if (result && result.status === "Success") {
                        uploadProductImage(result.id).always(function() {
                            alert("Product Saved Successfully");
                            if (value == "save_add") {
                                window.location.href = $('#base_url').val() + 'product_form';
                            } else {
                                window.location.href = $('#base_url').val() + 'product_list';
                            }
                        });
                    } else {
                        alert("Error: " + (result && result.message ? result.message : JSON.stringify(result)));
                    }
                },
                error: function(error) {
                    console.log(error);
                    alert("An error occurred while saving the product");
                }
            });
        }
    }
</script>


<script>
    if (floorId > 0) {
        onChangeStore(storeId, floorId);

    }
    let code = 0;

    if (id != null) {
        code = document.getElementById("product_id").value.toString();
    }

    // function validateForm(event) {
    //     // Prevent default form submission
    //     event.preventDefault();

    //     // Identify which button was clicked
    //     const buttonName = event.submitter.name; // Get the name of the button that was clicked
    //     async function checkProduct() {
    //         try {
    //             let response = await $.ajax({
    //                 type: "POST",
    //                 url: $('#baseUrl2').val() + 'product/product/getProductById',
    //                 data: {
    //                     code: document.getElementById('product_id').value.toString().padStart(6, '0'),
    //                 }
    //             });

    //             let data = JSON.parse(response);
    //             if (data === "success") {
    //                 return true;
    //             } else {
    //                 if (code == document.getElementById('product_id').value.toString().padStart(6, '0')) {
    //                     return true;
    //                 } else {
    //                     alert("Product code already exists");
    //                     return false;
    //                 }
    //             }

    //         } catch (error) {
    //             alert("An error occurred: " + error);
    //             return false;
    //         }
    //     }

    //     checkProduct().then((isValid) => {
    //         if (isValid) {
    //             if (buttonName === 'save') {
    //                 document.getElementById('button').value = "save";
    //                 document.getElementById('insert_product').submit();
    //             } else if (buttonName === 'add-another') {
    //                 document.getElementById('button').value = "add-another";
    //                 document.getElementById('insert_product').submit();
    //             }
    //         } else {
    //             return false;
    //         }
    //     });
    // }

let productImageBlob = null;   // holds compressed Blob ready to upload
let productImageRemoved = false;

function handleProductImage(input) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            const MAX = 300;
            let w = img.width, h = img.height;
            if (w > h) { if (w > MAX) { h = Math.round(h * MAX / w); w = MAX; } }
            else        { if (h > MAX) { w = Math.round(w * MAX / h); h = MAX; } }
            const canvas = document.getElementById('img_canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            canvas.toBlob(function(blob) {
                productImageBlob    = blob;
                productImageRemoved = false;
                const preview = document.getElementById('img_preview');
                preview.src = URL.createObjectURL(blob);
                preview.style.display = 'block';
                const icon = document.getElementById('img_placeholder_icon');
                if (icon) icon.style.display = 'none';
                document.getElementById('btn_remove_img').style.display = '';
            }, 'image/jpeg', 0.75);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function uploadProductImage(productId) {
    if (!productImageBlob && !productImageRemoved) return $.when(); // jQuery no-op deferred
    const fd = new FormData();
    fd.append('product_id', productId);
    if (productImageBlob) {
        fd.append('product_image', productImageBlob, productId + '.jpg');
    } else {
        fd.append('remove_image', '1');
    }
    return $.ajax({
        url: $('#baseUrl2').val() + 'product/product/upload_product_image',
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false
    });
}

function removeProductImage() {
    productImageBlob    = null;
    productImageRemoved = true;
    document.getElementById('product_image_file').value = '';
    const preview = document.getElementById('img_preview');
    preview.src = ''; preview.style.display = 'none';
    const icon = document.getElementById('img_placeholder_icon');
    if (icon) icon.style.display = '';
    document.getElementById('btn_remove_img').style.display = 'none';
}
</script>
