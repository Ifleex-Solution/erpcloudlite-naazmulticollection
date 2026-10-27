 <style>
 /* Keep rows single-line — long addresses/emails were wrapping to 2+
    lines and making rows noticeably taller than other list screens. */
 #CustomerList2 tbody td:nth-child(3),
 #CustomerList2 tbody td:nth-child(5) {
     max-width: 220px;
     white-space: nowrap;
     overflow: hidden;
     text-overflow: ellipsis;
 }
 </style>
 <div class="row">
     <div class="col-sm-12">
         <div class="panel panel-bd lobidrag">
             <div class="panel-heading">
                 <div class="panel-title">
                     <h4 style="display:inline-block; margin:0 14px 0 0;"><?php echo $title ?> </h4>
                     <span class="padding-lefttitle">
                         <?php if ($this->permission1->method('add_customer', 'create')->access()) { ?>
                         <a href="<?php echo base_url('add_customer') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_customer') ?> </a>
                         <?php } ?>
                     </span>
                 </div>
             </div>

             <div class="panel-body">

                 <div class="table-responsive">
                     <table class="table table-bordered" id="CustomerList2" width="100%">
                         <thead>

                             <tr>
                                 <th><?php echo display('sl') ?></th>
                                 <th><?php echo display('customer_name') ?></th>
                                 <th>Primary Address</th>
                                 <th>Mobile No</th>
                                 <th><?php echo display('email'); ?></th>
                                 <th>Status</th>

                                 <th width="50px;"><?php echo display('action') ?>
                                 </th>
                             </tr>
                         </thead>
                         <tbody id="customer_tablebody">

                         </tbody>

                     </table>
                 </div>

             </div>


         </div>
     </div>
 </div>
 <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

 <script>
     $(document).ready(function() {
         // customer list
         var CSRF_TOKEN = $('#CSRF_TOKEN').val();
         var customer_id = $('#customer_id').val();
         var base_url = $('#baseUrl2').val();
         var mydatatable = $('#CustomerList2').DataTable({
             responsive: true,
             "aaSorting": [
                 [1, "asc"]
             ],
             "columnDefs": [{
                 "bSortable": false,
                 "aTargets": [0, 2, 3]
             }],
             'processing': true,
             'serverSide': true,
             'lengthMenu': [
                 [10, 25, 50, 100, 250, 500, 1000],
                 [10, 25, 50, 100, 250, 500, 1000]
             ],
             dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
             buttons: [{
                 extend: "copy",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5]
                 },
                 className: "btn-sm prints"
             }, {
                 extend: "csv",
                 title: "Customer List",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5]
                 },
                 className: "btn-sm prints"
             }, {
                 extend: "excel",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5]
                 },
                 title: "Customer List",
                 className: "btn-sm prints"
             }, {
                 extend: "pdf",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5]
                 },
                 title: "Customer List",
                 className: "btn-sm prints"
             }, {
                 extend: "print",
                 exportOptions: {
                     columns: [0, 1, 2,3,4,5]
                 },
                 title: "<center> Customer List</center>",
                 className: "btn-sm prints"
             }],
             'serverMethod': 'post',
             'ajax': {
                 'url': base_url + 'customer/customer/bdtask_CheckCustomerList',
                 "data": function(data) {
                     data.csrf_test_name = CSRF_TOKEN;
                     data.customer_id = $('#customer_id').val();
                     data.customfiled = $("select[name='customsearch[]']").val();
                 }
             },
             'columns': [{
                     data: 'sl'
                 },
                 {
                     data: 'customer_name'
                 },
                 {
                     data: 'address'
                 },
                 {
                     data: 'mobile'
                 },
                 {
                     data: 'email'
                 },
                 {
                     data: 'status'
                 },
                 {
                     data: 'button'
                 },
             ]
         });

         $("#customer_id").on('change', function() {
             mydatatable.ajax.reload();
         });

         $("#customsearch").on('change', function() {
             mydatatable.ajax.reload();
         });
     });
 </script>