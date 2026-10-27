<!-- ── Store: Upload History ──────────────────────────────────── -->
<div class="row" id="history_store" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Store</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkStoreTable">
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
var STORE_NATURES  = ['outlet','warehouse','showroom'];
var DSTOCK_MAP     = { 'master stock': 1, 'substock': 0 };

function validateStoreRows(rows) {
    $('#preview_table_store').show();
    const seenCodes = new Set(), seenNames = new Set();
    for (let i = 0; i < rows.length; i++) {
        const r       = rows[i];
        const code    = (r['Store Code']       || '').trim();
        const name    = (r['Store Name']       || '').trim();
        const nature  = (r['Store Nature']     || '').trim();
        const grnRaw  = (r['GRN (Yes/No)']     || '').trim().toLowerCase();
        const gdnRaw  = (r['GDN (Yes/No)']     || '').trim().toLowerCase();
        const dstRaw  = (r['Default Stock']    || '').trim().toLowerCase();
        const stRaw   = (r['Status (Yes/No)']  || '').trim().toLowerCase();
        let error = null;

        if (!code)                                     error = 'Store Code is required';
        else if (!name)                                error = 'Store Name is required';
        else if (seenCodes.has(code.toLowerCase()))    error = 'Duplicate code in CSV: "' + code + '"';
        else if (db_store_codes.has(code.toLowerCase()))  error = 'Store Code already exists: "' + code + '"';
        else if (db_store_names.has(name.toLowerCase()))  error = 'Store Name already exists: "' + name + '"';
        else if (!STORE_NATURES.includes(nature.toLowerCase())) error = 'Store Nature must be: Outlet, Warehouse or Showroom';
        else if (!['yes','no'].includes(grnRaw))       error = 'GRN must be Yes or No';
        else if (!['yes','no'].includes(gdnRaw))       error = 'GDN must be Yes or No';
        else if (DSTOCK_MAP[dstRaw] === undefined)     error = 'Default Stock must be: Master Stock or Substock';
        else if (!['yes','no'].includes(stRaw))        error = 'Status must be Yes or No';
        if (!error) { seenCodes.add(code.toLowerCase()); seenNames.add(name.toLowerCase()); }

        const statusLabel = stRaw==='yes' ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error ? '<span class="label label-danger" title="'+esc(error)+'">'+esc(error)+'</span>' : '<span class="label label-success">OK</span>';
        if (error) {
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), nature:esc(nature), grnRaw, gdnRaw, dstRaw, statusLabel, badge, rowColor:'#fff5f5', hasError:true, _key:code });
        } else {
            validatedData.push({ _key:code, payload:{ code, name, store_nature:nature, auto_grn:grnRaw==='yes'?1:0, auto_gdn:gdnRaw==='yes'?1:0, dstock:DSTOCK_MAP[dstRaw], status:stRaw==='yes'?1:0 } });
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), nature:esc(nature), grnRaw, gdnRaw, dstRaw, statusLabel, badge, rowColor:'', hasError:false, _key:code });
        }
    }
    buildPreviewDT(['#','Code','Store Name','Nature','GRN','GDN','Default Stock','Status','Validation'],
                   ['idx','code','name','nature','grnRaw','gdnRaw','dstRaw','statusLabel','badge'], '#preview_table_store');
    finishValidation(rows.length);
}
function initStoreDT() {
    storeDT = $('#bulkStoreTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkStoreUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}
function showBulkStoreDetails(id) { showGenericBulkDetails(id,'get_bulk_store_details/',['#','Code','Store Name'],['code','name'],'Store Upload Details'); }
function deleteBulkStore(id) {
    if (!confirm('Delete this batch and all its stores?')) return;
    $.post($('#base_url').val()+'delete_bulk_store/'+id, function(){ if(storeDT) storeDT.ajax.reload(); });
}
</script>
