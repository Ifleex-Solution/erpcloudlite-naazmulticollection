<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title has-goback">
                    <h4><?php echo $title; ?></h4>
                
<a href="<?php echo base_url('payment_receipt_type_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <?php echo form_open('payment_receipt_type_form/' . $prt->id, [
                'class'    => 'form-vertical',
                'id'       => 'prt_form',
                'name'     => 'prt_form',
                'onsubmit' => 'return validatePrtForm(event)',
            ]); ?>

            <div class="panel-body">

                <!-- Payment/Receipt Name -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">
                                Payment/Receipt Name<i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Payment/Receipt Name"
                                    value="<?php echo htmlspecialchars($prt->name, ENT_QUOTES, 'UTF-8'); ?>" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Type dropdown -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="type" class="col-sm-4 col-form-label">
                                Type<i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Select option</option>
                                    <option value="Payment"  <?php echo ($prt->type === 'Payment')  ? 'selected' : ''; ?>>Payment</option>
                                    <option value="Receipt"  <?php echo ($prt->type === 'Receipt')  ? 'selected' : ''; ?>>Receipt</option>
                                    <option value="Common"   <?php echo ($prt->type === 'Common' || $prt->type === '')   ? 'selected' : ''; ?>>Common</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="detail" class="col-sm-4 col-form-label">Detail</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="detail" name="detail" rows="3"
                                    placeholder="Detail"><?php echo htmlspecialchars($prt->detail ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status dropdown -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">
                                Status<i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" required>
                                    <option value="">Select option</option>
                                    <option value="1" <?php echo ($prt->status == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo (isset($prt->status) && $prt->status == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="form-group row">
                    <div class="col-sm-6 text-right">
                        <input type="hidden" name="button" id="prt_btn_hidden" value="save" />
                        <button type="submit" class="btn btn-success"
                            onclick="document.getElementById('prt_btn_hidden').value='save'">
                            <?php echo empty($prt->id) ? 'Save' : 'Update'; ?>
                        </button>
                        <?php if (empty($prt->id)) { ?>
                        <button type="submit" class="btn btn-success"
                            onclick="document.getElementById('prt_btn_hidden').value='add-another'">
                            Save And Add Another
                        </button>
                        <?php } ?>
                    </div>
                </div>

            </div><!-- /.panel-body -->

            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script>
function validatePrtForm(e) {
    "use strict";
    var name   = document.getElementById('name').value.trim();
    var type   = document.getElementById('type').value;
    var status = document.getElementById('status').value;

    if (!name) {
        alert('Payment/Receipt Name is required.');
        e.preventDefault();
        return false;
    }
    if (!type) {
        alert('Please select a Type.');
        e.preventDefault();
        return false;
    }
    if (status === '') {
        alert('Please select a Status.');
        e.preventDefault();
        return false;
    }
    return true;
}
</script>
