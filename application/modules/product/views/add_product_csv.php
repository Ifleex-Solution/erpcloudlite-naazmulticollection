<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>

<!-- Info Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title"><h4>CSV File Information</h4></div>
            </div>
            <div class="panel-body">
                <a href="<?php echo base_url('assets/data/csv/sample_product.csv') ?>" class="btn btn-primary pull-right">
                    <i class="fa fa-download"></i> Download Sample File
                </a>
                <span class="text-warning">The first line in downloaded csv file should remain as it is. Please do not change the order of columns.</span><br>
                The correct column order is <span class="text-info">(Barcode/QR-code, Serial Number, Product Name, Product Print Name, Category, SubCategory, Brand, Origin of Product, Product Type, Default Store, Default Sales Price, Batch Type, Product VAT (%), Product Details, Status (Yes/No), Master Stock Unit, Fixed Purchase Price, Fixed Sale Price, Substock Unit, Substock Purchase Price, Substock Sale Price, Substock Primary (Yes/No), Supplier, Stock (Yes/No), Max. Stock Level, Min. Stock Level, Reorder Stock Level, Reserve Stock Level)</span><br>
                Please make sure the csv file is <strong>UTF-8 encoded</strong> and not saved with byte order mark (BOM).<br>
                <span class="text-info"><i class="fa fa-info-circle"></i> Products with multiple substock units should have one row per substock unit (same Barcode repeated).</span>
            </div>
        </div>
    </div>
</div>

<!-- Upload Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title"><h4>Import Product CSV</h4></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">Upload CSV File <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" type="file" id="csvFile" accept=".csv">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="button" onclick="validateCSV()" id="btn_validate" class="btn btn-info">
                            <i class="fa fa-search"></i> Validate CSV
                        </button>
                        <button type="button" onclick="confirmSave()" id="btn_save" class="btn btn-success" style="display:none; margin-left:10px;">
                            <i class="fa fa-save"></i> Confirm &amp; Save
                        </button>
                        <span id="upload_status" style="margin-left:15px; font-weight:600;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validation Preview Panel -->
<div class="row" id="preview_panel" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title"><h4>Validation Preview</h4></div>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-condensed" id="preview_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Barcode</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Store</th>
                            <th>Unit</th>
                            <th>Substock Units</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="preview_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Upload History Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title"><h4>Uploaded Product Batches</h4></div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkProductTable">
                    <thead>
                        <tr>
                            <th><?php echo display('sl') ?></th>
                            <th>Upload ID</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th><?php echo display('action') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Product IDs Details Modal -->
<div class="modal fade" id="bulkDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Uploaded Product IDs</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed" id="bulkDetailsTable">
                    <thead><tr><th>#</th><th>Product ID</th><th>Product Name</th></tr></thead>
                    <tbody id="bulkDetailsBody"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$all_ids_raw  = $all_product_ids ?: [];
$all_ids_flat = array_values(array_filter(
    array_map(function($r){ return strtolower(trim($r['product_id'] ?? '')); }, $all_ids_raw),
    function($v){ return $v !== ''; }
));
echo "<script>";
echo "let csv_categories="    . json_encode($categories    ?: []) . ";";
echo "let csv_subcategories=" . json_encode($subcategories ?: []) . ";";
echo "let csv_brands="        . json_encode($brands        ?: []) . ";";
echo "let csv_oops="          . json_encode($oops          ?: []) . ";";
echo "let csv_stores="        . json_encode($stores        ?: []) . ";";
echo "let csv_units="         . json_encode($units         ?: []) . ";";
echo "let csv_suppliers="     . json_encode($suppliers     ?: []) . ";";
echo "let csv_all_product_ids=new Set(" . json_encode($all_ids_flat) . ");";
echo "console.log('[CSV Import] Loaded', csv_all_product_ids.size, 'existing barcodes from DB. Sample:', [...csv_all_product_ids].slice(0,5));";
echo "</script>";
?>

<script>
const SALE_PRICE_MAP   = { 'fixed price': 'fixedprice', 'mrp': 'mrp', 'custom': 'custom' };
const BATCH_TYPE_MAP   = { 'single': 1, 'multiple': 2, 'both': 3 };
const VALID_PROD_TYPES = ['N/A','Retail Good','Finished Good','Ingredients','Raw Material','Packing Material','MRO'];

let validatedProducts = [];
let previewTableData  = [];
let previewDT         = null;

function findByName(arr, nameField, val) {
    if (!val || !arr || !arr.length) return null;
    const v = val.toLowerCase().trim();
    return arr.find(r => (r[nameField] || '').toLowerCase().trim() === v) || null;
}
function yesNo(val)  { return (val || '').toLowerCase().trim() === 'yes' ? 1 : 0; }
function num(val)    { return parseFloat(val) || 0; }
function esc(str)    { return $('<div>').text(str || '').html(); }
function setStatus(msg, color) { $('#upload_status').css('color', color || '#888').text(msg); }

// ── STEP 1: Validate ──────────────────────────────────────────
async function validateCSV() {
    const file = document.getElementById('csvFile').files[0];
    if (!file) { alert('Please select a CSV file.'); return; }

    $('#btn_validate').prop('disabled', true);
    $('#btn_save').hide();
    $('#preview_panel').show();
    validatedProducts = [];
    previewTableData  = [];
    setStatus('Parsing CSV…');

    if (previewDT) { previewDT.destroy(); previewDT = null; }
    $('#preview_body').empty();

    const text      = await file.text();
    const cleanText = text.replace(/^﻿/, '');

    let rows;
    try {
        rows = Papa.parse(cleanText, {
            header: true, skipEmptyLines: true,
            transformHeader: h => h.trim()
        }).data;
    } catch(e) {
        alert('Failed to parse CSV: ' + e.message);
        $('#btn_validate').prop('disabled', false);
        return;
    }

    if (!rows.length) { alert('CSV file is empty.'); $('#btn_validate').prop('disabled', false); return; }

    // Group by Barcode
    const grouped = {}, order = [];
    for (const r of rows) {
        const bar = (r['Barcode/QR-code'] || '').trim();
        if (!bar) continue;
        if (!grouped[bar]) { grouped[bar] = []; order.push(bar); }
        grouped[bar].push(r);
    }

    if (!order.length) {
        alert('No valid rows found. Check "Barcode/QR-code" column.');
        $('#btn_validate').prop('disabled', false);
        return;
    }

    let hasError = false;

    for (let idx = 0; idx < order.length; idx++) {
        const barcode = order[idx];
        const group   = grouped[barcode];
        const first   = group[0];
        const pName   = (first['Product Name'] || '').trim();
        let   errors  = [];

        // ── 1. Duplication check ──────────────────────────────
        console.log('[CSV Import] Checking barcode:', JSON.stringify(barcode), '→', JSON.stringify(barcode.toLowerCase()), '| found in DB:', csv_all_product_ids.has(barcode.toLowerCase()));
        if (csv_all_product_ids.has(barcode.toLowerCase())) {
            errors.push('Duplicate: Barcode "' + barcode + '" already exists in database');
        }

        // ── 2. Required field checks ──────────────────────────
        if (!pName) errors.push('Product Name is required');

        const productTypeVal = (first['Product Type'] || '').trim();
        if (!productTypeVal) {
            errors.push('Product Type is required');
        } else if (!VALID_PROD_TYPES.map(v => v.toLowerCase()).includes(productTypeVal.toLowerCase())) {
            errors.push('Invalid Product Type "' + productTypeVal + '". Allowed: ' + VALID_PROD_TYPES.join(', '));
        }

        const salePriceKey = (first['Default Sales Price'] || '').toLowerCase().trim();
        if (!salePriceKey) {
            errors.push('Default Sales Price is required');
        } else if (!SALE_PRICE_MAP[salePriceKey]) {
            errors.push('Invalid Default Sales Price "' + first['Default Sales Price'] + '". Allowed: Fixed Price, MRP, Custom');
        }

        const batchTypeKey = (first['Batch Type'] || '').toLowerCase().trim();
        if (!batchTypeKey) {
            errors.push('Batch Type is required');
        } else if (!BATCH_TYPE_MAP[batchTypeKey]) {
            errors.push('Invalid Batch Type "' + first['Batch Type'] + '". Allowed: Single, Multiple, Both');
        }

        const statusVal = (first['Status (Yes/No)'] || '').toLowerCase().trim();
        if (!statusVal || !['yes','no'].includes(statusVal)) {
            errors.push('Status must be "Yes" or "No"');
        }

        const stockVal = (first['Stock (Yes/No)'] || '').toLowerCase().trim();
        if (!stockVal || !['yes','no'].includes(stockVal)) {
            errors.push('Stock must be "Yes" or "No"');
        }

        // ── 3. DB validations ─────────────────────────────────
        const cat = findByName(csv_categories, 'category_name', first['Category']);
        if (!cat) errors.push('Category not found: "' + (first['Category'] || '') + '"');

        let subcatId = '';
        if ((first['SubCategory'] || '').trim()) {
            const subcat = findByName(csv_subcategories, 'subcategory_name', first['SubCategory']);
            if (!subcat) errors.push('SubCategory not found: "' + first['SubCategory'] + '"');
            else subcatId = subcat.subcategory_id;
        }

        let brandId = '';
        if ((first['Brand'] || '').trim()) {
            const brand = findByName(csv_brands, 'brand_name', first['Brand']);
            if (!brand) errors.push('Brand not found: "' + first['Brand'] + '"');
            else brandId = brand.brand_id;
        }

        // Origin of Product — optional, silent ignore if not found
        let oopId = '';
        if ((first['Origin of Product'] || '').trim()) {
            const oop = findByName(csv_oops, 'oop_name', first['Origin of Product']);
            if (oop) oopId = oop.oop_id;
        }

        const storeVal2 = (first['Default Store'] || '').trim();
        let store = null;
        if (!storeVal2) {
            errors.push('Default Store is required');
        } else if (storeVal2.toLowerCase() === 'n/a') {
            store = { id: 1 };
        } else {
            store = findByName(csv_stores, 'name', storeVal2);
            if (!store) errors.push('Store not found: "' + storeVal2 + '"');
        }

        const unit = findByName(csv_units, 'unit_name', first['Master Stock Unit']);
        if (!unit) errors.push('Master Stock Unit not found: "' + (first['Master Stock Unit'] || '') + '"');

        // Supplier — optional, silent ignore if not found
        let supplierId = '';
        if ((first['Supplier'] || '').trim()) {
            const sup = findByName(csv_suppliers, 'supplier_name', first['Supplier']);
            if (sup) supplierId = sup.supplier_id;
        }

        // ── 4. Substock Units ─────────────────────────────────
        let entries = [], subNames = [], primaryFound = false;
        for (const r of group) {
            const subUnitName = (r['Substock Unit'] || '').trim();
            if (!subUnitName) continue;
            const subUnit = findByName(csv_units, 'unit_name', subUnitName);
            if (!subUnit) { errors.push('Substock Unit not found: "' + subUnitName + '"'); break; }
            const isPrimary = yesNo(r['Substock Primary (Yes/No)']);
            if (isPrimary) primaryFound = true;
            entries.push({
                id: 0, subunitid: subUnit.unit_id, subunit: subUnit.unit_name,
                subcost_price: num(r['Substock Purchase Price']),
                subsell_price: num(r['Substock Sale Price']),
                selected: isPrimary === 1, selectedInt: isPrimary
            });
            subNames.push(subUnit.unit_name);
        }
        if (entries.length > 0 && !primaryFound) {
            entries[0].selected = true; entries[0].selectedInt = 1;
        }

        // ── Build row ─────────────────────────────────────────
        const hasRowError = errors.length > 0;
        const errorText   = errors.join(' | ');
        const statusBadge = hasRowError
            ? '<span class="label label-danger" title="' + esc(errorText) + '">' + esc(errorText) + '</span>'
            : '<span class="label label-success">OK</span>';

        previewTableData.push({
            idx:      idx + 1,
            barcode:  barcode,
            pName:    pName,
            category: esc(first['Category'] || ''),
            store:    esc(first['Default Store'] || ''),
            unit:     esc(first['Master Stock Unit'] || ''),
            subNames: esc(subNames.join(', ') || '—'),
            badge:    statusBadge,
            rowColor: hasRowError ? '#fff5f5' : '#f5fff8',
            hasError: hasRowError
        });

        if (hasRowError) { hasError = true; continue; }

        validatedProducts.push({
            barcode, pName,
            payload: {
                product_id:          barcode,
                product_name:        pName,
                serial_no:           (first['Serial Number'] || '').trim(),
                category_id:         cat.category_id,
                subcategory_id:      subcatId,
                brand_id:            brandId,
                oop_id:              oopId,
                supplier_id:         supplierId,
                product_type:        productTypeVal,
                store:               store.id,
                vat:                 num(first['Product VAT (%)']),
                defaultsaleprice:    SALE_PRICE_MAP[salePriceKey],
                product_model:       '',
                description:         (first['Product Details'] || '').trim(),
                unit:                unit.unit_id,
                status:              yesNo(first['Status (Yes/No)']),
                stock:               yesNo(first['Stock (Yes/No)']),
                max_stock_level:     num(first['Max. Stock Level']),
                min_stock_level:     num(first['Min. Stock Level']),
                reorder_stock_level: num(first['Reorder Stock Level']),
                reserve_stock_level: num(first['Reserve Stock Level']),
                cost_price:          num(first['Fixed Purchase Price']),
                sell_price:          num(first['Fixed Sale Price']),
                batchtype:           BATCH_TYPE_MAP[batchTypeKey],
                printname:           (first['Product Print Name'] || '').trim(),
                ad: '', bd: '', entries: entries
            }
        });
    }

    // Build paginated DataTable
    previewDT = $('#preview_table').DataTable({
        data:      previewTableData,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering:  false,
        columns: [
            { data: 'idx' },
            { data: 'barcode' },
            { data: 'pName' },
            { data: 'category' },
            { data: 'store' },
            { data: 'unit' },
            { data: 'subNames' },
            { data: 'badge' }
        ],
        createdRow: function(row, data) {
            $(row).css('background', data.rowColor);
        }
    });

    $('#btn_validate').prop('disabled', false);

    if (hasError) {
        if (validatedProducts.length > 0) {
            $('#btn_save').show();
            setStatus(validatedProducts.length + ' valid, ' + (order.length - validatedProducts.length) + ' have errors (red rows will be skipped).', '#e6a817');
        } else {
            setStatus('All ' + order.length + ' rows have errors. Fix and re-upload.', '#c0392b');
        }
    } else {
        setStatus('All ' + validatedProducts.length + ' products valid. Click Confirm & Save.', '#27ae60');
        $('#btn_save').show();
    }
}

// ── STEP 2: Save ──────────────────────────────────────────────
async function confirmSave() {
    if (!validatedProducts.length) { alert('No valid products to save.'); return; }
    if (!confirm('Save ' + validatedProducts.length + ' product(s)?')) return;

    $('#btn_save').prop('disabled', true);
    $('#btn_validate').prop('disabled', true);
    setStatus('Saving 0 / ' + validatedProducts.length + '…', '#e6a817');

    let savedIds = [], failCount = 0;

    for (let idx = 0; idx < validatedProducts.length; idx++) {
        const p = validatedProducts[idx];
        try {
            const resp   = await $.ajax({ url: $('#base_url').val() + 'product/product/save_product', type: 'POST', data: p.payload });
            const result = JSON.parse(resp);

            // Find this barcode in previewTableData and update its badge
            const row = previewTableData.find(r => r.barcode === p.barcode);
            if (result && result.status === 'Success') {
                savedIds.push(result.id);
                if (row) { row.badge = '<span class="label label-success">Saved</span>'; row.rowColor = '#f5fff8'; }
            } else {
                failCount++;
                const errMsg = result && result.message ? result.message : JSON.stringify(result);
                if (row) { row.badge = '<span class="label label-danger">' + esc(errMsg) + '</span>'; row.rowColor = '#fff5f5'; }
            }
        } catch(e) {
            failCount++;
        }
        setStatus('Saving ' + (idx+1) + ' / ' + validatedProducts.length + '…', '#e6a817');
    }

    // Refresh DataTable cells with updated badges
    if (previewDT) { previewDT.rows().invalidate().draw(false); }

    if (savedIds.length > 0) {
        await $.ajax({
            url:  $('#base_url').val() + 'save_product_bulk_log',
            type: 'POST',
            data: { product_ids: savedIds }
        });
        $('#bulkProductTable').DataTable().ajax.reload();
    }

    $('#btn_save').prop('disabled', false);
    $('#btn_validate').prop('disabled', false);
    validatedProducts = [];
    $('#btn_save').hide();

    setStatus(savedIds.length + ' saved, ' + failCount + ' failed.', failCount > 0 ? '#c0392b' : '#27ae60');
}

// ── History DataTable ─────────────────────────────────────────
$(document).ready(function() {
    var base_url = $('#base_url').val();
    var csrf     = $('#CSRF_TOKEN').val();

    $('#bulkProductTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: "lfrtip",
        serverMethod: 'post',
        ajax: {
            url: base_url + 'checkBulkProductUpload',
            data: { csrf_test_name: csrf }
        },
        columns: [
            { data: 'sl' },
            { data: 'uploaded_id' },
            { data: 'date' },
            { data: 'name' },
            { data: 'button', orderable: false }
        ]
    });
});

function deleteBulkProduct(id) {
    if (!confirm('Delete this upload record and all its products?')) return;
    $.post($('#base_url').val() + 'delete_bulk_product/' + id, function() {
        $('#bulkProductTable').DataTable().ajax.reload();
    });
}

var bulkDetailsDT   = null;
var bulkDetailsPending = null;

function showBulkDetails(id) {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    $('#bulkDetailsBody').html('');
    bulkDetailsPending = null;

    $.get($('#base_url').val() + 'get_bulk_product_details/' + id, function(resp) {
        var products = JSON.parse(resp);
        bulkDetailsPending = products.map(function(p, i) {
            return { sl: i + 1, product_id: p.product_id || '', product_name: p.product_name || '' };
        });
        if ($('#bulkDetailsModal').hasClass('in')) {
            initBulkDetailsTable();
        }
    });

    $('#bulkDetailsModal').modal('show');
}

function initBulkDetailsTable() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    bulkDetailsDT = $('#bulkDetailsTable').DataTable({
        data:      bulkDetailsPending || [],
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering:   false,
        autoWidth:  false,
        columns: [
            { data: 'sl',           title: '#',            width: '50px' },
            { data: 'product_id',   title: 'Product ID',   width: '150px' },
            { data: 'product_name', title: 'Product Name' }
        ]
    });
    bulkDetailsPending = null;
}

$('#bulkDetailsModal').on('shown.bs.modal', function() {
    if (bulkDetailsPending) { initBulkDetailsTable(); }
    else if (bulkDetailsDT) { bulkDetailsDT.columns.adjust().draw(false); }
});

$('#bulkDetailsModal').on('hidden.bs.modal', function() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    bulkDetailsPending = null;
});
</script>
