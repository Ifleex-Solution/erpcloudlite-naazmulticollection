<style>
.item-list {
    font-size: 13px;
    line-height: 1.8;
}

.item-separator {
    margin: 0 3px;
}

.default-item {
    font-weight: bold;
}
</style>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo (!empty($title)?$title:null) ;?></h4>
                </div>
            </div>
            <div class="panel-body">

                <div class="table-responsive">
                    <table class="datatable table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th><?php echo display('sl_no') ?></th>
                                <th><?php echo display('image') ?></th>
                                <th>User Name</th>
                                <th>User Id</th>
                                <th>Role(s)</th>
                                <th>Branch(s)</th>
                                <th>Store(s)</th>
                                <th><?php echo display('status') ?></th>
                                <th><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($user)) ?>
                            <?php $sl = 1; ?>
                            <?php foreach ($user as $value) { ?>
                            <tr>
                                <td><?php echo $sl++; ?></td>
                                <td><img src="<?php echo (!empty($value->logo)?base_url().$value->logo:base_url('assets/img/icons/default.jpg')) ; ?>"
                                        alt="Image" height="50"></td>
                                <td><?php echo $value->fullname; ?></td>
                                <td><?php echo $value->email; ?></td>

                                <!-- Roles Column -->
                                <td>
                                    <div class="item-list">
                                        <?php if (!empty($value->roles)) { ?>
                                        <?php 
                                            $roleNames = array();
                                            foreach ($value->roles as $role) {
                                                $roleNames[] = $role->type;
                                            }
                                            echo implode(', ', $roleNames);
                                            ?>
                                        <?php } else { ?>
                                        <span class="text-muted">-</span>
                                        <?php } ?>
                                    </div>
                                </td>

                                <!-- Branches Column -->
                                <td>
                                    <div class="item-list">
                                        <?php if (!empty($value->branches)) { ?>
                                        <?php 
                                            $branchNames = array();
                                            foreach ($value->branches as $branch) {
                                                if ($branch->default == 1) {
                                                    $branchNames[] = '<strong>' . $branch->name . '</strong>';
                                                } else {
                                                    $branchNames[] = $branch->name;
                                                }
                                            }
                                            echo implode(', ', $branchNames);
                                            ?>
                                        <?php } else { ?>
                                        <span class="text-muted">-</span>
                                        <?php } ?>
                                    </div>
                                </td>

                                <!-- Stores Column -->
                                <td>
                                    <div class="item-list">
                                        <?php if (!empty($value->stores)) { ?>
                                        <?php 
                                            $storeNames = array();
                                            foreach ($value->stores as $store) {
                                                if ($store->default == 1) {
                                                    $storeNames[] = '<strong>' . $store->name . '</strong>';
                                                } else {
                                                    $storeNames[] = $store->name;
                                                }
                                            }
                                            echo implode(', ', $storeNames);
                                            ?>
                                        <?php } else { ?>
                                        <span class="text-muted">-</span>
                                        <?php } ?>
                                    </div>
                                </td>

                                <td><?php echo (($value->status==1)?display('active'):display('inactive')); ?></td>
                                <td>
                                    <a href="<?php echo base_url("add_user/$value->user_id") ?>"
                                        class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left"
                                        title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    <a href="<?php echo base_url("dashboard/user/bdtask_deleteuser/$value->user_id") ?>"
                                        onclick="return confirm('<?php echo display('are_you_sure') ?>')"
                                        class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right"
                                        title="Delete "><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                 
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>