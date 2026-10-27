<!-- ── Stock Adjustment: Upload History ──────────────────────── -->
<div class="row" id="history_stockadjustment" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Stock Adjustment</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkStockAdjustmentTable">
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
var _saDT = null;
var _saValDT = null;

function initStockAdjustmentDT() {
    stockadjustmentDT = $('#bulkStockAdjustmentTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkStockAdjustmentUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}

var _INCIDENT_TYPE_MAP = {
    'stock adjustment': 'stockadjustment',
    'opening stock':    'openingstock',
    'store transfer':   'storetransfer',
    'stock disposal':   'stockdisposal'
};
var _STOCK_TYPE_MAP = {
    'actual stock':   'actualstock',
    'physical stock': 'physicalstock',
    'both':           'both'
};
var _ADJ_TYPE_MAP = { 'increase': 'increase', 'decrease': 'decrease' };

window._saValidationStore = [];

function validateStockAdjustmentRows(rows) {
    $('#preview_table_stockadjustment').show();
    window._saValidationStore = [];

    /* ── Build lookup maps ── */
    var prodNameMap = {};
    (csv_products || []).forEach(function(p) {
        prodNameMap[p.product_name.toLowerCase().trim()] = p;
    });
    var storeNameMap = {};
    (csv_stores || []).forEach(function(s) {
        storeNameMap[s.name.toLowerCase().trim()] = s.id;
    });
    var unitNameMap = {};
    (csv_units || []).forEach(function(u) {
        unitNameMap[u.unit_name.toLowerCase().trim()] = parseInt(u.unit_id);
    });

    /* ── Group by ID column ── */
    var groups = {}, groupOrder = [];
    rows.forEach(function(r) {
        var gid = (r['ID'] || '').trim();
        if (!groups[gid]) { groups[gid] = []; groupOrder.push(gid); }
        groups[gid].push(r);
    });

    var seenGroupIds = new Set();

    groupOrder.forEach(function(gid, gi) {
        var grpRows     = groups[gid];
        var first       = grpRows[0];
        var dateRaw     = (first['Date']          || '').trim();
        var incidentRaw = (first['Incident Type'] || '').trim();
        var stockTypeRaw= (first['Stock Type']    || '').trim();
        var reason      = (first['Reason']        || '').trim();
        var error       = null;

        if (!gid)
            error = 'ID is required';
        else if (seenGroupIds.has(gid))
            error = 'Duplicate ID in CSV: "' + gid + '"';
        else if (!dateRaw)
            error = 'Date is required';

        var parsedDate = '';
        if (!error) {
            var d = new Date(dateRaw);
            if (isNaN(d.getTime())) {
                error = 'Invalid date: "' + dateRaw + '"';
            } else {
                var mo  = d.getMonth() + 1;
                var day = d.getDate();
                parsedDate = d.getFullYear() + '-' + (mo < 10 ? '0'+mo : mo) + '-' + (day < 10 ? '0'+day : day);
            }
        }

        var incidentType = null, stockType = null;
        if (!error) {
            incidentType = _INCIDENT_TYPE_MAP[incidentRaw.toLowerCase()] || null;
            if (!incidentType) error = 'Invalid Incident Type: "' + incidentRaw + '"';
        }
        if (!error) {
            stockType = _STOCK_TYPE_MAP[stockTypeRaw.toLowerCase()] || null;
            if (!stockType) error = 'Invalid Stock Type: "' + stockTypeRaw + '"';
        }

        var items     = [];
        var rowErrors = [];

        if (!error) {
            for (var j = 0; j < grpRows.length; j++) {
                var dr        = grpRows[j];
                var prodName  = (dr['Product']  || '').trim();
                var storeName = (dr['Store']    || '').trim();
                var batchName = (dr['Batch']    || '').trim();
                var unitName  = (dr['Unit']     || '').trim();
                var adjRaw    = (dr['Adj.Type'] || '').trim();
                var adjQtyRaw = (dr['Adj.Qty']  || '').trim();
                var lineErrs  = [];

                if (!prodName)  lineErrs.push('Product is required');
                if (!storeName) lineErrs.push('Store is required');
                if (!batchName) lineErrs.push('Batch is required');
                if (!unitName)  lineErrs.push('Unit is required');
                if (!adjRaw)    lineErrs.push('Adj.Type is required');
                if (adjQtyRaw === '' || isNaN(parseFloat(adjQtyRaw)) || parseFloat(adjQtyRaw) <= 0)
                    lineErrs.push('Adj.Qty must be a positive number');

                var adjType = _ADJ_TYPE_MAP[adjRaw.toLowerCase()] || null;
                if (adjRaw && !adjType) lineErrs.push('Adj.Type must be "Increase" or "Decrease"');

                var prodObj = prodName ? prodNameMap[prodName.toLowerCase()] : null;
                if (prodName && !prodObj) lineErrs.push('Product not found: "' + prodName + '"');

                var storeId = storeName ? storeNameMap[storeName.toLowerCase()] : undefined;
                if (storeName && storeId === undefined) lineErrs.push('Store not found: "' + storeName + '"');

                var batchId = null;
                if (batchName) {
                    if (batchName.toLowerCase() === 'default') {
                        batchId = 1;
                    } else {
                        var batchObj = (window._batchNameMap || {})[batchName.toLowerCase()];
                        if (!batchObj) {
                            lineErrs.push('Batch not found: "' + batchName + '"');
                        } else {
                            if (batchObj.busage === 'single' && prodObj && parseInt(batchObj.product) !== parseInt(prodObj.id))
                                lineErrs.push('Batch "' + batchName + '" is not assigned to product "' + prodName + '"');
                            batchId = batchObj.id;
                        }
                    }
                }

                var unitId = unitName ? unitNameMap[unitName.toLowerCase()] : undefined;
                if (unitName && unitId === undefined) lineErrs.push('Unit not found: "' + unitName + '"');

                var conversionid = '';
                var convertedQty = parseFloat(adjQtyRaw) || 0;
                if (lineErrs.length === 0 && prodObj && unitId !== undefined) {
                    if (unitId !== parseInt(prodObj.unit)) {
                        var crKey = prodObj.id + '_' + unitId;
                        var cr    = (window._convRatioMap || {})[crKey];
                        if (!cr) {
                            lineErrs.push('Unit "' + unitName + '" not valid for product "' + prodName + '"');
                        } else {
                            conversionid = cr.conversionratio_id;
                            var ratio    = parseFloat(cr.conversion_ratio);
                            if      (cr.convertiontype === '*') convertedQty = parseFloat(adjQtyRaw) / ratio;
                            else if (cr.convertiontype === '/') convertedQty = parseFloat(adjQtyRaw) * ratio;
                            else if (cr.convertiontype === '+') convertedQty = parseFloat(adjQtyRaw) - ratio;
                            else if (cr.convertiontype === '-') convertedQty = parseFloat(adjQtyRaw) + ratio;
                        }
                    }
                }

                if (lineErrs.length === 0) {
                    items.push({
                        product_id:   prodObj.id,
                        store_id:     storeId,
                        batch_id:     batchId,
                        unit_id:      unitId,
                        conversionid: conversionid,
                        adj:          adjType,
                        qty:          parseFloat(convertedQty.toFixed(6)),
                        aqty:         adjQtyRaw + ' ' + unitName
                    });
                } else {
                    rowErrors.push({ rowIdx: j, messages: lineErrs });
                }
            }
        }

        var hasGroupError = !!error;
        var hasRowErrors  = rowErrors.length > 0;
        if (!hasGroupError && !hasRowErrors) seenGroupIds.add(gid);

        var prodList = grpRows.map(function(r) { return esc((r['Product'] || '').trim()); }).join(', ');

        if (hasGroupError || hasRowErrors) {
            var storeIdx = window._saValidationStore.length;
            window._saValidationStore.push({ gid: gid, csvRows: grpRows.slice(), groupError: error, rowErrors: rowErrors });

            var totalErr  = hasGroupError ? 1 : rowErrors.length;
            var badgeText = totalErr === 1
                ? esc(hasGroupError ? error : rowErrors[0].messages[0])
                : totalErr + ' errors found';
            var badge     = '<span class="label label-danger">' + badgeText + '</span>';
            var detailBtn = '<button class="btn btn-xs btn-danger" onclick="showSAValidationDetail(' + storeIdx + ')" title="View all errors"><i class="fa fa-search"></i> Details</button>';

            previewTableData.push({
                idx: gi+1, gid: esc(gid), dateRaw: esc(dateRaw), reason: esc(reason),
                items: grpRows.length, prodList: prodList,
                badge: badge, action: detailBtn,
                rowColor: '#fff5f5', hasError: true, _key: gid
            });
        } else {
            validatedData.push({
                _key: gid,
                payload: {
                    date:          parsedDate,
                    incident_type: incidentType,
                    stock_type:    stockType,
                    reason:        reason,
                    items_json:    JSON.stringify(items)
                }
            });
        }
    });

    buildPreviewDT(
        ['#', 'Transaction ID', 'Date', 'Reason', 'Lines', 'Products', 'Error', ''],
        ['idx', 'gid', 'dateRaw', 'reason', 'items', 'prodList', 'badge', 'action'],
        '#preview_table_stockadjustment'
    );
    finishValidation(groupOrder.length);
}

function showBulkStockAdjustmentDetails(id) {
    if ($.fn.DataTable.isDataTable('#saDetailsTable')) {
        $('#saDetailsTable').DataTable().destroy();
        $('#saDetailsTable tbody').empty();
    }
    $('#saDetailsModal').modal('show');

    $.get($('#base_url').val() + 'get_bulk_stockadjustment_details/' + id, function(raw) {
        var rows; try { rows = JSON.parse(raw); } catch(e) { rows = []; }

        /* alternating group colours per adj_id */
        var adjColours = {}, colIdx = 0, palette = ['#ffffff', '#f0f4ff'];
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
                type:         r.type,
                stocktype:    r.stocktype,
                reason:       r.reason || '—',
                product_name: r.product_name,
                store_name:   r.store_name,
                batch_name:   r.batch_name || 'Default',
                unit_name:    r.unit_name,
                qty:          parseFloat(r.qty),
                _bg:          adjColours[r.adj_id]
            };
        });

        _saDT = $('#saDetailsTable').DataTable({
            data:       data,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ordering:   false,
            autoWidth:  false,
            dom: '<"row"<"col-sm-4"l><"col-sm-4 text-center"i><"col-sm-4"f>>rt<"row"<"col-sm-12"p>>',
            columns: [
                { data: 'sl',           width: '40px',  className: 'text-center' },
                { data: 'adj_id',       width: '70px',  className: 'text-center' },
                { data: 'date',         width: '95px' },
                { data: 'type',         width: '110px' },
                { data: 'stocktype',    width: '100px' },
                { data: 'reason' },
                { data: 'product_name' },
                { data: 'store_name' },
                { data: 'batch_name' },
                {
                    data: 'qty',
                    className: 'text-right',
                    render: function(val, type, row) {
                        if (type === 'display') {
                            return '<strong>' + val.toLocaleString(undefined, {minimumFractionDigits:0, maximumFractionDigits:4})
                                 + '</strong> <span class="text-muted">' + esc(row.unit_name) + '</span>';
                        }
                        return val;
                    }
                }
            ],
            createdRow: function(tr, rowData) { $(tr).css('background-color', rowData._bg); },
            language: { emptyTable: 'No stock adjustment lines found.' }
        });
    });
}

function showSAValidationDetail(idx) {
    var entry = (window._saValidationStore || [])[idx];
    if (!entry) return;

    var titleSuffix = entry.groupError
        ? entry.groupError
        : (entry.rowErrors.length === 1 ? '1 error found' : entry.rowErrors.length + ' errors found');
    $('#saValDetailTitle').text('Error Details — Transaction: ' + entry.gid);
    $('#saValDetailError').text(titleSuffix);

    var dtRows = [];
    if (entry.groupError) {
        dtRows.push({
            rowNum: '!',
            product: '<span class="text-danger"><i class="fa fa-exclamation-triangle"></i> ' + esc(entry.groupError) + '</span>',
            store: '', batch: '', unit: '', adj: '', qty: '', status: ''
        });
    }
    (entry.rowErrors || []).forEach(function(re) {
        var r = entry.csvRows[re.rowIdx];
        if (!r) return;
        dtRows.push({
            rowNum:  '<i class="fa fa-times-circle text-danger"></i> ' + (re.rowIdx + 1),
            product: esc(r['Product']  || ''),
            store:   esc(r['Store']    || ''),
            batch:   esc(r['Batch']    || ''),
            unit:    esc(r['Unit']     || ''),
            adj:     esc(r['Adj.Type'] || ''),
            qty:     esc(r['Adj.Qty']  || ''),
            status:  re.messages.map(function(m) {
                return '<span class="label label-danger" style="display:inline-block;margin:1px 2px 1px 0;">' + esc(m) + '</span>';
            }).join(' ')
        });
    });

    if (_saValDT) { try { _saValDT.destroy(); } catch(e) {} _saValDT = null; }
    $('#saValDetailTable tbody').empty();

    _saValDT = $('#saValDetailTable').DataTable({
        data: dtRows,
        pageLength: 10,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        ordering: false,
        autoWidth: false,
        columns: [
            { data: 'rowNum',  title: '#',        width: '50px', className: 'text-center' },
            { data: 'product', title: 'Product'   },
            { data: 'store',   title: 'Store'     },
            { data: 'batch',   title: 'Batch'     },
            { data: 'unit',    title: 'Unit'      },
            { data: 'adj',     title: 'Adj.Type'  },
            { data: 'qty',     title: 'Adj.Qty',  className: 'text-right' },
            { data: 'status',  title: 'Error(s)'  }
        ],
        createdRow: function(row) { $(row).css({ background: '#fff0f0', fontWeight: '600' }); },
        language: { emptyTable: 'No errors to display.' }
    });

    $('#saValDetailModal').modal('show');
}

function deleteBulkStockAdjustment(id) {
    if (!confirm('Delete this upload record? The stock adjustment entries will be removed from the system.')) return;
    $.post($('#base_url').val() + 'delete_bulk_stockadjustment/' + id, function() {
        if (stockadjustmentDT) stockadjustmentDT.ajax.reload();
    });
}

$('#saDetailsModal').on('hidden.bs.modal', function() {
    if (_saDT) { try { _saDT.destroy(); } catch(e) {} _saDT = null; }
});
$('#saValDetailModal').on('hidden.bs.modal', function() {
    if (_saValDT) { try { _saValDT.destroy(); } catch(e) {} _saValDT = null; }
});
</script>

<!-- ── Stock Adjustment Details Modal ───────────────────────── -->
<div class="modal fade" id="saDetailsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width:92%;max-width:1100px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#f4f6f9;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-list-alt"></i> Stock Adjustment Upload Details</h4>
            </div>
            <div class="modal-body" style="padding:12px 16px;">
                <table class="table table-bordered table-condensed" id="saDetailsTable" style="width:100%;font-size:13px;">
                    <thead style="background:#e8edf3;">
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:70px;">Adj. ID</th>
                            <th style="width:95px;">Date</th>
                            <th style="width:110px;">Incident Type</th>
                            <th style="width:100px;">Stock Type</th>
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

<!-- ── Stock Adjustment Validation Error Detail Modal ────────── -->
<div class="modal fade" id="saValDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width:90%;max-width:960px;">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff0f0;border-bottom:2px solid #e74c3c;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-danger" id="saValDetailTitle">
                    <i class="fa fa-exclamation-circle"></i> Error Details
                </h4>
            </div>
            <div class="modal-body" style="padding:0;">
                <div class="alert alert-danger" style="margin:14px 16px 0;border-radius:4px;">
                    <i class="fa fa-times-circle"></i>
                    <strong>Error: </strong><span id="saValDetailError"></span>
                </div>
                <div style="padding:12px 16px;">
                    <table class="table table-bordered table-condensed" id="saValDetailTable" style="width:100%;font-size:13px;">
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
