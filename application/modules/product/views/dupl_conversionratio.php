<!-- ── Conversion Ratio: Upload History ──────────────────────── -->
<div class="row" id="history_cr" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Conversion Ratio</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkCRTable">
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
/* ── Conversion Ratio validation ─────────────────────────────── */
async function validateCRRows(rows) {
    $('#preview_table_cr').show();

    const subunitMap = {};
    for (const s of csv_product_subunits) {
        const pid = String(s.product_id);
        if (!subunitMap[pid]) subunitMap[pid] = [];
        subunitMap[pid].push(String(s.unit_id));
    }

    const existingCRSet = new Set(
        (csv_existing_conversionratios || []).map(r => String(r.product) + '_' + String(r.subunit))
    );

    const seenCombos = {};
    let hasError = false;

    for (let idx = 0; idx < rows.length; idx++) {
        const r       = rows[idx];
        const pName   = (r['Product']          || '').trim();
        const masterVal = (r['Master Stock Unit'] || '').trim();
        const subName = (r['Substock Unit']    || '').trim();
        const ratio   = (r['Conversion Ratio'] || '').trim();
        let error = null;

        const product = findByName(csv_products, 'product_name', pName);
        if (!product) { error = 'Product not found: ' + pName; }

        if (!error && masterVal && norm(masterVal) !== norm(product.unit_name)) {
            error = 'Master Stock Unit mismatch: CSV has "' + masterVal + '" but product uses "' + product.unit_name + '"';
        }

        let subUnit = null;
        if (!error) {
            subUnit = findByName(csv_units, 'unit_name', subName);
            if (!subUnit) error = 'Substock Unit not found: ' + subName;
        }

        if (!error) {
            const productSubunits = subunitMap[String(product.id)] || [];
            if (!productSubunits.includes(String(subUnit.unit_id))) {
                error = '"' + subName + '" is not a substock unit of "' + pName + '"';
            }
        }

        if (!error && (!ratio || isNaN(parseFloat(ratio)) || parseFloat(ratio) <= 0)) {
            error = 'Invalid Conversion Ratio: ' + ratio;
        }

        if (!error) {
            const key = String(product.id) + '_' + String(subUnit.unit_id);
            if (seenCombos[key]) { error = 'Duplicate in CSV: ' + pName + ' + ' + subName; }
            else seenCombos[key] = true;
        }

        if (!error) {
            const dbKey = String(product.id) + '_' + String(subUnit.unit_id);
            if (existingCRSet.has(dbKey)) error = 'Already exists in DB: ' + pName + ' + ' + subName;
        }

        const masterUnit = product ? product.unit_name : '';
        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            hasError = true;
            previewTableData.push({ idx: previewTableData.length+1, pName: esc(pName), master: esc(masterUnit), subName: esc(subName), ratio: esc(ratio), badge, rowColor: '#fff5f5', hasError: true, _key: pName+'|'+subName });
            continue;
        }
        validatedData.push({ _key: pName+'|'+subName, payload: { product: product.id, subunit: subUnit.unit_id, conversion_ratio: parseFloat(ratio) } });
        previewTableData.push({ idx: previewTableData.length+1, pName: esc(pName), master: esc(masterUnit), subName: esc(subName), ratio: esc(ratio), badge, rowColor: '', hasError: false, _key: pName+'|'+subName });
    }

    buildPreviewDT(['#','Product','Master Stock Unit','Substock Unit','Conversion Ratio','Status'],
        ['idx','pName','master','subName','ratio','badge'], '#preview_table_cr');
    finishValidation(rows.length);
}

function initCRDT() {
    crDT = $('#bulkCRTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkConversionratioUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}

function deleteBulkRecord(id) {
    if (!confirm('Delete this upload record and all its conversion ratios?')) return;
    $.post($('#base_url').val() + 'delete_bulk_conversionratio/' + id, function() { if (crDT) crDT.ajax.reload(); });
}
</script>
