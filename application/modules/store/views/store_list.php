<style>
.panel.panel-bd.lobidrag {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06) !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    margin-bottom: 24px !important;
}
.panel.panel-bd.lobidrag .panel-heading {
    background: #ffffff !important;
    padding: 14px 22px !important;
    border: none !important;
    border-bottom: 2px solid #F1F5F9 !important;
}
.panel.panel-bd.lobidrag .panel-title {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .panel-title > span:first-child {
    color: #1E293B !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 7px 16px !important;
    color: #fff !important;
    letter-spacing: 0.3px !important;
    transition: background 0.15s, box-shadow 0.15s !important;
    text-decoration: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary:hover {
    background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important;
    box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important;
}
.panel.panel-bd.lobidrag .panel-body {
    background: #ffffff !important;
    padding: 20px 22px !important;
}
#storeList thead th {
    background: #F1F5F9 !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.7px !important;
    border-bottom: 2px solid #DDD6FE !important;
    border-top: none !important;
    padding: 11px 14px !important;
    white-space: nowrap !important;
}
#storeList tbody td {
    padding: 10px 14px !important;
    font-size: 13px !important;
    color: #374151 !important;
    border-color: #F3F0FE !important;
    vertical-align: middle !important;
}
#storeList tbody td:nth-child(2) {
    color: #2563EB !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
}
#storeList tbody tr:hover td {
    background: #F3F0FE !important;
}
#storeList tbody td:nth-child(2) {
    color: #8b5cf6 !important;
    font-weight: 600 !important;
}
/* Horizontal scroll on mobile */
.panel.panel-bd.lobidrag .panel-body .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
}
/* DataTable toolbar */
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter {
    margin-bottom: 10px !important;
}
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons {
    margin-bottom: 8px !important;
}
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn {
    border-radius: 6px !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
}
@media (max-width: 767px) {
    .panel.panel-bd.lobidrag .panel-heading {
        padding: 12px 14px !important;
    }
    .panel.panel-bd.lobidrag .panel-body {
        padding: 10px 8px !important;
    }
    #storeList thead th {
        font-size: 10px !important;
        padding: 8px 6px !important;
    }
    #storeList tbody td {
        font-size: 12px !important;
        padding: 8px 6px !important;
    }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter {
        width: 100% !important;
        text-align: left !important;
    }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_info,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_paginate {
        width: 100% !important;
        text-align: center !important;
        float: none !important;
    }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons {
        width: 100% !important;
    }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn {
        margin-bottom: 4px !important;
    }
}
.label-success{background:#16A34A !important;color:#fff !important;padding:4px 10px !important;border-radius:4px !important;font-size:11px !important;font-weight:600 !important;display:inline-block !important;line-height:1.4 !important;border:none !important}
.label-danger{background:#DC2626 !important;color:#fff !important;padding:4px 10px !important;border-radius:4px !important;font-size:11px !important;font-weight:600 !important;display:inline-block !important;line-height:1.4 !important;border:none !important}
</style>

<div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <span><?php echo display('storelist') ?></span>
                           <span class="padding-lefttitle">
                                   
         <?php if($this->permission1->method('add_store','create')->access()){ ?>
                    <a href="<?php echo base_url('store_form') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_store') ?> </a>
                                 <?php }?>
                           </span>

                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="storeList"> 
                                <thead>
                                    <tr>
                                        <th><?php echo display('sl') ?></th>
                                        <th>Store Code</th>
                                        <th>Store Name</th>
                                        <th>Store Nature</th>
                                        <th>GRN</th>
                                        <th>GDN</th>
                                        <th>Default Stock</th>

                                        <th>Status</th>
                                        <th><?php echo display('action') ?> 
                                        </th>
                                    </tr>  
                                </thead>
                                <tbody>
                                  
                                </tbody>
                            </table>
                          
                        </div>
                    </div>
                </div>
                <input type="hidden" id="total_product" value="<?php echo $total_product;?>" name="">
            </div>
        </div>
        <script>
            $(document).ready(function() { 
      "use strict";
   var csrf_test_name = $('#CSRF_TOKEN').val();
   var base_url = '<?php echo base_url(); ?>';
    var total_product = $("#total_product").val();
     $('#storeList').DataTable({
             responsive: false,

             "aaSorting": [[ 1, "asc" ]],
             "columnDefs": [
                     { "bSortable": false, "aTargets": [0,8] },

            ],
           'processing': true,
           'serverSide': true,

          
           'lengthMenu':[[10, 25, 50,100,250,500, 1000], [10, 25, 50,100,250,500, 1000]],

             dom:"'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip", buttons:[ {
                extend: "copy",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want
                           }, className: "btn-sm prints"
            }
            , {
                extend: "csv", title: "Store List",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
                           }, className: "btn-sm prints"
            }
            , {
                extend: "excel",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
                           }, title: "Store List", className: "btn-sm prints"
            }
            , {
                extend: "pdf",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4, 5, 6 ] //Your Colume value those you want print
                           }, title: "Store List", className: "btn-sm prints"
            }
            , {
                extend: "print",exportOptions: {
                       columns: [ 0, 1, 2, 3, 4, 5, 6 ] //Your Colume value those you want print
                           },title: "<center>Store List</center>", className: "btn-sm prints"
            }
            ],
            
            'serverMethod': 'post',
            'ajax': {
               'url': base_url + 'store/store/checkStoreList',
               data:{
                csrf_test_name : csrf_test_name,
               }
            },
          'columns': [
             { data: 'sl' },
             { data: 'code' },
             { data: 'name'},
                 { data: 'store_nature' },
             { data: 'auto_grn'},
             { data: 'auto_gdn' },
                 { data: 'default_stock'},
             { data: 'status_label' },
             { data: 'button'},
          ],

    });

});
        </script>