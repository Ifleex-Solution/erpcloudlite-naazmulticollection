<!-- Login Alert Pop-ups — included unconditionally from layout.php (body
     root, same reason as the Change Password modal: header.php/other
     per-page views render in <header>/<div class="content">, not at the
     body root, so a modal declared there wouldn't exist in the DOM on
     every page the header icons appear on).

     Two ways these same 4 modals get opened:
       - Mandatory sequence (home.php calls startLoginAlertSequence() only
         when just_logged_in): backdrop:static, keyboard:false, no close
         (X) — Skip is the only way forward, per the spec.
       - On demand (header icon / dropdown "View X" link, from ANY page,
         any time): normal closable modal (X, Esc, click-outside all work),
         via openAlertModalOnDemand(key).
     Each DataTable is lazy-initialised the first time its modal is shown,
     by whichever path opened it first — not on page load. -->
<style>
    .login-alert-modal .modal-header { position: relative; }
    .login-alert-progress {
        position: absolute; top: 18px; right: 50px; font-size: 12px; font-weight: 600; color: #94A3B8;
        display: flex; align-items: center; gap: 8px;
    }
    .login-alert-modal.la-on-demand .login-alert-progress { display: none; }
    .login-alert-progress .dots { display: flex; gap: 4px; }
    .login-alert-progress .dots span { width: 18px; height: 4px; border-radius: 2px; background: #E2E8F0; }
    .login-alert-progress .dots span.done { background: #8b5cf6; }
    .login-alert-modal .modal-footer { display: flex; align-items: center; justify-content: space-between; }
    .login-alert-locked-note { font-size: 12px; color: #94A3B8; }
    .login-alert-locked-note i { margin-right: 4px; }
    .login-alert-modal .close { position: relative; z-index: 2; }
    .login-alert-skip-btn { display: none; }
    .login-alert-modal.la-mandatory .login-alert-skip-btn { display: inline-block; }
    .login-alert-modal.la-mandatory .close { display: none; }

    /* 4 summary cards above each pop-up's table (spec: "four summary cards
       above the table", one per alert type's figure). Plain colored-left-
       border cards, not the dashboard's heavier .db-card component — this
       is a small strip inside a popup, not a dashboard widget. */
    .login-alert-cards {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;
        margin: 16px 0 14px;
    }
    .login-alert-card {
        background: #F8FAFC; border-radius: 10px; padding: 10px 12px;
        border-left: 3px solid #CBD5E1;
    }
    .login-alert-card .lac-value { font-size: 17px; font-weight: 700; color: #1E293B; line-height: 1.2; }
    .login-alert-card .lac-label { font-size: 11px; color: #64748B; margin-top: 2px; }
    .login-alert-card.lac-red    { border-left-color: #DC2626; }
    .login-alert-card.lac-red    .lac-value { color: #DC2626; }
    .login-alert-card.lac-orange { border-left-color: #F59E0B; }
    .login-alert-card.lac-orange .lac-value { color: #F59E0B; }
    .login-alert-card.lac-yellow { border-left-color: #EAB308; }
    .login-alert-card.lac-yellow .lac-value { color: #CA8A04; }
    .login-alert-card.lac-blue   { border-left-color: #2563EB; }
    .login-alert-card.lac-blue   .lac-value { color: #2563EB; }
    @media (max-width: 767px) {
        .login-alert-cards { grid-template-columns: repeat(2, 1fr); }
    }

    /* Plain table inside the popup body — not the full .panel-bd "screen"
       card (that nested a whole page component inside the modal, making a
       small popup look like a page within a page). Just themed borders/
       header to match the rest of the app, kept light. */
    .login-alert-modal .modal-body { padding: 0 20px 18px; }
    .login-alert-modal table.table { margin-bottom: 0 !important; }
    /* Exact same colors as the branch-screen (and every other list screen's)
       table theming — .panel.panel-bd.lobidrag .panel-body table.table in
       style.css — just re-scoped to .login-alert-modal since these tables
       sit inside a popup, not that panel wrapper (keeping the look
       identical without pulling in the wrapper's own card/shadow chrome). */
    .login-alert-modal table.table thead th {
        background: #F1F5F9 !important; color: #475569 !important; font-size: 11px !important;
        font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.7px !important;
        border-bottom: 2px solid #DDD6FE !important; border-top: none !important;
        padding: 11px 14px !important; white-space: nowrap !important;
    }
    .login-alert-modal table.table tbody td {
        padding: 10px 14px !important; font-size: 13px !important; color: #374151 !important;
        border-color: #F3F0FE !important; vertical-align: middle !important;
    }
    .login-alert-modal table.table tbody tr.odd td,
    .login-alert-modal table.table tbody tr:nth-child(odd) td { background: #ffffff !important; }
    .login-alert-modal table.table tbody tr.even td,
    .login-alert-modal table.table tbody tr:nth-child(even) td { background: #F8FAFC !important; }
    .login-alert-modal table.table tbody tr:hover td { background: #F3F0FE !important; }

    /* Final "all alerts reviewed" toast — toastr's own .toast-success is
       otherwise unused anywhere in this app, so this is safe to restyle
       globally: a clean white card with a small green check badge, not
       toastr's default solid-color block. */
    #toast-container > .toast-success {
        background-image: none !important;
        background-color: #ffffff !important;
        color: #1E293B !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 28px rgba(0,0,0,0.14) !important;
        padding: 14px 16px 14px 54px !important;
        opacity: 1 !important;
        width: 340px !important;
    }
    #toast-container > .toast-success::before {
        content: '\f00c';
        font-family: FontAwesome;
        position: absolute; left: 14px; top: 14px;
        width: 26px; height: 26px; line-height: 26px; text-align: center;
        background: #DCFCE7; color: #16A34A; border-radius: 8px; font-size: 12px;
    }
    #toast-container > .toast-success .toast-message { font-size: 13px !important; line-height: 1.5 !important; }
    #toast-container > .toast-success .toast-message strong { color: #1E293B; font-size: 13px; }
    #toast-container > .toast-success .la-toast-detail { color: #64748B; font-size: 12px; margin-top: 3px; }
    #toast-container > .toast-success .toast-close-button { color: #94A3B8 !important; text-shadow: none !important; opacity: 1 !important; top: -6px !important; right: -2px !important; }
    @media (max-width: 767px) {
        .login-alert-modal .modal-dialog { width: 100% !important; height: 100% !important; margin: 0 !important; }
        .login-alert-modal .modal-content { height: 100%; border-radius: 0 !important; }
        .login-alert-modal .modal-footer { flex-direction: column; align-items: stretch; gap: 8px; }
        .login-alert-modal .modal-footer .login-alert-locked-note { text-align: center; }
    }
</style>

<!-- Customer Credit -->
<div class="modal fade login-alert-modal" id="creditCustomerAlertModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="dd-icon dd-icon-profile" style="display:inline-flex;width:28px;height:28px;border-radius:8px;background:#F3F0FE;vertical-align:middle;margin-right:8px;"><i class="fa fa-users" style="margin:auto;color:#8b5cf6;"></i></span>Credit Alert Notification <span class="label" style="background:#8b5cf6;">Customer</span></h4>
                <div class="login-alert-progress"><span class="la-progress-text">Alert 1 of 4</span><div class="dots"></div></div>
            </div>
            <div class="modal-body">
                <div class="login-alert-cards" id="creditCustomerCards">
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="count">0</div><div class="lac-label">Not Paid Invoices</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="parties">0</div><div class="lac-label">Customers</div></div>
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="total">LKR 0.00</div><div class="lac-label">Total Due Amount</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="oldest_date">—</div><div class="lac-label">Oldest Due Date</div></div>
                </div>
                <div class="table-responsive">
                    <table id="creditCustomerAlertTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Customer</th>
                                <th>Invoice ID</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Due Amount (LKR)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="login-alert-locked-note"><i class="fa fa-lock"></i>Day-to-day work unlocks after reviewing all alerts</span>
                <span>
                    <button type="button" class="btn btn-primary login-alert-skip-btn" onclick="loginAlertSkip('customer_credit')">Skip &rarr; Supplier Credit</button>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Credit -->
<div class="modal fade login-alert-modal" id="creditSupplierAlertModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="dd-icon dd-icon-profile" style="display:inline-flex;width:28px;height:28px;border-radius:8px;background:#F3F0FE;vertical-align:middle;margin-right:8px;"><i class="fa fa-truck" style="margin:auto;color:#8b5cf6;"></i></span>Credit Alert Notification <span class="label" style="background:#8b5cf6;">Supplier</span></h4>
                <div class="login-alert-progress"><span class="la-progress-text">Alert 1 of 4</span><div class="dots"></div></div>
            </div>
            <div class="modal-body">
                <div class="login-alert-cards" id="creditSupplierCards">
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="count">0</div><div class="lac-label">Not Paid Invoices</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="parties">0</div><div class="lac-label">Suppliers</div></div>
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="total">LKR 0.00</div><div class="lac-label">Total Due Amount</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="oldest_date">—</div><div class="lac-label">Oldest Due Date</div></div>
                </div>
                <div class="table-responsive">
                    <table id="creditSupplierAlertTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Supplier</th>
                                <th>Invoice ID</th>
                                <th>Invoice Date</th>
                                <th>Due Date</th>
                                <th>Due Amount (LKR)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="login-alert-locked-note"><i class="fa fa-lock"></i>Day-to-day work unlocks after reviewing all alerts</span>
                <span>
                    <button type="button" class="btn btn-primary login-alert-skip-btn" onclick="loginAlertSkip('supplier_credit')">Skip &rarr; Expiration Alert</button>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Expiration Alert -->
<div class="modal fade login-alert-modal" id="expiryAlertPopupModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="dd-icon dd-icon-profile" style="display:inline-flex;width:28px;height:28px;border-radius:8px;background:#F3F0FE;vertical-align:middle;margin-right:8px;"><i class="fa fa-clock-o" style="margin:auto;color:#8b5cf6;"></i></span>Expiration Alert Notification</h4>
                <div class="login-alert-progress"><span class="la-progress-text">Alert 1 of 4</span><div class="dots"></div></div>
            </div>
            <div class="modal-body">
                <div class="login-alert-cards" id="expiryCards">
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="Expired">0</div><div class="lac-label">Expired</div></div>
                    <div class="login-alert-card lac-orange"><div class="lac-value" data-f="To be Expired">0</div><div class="lac-label">To be Expired</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="total_batches">0</div><div class="lac-label">Total Batches</div></div>
                    <div class="login-alert-card"><div class="lac-value" data-f="next_expiry_date">—</div><div class="lac-label">Next Expiry Date</div></div>
                </div>
                <div class="table-responsive">
                    <table id="expiryAlertPopupTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Batch ID</th>
                                <th>Expiry Date</th>
                                <th>Master Stock Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="login-alert-locked-note"><i class="fa fa-lock"></i>Day-to-day work unlocks after reviewing all alerts</span>
                <span>
                    <button type="button" class="btn btn-primary login-alert-skip-btn" onclick="loginAlertSkip('expiration')">Skip &rarr; Stock Alert</button>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Stock Alert -->
<div class="modal fade login-alert-modal" id="stockAlertPopupModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><span class="dd-icon dd-icon-profile" style="display:inline-flex;width:28px;height:28px;border-radius:8px;background:#F3F0FE;vertical-align:middle;margin-right:8px;"><i class="fa fa-cube" style="margin:auto;color:#8b5cf6;"></i></span>Stock Alert Notification</h4>
                <div class="login-alert-progress"><span class="la-progress-text">Alert 1 of 4</span><div class="dots"></div></div>
            </div>
            <div class="modal-body">
                <div class="login-alert-cards" id="stockCards">
                    <div class="login-alert-card lac-red"><div class="lac-value" data-f="Out of Stock">0</div><div class="lac-label">Out of Stock</div></div>
                    <div class="login-alert-card lac-orange"><div class="lac-value" data-f="Reorder">0</div><div class="lac-label">Reorder</div></div>
                    <div class="login-alert-card lac-yellow"><div class="lac-value" data-f="Near Order">0</div><div class="lac-label">Near Order</div></div>
                    <div class="login-alert-card lac-blue"><div class="lac-value" data-f="Overstock">0</div><div class="lac-label">Overstock</div></div>
                </div>
                <div class="table-responsive">
                    <table id="stockAlertPopupTable" class="table table-bordered table-striped table-hover" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th><?php echo display('sl') ?></th>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Master Stock Qty</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <span class="login-alert-locked-note"><i class="fa fa-lock"></i>Day-to-day work unlocks after reviewing all alerts</span>
                <span>
                    <button type="button" class="btn btn-primary login-alert-skip-btn" onclick="loginAlertSkip('stock')">Skip &amp; Start My Day &check;</button>
                </span>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var MODALS = {
        customer_credit: '#creditCustomerAlertModal',
        supplier_credit: '#creditSupplierAlertModal',
        expiration:       '#expiryAlertPopupModal',
        stock:            '#stockAlertPopupModal'
    };
    var TABLE_INIT = {
        customer_credit: initCreditCustomerTable,
        supplier_credit: initCreditSupplierTable,
        expiration:       initExpiryPopupTable,
        stock:            initStockPopupTable
    };
    var initialised = {};
    var queue = [];
    var qIndex = 0;
    // Read lazily, not at top-level — this script can sit above #base_url
    // in the DOM (layout.php's footer) depending on where it's included,
    // and reading it synchronously at parse time would get an empty value.
    var baseUrl = '';
    var counts = {};

    function ensureBaseUrl() {
        if (!baseUrl) { baseUrl = $('#base_url').val(); }
    }

    // Called by home.php only when just_logged_in — the forced, ordered,
    // un-skippable-except-via-Skip walkthrough.
    window.startLoginAlertSequence = function () {
        ensureBaseUrl();
        $.ajax({
            type: 'GET',
            url: baseUrl + 'login_alert_counts',
            dataType: 'json',
            success: function (data) {
                counts = data || {};
                ['customer_credit', 'supplier_credit', 'expiration', 'stock'].forEach(function (k) {
                    if ((counts[k] || 0) > 0) { queue.push(k); }
                });
                if (queue.length === 0) { return; } // nothing to show, straight to dashboard
                qIndex = 0;
                showCurrent();
            }
        });
    };

    function showCurrent() {
        var key = queue[qIndex];
        var sel = MODALS[key];
        updateProgress(sel, qIndex + 1, queue.length);
        currentFilter[key] = '';
        applyCardVisibility(key, '');
        $(sel).removeClass('la-on-demand').addClass('la-mandatory');
        $(sel).modal({ backdrop: 'static', keyboard: false });
        ensureTable(key);
        loadSummaryCards(key);
    }

    // Reopenable any time from the header icon / dropdown "View X" link —
    // normal closable modal (X, Esc, click-outside), no forced order, no
    // Skip button, can be opened for just one alert type on its own.
    // statusFilter (e.g. "Reorder", "Expired") pre-filters the table to
    // just that status, matching whichever dropdown row was clicked —
    // omit it (or pass '') to show everything, as the "View X Notification"
    // link and the mandatory sequence both do.
    var currentFilter = {};
    var CARDS = { expiration: '#expiryCards', stock: '#stockCards' };

    // When the popup is opened already filtered to one status (a header
    // dropdown row), the cards show just that one matching card instead of
    // all 4 — no filter (opened via the icon itself / "View X Notification")
    // still shows the full set.
    function applyCardVisibility(key, statusFilter) {
        var $cards = $(CARDS[key]);
        if (!$cards.length) { return; }
        if (!statusFilter) {
            $cards.find('.login-alert-card').show();
            return;
        }
        $cards.find('.login-alert-card').each(function () {
            var isMatch = $(this).find('[data-f="' + statusFilter + '"]').length > 0;
            $(this).toggle(isMatch);
        });
    }

    window.openAlertModalOnDemand = function (key, statusFilter) {
        ensureBaseUrl();
        var sel = MODALS[key];
        var filterChanged = currentFilter[key] !== (statusFilter || '');
        currentFilter[key] = statusFilter || '';
        applyCardVisibility(key, currentFilter[key]);
        $(sel).removeClass('la-mandatory').addClass('la-on-demand');
        $(sel).modal({ backdrop: true, keyboard: true });
        if (initialised[key] && filterChanged) {
            $(MODALS[key]).find('table').DataTable().ajax.reload();
        } else {
            ensureTable(key);
        }
        loadSummaryCards(key);
    };

    function ensureTable(key) {
        if (!initialised[key]) {
            initialised[key] = true;
            TABLE_INIT[key]();
        }
    }

    // The 4 summary cards above each table — same breakdown/summary data
    // the header dropdown uses (header_dropdown_data), fetched once per
    // page view and cached, so reopening a popup (or opening a second one)
    // doesn't re-hit the server.
    var summaryData = null;
    var summaryLoading = false;
    var pendingCardTargets = [];

    function loadSummaryCards(key) {
        pendingCardTargets.push(key);
        if (summaryData) {
            renderSummaryCards();
            return;
        }
        if (summaryLoading) { return; }
        summaryLoading = true;
        $.ajax({
            type: 'GET',
            url: baseUrl + 'header_dropdown_data',
            dataType: 'json',
            success: function (data) {
                summaryData = data || {};
                renderSummaryCards();
            },
            complete: function () { summaryLoading = false; }
        });
    }

    function moneyFmtLA(n) {
        return 'LKR ' + parseFloat(n || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function dateFmtLA(d) {
        if (!d) { return '—'; }
        var dt = new Date(d + 'T00:00:00');
        if (isNaN(dt.getTime())) { return d; }
        return dt.toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function fillCards(containerId, values) {
        var $c = $('#' + containerId);
        $c.find('[data-f]').each(function () {
            var field = $(this).data('f');
            var v = values ? values[field] : undefined;
            if (field === 'total') {
                $(this).text(moneyFmtLA(v));
            } else if (field === 'oldest_date' || field === 'next_expiry_date') {
                $(this).text(dateFmtLA(v));
            } else {
                $(this).text(v || 0);
            }
        });
    }

    function renderSummaryCards() {
        if (!summaryData) { return; }
        pendingCardTargets.forEach(function (key) {
            if (key === 'customer_credit' && summaryData.credit_alert) {
                fillCards('creditCustomerCards', summaryData.credit_alert.customer);
            }
            if (key === 'supplier_credit' && summaryData.credit_alert) {
                fillCards('creditSupplierCards', summaryData.credit_alert.supplier);
            }
            if (key === 'expiration' && summaryData.expiry_alert) {
                fillCards('expiryCards', summaryData.expiry_alert);
            }
            if (key === 'stock' && summaryData.stock_alert) {
                fillCards('stockCards', summaryData.stock_alert);
            }
        });
        pendingCardTargets = [];
    }

    // "Alert X of N" — N is however many pop-ups actually have data (a
    // disabled or empty alert never enters the queue), not a hardcoded 4,
    // per the spec's "the counter adjusts to the number of enabled pop-ups".
    function updateProgress(sel, step, total) {
        var $p = $(sel).find('.login-alert-progress');
        $p.find('.la-progress-text').text('Alert ' + step + ' of ' + total);
        var $dots = $p.find('.dots').empty();
        for (var i = 1; i <= total; i++) {
            $dots.append('<span' + (i <= step ? ' class="done"' : '') + '></span>');
        }
    }

    window.loginAlertSkip = function (key) {
        var sel = MODALS[key];
        $(sel).modal('hide');
        qIndex++;
        if (qIndex < queue.length) {
            showCurrent();
        } else {
            finishSequence();
        }
    };

    function finishSequence() {
        var parts = [];
        var creditTotal = (counts.customer_credit || 0) + (counts.supplier_credit || 0);
        if (creditTotal > 0) { parts.push(creditTotal + ' unpaid invoices'); }
        if ((counts.expiration || 0) > 0) { parts.push(counts.expiration + ' expiry alerts'); }
        if ((counts.stock || 0) > 0) { parts.push(counts.stock + ' stock alerts'); }
        if (typeof toastr !== 'undefined') {
            var detail = parts.length ? (parts.join(' &middot; ') + '.') : '';
            var html = '<strong>All alerts reviewed — you\'re good to go!</strong>'
                + '<div class="la-toast-detail">' + detail + ' Reopen them anytime from the top bar icons.</div>';
            toastr.success(html, '', { closeButton: true, timeOut: 6000, positionClass: 'toast-top-right', escapeHtml: false });
        }
    }

    function dtButtons(title, cols) {
        return [
            { extend: 'copy',  className: 'btn-sm prints', exportOptions: { columns: cols } },
            { extend: 'csv',   className: 'btn-sm prints', title: title, exportOptions: { columns: cols } },
            { extend: 'excel', className: 'btn-sm prints', title: title, exportOptions: { columns: cols } },
            { extend: 'pdf',   className: 'btn-sm prints', title: title, exportOptions: { columns: cols } },
            { extend: 'print', className: 'btn-sm prints', title: '<center>' + title + '</center>', exportOptions: { columns: cols } }
        ];
    }

    function moneyRender(data) {
        return parseFloat(data || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function statusBadge(data) {
        return '<span class="btn btn-xs btn-danger" style="cursor:default;min-width:90px;">' + data + '</span>';
    }

    function initCreditCustomerTable() {
        $('#creditCustomerAlertTable').DataTable({
            responsive: true, processing: true, serverSide: true,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: dtButtons('Credit Alert Notification - Customer', [0,1,2,3,4,5,6]),
            serverMethod: 'post',
            ajax: { url: baseUrl + 'customer_credit_data', type: 'POST' },
            columnDefs: [{ targets: [0], orderable: false }],
            columns: [
                { data: 'sl' }, { data: 'customer_name' }, { data: 'invoice_no' },
                { data: 'invoice_date' }, { data: 'due_date' },
                { data: 'due_amount', render: moneyRender },
                { data: 'status', render: statusBadge }
            ]
        });
    }

    function initCreditSupplierTable() {
        $('#creditSupplierAlertTable').DataTable({
            responsive: true, processing: true, serverSide: true,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: dtButtons('Credit Alert Notification - Supplier', [0,1,2,3,4,5,6]),
            serverMethod: 'post',
            ajax: { url: baseUrl + 'supplier_credit_data', type: 'POST' },
            columnDefs: [{ targets: [0], orderable: false }],
            columns: [
                { data: 'sl' }, { data: 'supplier_name' }, { data: 'invoice_no' },
                { data: 'invoice_date' }, { data: 'due_date' },
                { data: 'due_amount', render: moneyRender },
                { data: 'status', render: statusBadge }
            ]
        });
    }

    function initExpiryPopupTable() {
        $('#expiryAlertPopupTable').DataTable({
            responsive: true, processing: true, serverSide: true,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: dtButtons('Expiration Alert Notification', [0,1,2,3,4,5,6]),
            serverMethod: 'post',
            ajax: {
                url: baseUrl + 'expiry_alert_data', type: 'POST',
                data: function (d) { d.status_filter = currentFilter.expiration || ''; return d; }
            },
            columnDefs: [{ targets: [0], orderable: false }],
            columns: [
                { data: 'sl' }, { data: 'product_id' }, { data: 'product_name' }, { data: 'batch_id' },
                { data: 'expiry_date' },
                {
                    data: 'master_stock_qty',
                    render: function (data, type, row) { return convertmasterstockLA(data, row.conversion_ratio, row.master, row.sub); }
                },
                {
                    data: 'status_text',
                    render: function (data, type, row) {
                        return '<span class="btn btn-xs ' + row.status_class + '" style="cursor:default;min-width:115px;">' + data + '</span>';
                    }
                }
            ]
        });
    }

    function initStockPopupTable() {
        $('#stockAlertPopupTable').DataTable({
            responsive: true, processing: true, serverSide: true,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            dom: "<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: dtButtons('Stock Alert Notification', [0,1,2,3,4,5]),
            serverMethod: 'post',
            ajax: {
                url: baseUrl + 'out_of_stock_data', type: 'POST',
                data: function (d) { d.status_filter = currentFilter.stock || ''; return d; }
            },
            columnDefs: [{ targets: [0], orderable: false }],
            columns: [
                { data: 'sl' }, { data: 'product_id' }, { data: 'product_name' }, { data: 'category_name' },
                {
                    data: 'master_stock_qty',
                    render: function (data, type, row) { return convertmasterstockLA(data, row.conversion_ratio, row.master, row.sub); }
                },
                {
                    data: 'status',
                    render: function (data, type, row) {
                        return '<span class="btn btn-xs stock-status-btn ' + row.status_class + '" style="cursor:default;min-width:115px;">' + data + '</span>';
                    }
                }
            ]
        });
    }

    window.convertmasterstockLA = function (avstock, conversion_ratio, mastername, subname) {
        if (!subname || !conversion_ratio) { return ((avstock ? avstock : 0) + (mastername || '')); }
        var mas = conversion_ratio * avstock / conversion_ratio;
        var sub = conversion_ratio * avstock % conversion_ratio;
        var mas2 = Math.floor((mas).toLocaleString());
        var totalcount = isNaN(mas2) ? Math.floor(Number(mas).toFixed(6)) : mas2;
        var sub2 = Math.floor((Number(sub).toFixed(6)).toLocaleString());
        var subcount = isNaN(sub2) ? Math.floor(Number(sub).toFixed(6)) : sub2;
        if (isNaN(totalcount)) totalcount = 0;
        if (isNaN(subcount)) subcount = 0;
        return totalcount + mastername + ' ' + subcount + subname;
    };
})();
</script>
