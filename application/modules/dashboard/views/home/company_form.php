<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <?php echo form_open_multipart('dashboard/setting/company_update', array('class' => 'form-vertical', 'id' => 'update_company')) ?>
            <div class="panel-body">

                <div class="form-group row">
                    <label for="company_name" class="col-sm-3 col-form-label"><?php echo display('name') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="2" class="form-control" name="company_name"
                            value="<?php echo $companys->company_name ?>"
                            placeholder="<?php echo display('company_name') ?>" required tabindex="1" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label">TP Number <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" name="mobile"
                            value="<?php echo $companys->mobile ?>" placeholder="<?php echo display('mobile') ?>"
                            required tabindex="2" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="address" class="col-sm-3 col-form-label"><?php echo display('address') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <textarea class="form-control input-description" tabindex="3" id="adress" name="address"
                            placeholder="<?php echo display('address') ?>"
                            required><?php echo $companys->address ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-sm-3 col-form-label"><?php echo display('email') ?>
                        <!-- <i class="text-danger">*</i> -->
                    </label>
                    <div class="col-sm-6">
                        <input type="email" tabindex="3" class="form-control" value="<?php echo $companys->email ?>"
                            name="email" placeholder="<?php echo display('email') ?>" tabindex="4" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label"><?php echo display('website') ?>
                        <!-- <i        class="text-danger">*</i> -->
                    </label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" value="<?php echo $companys->website ?>"
                            name="website" placeholder="<?php echo display('website') ?>" tabindex="5" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label"><?php echo display('vat_no') ?> </label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" value="<?php echo $companys->vat_no ?>"
                            name="vat_no" placeholder="<?php echo display('vat_no') ?>" tabindex="5" />
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label">BR NO
                    </label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" value="<?php echo $companys->cr_no ?>"
                            name="cr_no" placeholder="<?php echo display('cr_no') ?>" tabindex="5" />
                    </div>
                </div>

                <?php if ($companys->company_id == 1) { ?>
                    <div class="form-group row">
                        <label for="instance_type" class="col-sm-3 col-form-label">Instance Type <i
                                class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <select name="instance_type" id="instance_type" class="form-control" required tabindex="6">
                                <option value="">Select Instance Type</option>
                                <option value="DEV"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'DEV') ? 'selected' : ''; ?>>
                                    DEV - Development</option>
                                <option value="UAT"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'UAT') ? 'selected' : ''; ?>>
                                    UAT - User Acceptance Testing</option>
                                <option value="PROD"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'PROD') ? 'selected' : ''; ?>>
                                    PROD - Production</option>
                                <option value="BETA"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'BETA') ? 'selected' : ''; ?>>
                                    BETA - Beta</option>
                                <option value="LIVE"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'LIVE') ? 'selected' : ''; ?>>
                                    LIVE - Live</option>
                                <option value="DEMO"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'DEMO') ? 'selected' : ''; ?>>
                                    DEMO - Demo</option>
                                <option value="MARKETING"
                                    <?php echo (isset($companys->instance_type) && $companys->instance_type == 'MARKETING') ? 'selected' : ''; ?>>
                                    MARKETING - Marketing</option>
                            </select>
                        </div>
                    </div>
                <?php } else { ?>
                    <input type="hidden" tabindex="3" class="form-control" value="" name="instance_type" tabindex="5" />
                <?php } ?>
                <div class="form-group row">
                    <label for="instance_type" class="col-sm-3 col-form-label">Password Option <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <select class="form-control" id="status" name="password_enable" tabindex="-1" aria-hidden="true" required>
                            <option value="">Select One</option>
                            <option value="1" <?php echo ($companys->password_enable == "1") ? 'selected' : ''; ?>>Enable</option>
                            <option value="0" <?php echo ($companys->password_enable == "0") ? 'selected' : ''; ?>>Disable</option>
                        </select>

                    </div>
                </div>
                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label">Password
                    </label>
                    <div class="col-sm-6">
                        <input type="password" tabindex="3" class="form-control" value="<?php echo $companys->password ?>"
                            name="password" placeholder="<?php echo display('password') ?>" tabindex="5" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label">Header Text
                    </label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" value="<?php echo $companys->header_text ?>"
                            name="header_text" placeholder="Header Text" tabindex="5" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="bank_name" class="col-sm-3 col-form-label">Footer Text
                    </label>
                    <div class="col-sm-6">
                        <input type="text" tabindex="3" class="form-control" value="<?php echo $companys->footer_text ?>"
                            name="footer_text" placeholder="<?php echo display('footer') ?>" tabindex="5" />
                    </div>
                </div>

           

                <div class="form-group row">
                    <label for="instance_type" class="col-sm-3 col-form-label">Status <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-6">
                        <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true" required>
                            <option value="">Select One</option>
                            <option value="1" <?php echo ($companys->status == "1") ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($companys->status == "0") ? 'selected' : ''; ?>>Inactive</option>
                        </select>

                    </div>
                </div>

                


                <input type="hidden" name="company_id" value="<?php echo $companys->company_id ?>" />



                <div class="form-group row">

                    <div class="col-sm-9 text-right">
                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo (empty($companys->company_id) ? display('save') : display('update')) ?></button>

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>