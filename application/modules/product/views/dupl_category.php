<!-- ── Category: Upload History ──────────────────────────────── -->
<div class="row" id="history_category" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Category</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkCategoryTable">
                    <thead>
                        <tr>
                            <th><?php echo display('sl') ?></th>
                            <th>Upload ID</th><th>Date</th><th>Uploaded By</th>
                            <th><?php echo display('action') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function initCategoryDT() {
    categoryDT = $('#bulkCategoryTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkCategoryUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}
function showBulkCategoryDetails(id) { showGenericBulkDetails(id, 'get_bulk_category_details/', ['#','Category Name'], ['category_name'], 'Category Upload Details'); }
function deleteBulkCategory(id) {
    if (!confirm('Delete this batch and all its categories?')) return;
    $.post($('#base_url').val() + 'delete_bulk_category/' + id, function() { if (categoryDT) categoryDT.ajax.reload(); });
}
</script>
