<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<style>
.panel.panel-bd.lobidrag {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06) !important;
    border-radius: 14px !important;
    overflow: hidden !important;
}
.panel.panel-bd.lobidrag .panel-heading {
    background: #ffffff !important;
    padding: 14px 24px !important;
    border: none !important;
    border-bottom: 2px solid #F1F5F9 !important;
}
.panel.panel-bd.lobidrag .panel-title {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .panel-title > span:first-child {
    color: #1E293B !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
}
.panel.panel-bd.lobidrag .panel-body {
    padding: 28px 32px !important;
    background: #ffffff !important;
}
.panel.panel-bd.lobidrag .form-group {
    margin-bottom: 16px !important;
    align-items: center !important;
}
.panel.panel-bd.lobidrag .col-form-label {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #374151 !important;
    text-align: right !important;
    padding-right: 14px !important;
    padding-top: 8px !important;
}
.panel.panel-bd.lobidrag .form-control {
    border: 1.5px solid #E2E8F0 !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 13px !important;
    color: #374151 !important;
    background: #F8FAFC !important;
    height: auto !important;
    transition: border-color 0.16s, box-shadow 0.16s, background 0.16s !important;
}
.panel.panel-bd.lobidrag .form-control:focus {
    border-color: #8b5cf6 !important;
    background: #ffffff !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,0.12) !important;
    outline: none !important;
}
.panel.panel-bd.lobidrag .form-control[readonly] {
    background: #F1F5F9 !important;
    color: #94A3B8 !important;
    cursor: not-allowed !important;
}
.panel.panel-bd.lobidrag .btn.btn-success {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 9px 24px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #ffffff !important;
    letter-spacing: 0.3px !important;
    transition: background 0.16s, box-shadow 0.16s !important;
    margin-left: 6px !important;
}
.panel.panel-bd.lobidrag .btn.btn-success:hover {
    background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important;
    box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important;
}
.panel.panel-bd.lobidrag .text-right { text-align: right !important; }
@media (max-width: 767px) {
    .panel.panel-bd.lobidrag .panel-body {
        padding: 16px !important;
    }
    .panel.panel-bd.lobidrag .col-form-label {
        text-align: left !important;
        padding-top: 0 !important;
        padding-right: 0 !important;
        padding-bottom: 4px !important;
    }
    .panel.panel-bd.lobidrag .btn.btn-success {
        width: 100% !important;
        margin: 6px 0 0 0 !important;
    }
    .panel.panel-bd.lobidrag .text-right {
        text-align: left !important;
    }
    .panel.panel-bd.lobidrag .form-group.row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
}
.select2-container .select2-selection--single{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;background:#F8FAFC !important;height:38px !important}
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
                    <span><?php echo $title; ?></span>
                    <a href="<?php echo base_url('branch_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>
            <?php echo form_open_multipart('branch_form/' . $branch->id, array('class' => 'form-vertical', 'id' => 'insert_branch', 'name' => 'insert_branch', 'onsubmit' => 'return validateForm(event)')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="code" class="col-sm-4 col-form-label">Branch Code<i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php if (!empty($branch->id)) { ?>
                                <input class="form-control" name="code" type="text" id="code" placeholder="branch Code"
                                    tabindex="1" value="<?php echo $branch->code ?>" readonly>
                                <?php } else { ?>
                                <input class="form-control" name="code" type="text" id="code" placeholder="branch Code"
                                    tabindex="1" value="<?php echo $branchid ?>">
                                <?php } ?>
                                <input type="hidden" name="button" id="button" />

                            </div>
                        </div>
                    </div>


                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">Branch<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" tabindex="" class="form-control" id="name" name="name"
                                    placeholder="Branch" value="<?php echo $branch->name ?>" />
                            </div>
                        </div>
                    </div>

                </div>
                

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="nature" class="col-sm-4 col-form-label">Branch Nature<i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="nature" name="nature">
                                    <option value="">Select Option</option>
                                    <option value="Retail"
                                        <?php echo (isset($branch->nature) && $branch->nature == "Retail") ? 'selected' : ''; ?>>
                                        Retail</option>
                                    <option value="Wholesale"
                                        <?php echo (isset($branch->nature) && $branch->nature == "Wholesale") ? 'selected' : ''; ?>>
                                        Wholesale</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">Status<i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status">
                                    <option value="">Select One</option>
                                    <option value="1"
                                        <?php echo ($branch->status_label == "Active") ? 'selected' : ''; ?>>Active
                                    </option>
                                    <option value="0"
                                        <?php echo ($branch->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive
                                    </option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>



                <!-- <div class="row"> -->

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <i class="fa fa-save"></i> <?php echo (empty($branch->id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($branch->id)) { ?>
                        <button type="submit" class="btn btn-success" name="add-another">
                            <i class="fa fa-save"></i> <?php echo display('save_and_add_another'); ?>
                        </button>
                        <?php } ?>

                    </div>
                </div>


                <!-- </div> -->
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "var id = " . json_encode($branch->id) . ";";
echo "</script>";
?>

<script>
$(document).ready(function() {
    $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});

    wireDuplicateCheck({
        input: '#name',
        url: $('#baseUrl2').val() + 'store/store/check_duplicate_branch',
        saveButtons: 'button[type="submit"]',
        excludeId: id
    });
});

var csrf_token = $('#CSRF_TOKEN').val();

function validateForm(event) {
    event.preventDefault();
    var buttonName = event.submitter.name;
    var codeVal    = $('#code').val().trim();
    var nameVal    = $('#name').val().trim();
    var natureVal  = $('#nature').val();
    var statusVal  = $('#status').val();

    if (!codeVal)   { alert('Branch Code is required');   return; }
    if (!nameVal)   { alert('Branch Name is required');   return; }
    if (!natureVal) { alert('Branch Nature is required'); return; }
    if (!statusVal) { alert('Status is required');        return; }

    var $btns = $('button[type="submit"]');
    $btns.prop('disabled', true);

    $.ajax({
        type: 'POST',
        url: '<?php echo base_url("store/store/api_save_branch"); ?>',
        data: {
            csrf_test_name: csrf_token,
            id:     id,
            code:   codeVal,
            name:   nameVal,
            nature: natureVal,
            status: statusVal,
            button: buttonName
        },
        success: function(response) {
            var res;
            try { res = (typeof response === 'object') ? response : JSON.parse(response); }
            catch(e) { alert('Unexpected server response. Please try again.'); $btns.prop('disabled', false); return; }

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