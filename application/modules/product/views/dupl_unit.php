<!-- ── Unit: Upload History ───────────────────────────────────── -->
<div class="row" id="history_unit" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Unit</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkUnitTable">
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
/* ── Unit validation ──────────────────────────────────────────── */
function validateUnitRows(rows) {
    $('#preview_table_unit').show();
    let hasError = false;
    const seenInCsv = new Set();

    for (let i = 0; i < rows.length; i++) {
        const r         = rows[i];
        const unitName  = (r['Unit Name']        || '').trim();
        const printName = (r['Print Name']        || '').trim();
        const statusRaw = (r['Status (Yes/No)']   || '').trim().toLowerCase();
        let error = null;

        if (!unitName)                                  error = 'Unit Name is required';
        else if (seenInCsv.has(unitName.toLowerCase())) error = 'Duplicate in CSV: "' + unitName + '"';
        else if (db_unit_names.has(unitName.toLowerCase())) error = 'Already exists in DB: "' + unitName + '"';
        else if (!['yes','no'].includes(statusRaw))     error = 'Status must be Yes or No';
        if (!error) seenInCsv.add(unitName.toLowerCase());

        const statusInt   = statusRaw === 'yes' ? 1 : 0;
        const statusLabel = statusInt ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            hasError = true;
            previewTableData.push({ idx: i+1, name: esc(unitName), printName: esc(printName), statusLabel, badge, rowColor: '#fff5f5', hasError: true, _key: unitName });
        } else {
            validatedData.push({ _key: unitName, payload: { unit_name: unitName, unit_display_name: printName || unitName, status: statusInt } });
            previewTableData.push({ idx: i+1, name: esc(unitName), printName: esc(printName || unitName), statusLabel, badge, rowColor: '', hasError: false, _key: unitName });
        }
    }

    buildPreviewDT(['#','Unit Name','Print Name','Status','Validation'], ['idx','name','printName','statusLabel','badge'], '#preview_table_unit');
    finishValidation(rows.length);
}

function initUnitDT() {
    unitDT = $('#bulkUnitTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkUnitUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}
function showBulkUnitDetails(id) { showGenericBulkDetails(id, 'get_bulk_unit_details/', ['#','Unit Name','Print Name'], ['unit_name','unit_display_name'], 'Unit Upload Details'); }
function deleteBulkUnit(id) {
    if (!confirm('Delete this batch and all its units?')) return;
    $.post($('#base_url').val() + 'delete_bulk_unit/' + id, function() { if (unitDT) unitDT.ajax.reload(); });
}
</script>
