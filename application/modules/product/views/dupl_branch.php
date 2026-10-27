<!-- ── Branch: Upload History ─────────────────────────────────── -->
<div class="row" id="history_branch" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Branch</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkBranchTable">
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
function validateBranchRows(rows) {
    $('#preview_table_branch').show();
    const seenCodes = new Set(), seenNames = new Set();
    for (let i = 0; i < rows.length; i++) {
        const r      = rows[i];
        const code   = (r['Branch Code']      || '').trim();
        const name   = (r['Branch']           || '').trim();
        const nature = (r['Branch Nature']    || '').trim();
        const stRaw  = (r['Status (Yes/No)']  || '').trim().toLowerCase();
        let error = null;

        if (!code)                                    error = 'Branch Code is required';
        else if (!name)                               error = 'Branch Name is required';
        else if (seenCodes.has(code.toLowerCase()))   error = 'Duplicate code in CSV: "' + code + '"';
        else if (db_branch_codes.has(code.toLowerCase())) error = 'Branch Code already exists: "' + code + '"';
        else if (db_branch_names.has(name.toLowerCase())) error = 'Branch Name already exists: "' + name + '"';
        else if (!nature)                             error = 'Branch Nature is required';
        else if (!['yes','no'].includes(stRaw))       error = 'Status must be Yes or No';
        if (!error) { seenCodes.add(code.toLowerCase()); seenNames.add(name.toLowerCase()); }

        const statusLabel = stRaw === 'yes' ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error ? '<span class="label label-danger" title="'+esc(error)+'">'+esc(error)+'</span>' : '<span class="label label-success">OK</span>';
        if (error) {
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), nature:esc(nature), statusLabel, badge, rowColor:'#fff5f5', hasError:true, _key:code });
        } else {
            validatedData.push({ _key:code, payload:{ code, name, nature, status: stRaw==='yes'?1:0 } });
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), nature:esc(nature), statusLabel, badge, rowColor:'', hasError:false, _key:code });
        }
    }
    buildPreviewDT(['#','Code','Branch Name','Nature','Status','Validation'], ['idx','code','name','nature','statusLabel','badge'], '#preview_table_branch');
    finishValidation(rows.length);
}
function initBranchDT() {
    branchDT = $('#bulkBranchTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkBranchUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}
function showBulkBranchDetails(id) { showGenericBulkDetails(id,'get_bulk_branch_details/',['#','Code','Branch Name'],['code','name'],'Branch Upload Details'); }
function deleteBulkBranch(id) {
    if (!confirm('Delete this batch and all its branches?')) return;
    $.post($('#base_url').val()+'delete_bulk_branch/'+id, function(){ if(branchDT) branchDT.ajax.reload(); });
}
</script>
