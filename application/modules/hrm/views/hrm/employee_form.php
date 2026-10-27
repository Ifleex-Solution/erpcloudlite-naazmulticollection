<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title has-goback">
                    <h4><?php echo html_escape($title) ?> </h4>
                
<a href="<?php echo base_url('employee_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <div class="panel-body">

                <?php echo form_open_multipart('employee_form/'.$employee->id,'id="validate"') ?>
                <input type="hidden" name="id" id="id" value="<?php echo $employee->id?>">
                <div class="form-group row">
                   
                    <label for="last_name" class="col-sm-2 col-form-div">Employee Id<i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input name="last_name" class="form-control" type="text" placeholder="Employee Id"
                            id="last_name" value="<?php echo $employee->last_name?>">
                        <input type="hidden" name="old_last_name" value="<?php echo $employee->last_name?>">
                    </div>
                    <label for="first_name" class="col-sm-2 col-form-div">Employee Name<i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input name="first_name" class="form-control" type="text" placeholder="Employee Name" required
                            id="first_name" value="<?php echo $employee->first_name?>">
                        <input type="hidden" name="old_first_name" value="<?php echo $employee->first_name?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="designation" class="col-sm-2 col-form-div"><?php echo display('designation') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('designation',$desig,$employee->designation,'class="form-control" required') ?>
                    </div>
                    <label for="phone" class="col-sm-2 col-form-div">Contact No.</label>
                    <div class="col-sm-4">
                        <input name="phone" class="form-control" type="number" placeholder="Contact No." id="phone"
                            value="<?php echo $employee->phone?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="rate_type" class="col-sm-2 col-form-div">Pay Type</label>
                    <div class="col-sm-4">
                        <select name="rate_type" class="form-control">
                            <option value="">Select type</option>
                            <option value="1" <?php if($employee->rate_type == 1){echo 'selected';}?>>Hourly</option>
                            <option value="2" <?php if($employee->rate_type == 2){echo 'selected';}?>>Salary</option>
                        </select>
                    </div>
                    <label for="hour_rate_or_salary" class="col-sm-2 col-form-div">Rate</label>
                    <div class="col-sm-4">
                        <input name="hrate" class="form-control" type="number" placeholder="Rate" id="hrate"
                            value="<?php echo $employee->hrate?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-div"><?php echo display('email') ?></label>
                    <div class="col-sm-4">
                        <input name="email" class="form-control" type="email"
                            placeholder="<?php echo display('email') ?>" id="email"
                            value="<?php echo $employee->email?>">
                    </div>
                    <label for="blood_group" class="col-sm-2 col-form-div"><?php echo display('blood_group') ?> </label>
                    <div class="col-sm-4">
                        <input name="blood_group" class="form-control" type="text"
                            placeholder="<?php echo display('blood_group') ?>" id="blood_group"
                            value="<?php echo $employee->blood_group?>">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_line_1" class="col-sm-2 col-form-div">Primary Address</label>
                    <div class="col-sm-4">
                        <textarea name="address_line_1" class="form-control" placeholder="Primary Address"
                            id="address_line_1"><?php echo $employee->address_line_1?></textarea>
                    </div>
                    <label for="address_line_2" class="col-sm-2 col-form-div">Secondary Address</label>
                    <div class="col-sm-4">
                        <textarea name="address_line_2" class="form-control" placeholder="Secondary Address"
                            id="address_line_2"><?php echo $employee->address_line_2?></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="state" class="col-sm-2 col-form-div">State/Province</label>
                    <div class="col-sm-4">
                        <input name="state" class="form-control" type="text" placeholder="State/Province" id="state"
                            value="<?php echo isset($employee->state) ? $employee->state : ''?>">
                    </div>
                    <label for="country" class="col-sm-2 col-form-div"><?php echo display('country') ?> </label>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('country', $country_list,$employee->country, ' class="form-control"') ?>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="city" class="col-sm-2 col-form-div"><?php echo display('city') ?></label>
                    <div class="col-sm-4">
                        <input name="city" class="form-control" type="text" placeholder="<?php echo display('city') ?>"
                            id="city" value="<?php echo $employee->city?>">
                    </div>
                    <label for="zip" class="col-sm-2 col-form-div"><?php echo display('zip') ?></label>
                    <div class="col-sm-4">
                        <input name="zip" class="form-control" type="text" placeholder="<?php echo display('zip') ?>"
                            value="<?php echo $employee->zip?>" id="zip">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-div">Status <i class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <select name="status" class="form-control" id="status" required>
                            <option value="">Select Status</option>
                            <option value="1"
                                <?php echo (isset($employee->status) && $employee->status == 1) ? 'selected' : ''; ?>>
                                Active</option>
                            <option value="0"
                                <?php echo (isset($employee->status) && $employee->status == 0) ? 'selected' : ''; ?>>
                                Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group text-right">
                    <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo display('reset') ?></button>
                    <button type="submit" class="btn btn-success w-md m-b-5"><?php echo display('save') ?></button>
                </div>
                <?php echo form_close() ?>
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    wireDuplicateCheck({
        input: '#last_name',
        url: $('#base_url').val() + 'hrm/hrm/check_duplicate_employee',
        saveButtons: 'button[type="submit"]',
        excludeId: <?php echo json_encode(!empty($employee->id) ? $employee->id : null); ?>
    });
});
</script>