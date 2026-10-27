<!-- Manage service -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo display('manage_service') ?></span>
                    <span class="padding-lefttitle">

                        <?php if ($this->permission1->method('add_service', 'create')->access()) { ?>
                            <a href="<?php echo base_url('add_service') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_service') ?> </a>
                        <?php } ?>
                    </span>

                </div>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table id="" class="table table-bordered table-striped table-hover datatable">
                        <thead>
                            <tr>
                                <th class="text-left"><?php echo display('sl') ?></th>
                                <th class="text-left"><?php echo 'Service ID' ?></th>
                                <th class="text-left"><?php echo display('service_name') ?></th>
                                <th class="text-left"><?php echo display('charge') ?></th>
                                <?php if ($vattaxinfo->fixed_tax == 1) { ?>
                                    <th class="text-left"><?php echo display('service_vat') ?></th>
                                <?php } ?>
                                <th class="text-left"><?php echo display('description') ?></th>
                                <?php if ($vattaxinfo->dynamic_tax == 1) {
                                    foreach ($taxfiled as $taxhead) { ?>
                                        <th class="text-left"><?php echo $taxhead['tax_name']; ?></th>
                                <?php }
                                } ?>
                                <th class="text-left"><?php echo display('status') ?></th>

                                <th class="text-left"><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($service_list) {
                                $sl = 1;
                                foreach ($service_list as $services) {

                            ?>

                                    <tr>
                                        <td class="text-left"><?php echo $sl; ?></td>
                                        <td class="text-left"><?php echo !empty($services['service_code'])?html_escape($services['service_code']):''; ?></td>
                                        <td class="text-left"><?php echo html_escape($services['service_name']); ?></td>
                                        <td class="text-left"><?php echo html_escape($services['charge']); ?></td>
                                        <?php if ($vattaxinfo->fixed_tax == 1) { ?>
                                            <td class="text-left"><?php echo html_escape($services['service_vat']); ?></td>
                                        <?php } ?>
                                        <td class="text-left"><?php echo html_escape($services['description']); ?></td>
                                        <?php if ($vattaxinfo->dynamic_tax == 1) {
                                            for ($i = 0; $i < $rowumber; $i++) {
                                                $tax = 'tax' . $i;
                                        ?>
                                                <td class="text-left"><?php echo round($services[$tax] * 100); ?> %</td>
                                        <?php }
                                        } ?>
                                        <td class="text-left">

                                            <?php if ($services['status'] == 1) { ?>
                                                <span class="label label-success">Active</span>

                                            <?php } ?>

                                            <?php if ($services['status'] == 0) { ?>
                                                <span class="label label-danger">Inactive</span>

                                            <?php } ?>



                                        <td>
                                            <center>
                                                <?php echo form_open() ?>
                                                <?php if ($this->permission1->method('manage_service', 'update')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'edit_service/' . $services['service_id']; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                                                <?php } ?>
                                                <?php if ($this->permission1->method('manage_service', 'delete')->access()) { ?>
                                                    <a href="<?php echo base_url('service/service/service_delete/' . $services['service_id']) ?>" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="right" title="delete" onclick="return confirm('Are Your Sure ?')" data-original-title="<?php echo display('delete') ?> "><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                <?php } ?>
                                                <?php echo form_close() ?>
                                            </center>
                                        </td>
                                    </tr>

                            <?php $sl++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>