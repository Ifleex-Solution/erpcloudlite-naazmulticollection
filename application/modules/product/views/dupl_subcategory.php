<!-- ── Subcategory: Upload History ───────────────────────────── -->
<div class="row" id="history_subcategory" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Subcategory</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkSubcategoryTable">
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
/* ── Subcategory validation ───────────────────────────────────── */
function validateSubcategoryRows(rows) {
    $('#preview_table_subcategory').show();
    let hasError = false;
    const seenInCsv = new Set();

    for (let i = 0; i < rows.length; i++) {
        const r          = rows[i];
        const subcatName = (r['Subcategory Name'] || '').trim();
        const catName    = (r['Category']         || '').trim();
        const statusRaw  = (r['Status (Yes/No)']  || '').trim().toLowerCase();
        let error = null, catObj = null;

        if (!subcatName)  { error = 'Subcategory Name is required'; }
        else if (!catName){ error = 'Category is required'; }
        else {
            catObj = findByName(csv_categories, 'category_name', catName);
            if (!catObj) error = 'Category not found: "' + catName + '"';
        }

        if (!error && !['yes','no'].includes(statusRaw)) error = 'Status must be Yes or No';

        if (!error) {
            const pairKey = subcatName.toLowerCase() + '||' + catObj.category_id;
            if (seenInCsv.has(pairKey))              error = 'Duplicate in CSV: "' + subcatName + '" in "' + catName + '"';
            else if (db_subcategory_pairs.has(pairKey)) error = 'Already exists in DB: "' + subcatName + '" in "' + catName + '"';
            else seenInCsv.add(pairKey);
        }

        const statusInt   = statusRaw === 'yes' ? 1 : 0;
        const statusLabel = statusInt ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            hasError = true;
            previewTableData.push({ idx: i+1, name: esc(subcatName), catName: esc(catName), statusLabel, badge, rowColor: '#fff5f5', hasError: true, _key: subcatName+'|'+catName });
        } else {
            validatedData.push({ _key: subcatName+'|'+catName, payload: { subcategory_name: subcatName, category_id: catObj.category_id, status: statusInt } });
            previewTableData.push({ idx: i+1, name: esc(subcatName), catName: esc(catName), statusLabel, badge, rowColor: '', hasError: false, _key: subcatName+'|'+catName });
        }
    }

    buildPreviewDT(['#','Subcategory Name','Category','Status','Validation'], ['idx','name','catName','statusLabel','badge'], '#preview_table_subcategory');
    finishValidation(rows.length);
}

function initSubcategoryDT() {
    subcategoryDT = $('#bulkSubcategoryTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkSubcategoryUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}
function showBulkSubcategoryDetails(id) { showGenericBulkDetails(id, 'get_bulk_subcategory_details/', ['#','Subcategory Name','Category'], ['subcategory_name','category_name'], 'Subcategory Upload Details'); }
function deleteBulkSubcategory(id) {
    if (!confirm('Delete this batch and all its subcategories?')) return;
    $.post($('#base_url').val() + 'delete_bulk_subcategory/' + id, function() { if (subcategoryDT) subcategoryDT.ajax.reload(); });
}
</script>
