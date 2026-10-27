<!-- ── Designation: Upload History ──────────────────────────── -->
<div class="row" id="history_designation" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Designation</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkDesignationTable">
                    <thead><tr>
                        <th><?php echo display('sl') ?></th>
                        <th>Upload ID</th><th>Date</th><th>Uploaded By</th>
                        <th><?php echo display('action') ?></th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function initDesignationDT() {
    designationDT = $('#bulkDesignationTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkDesignationUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}

function validateDesignationRows(rows) {
    $('#preview_table_designation').show();

    var seenInCsv = new Set();

    for (var i = 0; i < rows.length; i++) {
        var r          = rows[i];
        var desig      = (r['Designation']     || '').trim();
        var details    = (r['Details']         || '').trim();
        var statusRaw  = (r['Status (Yes/No)'] || '').trim().toLowerCase();
        var error      = null;

        if (!desig)
            error = 'Designation is required';
        else if (seenInCsv.has(desig.toLowerCase()))
            error = 'Duplicate in CSV: "' + desig + '"';
        else if (db_designation_names.has(desig.toLowerCase()))
            error = 'Already exists: "' + desig + '"';
        else if (!['yes','no'].includes(statusRaw))
            error = 'Status must be Yes or No';

        if (!error) seenInCsv.add(desig.toLowerCase());

        var status      = statusRaw === 'yes' ? 1 : 0;
        var statusLabel = status
            ? '<span class="label label-success">Active</span>'
            : '<span class="label label-default">Inactive</span>';
        var badge = error
            ? '<span class="label label-danger">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            previewTableData.push({ idx:i+1, desig:esc(desig), details:esc(details), statusLabel:statusLabel, badge:badge, rowColor:'#fff5f5', hasError:true, _key:desig||String(i) });
        } else {
            validatedData.push({ _key: desig, payload: { designation: desig, details: details, status: status } });
            previewTableData.push({ idx:i+1, desig:esc(desig), details:esc(details), statusLabel:statusLabel, badge:badge, rowColor:'', hasError:false, _key:desig });
        }
    }

    buildPreviewDT(
        ['#', 'Designation', 'Details', 'Status', 'Validation'],
        ['idx', 'desig', 'details', 'statusLabel', 'badge'],
        '#preview_table_designation'
    );
    finishValidation(rows.length);
}

function showBulkDesignationDetails(id) {
    showGenericBulkDetails(
        id,
        'get_bulk_designation_details/',
        ['#', 'Designation', 'Details', 'Status'],
        ['designation', 'details', 'status_label'],
        'Designation Upload Details'
    );
}

function deleteBulkDesignation(id) {
    if (!confirm('Delete this upload record? The designation entries will remain in the system.')) return;
    $.post($('#base_url').val()+'delete_bulk_designation/'+id, function(){ if(designationDT) designationDT.ajax.reload(); });
}
</script>
