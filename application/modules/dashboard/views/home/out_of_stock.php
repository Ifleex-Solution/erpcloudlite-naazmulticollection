<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <style>
                .stock-status-btn {
                    cursor: default;
                    min-width: 115px;
                }

                
                .btn-low-stock,
                .btn-reorder,
                .btn-warning {
                    background-color: #ec8303ca;
                    border-color: #ec8303ca;
                    color: #000;
                }

                .btn-near-order {
                    background-color: #fff59d; 
                    border-color: #f3ec8a;
                    color: #000; 
                }
                
                .btn-info {
                    color: #000;
                }
            </style>
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Stock Alert Notification</h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="out_of_stock_table" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th class="text-left"><?php echo display('sl') ?></th>
                                <th class="text-left">Product ID</th>
                                <th class="text-left"><?php echo display('product_name') ?></th>
                                <th class="text-left"><?php echo display('category') ?></th>
                                <th class="text-center">Master Stock Qty</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<script>
    $(document).ready(function () {
        $('#out_of_stock_table').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            lengthMenu: [[10, 25, 50, 100, 250], [10, 25, 50, 100, 250]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: [
                { extend: 'copy',  className: 'btn-sm prints', exportOptions: { columns: [0,1,2,3,4,5] } },
                { extend: 'csv',   className: 'btn-sm prints', title: 'Stock Alert Notification', exportOptions: { columns: [0,1,2,3,4,5] } },
                { extend: 'excel', className: 'btn-sm prints', title: 'Stock Alert Notification', exportOptions: { columns: [0,1,2,3,4,5] } },
                { extend: 'pdf',   className: 'btn-sm prints', title: 'Stock Alert Notification', exportOptions: { columns: [0,1,2,3,4,5] } },
                { extend: 'print', className: 'btn-sm prints', title: '<center>Stock Alert Notification</center>', exportOptions: { columns: [0,1,2,3,4,5] } }
            ],
            serverMethod: 'post',
            ajax: {
                url: $('#baseUrl2').val() + 'out_of_stock_data',
                type: 'POST'
            },
            columnDefs: [
                { targets: [0], orderable: false }
            ],
            columns: [
                { data: 'sl',           className: 'text-left' },
                { data: 'product_id',   className: 'text-left' },
                { data: 'product_name', className: 'text-left' },
                { data: 'category_name',className: 'text-left' },
                {
                    data: 'master_stock_qty',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return convertmasterstock(data, row.conversion_ratio, row.master, row.sub);
                    }
                },
                {
                    data: 'status',
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<span class="btn btn-xs stock-status-btn ' + row.status_class + '">' + data + '</span>';
                    }
                }
            ]
        });
    });

    function convertmasterstock(avstock, conversion_ratio, mastername, subname) {
        console.log(avstock+" "+conversion_ratio)
        if (!subname || !conversion_ratio) {
            return ((avstock ? avstock : 0) + mastername);
        }
        let mas  = conversion_ratio * avstock / conversion_ratio;
        let sub  = conversion_ratio * avstock % conversion_ratio;

        let mas2 = Math.floor((mas).toLocaleString());
        let totalcount = isNaN(mas2) ? Math.floor(Number(mas).toFixed(6)) : mas2;

        let sub2 = Math.floor((Number(sub).toFixed(6)).toLocaleString());
        let subcount = isNaN(sub2) ? Math.floor(Number(sub).toFixed(6)) : sub2;

        if (isNaN(totalcount)) totalcount = 0;
        if (isNaN(subcount))   subcount   = 0;

        return totalcount + mastername + ' ' + subcount + subname;
    }
</script>
