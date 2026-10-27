<!-- ── Opening Stock: Upload History ───────────────────────────── -->
<div class="row" id="history_openingstock" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Opening Stock</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkOpeningStockTable">
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
/* ── Build lookup maps — called after all PHP→JS vars are injected ─ */
function _initOpeningStockMaps() {
    window._batchNameMap = {};
    (db_all_stockbatches || []).forEach(function(b) {
        if (b.batchid) window._batchNameMap[b.batchid.toLowerCase()] = b;
    });

    window._convRatioMap = {};
    (db_conversion_ratios || []).forEach(function(cr) {
        window._convRatioMap[cr.product + '_' + cr.subunit] = cr;
    });
}

function validateOpeningStockRows(rows) {
    $('#preview_table_openingstock').show();

    window._osValidationStore = [];   /* reset on each validation run */

    /* ── Group rows by the "ID" column ─ */
    var groups = {}, groupOrder = [];
    rows.forEach(function(r) {
        var gid = (r['ID'] || '').trim();
        if (!groups[gid]) { groups[gid] = []; groupOrder.push(gid); }
        groups[gid].push(r);
    });

    /* ── Build per-product master-unit map from csv_products ─ */
    var prodNameMap = {};   // product_name lower → {id, unit (master unit_id), unit_name}
    (csv_products || []).forEach(function(p) {
        prodNameMap[p.product_name.toLowerCase()] = p;
    });

    /* ── Store name → id map from JS `csv_stores` ─ */
    var storeNameMap = {};
    (csv_stores || []).forEach(function(s) {
        storeNameMap[s.name.toLowerCase()] = s.id;
    });

    /* ── Unit name → unit_id map from csv_units ─ */
    var unitNameMap = {};
    (csv_units || []).forEach(function(u) {
        unitNameMap[u.unit_name.toLowerCase()] = parseInt(u.unit_id);
    });

    var seenGroupIds = new Set();

    groupOrder.forEach(function(gid, gi) {
        var grpRows   = groups[gid];
        var first     = grpRows[0];
        var dateRaw   = (first['Date']          || '').trim();
        var reason    = (first['Reason']         || '').trim();
        var error     = null;
        var items     = [];

        if (!gid)          error = 'Transaction ID is required';
        else if (seenGroupIds.has(gid)) error = 'Duplicate Transaction ID in CSV: "' + gid + '"';
        else if (!dateRaw) error = 'Date is required';

        /* convert DD-Mon-YY or similar to YYYY-MM-DD */
        var parsedDate = '';
        if (!error) {
            var d = new Date(dateRaw);
            if (isNaN(d.getTime())) { error = 'Invalid date: "' + dateRaw + '"'; }
            else {
                var m = d.getMonth() + 1;
                var day = d.getDate();
                parsedDate = d.getFullYear() + '-' + (m < 10 ? '0'+m : m) + '-' + (day < 10 ? '0'+day : day);
            }
        }

        /* rowErrors: array of { rowIdx, messages[] } — one entry per failing CSV line */
        var rowErrors = [];
        var seenCombo = new Set();

        if (!error) {
            for (var j = 0; j < grpRows.length; j++) {
                var dr        = grpRows[j];
                var prodName  = (dr['Product'] || '').trim();
                var storeName = (dr['Store']   || '').trim();
                var batchName = (dr['Batch']   || '').trim();
                var unitName  = (dr['Unit']    || '').trim();
                var qtyRaw    = (dr['Qty']     || '').trim();
                var unitCostRaw = (dr['Unit Cost'] || '').trim();
                var lineErrs  = [];

                /* ── field-level checks (all independent) ── */
                if (!prodName)  lineErrs.push('Product is required');
                if (!storeName) lineErrs.push('Store is required');
                if (!batchName) lineErrs.push('Batch is required');
                if (!unitName)  lineErrs.push('Unit is required');
                if (qtyRaw === '' || isNaN(parseFloat(qtyRaw)) || parseFloat(qtyRaw) <= 0)
                    lineErrs.push('Qty must be a positive number');
                /* Unit Cost Logic Specification v1.1 (Phase 7, DUP-03) — Opening Stock's Unit
                   Cost is a user-entered historical fact (UCL-40), same as the live New Inventory
                   screen, so it's mandatory and must be > 0 here too, not defaulted or skipped. */
                if (unitCostRaw === '' || isNaN(parseFloat(unitCostRaw)) || parseFloat(unitCostRaw) <= 0)
                    lineErrs.push('Unit Cost must be a positive number');

                /* ── lookup checks ── */
                var prodObj = prodName ? prodNameMap[prodName.toLowerCase()] : null;
                if (prodName && !prodObj) lineErrs.push('Product not found: "'+prodName+'"');

                var storeId = storeName ? storeNameMap[storeName.toLowerCase()] : undefined;
                if (storeName && storeId === undefined) lineErrs.push('Store not found: "'+storeName+'"');

                var batchId = null;
                if (batchName) {
                    if (batchName.toLowerCase() === 'default') {
                        batchId = 1;
                    } else {
                        var batchObj = window._batchNameMap[batchName.toLowerCase()];
                        if (!batchObj) {
                            lineErrs.push('Batch not found: "'+batchName+'"');
                        } else {
                            if (batchObj.busage === 'single' && prodObj && parseInt(batchObj.product) !== parseInt(prodObj.id))
                                lineErrs.push('Batch "'+batchName+'" is not assigned to product "'+prodName+'"');
                            batchId = batchObj.id;
                        }
                    }
                }

                var unitId = unitName ? unitNameMap[unitName.toLowerCase()] : undefined;
                if (unitName && unitId === undefined) lineErrs.push('Unit not found: "'+unitName+'"');

                /* ── unit conversion (only when product + unit both resolved) ── */
                var conversionid = '';
                var convertedQty = parseFloat(qtyRaw) || 0;
                if (prodObj && unitId !== undefined) {
                    if (unitId !== parseInt(prodObj.unit)) {
                        var crKey = prodObj.id + '_' + unitId;
                        var cr    = window._convRatioMap[crKey];
                        if (!cr) {
                            lineErrs.push('Unit "'+unitName+'" not valid for product "'+prodName+'"');
                        } else {
                            conversionid = cr.conversionratio_id;
                            var ratio = parseFloat(cr.conversion_ratio);
                            if      (cr.convertiontype === '*') convertedQty = parseFloat(qtyRaw) / ratio;
                            else if (cr.convertiontype === '/') convertedQty = parseFloat(qtyRaw) * ratio;
                            else if (cr.convertiontype === '+') convertedQty = parseFloat(qtyRaw) - ratio;
                            else if (cr.convertiontype === '-') convertedQty = parseFloat(qtyRaw) + ratio;
                        }
                    }
                }

                /* ── duplicate combo check (only when all lookups passed) ── */
                if (lineErrs.length === 0) {
                    var comboKey = prodObj.id + '_' + batchId + '_' + storeId;
                    if (seenCombo.has(comboKey)) {
                        lineErrs.push('Duplicate (Product, Batch, Store) in same transaction');
                    } else {
                        seenCombo.add(comboKey);
                        var unitCostNum = parseFloat(unitCostRaw);
                        items.push({
                            product_id:   prodObj.id,
                            store_id:     storeId,
                            batch_id:     batchId,
                            unit_id:      unitId,
                            conversionid: conversionid,
                            qty:          parseFloat(convertedQty.toFixed(6)),
                            aqty:         qtyRaw + ' ' + unitName,
                            // UCL-11 — line value is never converted, only quantity: computed
                            // from the raw as-entered Qty (qtyRaw), not the base-unit convertedQty.
                            unitcost:     unitCostNum,
                            linevalue:    parseFloat((parseFloat(qtyRaw) * unitCostNum).toFixed(6))
                        });
                    }
                }

                if (lineErrs.length > 0) rowErrors.push({ rowIdx: j, messages: lineErrs });
            }
        }

        var hasGroupError = !!error;
        var hasRowErrors  = rowErrors.length > 0;
        if (!hasGroupError && !hasRowErrors) seenGroupIds.add(gid);

        var prodList = grpRows.map(function(r){ return esc((r['Product']||'').trim()); }).join(', ');

        if (hasGroupError || hasRowErrors) {
            var storeIdx = window._osValidationStore.length;
            window._osValidationStore.push({ gid: gid, csvRows: grpRows.slice(), groupError: error, rowErrors: rowErrors });

            var totalErr  = hasGroupError ? 1 : rowErrors.length;
            var badgeText = totalErr === 1
                ? esc(hasGroupError ? error : rowErrors[0].messages[0])
                : totalErr + ' errors found';
            var badge     = '<span class="label label-danger">'+badgeText+'</span>';
            var detailBtn = '<button class="btn btn-xs btn-danger" onclick="showOSValidationDetail('+storeIdx+')" title="View all errors"><i class="fa fa-search"></i> Details</button>';

            previewTableData.push({ idx:gi+1, gid:esc(gid), dateRaw:esc(dateRaw), reason:esc(reason), items:grpRows.length, prodList:prodList, badge:badge, action:detailBtn, rowColor:'#fff5f5', hasError:true, _key:gid });
        } else {
            validatedData.push({ _key:gid, payload:{ date:parsedDate, reason, items_json:JSON.stringify(items) } });
        }
    });

    buildPreviewDT(
        ['#','Transaction ID','Date','Reason','Lines','Products','Error',''],
        ['idx','gid','dateRaw','reason','items','prodList','badge','action'],
        '#preview_table_openingstock'
    );
    finishValidation(groupOrder.length);
}

function initOpeningStockDT() {
    openingstockDT = $('#bulkOpeningStockTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkOpeningStockUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}
var _osDT = null;
function showBulkOpeningStockDetails(id) {
    if ($.fn.DataTable.isDataTable('#osDetailsTable')) {
        $('#osDetailsTable').DataTable().destroy();
    }
    $('#osDetailsTable tbody').empty();
    $('#osDetailsModal').modal('show');

    $.get($('#base_url').val() + 'get_bulk_openingstock_details/' + id, function(raw) {
        var rows; try { rows = JSON.parse(raw); } catch(e){ rows = []; }

        /* assign alternating group colours per adj_id */
        var adjColours = {}, colIdx = 0, palette = ['#ffffff','#f0f4ff'];
        rows.forEach(function(r) {
            if (adjColours[r.adj_id] === undefined) {
                adjColours[r.adj_id] = palette[colIdx % 2];
                colIdx++;
            }
        });

        var data = rows.map(function(r, i) {
            return {
                sl:           i + 1,
                adj_id:       r.adj_id,
                date:         r.date,
                reason:       r.reason || '—',
                product_name: r.product_name,
                store_name:   r.store_name,
                batch_name:   r.batch_name || 'Default',
                qty:          parseFloat(r.qty),
                unit_name:    r.unit_name,
                _bg:          adjColours[r.adj_id]
            };
        });

        _osDT = $('#osDetailsTable').DataTable({
            data:       data,
            pageLength: 25,
            lengthMenu: [[10,25,50,100],[10,25,50,100]],
            ordering:   false,
            autoWidth:  false,
            dom:        '<"row"<"col-sm-4"l><"col-sm-4 text-center" i><"col-sm-4"f>>rt<"row"<"col-sm-12"p>>',
            columns: [
                { data:'sl',           width:'40px',  className:'text-center' },
                { data:'adj_id',       width:'80px',  className:'text-center' },
                { data:'date',         width:'95px' },
                { data:'reason' },
                { data:'product_name' },
                { data:'store_name' },
                { data:'batch_name' },
                {
                    data:'qty',
                    className:'text-right',
                    render: function(val, type, row) {
                        if (type === 'display') {
                            return '<strong>' + val.toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:4})
                                 + '</strong> <span class="text-muted">' + esc(row.unit_name) + '</span>';
                        }
                        return val;
                    }
                }
            ],
            createdRow: function(tr, rowData) {
                $(tr).css('background-color', rowData._bg);
            },
            language: { emptyTable: 'No stock lines found.' }
        });
    });
}
var _osValDT = null;
function showOSValidationDetail(idx) {
    var entry = (window._osValidationStore || [])[idx];
    if (!entry) return;

    var titleSuffix = entry.groupError
        ? entry.groupError
        : (entry.rowErrors.length === 1 ? '1 error found' : entry.rowErrors.length + ' errors found');
    $('#osValDetailTitle').text('Error Details — Transaction: ' + entry.gid);
    $('#osValDetailError').text(titleSuffix);

    /* build data rows for DataTables */
    var dtRows = [];

    if (entry.groupError) {
        dtRows.push({
            rowNum:  '!',
            product: '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + esc(entry.groupError) + '</span>',
            store: '', batch: '', unit: '', qty: '',
            status: ''
        });
    }

    (entry.rowErrors || []).forEach(function(re) {
        var r = entry.csvRows[re.rowIdx];
        if (!r) return;
        dtRows.push({
            rowNum:  '<i class="fa fa-times-circle text-danger"></i> ' + (re.rowIdx + 1),
            product: esc(r['Product'] || ''),
            store:   esc(r['Store']   || ''),
            batch:   esc(r['Batch']   || ''),
            unit:    esc(r['Unit']    || ''),
            qty:     esc(r['Qty']     || ''),
            status:  re.messages.map(function(m){
                return '<span class="label label-danger" style="display:inline-block;margin:1px 2px 1px 0;">'+esc(m)+'</span>';
            }).join(' ')
        });
    });

    /* destroy previous instance */
    if (_osValDT) { try { _osValDT.destroy(); } catch(e){} _osValDT = null; }
    $('#osValDetailTable tbody').empty();

    _osValDT = $('#osValDetailTable').DataTable({
        data: dtRows,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering: false,
        autoWidth: false,
        columns: [
            { data: 'rowNum',  title: '#',       width: '50px', className: 'text-center' },
            { data: 'product', title: 'Product'  },
            { data: 'store',   title: 'Store'    },
            { data: 'batch',   title: 'Batch'    },
            { data: 'unit',    title: 'Unit'     },
            { data: 'qty',     title: 'Qty',     className: 'text-right' },
            { data: 'status',  title: 'Error(s)' }
        ],
        createdRow: function(row) { $(row).css({ background: '#fff0f0', fontWeight: '600' }); },
        initComplete: function() {
            $('#osValDetailTable thead tr th').css({ background: '#f8f8f8', fontWeight: '700' });
        },
        language: { emptyTable: 'No errors to display.' }
    });

    $('#osValDetailModal').modal('show');
}
function deleteBulkOpeningStock(id) {
    if (!confirm('Delete this batch? This will remove all associated stock entries from the system.')) return;
    $.post($('#base_url').val()+'delete_bulk_openingstock/'+id, function(){ if(openingstockDT) openingstockDT.ajax.reload(); });
}
$('#osDetailsModal').on('hidden.bs.modal', function() {
    if (_osDT) { try { _osDT.destroy(); } catch(e){} _osDT = null; }
});
$('#osValDetailModal').on('hidden.bs.modal', function() {
    if (_osValDT) { try { _osValDT.destroy(); } catch(e){} _osValDT = null; }
});
</script>

<!-- ── Opening Stock Details Modal ──────────────────────────── -->
<div class="modal fade" id="osDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width:90%;max-width:1000px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#f4f6f9;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-list-alt"></i> Opening Stock Upload Details</h4>
            </div>
            <div class="modal-body" style="padding:12px 16px;">
                <table class="table table-bordered table-condensed" id="osDetailsTable" style="width:100%;font-size:13px;">
                    <thead style="background:#e8edf3;">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:80px;">Trans. ID</th>
                            <th style="width:95px;">Date</th>
                            <th>Reason</th>
                            <th>Product</th>
                            <th>Store</th>
                            <th>Batch</th>
                            <th class="text-right">Qty</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ── Opening Stock Validation Error Detail Modal ───────────── -->
<div class="modal fade" id="osValDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width:88%;max-width:920px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff0f0;border-bottom:2px solid #e74c3c;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-danger" id="osValDetailTitle">
                    <i class="fa fa-exclamation-circle"></i> Error Details
                </h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <div class="alert alert-danger" style="margin:14px 16px 0;border-radius:4px;">
                    <i class="fa fa-times-circle"></i>
                    <strong>Error: </strong><span id="osValDetailError"></span>
                </div>
                <div style="padding:12px 16px;">
                    <table class="table table-bordered table-condensed" id="osValDetailTable" style="width:100%;font-size:13px;">
                        <thead style="background:#f8f8f8;"></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

