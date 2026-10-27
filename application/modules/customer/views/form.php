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
        <?php echo form_open('', 'class="" id="customer_form2"') ?>
        <input type="hidden" name="customer_id" id="customer_id" value="<?php echo $customer->customer_id ?>">

        <!-- ===================== Section 1: Basic Information ===================== -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo $title ?></span>
                    <a href="<?php echo base_url('customer_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="customer_name" class="col-sm-4 col-form-label">
                                <?php echo display('customer_name') ?> <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="customer_name" class="form-control" id="customer_name"
                                    placeholder="<?php echo display('customer_name') ?>"
                                    value="<?php echo $customer->customer_name ?>">
                                <input type="hidden" name="old_name" value="<?php echo $customer->customer_name ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="customer_calling_name" class="col-sm-4 col-form-label">
                                Customer Calling Name
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="customer_calling_name"
                                    id="customer_calling_name" placeholder="Customer Calling Name"
                                    value="<?php echo $customer->customer_calling_name ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="customer_billing_name" class="col-sm-4 col-form-label">
                                Customer Printing Name<i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="customer_billing_name"
                                    id="customer_billing_name" placeholder="Customer Printing Name"
                                    value="<?php echo $customer->customer_billing_name ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">
                                Status <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($customer->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($customer->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===================== Section 2: Contact Details ===================== -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Contact Details</span>
                </div>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="customer_mobile" class="col-sm-4 col-form-label">Mobile No</label>
                            <div class="col-sm-8">
                                <input type="text" name="customer_mobile"
                                    class="form-control input-mask-trigger text-left" id="customer_mobile"
                                    placeholder="Mobile No"
                                    value="<?php echo $customer->customer_mobile ?>"
                                    im-insert="true">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="phone" class="col-sm-4 col-form-label">TP Number</label>
                            <div class="col-sm-8">
                                <input class="form-control input-mask-trigger text-left" id="phone" type="text"
                                    name="phone" placeholder="TP Number"
                                    im-insert="true" value="<?php echo $customer->phone ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="email" class="col-sm-4 col-form-label">
                                <?php echo display('email_address') ?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control input-mask-trigger" name="customer_email"
                                    id="email" data-inputmask="'alias': 'email'" im-insert="true"
                                    placeholder="<?php echo display('email') ?>"
                                    value="<?php echo $customer->customer_email ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="nic_no" class="col-sm-4 col-form-label">NIC No</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nic_no" id="nic_no"
                                    placeholder="NIC No" value="<?php echo $customer->nic_no ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="country" class="col-sm-4 col-form-label">
                                <?php echo display('country') ?>
                            </label>
                            <div class="col-sm-8">
                                <input name="country" type="text" class="form-control"
                                    placeholder="<?php echo display('country') ?>"
                                    value="<?php echo $customer->country ?>" id="country">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="state" class="col-sm-4 col-form-label">State / Province</label>
                            <div class="col-sm-8">
                                <input type="text" name="state" class="form-control" id="state"
                                    placeholder="State / Province" value="<?php echo $customer->state ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="city" class="col-sm-4 col-form-label">
                                <?php echo display('city') ?>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="city" class="form-control" id="city"
                                    placeholder="<?php echo display('city') ?>" value="<?php echo $customer->city ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="zip" class="col-sm-4 col-form-label">
                                <?php echo display('zip') ?>
                            </label>
                            <div class="col-sm-8">
                                <input name="zip" type="text" class="form-control" id="zip"
                                    placeholder="<?php echo display('zip') ?>" value="<?php echo $customer->zip ?>">
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        <!-- ===================== Section 3: Address & Registration ===================== -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Address &amp; Registration</span>
                </div>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="customer_address" class="col-sm-4 col-form-label">Primary Address</label>
                            <div class="col-sm-8">
                                <textarea name="customer_address" id="customer_address" class="form-control" rows="3"
                                    placeholder="Primary Address"><?php echo $customer->customer_address ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address2" class="col-sm-4 col-form-label">Secondary Address</label>
                            <div class="col-sm-8">
                                <textarea name="address2" id="address2" class="form-control" rows="3"
                                    placeholder="Secondary Address"><?php echo $customer->address2 ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="contact" class="col-sm-4 col-form-label">BR No</label>
                            <div class="col-sm-8">
                                <input class="form-control" id="contact" type="text" name="contact"
                                    placeholder="BR No" value="<?php echo $customer->contact ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="email_address" class="col-sm-4 col-form-label">VAT No</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email_address" id="email_address"
                                    placeholder="VAT No" value="<?php echo $customer->email_address ?>">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===================== Section 4: Credit Control ===================== -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span>Credit Control</span>
                </div>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="due_date" class="col-sm-4 col-form-label">Due Date (Days)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="due_date" id="due_date"
                                    placeholder="Due Date (Days)" min="0"
                                    value="<?php echo isset($customer->due_date) ? $customer->due_date : 0 ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="branch_limit" class="col-sm-4 col-form-label">Branch Limit</label>
                            <div class="col-sm-8">
                                <input type="number" step="0.01" class="form-control" name="branch_limit" id="branch_limit"
                                    placeholder="Branch Limit" min="0"
                                    value="<?php echo isset($customer->branch_limit) ? $customer->branch_limit : 0 ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="company_limit" class="col-sm-4 col-form-label">Company Limit</label>
                            <div class="col-sm-8">
                                <input type="number" step="0.01" class="form-control" name="company_limit" id="company_limit"
                                    placeholder="Company Limit" min="0"
                                    value="<?php echo isset($customer->company_limit) ? $customer->company_limit : 0 ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="customer_save_btn" onclick="customer_form2()" class="btn btn-success">
                            <i class="fa fa-save"></i> <?php echo (empty($customer->customer_id) ? display('save') : display('update')) ?>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('select.form-control').select2('destroy').select2({placeholder: 'Select option', allowClear: true});

    wireDuplicateCheck({
        input: '#customer_name',
        url: $('#base_url').val() + 'customer/customer/check_duplicate_customer',
        saveButtons: '#customer_save_btn',
        excludeId: <?php echo json_encode(!empty($customer->customer_id) ? $customer->customer_id : null); ?>
    });
});
function customer_form2() {
    var form               = $("#customer_form2");
    var custome_id         = $("#customer_id").val();
    var customer_name      = $("#customer_name").val().trim();
    var status             = $("#status").val();
    var customer_billing_name = $("#customer_billing_name").val().trim();
    var base_url           = $("#base_url").val();
    var form_url           = custome_id !== '' ? base_url + 'edit_customer/' + custome_id : base_url + 'add_customer';

    if (!customer_name) {
        $("#customer_name").focus();
        alert("Customer name is required");
        return false;
    }
    if (!status) {
        alert("Status is required");
        return false;
    }
    if (!customer_billing_name) {
        $("#customer_billing_name").focus();
        alert("Customer Printing Name is required");
        return false;
    }
    $.ajax({
        url: form_url,
        method: 'POST',
        dataType: 'json',
        data: form.serialize(),
        success: function(r) {
            if (r.status == 1) {
                alert(r.msg);
                if (custome_id == '') { $('#customer_form2').trigger("reset"); }
                location.reload();
            } else {
                alert(r.msg);
            }
        },
        error: function() {
            alert('Something went wrong. Please try again.');
        }
    });
}
</script>