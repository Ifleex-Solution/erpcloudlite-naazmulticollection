<!-- New Service -->
<style>
.panel.panel-bd.lobidrag{border:none !important;box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important;border-radius:14px !important;overflow:hidden !important}
.panel.panel-bd.lobidrag .panel-heading{background:#fff !important;padding:14px 24px !important;border:none !important;border-bottom:2px solid #F1F5F9 !important}
.panel.panel-bd.lobidrag .panel-title{display:flex !important;align-items:center !important;justify-content:space-between !important;flex-wrap:wrap !important;gap:10px !important;margin:0 !important}
.panel.panel-bd.lobidrag .panel-title > span:first-child,.panel.panel-bd.lobidrag .panel-title h4{color:#1E293B !important;font-size:15px !important;font-weight:600 !important;letter-spacing:.3px !important;margin:0 !important}
.panel.panel-bd.lobidrag .panel-body{padding:28px 32px !important;background:#fff !important}
.panel.panel-bd.lobidrag .col-form-label{font-size:13px !important;font-weight:600 !important;color:#374151 !important;text-align:right !important;padding-right:14px !important;padding-top:8px !important}
.panel.panel-bd.lobidrag .form-control{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;padding:8px 12px !important;font-size:13px !important;color:#374151 !important;background:#F8FAFC !important;height:auto !important;transition:border-color .16s,box-shadow .16s,background .16s !important}
.panel.panel-bd.lobidrag .form-control:focus{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.panel.panel-bd.lobidrag .btn.btn-success{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:9px 24px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;letter-spacing:.3px !important;transition:background .16s,box-shadow .16s !important;margin-left:6px !important}
.panel.panel-bd.lobidrag .btn.btn-success:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}
@media(max-width:767px){
  .panel.panel-bd.lobidrag .panel-body{padding:16px !important}
  .panel.panel-bd.lobidrag .col-form-label{text-align:left !important;padding-top:0 !important;padding-right:0 !important;padding-bottom:4px !important}
  .panel.panel-bd.lobidrag .btn.btn-success{width:100% !important;margin:6px 0 0 0 !important}
  .panel.panel-bd.lobidrag .text-right{text-align:left !important}
}
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
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('add_service') ?> </span>
                    <a href="<?php echo base_url('manage_service') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>
            <?php echo form_open('service/service/insert_service', array('class' => 'form-vertical', 'id' => 'insert_service', 'name' => 'insert_service', 'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">


                <div class="form-group row">
                    <label for="service_code" class="col-sm-2 col-form-label">Service ID <i class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input class="form-control" name="service_code" id="service_code" type="text" required
                            placeholder="Service ID" value="<?php echo isset($service_code)?html_escape($service_code):''; ?>">
                    </div>
                </div>
                

                <div class="form-group row">
                    <label for="service_name" class="col-sm-2 col-form-label"><?php echo display('service_name') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input class="form-control" name="service_name" id="service_name" type="text"
                            placeholder="<?php echo display('service_name') ?>" >
                    </div>
                    <input type="hidden" name="button" id="button" />

                </div>

                <div class="form-group row">
                    <label for="charge" class="col-sm-2 col-form-label"><?php echo display('charge') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input class="form-control" name="charge" id="charge" type="text"
                            placeholder="<?php echo display('charge') ?>" >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="service_vat" class="col-sm-2 col-form-label"><?php echo display('service_vat') . ' %' ?> </label>
                    <div class="col-sm-4">
                        <input class="form-control" name="service_vat" id="service_vat" type="text"
                            placeholder="<?php echo display('service_vat') . ' %' ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label"><?php echo display('description') ?> <i
                            class="text-danger"></i></label>
                    <div class="col-sm-4">
                        <textarea class="form-control" name="description" id="description"
                            placeholder="<?php echo display('description') ?>"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Status<i class="text-danger">*</i></label>

                    <div class="col-sm-4">

                        <select class="form-control" id="status" name="status">
                            <option value="">Select One</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select> 


                    </div>
                </div>


                <?php if ($vattaxinfo->dynamic_tax == 1) {
                    $i = 0;
                    foreach ($taxfield as $taxss) { ?>

                        <div class="form-group row">
                            <label for="tax" class="col-sm-3 col-form-label"><?php echo $taxss['tax_name']; ?> <i class="text-danger"></i></label>
                            <div class="col-sm-6">
                                <input type="text" name="tax<?php echo $i; ?>" class="form-control" value="<?php echo number_format($taxss['default_value'], 2, '.', ','); ?>">
                            </div>
                            <div class="col-sm-1"> <i class="text-success">%</i></div>
                        </div>
                <?php $i++;
                    }
                }

                ?>


                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <i class="fa fa-save"></i> <?php echo display('save')  ?></button>

                        <?php if (empty($branch->id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another">
                                <?php echo display('save_and_add_another'); ?>
                            </button>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>

</div>
<script>
$(document).ready(function() {
    $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});

    wireDuplicateCheck({
        input: '#service_name',
        url: $('#base_url').val() + 'service/service/check_duplicate_service',
        saveButtons: 'button[type="submit"]'
    });
});
let code = 0;
if (id != null) { code = document.getElementById("code").value.toString(); }

function validateForm(event) {
    event.preventDefault();
    var buttonName = event.submitter ? event.submitter.name : 'save';

    if (!document.getElementById('charge').value) {
        alert("Charge is required"); return;
    }
    if (!document.getElementById('service_name').value) {
        alert("Service Name is required"); return;
    }
    if (!document.getElementById('status').value) {
        alert("Status is required"); return;
    }
    if (buttonName === 'save') {
        document.getElementById('button').value = "save";
        document.getElementById('insert_service').submit();
    } else if (buttonName === 'add-another') {
        document.getElementById('button').value = "add-another";
        document.getElementById('insert_service').submit();
    }
}
</script>