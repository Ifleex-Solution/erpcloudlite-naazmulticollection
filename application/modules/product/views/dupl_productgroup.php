<!-- ── Product Group: Upload History ───────────────────────── -->
<div class="row" id="history_productgroup" style="display:none;">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header"><span class="inv-page-title">DUPL History - Product Group</span></div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" id="bulkProductGroupTable">
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
function validateProductGroupRows(rows) {
    $('#preview_table_productgroup').show();

    // Group CSV rows by "Product Group ID" column
    var groups = {};
    var groupOrder = [];
    rows.forEach(function(r) {
        var gid = (r['Product Group ID'] || '').trim();
        if (!groups[gid]) { groups[gid] = []; groupOrder.push(gid); }
        groups[gid].push(r);
    });

    var seenCodes = new Set();

    groupOrder.forEach(function(gid, gi) {
        var grpRows  = groups[gid];
        var first    = grpRows[0];
        var groupcode = gid;
        var name      = (first['Product Group Name']            || '').trim();
        var invRaw    = (first['Invoice Grouping (Yes/No)']     || '').trim().toLowerCase();
        var stRaw     = (first['Status (Yes/No)']               || '').trim().toLowerCase();
        var error     = null;

        if (!groupcode)                                          error = 'Product Group ID is required';
        else if (!name)                                          error = 'Product Group Name is required';
        else if (seenCodes.has(groupcode.toLowerCase()))         error = 'Duplicate Product Group ID in CSV: "' + groupcode + '"';
        else if (db_productgroup_codes.has(groupcode.toLowerCase())) error = 'Product Group ID already exists: "' + groupcode + '"';
        else if (!['yes','no'].includes(invRaw))                 error = 'Invoice Grouping must be Yes or No';
        else if (!['yes','no'].includes(stRaw))                  error = 'Status must be Yes or No';

        var items = [];
        if (!error) {
            for (var j = 0; j < grpRows.length; j++) {
                var dr       = grpRows[j];
                var prodName = (dr['Product']           || '').trim();
                var unitName = (dr['Unit']              || '').trim();
                var qty      = (dr['Qty']               || '').trim();
                var parRaw   = (dr['Parent (Yes/No)']   || '').trim().toLowerCase();

                if (!prodName) continue; // skip empty detail rows

                var productEntry = csv_products.find(function(p){ return p.product_name.toLowerCase() === prodName.toLowerCase(); });
                var unitEntry    = csv_units.find(function(u){ return u.unit_name.toLowerCase() === unitName.toLowerCase(); });

                if (!productEntry) { error = 'Product not found: "' + prodName + '"'; break; }
                if (!unitEntry)    { error = 'Unit not found: "' + unitName + '"'; break; }
                if (qty === '' || isNaN(parseFloat(qty))) { error = 'Qty must be numeric for product "' + prodName + '"'; break; }
                if (!['yes','no'].includes(parRaw)) { error = 'Parent must be Yes or No for product "' + prodName + '"'; break; }

                items.push({ product_id: productEntry.id, unit_id: unitEntry.unit_id, qty: qty, parent: parRaw==='yes'?1:0 });
            }
        }

        if (!error && items.length === 0) error = 'No valid detail rows found for group';
        if (!error) seenCodes.add(groupcode.toLowerCase());

        var statusLabel = stRaw==='yes' ? '<span class="label label-success">Active</span>' : '<span class="label label-default">Inactive</span>';
        var badge = error ? '<span class="label label-danger" title="'+esc(error)+'">'+esc(error)+'</span>' : '<span class="label label-success">OK</span>';
        var itemCount = items.length > 0 ? items.length : grpRows.length;

        if (error) {
            previewTableData.push({ idx:gi+1, groupcode:esc(groupcode), name:esc(name), items:itemCount, statusLabel, badge, rowColor:'#fff5f5', hasError:true, _key:groupcode });
        } else {
            validatedData.push({ _key:groupcode, payload:{ groupcode, name, status:stRaw==='yes'?1:0, invoice_group:invRaw==='yes'?1:0, items_json:JSON.stringify(items) } });
            previewTableData.push({ idx:gi+1, groupcode:esc(groupcode), name:esc(name), items:itemCount, statusLabel, badge, rowColor:'', hasError:false, _key:groupcode });
        }
    });

    buildPreviewDT(['#','Product Group ID','Product Group Name','Items','Status','Validation'],
                   ['idx','groupcode','name','items','statusLabel','badge'], '#preview_table_productgroup');
    finishValidation(groupOrder.length);
}
function initProductGroupDT() {
    productgroupDT = $('#bulkProductGroupTable').DataTable({
        responsive:true, processing:true, serverSide:true, order:[[1,'desc']],
        dom:'lfrtip', serverMethod:'post',
        ajax:{ url:$('#base_url').val()+'checkBulkProductGroupUpload', data:{ csrf_test_name:$('#CSRF_TOKEN').val() } },
        columns:[{data:'sl'},{data:'uploaded_id'},{data:'date'},{data:'name'},{data:'button',orderable:false}]
    });
}
function showBulkProductGroupDetails(id) { showGenericBulkDetails(id,'get_bulk_productgroup_details/',['#','Product Group ID','Product Group Name'],['groupcode','name'],'Product Group Upload Details'); }
function deleteBulkProductGroup(id) {
    if (!confirm('Delete this batch and all its product groups?')) return;
    $.post($('#base_url').val()+'delete_bulk_productgroup/'+id, function(){ if(productgroupDT) productgroupDT.ajax.reload(); });
}
</script>
