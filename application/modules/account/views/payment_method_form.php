<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                    <a href="<?php echo base_url('payment_method_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>
            <?php echo form_open_multipart('add_payment_method/' . $pmethod->id, array('class' => 'form-vertical', 'id' => 'validate')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="payment_method_name" class="col-sm-4 col-form-label" style="white-space: nowrap;"><?php echo display('payment_method_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="HeadName" id="HeadName" required="" placeholder="<?php echo display('payment_method_name') ?>" value="<?php echo $pmethod->name ?>" tabindex="1" />
                                <input type="hidden" name="old_name" value="<?php echo $pmethod->name ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="nature" class="col-sm-4 col-form-label" style="white-space: nowrap;">Payment Nature <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="nature" name="nature" tabindex="2" required>
                                    <option value="">Select Nature</option>
                                    <?php foreach (['Cash Nature','Bank Nature'] as $n): ?>
                                    <option value="<?php echo $n; ?>" <?php echo (isset($pmethod->nature) && $pmethod->nature === $n) ? 'selected' : ''; ?>><?php echo $n; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label" style="white-space: nowrap;">Status <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" tabindex="3" required>
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($pmethod->status_label == "Active") ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($pmethod->status_label == "Inactive") ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6 text-right">
                        <button type="submit" class="btn btn-success" name="button" value="save" tabindex="2">
                            <i class="fa fa-save"></i> <?php echo (empty($pmethod->id) ? display('save') : display('update')) ?>
                        </button>

                        <?php if (empty($pmethod->id)) { ?>
                        <button type="submit" class="btn btn-success" name="button" value="add-another" tabindex="3">
                            <i class="fa fa-save"></i> <?php echo display('save_and_add_another'); ?>
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
    wireDuplicateCheck({
        input: '#HeadName',
        url: $('#base_url').val() + 'account/accounts/check_duplicate_paymentmethod',
        saveButtons: 'button[type="submit"]',
        excludeId: <?php echo json_encode(!empty($pmethod->id) ? $pmethod->id : null); ?>
    });
});
</script>