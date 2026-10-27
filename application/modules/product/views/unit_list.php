<style>
.panel.panel-bd.lobidrag{border:none !important;box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important;border-radius:14px !important;overflow:hidden !important;margin-bottom:24px !important}
.panel.panel-bd.lobidrag .panel-heading{background:#fff !important;padding:14px 22px !important;border:none !important;border-bottom:2px solid #F1F5F9 !important}
.panel.panel-bd.lobidrag .panel-title{display:flex !important;align-items:center !important;justify-content:space-between !important;flex-wrap:wrap !important;gap:10px !important;margin:0 !important}
.panel.panel-bd.lobidrag .panel-title > span:first-child{color:#1E293B !important;font-size:15px !important;font-weight:600 !important;letter-spacing:.3px !important}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;font-size:12px !important;font-weight:600 !important;padding:7px 16px !important;color:#fff !important;transition:background .15s,box-shadow .15s !important;text-decoration:none !important}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}
.panel.panel-bd.lobidrag .panel-body{background:#fff !important;padding:20px 22px !important}
.panel.panel-bd.lobidrag .panel-body .table-responsive{overflow-x:auto !important;-webkit-overflow-scrolling:touch !important}
.panel.panel-bd.lobidrag .panel-body table.table thead th{background:#F1F5F9 !important;color:#475569 !important;font-size:11px !important;font-weight:700 !important;text-transform:uppercase !important;letter-spacing:.7px !important;border-bottom:2px solid #DDD6FE !important;border-top:none !important;padding:11px 14px !important;white-space:nowrap !important}
.panel.panel-bd.lobidrag .panel-body table.table tbody td{padding:10px 14px !important;font-size:13px !important;color:#374151 !important;border-color:#F3F0FE !important;vertical-align:middle !important}
.panel.panel-bd.lobidrag .panel-body table.table tbody tr:hover td{background:#F3F0FE !important}
@media(max-width:767px){
  .panel.panel-bd.lobidrag .panel-heading{padding:12px 14px !important}
  
  
  
  .panel.panel-bd.lobidrag .panel-body{padding:10px 8px !important}
  .panel.panel-bd.lobidrag .panel-body table.table thead th{font-size:10px !important;padding:8px 6px !important}
  .panel.panel-bd.lobidrag .panel-body table.table tbody td{font-size:12px !important;padding:8px 6px !important}
  .panel.panel-bd.lobidrag .panel-body table.table tbody td .btn-xs{font-size:11px !important;padding:4px 8px !important;margin:1px !important}
  div.dataTables_wrapper div.dataTables_filter{text-align:left !important;margin-top:6px !important}
  div.dataTables_wrapper div.dataTables_filter label{display:block !important}
  div.dataTables_wrapper div.dataTables_filter input{width:100% !important;margin:0 !important}
  div.dataTables_wrapper div.dataTables_length label{display:block !important}
}
.label-success{background:#16A34A !important;color:#fff !important;padding:4px 10px !important;border-radius:4px !important;font-size:11px !important;font-weight:600 !important;display:inline-block !important;line-height:1.4 !important;border:none !important}
.label-danger{background:#DC2626 !important;color:#fff !important;padding:4px 10px !important;border-radius:4px !important;font-size:11px !important;font-weight:600 !important;display:inline-block !important;line-height:1.4 !important;border:none !important}
</style>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <span><?php echo $title ?></span>
                    <span class="padding-lefttitle">
                        <?php if($this->permission1->method('manage_unit','create')->access()){ ?>
                        <a href="<?php echo base_url('unit_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_unit') ?></a>
                        <?php } ?>
                    </span>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table  class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th data-priority="3"><?php echo display('sl') ?></th>
                                <th data-priority="1"><?php echo display('unit_name') ?></th>
                                <th data-priority="2"><?php echo display('status') ?></th>
                                <th class="text-center" data-priority="1"><?php echo display('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sl = 1;
                            if ($unit_list) {
                                foreach ($unit_list as $categories) {
                            ?>

                                    <tr>
                                        <td><?php echo $sl++; ?></td>
                                        <td ><?php echo $categories->unit_name ?></td>
                                        <td>
                                        <?php if ($categories->status == 1) { ?>
                                            <span class="label label-success">Active</span>
                                        <?php } else { ?>
                                            <span class="label label-danger">Inactive</span>
                                        <?php } ?></td>
                                        <td>
                                            <center>

                                                <?php if ($this->permission1->method('manage_unit', 'update')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'unit_form/' . $categories->unit_id; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo display('update') ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                <?php } ?>
                                                <?php if ($this->permission1->method('manage_unit', 'delete')->access()) { ?>
                                                    <a href="<?php echo base_url() . 'product/product/bdtask_deleteunit/' . $categories->unit_name . "1234" . $categories->unit_id; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Are You Sure To Want To Delete ?')" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo display('delete') ?> "><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                <?php } ?>

                                            </center>
                                        </td>
                                    </tr>

                                <?php
                                }
                            } else { ?>

                                <tr>
                                    <td colspan="3" class="text-center"><?php echo display('no_record_found') ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>