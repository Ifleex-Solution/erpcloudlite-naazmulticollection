<!-- ── Service: Upload History ──────────────────────────────── -->
<div class="row" id="history_service" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Service</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkServiceTable">
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
function validateServiceRows(rows) {
    $('#preview_table_service').show();
    const seenNames = new Set();
    const seenCodes = new Set();
    for (let i = 0; i < rows.length; i++) {
        const r       = rows[i];
        const code    = (r['Service ID']          || '').trim();
        const name    = (r['Service Name']        || '').trim();
        const charge  = (r['Charge']              || '').trim();
        const vat     = (r['Service VAT (%)']     || '').trim();
        const desc    = (r['Description']         || '').trim();
        const stRaw   = (r['Status (Yes/No)']     || '').trim().toLowerCase();
        let error = null;

        if (!code)                                          error = 'Service ID is required';
        else if (seenCodes.has(code.toLowerCase()))         error = 'Duplicate Service ID in CSV: "' + code + '"';
        else if (db_service_codes.has(code.toLowerCase()))  error = 'Service ID already exists: "' + code + '"';
        else if (!name)                                      error = 'Service Name is required';
        else if (seenNames.has(name.toLowerCase()))     error = 'Duplicate name in CSV: "' + name + '"';
        else if (db_service_names.has(name.toLowerCase())) error = 'Service already exists: "' + name + '"';
        else if (charge !== '' && isNaN(parseFloat(charge))) error = 'Charge must be numeric';
        else if (vat    !== '' && isNaN(parseFloat(vat)))    error = 'Service VAT must be numeric';
        else if (!['yes','no'].includes(stRaw))         error = 'Status must be Yes or No';
        if (!error) { seenNames.add(name.toLowerCase()); seenCodes.add(code.toLowerCase()); }

        const statusLabel = stRaw==='yes' ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error ? '<span class="label label-danger" title="'+esc(error)+'">'+esc(error)+'</span>' : '<span class="label label-success">OK</span>';
        if (error) {
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), charge:esc(charge), vat:esc(vat), statusLabel, badge, rowColor:'#fff5f5', hasError:true, _key:code||name });
        } else {
            validatedData.push({ _key:code, payload:{ service_code:code, service_name:name, charge, service_vat:vat, description:desc, status:stRaw==='yes'?1:0 } });
            previewTableData.push({ idx:i+1, code:esc(code), name:esc(name), charge:esc(charge), vat:esc(vat), statusLabel, badge, rowColor:'', hasError:false, _key:code });
        }
    }
    buildPreviewDT(['#','Service ID','Service Name','Charge','VAT','Status','Validation'],
                   ['idx','code','name','charge','vat','statusLabel','badge'], '#preview_table_service');
    finishValidation(rows.length);
}
function initServiceDT() {
    serviceDT = $('#bulkServiceTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkServiceUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}
function showBulkServiceDetails(id) { showGenericBulkDetails(id,'get_bulk_service_details/',['#','Service ID','Service Name','Charge','VAT'],['service_code','service_name','charge','service_vat'],'Service Upload Details'); }
function deleteBulkService(id) {
    if (!confirm('Delete this batch and all its services?')) return;
    $.post($('#base_url').val()+'delete_bulk_service/'+id, function(){ if(serviceDT) serviceDT.ajax.reload(); });
}
</script>
