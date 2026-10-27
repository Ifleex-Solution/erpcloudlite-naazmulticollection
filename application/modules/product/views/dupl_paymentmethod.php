<!-- ── Payment Method: Upload History ────────────────────────── -->
<div class="row" id="history_paymentmethod" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Payment Method</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkPaymentMethodTable">
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
const PAYMENT_NATURES = ['Cash Nature', 'Bank Nature'];

function validatePaymentMethodRows(rows) {
    $('#preview_table_paymentmethod').show();
    const seenInCsv = new Set();

    for (let i = 0; i < rows.length; i++) {
        const r       = rows[i];
        const name    = (r['Payment Method Name'] || '').trim();
        const nature  = (r['Payment Method Nature'] || '').trim();
        const statusRaw = (r['Status (Yes/No)'] || '').trim().toLowerCase();
        let error = null;

        if (!name)                                    error = 'Name is required';
        else if (seenInCsv.has(name.toLowerCase()))   error = 'Duplicate in CSV: "' + name + '"';
        else if (db_payment_method_names.has(name.toLowerCase())) error = 'Already exists in DB: "' + name + '"';
        else if (!PAYMENT_NATURES.includes(nature))   error = 'Nature must be one of: ' + PAYMENT_NATURES.join(', ');
        else if (!['yes','no'].includes(statusRaw))   error = 'Status must be Yes or No';
        if (!error) seenInCsv.add(name.toLowerCase());

        const statusInt   = statusRaw === 'yes' ? 1 : 0;
        const statusLabel = statusInt ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            previewTableData.push({ idx: i+1, name: esc(name), nature: esc(nature), statusLabel, badge, rowColor: '#fff5f5', hasError: true, _key: name });
        } else {
            validatedData.push({ _key: name, payload: { name, nature, status: statusInt } });
            previewTableData.push({ idx: i+1, name: esc(name), nature: esc(nature), statusLabel, badge, rowColor: '', hasError: false, _key: name });
        }
    }

    buildPreviewDT(
        ['#', 'Payment Method Name', 'Nature', 'Status', 'Validation'],
        ['idx', 'name', 'nature', 'statusLabel', 'badge'],
        '#preview_table_paymentmethod'
    );
    finishValidation(rows.length);
}

function initPaymentMethodDT() {
    paymentmethodDT = $('#bulkPaymentMethodTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkPaymentMethodUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}
function showBulkPaymentMethodDetails(id) { showGenericBulkDetails(id, 'get_bulk_paymentmethod_details/', ['#','Payment Method','Nature'], ['name','nature'], 'Payment Method Upload Details'); }
function deleteBulkPaymentMethod(id) {
    if (!confirm('Delete this batch and all its payment methods?')) return;
    $.post($('#base_url').val() + 'delete_bulk_paymentmethod/' + id, function() { if (paymentmethodDT) paymentmethodDT.ajax.reload(); });
}
</script>
