<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <p class="text-muted">
                    One-time job (UCL-90–UCL-93): rebuilds the Unit Cost table from this
                    database's full transaction history, in date order, using the same costing
                    rules every live save already uses. Safe to run more than once — it always
                    rebuilds from scratch rather than adding to what's there.
                </p>

                <div id="uc_blocker_panel" style="display:none;">
                    <div class="alert alert-warning">
                        <strong>Opening Stock Cost Review required (UCL-91)</strong> — the lines
                        below predate this feature and have no Unit Cost recorded. Every one needs
                        a cost entered before the migration can run, since a Product+Batch's very
                        first movement can't be costed without one.
                    </div>
                    <table class="table table-bordered table-striped" id="uc_review_table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Batch</th>
                                <th>Store</th>
                                <th class="text-right">Qty</th>
                                <th style="width:180px;">Unit Cost</th>
                                <th style="width:90px;"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div id="uc_ready_panel" style="display:none;">
                    <div class="alert alert-success">
                        No uncosted Opening Stock lines found — the migration is clear to run.
                    </div>
                </div>

                <button type="button" id="uc_run_btn" class="btn btn-danger" style="display:none;" onclick="runMigration()">
                    Run Unit Cost Migration
                </button>

                <div id="uc_result_panel" style="display:none; margin-top:16px;"></div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<script>
$(function() { loadUncostedOpeningStock(); });

function loadUncostedOpeningStock() {
    $.ajax({
        url: $('#baseUrl2').val() + 'stock/stock/get_uncosted_opening_stock',
        type: 'POST',
        dataType: 'json',
        success: function(r) {
            if (!r.success) { alert('Failed to load review list.'); return; }
            renderReviewTable(r.rows);
        },
        error: function() { alert('Failed to load review list.'); }
    });
}

function renderReviewTable(rows) {
    var $tbody = $('#uc_review_table tbody').empty();

    if (!rows || rows.length === 0) {
        $('#uc_blocker_panel').hide();
        $('#uc_ready_panel').show();
        $('#uc_run_btn').show();
        return;
    }

    $('#uc_ready_panel').hide();
    $('#uc_run_btn').hide();
    $('#uc_blocker_panel').show();

    rows.forEach(function(row) {
        var $tr = $('<tr>').attr('data-id', row.stock_details_id);
        $tr.append($('<td>').text(row.date));
        $tr.append($('<td>').text(row.product_name));
        $tr.append($('<td>').text(row.batch_name || 'Default'));
        $tr.append($('<td>').text(row.store_name || ''));
        $tr.append($('<td class="text-right">').text(Number(row.qty).toLocaleString(undefined, {maximumFractionDigits: 4})));
        var $costTd = $('<td>');
        var $input = $('<input type="number" class="form-control" min="0.01" step="any" placeholder="0.00">');
        $costTd.append($input);
        $tr.append($costTd);
        var $btnTd = $('<td>');
        var $btn = $('<button type="button" class="btn btn-primary btn-sm">Save</button>');
        $btn.on('click', function() { saveOpeningStockCost(row.stock_details_id, $input, $tr); });
        $btnTd.append($btn);
        $tr.append($btnTd);
        $tbody.append($tr);
    });
}

function saveOpeningStockCost(id, $input, $tr) {
    var cost = parseFloat($input.val());
    if (!cost || cost <= 0) {
        alert('Unit Cost is required and must be greater than 0.');
        return;
    }
    $.ajax({
        url: $('#baseUrl2').val() + 'stock/stock/save_opening_stock_cost_ajax',
        type: 'POST',
        data: { stock_details_id: id, unit_cost: cost },
        dataType: 'json',
        success: function(r) {
            if (r.success) {
                $tr.fadeOut(200, function() {
                    $tr.remove();
                    if ($('#uc_review_table tbody tr').length === 0) {
                        $('#uc_blocker_panel').hide();
                        $('#uc_ready_panel').show();
                        $('#uc_run_btn').show();
                    }
                });
            } else {
                alert('Error: ' + (r.error || 'Could not save.'));
            }
        },
        error: function() { alert('Could not save. Please try again.'); }
    });
}

function runMigration() {
    if (!confirm('This rebuilds the Unit Cost table from the full transaction history. Continue?')) return;

    $('#uc_run_btn').prop('disabled', true).text('Running...');
    $.ajax({
        url: $('#baseUrl2').val() + 'stock/stock/run_unit_cost_migration_ajax',
        type: 'POST',
        dataType: 'json',
        success: function(r) {
            $('#uc_run_btn').prop('disabled', false).text('Run Unit Cost Migration');
            var $panel = $('#uc_result_panel').show().empty();

            if (!r.success) {
                if (r.blocked) {
                    $panel.append($('<div class="alert alert-warning">').text('Blocked — uncosted Opening Stock lines still exist. Reloading the review list.'));
                    renderReviewTable(r.rows);
                } else {
                    $panel.append($('<div class="alert alert-danger">').text('Migration failed: ' + (r.error || 'Unknown error')));
                }
                return;
            }

            var html = '<div class="alert alert-success">Migration complete — ' + r.replayed +
                ' movement(s) replayed' + (r.skipped > 0 ? ', ' + r.skipped + ' skipped (source line no longer exists)' : '') + '.</div>';

            if (r.reconciliation && r.reconciliation.length > 0) {
                html += '<div class="alert alert-warning"><strong>Reconciliation mismatches found (UCL-61/UCL-62) — review before trusting this result:</strong><table class="table table-condensed" style="margin-top:8px;"><thead><tr><th>Product</th><th>Batch</th><th class="text-right">Unit Cost Qty</th><th class="text-right">Ledger Qty</th></tr></thead><tbody>';
                r.reconciliation.forEach(function(row) {
                    html += '<tr><td>' + row.product + '</td><td>' + row.batch + '</td><td class="text-right">' + row.unit_cost_qty + '</td><td class="text-right">' + row.ledger_qty + '</td></tr>';
                });
                html += '</tbody></table></div>';
            } else {
                html += '<div class="alert alert-success">Reconciliation passed — Available Qty matches the Actual-stock ledger for every Product+Batch.</div>';
            }

            $panel.html(html);
        },
        error: function() {
            $('#uc_run_btn').prop('disabled', false).text('Run Unit Cost Migration');
            alert('Migration request failed. Please try again.');
        }
    });
}
</script>
