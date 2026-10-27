<style>
.panel.panel-bd.lobidrag{border:none !important;box-shadow:0 2px 12px rgba(0,0,0,.07),0 6px 24px rgba(0,0,0,.05) !important;border-radius:14px !important;overflow:hidden !important;margin-bottom:20px !important}
.panel.panel-bd.lobidrag .panel-heading{background:#fff !important;padding:14px 24px !important;border:none !important;border-bottom:2px solid #F1F5F9 !important}
.panel.panel-bd.lobidrag .panel-title{display:flex !important;align-items:center !important;justify-content:space-between !important;flex-wrap:wrap !important;gap:10px !important;margin:0 !important}
.panel.panel-bd.lobidrag .panel-title > span:first-child{color:#1E293B !important;font-size:15px !important;font-weight:600 !important;letter-spacing:.3px !important}
.panel.panel-bd.lobidrag .panel-body{padding:24px 28px !important;background:#fff !important}
.frm-label{font-size:13px !important;font-weight:600 !important;color:#374151 !important;margin-bottom:6px !important;display:block !important}
.panel.panel-bd.lobidrag .form-control{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;padding:8px 12px !important;font-size:13px !important;color:#374151 !important;background:#F8FAFC !important;height:auto !important;transition:border-color .16s,box-shadow .16s,background .16s !important}
.panel.panel-bd.lobidrag .form-control:focus{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.panel.panel-bd.lobidrag .btn.btn-success{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:9px 24px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;letter-spacing:.3px !important;transition:background .16s,box-shadow .16s !important;margin-right:8px !important}
.panel.panel-bd.lobidrag .btn.btn-success:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}
@media(max-width:767px){
  .panel.panel-bd.lobidrag .panel-body{padding:16px !important}
  .panel.panel-bd.lobidrag .btn.btn-success{width:100% !important;margin:6px 0 0 0 !important}
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
                    <span><?php echo $title ?></span>
                    <a href="<?php echo base_url('subcategory_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open('subcategory_form/' . $subcategory->subcategory_id, 'class="" id="subcategory_form" novalidate onsubmit="return validateForm(event)"') ?>
                <input type="hidden" name="subcategory_id" id="subcategory_id" value="<?php echo $subcategory->subcategory_id ?>">

                <div class="form-group row">
                    <label for="subcategory_name" class="col-xs-12 col-sm-2 text-right col-form-label">Subcategory Name <i class="text-danger"> * </i>:</label>
                    <div class="col-xs-12 col-sm-4">
                        <input type="text" name="subcategory_name" class="form-control" id="subcategory_name" placeholder="Subcategory Name" value="<?php echo $subcategory->subcategory_name ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="category_id" class="col-xs-12 col-sm-2 text-right col-form-label">Category <i class="text-danger"> * </i>:</label>
                    <div class="col-xs-12 col-sm-4">
                        <select class="form-control" id="category_id" required name="category_id">
                            <option value=""></option>
                            <?php if ($category_list) { foreach ($category_list as $categories) { ?>
                                <option value="<?php echo $categories['category_id'] ?>" <?php if ($subcategory->category_id == $categories['category_id']) { echo 'selected'; } ?>><?php echo $categories['category_name'] ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-xs-12 col-sm-2 text-right col-form-label"><?php echo display('status') ?> <i class="text-danger"> * </i>:</label>
                    <div class="col-xs-12 col-sm-4">
                        <select name="status" id="status" class="form-control" required>
                            <?php if (!empty($subcategory->subcategory_name)) { ?>
                                <option value="1" <?php if ($subcategory->status == 1) { echo 'selected'; } ?>><?php echo display('active') ?></option>
                                <option value="0" <?php if ($subcategory->status == 0) { echo 'selected'; } ?>><?php echo display('inactive') ?></option>
                            <?php } else { ?>
                                <option value="1"><?php echo display('active') ?></option>
                                <option value="0"><?php echo display('inactive') ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-xs-12 col-sm-6 text-right">
                        <button type="submit" name="save" class="btn btn-success">
                            <i class="fa fa-save"></i> <?php echo (empty($subcategory->subcategory_id) ? display('save') : display('update')) ?>
                        </button>
                        <?php if (empty($subcategory->subcategory_id)) { ?>
                            <button type="submit" name="add-another" class="btn btn-success"><i class="fa fa-save"></i> <?php echo display('save_and_add_another') ?></button>
                        <?php } ?>
                    </div>
                </div>

                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php echo "<script>var subcategoryId=" . json_encode(!empty($subcategory->subcategory_id) ? (int)$subcategory->subcategory_id : 0) . ";</script>"; ?>
<script>
var csrf_token;
$(document).ready(function() {
    $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});
    csrf_token = $('#CSRF_TOKEN').val();

    wireDuplicateCheck({
        input: '#subcategory_name',
        url: $('#base_url').val() + 'product/product/check_duplicate_subcategory',
        saveButtons: 'button[type="submit"]',
        excludeId: subcategoryId || null
    });
});
function validateForm(event) {
    event.preventDefault();
    var buttonName      = event.submitter ? event.submitter.name : 'save';
    var subcategoryName = $('#subcategory_name').val().trim();
    var categoryId      = $('#category_id').val();
    var statusVal       = $('#status').val();
    if (!subcategoryName) { alert('Subcategory Name is required'); return; }
    if (!categoryId)      { alert('Category is required'); return; }
    var $btns = $('button[type="submit"]');
    $btns.prop('disabled', true);
    $.ajax({
        type: 'POST',
        url: '<?php echo base_url("product/product/api_save_subcategory"); ?>',
        data: {
            csrf_test_name: csrf_token || $('#CSRF_TOKEN').val(),
            subcategory_id: subcategoryId,
            subcategory_name: subcategoryName,
            category_id: categoryId,
            status: statusVal,
            button: buttonName
        },
        success: function(response) {
            var res;
            try { res = (typeof response === 'object') ? response : JSON.parse(response); }
            catch(e) { alert('Unexpected server response.'); $btns.prop('disabled', false); return; }
            if (res.status === 'success') {
                alert(res.message);
                window.location.href = '<?php echo base_url(); ?>' + res.redirect;
            } else {
                alert(res.message);
                $btns.prop('disabled', false);
            }
        },
        error: function() {
            alert('Something went wrong. Please try again.');
            $btns.prop('disabled', false);
        }
    });
}
</script>
