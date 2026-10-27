<!-- ── Stock Batch: Upload History ─────────────────────────── -->
<div class="row" id="history_stockbatch" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Stock Batch</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkStockBatchTable">
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
function initStockBatchDT() {
    stockbatchDT = $('#bulkStockBatchTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkStockBatchUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}

function validateStockBatchRows(rows) {
    $('#preview_table_stockbatch').show();

    /* existing batch IDs in DB (all statuses) */
    var existingIds = new Set();
    (db_all_stockbatch_batchids || []).forEach(function(b) {
        if (b.batchid) existingIds.add(b.batchid.toLowerCase().trim());
    });

    /* product name → product object */
    var prodNameMap = {};
    (csv_products || []).forEach(function(p) {
        prodNameMap[p.product_name.toLowerCase().trim()] = p;
    });

    var seenInCsv = new Set();
    var MON = { jan:0,feb:1,mar:2,apr:3,may:4,jun:5,jul:6,aug:7,sep:8,oct:9,nov:10,dec:11 };

    function parseDate(raw) {
        raw = (raw || '').trim();
        if (!raw) return '';
        /* DD-Mon-YY or DD-Mon-YYYY */
        var m = raw.match(/^(\d{1,2})-([A-Za-z]{3})-(\d{2,4})$/);
        if (m) {
            var day = parseInt(m[1], 10);
            var mon = MON[m[2].toLowerCase()];
            var yr  = parseInt(m[3], 10);
            if (yr < 100) yr += 2000;
            if (mon === undefined) return null;
            return yr + '-' + String(mon+1).padStart(2,'0') + '-' + String(day).padStart(2,'0');
        }
        if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) return raw;
        return null;
    }

    for (var i = 0; i < rows.length; i++) {
        var r         = rows[i];
        var batchId   = (r['Batch ID']                       || '').trim();
        var busageRaw = (r['Batch Usage Type']               || '').trim();
        var prodName  = (r['Product']                        || '').trim();
        var expRaw    = (r['Product Expiration (Yes/No)']    || '').trim().toLowerCase();
        var mdateRaw  = (r['Manufacturing Date']             || '').trim();
        var pdateRaw  = (r['Packing Date']                   || '').trim();
        var edateRaw  = (r['Expiry Date']                    || '').trim();
        var mrpRaw    = (r['MRP']                            || '').trim();
        var details   = (r['Details']                        || '').trim();
        var statusRaw = (r['Status (Yes/No)']                || '').trim().toLowerCase();

        var error  = null;
        var busage = busageRaw.toLowerCase() === 'multiple usage' ? 'multiple'
                   : busageRaw.toLowerCase() === 'single usage'   ? 'single'
                   : null;

        /* ── required + duplicate checks ── */
        if (!batchId)
            error = 'Batch ID is required';
        else if (seenInCsv.has(batchId.toLowerCase()))
            error = 'Duplicate Batch ID in CSV: "'+batchId+'"';
        else if (existingIds.has(batchId.toLowerCase()))
            error = 'Batch ID already exists: "'+batchId+'"';

        if (!error && !busage)
            error = 'Batch Usage Type must be "Multiple Usage" or "Single Usage"';

        if (!error && !['yes','no'].includes(statusRaw))
            error = 'Status must be Yes or No';

        /* ── single-usage specific fields ── */
        var productId = 0, edateEnabled = 0;
        var mdate = '', pdate = '', edate = '', mrp = 0;

        if (!error && busage === 'single') {
            if (!prodName) {
                error = 'Product is required for Single Usage batch';
            } else {
                var prodObj = prodNameMap[prodName.toLowerCase()];
                if (!prodObj) error = 'Product not found: "'+prodName+'"';
                else productId = prodObj.id;
            }

            if (!error) {
                if (!['yes','no'].includes(expRaw))
                    error = 'Product Expiration must be Yes or No';
                else {
                    edateEnabled = expRaw === 'yes' ? 1 : 0;

                    if (mdateRaw) {
                        mdate = parseDate(mdateRaw);
                        if (mdate === null) { error = 'Invalid Manufacturing Date: "'+mdateRaw+'"'; mdate=''; }
                    }
                    if (!error && pdateRaw) {
                        pdate = parseDate(pdateRaw);
                        if (pdate === null) { error = 'Invalid Packing Date: "'+pdateRaw+'"'; pdate=''; }
                    }
                    if (!error && edateEnabled) {
                        if (!edateRaw) {
                            error = 'Expiry Date required when Product Expiration is Yes';
                        } else {
                            edate = parseDate(edateRaw);
                            if (edate === null) { error = 'Invalid Expiry Date: "'+edateRaw+'"'; edate=''; }
                        }
                    }
                    if (!error && mrpRaw !== '' && isNaN(parseFloat(mrpRaw)))
                        error = 'MRP must be a number';
                    else if (mrpRaw !== '')
                        mrp = parseFloat(mrpRaw);
                }
            }
        }

        if (!error) seenInCsv.add(batchId.toLowerCase());

        var status      = statusRaw === 'yes' ? 1 : 0;
        var statusLabel = status
            ? '<span class="label label-success">Active</span>'
            : '<span class="label label-default">Inactive</span>';
        var usageLabel  = busage === 'multiple'
            ? '<span class="label label-info">Multiple</span>'
            : busage === 'single'
            ? '<span class="label label-warning">Single</span>'
            : '<span class="label label-danger">'+esc(busageRaw||'?')+'</span>';
        var badge = error
            ? '<span class="label label-danger">'+esc(error)+'</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            previewTableData.push({ idx:i+1, batchId:esc(batchId), usageLabel:usageLabel, product:esc(prodName||'—'), statusLabel:statusLabel, badge:badge, rowColor:'#fff5f5', hasError:true, _key:batchId||String(i) });
        } else {
            validatedData.push({ _key: batchId, payload: {
                batchid:      batchId,
                busage:       busage,
                product:      productId,
                edate_enabled: edateEnabled,
                mdate:        mdate || '',
                pdate:        pdate || '',
                edate:        edate || '',
                mrp:          mrp,
                details:      details,
                status:       status
            }});
            previewTableData.push({ idx:i+1, batchId:esc(batchId), usageLabel:usageLabel, product:esc(prodName||'—'), statusLabel:statusLabel, badge:badge, rowColor:'', hasError:false, _key:batchId });
        }
    }

    buildPreviewDT(
        ['#','Batch ID','Usage Type','Product','Status','Validation'],
        ['idx','batchId','usageLabel','product','statusLabel','badge'],
        '#preview_table_stockbatch'
    );
    finishValidation(rows.length);
}

function showBulkStockBatchDetails(id) {
    showGenericBulkDetails(
        id,
        'get_bulk_stockbatch_details/',
        ['#','Batch ID','Usage','Product','Mfg Date','Pack Date','Expiry','MRP','Details','Status'],
        ['batchid','busage','product_name','mdate','pdate','edate','mrp','details','status_label'],
        'Stock Batch Upload Details'
    );
}

function deleteBulkStockBatch(id) {
    if (!confirm('Delete this upload record? The batch entries will remain in the system.')) return;
    $.post($('#base_url').val()+'delete_bulk_stockbatch/'+id, function(){ if(stockbatchDT) stockbatchDT.ajax.reload(); });
}
</script>
