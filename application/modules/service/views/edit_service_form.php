<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('service_edit') ?> </h4>
                    <a href="<?php echo base_url('manage_service') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
                </div>
            </div>
            <?php echo form_open_multipart('service/service/service_update', array('class' => 'form-vertical', 'id' => 'service_update')) ?>
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
                            placeholder="<?php echo display('service_name') ?>" required=""
                            value="<?php echo $service_name ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="charge" class="col-sm-2 col-form-label"><?php echo display('charge') ?> <i
                            class="text-danger">*</i></label>
                    <div class="col-sm-4">
                        <input class="form-control" name="charge" id="charge" type="text"
                            placeholder="<?php echo display('charge') ?>" required="" value="<?php echo $charge ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="service_vat" class="col-sm-2 col-form-label"><?php echo display('service_vat') . ' %' ?>
                    </label>
                    <div class="col-sm-4">
                        <input class="form-control" name="service_vat" id="service_vat" type="text"
                            placeholder="<?php echo display('service_vat') . ' %' ?>" value="<?php echo $service_vat ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="description" class="col-sm-2 col-form-label"><?php echo display('description') ?> <i
                            class="text-danger"></i></label>
                    <div class="col-sm-4">
                        <textarea class="form-control" name="description" id="description"
                            placeholder="<?php echo display('description') ?>"><?php echo $description ?></textarea>
                    </div>
                </div>

                <div class="row">

                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="status" class="col-sm-4 col-form-label">Status<i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="status" name="status" tabindex="-1" aria-hidden="true" required="" >
                                    <option value="">Select One</option>
                                    <option value="1" <?php echo ($status == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($status == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>

                            </div>
                        </div>
                    </div>
                </div>


                <?php
                $i = 0;
                foreach ($taxfield as $txs) {
                    $tax = 'tax' . $i;
                ?>
                    <div class="form-group row" <?php if ($vattaxinfo->dynamic_tax != 1) {
                                                    echo 'hidden';
                                                } ?>>
                        <label for="tax" class="col-sm-3 col-form-label"><?php echo $txs['tax_name']; ?> <i
                                class="text-danger"></i></label>
                        <div class="col-sm-6">
                            <input type="text" name="tax<?php echo $i; ?>" class="form-control"
                                value="<?php echo  number_format($servicedetails[0][$tax] * 100, 2, '.', ','); ?>">
                        </div>
                        <div class="col-sm-1"> <i class="text-success">%</i></div>
                    </div>
                <?php $i++;
                }

                ?>

                <input type="hidden" value="<?php echo $service_id ?>" name="service_id">

                <div class="form-group row">

                    <div class="col-sm-6 text-right">


                        <button type="submit" class="btn btn-success " name="save">
                            <?php echo display('update')  ?></button>

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>