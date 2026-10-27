<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>

<!-- Info Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title"><h4>CSV File Information</h4></div>
            </div>
            <div class="panel-body">
                <a href="<?php echo base_url('assets/data/csv/sample_conversionratio.csv') ?>" class="btn btn-primary pull-right">
                    <i class="fa fa-download"></i> Download Sample File
                </a>
                <span class="text-warning">The first line in downloaded csv file should remain as it is. Please do not change the order of columns.</span><br>
                The correct column order is <span class="text-info">(Product, Master Stock Unit, Substock Unit, Conversion Ratio)</span><br>
                Please make sure the csv file is <strong>UTF-8 encoded</strong>.<br>
                <span class="text-info"><i class="fa fa-info-circle"></i> Each row represents one substock unit conversion for a product. Same product can have multiple rows for different substock units.</span>
            </div>
        </div>
    </div>
</div>

<!-- Upload Panel -->
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title"><h4>Import Conversion Ratio CSV</h4></div>
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
                            <th>Product</th>
                            <th>Master Stock Unit</th>
                            <th>Substock Unit</th>
                            <th>Conversion Ratio</th>
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
                <div class="panel-title"><h4>Uploaded Conversion Ratio Batches</h4></div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkCRTable">
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

<!-- Details Modal -->
<div class="modal fade" id="bulkDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Uploaded Conversion Ratio Details</h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed" id="bulkDetailsTable">
                    <thead>
                        <tr><th>#</th><th>Product</th><th>Master Unit</th><th>Substock Unit</th><th>Conversion Ratio</th></tr>
                    </thead>
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
echo "<script>";
echo "let csv_products=" . json_encode($products ?: []) . ";";
echo "let csv_units="    . json_encode($units    ?: []) . ";";
echo "</script>";
?>

<script>
let validatedRows  = [];
let previewTableData = [];
let previewDT      = null;

function findByName(arr, nameField, val) {
    if (!val || !arr || !arr.length) return null;
    const v = val.toLowerCase().trim();
    return arr.find(r => (r[nameField] || '').toLowerCase().trim() === v) || null;
}
function num(val) { return parseFloat(val) || 0; }
function esc(str) { return $('<div>').text(str || '').html(); }
function setStatus(msg, color) { $('#upload_status').css('color', color || '#888').text(msg); }

// ── STEP 1: Validate ──────────────────────────────────────────
async function validateCSV() {
    const file = document.getElementById('csvFile').files[0];
    if (!file) { alert('Please select a CSV file.'); return; }

    $('#btn_validate').prop('disabled', true);
    $('#btn_save').hide();
    $('#preview_panel').show();
    validatedRows    = [];
    previewTableData = [];
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

    // Track intra-CSV duplicates
    const seenCombos = {};
    let hasError = false;

    for (let idx = 0; idx < rows.length; idx++) {
        const r         = rows[idx];
        const pName     = (r['Product'] || '').trim();
        const subName   = (r['Substock Unit'] || '').trim();
        const ratio     = (r['Conversion Ratio'] || '').trim();
        let   error     = null;

        // Product lookup
        const product = findByName(csv_products, 'product_name', pName);
        if (!product) error = 'Product not found: ' + pName;

        // Substock unit lookup
        let subUnit = null;
        if (!error) {
            subUnit = findByName(csv_units, 'unit_name', subName);
            if (!subUnit) error = 'Substock Unit not found: ' + subName;
        }

        // Ratio validation
        if (!error && (!ratio || isNaN(parseFloat(ratio)) || parseFloat(ratio) <= 0)) {
            error = 'Invalid Conversion Ratio: ' + ratio;
        }

        // Intra-CSV duplicate check
        if (!error) {
            const key = product.id + '_' + subUnit.unit_id;
            if (seenCombos[key]) {
                error = 'Duplicate in CSV: ' + pName + ' + ' + subName;
            } else {
                seenCombos[key] = true;
            }
        }

        const masterUnit = product ? product.unit_name : '';
        const badge = error
            ? '<span class="label label-danger" title="' + esc(error) + '">' + esc(error) + '</span>'
            : '<span class="label label-success">OK</span>';

        previewTableData.push({
            idx:      idx + 1,
            pName:    esc(pName),
            master:   esc(masterUnit),
            subName:  esc(subName),
            ratio:    esc(ratio),
            badge:    badge,
            rowColor: error ? '#fff5f5' : '#f5fff8',
            hasError: !!error
        });

        if (error) { hasError = true; continue; }

        validatedRows.push({
            pName, subName, masterUnit, ratio,
            payload: {
                product:          product.id,
                subunit:          subUnit.unit_id,
                conversion_ratio: parseFloat(ratio)
            }
        });
    }

    previewDT = $('#preview_table').DataTable({
        data:      previewTableData,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering:  false,
        autoWidth: false,
        columns: [
            { data: 'idx',     width: '40px' },
            { data: 'pName' },
            { data: 'master',  width: '140px' },
            { data: 'subName', width: '140px' },
            { data: 'ratio',   width: '120px' },
            { data: 'badge' }
        ],
        createdRow: function(row, data) { $(row).css('background', data.rowColor); }
    });

    $('#btn_validate').prop('disabled', false);

    if (hasError) {
        if (validatedRows.length > 0) {
            $('#btn_save').show();
            setStatus(validatedRows.length + ' valid, ' + (rows.length - validatedRows.length) + ' have errors (red rows will be skipped).', '#e6a817');
        } else {
            setStatus('All ' + rows.length + ' rows have errors. Fix and re-upload.', '#c0392b');
        }
    } else {
        setStatus('All ' + validatedRows.length + ' rows are valid. Click Confirm & Save.', '#27ae60');
        $('#btn_save').show();
    }
}

// ── STEP 2: Save ──────────────────────────────────────────────
async function confirmSave() {
    if (!validatedRows.length) { alert('No valid rows to save.'); return; }
    if (!confirm('Save ' + validatedRows.length + ' conversion ratio(s)?')) return;

    $('#btn_save').prop('disabled', true);
    $('#btn_validate').prop('disabled', true);
    setStatus('Saving 0 / ' + validatedRows.length + '…', '#e6a817');

    let savedIds = [], failCount = 0;

    for (let idx = 0; idx < validatedRows.length; idx++) {
        const r = validatedRows[idx];
        try {
            const resp   = await $.ajax({ url: $('#base_url').val() + 'save_conversionratio_from_csv', type: 'POST', data: r.payload });
            const result = JSON.parse(resp);

            const row = previewTableData.find(d => !d.hasError && d.pName === esc(r.pName) && d.subName === esc(r.subName));
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
        setStatus('Saving ' + (idx + 1) + ' / ' + validatedRows.length + '…', '#e6a817');
    }

    if (previewDT) { previewDT.rows().invalidate().draw(false); }

    if (savedIds.length > 0) {
        await $.ajax({
            url:  $('#base_url').val() + 'save_conversionratio_bulk_log',
            type: 'POST',
            data: { conversionratio_ids: savedIds }
        });
        $('#bulkCRTable').DataTable().ajax.reload();
    }

    $('#btn_save').prop('disabled', false);
    $('#btn_validate').prop('disabled', false);
    validatedRows = [];
    $('#btn_save').hide();

    setStatus(savedIds.length + ' saved, ' + failCount + ' failed.', failCount > 0 ? '#c0392b' : '#27ae60');
}

// ── History DataTable ─────────────────────────────────────────
$(document).ready(function() {
    var base_url = $('#base_url').val();
    var csrf     = $('#CSRF_TOKEN').val();

    $('#bulkCRTable').DataTable({
        responsive:  true,
        processing:  true,
        serverSide:  true,
        order:       [[1, 'desc']],
        lengthMenu:  [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom:         "lfrtip",
        serverMethod:'post',
        ajax: {
            url:  base_url + 'checkBulkConversionratioUpload',
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

function deleteBulkRecord(id) {
    if (!confirm('Delete this upload record and all its conversion ratios?')) return;
    $.post($('#base_url').val() + 'delete_bulk_conversionratio/' + id, function() {
        $('#bulkCRTable').DataTable().ajax.reload();
    });
}

// ── Details Modal ─────────────────────────────────────────────
var bulkDetailsDT      = null;
var bulkDetailsPending = null;

function showBulkDetails(id) {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    $('#bulkDetailsBody').html('');
    bulkDetailsPending = null;

    $.get($('#base_url').val() + 'get_bulk_conversionratio_details/' + id, function(resp) {
        var items = JSON.parse(resp);
        bulkDetailsPending = items.map(function(d, i) {
            return {
                sl:     i + 1,
                pname:  d.product_name  || '',
                master: d.master_unit   || '',
                sub:    d.subunit_name  || '',
                ratio:  d.conversion_ratio || ''
            };
        });
        if ($('#bulkDetailsModal').hasClass('in')) { initDetailsTable(); }
    });

    $('#bulkDetailsModal').modal('show');
}

function initDetailsTable() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    bulkDetailsDT = $('#bulkDetailsTable').DataTable({
        data:      bulkDetailsPending || [],
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering:  false,
        autoWidth: false,
        columns: [
            { data: 'sl',     title: '#',               width: '40px' },
            { data: 'pname',  title: 'Product' },
            { data: 'master', title: 'Master Unit',      width: '130px' },
            { data: 'sub',    title: 'Substock Unit',    width: '130px' },
            { data: 'ratio',  title: 'Conversion Ratio', width: '130px' }
        ]
    });
    bulkDetailsPending = null;
}

$('#bulkDetailsModal').on('shown.bs.modal', function() {
    if (bulkDetailsPending) { initDetailsTable(); }
    else if (bulkDetailsDT) { bulkDetailsDT.columns.adjust().draw(false); }
});

$('#bulkDetailsModal').on('hidden.bs.modal', function() {
    if (bulkDetailsDT) { bulkDetailsDT.destroy(); bulkDetailsDT = null; }
    bulkDetailsPending = null;
});
</script>
