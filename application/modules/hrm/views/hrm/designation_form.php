<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title has-goback">
                    <h4><?php echo $title ?> </h4>
                
<a href="<?php echo base_url('designation_list') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <div class="panel-body">
                <?php echo form_open('designation_form/'.$designation->id,'class="" id="designation_form"')?>


                <div class="form-group row">
                    <label for="designation"
                        class="col-sm-2 text-right col-form-label"><?php echo display('designation')?> <i
                            class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="designation" class="form-control" id="designation"
                            placeholder="<?php echo display('designation')?>"
                            value="<?php echo $designation->designation?>" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="details" class="col-sm-2 text-right col-form-label"><?php echo display('details')?> <i
                            class="text-danger"> </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="details" class="form-control" id="details"
                            placeholder="<?php echo display('details')?>" value="<?php echo $designation->details?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label"><?php echo display('status')?> <i
                            class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">
                        <select name="status" class="form-control" id="status" required>
                            <option value="">Select Status</option>
                            <option value="1"
                                <?php echo (isset($designation->status) && $designation->status == 1) ? 'selected' : ''; ?>>
                                Active
                            </option>
                            <option value="0"
                                <?php echo (isset($designation->status) && $designation->status == 0) ? 'selected' : ''; ?>>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success ">
                            <?php echo (empty($designation->id)?display('save'):display('update')) ?></button>



                    </div>
                </div>


                <?php echo form_close();?>
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    wireDuplicateCheck({
        input: '#designation',
        url: $('#base_url').val() + 'hrm/hrm/check_duplicate_designation',
        saveButtons: 'button[type="submit"]',
        excludeId: <?php echo json_encode(!empty($designation->id) ? $designation->id : null); ?>
    });
});
</script>