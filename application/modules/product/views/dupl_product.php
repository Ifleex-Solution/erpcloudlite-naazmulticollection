<!-- ── Product: Upload History ────────────────────────────────── -->
<div class="row" id="history_product" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Product</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkProductTable">
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
/* ── Product validation ──────────────────────────────────────── */
async function validateProductRows(rows) {
    $('#preview_table_product').show();

    const grouped = {}, order = [];
    for (const r of rows) {
        const bar = (r['Barcode/QR-code'] || '').trim();
        if (!bar) continue;
        if (!grouped[bar]) { grouped[bar] = []; order.push(bar); }
        grouped[bar].push(r);
    }
    if (!order.length) { alert('No valid rows found. Check "Barcode/QR-code" column.'); return; }

    const existingProductNames = new Set((csv_all_product_names || []).map(p => norm(p.product_name)));
    let hasError = false;

    for (let idx = 0; idx < order.length; idx++) {
        const barcode = order[idx];
        const group   = grouped[barcode];
        const first   = group[0];
        const pName   = (first['Product Name'] || '').trim();
        let error = null;

        if (pName && existingProductNames.has(norm(pName))) error = 'Product already found: ' + pName;

        const cat = !error ? findByName(csv_categories, 'category_name', first['Category']) : null;
        if (!error && !cat) error = 'Category not found: ' + first['Category'];

        let subcatId = '';
        if (!error && (first['Subcategory'] || '').trim()) {
            const subcat = findByName(csv_subcategories, 'subcategory_name', first['Subcategory']);
            if (!subcat) error = 'SubCategory not found: ' + first['Subcategory'];
            else subcatId = subcat.subcategory_id;
        }
        let brandId = '';
        if (!error && (first['Brand'] || '').trim()) {
            const brand = findByName(csv_brands, 'brand_name', first['Brand']);
            if (!brand) error = 'Brand not found: ' + first['Brand'];
            else brandId = brand.brand_id;
        }
        const oopId = (first['Origin of Product'] || '').trim();
        const storeVal = (first['Default Store'] || '').trim();
        let store = null;
        if (!error) {
            if (norm(storeVal) === 'n/a') { store = { id: 1 }; }
            else { store = findByName(csv_stores, 'name', storeVal); if (!store) error = 'Store not found: ' + storeVal; }
        }
        const unit = !error ? findByName(csv_units, 'unit_name', first['Master Stock Unit']) : null;
        if (!error && !unit) error = 'Unit not found: ' + first['Master Stock Unit'];

        // Unit Cost Logic Specification v1.1 (Phase 7) — UCL-46: Fixed Purchase Price is
        // mandatory and must be > 0 here too (see Product_form.php / Product::save_product() for
        // the same rule on the other product-creation paths), since the costing engine falls back
        // to this value whenever a batch has never been costed yet.
        const costPriceNum = num(first['Fixed Purchase Price']);
        if (!error && (!first['Fixed Purchase Price'] || isNaN(costPriceNum) || costPriceNum <= 0)) {
            error = 'Fixed Purchase Price must be a positive number';
        }

        let supplierId = '';
        if (!error && (first['Supplier'] || '').trim()) {
            const sup = findByName(csv_suppliers, 'supplier_name', first['Supplier']);
            if (sup) supplierId = sup.supplier_id;
        }

        let entries = [], subNames = [], primaryFound = false;
        let subunitError = null;
        for (const r of group) {
            const subUnitName = (r['Substock Unit'] || '').trim();
            if (!subUnitName) continue;
            const subUnit = findByName(csv_units, 'unit_name', subUnitName);
            if (!subUnit) {
                subunitError = 'Substock Unit not found: ' + subUnitName;
                entries.push({ id: 0, subunitid: '', subunit: subUnitName + ' (?)', subcost_price: num(r['Substock Purchase Price']), subsell_price: num(r['Substock Sale Price']), selected: false, selectedInt: 0 });
                subNames.push(subUnitName + ' (?)');
                continue;
            }
            const isPrimary = yesNo(r['Substock Primary (Yes/No)']);
            if (isPrimary) primaryFound = true;
            entries.push({ id: 0, subunitid: subUnit.unit_id, subunit: subUnit.unit_name, subcost_price: num(r['Substock Purchase Price']), subsell_price: num(r['Substock Sale Price']), selected: isPrimary === 1, selectedInt: isPrimary, conversion_ratio: num(r['Conversion Ratio']) });
            subNames.push(subUnit.unit_name);
        }
        if (!error && subunitError) error = subunitError;
        if (!error && entries.length > 0 && !primaryFound) { entries[0].selected = true; entries[0].selectedInt = 1; }

        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        if (error) {
            hasError = true;
            const rowIdx = previewTableData.length;
            const subunitsBtn = entries.length
                ? '<button class="btn btn-xs btn-info" onclick="showSubunitsPopup(' + rowIdx + ')"><i class="fa fa-list"></i> ' + entries.length + '</button>'
                : '<span class="text-muted">—</span>';
            previewTableData.push({
                idx: rowIdx+1, barcode: esc(barcode), pName: esc(pName),
                category: esc(first['Category']||''), subcategory: esc(first['Subcategory']||''),
                brand: esc(first['Brand']||''), store: esc(first['Default Store']||''),
                unit: esc(first['Master Stock Unit']||''), costPrice: esc(first['Fixed Purchase Price']||''),
                sellPrice: esc(first['Fixed Sale Price']||''), vat: esc(first['Product VAT (%)'||'']),
                prodStatus: esc(first['Status (Yes/No)']||''), supplier: esc(first['Supplier']||''),
                stock: esc(first['Stock (Yes/No)']||''), maxStock: esc(first['Max. Stock Level']||''),
                minStock: esc(first['Min. Stock Level']||''), reorderStock: esc(first['Reorder Stock Level']||''),
                reserveStock: esc(first['Reserve Stock Level']||''), subunitsBtn, badge,
                rowColor: '#fff5f5', hasError: true, entries, _key: barcode
            });
            continue;
        }

        validatedData.push({ _key: barcode, payload: {
            product_id: barcode, product_name: pName,
            serial_no: (first['Serial Number']||'').trim(),
            category_id: cat.category_id, subcategory_id: subcatId,
            brand_id: brandId, oop_id: oopId, supplier_id: supplierId,
            product_type: (first['Product Type']||'N/A').trim(),
            store: store.id, vat: num(first['Product VAT (%)']),
            defaultsaleprice: SALE_PRICE_MAP[norm(first['Default Sales Price'])]||'custom',
            product_model: (first['Model']||'').trim(), description: (first['Product Details']||'').trim(),
            unit: unit.unit_id, status: yesNo(first['Status (Yes/No)']),
            stock: yesNo(first['Stock (Yes/No)']),
            max_stock_level: num(first['Max. Stock Level']), min_stock_level: num(first['Min. Stock Level']),
            reorder_stock_level: num(first['Reorder Stock Level']), reserve_stock_level: num(first['Reserve Stock Level']),
            cost_price: num(first['Fixed Purchase Price']), sell_price: num(first['Fixed Sale Price']),
            batchtype: BATCH_TYPE_MAP[norm(first['Batch Type'])]||3,
            printname: (first['Product Print Name']||'').trim(),
            ad: '', bd: '', entries
        }});
        const rowIdx = previewTableData.length;
        const subunitsBtn = entries.length
            ? '<button class="btn btn-xs btn-info" onclick="showSubunitsPopup(' + rowIdx + ')"><i class="fa fa-list"></i> ' + entries.length + '</button>'
            : '<span class="text-muted">—</span>';
        previewTableData.push({
            idx: rowIdx+1, barcode: esc(barcode), pName: esc(pName),
            category: esc(first['Category']||''), subcategory: esc(first['Subcategory']||''),
            brand: esc(first['Brand']||''), store: esc(first['Default Store']||''),
            unit: esc(first['Master Stock Unit']||''), costPrice: esc(first['Fixed Purchase Price']||''),
            sellPrice: esc(first['Fixed Sale Price']||''), vat: esc(first['Product VAT (%)']||''),
            prodStatus: esc(first['Status (Yes/No)']||''), supplier: esc(first['Supplier']||''),
            stock: esc(first['Stock (Yes/No)']||''), maxStock: esc(first['Max. Stock Level']||''),
            minStock: esc(first['Min. Stock Level']||''), reorderStock: esc(first['Reorder Stock Level']||''),
            reserveStock: esc(first['Reserve Stock Level']||''), subunitsBtn, badge,
            rowColor: '', hasError: false, entries, _key: barcode
        });
    }

    buildPreviewDT(
        ['#','Barcode','Product Name','Category','Sub-Cat','Brand','Store','Master Unit','Cost Price','Sale Price','VAT%','Status','Supplier','Stock','Max Stock','Min Stock','Reorder','Reserve','Subunits','Validation'],
        ['idx','barcode','pName','category','subcategory','brand','store','unit','costPrice','sellPrice','vat','prodStatus','supplier','stock','maxStock','minStock','reorderStock','reserveStock','subunitsBtn','badge'],
        '#preview_table_product'
    );
    finishValidation(order.length);
}

function showSubunitsPopup(rowIdx) {
    const row = previewTableData[rowIdx];
    if (!row || !row.entries || !row.entries.length) { alert('No subunits for this product.'); return; }
    let html = '<table class="table table-bordered table-condensed"><thead><tr><th>Subunit</th><th>Purchase Price</th><th>Sale Price</th><th>Primary</th></tr></thead><tbody>';
    for (const e of row.entries) {
        html += '<tr><td>' + esc(e.subunit) + '</td><td>' + e.subcost_price + '</td><td>' + e.subsell_price + '</td><td>' + (e.selectedInt ? '<span class="label label-success">Yes</span>' : 'No') + '</td></tr>';
    }
    html += '</tbody></table>';
    $('#subunitsModalTitle').text(row.pName + ' — Subunits (' + row.entries.length + ')');
    $('#subunitsModalBody').html(html);
    $('#subunitsModal').modal('show');
}

function initProductDT() {
    productDT = $('#bulkProductTable').DataTable({
        responsive: true, processing: true, serverSide: true,
        order: [[1,'desc']], lengthMenu: [[10,25,50,100],[10,25,50,100]],
        dom: 'lfrtip', serverMethod: 'post',
        ajax: { url: $('#base_url').val() + 'checkBulkProductUpload', data: { csrf_test_name: $('#CSRF_TOKEN').val() } },
        columns: [{ data:'sl' },{ data:'uploaded_id' },{ data:'date' },{ data:'name' },{ data:'button', orderable:false }]
    });
}

function deleteBulkProduct(id) {
    if (!confirm('Delete this upload record and all its products?')) return;
    $.post($('#base_url').val() + 'delete_bulk_product/' + id, function() { if (productDT) productDT.ajax.reload(); });
}

var bulkDetailsDT      = null;
var bulkDetailsPending = null;
var bulkDetailsType    = null;

function showBulkDetails(id) {
    bulkDetailsType = currentFn();
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    $('#bulkDetailsBody').html('');
    bulkDetailsPending = null;

    const url = bulkDetailsType === 'product'
        ? $('#base_url').val() + 'get_bulk_product_details/' + id
        : $('#base_url').val() + 'get_bulk_conversionratio_details/' + id;

    $.get(url, function(resp) {
        const items = JSON.parse(resp);
        if (bulkDetailsType === 'product') {
            $('#detailsModalTitle').text('Uploaded Product IDs');
            $('#detailsTableHead').html('<tr><th>#</th><th>Product ID</th><th>Product Name</th></tr>');
            bulkDetailsPending = items.map(function(p, i) { return { sl: i+1, a: p.product_id||'', b: p.product_name||'' }; });
        } else {
            $('#detailsModalTitle').text('Uploaded Conversion Ratio Details');
            $('#detailsTableHead').html('<tr><th>#</th><th>Product</th><th>Master Unit</th><th>Substock Unit</th><th>Ratio</th></tr>');
            bulkDetailsPending = items.map(function(d, i) { return { sl: i+1, a: d.product_name||'', b: d.master_unit||'', c: d.subunit_name||'', e: d.conversion_ratio||'' }; });
        }
        if ($('#bulkDetailsModal').hasClass('in')) initDetailsTable();
    });
    $('#bulkDetailsModal').modal('show');
}

function initDetailsTable() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    const cols = bulkDetailsType === 'product'
        ? [{ data:'sl', width:'40px' },{ data:'a', title:'Product ID', width:'130px' },{ data:'b', title:'Product Name' }]
        : [{ data:'sl', width:'40px' },{ data:'a', title:'Product' },{ data:'b', title:'Master Unit', width:'110px' },{ data:'c', title:'Substock Unit', width:'110px' },{ data:'e', title:'Ratio', width:'80px' }];
    bulkDetailsDT = $('#bulkDetailsTable').DataTable({
        data: bulkDetailsPending || [], pageLength: 10,
        lengthMenu: [[10,25,50],[10,25,50]], ordering: false, autoWidth: false, columns: cols
    });
    bulkDetailsPending = null;
}

$('#bulkDetailsModal').on('shown.bs.modal', function() {
    if (bulkDetailsPending) initDetailsTable();
    else if (bulkDetailsDT) bulkDetailsDT.columns.adjust().draw(false);
});
$('#bulkDetailsModal').on('hidden.bs.modal', function() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    bulkDetailsPending = null;
});
</script>
