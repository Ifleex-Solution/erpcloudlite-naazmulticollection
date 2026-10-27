<input type="hidden" id="baseUrl" value="<?php echo base_url(); ?>">
<input type="hidden" id="siteCurrency" value="<?php echo isset($currency) ? $currency : ''; ?>">
<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Barcode Print</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
  --bg:         #EEF1F6;
  --surface:    #FFFFFF;
  --surface-2:  #F5F7FB;
  --border:     #D6DAE6;
  --text:       #0E1421;
  --text-2:     #556078;
  --text-3:     #8A95AA;
  --accent:     #8b5cf6;
  --accent-h:   #7c4fe0;
  --accent-bg:  #F3F0FE;
  --accent-txt: #8b5cf6;
  --success:    #16A34A;
  --success-bg: #DCFCE7;
  --danger:     #DC2626;
  --danger-bg:  #FEE2E2;
  --radius:     6px;
  --shadow:     0 1px 3px rgba(0,0,0,.08),0 1px 2px rgba(0,0,0,.04);
}

.bp-wrap {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 16px;
  padding: 16px;
  align-items: start;
}

/* ── Panels ── */
.bp-panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  overflow: hidden;
}
.bp-panel-head {
  padding: 11px 16px;
  border-bottom: 1px solid var(--border);
  background: var(--surface-2);
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.bp-panel-title {
  font-size: 11.5px; font-weight: 600;
  text-transform: uppercase; letter-spacing: .06em;
  color: var(--text-2); display: flex; align-items: center; gap: 6px;
}
.bp-panel-body { padding: 14px 16px; }

/* ── Search ── */
.bp-search-row { display: flex; gap: 10px; align-items: center; }
.bp-search-wrap { position: relative; flex: 1; }
.bp-search-wrap svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-3); pointer-events: none; }
.bp-search-wrap input {
  width: 100%; padding: 8px 10px 8px 34px;
  border: 1px solid var(--border); border-radius: var(--radius);
  background: var(--surface); color: var(--text); font-size: 13.5px; outline: none;
  transition: border-color .15s;
}
.bp-search-wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }
.bp-cat-select {
  padding: 8px 10px; border: 1px solid var(--border); border-radius: var(--radius);
  background: var(--surface); color: var(--text); font-size: 13px; outline: none;
  cursor: pointer; min-width: 150px;
}
.bp-cat-select:focus { border-color: var(--accent); }

/* ── Scrollable panels ── */
.bp-left {
  max-height: calc(100vh - 160px);
  display: flex; flex-direction: column; overflow: hidden;
}
.bp-left .bp-panel-head { flex-shrink: 0; }
.bp-left .bp-panel-body {
  flex: 1; overflow: hidden;
  display: flex; flex-direction: column;
}
.bp-right {
  max-height: calc(100vh - 160px);
  display: flex; flex-direction: column; overflow: hidden;
}
.bp-right .bp-panel-head { flex-shrink: 0; }
.bp-footer { flex-shrink: 0; }

/* ── Product table ── */
.bp-results-wrap { margin-top: 12px; overflow-x: auto; flex: 1; overflow-y: auto; }
.bp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.bp-table th {
  text-align: left; padding: 8px 12px;
  font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
  color: var(--text-3); background: var(--surface-2); border-bottom: 1px solid var(--border); white-space: nowrap;
}
.bp-table th.r { text-align: right; }
.bp-table td { padding: 9px 12px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
.bp-table tbody tr:last-child td { border-bottom: none; }
.bp-table tbody tr:hover td { background: var(--surface-2); cursor: pointer; }
.bp-code { font-family: "SF Mono", Consolas, monospace; font-size: 11.5px; color: var(--text-2); }
.bp-pname { font-weight: 500; }
.bp-cat-tag { display: inline-block; font-size: 11px; padding: 2px 7px; border-radius: 3px; background: var(--accent-bg); color: var(--accent-txt); font-weight: 500; }
.bp-price { text-align: right; font-variant-numeric: tabular-nums; font-weight: 500; }
.bp-td-action { text-align: right; white-space: nowrap; }

/* ── Spinner / empty ── */
.bp-spinner { display: none; padding: 24px; text-align: center; color: var(--text-3); font-size: 13px; }
.bp-spinner.show { display: block; }
.bp-no-results { display: none; padding: 24px; text-align: center; color: var(--text-3); font-size: 13px; }
.bp-no-results.show { display: block; }
.bp-result-count { font-size: 12px; color: var(--text-3); }

/* ── Buttons ── */
.bp-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 13px; border-radius: var(--radius); border: none; font-size: 12.5px; font-weight: 500; cursor: pointer; transition: background .12s; white-space: nowrap; }
.bp-btn-sm { padding: 4px 10px; font-size: 12px; }
.bp-btn-select { background: var(--accent-bg); color: var(--accent-txt); border: 1px solid transparent; }
.bp-btn-select:hover { background: var(--accent); color: #fff; }
.bp-btn-remove { background: transparent; color: var(--text-3); border: 1px solid var(--border); padding: 3px 7px; }
.bp-btn-remove:hover { background: var(--danger-bg); color: var(--danger); border-color: var(--danger); }
.bp-btn-clear { background: transparent; color: var(--text-3); border: 1px solid var(--border); font-size: 12px; padding: 4px 10px; }
.bp-btn-clear:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-bg); }
.bp-btn-print { background: var(--accent); color: #fff; width: 100%; justify-content: center; padding: 10px; font-size: 13.5px; font-weight: 600; border-radius: var(--radius); border: none; cursor: pointer; display: flex; align-items: center; gap: 7px; }
.bp-btn-print:hover { background: var(--accent-h); }
.bp-btn-print:disabled { opacity: .4; cursor: not-allowed; }

/* ── Right panel ── */
.bp-right { position: sticky; top: 16px; }
.bp-sel-list { list-style: none; flex: 1; overflow-y: auto; }
.bp-sel-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-bottom: 1px solid var(--border); }
.bp-sel-item:last-child { border-bottom: none; }
.bp-sel-item:hover { background: var(--surface-2); }
.bp-item-info { flex: 1; min-width: 0; }
.bp-item-name { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.bp-item-batch { display: inline-block; font-size: 11px; padding: 1px 6px; border-radius: 3px; background: #FEF3C7; color: #92400E; font-weight: 500; margin-top: 3px; }
.bp-item-meta { font-size: 11.5px; color: var(--text-2); margin-top: 2px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.bp-item-code { font-family: "SF Mono", Consolas, monospace; }

/* ── Qty control ── */
.bp-qty { display: flex; align-items: center; border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; background: var(--surface); }
.bp-qty-btn { width: 26px; height: 28px; border: none; background: var(--surface-2); color: var(--text-2); cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; line-height: 1; }
.bp-qty-btn:hover { background: var(--accent-bg); color: var(--accent); }
.bp-qty-input { width: 38px; height: 28px; border: none; border-left: 1px solid var(--border); border-right: 1px solid var(--border); text-align: center; font-size: 13px; font-weight: 600; font-variant-numeric: tabular-nums; background: var(--surface); color: var(--text); outline: none; }
.bp-qty-input:focus { background: var(--accent-bg); }

/* ── Empty state ── */
.bp-empty { padding: 40px 16px; text-align: center; color: var(--text-3); }
.bp-empty svg { margin-bottom: 10px; opacity: .35; }
.bp-empty p { font-size: 13px; }

/* ── Footer ── */
.bp-footer { padding: 14px 16px; border-top: 1px solid var(--border); background: var(--surface-2); display: flex; flex-direction: column; gap: 12px; }
.bp-summary { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: var(--text-2); }
.bp-total { font-size: 13px; font-weight: 600; color: var(--text); font-variant-numeric: tabular-nums; }
.bp-badge { display: inline-flex; align-items: center; justify-content: center; background: var(--accent); color: #fff; border-radius: 10px; font-size: 11px; font-weight: 700; min-width: 18px; height: 18px; padding: 0 5px; }
.bp-badge:empty { display: none; }

/* ── Batch Modal ── */
.bm-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,.45); z-index: 9999;
  align-items: center; justify-content: center;
}
.bm-overlay.open { display: flex; }
.bm-box {
  background: var(--surface); border-radius: 8px;
  width: 620px; max-width: 96vw; max-height: 90vh;
  display: flex; flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.25); overflow: hidden;
}
.bm-head {
  padding: 14px 20px; border-bottom: 1px solid var(--border);
  display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
  background: var(--surface-2);
}
.bm-product-name { font-size: 14px; font-weight: 600; color: var(--text); }
.bm-product-meta { font-size: 12px; color: var(--text-2); margin-top: 3px; display: flex; gap: 12px; }
.bm-close { background: none; border: none; cursor: pointer; color: var(--text-3); padding: 4px; border-radius: 4px; display: flex; align-items: center; }
.bm-close:hover { color: var(--text); background: var(--border); }

/* Modal body split */
.bm-body { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
.bm-section { padding: 12px 20px; }
.bm-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
.bm-section-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--text-2); }
.bm-count-tag { font-size: 11px; background: var(--accent); color: #fff; border-radius: 10px; padding: 1px 7px; font-weight: 600; }
.bm-divider { height: 1px; background: var(--border); margin: 0 20px; }

/* Batch search input */
.bm-search-wrap { position: relative; }
.bm-search-wrap svg { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); color: var(--text-3); pointer-events: none; }
.bm-search-input {
  width: 100%; padding: 7px 10px 7px 32px;
  border: 1px solid var(--border); border-radius: var(--radius);
  background: var(--surface); color: var(--text); font-size: 13px; outline: none;
}
.bm-search-input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-bg); }

/* Batch tables */
.bm-tbl-wrap { margin-top: 8px; overflow-x: auto; max-height: 180px; overflow-y: auto; border: 1px solid var(--border); border-radius: var(--radius); }
.bm-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
.bm-tbl th {
  text-align: left; padding: 7px 10px;
  font-size: 10.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
  color: var(--text-3); background: var(--surface-2);
  border-bottom: 1px solid var(--border); white-space: nowrap;
  position: sticky; top: 0; z-index: 1;
}
.bm-tbl td { padding: 8px 10px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
.bm-tbl tbody tr:last-child td { border-bottom: none; }
.bm-tbl tbody tr:hover td { background: var(--surface-2); }
.bm-tbl tbody tr.bm-row-added td { opacity: .5; }
.bm-type-tag { display: inline-block; font-size: 10px; padding: 1px 6px; border-radius: 3px; font-weight: 600; }
.bm-type-single   { background: var(--accent-bg); color: var(--accent-txt); }
.bm-type-multiple { background: #F0FDF4; color: #166534; }
.bm-av-add { padding: 4px 12px; border-radius: var(--radius); background: var(--accent); color: #fff; border: none; font-size: 12px; font-weight: 500; cursor: pointer; white-space: nowrap; }
.bm-av-add:hover { background: var(--accent-h); }
.bm-av-add.added { background: #9CA3AF; cursor: default; }
.bm-rm-btn { padding: 3px 8px; border-radius: var(--radius); background: transparent; color: var(--danger); border: 1px solid var(--danger); font-size: 12px; cursor: pointer; }
.bm-rm-btn:hover { background: var(--danger-bg); }
.bm-msg { padding: 16px; text-align: center; color: var(--text-3); font-size: 12.5px; }

/* Footer */
.bm-footer {
  padding: 12px 20px; border-top: 1px solid var(--border);
  background: var(--surface-2); display: flex; align-items: center; justify-content: flex-end; gap: 10px;
}
.bm-cancel { padding: 7px 18px; border-radius: var(--radius); background: var(--surface); border: 1px solid var(--border); color: var(--text-2); font-size: 13px; cursor: pointer; }
.bm-cancel:hover { background: var(--surface-2); }
.bm-done { padding: 7px 22px; border-radius: var(--radius); background: var(--accent); color: #fff; border: none; font-size: 13px; font-weight: 600; cursor: pointer; }
.bm-done:hover { background: var(--accent-h); }
</style>

<!-- ── Two-panel layout ── -->
<div class="bp-wrap">

  <!-- LEFT: Product Search -->
  <div class="bp-panel bp-left">
    <div class="bp-panel-head">
      <span class="bp-panel-title">Products</span>
      <span class="bp-result-count" id="bp-result-count"></span>
    </div>
    <div class="bp-panel-body">
      <div class="bp-search-row">
        <div class="bp-search-wrap">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input type="text" id="bp-search" placeholder="Search by product name or code…" autocomplete="off">
        </div>
        <select class="bp-cat-select" id="bp-category">
          <option value="">All Categories</option>
          <?php if (!empty($category_list)): foreach ($category_list as $cat): ?>
          <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
          <?php endforeach; endif; ?>
        </select>
      </div>

      <div class="bp-results-wrap">
        <table class="bp-table">
          <thead>
            <tr>
              <th>Code</th>
              <th>Product Name</th>
              <th>Category</th>
              <th class="r">Price</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="bp-tbody"></tbody>
        </table>
        <div class="bp-spinner" id="bp-spinner">Loading…</div>
        <div class="bp-no-results" id="bp-no-results">No products match your search.</div>
      </div>
    </div>
  </div>

  <!-- RIGHT: Selected Products -->
  <div class="bp-panel bp-right">
    <div class="bp-panel-head">
      <span class="bp-panel-title">
        Print List
        <span class="bp-badge" id="bp-badge"></span>
      </span>
      <button class="bp-btn-clear" id="bp-clear" onclick="bpClearAll()" style="display:none;">Clear all</button>
    </div>
    <ul class="bp-sel-list" id="bp-sel-list"></ul>
    <div class="bp-empty" id="bp-empty">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <p>Select a product to pick its batch and quantity</p>
    </div>
    <div class="bp-footer">
      <div class="bp-summary">
        <span>Total labels to print</span>
        <span class="bp-total" id="bp-total">0</span>
      </div>
      <button class="bp-btn-print" id="bp-print-btn" onclick="bpGenerate()" disabled>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Generate &amp; Print Labels
      </button>
    </div>
  </div>

</div>

<!-- ── Batch Selection Modal ── -->
<div class="bm-overlay" id="bm-overlay">
  <div class="bm-box">

    <!-- Header -->
    <div class="bm-head">
      <div>
        <div class="bm-product-name" id="bm-product-name"></div>
        <div class="bm-product-meta">
          <span id="bm-product-code"></span>
          <span id="bm-product-price"></span>
        </div>
      </div>
      <button class="bm-close" onclick="bpCloseModalDirect()">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="bm-body">

      <!-- TOP TABLE: Batch Search -->
      <div class="bm-section">
        <div class="bm-section-head">
          <span class="bm-section-title">Search Batch</span>
        </div>
        <div class="bm-search-wrap">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
          <input type="text" class="bm-search-input" id="bm-batch-search" placeholder="Type to search batch name…" autocomplete="off">
        </div>
        <div class="bm-tbl-wrap">
          <table class="bm-tbl">
            <thead>
              <tr>
                <th>Batch Name</th>
                <th>Type</th>
                <th>Expiry</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="bm-avail-body"></tbody>
          </table>
          <div class="bm-msg" id="bm-avail-loading">Loading…</div>
          <div class="bm-msg" id="bm-avail-empty" style="display:none;">No active batches found.</div>
        </div>
      </div>

      <div class="bm-divider"></div>

      <!-- BOTTOM TABLE: Selected Batches -->
      <div class="bm-section">
        <div class="bm-section-head">
          <span class="bm-section-title">Selected Batches</span>
          <span class="bm-count-tag" id="bm-sel-count" style="display:none;"></span>
        </div>
        <div class="bm-tbl-wrap">
          <table class="bm-tbl">
            <thead>
              <tr>
                <th>Batch Name</th>
                <th>Qty</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="bm-sel-body"></tbody>
          </table>
          <div class="bm-msg" id="bm-sel-empty">No batches added yet — add from the table above.</div>
        </div>
      </div>

    </div><!-- /.bm-body -->

    <!-- Footer -->
    <div class="bm-footer">
      <button class="bm-cancel" onclick="bpCloseModalDirect()">Cancel</button>
      <button class="bm-done" onclick="bpDoneModal()">Done</button>
    </div>

  </div>
</div>

<script>
(function () {
  var base_url = document.getElementById('baseUrl').value;
  var currency = document.getElementById('siteCurrency').value;
  var selected     = {};   // key: productId + '_b' + batchId
  var labelsPerRow = 2;
  var searchTimer  = null;
  var currentProduct = null;

  /* ════════════════════════════════
     PRODUCT SEARCH
  ════════════════════════════════ */
  function bpFetch(search, category) {
    var tbody   = document.getElementById('bp-tbody');
    var spinner = document.getElementById('bp-spinner');
    var noRes   = document.getElementById('bp-no-results');
    var countEl = document.getElementById('bp-result-count');
    tbody.innerHTML = '';
    noRes.classList.remove('show');
    spinner.classList.add('show');
    countEl.textContent = '';
    $.ajax({
      url: base_url + 'product/product/search_products_for_barcode',
      type: 'POST',
      data: { search: search, category: category },
      success: function (res) {
        spinner.classList.remove('show');
        var list = typeof res === 'string' ? JSON.parse(res) : res;
        if (!Array.isArray(list)) list = [];
        if (list.length === 0) { noRes.classList.add('show'); countEl.textContent = '0 products'; return; }
        countEl.textContent = list.length + (list.length === 20 ? '+' : '') + ' product' + (list.length === 1 ? '' : 's');
        bpRenderRows(list);
      },
      error: function () { spinner.classList.remove('show'); noRes.classList.add('show'); }
    });
  }

  function bpRenderRows(list) {
    var tbody = document.getElementById('bp-tbody');
    tbody.innerHTML = list.map(function (p) {
      var price = parseFloat(p.price) || 0;
      return '<tr onclick="bpOpenModal(' + JSON.stringify(p).replace(/"/g, '&quot;') + ')">' +
        '<td><span class="bp-code">' + esc(p.product_id) + '</span></td>' +
        '<td class="bp-pname">' + esc(p.product_name) + '</td>' +
        '<td>' + (p.category_name ? '<span class="bp-cat-tag">' + esc(p.category_name) + '</span>' : '') + '</td>' +
        '<td class="bp-price">' + fmt(price) + '</td>' +
        '<td class="bp-td-action">' +
          '<button class="bp-btn bp-btn-select bp-btn-sm" onclick="event.stopPropagation();bpOpenModal(' + JSON.stringify(p).replace(/"/g, '&quot;') + ')">' +
            '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Select' +
          '</button>' +
        '</td>' +
      '</tr>';
    }).join('');
  }

  /* ════════════════════════════════
     BATCH MODAL
  ════════════════════════════════ */
  var modalSelected  = {};   // temp: {batchId: {batch_id, batch_name, busage, qty}}
  var bmSearchTimer  = null;

  window.bpOpenModal = function (p) {
    currentProduct = p;

    // Pre-load existing selections for this product into modalSelected
    modalSelected = {};
    Object.keys(selected).forEach(function (k) {
      if (k.indexOf(p.product_id + '_b') === 0) {
        var b = selected[k];
        modalSelected[b.batch_id] = { batch_id: b.batch_id, batch_name: b.batch_name, busage: b.busage || '', qty: b.qty };
      }
    });

    document.getElementById('bm-product-name').textContent = p.product_name;
    document.getElementById('bm-product-code').textContent = 'Code: ' + p.product_id;
    document.getElementById('bm-product-price').textContent = fmt(parseFloat(p.price) || 0) + (currency ? ' ' + currency : '');
    document.getElementById('bm-batch-search').value = '';
    document.getElementById('bm-overlay').classList.add('open');

    bmFetch('');
    bmRenderSelTable();
  };

  /* Fetch top-table batches */
  function bmFetch(search) {
    var body    = document.getElementById('bm-avail-body');
    var loading = document.getElementById('bm-avail-loading');
    var empty   = document.getElementById('bm-avail-empty');
    body.innerHTML = '';
    loading.style.display = 'block';
    empty.style.display   = 'none';
    $.ajax({
      url: base_url + 'product/product/get_product_batches',
      type: 'POST',
      data: { product_id: currentProduct.id, search: search },
      success: function (res) {
        loading.style.display = 'none';
        var list = typeof res === 'string' ? JSON.parse(res) : res;
        if (!Array.isArray(list) || list.length === 0) { empty.style.display = 'block'; return; }
        bmRenderAvail(list);
      }
    });
  }

  /* Render top table rows */
  function bmRenderAvail(list) {
    var body = document.getElementById('bm-avail-body');
    body.innerHTML = list.map(function (b) {
      var isAdded = !!modalSelected[b.id];
      var typeTag = '<span class="bm-type-tag ' + (b.busage === 'single' ? 'bm-type-single' : 'bm-type-multiple') + '">'
                    + (b.busage === 'single' ? 'Product' : 'Shared') + '</span>';
      return '<tr id="bm-avrow-' + b.id + '" class="' + (isAdded ? 'bm-row-added' : '') + '">' +
        '<td>' + esc(b.batch_name) + '</td>' +
        '<td>' + typeTag + '</td>' +
        '<td style="color:var(--text-3);font-size:12px;">' + (b.edate || '—') + '</td>' +
        '<td style="text-align:right;">' +
          '<button class="bm-av-add' + (isAdded ? ' added' : '') + '" id="bm-avbtn-' + b.id + '" ' +
            'onclick="bmAddBatch(' + b.id + ',\'' + esc(b.batch_name) + '\',\'' + b.busage + '\')">' +
            (isAdded ? '✓ Added' : '+ Add') +
          '</button>' +
        '</td>' +
      '</tr>';
    }).join('');
  }

  /* Add batch to modal temp selection */
  window.bmAddBatch = function (batchId, batchName, busage) {
    if (modalSelected[batchId]) return;
    modalSelected[batchId] = { batch_id: batchId, batch_name: batchName, busage: busage, qty: 1 };
    var btn = document.getElementById('bm-avbtn-' + batchId);
    if (btn) { btn.textContent = '✓ Added'; btn.classList.add('added'); }
    var row = document.getElementById('bm-avrow-' + batchId);
    if (row) row.classList.add('bm-row-added');
    bmRenderSelTable();
  };

  /* Remove batch from modal temp selection */
  window.bmRemoveBatch = function (batchId) {
    delete modalSelected[batchId];
    var btn = document.getElementById('bm-avbtn-' + batchId);
    if (btn) { btn.textContent = '+ Add'; btn.classList.remove('added'); }
    var row = document.getElementById('bm-avrow-' + batchId);
    if (row) row.classList.remove('bm-row-added');
    bmRenderSelTable();
  };

  /* Qty controls on bottom table */
  window.bmQtyDelta = function (bid, d) {
    if (!modalSelected[bid]) return;
    modalSelected[bid].qty = Math.max(1, (modalSelected[bid].qty || 1) + d);
    var inp = document.getElementById('bm-qty-' + bid);
    if (inp) inp.value = modalSelected[bid].qty;
  };
  window.bmQtyInput = function (bid, v) {
    if (!modalSelected[bid]) return;
    modalSelected[bid].qty = Math.max(1, parseInt(v) || 1);
  };

  /* Render bottom selected table */
  function bmRenderSelTable() {
    var body    = document.getElementById('bm-sel-body');
    var empty   = document.getElementById('bm-sel-empty');
    var countEl = document.getElementById('bm-sel-count');
    var keys    = Object.keys(modalSelected);

    if (keys.length === 0) {
      body.innerHTML = '';
      empty.style.display  = 'block';
      countEl.style.display = 'none';
    } else {
      empty.style.display   = 'none';
      countEl.style.display = '';
      countEl.textContent   = keys.length;
      body.innerHTML = keys.map(function (bid) {
        var b = modalSelected[bid];
        return '<tr>' +
          '<td>' + esc(b.batch_name) + '</td>' +
          '<td style="width:110px;">' +
            '<div class="bp-qty">' +
              '<button class="bp-qty-btn" onclick="bmQtyDelta(' + bid + ',-1)">&#8722;</button>' +
              '<input class="bp-qty-input" id="bm-qty-' + bid + '" type="number" min="1" value="' + b.qty + '" oninput="bmQtyInput(' + bid + ',this.value)">' +
              '<button class="bp-qty-btn" onclick="bmQtyDelta(' + bid + ',1)">+</button>' +
            '</div>' +
          '</td>' +
          '<td style="text-align:right;">' +
            '<button class="bm-rm-btn" onclick="bmRemoveBatch(' + bid + ')">Remove</button>' +
          '</td>' +
        '</tr>';
      }).join('');
    }
  }

  /* Done — commit modalSelected into main selected */
  window.bpDoneModal = function () {
    if (!currentProduct) return;
    var p = currentProduct;

    // Remove existing selections for this product
    Object.keys(selected).forEach(function (k) {
      if (k.indexOf(p.product_id + '_b') === 0) delete selected[k];
    });

    // Commit modal selections
    Object.keys(modalSelected).forEach(function (bid) {
      var b   = modalSelected[bid];
      var key = p.product_id + '_b' + bid;
      selected[key] = {
        product_id: p.product_id, product_name: p.product_name,
        price: parseFloat(p.price) || 0,
        batch_id: b.batch_id, batch_name: b.batch_name, busage: b.busage,
        qty: b.qty
      };
    });

    bpRenderSelected();
    bpCloseModalDirect();
  };

  window.bpCloseModalDirect = function () {
    document.getElementById('bm-overlay').classList.remove('open');
    modalSelected  = {};
    currentProduct = null;
  };

  /* Batch search wiring */
  document.getElementById('bm-batch-search').addEventListener('input', function () {
    clearTimeout(bmSearchTimer);
    var q = this.value;
    bmSearchTimer = setTimeout(function () { bmFetch(q); }, 300);
  });

  /* ════════════════════════════════
     RIGHT PANEL
  ════════════════════════════════ */
  function bpRenderSelected() {
    var list   = document.getElementById('bp-sel-list');
    var empty  = document.getElementById('bp-empty');
    var clrBtn = document.getElementById('bp-clear');
    var keys   = Object.keys(selected);
    if (keys.length === 0) {
      list.innerHTML = '';
      empty.style.display = 'block';
      clrBtn.style.display = 'none';
    } else {
      empty.style.display = 'none';
      clrBtn.style.display = '';
      list.innerHTML = keys.map(function (k) {
        var p = selected[k];
        return '<li class="bp-sel-item">' +
          '<div class="bp-item-info">' +
            '<div class="bp-item-name">' + esc(p.product_name) + '</div>' +
            '<div class="bp-item-meta">' +
              '<span class="bp-item-code">' + esc(p.product_id) + '</span>' +
              '<span class="bp-item-batch">' + esc(p.batch_name) + '</span>' +
            '</div>' +
          '</div>' +
          '<div class="bp-qty">' +
            '<button class="bp-qty-btn" onclick="bpDelta(\'' + k + '\',-1)">&#8722;</button>' +
            '<input class="bp-qty-input" id="bpq-' + k + '" type="number" min="1" value="' + p.qty + '" oninput="bpQtyInp(\'' + k + '\',this.value)">' +
            '<button class="bp-qty-btn" onclick="bpDelta(\'' + k + '\',1)">+</button>' +
          '</div>' +
          '<button class="bp-btn bp-btn-remove" onclick="bpRemove(\'' + k + '\')">' +
            '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
          '</button>' +
        '</li>';
      }).join('');
    }
    bpUpdateTotals();
  }

  window.bpRemove = function (k) {
    delete selected[k];
    var btn = document.getElementById('bmbtn-' + k);
    if (btn) { btn.textContent = 'Add'; btn.classList.remove('added'); }
    bpRenderSelected();
  };

  window.bpDelta = function (k, d) {
    if (!selected[k]) return;
    selected[k].qty = Math.max(1, (selected[k].qty || 1) + d);
    var inp = document.getElementById('bpq-' + k);
    if (inp) inp.value = selected[k].qty;
    bpUpdateTotals();
  };

  window.bpQtyInp = function (k, v) {
    if (!selected[k]) return;
    selected[k].qty = Math.max(1, parseInt(v) || 1);
    bpUpdateTotals();
  };

  function bpUpdateTotals() {
    var keys  = Object.keys(selected);
    var total = keys.reduce(function (s, k) { return s + (selected[k].qty || 1); }, 0);
    document.getElementById('bp-total').textContent = total;
    document.getElementById('bp-badge').textContent = keys.length || '';
    document.getElementById('bp-print-btn').disabled = keys.length === 0;
  }

  /* ════════════════════════════════
     CONTROLS
  ════════════════════════════════ */

  window.bpClearAll = function () {
    selected = {};
    bpRenderSelected();
  };

  /* PHP values for iframe label print */
  var bpCompany     = '<?php echo addslashes(isset($company_name) ? $company_name : ''); ?>';
  var bpCurrency    = '<?php echo addslashes(isset($currency) ? $currency : ''); ?>';
  var bpCurrencyPos = <?php echo isset($position) ? (int)$position : 0; ?>;

  function bpFmtPrice(p) {
    return parseFloat(p).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function bpPrintViaIframe(datas) {
    var body = '';
    datas.forEach(function (item) {
      for (var i = 0; i < parseInt(item.noOfLabel || 1); i++) {
        var pv = bpFmtPrice(item.price);
        var pd = bpCurrencyPos === 0
            ? esc(bpCurrency) + ' ' + pv
            : pv + ' ' + esc(bpCurrency);
        var rawLen = (bpCurrency + ' ' + pv).replace(/&amp;/g,'&').length;
        var priceFz = rawLen <= 10 ? '14pt' : rawLen <= 13 ? '11pt' : rawLen <= 16 ? '9pt' : '7.5pt';
        var batchLine = item.batchName
            ? '<div class="lbl-batch">Batch ID: ' + esc(item.batchName) + '</div>' : '';
        body +=
          '<div class="lbl">' +
            '<div class="lbl-co">'    + esc(bpCompany)        + '</div>' +
            '<div class="lbl-name">'  + esc(item.productName) + '</div>' +
            '<div class="lbl-price" style="font-size:' + priceFz + '">' + pd + '</div>' +
            '<svg class="lbl-bc" data-pid="' + esc(item.productId) + '"></svg>' +
            '<div class="lbl-code">'  + esc(item.productId)   + '</div>' +
            batchLine +
          '</div>';
      }
    });

    var iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:1px;height:1px;border:0;';
    document.body.appendChild(iframe);
    var doc = iframe.contentWindow.document;
    doc.open();
    doc.write([
      '<!DOCTYPE html><html><head><meta charset="UTF-8">',
      '<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"><\/script>',
      '<link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&display=swap" rel="stylesheet">',
      '<style>',
      '*{box-sizing:border-box;margin:0;padding:0;}',
      'body{background:#fff;font-family:Arial,sans-serif;padding:6mm;}',
      '.lbl{display:flex;flex-direction:column;align-items:center;text-align:center;',
        'width:1.69in;height:1.25in;border:1.5pt solid #000;border-radius:5pt;',
        'padding:5pt 6pt 4pt;overflow:hidden;margin-bottom:5mm;',
        'page-break-inside:avoid;break-inside:avoid;}',
      '.lbl-co   {font-size:6pt;color:#444;margin-bottom:1pt;}',
      '.lbl-name {font-size:8pt;font-weight:700;margin-bottom:1pt;}',
      '.lbl-price{font-size:14pt;font-weight:900;white-space:nowrap;margin-bottom:2pt;}',
      '.lbl-bc   {width:1.45in;height:0.38in;margin-bottom:1pt;}',
      '.lbl-code {font-size:10pt;font-family:"Share Tech Mono","Courier New",monospace;letter-spacing:.12em;margin-bottom:.5pt;}',
      '.lbl-batch{font-size:5.5pt;}',
      '</style></head><body>',
      body,
      '</body></html>'
    ].join(''));
    doc.close();

    function doprint() {
      iframe.contentWindow.focus();
      iframe.contentWindow.print();
      setTimeout(function () { document.body.removeChild(iframe); }, 2000);
    }

    iframe.onload = function () {
      var win = iframe.contentWindow;
      /* Generate barcodes client-side — no PHP image request needed */
      if (win.JsBarcode) {
        doc.querySelectorAll('svg.lbl-bc').forEach(function (svg) {
          try {
            win.JsBarcode(svg, svg.getAttribute('data-pid'), {
              format: 'CODE128',
              displayValue: false,
              margin: 0,
              height: 36,
              width: 1.4
            });
          } catch(e) {}
        });
      }
      doprint();
    };
  }

  window.bpGenerate = function () {
    var keys = Object.keys(selected);
    if (keys.length === 0) return;
    var labels = keys.map(function (k) {
      var p = selected[k];
      return { productId: p.product_id, productName: p.product_name, price: p.price,
               batchId: p.batch_id, batchName: p.batch_name, noOfLabel: p.qty };
    });
    $.ajax({
      url: base_url + 'product/product/printsticker',
      type: 'POST',
      data: { labels: labels, cqty: labelsPerRow },
      success: function (res) {
        var datas = typeof res === 'string' ? JSON.parse(res) : res;
        if (!datas || datas.length === 0) { alert('No data available.'); return; }
        bpPrintViaIframe(datas);
      }
    });
  };

  /* ════════════════════════════════
     HELPERS
  ════════════════════════════════ */
  function fmt(n) { return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }

  /* ── Search wiring ── */
  document.getElementById('bp-search').addEventListener('input', function () {
    clearTimeout(searchTimer);
    var q = this.value, cat = document.getElementById('bp-category').value;
    searchTimer = setTimeout(function () { bpFetch(q, cat); }, 300);
  });
  document.getElementById('bp-category').addEventListener('change', function () {
    clearTimeout(searchTimer);
    bpFetch(document.getElementById('bp-search').value, this.value);
  });

  /* ── Init ── */
  bpFetch('', '');
  bpRenderSelected();
}());
</script>
