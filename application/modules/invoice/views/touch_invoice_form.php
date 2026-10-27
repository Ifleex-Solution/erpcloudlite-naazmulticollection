<style>
/* ── Reset & base ── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
.ti-wrapper {
    font-family: 'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
.ti-wrapper *:not(i):not([class*="fa"]):not([class*="ti-icon"]):not([class*="glyphicon"]) {
    font-family: inherit;
}

.ti-wrapper {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 60px);
    background: #f0f2f5;
    overflow: hidden;
}
:fullscreen .content-wrapper,
:-webkit-full-screen .content-wrapper,
:-moz-full-screen .content-wrapper {
    margin-left: 0 !important;
    padding: 0 !important;
}

/* ── Top bar — matches panel-bd panel-heading style ── */
.ti-topbar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 22px;
    background: #fff;
    color: #374767;
    flex-shrink: 0;
    flex-wrap: wrap;
    border-bottom: 2px solid #F1F5F9;
    position: relative;
}
.ti-topbar::before {
    content: '';
    width: 0;
    height: 0;
    border-top: 16px solid #8b5cf6;
    border-right: 16px solid transparent;
    position: absolute;
    left: 0;
    top: 0;
    pointer-events: none;
}
.ti-topbar-title {
    margin-right: auto;
    letter-spacing: .3px;
    font-size: 15px;
    font-weight: 600;
    color: #1E293B;
}
.ti-topbar input {
    height: 40px;
    border: 1.5px solid #d0d7e6;
    border-radius: 7px;
    padding: 0 10px;
    font-size: 13px;
    background: #fff;
    color: #1a2a45;
    min-width: 140px;
    cursor: pointer;
}
.ti-topbar input:focus { outline: none; border-color: #8b5cf6; }

/* Select2 in white topbar */
.ti-topbar-field .select2-container { min-width: 140px; }
.ti-topbar-field .select2-container .select2-selection--single {
    height: 40px;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    background: #F8FAFC;
    display: flex;
    align-items: center;
}
.ti-topbar-field .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 40px;
    padding-left: 10px;
    padding-right: 28px;
    color: #1a2a45 !important;
    font-size: 13px;
}
.ti-topbar-field .select2-container .select2-selection--single .select2-selection__arrow { height: 40px; right: 6px; }
.ti-topbar-field .select2-container--focus .select2-selection--single,
.ti-topbar-field .select2-container--open  .select2-selection--single { border-color: #8b5cf6; background: #fff; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }
.ti-topbar-label { font-size: 11px; color: #888; margin-bottom: 2px; }
.ti-topbar-field { display: flex; flex-direction: column; }
.ti-btn-addcust {
    height: 36px;
    padding: 0 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background: #f4f4f4;
    color: #374767;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    display: flex; align-items: center; gap: 6px;
}
.ti-btn-addcust:hover { background: #e8e8e8; border-color: #bbb; }
.ti-btn-green { background: #27ae60 !important; color: #fff !important; border-color: #219150 !important; }
.ti-btn-green:hover { background: #219150 !important; border-color: #1a7a42 !important; }

/* ── Main body split ── */
.ti-body {
    display: flex;
    flex: 1;
    overflow: hidden;
    gap: 0;
}

/* ══════════════ LEFT PANEL ══════════════ */
.ti-left {
    width: 50%;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-right: 1px solid #dde2ec;
    overflow: hidden;
}

/* Search */
.ti-search-wrap {
    padding: 10px 12px 8px;
    border-bottom: 1px solid #eee;
    flex-shrink: 0;
}
.ti-search {
    width: 100%;
    height: 44px;
    border: 1.5px solid #E2E8F0;
    border-radius: 10px;
    padding: 0 14px 0 40px;
    font-size: 14px;
    background: #F8FAFC url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.242 1.156a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 12px center;
    outline: none;
    transition: border-color .2s;
}
.ti-search:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }
#ti_category {
    height: 44px;
    border: 1.5px solid #D8CCFB;
    border-radius: 10px;
    padding: 0 14px;
    font-size: 14px;
    background-color: #F5F2FE;
    color: #6D28D9;
    font-weight: 600;
    outline: none;
    box-shadow: none;
    transition: border-color .2s, background .2s;
    appearance: auto;
}
#ti_category:focus { border-color: #8b5cf6; background-color: #fff; box-shadow: 0 0 0 3px rgba(139,92,246,.15); }
/* custom.js auto-converts every select.form-control (that isn't
   .dont-select-me) into a select2 widget site-wide, which hides the
   native <select> above and replaces it with its own markup — so the
   rule above never actually rendered. This targets the real element. */
.ti-search-wrap .select2-container { flex: 0 0 50%; }
.ti-search-wrap .select2-container .select2-selection--single {
    height: 44px !important;
    border: 1.5px solid #D8CCFB !important;
    border-radius: 10px !important;
    background-color: #F5F2FE !important;
    display: flex;
    align-items: center;
}
.ti-search-wrap .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 44px !important;
    padding-left: 14px !important;
    color: #6D28D9 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
}
.ti-search-wrap .select2-container .select2-selection--single .select2-selection__arrow { height: 44px !important; }
.ti-search-wrap .select2-container--focus .select2-selection--single,
.ti-search-wrap .select2-container--open  .select2-selection--single {
    border-color: #8b5cf6 !important;
    background-color: #fff !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.15) !important;
}

/* Product grid */
.ti-grid {
    flex: 1;
    overflow-y: auto;
    padding: 10px 10px 6px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    align-content: start;
}
.ti-grid::-webkit-scrollbar { width: 4px; }
.ti-grid::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

.ti-tile {
    background: #fff;
    border: 1.5px solid #e4e9f2;
    border-radius: 10px;
    padding: 8px 6px 10px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all .15s;
    min-height: 150px;
    user-select: none;
    position: relative;
    overflow: visible;
}
.ti-tile:hover, .ti-tile:active {
    border-color: #8b5cf6;
    background: #F8F6FE;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(139,92,246,.15);
}
.ti-tile-name {
    font-size: 11.5px;
    font-weight: 600;
    color: #2c3e50;
    line-height: 1.3;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 5px;
}
.ti-tile-price {
    font-size: 11.5px;
    font-weight: 700;
    color: #8b5cf6;
    background: #F3F0FE;
    padding: 2px 8px;
    border-radius: 12px;
}
.ti-tile-nostock {
    position: absolute;
    top: 4px; right: 4px;
    font-size: 9px;
    background: #ff5252;
    color: #fff;
    border-radius: 4px;
    padding: 1px 4px;
    font-weight: 700;
}
.ti-tile-img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    margin-bottom: 8px;
    flex-shrink: 0;
}
.ti-tile-icon {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    background: #eef2ff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    flex-shrink: 0;
}
.ti-tile-icon i {
    font-size: 30px;
    color: #a0aec0;
}

/* ══════════════ RIGHT PANEL ══════════════ */
.ti-right {
    width: 50%;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #f0f2f5;
}

/* Cart */
.ti-cart-label {
    padding: 8px 14px 4px;
    font-size: 11px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .5px;
    flex-shrink: 0;
}
.ti-cart {
    flex: 1;
    overflow-y: auto;
    padding: 0 10px 6px;
}
.ti-cart::-webkit-scrollbar { width: 4px; }
.ti-cart::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

.ti-cart-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #bbb;
    font-size: 14px;
    gap: 8px;
}
.ti-cart-empty i { font-size: 36px; }
.ti-cart.ti-cart-err {
    border: 2px dashed #e53935;
    border-radius: 10px;
    background: #fff5f5;
}
.ti-cart.ti-cart-err .ti-cart-empty { color: #e53935; }

.ti-cart-item {
    background: #fff;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 8px;
    border: 1px solid #e8ecf4;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.ti-ci-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6px;
}
.ti-ci-name {
    font-size: 13px;
    font-weight: 700;
    color: #1a2a45;
    flex: 1;
    padding-right: 8px;
    line-height: 1.3;
}
.ti-ci-delete {
    background: #fff0f0;
    border: 1px solid #ffcdd2;
    color: #e53935;
    border-radius: 6px;
    width: 28px;
    height: 28px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 13px;
    flex-shrink: 0;
}
.ti-ci-delete:hover { background: #ffebee; }

.ti-ci-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}
.ti-ci-tag {
    font-size: 11px;
    background: #F3F0FE;
    color: #8b5cf6;
    border-radius: 6px;
    padding: 2px 8px;
    font-weight: 600;
}
.ti-ci-avqty {
    font-size: 11px;
    background: #f0fff4;
    color: #2e7d32;
    border-radius: 6px;
    padding: 2px 8px;
    font-weight: 600;
}

.ti-ci-bottom {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
}
.ti-qty-control {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1.5px solid #d0d7e6;
    border-radius: 8px;
    overflow: hidden;
}
.ti-qty-btn {
    width: 34px; height: 34px;
    background: #f4f6fb;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #8b5cf6;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    transition: background .15s;
    flex-shrink: 0;
}
.ti-qty-btn:hover { background: #F3F0FE; }
.ti-qty-input {
    width: 52px;
    height: 34px;
    border: none;
    border-left: 1.5px solid #d0d7e6;
    border-right: 1.5px solid #d0d7e6;
    text-align: center;
    font-size: 14px;
    font-weight: 700;
    color: #1a2a45;
    outline: none;
}
.ti-ci-edit-btn {
    background: #F3F0FE;
    border: 1px solid #d0d7e6;
    color: #8b5cf6;
    border-radius: 6px;
    padding: 5px 14px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 6px;
}
.ti-ci-edit-btn:hover { background: #E9E3FD; }
.ti-ci-left {
    display: flex; flex-direction: column; align-items: flex-start; flex-shrink: 0;
}
.ti-ci-pricebox {
    flex: 1;
    display: table;
    border-collapse: collapse;
    font-size: 12px;
    min-width: 180px;
}
.ti-ci-prow {
    display: table-row;
}
.ti-ci-prow > span {
    display: table-cell;
    padding: 2px 6px;
    white-space: nowrap;
}
.ti-ci-prow > span:first-child {
    color: #888;
    font-weight: 500;
    text-align: left;
    width: 60%;
}
.ti-ci-prow > span:last-child {
    text-align: right;
    font-weight: 600;
    color: #1a2a45;
}
.ti-ci-prow.ti-row-disc > span:last-child { color: #2e7d32; }
.ti-ci-prow.ti-row-vat  > span:last-child { color: #e53935; }
.ti-ci-prow.ti-row-total > span:first-child { color: #1a2a45; font-weight: 700; border-top: 1px solid #e0e4ef; padding-top: 4px; }
.ti-ci-prow.ti-row-total > span:last-child  { font-size: 15px; font-weight: 800; color: #1a2a45; border-top: 1px solid #e0e4ef; padding-top: 4px; }

/* ── Totals box ── */
.ti-totals {
    background: #fff;
    border-top: 1px solid #e4e9f2;
    padding: 10px 14px;
    flex-shrink: 0;
}
.ti-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
    font-size: 13px;
}
.ti-total-label { color: #666; }
.ti-total-val { font-weight: 700; color: #1a2a45; font-size: 14px; }
.ti-total-row.grand {
    border-top: 1.5px solid #e4e9f2;
    margin-top: 4px;
    padding-top: 8px;
}
.ti-total-row.grand .ti-total-label { font-weight: 700; font-size: 14px; color: #1a2a45; }
.ti-total-row.grand .ti-total-val { font-size: 18px; color: #4a7cdc; }

.ti-discount-input {
    width: 110px;
    height: 30px;
    border: 1.5px solid #d0d7e6;
    border-radius: 6px;
    padding: 0 8px;
    font-size: 13px;
    font-weight: 600;
    text-align: right;
    color: #1a2a45;
    outline: none;
}
.ti-discount-input:focus { border-color: #4a7cdc; }

/* ── Save bar ── */
.ti-savebar {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #fff;
    border-top: 1px solid #e4e9f2;
    flex-shrink: 0;
}
/* Select2 for payment method in savebar */
.ti-savebar .select2-container { flex: 1; min-width: 0; }
.ti-savebar .select2-container .select2-selection--single {
    height: 44px;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    background: #F8FAFC;
    display: flex;
    align-items: center;
}
.ti-savebar .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 44px;
    padding-left: 12px;
    padding-right: 30px;
    color: #1a2a45 !important;
    font-size: 13px;
}
.ti-savebar .select2-container .select2-selection--single .select2-selection__arrow { height: 44px; right: 8px; }
.ti-savebar .select2-container--focus .select2-selection--single,
.ti-savebar .select2-container--open  .select2-selection--single { border-color: #8b5cf6; background: #fff; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }
.ti-save-btn {
    flex: 2;
    height: 48px;
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    letter-spacing: .4px;
    box-shadow: 0 4px 14px rgba(139,92,246,.35);
    transition: opacity .2s;
}
.ti-save-btn:hover { opacity: .92; }
.ti-save-btn:active { opacity: .85; }

/* ══════════════ SLIDE-UP OVERLAY ══════════════ */
.ti-overlay-bg {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}
.ti-overlay-bg.open {
    display: flex;
    animation: fadeIn .2s;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

.ti-panel {
    background: #fff;
    border-radius: 16px;
    width: 100%;
    max-width: 400px;
    max-height: 72vh;
    overflow-y: auto;
    padding: 0 0 10px;
    animation: slideUp .25s ease;
    box-shadow: 0 8px 40px rgba(0,0,0,.18);
}
@keyframes slideUp { from { transform: translateY(60px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.ti-panel-handle {
    display: flex;
    justify-content: center;
    padding: 7px 0 3px;
}
.ti-panel-handle::before {
    content: '';
    width: 32px; height: 3px;
    background: #ddd;
    border-radius: 4px;
}
.ti-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 12px 6px;
    border-bottom: 1px solid #f0f0f0;
}
.ti-panel-title {
    font-size: 14px;
    font-weight: 700;
    color: #1a2a45;
    max-width: 80%;
    line-height: 1.3;
}
.ti-panel-close {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: #f4f6fb;
    border: none;
    font-size: 15px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #666;
}
.ti-panel-close:hover { background: #eee; }

.ti-panel-body { padding: 8px 12px; position: relative; }
.ti-spinner {
    width: 36px; height: 36px;
    border: 4px solid #e0e8ff;
    border-top-color: #4a7cdc;
    border-radius: 50%;
    animation: ti-spin .7s linear infinite;
}
@keyframes ti-spin { to { transform: rotate(360deg); } }

.ti-field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}
.ti-field-row.full { grid-template-columns: 1fr; }
.ti-field { display: flex; flex-direction: column; gap: 3px; }
.ti-field label {
    font-size: 10px;
    font-weight: 700;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.ti-req { color: #e53935; margin-left: 2px; }
.ti-field-err select,
.ti-field-err input,
.ti-field-err .select2-selection--single {
    border-color: #e53935 !important;
    background: #fff5f5 !important;
}
.ti-topbar-field.ti-field-err .select2-selection--single,
.ti-topbar-field.ti-field-err input { border-color: #e53935 !important; background: #fff5f5 !important; }
.ti-savebar-paymethod-wrap.ti-field-err .select2-selection--single { border-color: #e53935 !important; background: #fff5f5 !important; }
.ti-field input {
    height: 32px;
    border: 1.5px solid #d0d7e6;
    border-radius: 7px;
    padding: 0 8px;
    font-size: 12px;
    font-weight: 600;
    color: #1a2a45;
    outline: none;
    background: #f7f9fc;
    width: 100%;
}
.ti-field input:focus { border-color: #4a7cdc; background: #fff; }

/* Select2 container inside panel fields */
.ti-field .select2-container { width: 100% !important; }
.ti-field .select2-container .select2-selection--single {
    height: 32px;
    border: 1.5px solid #E2E8F0;
    border-radius: 7px;
    background: #F8FAFC;
    display: flex;
    align-items: center;
}
.ti-field .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    padding-left: 8px;
    padding-right: 24px;
}
.ti-field .select2-container .select2-selection--single .select2-selection__arrow {
    height: 32px;
    right: 4px;
}
.ti-field .select2-container--focus .select2-selection--single,
.ti-field .select2-container--open  .select2-selection--single {
    border-color: #8b5cf6;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12);
}
.ti-field .select2-selection__clear { font-size: 14px; }

/* Select2 in right-header fields (Customer/InvoiceType/etc) — 42px height */
.ti-right-header .ti-field .select2-container { width: 100% !important; }
.ti-right-header .ti-field .select2-container .select2-selection--single {
    height: 42px;
    border: 1.5px solid #E2E8F0;
    border-radius: 8px;
    background: #F8FAFC;
    display: flex;
    align-items: center;
}
.ti-right-header .ti-field .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 42px;
    padding-left: 10px;
    padding-right: 28px;
    color: #1a2a45 !important;
    font-size: 13px;
}
.ti-right-header .ti-field .select2-container .select2-selection--single .select2-selection__arrow { height: 42px; right: 6px; }
.ti-right-header .ti-field .select2-container--focus .select2-selection--single,
.ti-right-header .ti-field .select2-container--open  .select2-selection--single { border-color: #8b5cf6; background: #fff; box-shadow: 0 0 0 3px rgba(139,92,246,.12); }

.ti-avqty-badge {
    height: 32px;
    background: #f0fff4;
    border: 1.5px solid #a5d6a7;
    border-radius: 7px;
    display: flex;
    align-items: center;
    padding: 0 8px;
    font-size: 12px;
    font-weight: 800;
    color: #2e7d32;
}

/* Numpad section */
.ti-numpad-section {
    margin-top: 4px;
    border-top: 1px solid #f0f0f0;
    padding-top: 8px;
}
.ti-numpad-top {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 8px;
}
.ti-numpad-qty-ctrl {
    display: flex;
    align-items: center;
    border: 2px solid #4a7cdc;
    border-radius: 8px;
    overflow: hidden;
    height: 36px;
}
.ti-numpad-qty-btn {
    width: 36px; height: 36px;
    background: #eef2ff;
    border: none;
    font-size: 18px;
    font-weight: 700;
    color: #4a7cdc;
    cursor: pointer;
}
.ti-numpad-qty-btn:hover { background: #dce5ff; }
.ti-numpad-qty-val {
    width: 60px; height: 36px;
    border: none;
    border-left: 2px solid #4a7cdc;
    border-right: 2px solid #4a7cdc;
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    color: #1a2a45;
    outline: none;
}

.ti-numpad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    margin-bottom: 12px;
}
.ti-numkey {
    height: 52px;
    background: #f7f9fc;
    border: 1.5px solid #e4e9f2;
    border-radius: 10px;
    font-size: 20px;
    font-weight: 600;
    color: #1a2a45;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .1s;
    user-select: none;
}
.ti-numkey:hover { background: #eef2ff; border-color: #4a7cdc; color: #4a7cdc; }
.ti-numkey:active { transform: scale(.95); }
.ti-numkey.backspace { font-size: 16px; background: #fff3f3; border-color: #ffcdd2; color: #e53935; }
.ti-numkey.zero { grid-column: span 2; }

.ti-price-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
    margin-bottom: 8px;
}

/* Panel footer buttons */
.ti-panel-footer {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 8px;
    padding: 0 12px;
}
.ti-panel-cancel {
    height: 40px;
    background: #f4f6fb;
    border: 1.5px solid #d0d7e6;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #666;
    cursor: pointer;
}
.ti-panel-cancel:hover { background: #eee; }
.ti-panel-add {
    height: 40px;
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(139,92,246,.3);
}
.ti-panel-add:hover { opacity: .92; }

/* Add Customer Modal */
.ti-modal-bg {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}
.ti-modal-bg.open { display: flex; }
.ti-modal {
    background: #fff;
    border-radius: 14px;
    width: 360px;
    padding: 24px;
}
.ti-modal h3 { font-size: 17px; color: #1a2a45; margin-bottom: 16px; }
.ti-modal .ti-field { margin-bottom: 12px; }
.ti-modal-btns { display: flex; gap: 10px; margin-top: 16px; }
.ti-modal-btns button { flex: 1; height: 44px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; }
.ti-modal-cancel { background: #f4f6fb; color: #666; border: 1.5px solid #ddd !important; }
.ti-modal-save { background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3); color: #fff; }

/* Add New Product modal — had no custom styling at all before, so every
   dropdown (auto-select2-ified by custom.js, same as #ti_category) and
   the Save button were plain Bootstrap defaults (blue). */
#addProductModal .modal-footer .btn.btn-primary {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
}
#addProductModal .modal-footer .btn.btn-primary:hover {
    background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important;
}
#addProductModal .form-control:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
}
#addProductModal .select2-container .select2-selection--single {
    border: 1.5px solid #E2E8F0 !important;
    border-radius: 6px !important;
    background: #F8FAFC !important;
    height: 34px !important;
}
#addProductModal .select2-container .select2-selection--single .select2-selection__rendered {
    line-height: 32px !important;
    color: #374151 !important;
}
#addProductModal .select2-container .select2-selection--single .select2-selection__arrow { height: 32px !important; }
#addProductModal .select2-container--focus .select2-selection--single,
#addProductModal .select2-container--open  .select2-selection--single {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
}

/* Scrollbar */
.ti-panel::-webkit-scrollbar { width: 4px; }
.ti-panel::-webkit-scrollbar-thumb { background: #ddd; border-radius: 4px; }
/* Push panel up when VK is open */
.ti-overlay-bg.ti-vk-up {
    padding-bottom: 300px;
    transition: padding-bottom .22s cubic-bezier(.4,0,.2,1);
}
.ti-overlay-bg.ti-vk-up .ti-panel {
    max-height: calc(100vh - 320px);
    transition: max-height .22s cubic-bezier(.4,0,.2,1);
}

/* Numpad field-target tabs */
.ti-numpad-tabs { display: flex; gap: 6px; margin-bottom: 10px; }
.ti-numpad-tab {
    flex: 1; height: 36px; border: 1.5px solid #d0d7e6; border-radius: 8px;
    background: #f7f9fc; font-size: 12px; font-weight: 700; color: #888;
    cursor: pointer; text-transform: uppercase; letter-spacing: .4px; transition: all .15s;
}
.ti-numpad-tab.active { background: #4a7cdc; border-color: #4a7cdc; color: #fff; }
.ti-numpad-tab:not(.active):hover { border-color: #4a7cdc; color: #4a7cdc; }
/* Highlight the input whose value the numpad is targeting */
.ti-field input.numpad-active {
    border-color: #4a7cdc !important;
    background: #fff !important;
    color: #1a2a45 !important;
    box-shadow: 0 0 0 3px rgba(74,124,220,.15);
    -webkit-text-fill-color: #1a2a45 !important;
}
.ti-numpad-qty-val.numpad-active {
    border-color: #4a7cdc !important;
    background: #fff !important;
    color: #1a2a45 !important;
    -webkit-text-fill-color: #1a2a45 !important;
}

/* ── Virtual Keyboard ── */
/* ── Virtual Keyboard ── */
.ti-vk {
    position: fixed;
    bottom: -420px;
    left: 0; right: 0;
    z-index: 9999;
    background: #d1d5db;
    border-top: 1px solid #b0b7c3;
    padding: 6px 8px 12px;
    box-shadow: 0 -4px 24px rgba(0,0,0,.25);
    transition: bottom .22s cubic-bezier(.4,0,.2,1);
    -webkit-user-select: none;
    user-select: none;
}
.ti-vk.open { bottom: 0; }
/* Preview bar */
.ti-vk-bar { display: flex; align-items: center; gap: 8px; padding: 2px 2px 8px; }
.ti-vk-preview {
    flex: 1; background: #fff; border: 1.5px solid #4a7cdc; border-radius: 7px;
    padding: 5px 12px; font-size: 15px; color: #1a2a45; min-height: 36px;
    overflow: hidden; white-space: nowrap; text-overflow: ellipsis;
}
.ti-vk-done {
    height: 36px; padding: 0 18px; background: #4a7cdc; color: #fff;
    border: none; border-radius: 7px; font-size: 14px; font-weight: 700;
    cursor: pointer; white-space: nowrap; flex-shrink: 0;
}
/* Body: both keyboards centered, side by side */
.ti-vk-body { display: flex; gap: 16px; align-items: flex-start; justify-content: center; }
/* ── Letter keys (left) — auto width, fixed key size ── */
.ti-vk-letters { flex: 0 0 auto; }
.ti-vk-row { display: flex; justify-content: center; gap: 9px; margin-bottom: 9px; }
.ti-vk-key {
    flex: 0 0 51px; width: 51px; height: 51px;
    background: #fff; border: none; border-radius: 6px;
    font-size: 18px; font-weight: 400; color: #1a2a45;
    cursor: pointer; box-shadow: 0 2px 0 #8a8a8a;
    display: flex; align-items: center; justify-content: center;
    -webkit-tap-highlight-color: transparent; transition: background .1s;
}
.ti-vk-key:active { background: #b0b4bc; box-shadow: none; transform: translateY(1px); }
.ti-vk-key.fn {
    background: #9ca3af; font-size: 13px; font-weight: 700;
    flex: 0 0 61px; width: 61px; color: #fff;
    box-shadow: 0 2px 0 #6b7280;
}
.ti-vk-key.fn:active { background: #6b7280; }
.ti-vk-key.space { flex: 0 0 210px; width: 210px; font-size: 12px; color: #555; }
.ti-vk-key.shift-on { background: #dbeafe; box-shadow: 0 2px 0 #3563c7; color: #1d4ed8; }
/* ── Numpad (right) — auto width, same fixed key size ── */
.ti-vk-numpad {
    flex: 0 0 auto;
    display: flex; flex-direction: column; gap: 9px;
}
.ti-vk-np-row { display: flex; gap: 9px; }
.ti-vk-np-key {
    flex: 0 0 51px; width: 51px; height: 51px;
    background: #fff; border: none; border-radius: 8px;
    font-size: 18px; font-weight: 600; color: #1a2a45;
    cursor: pointer; box-shadow: 0 3px 0 #8a8a8a;
    display: flex; align-items: center; justify-content: center;
    -webkit-tap-highlight-color: transparent; transition: background .1s;
}
.ti-vk-np-key:active { box-shadow: none; transform: translateY(2px); background: #e5e7eb; }
.ti-vk-np-key.np-dot { font-size: 18px; color: #555; }
.ti-vk-np-key.np-erase {
    background: #fee2e2; color: #dc2626;
    box-shadow: 0 3px 0 #fca5a5; font-size: 18px;
}
.ti-vk-np-key.np-erase:active { background: #fecaca; }
.ti-vk-np-key.np-done {
    height: 34px; background: #4a7cdc; color: #fff;
    box-shadow: 0 2px 0 #2d5bb5; font-size: 13px; font-weight: 700;
    letter-spacing: .5px; border-radius: 6px;
}
.ti-vk-np-key.np-done:active { background: #3563c7; }
/* ── Toast notification ── */
#ti-toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 99999;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
    opacity: 0;
    transition: opacity .3s;
    pointer-events: none;
    max-width: 400px;
    text-align: center;
}
#ti-toast.ti-toast-show    { opacity: 1; }
#ti-toast.ti-toast-success { background: #27ae60; }
#ti-toast.ti-toast-error   { background: #e74c3c; }
#ti-toast.ti-toast-info    { background: #4a7cdc; }
</style>

<div id="ti-toast"></div>
<input type="hidden" id="ti_base_url" value="<?php echo base_url(); ?>">
<input type="hidden" id="ti_vk_enabled" value="<?php echo isset($vk_enable) ? $vk_enable : 1; ?>">
<?php
echo "<script>";
echo "var ap_categories=" . json_encode($categories ?: []) . ";";
echo "var ap_units=" . json_encode($unit_list ?: []) . ";";
echo "var ap_suppliers=" . json_encode($all_supplier ?: []) . ";";
echo "var ap_stores=" . json_encode($store_list ?: []) . ";";
echo "</script>";
?>

<!-- ════════════ WRAPPER ════════════ -->
<div class="ti-wrapper">

    <!-- TOP BAR -->
    <div class="ti-topbar">
        <h4 class="ti-topbar-title"><?php echo (isset($id) && $id) ? 'Edit Invoice ' . (isset($sale_id) && $sale_id ? $sale_id : '#' . (int)$id) : 'New GUI POS'; ?></h4>

        <div class="ti-topbar-field" id="ti_barcode_wrap">
            <span class="ti-topbar-label">Barcode / Product ID</span>
            <input type="text" id="ti_barcode" placeholder="Scan or type &amp; press Enter" autocomplete="off" style="width:240px;">
        </div>

        <div class="ti-topbar-field">
            <span class="ti-topbar-label">&nbsp;</span>
            <button class="btn btn-info btn-sm" onclick="openAddProductModal()" style="height:40px; padding:0 14px;">
                <i class="fa fa-plus"></i> Add Product
            </button>
        </div>

        <div class="ti-topbar-field" id="ti_branch_wrap">
            <span class="ti-topbar-label">Branch <span class="ti-req">*</span></span>
            <select class="form-control dont-select-me" id="ti_branch">
                <option value="">Select Branch</option>
            </select>
        </div>

        <div class="ti-topbar-field" id="ti_date_wrap">
            <span class="ti-topbar-label">Date <span class="ti-req">*</span></span>
            <input type="text" id="ti_date" class="datepicker" value="<?php echo date('Y-m-d'); ?>" style="width:120px;">
        </div>

        <button class="ti-btn-addcust ti-btn-green" onclick="document.getElementById('ti_cust_modal').classList.add('open')">
            &#43; Customer
        </button>
        <button class="ti-btn-addcust" onclick="tiToggleFullscreen()" id="ti_fs_btn" title="Toggle Fullscreen" style="margin-left:4px;">
            <i class="fa fa-expand" id="ti_fs_icon"></i>
        </button>
    </div>

    <!-- BODY -->
    <div class="ti-body">

        <!-- ═══ LEFT: Product Grid ═══ -->
        <div class="ti-left">
            <div class="ti-search-wrap" style="display:flex; gap:6px; align-items:center;">
                <select id="ti_category" class="form-control" style="flex:0 0 50%; height:44px; font-size:13px;" onchange="tiOnCategoryChange()">
                    <option value="">All Categories</option>
                    <?php if (!empty($categories)) foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['category_id']; ?>"><?php echo html_escape($cat['category_name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" class="ti-search ti-vk-field" id="ti_search" placeholder="Search product..." oninput="tiOnSearchInput(this.value)" readonly style="flex:0 0 calc(50% - 6px);">
            </div>
            <div class="ti-grid" id="ti_grid">
                <!-- Tiles rendered by JS -->
            </div>
        </div>

        <!-- ═══ RIGHT: Cart + Totals ═══ -->
        <div class="ti-right">

            <!-- Header fields -->
            <div class="ti-right-header" style="display:flex; gap:8px; padding:10px 12px 6px; flex-shrink:0; flex-wrap:wrap; background:#fff; border-bottom:1px solid #eee;">
                <div class="ti-field" id="ti_customer_wrap" style="flex:1; min-width:130px;">
                    <label>Customer <span class="ti-req">*</span></label>
                    <select class="form-control dont-select-me" id="ti_customer">
                        <option value="">Select Customer</option>
                    </select>
                </div>
                <div class="ti-field" style="flex:1; min-width:120px;">
                    <label>Invoice Type</label>
                    <select class="form-control dont-select-me" id="ti_invoicetype">
                        <option value="cash">Cash</option>
                        <option value="credit">Credit</option>
                        <option value="cash_vat">Cash VAT</option>
                        <option value="credit_vat">Credit VAT</option>
                    </select>
                </div>
                <div class="ti-field" id="ti_due_date_field" style="flex:0 0 100px; min-width:90px; display:none;">
                    <label>Due Days</label>
                    <input type="number" id="ti_due_date" class="form-control" min="0" value="0" placeholder="0">
                </div>
                <div class="ti-field" style="flex:1; min-width:110px;">
                    <label>Incident Type</label>
                    <select class="form-control dont-select-me" id="ti_incidenttype">
                        <option value="1">Retail</option>
                        <option value="2">Wholesale</option>
                    </select>
                </div>
                <div class="ti-field" style="flex:1; min-width:110px;">
                    <label>Salesman</label>
                    <select class="form-control dont-select-me" id="ti_salesman">
                        <option value="1">N/A</option>
                    </select>
                </div>
            </div>

            <div class="ti-cart-label">Cart Items</div>

            <div class="ti-cart" id="ti_cart">
                <div class="ti-cart-empty" id="ti_empty_msg">
                    <i class="fa fa-shopping-cart"></i>
                    <span>Tap a product to add it to the cart</span>
                </div>
            </div>

            <!-- Totals -->
            <div class="ti-totals">
                <div class="ti-total-row">
                    <span class="ti-total-label">Subtotal</span>
                    <span class="ti-total-val" id="ti_subtotal">0.00</span>
                </div>
                <div class="ti-total-row" id="ti_disc_total_row" style="display:none;">
                    <span class="ti-total-label">Item Discount</span>
                    <span class="ti-total-val" style="color:#2e7d32;" id="ti_disc_total">0.00</span>
                </div>
                <div class="ti-total-row" id="ti_vat_total_row" style="display:none;">
                    <span class="ti-total-label">VAT Total</span>
                    <span class="ti-total-val" style="color:#e53935;" id="ti_vat_total">0.00</span>
                </div>
                <div class="ti-total-row">
                    <span class="ti-total-label">Total</span>
                    <span class="ti-total-val" id="ti_total">0.00</span>
                </div>
                <div class="ti-total-row">
                    <span class="ti-total-label">Sale Discount</span>
                    <input type="text" class="ti-discount-input" id="ti_discount" value="0.00" oninput="tiRecalcTotals()" placeholder="0.00">
                </div>
                <div class="ti-total-row grand">
                    <span class="ti-total-label">Grand Total</span>
                    <span class="ti-total-val" id="ti_grand">0.00</span>
                </div>
            </div>

            <!-- Save bar -->
            <div class="ti-savebar">
                <div class="ti-savebar-paymethod-wrap" id="ti_paymethod_wrap" style="flex:1; min-width:0; display:flex; flex-direction:column; gap:2px;">
                    <span style="font-size:10px; font-weight:700; color:#888; text-transform:uppercase; letter-spacing:.4px;">Payment Method <span class="ti-req">*</span></span>
                    <select class="form-control dont-select-me" id="ti_paymethod">
                        <option value="">Select Method</option>
                    </select>
                </div>
                <input type="text" placeholder="Details (optional)" id="ti_details" class="ti-vk-field" readonly style="flex:1.5; height:44px; border:1.5px solid #d0d7e6; border-radius:8px; padding:0 12px; font-size:13px; outline:none; color:#1a2a45; background:#f7f9fc; cursor:pointer;">
                <button class="ti-save-btn" onclick="tiSave()">&#10003; &nbsp;<?php echo (isset($id) && $id) ? 'UPDATE INVOICE' : 'SAVE INVOICE'; ?></button>
            </div>
        </div>
    </div>
</div>

<!-- ════════════ SLIDE-UP PANEL ════════════ -->
<div class="ti-overlay-bg" id="ti_panel_bg" onclick="tiClosePanel(event)">
    <div class="ti-panel" id="ti_panel">
        <div class="ti-panel-handle"></div>
        <div class="ti-panel-header">
            <div class="ti-panel-title" id="ti_panel_title">Product Name</div>
            <button class="ti-panel-close" onclick="tiClosePanelDirect()">&#x2715;</button>
        </div>
        <div class="ti-panel-body" style="position:relative;">

            <!-- Loading overlay — shown while product AJAX is in flight -->
            <div id="ti_panel_loading" style="
                display:none; position:absolute; inset:0; z-index:10;
                background:rgba(255,255,255,.88); border-radius:10px;
                flex-direction:column;
                align-items:center; justify-content:center; gap:10px;">
                <div class="ti-spinner"></div>
                <span style="font-size:13px; font-weight:600; color:#4a7cdc;">Loading product data…</span>
            </div>

            <div class="ti-field-row">
                <div class="ti-field">
                    <label>Store *</label>
                    <select class="form-control dont-select-me ti-panel-select" id="ti_p_store" onchange="tiPanelStoreChange();">
                        <option value="">Select Store</option>
                    </select>
                </div>
                <div class="ti-field">
                    <label>Batch *</label>
                    <select class="form-control dont-select-me ti-panel-select" id="ti_p_batch" onchange="tiPanelBatchChange()">
                        <option value="">Select Batch</option>
                    </select>
                </div>
            </div>

            <div class="ti-field-row">
                <div class="ti-field">
                    <label>Unit *</label>
                    <select class="form-control dont-select-me ti-panel-select" id="ti_p_unit" onchange="tiPanelUnitChange()">
                        <option value="">Select Unit</option>
                    </select>
                </div>
                <div class="ti-field">
                    <label>Av. Qty</label>
                    <div class="ti-avqty-badge" id="ti_p_avqty">— Qty</div>
                </div>
            </div>

            <!-- Numpad -->
            <div class="ti-numpad-section">
                <div class="ti-numpad-top" id="ti_numpad_qty_ctrl">
                    <div class="ti-numpad-qty-ctrl">
                        <button class="ti-numpad-qty-btn" onclick="tiQtyStep(-1)">&#8722;</button>
                        <input type="text" class="ti-numpad-qty-val" id="ti_p_qty" value="1"
                               inputmode="decimal" autocomplete="off"
                               onclick="tiSetNumpadTarget('qty')" oninput="tiNumericOnly(this); tiPanelCalc()">
                        <button class="ti-numpad-qty-btn" onclick="tiQtyStep(1)">&#43;</button>
                    </div>
                </div>

                <div class="ti-price-row">
                    <div class="ti-field">
                        <label>Price *</label>
                        <input type="text" id="ti_p_price" placeholder="0.00"
                               inputmode="decimal" autocomplete="off"
                               onclick="tiSetNumpadTarget('price')" oninput="tiNumericOnly(this); tiPanelCalc()">
                    </div>
                    <div class="ti-field">
                        <label>Discount %</label>
                        <input type="text" id="ti_p_discount" placeholder="0.00" value="0.00"
                               inputmode="decimal" autocomplete="off"
                               onclick="tiSetNumpadTarget('disc')" oninput="tiNumericOnly(this); tiPanelCalc()">
                    </div>
                </div>

                <div class="ti-price-row" id="ti_vat_row" style="display:none;">
                    <div class="ti-field">
                        <label>VAT %</label>
                        <input type="text" id="ti_p_vat" placeholder="0.00" value="0.00"
                               inputmode="decimal" autocomplete="off"
                               onclick="tiSetNumpadTarget('vat')" oninput="tiNumericOnly(this); tiPanelCalc()">
                    </div>
                </div>

                <div style="border-top:1px solid #f0f0f0; margin-top:6px; padding-top:6px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:3px 2px;">
                        <span style="font-size:11px; color:#888;">Discount Value</span>
                        <span style="font-size:13px; font-weight:700; color:#2e7d32;" id="ti_p_disc_val">0.00</span>
                    </div>
                    <div id="ti_p_vat_row_display" style="display:none; justify-content:space-between; align-items:center; padding:3px 2px;">
                        <span style="font-size:11px; color:#888;">VAT Value</span>
                        <span style="font-size:13px; font-weight:700; color:#e53935;" id="ti_p_vat_val">0.00</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:4px 2px 6px;">
                        <span style="font-size:11px; color:#888;">Line Total</span>
                        <span style="font-size:16px; font-weight:800; color:#4a7cdc;" id="ti_p_linetotal">0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="ti-panel-footer">
            <button class="ti-panel-cancel" onclick="tiClosePanelDirect()">Cancel</button>
            <button class="ti-panel-add" onclick="tiAddToCart()">&#10003; &nbsp;Add to Cart</button>
        </div>
    </div>
</div>

<!-- ════════════ ADD CUSTOMER MODAL ════════════ -->
<div class="ti-modal-bg" id="ti_cust_modal">
    <div class="ti-modal">
        <h3>&#43; Add New Customer</h3>
        <div class="ti-field">
            <label>Customer Name *</label>
            <input type="text" id="ti_cust_name" class="ti-vk-field" placeholder="Enter name" readonly>
        </div>
        <div class="ti-field">
            <label>Phone Number *</label>
            <input type="text" id="ti_cust_phone" class="ti-vk-field" placeholder="Enter phone" readonly>
        </div>
        <div class="ti-modal-btns">
            <button class="ti-modal-cancel" onclick="document.getElementById('ti_cust_modal').classList.remove('open')">Cancel</button>
            <button class="ti-modal-save" onclick="tiSaveCustomer()">Save</button>
        </div>
    </div>
</div>

<!-- ════════════ VIRTUAL KEYBOARD ════════════ -->
<div class="ti-vk" id="ti_vk">
    <div class="ti-vk-bar">
        <div class="ti-vk-preview" id="ti_vk_preview">&nbsp;</div>
        <button class="ti-vk-done" onmousedown="event.preventDefault()" onclick="tiVkClose()">Done &#10003;</button>
    </div>
    <div class="ti-vk-body">
        <div class="ti-vk-letters" id="ti_vk_keys"></div>
        <div class="ti-vk-numpad" id="ti_vk_numpad"></div>
    </div>
</div>

<!-- ════════════ PHP DATA → JS ════════════ -->
<?php
echo "<script>";
echo "let ti_stores="   . json_encode($store_list)    . ";";
echo "let ti_customers=" . json_encode($all_customer)  . ";";
echo "let ti_employees=" . json_encode($all_employee)  . ";";
echo "let ti_pmethods="  . json_encode($all_pmethod)   . ";";
echo "let ti_categories=" . json_encode($categories ?: []) . ";";
echo "let ti_batches="   . json_encode($batches)       . ";";
echo "let ti_units="     . json_encode($units)         . ";";
echo "let ti_edit_id="   . (isset($id) && $id ? (int)$id : 0) . ";";
echo "let ti_usertype="  . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>

<script>
let tiCart           = [];    // cart items
let tiDeletedDetails = [];   // invoice_detail_id values of removed items (edit mode)
let tiPanelItem       = null;  // raw product object being configured in panel
let tiPanelProd       = null;  // full product detail from getproduct AJAX
let tiEditIndex       = null;  // cart index being edited (null = new item)
let tiEditPriceLocked  = false; // true during initial panel load for edits — blocks auto price overwrite
let tiLastInvoiceType  = 'cash'; // tracks previous invoice type to allow reverting on cancel

// Sub-unit display vars (mirrors mconversion_ratio/bd/ad in add_invoice_form)
let tiMconvRatio   = '';  // primary sub-unit conversion ratio (e.g., 100 for 1 Drum = 100 Ltr)
let tiBaseUnitName = '';  // base unit label (e.g., "Drum")
let tiSubUnitName  = '';  // sub unit label  (e.g., "Ltr")
// Active sub-unit conversion (set when a non-base unit is selected)
let tiConvType     = '';  // *, /, +, -
let tiConvRatio    = '';  // conversion ratio
let tiConvRatioId  = 0;   // conversionratio_id for save_sale
let tiAvQtyRaw     = 0;   // raw numeric available qty for save_sale
let tiMrpPrice     = '';  // MRP price from getBatchPrice
let tiConvXhr      = null; // in-flight conversion AJAX (aborted on new unit change)

$(document).ready(function () {
    $('body').addClass('sidebar-mini sidebar-collapse');

    // Auto-enter fullscreen on load
    setTimeout(function() {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(function() {});
        }
    }, 300);

    // Initialize Select2 — panel dropdowns (inside slide-up panel)
    $('#ti_p_store, #ti_p_batch, #ti_p_unit').select2({
        placeholder: 'Select option',
        allowClear: false,
        dropdownParent: $('#ti_panel')
    });

    // Initialize Select2 — topbar dropdowns
    $('#ti_branch').select2({
        placeholder: 'Select option',
        allowClear: false,
        dropdownParent: $('body')
    });

    // Initialize Select2 — right-header dropdowns (Customer / Invoice Type / Incident / Salesman)
    $('#ti_customer, #ti_invoicetype, #ti_incidenttype, #ti_salesman').select2({
        placeholder: 'Select option',
        allowClear: false,
        dropdownParent: $('body')
    });

    // Initialize Select2 — payment method in savebar
    $('#ti_paymethod').select2({
        placeholder: 'Payment Method',
        allowClear: false,
        dropdownParent: $('body')
    });

    // Customers
    let $cust = $('#ti_customer');
    $cust.empty().append('<option value="" disabled selected>Select Customer</option>');
    $.each(ti_customers, function (_, c) {
        $cust.append('<option value="' + c.customer_id + '">' + c.customer_name + '</option>');
    });
    $cust.val(1).trigger('change');

    // Salesman
    let $emp = $('#ti_salesman');
    $emp.empty().append('<option value="1">N/A</option>');
    $.each(ti_employees, function (_, e) {
        $emp.append('<option value="' + e.id + '">' + e.first_name + '</option>');
    });
    $emp.val(1).trigger('change');

    // Payment methods
    let $pay = $('#ti_paymethod');
    $pay.empty().append('<option value="" disabled selected>Payment Method</option>');
    $.each(ti_pmethods, function (_, p) {
        $pay.append('<option value="' + p.id + '">' + p.name + '</option>');
    });
    if (ti_pmethods.length) $pay.val(ti_pmethods[0].id).trigger('change');

    // Clear field-level error highlight when user fixes the field
    $('#ti_branch').on('change', function() { if ($(this).val()) { $('#ti_branch_wrap').removeClass('ti-field-err'); tiClearErrMsg(); } });
    $('#ti_date').on('change input', function() { if ($(this).val()) { $('#ti_date_wrap').removeClass('ti-field-err'); tiClearErrMsg(); } });
    $('#ti_customer').on('change', function() { if ($(this).val()) { $('#ti_customer_wrap').removeClass('ti-field-err'); tiClearErrMsg(); } tiApplyCustomerDueDate(); });
    $('#ti_paymethod').on('change', function() { if ($(this).val()) { $('#ti_paymethod_wrap').removeClass('ti-field-err'); tiClearErrMsg(); } });

    // Invoice type default + VAT show/hide
    $('#ti_invoicetype').on('change', tiInvoiceTypeChange);
    $('#ti_invoicetype').val('cash').trigger('change');

    // Branches — same as add_invoice_form getBranchDropdown()
    if (ti_edit_id > 0) {
        tiLoadBranches(function() { tiLoadEditData(); });
    } else {
        tiLoadBranches();
    }

    // Load initial 20 products from backend
    tiLoadProducts('', '');

    // Virtual keyboard — tap to open on designated text inputs
    if ($('#ti_vk_enabled').val() === '1') {
        $(document).on('click', '.ti-vk-field', function(e) {
            e.preventDefault();
            let self = this;
            $(self).blur();
            tiVkOpen(self);
        });
    } else {
        // VK disabled — make all VK-wired fields editable with native keyboard
        $('.ti-vk-field').removeAttr('readonly').css('cursor', 'text');
    }

    // Virtual keyboard — wire to ALL touch invoice Select2 dropdowns
    // Use document-level delegation (more reliable than binding directly on the select)
    // When payment dropdown opens, slide keyboard down so dropdown is fully visible
    $('#ti_paymethod').on('select2:open', function() { tiVkClose(); });

    $(document).on('select2:open', '#ti_customer, #ti_branch,\
        #ti_invoicetype, #ti_incidenttype, #ti_salesman,\
        #ti_p_store, #ti_p_batch, #ti_p_unit', function() {
        setTimeout(function() {
            // The open dropdown is always the last .select2-container--open in the DOM
            let $s = $('.select2-container--open .select2-search__field').last();
            if (!$s.length) return;
            $s[0].setAttribute('inputmode', 'none'); // suppress native keyboard on touch
            tiVkOpen($s[0]);
        }, 150);
    });
    // Close VK for topbar dropdowns on select/close
    $('#ti_customer, #ti_branch, #ti_invoicetype, #ti_incidenttype, #ti_salesman, #ti_paymethod')
        .on('select2:select select2:close', function() {
            tiVkClose();
        });
    // For panel dropdowns — reopen keyboard on the active numpad field after selection
    $('#ti_p_store, #ti_p_batch, #ti_p_unit')
        .on('select2:select select2:close', function() {
            setTimeout(function() {
                let targetEl;
                if (tiNumpadTarget === 'price') targetEl = document.getElementById('ti_p_price');
                else if (tiNumpadTarget === 'disc') targetEl = document.getElementById('ti_p_discount');
                else targetEl = document.getElementById('ti_p_qty');
                if (targetEl) tiVkOpen(targetEl);
            }, 100);
        });

    // Esc closes the product popup
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#ti_panel_bg').hasClass('open')) {
            tiClosePanelDirect();
        }
    });

    // Enter on qty → move to price; Enter on price → move to discount
    $('#ti_p_qty').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tiSetNumpadTarget('price');
            $('#ti_p_price').focus().select();
        }
    });
    $('#ti_p_price').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tiSetNumpadTarget('disc');
            $('#ti_p_discount').focus().select();
        }
    });
    $('#ti_p_discount').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if ($('#ti_vat_row').is(':visible')) {
                tiSetNumpadTarget('vat');
                $('#ti_p_vat').focus().select();
            } else {
                tiAddToCart();
            }
        }
    });
    $('#ti_p_vat').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            tiAddToCart();
        }
    });
});

// ─── Toast alert (replaces native alert to keep fullscreen) ────
var tiToastTimer = null;
function tiAlert(msg, type) {
    type = type || 'info';
    var el = document.getElementById('ti-toast');
    el.textContent = msg;
    el.className = 'ti-toast-show ti-toast-' + type;
    clearTimeout(tiToastTimer);
    tiToastTimer = setTimeout(function() { el.className = ''; }, 3000);
}

// ─── Fullscreen toggle ─────────────────────────
function tiToggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(function() {});
        document.getElementById('ti_fs_icon').className = 'fa fa-compress';
    } else {
        document.exitFullscreen();
        document.getElementById('ti_fs_icon').className = 'fa fa-expand';
    }
}
document.addEventListener('fullscreenchange', function() {
    var icon = document.getElementById('ti_fs_icon');
    if (icon) icon.className = document.fullscreenElement ? 'fa fa-compress' : 'fa fa-expand';
});

// ─── Branches (mirrors getBranchDropdown) ──────
function tiLoadBranches(callback) {
    $.ajax({
        url: $('#ti_base_url').val() + 'store/store/getbranchbyuserid',
        type: 'POST',
        success: function (data) {
            try {
                let branches = JSON.parse(data);
                let $b = $('#ti_branch');
                $b.empty().append('<option value="" disabled selected>Select Branch</option>');
                $.each(branches, function (_, branch) {
                    $b.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (!callback && branch.default != 0) {
                        $b.val(branch.id).trigger('change');
                    }
                });
                if (callback) callback();
            } catch (e) { if (callback) callback(); }
        }
    });
}


// ─── Edit mode: load existing invoice ──────────
function tiLoadEditData() {
    $.ajax({
        url: $('#ti_base_url').val() + 'invoice/invoice/getSaleById',
        type: 'POST',
        data: { id: ti_edit_id },
        success: function (response) {
            try {
                let sales = JSON.parse(response);
                if (!sales || !sales.length) { tiAlert('Invoice not found.', 'error'); return; }
                let s0 = sales[0];

                // Header fields
                $('#ti_date').val(s0.date);
                if (s0.details) $('#ti_details').val(s0.details);

                // Branch (already populated by tiLoadBranches callback)
                $('#ti_branch').val(s0.branch).trigger('change');

                // Customer
                $('#ti_customer').val(s0.customer_id).trigger('change');

                // Salesman
                $('#ti_salesman').val(s0.employee_id || 1).trigger('change');

                // Payment method
                $('#ti_paymethod').val(s0.payment_type).trigger('change');

                // Incident type
                if (s0.incidenttype) $('#ti_incidenttype').val(s0.incidenttype).trigger('change');

                // Invoice type
                if (s0.invoicetype) $('#ti_invoicetype').val(s0.invoicetype).trigger('change');
                if (s0.due_date !== undefined) $('#ti_due_date').val(s0.due_date || 0);

                // Sale-level discount
                $('#ti_discount').val(parseFloat(s0.discount) || 0);

                // Build lookup maps for display names
                let storeMap = {};
                $.each(ti_stores, function (_, s) { storeMap[s.id] = s.name; });
                let batchMap = {};
                $.each(ti_batches, function (_, b) { batchMap[b.id] = b.batchid; });
                let unitMap = {};
                $.each(ti_units, function (_, u) { unitMap[u.unit_id] = u.unit_name; });

                // Populate cart
                tiCart = [];
                tiDeletedDetails = [];
                for (let i = 0; i < sales.length; i++) {
                    let s = sales[i];
                    let convType  = s.convertiontype || '';
                    let convRatio = parseFloat(s.conversion_ratio) || 0;

                    // avstock from getSaleById is already in display units (SQL applied conversion).
                    // Reverse to base units so avqty_num matches tiAvQtyRaw in the add flow.
                    let avStockDisplay = parseFloat(s.avstock) || 0;
                    let avStockBase    = avStockDisplay;
                    if      (convType === '*' && convRatio) avStockBase = avStockDisplay / convRatio;
                    else if (convType === '/' && convRatio) avStockBase = avStockDisplay * convRatio;
                    else if (convType === '+')              avStockBase = avStockDisplay - convRatio;
                    else if (convType === '-')              avStockBase = avStockDisplay + convRatio;

                    // Display avqty — mirror Math.floor logic used by tiAvStock()
                    let avqtyDisplay = Math.floor(avStockDisplay);

                    tiCart.push({
                        product_id:        s.product,
                        product_name:      s.product_name,
                        store_id:          s.store,
                        store_name:        storeMap[s.store] || '',
                        batch_id:          s.batch,
                        batch_name:        batchMap[s.batch] || '',
                        unit_id:           s.unit,
                        unit_name:         unitMap[s.unit] || '',
                        avqty:             avqtyDisplay,
                        avqty_num:         avStockBase,
                        qty:               parseFloat(s.quantity),
                        price:             parseFloat(s.product_rate),
                        discount:          parseFloat(s.discount2) || 0,
                        disc_val:          parseFloat(s.discount_value) || 0,
                        vat_percent:       parseFloat(s.vat_percent) || 0,
                        vat_val:           parseFloat(s.vat_value) || 0,
                        total:             parseFloat(s.total_price),
                        isstock:           s.stock,
                        conversion_id:     s.conversion_id || 0,
                        conv_type:         convType,
                        conv_ratio:        convRatio,
                        invoice_detail_id: s.invoice_detail_id
                    });
                }

                tiRenderCart();
                tiRecalcTotals();

            } catch (e) { tiAlert('Error parsing invoice data.', 'error'); }
        },
        error: function () { tiAlert('Error loading invoice. Please try again.', 'error'); }
    });
}

// ─── Product Grid ───────────────────────────────
let _tiSearchTimer = null;

function tiLoadProducts(q, category_id) {
    let $grid = $('#ti_grid');
    $grid.html('<div style="grid-column:1/-1;text-align:center;padding:30px;color:#bbb;font-size:13px;"><i class="fa fa-spinner fa-spin"></i> Loading…</div>');
    $.ajax({
        url:  $('#ti_base_url').val() + 'ti_search_products',
        type: 'POST',
        data: { q: q || '', category_id: category_id || '' },
        success: function(res) {
            var products;
            try { products = JSON.parse(res); } catch(e) { products = []; }
            tiRenderGrid(products);
        },
        error: function() {
            $grid.html('<div style="grid-column:1/-1;text-align:center;padding:30px;color:#c0392b;font-size:13px;">Failed to load products.</div>');
        }
    });
}

function tiRenderGrid(products) {
    let $grid = $('#ti_grid');
    $grid.empty();
    if (!products || products.length === 0) {
        $grid.html('<div style="grid-column:1/-1;text-align:center;padding:30px;color:#bbb;font-size:13px;">No products found</div>');
        return;
    }
    let base = $('#ti_base_url').val();
    let fragment = document.createDocumentFragment();
    $.each(products, function (_, p) {
        let tile = $('<div class="ti-tile" data-id="' + p.id + '"></div>');
        if (p.product_image) {
            tile.append('<img src="' + base + p.product_image + '" class="ti-tile-img" alt="">');
        } else {
            tile.append('<div class="ti-tile-icon"><i class="fa fa-cube"></i></div>');
        }
        tile.append('<div class="ti-tile-name">' + p.product_name + '</div>');
        tile.append('<div class="ti-tile-price">' + p.product_id + '</div>');
        tile.on('click', function () { tiOpenPanel(p); });
        fragment.appendChild(tile[0]);
    });
    $grid[0].appendChild(fragment);
}

function tiOnCategoryChange() {
    var category_id = $('#ti_category').val();
    var q           = $('#ti_search').val().trim();
    tiLoadProducts(q, category_id);
}

function tiOnSearchInput(val) {
    clearTimeout(_tiSearchTimer);
    _tiSearchTimer = setTimeout(function() {
        var category_id = $('#ti_category').val();
        tiLoadProducts(val.trim(), category_id);
    }, 300);
}

// ─── Barcode / Product-ID lookup ───────────────
$('#ti_barcode').on('keydown', function(e) {
    if (e.key === 'Enter' || e.keyCode === 13) {
        var val = $(this).val().trim();
        if (!val) return;
        $.ajax({
            url:  $('#ti_base_url').val() + 'ti_search_products',
            type: 'POST',
            data: { q: val, category_id: '' },
            dataType: 'json',
            success: function(results) {
                $('#ti_barcode').val('');
                if (!results || !results.length) {
                    tiAlert('No product found for: ' + val, 'error');
                    return;
                }
                // Prefer exact product_id match, fallback to first result
                var match = null;
                for (var i = 0; i < results.length; i++) {
                    if (String(results[i].product_id).toLowerCase() === val.toLowerCase()) {
                        match = results[i]; break;
                    }
                }
                if (!match) match = results[0];
                tiOpenPanel(match);
            },
            error: function() {
                $('#ti_barcode').val('');
                tiAlert('Barcode lookup failed', 'error');
            }
        });
    }
});

// ─── Slide-up Panel ────────────────────────────
// Mirrors product_search(item, "product") from add_invoice_form.php
function tiOpenPanel(product, cartIndex) {
    // Cart items use product_id; product tiles use id — normalise to id
    if (!product.id && product.product_id) product.id = product.product_id;
    tiPanelItem = product;
    tiPanelProd = null;
    tiEditIndex = (cartIndex !== undefined) ? cartIndex : null;

    // Lock price during initial load for edits so cascaded AJAXs don't overwrite saved price
    if (tiEditIndex !== null) {
        tiEditPriceLocked = true;
        setTimeout(function() { tiEditPriceLocked = false; }, 1500);
    } else {
        tiEditPriceLocked = false;
    }

    $('#ti_panel_title').text(product.product_name);
    $('#ti_p_qty').val(tiEditIndex !== null ? tiCart[tiEditIndex].qty : 1);
    $('#ti_p_price').val(tiEditIndex !== null ? parseFloat(tiCart[tiEditIndex].price).toFixed(2) : '0.00');
    $('#ti_p_discount').val(tiEditIndex !== null ? tiCart[tiEditIndex].discount : '0.00');
    $('#ti_p_vat').val(tiEditIndex !== null ? (tiCart[tiEditIndex].vat_percent || '0.00') : '0.00');
    $('#ti_p_vat_val').text('0.00');
    $('#ti_p_disc_val').text('0.00');
    $('#ti_p_avqty').text('Loading...');
    tiPanelCalc();

    // Reset numpad highlight to QTY, select text, and auto-open keyboard
    tiNumpadTarget = 'qty';
    $('#ti_p_price, #ti_p_discount').removeClass('numpad-active');
    $('#ti_p_qty').addClass('numpad-active');
    setTimeout(function() {
        let qtyEl = document.getElementById('ti_p_qty');
        qtyEl.focus();
        qtyEl.select();
        tiVkOpen(qtyEl);
    }, 100);

    // Pre-fill stores from passed array (same as getStoresDropdown)
    let $store = $('#ti_p_store');
    $store.empty().append('<option value="" disabled selected>Select Store</option>');
    $store.append('<option value="1">N/A</option>');
    $.each(ti_stores, function (_, s) {
        $store.append('<option value="' + s.id + '">' + s.name + '</option>');
    });
    $store.trigger('change');

    // Pre-fill units and batches as loading state
    $('#ti_p_batch').empty().append('<option value="">Loading...</option>').trigger('change');
    $('#ti_p_unit').empty().append('<option value="">Loading...</option>').trigger('change');

    $('#ti_panel_bg').addClass('open');
    $('#ti_panel_loading').css('display', 'flex');

    // Call getproduct exactly like product_search(item,"product") does
    $.ajax({
        url: $('#ti_base_url').val() + 'stock/stock/getproduct',
        type: 'POST',
        data: { prodid: product.id.toString() },
        success: function (response) {
            $('#ti_panel_loading').hide();
            let prod = JSON.parse(response);
            tiPanelProd = prod[0];

            // Reset sub-unit display vars for this product
            tiMconvRatio = ''; tiBaseUnitName = ''; tiSubUnitName = '';
            tiConvType   = ''; tiConvRatio    = ''; tiConvRatioId = 0;
            tiMrpPrice   = ''; tiAvQtyRaw     = 0;
            if (tiConvXhr) { tiConvXhr.abort(); tiConvXhr = null; }

            // When editing use the cart item's saved store, otherwise product default
            let activeStore = (tiEditIndex !== null) ? tiCart[tiEditIndex].store_id : tiPanelProd.store;
            $store.val(activeStore).trigger('change');

            // Set price from product default only when not locked (new items or after initial edit load)
            if (!tiEditPriceLocked) {
                let price = 0;
                if (tiPanelProd.defaultsaleprice === 'fixedprice' || tiPanelProd.defaultsaleprice === 'mrp') {
                    price = parseFloat(tiPanelProd.price) || 0;
                }
                $('#ti_p_price').val(price.toFixed(2));
            }
            tiPanelCalc();

            // Load batches — skipAvStock=true; avStock called after subunit primary loads
            let editBatch = tiEditIndex !== null ? tiCart[tiEditIndex].batch_id : null;
            tiLoadPanelBatches(tiPanelProd, editBatch, activeStore, true);

            // Load units — mirrors getActiveSubUnit
            let editUnit = tiEditIndex !== null ? tiCart[tiEditIndex].unit_id : null;
            tiLoadPanelUnits(product.id, tiPanelProd.unit, editUnit);

            // Mirrors getproductSubUnitPrimary call in add_invoice_form.php:
            // fetches the primary sub-unit ratio so avStock can show split display (e.g. "1Drum 90Ltr")
            $.ajax({
                url: $('#ti_base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                type: 'POST',
                data: { prodid: product.id.toString() },
                success: function (res2) {
                    if (res2 && res2 !== 'null') {
                        try {
                            let p2 = JSON.parse(res2);
                            tiMconvRatio   = p2[0].conversion_ratio;
                            tiBaseUnitName = tiPanelProd.unit_name || '';
                            tiSubUnitName  = p2[0].unit_name || '';
                        } catch (e) {
                            tiMconvRatio = ''; tiBaseUnitName = ''; tiSubUnitName = '';
                        }
                    } else {
                        tiMconvRatio = ''; tiBaseUnitName = ''; tiSubUnitName = '';
                    }
                    tiAvStock(); // now mconv vars are set — call avStock here
                },
                error: function () {
                    tiMconvRatio = ''; tiBaseUnitName = ''; tiSubUnitName = '';
                    tiAvStock();
                }
            });

            tiPanelCalc();
        },
        error: function () {
            $('#ti_panel_loading').hide();
            $('#ti_p_avqty').text('Error');
        }
    });
}

// Mirrors getBatchDropdown — loads batches, auto-selects Default (id=1)
// skipAvStock: true on initial open (avStock called separately after subunit primary loads)
function tiLoadPanelBatches(prod, selectedBatch, storeId, skipAvStock) {
    let $b = $('#ti_p_batch');
    $b.empty().append('<option value="">Loading...</option>').trigger('change');
    $.ajax({
        url: $('#ti_base_url').val() + 'stock/stock/getBatchInStockByProductAndBatchtype',
        type: 'POST',
        data: { product: tiPanelItem.id, batchtype: prod.batchtype, id: '', nstock: prod.stock },
        success: function (res) {
            $b.empty().append('<option value="" disabled selected>Select Batch</option>');
            if (res && res !== 'not') {
                try {
                    let batches = JSON.parse(res);
                    $.each(batches, function (_, bat) {
                        $b.append('<option value="' + bat.id + '">' + bat.batchid + '</option>');
                    });
                } catch (e) {}
            }
            // Restore edit selection OR auto-select Default batch (id=1)
            if (selectedBatch) {
                $b.val(selectedBatch).trigger('change');
            } else {
                $b.val(1); // Default batch — mirrors getBatchDropdown(..., value=1, ...)
                if (!$b.val()) {
                    // id=1 not in list, pick first real option
                    $b.find('option:not([disabled])').first().prop('selected', true);
                }
                $b.trigger('change');
            }
            if (!skipAvStock) tiAvStock(); // recalculate avqty (skip on initial open)
        }
    });
}

// Mirrors getActiveSubUnit — loads units, auto-selects product's default unit
function tiLoadPanelUnits(productId, defaultUnit, selectedUnit) {
    let $u = $('#ti_p_unit');
    $u.empty().append('<option value="">Loading...</option>').trigger('change');
    $.ajax({
        url: $('#ti_base_url').val() + 'product/product/active_subunitsbyproductId',
        type: 'POST',
        data: { product_id: productId },
        success: function (res) {
            try {
                let units = JSON.parse(res);
                $u.empty();
                // First option: primary unit (like add_invoice_form appends datas[0] separately)
                if (units.length) {
                    $u.append('<option value="' + units[0].unit + '">' + units[0].name2 + '</option>');
                }
                $.each(units, function (_, u) {
                    if (u.unit_id) {
                        $u.append('<option value="' + u.unit_id + '">' + u.unit_name + '</option>');
                    }
                });
                // Restore edit selection OR auto-select product's default unit
                if (selectedUnit) {
                    $u.val(selectedUnit).trigger('change');
                } else if (defaultUnit) {
                    $u.val(defaultUnit).trigger('change');
                }
            } catch (e) {
                $u.empty().append('<option value="">— No unit —</option>');
                $u.trigger('change');
            }
        }
    });
}

// Mirrors avStock() — calls avg_avstock then formats with full split display logic
function tiAvStock() {
    if (!tiPanelItem) return;
    let batchId = $('#ti_p_batch').val();
    let storeId = $('#ti_p_store').val() || (tiPanelProd ? tiPanelProd.store : '');
    if (!batchId) { $('#ti_p_avqty').text('—'); return; }
    $.ajax({
        url: $('#ti_base_url').val() + 'stock/stock/avg_avstock',
        type: 'POST',
        data: { prodid: tiPanelItem.id, storeid: storeId, batch: batchId },
        success: function (response) {
            try {
                let stock  = JSON.parse(response);
                let avgqty = stock[0] ? parseFloat(stock[0].avgqty) || 0 : 0;
                tiAvQtyRaw = avgqty;
                let unitText = $('#ti_p_unit option:selected').text();
                let $el = $('#ti_p_avqty');

                // Mirror avStock() conversion logic from add_invoice_form.php
                if (tiConvType === '*') {
                    $el.text(Math.floor(avgqty * parseFloat(tiConvRatio)).toLocaleString() + ' ' + unitText);
                } else if (tiConvType === '/') {
                    $el.text(Math.floor(avgqty / parseFloat(tiConvRatio)).toLocaleString() + ' ' + unitText);
                } else if (tiConvType === '+') {
                    $el.text(Math.floor(avgqty + parseFloat(tiConvRatio)).toLocaleString() + ' ' + unitText);
                } else if (tiConvType === '-') {
                    $el.text(Math.floor(avgqty - parseFloat(tiConvRatio)).toLocaleString() + ' ' + unitText);
                } else if (tiMconvRatio !== '') {
                    // Split display: e.g. "1Drum 90Ltr" (mirrors mconversion_ratio branch in avStock)
                    let mconv      = parseFloat(tiMconvRatio);
                    let totalcount = Math.floor(mconv * avgqty / mconv);
                    let subcount   = Math.floor(mconv * avgqty % mconv);
                    $el.text(totalcount + tiBaseUnitName + ' ' + subcount + tiSubUnitName);
                } else {
                    $el.text(Math.floor(avgqty).toLocaleString() + ' ' + unitText);
                }
            } catch (e) {
                $('#ti_p_avqty').text('—');
            }
        },
        error: function () { $('#ti_p_avqty').text('—'); }
    });
}

// On store change — mirrors product_search(item,"store")
function tiPanelStoreChange() {
    if (!tiPanelProd) return;
    tiLoadPanelBatches(tiPanelProd, null, $('#ti_p_store').val());
}

// On batch change — mirrors product_search(item,"batch")
function tiPanelBatchChange() {
    tiAvStock();
    // Fetch batch price for MRP products — skip only during initial edit lock
    if (tiPanelProd && tiPanelProd.defaultsaleprice === 'mrp') {
        $.ajax({
            url: $('#ti_base_url').val() + 'stock/stock/getBatchPrice',
            type: 'POST',
            data: { product: tiPanelItem.id, batch: $('#ti_p_batch').val() },
            success: function (res) {
                if (res) {
                    try {
                        let d = JSON.parse(res);
                        if (d[0] && d[0].mrp) {
                            tiMrpPrice = d[0].mrp;
                            if (!tiEditPriceLocked) $('#ti_p_price').val(parseFloat(d[0].mrp).toFixed(2));
                        }
                    } catch (e) {}
                }
                tiPanelCalc();
            }
        });
    } else {
        tiPanelCalc();
    }
}

// On unit change — mirrors product_search(item,"unit") → convertion()
function tiPanelUnitChange() {
    if (!tiPanelItem || !tiPanelProd) return;
    let unitId   = $('#ti_p_unit').val();
    if (tiConvXhr) { tiConvXhr.abort(); tiConvXhr = null; }
    tiConvXhr = $.ajax({
        url: $('#ti_base_url').val() + 'stock/stock/conversion',
        type: 'POST',
        data: { product_id: tiPanelItem.id, unit: unitId },
        success: function (res) {
            if (res && res !== 'not') {
                try {
                    let conv      = JSON.parse(res);
                    tiConvType    = conv[0].convertiontype;
                    tiConvRatio   = conv[0].conversion_ratio;
                    tiConvRatioId = conv[0].conversionratio_id || 0;
                    // Update price for sub-unit when not locked
                    if (!tiEditPriceLocked) {
                        if (tiPanelProd.defaultsaleprice === 'fixedprice') {
                            $('#ti_p_price').val(parseFloat(conv[0].subsell_price || 0).toFixed(2));
                        }
                        if (tiPanelProd.defaultsaleprice === 'mrp') {
                            if (tiMrpPrice) {
                                var _ratio = parseFloat(conv[0].conversion_ratio) || 1;
                                $('#ti_p_price').val((parseFloat(tiMrpPrice) / _ratio).toFixed(2));
                            } else {
                                $('#ti_p_price').val(parseFloat(conv[0].subsell_price || 0).toFixed(2));
                            }
                        }
                    }
                } catch (e) { tiConvType = ''; tiConvRatio = ''; }
            } else {
                tiConvType    = '';
                tiConvRatio   = '';
                tiConvRatioId = 0;
                // No conversion — revert to master price when not locked
                if (!tiEditPriceLocked) {
                    if (tiPanelProd.defaultsaleprice === 'fixedprice') {
                        $('#ti_p_price').val(parseFloat(tiPanelProd.price || 0).toFixed(2));
                    }
                    if (tiPanelProd.defaultsaleprice === 'mrp') {
                        if (tiMrpPrice) {
                            $('#ti_p_price').val(parseFloat(tiMrpPrice).toFixed(2));
                        } else {
                            $('#ti_p_price').val(parseFloat(tiPanelProd.price || 0).toFixed(2));
                        }
                    }
                }
            }
            tiAvStock();
            tiPanelCalc();
        },
        error: function (xhr) { if (xhr.statusText !== 'abort') { tiConvType = ''; tiConvRatio = ''; tiConvRatioId = 0; tiAvStock(); } },
        complete: function () { tiConvXhr = null; }
    });
}

function tiClosePanel(e) {
    if (e.target === document.getElementById('ti_panel_bg')) tiClosePanelDirect();
}
function tiClosePanelDirect() {
    $('#ti_panel_bg').removeClass('open');
    tiPanelItem = null;
    tiEditIndex = null;
    tiVkClose();
}

// ─── Numpad ─────────────────────────────────────
let tiNumpadTarget = 'qty'; // 'qty' | 'price' | 'disc'

function tiSetNumpadTarget(target) {
    tiNumpadTarget = target;
    $('#ti_p_qty, #ti_p_price, #ti_p_discount, #ti_p_vat').removeClass('numpad-active');
    let targetEl;
    if (target === 'qty')   { $('#ti_p_qty').addClass('numpad-active');      targetEl = document.getElementById('ti_p_qty'); }
    if (target === 'price') { $('#ti_p_price').addClass('numpad-active');    targetEl = document.getElementById('ti_p_price'); }
    if (target === 'disc')  { $('#ti_p_discount').addClass('numpad-active'); targetEl = document.getElementById('ti_p_discount'); }
    if (target === 'vat')   { $('#ti_p_vat').addClass('numpad-active');      targetEl = document.getElementById('ti_p_vat'); }
    if (targetEl) {
        targetEl.select();
        tiVkReplaceNext = true;
        tiVkOpen(targetEl);
    }
}

function tiNumericOnly(el) {
    let pos = el.selectionStart;
    let cleaned = el.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
    if (el.value !== cleaned) { el.value = cleaned; el.selectionStart = el.selectionEnd = pos - 1; }
}

function tiQtyStep(delta) {
    let $q  = $('#ti_p_qty');
    let val = parseFloat($q.val()) || 0;
    val     = Math.max(0, val + delta);
    $q.val(val % 1 === 0 ? val : val.toFixed(2));
    tiPanelCalc();
}

function tiPanelCalc() {
    let qty    = parseFloat($('#ti_p_qty').val())      || 0;
    let price  = parseFloat($('#ti_p_price').val())    || 0;
    let disc   = parseFloat($('#ti_p_discount').val()) || 0;
    let vatPct = parseFloat($('#ti_p_vat').val())      || 0;
    let sub    = qty * price;
    let dval   = sub * disc / 100;
    let afterDisc = sub - dval;
    let vatVal = afterDisc * vatPct / 100;
    let total  = afterDisc + vatVal;
    $('#ti_p_disc_val').text(dval.toFixed(2));
    $('#ti_p_vat_val').text(vatVal.toFixed(2));
    $('#ti_p_linetotal').text(total.toFixed(2));
}

// ─── Add to Cart ────────────────────────────────
function tiAddToCart() {
    if (!tiPanelItem) return;
    let storeId   = $('#ti_p_store').val();
    let storeName = $('#ti_p_store option:selected').text();
    let batchId   = $('#ti_p_batch').val();
    let batchName = $('#ti_p_batch option:selected').text();
    let unitId    = $('#ti_p_unit').val();
    let unitName  = $('#ti_p_unit option:selected').text();
    let qty       = parseFloat($('#ti_p_qty').val())      || 0;
    let price     = parseFloat($('#ti_p_price').val())    || 0;
    let disc      = parseFloat($('#ti_p_discount').val()) || 0;
    let vatPct    = parseFloat($('#ti_p_vat').val())      || 0;
    let avqty     = $('#ti_p_avqty').text();

    if (!storeId)  { tiAlert('Please select a Store.', 'error'); return; }
    if (!batchId)  { tiAlert('Please select a Batch.', 'error'); return; }
    if (!unitId)   { tiAlert('Please select a Unit.', 'error'); return; }
    if (qty <= 0)  { tiAlert('Qty must be greater than 0.', 'error'); return; }

    let sub       = qty * price;
    let dval      = sub * disc / 100;
    let afterDisc = sub - dval;
    let vatVal    = afterDisc * vatPct / 100;
    let total     = afterDisc + vatVal;

    let cartItem = {
        product_id: tiPanelItem.id,
        product_name: tiPanelItem.product_name,
        store_id: storeId, store_name: storeName,
        batch_id: batchId, batch_name: batchName,
        unit_id: unitId,   unit_name: unitName,
        avqty: avqty, avqty_num: tiAvQtyRaw,
        qty: qty, price: price, discount: disc,
        disc_val: dval, vat_percent: vatPct, vat_val: vatVal, total: total,
        isstock: tiPanelProd ? tiPanelProd.stock : 1,
        conversion_id: tiConvRatioId,
        conv_type: tiConvType,
        conv_ratio: parseFloat(tiConvRatio) || 0
    };

    if (tiEditIndex !== null) {
        // Preserve invoice_detail_id from the original item so update_sale UPDATEs (not INSERTs)
        cartItem.invoice_detail_id = tiCart[tiEditIndex].invoice_detail_id || 0;
        tiCart[tiEditIndex] = cartItem;
    } else {
        tiCart.push(cartItem);
    }

    tiClosePanelDirect();
    tiRenderCart();
    tiRecalcTotals();
    $('#ti_cart').removeClass('ti-cart-err');
}

// ─── Cart Render ────────────────────────────────
function tiRenderCart() {
    let $cart  = $('#ti_cart');
    let $empty = $('#ti_empty_msg');

    $cart.find('.ti-cart-item').remove();

    if (tiCart.length === 0) {
        $empty.show();
        return;
    }
    $empty.hide();

    $.each(tiCart, function (i, item) {
        let html =
            '<div class="ti-cart-item" id="ti_ci_' + i + '">' +
                '<div class="ti-ci-top">' +
                    '<div class="ti-ci-name">' + item.product_name + '</div>' +
                    '<button class="ti-ci-delete" onclick="tiRemoveItem(' + i + ')"><i class="fa fa-trash"></i></button>' +
                '</div>' +
                '<div class="ti-ci-meta">' +
                    '<span class="ti-ci-tag">&#128230; ' + item.batch_name + '</span>' +
                    '<span class="ti-ci-tag">&#127970; ' + item.store_name + '</span>' +
                    '<span class="ti-ci-tag">&#128230; ' + item.unit_name + '</span>' +
                    '<span class="ti-ci-avqty">Av: ' + item.avqty + '</span>' +
                '</div>' +
                '<div class="ti-ci-bottom">' +
                    '<div class="ti-ci-left">' +
                        '<div class="ti-qty-control">' +
                            '<button class="ti-qty-btn" onclick="tiCartQtyStep(' + i + ',-1)">&#8722;</button>' +
                            '<input type="text" class="ti-qty-input" id="ti_ci_qty_' + i + '" value="' + item.qty + '" onchange="tiCartQtyChange(' + i + ',this.value)">' +
                            '<button class="ti-qty-btn" onclick="tiCartQtyStep(' + i + ',1)">&#43;</button>' +
                        '</div>' +
                        '<button class="ti-ci-edit-btn" onclick="tiEditItem(' + i + ')">&#9998; Edit</button>' +
                    '</div>' +
                    '<div class="ti-ci-pricebox">' +
                        '<div class="ti-ci-prow">' +
                            '<span>Price</span>' +
                            '<span>' + parseFloat(item.price).toFixed(2) + ' &times; ' + item.qty + '</span>' +
                        '</div>' +
                        (item.disc_val > 0 ?
                            '<div class="ti-ci-prow ti-row-disc">' +
                                '<span>Discount (' + item.discount + '%)</span>' +
                                '<span>-' + parseFloat(item.disc_val).toFixed(2) + '</span>' +
                            '</div>' : '') +
                        (item.vat_val > 0 ?
                            '<div class="ti-ci-prow ti-row-vat">' +
                                '<span>VAT (' + item.vat_percent + '%)</span>' +
                                '<span>+' + parseFloat(item.vat_val).toFixed(2) + '</span>' +
                            '</div>' : '') +
                        '<div class="ti-ci-prow ti-row-total">' +
                            '<span>Total</span>' +
                            '<span id="ti_ci_total_' + i + '">' + parseFloat(item.total).toFixed(2) + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';
        $cart.append(html);
    });
}

function tiRemoveItem(i) {
    let item = tiCart[i];
    if (item && item.invoice_detail_id && item.invoice_detail_id > 0) {
        tiDeletedDetails.push(item.invoice_detail_id);
    }
    tiCart.splice(i, 1);
    tiRenderCart();
    tiRecalcTotals();
}

function tiEditItem(i) {
    tiOpenPanel(tiCart[i], i);
}

function tiCartQtyStep(i, delta) {
    let item = tiCart[i];
    let qty  = Math.max(0, parseFloat(item.qty) + delta);
    tiCartQtyChange(i, qty);
}

function tiCartQtyChange(i, val) {
    let qty  = parseFloat(val) || 0;
    let item = tiCart[i];
    item.qty = qty;
    let sub       = qty * item.price;
    item.disc_val = sub * (item.discount || 0) / 100;
    let afterDisc = sub - item.disc_val;
    item.vat_val  = afterDisc * (item.vat_percent || 0) / 100;
    item.total    = afterDisc + item.vat_val;
    tiRenderCart();
    tiRecalcTotals();
}

// ─── Totals ─────────────────────────────────────
function tiRecalcTotals() {
    let subtotal  = tiCart.reduce(function (sum, item) { return sum + (item.qty * item.price); }, 0);
    let discTotal = tiCart.reduce(function (sum, item) { return sum + (item.disc_val || 0); }, 0);
    let vatTotal  = tiCart.reduce(function (sum, item) { return sum + (item.vat_val || 0); }, 0);
    let total     = tiCart.reduce(function (sum, item) { return sum + item.total; }, 0);
    let saleDisc  = parseFloat($('#ti_discount').val()) || 0;
    let grand     = total - saleDisc;

    $('#ti_subtotal').text(subtotal.toFixed(2));
    $('#ti_disc_total').text(discTotal.toFixed(2));
    $('#ti_disc_total_row').toggle(discTotal > 0);
    $('#ti_vat_total').text(vatTotal.toFixed(2));
    $('#ti_total').text(total.toFixed(2));
    $('#ti_grand').text(grand.toFixed(2));
}

function tiInvoiceTypeChange() {
    let type  = $('#ti_invoicetype').val();
    let isVat = (type === 'cash_vat' || type === 'credit_vat');

    // Clear cart when invoice type changes (if cart has items)
    if (tiCart.length > 0) {
        if (!confirm('Changing invoice type will clear the cart. Continue?')) {
            // Revert dropdown to previous type
            $('#ti_invoicetype').val(tiLastInvoiceType || 'cash').trigger('change');
            return;
        }
        tiCart = [];
        tiRenderCart();
        tiRecalcTotals();
    }
    tiLastInvoiceType = type;

    $('#ti_vat_row').toggle(isVat);
    $('#ti_p_vat_row_display').css('display', isVat ? 'flex' : 'none');
    $('#ti_vat_total_row').toggle(isVat);
    if (!isVat) {
        $('#ti_p_vat').val('0.00');
        $('#ti_p_vat_val').text('0.00');
        tiPanelCalc();
    }

    var isCredit = (type === 'credit' || type === 'credit_vat');
    $('#ti_due_date_field').toggle(isCredit);
    if (isCredit) {
        tiApplyCustomerDueDate();
    } else {
        $('#ti_due_date').val(0);
    }

    tiRecalcTotals();
}

function tiApplyCustomerDueDate() {
    var invType = $('#ti_invoicetype').val();
    if (invType !== 'credit' && invType !== 'credit_vat') return;
    var custId = $('#ti_customer').val();
    var cust = (typeof ti_customers !== 'undefined') && ti_customers.find(function(c) { return String(c.customer_id) === String(custId); });
    if (cust && cust.due_date !== undefined) {
        $('#ti_due_date').val(cust.due_date || 0);
    }
}

// ─── Save Invoice ───────────────────────────────
function tiToastShow(errors) {
    tiAlert(errors.join(' | '), 'error');
}
function tiClearErrMsg() {}

function tiSave() {
    // Clear previous errors
    $('.ti-field-err').removeClass('ti-field-err');

    let errors = [];

    if (tiCart.length === 0) {
        errors.push('Cart is empty — add at least one product.');
        $('#ti_cart').addClass('ti-cart-err');
    }

    if (!$('#ti_branch').val()) {
        errors.push('Branch is required.');
        $('#ti_branch_wrap').addClass('ti-field-err');
    }
    if (!$('#ti_date').val()) {
        errors.push('Date is required.');
        $('#ti_date_wrap').addClass('ti-field-err');
    }
    if (!$('#ti_customer').val()) {
        errors.push('Customer is required.');
        $('#ti_customer_wrap').addClass('ti-field-err');
    }
    if (!$('#ti_paymethod').val()) {
        errors.push('Payment Method is required.');
        $('#ti_paymethod_wrap').addClass('ti-field-err');
    }

    if (errors.length > 0) {
        tiToastShow(errors);
        return;
    }

    let customer     = $('#ti_customer').val();
    let paymethod    = $('#ti_paymethod').val();
    let incidenttype = $('#ti_incidenttype').val();
    let type2        = ti_usertype == 3 ? 'B' : 'A';
    let invoicetype  = $('#ti_invoicetype').val();
    let saleDisc     = parseFloat($('#ti_discount').val()) || 0;

    let arrItem = [];
    let totalAfterItemDisc = 0;
    let totalDiscAmt       = 0;
    let totalVatAmt        = 0;

    tiCart.forEach(function(item) {
        let afterDisc = item.qty * item.price - item.disc_val;
        totalAfterItemDisc += afterDisc;
        totalDiscAmt       += item.disc_val || 0;
        totalVatAmt        += item.vat_val  || 0;

        // Convert entered sub-unit qty back to base units for stock deduction
        // Mirrors add_invoice_form.php (inverse of avStock display formula)
        let stockQty = item.qty;
        if (item.conv_type === '+') stockQty = item.qty - item.conv_ratio;
        else if (item.conv_type === '-') stockQty = item.qty + item.conv_ratio;
        else if (item.conv_type === '*') stockQty = item.qty / item.conv_ratio;
        else if (item.conv_type === '/') stockQty = item.qty * item.conv_ratio;

        arrItem.push({
            product:        item.product_id,
            product_name:   item.product_name,
            store:          item.store_id,
            quantity:       stockQty,
            product_rate:   item.price,
            batch:          item.batch_id,
            discount:       item.discount,
            discount_value: item.disc_val,
            vat_percent:    item.vat_percent,
            vat_value:      item.vat_val,
            total_price:    afterDisc,
            total_discount: item.disc_val,
            all_discount:   item.disc_val,
            unit:           item.unit_id,
            group:          0,
            parent:         0,
            invoicegroup:   0,
            conversionid:   item.conversion_id || 0,
            invoicedetail:  item.invoice_detail_id || 0,
            isstock:        item.isstock,
            aqty:  '-'+item.qty + ti_units.find(unit => unit.unit_id == item.unit_id).unit_name,

        });
    });

    let grandTotal = totalAfterItemDisc - saleDisc + totalVatAmt;
    let isEdit     = ti_edit_id > 0;
    let saveUrl    = isEdit ? 'invoice/invoice/update_sale' : 'invoice/invoice/save_sale';

    let postData = {
        items:                  arrItem,
        grouparrItem:           [],
        type2:                  type2,
        discount:               saleDisc,
        total_discount_ammount: totalDiscAmt.toFixed(2),
        total_vat_amnt:         totalVatAmt.toFixed(2),
        grandTotal:             grandTotal.toFixed(2),
        total:                  totalAfterItemDisc.toFixed(2),
        date:                   $('#ti_date').val(),
        details:                $('#ti_details').val(),
        customer_id:            customer,
        payment_type:           paymethod,
        payment:                $('#ti_paymethod option:selected').text(),
        employee_id:            $('#ti_salesman').val(),
        incidenttype:           incidenttype,
        branch:                 $('#ti_branch').val(),
        invoicetype:            invoicetype,
        due_date:               parseInt($('#ti_due_date').val()) || 0,
        sales_order_no:         0
    };
    if (isEdit) {
        postData.id                  = ti_edit_id;
        postData.deletedsaleDetails  = tiDeletedDetails;
    }

    $.ajax({
        url: $('#ti_base_url').val() + saveUrl,
        type: 'POST',
        data: postData,
        success: function(response) {
            try {
                let datas = JSON.parse(response);
                if (datas.success === false) {
                    tiAlert('Error: ' + (datas.error || 'Save failed. Please try again.'), 'error');
                    return;
                }
                tiCart = [];
                tiDeletedDetails = [];
                tiRenderCart();
                tiRecalcTotals();
                $('#ti_discount').val('0.00');
                tiAlert(isEdit ? 'Invoice updated successfully!' : 'Invoice saved successfully!', 'success');
                tiPusherNotify(isEdit ? 'updated' : 'created');
                if (datas.details) { printRawHtmlTI(datas.details, datas.invoiceno); }
            } catch(e) {
                tiAlert('Saved but could not parse response.', 'info');
            }
        },
        error: function() { tiAlert('Error saving invoice. Please try again.', 'error'); }
    });
}

function tiPusherNotify(action) {
    fetch('https://fexten.com/fexten_cloud_hub/modules/pusher/trigger.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ channel: 'notifications', event: 'record.saved', data: { module: 'Sale', action: action } })
    }).catch(function() {});
}

function pdfDateStamp() {
    var d = new Date();
    var dd = String(d.getDate()).padStart(2, '0');
    var mm = String(d.getMonth() + 1).padStart(2, '0');
    return dd + mm + d.getFullYear();
}

function printRawHtmlTI(view, voucherNo) {
    if (voucherNo) {
        document.title = 'salesinvoice_' + voucherNo + '_' + pdfDateStamp();
    }
    $(view).print({
        deferred: $.Deferred().done(function() {
            window.location.reload();
        })
    });
}

// ─── Virtual Keyboard ─────────────────────────
let tiVkTarget       = null;
let tiVkShift        = false;
let tiVkCaps         = false;
let tiVkReplaceNext  = false; // clear field on first key after selecting

// Letter keyboard layout (left panel) — numbers moved to dedicated numpad
const TI_VK_LAYOUT = [
    ['q','w','e','r','t','y','u','i','o','p'],
    ['a','s','d','f','g','h','j','k','l'],
    ['⇧','z','x','c','v','b','n','m'],
    ['.','@','-',' ']
];
// Numpad layout (right panel)
const TI_VK_NUMPAD = [
    ['7','8','9'],
    ['4','5','6'],
    ['1','2','3'],
    ['.','0','⌫'],
];

function tiVkOpen(el) {
    if ($('#ti_vk_enabled').val() !== '1') return;
    tiVkTarget      = el;
    tiVkShift       = false;
    tiVkCaps        = false;
    tiVkReplaceNext = false;
    tiVkRender();
    tiVkRefreshPreview();
    document.getElementById('ti_vk').classList.add('open');
    if ($('#ti_panel_bg').hasClass('open')) {
        $('#ti_panel_bg').addClass('ti-vk-up');
    }
}

function tiVkClose() {
    document.getElementById('ti_vk').classList.remove('open');
    // Restore panel position
    $('#ti_panel_bg').removeClass('ti-vk-up');
    // Don't re-trigger events on Select2 search inputs — it causes the dropdown to reopen
    if (tiVkTarget && !$(tiVkTarget).hasClass('select2-search__field')) {
        $(tiVkTarget).trigger('input').trigger('change');
    }
    tiVkTarget = null;
}

function tiVkRefreshPreview() {
    let val = tiVkTarget ? ($(tiVkTarget).val() || '') : '';
    $('#ti_vk_preview').text(val || ' ');
}

function tiVkRender() {
    // ── Letter panel (left) ──
    let $wrap = $('#ti_vk_keys').empty();
    let upper = tiVkShift || tiVkCaps;
    TI_VK_LAYOUT.forEach(function(row) {
        let $row = $('<div class="ti-vk-row"></div>');
        row.forEach(function(k) {
            let label = k;
            let cls   = 'ti-vk-key';
            if (k === '⇧')         { cls += ' fn' + (upper ? ' shift-on' : ''); }
            else if (k === ' ')    { cls += ' space'; label = 'space'; }
            else if (upper && /^[a-z]$/.test(k)) { label = k.toUpperCase(); }
            let $k = $('<button></button>').addClass(cls).text(label);
            $k.on('mousedown touchstart', function(e) {
                e.preventDefault(); e.stopPropagation(); tiVkPress(k);
            });
            $row.append($k);
        });
        $wrap.append($row);
    });

    // ── Numpad (right) ──
    let $np = $('#ti_vk_numpad').empty();
    TI_VK_NUMPAD.forEach(function(row) {
        let $row = $('<div class="ti-vk-np-row"></div>');
        row.forEach(function(k) {
            let cls = 'ti-vk-np-key';
            if (k === '⌫')    cls += ' np-erase';
            else if (k === 'Done') cls += ' np-done';
            else if (k === '.') cls += ' np-dot';
            let label = k === '⌫' ? '⌫' : k;
            let $k = $('<button></button>').addClass(cls).text(label);
            $k.on('mousedown touchstart', function(e) {
                e.preventDefault(); e.stopPropagation(); tiVkPress(k);
            });
            $row.append($k);
        });
        $np.append($row);
    });
}

function tiVkPress(key) {
    if (!tiVkTarget) return;
    let $el  = $(tiVkTarget);
    let upper = tiVkShift || tiVkCaps;

    // On first key after field selection, replace existing value
    if (tiVkReplaceNext && key !== '⇧' && key !== 'Done') {
        tiVkReplaceNext = false;
        if (key === '⌫') { $el.val('').trigger('input'); tiVkRefreshPreview(); return; }
        $el.val('');
    }
    let val = $el.val() || '';

    if (key === '⌫') {
        let el = tiVkTarget;
        let s = el.selectionStart, e = el.selectionEnd;
        if (s !== e) {
            val = val.slice(0, s) + val.slice(e);
        } else {
            val = val.slice(0, -1);
        }
    } else if (key === '⇧') {
        if (tiVkCaps && tiVkShift)  { tiVkCaps = false; tiVkShift = false; }
        else if (tiVkShift)         { tiVkCaps = true; }
        else                        { tiVkShift = true; }
        tiVkRender(); return;
    } else if (key === 'Done') {
        tiVkClose(); return;
    } else {
        let ch = (key === ' ') ? ' ' : ((upper && /^[a-z]$/.test(key)) ? key.toUpperCase() : key);
        let el = tiVkTarget;
        let s = el.selectionStart, e = el.selectionEnd;
        if (s !== e) {
            val = val.slice(0, s) + ch + val.slice(e);
        } else {
            val += ch;
        }
        if (tiVkShift && !tiVkCaps) { tiVkShift = false; tiVkRender(); }
    }

    $el.val(val).trigger('input').trigger('keyup');
    tiVkRefreshPreview();
}

// ─── Save Customer ──────────────────────────────
function tiSaveCustomer() {
    let name  = $('#ti_cust_name').val().trim();
    let phone = $('#ti_cust_phone').val().trim();
    if (!name || !phone) { tiAlert('Name and phone are required.', 'error'); return; }
    $.ajax({
        url: $('#ti_base_url').val() + 'invoice/invoice/save_customer',
        type: 'POST',
        data: { customer_name: name, customer_phone: phone },
        success: function (res) {
            try {
                let r = JSON.parse(res);
                if (r.inserted_id) {
                    $('#ti_customer').append('<option value="' + r.inserted_id + '">' + name + '</option>');
                    if ($('#ti_customer').data('select2')) {
                        $('#ti_customer').val(r.inserted_id).trigger('change');
                    } else {
                        $('#ti_customer').val(r.inserted_id);
                    }
                    tiAlert('Customer added successfully.', 'success');
                }
            } catch (e) {}
            document.getElementById('ti_cust_modal').classList.remove('open');
            $('#ti_cust_name').val('');
            $('#ti_cust_phone').val('');
        },
        error: function() { tiAlert('Failed to save customer.', 'error'); }
    });
}
</script>

<!-- ═══ Add Product Modal ═══ -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align:center; font-weight:600;">Add New Product</h4>
            </div>
            <div class="modal-body">
                <div id="ap_loading" style="text-align:center; padding:20px; display:none;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div id="ap_form_body">
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Barcode / QR Code</label>
                            <input type="text" id="ap_barcode" class="form-control" placeholder="Leave blank to auto-generate">
                        </div></div>
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Product Name <span class="text-danger">*</span></label>
                            <input type="text" id="ap_product_name" class="form-control" placeholder="Enter product name">
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Category <span class="text-danger">*</span></label>
                            <select id="ap_category_id" class="form-control"><option value="">Select Category</option></select>
                        </div></div>
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Product Type</label>
                            <select id="ap_product_type" class="form-control">
                                <option value="N/A" selected>N/A</option>
                                <option value="Retail Good">Retail Good</option>
                                <option value="Finished Good">Finished Good</option>
                                <option value="Ingredients">Ingredients</option>
                                <option value="Raw Material">Raw Material</option>
                                <option value="Packing Material">Packing Material</option>
                                <option value="MRO">MRO</option>
                            </select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Batch Type</label>
                            <select id="ap_batchtype" class="form-control">
                                <option value="1">Single</option><option value="2">Multiple</option><option value="3" selected>Both</option>
                            </select>
                        </div></div>
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Default Sales Price</label>
                            <select id="ap_defaultsaleprice" class="form-control">
                                <option value="fixedprice">Fixed Price</option><option value="mrp">MRP</option><option value="custom" selected>Custom</option>
                            </select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Master Stock Unit <span class="text-danger">*</span></label>
                            <select id="ap_unit" class="form-control"><option value="">Select Unit</option></select>
                        </div></div>
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Stock</label>
                            <select id="ap_stock" class="form-control"><option value="1">Enable</option><option value="0" selected>Disable</option></select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Default Store</label>
                            <select id="ap_store" class="form-control"><option value="1" selected>N/A</option></select>
                        </div></div>
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Supplier</label>
                            <select id="ap_supplier_id" class="form-control"><option value="">Select Supplier</option></select>
                        </div></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6"><div class="form-group">
                            <label style="font-weight:700;">Fixed Purchase Price <span class="text-danger">*</span></label>
                            <input type="number" id="ap_cost_price" class="form-control" placeholder="0.00" min="0.01" step="any">
                        </div></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="ap_save_btn" onclick="saveNewProduct()">Save Product</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    wireDuplicateCheck({
        input: '#ap_product_name',
        url: $('#base_url').val() + 'product/product/check_duplicate_product',
        saveButtons: '#ap_save_btn'
    });
});
function openAddProductModal() {
    $('#ap_barcode').val('');
    $('#ap_product_name').val('');
    $('#ap_product_type').val('N/A');
    $('#ap_batchtype').val('3');
    $('#ap_defaultsaleprice').val('custom');
    $('#ap_stock').val('0');
    $('#ap_cost_price').val('');
    $('#ap_save_btn').text('Save Product').prop('disabled', false);

    var $store = $('#ap_store').empty().append('<option value="1" selected>N/A</option>');
    if (typeof ap_stores !== 'undefined') {
        $.each(ap_stores, function(i, s) { $store.append('<option value="' + s.id + '">' + s.name + '</option>'); });
    }
    var $sup = $('#ap_supplier_id').empty().append('<option value="">Select Supplier</option>');
    if (typeof ap_suppliers !== 'undefined') {
        $.each(ap_suppliers, function(i, s) { $sup.append('<option value="' + s.supplier_id + '">' + s.supplier_name + '</option>'); });
    }

    $('#ap_loading').show();
    $('#ap_form_body').hide();
    $('#ap_save_btn').prop('disabled', true);
    $('#addProductModal').modal('show');

    $.ajax({
        url: $('#ti_base_url').val() + 'get_product_form_data',
        type: 'GET', dataType: 'json',
        success: function(d) {
            var $cat = $('#ap_category_id').empty().append('<option value="">Select Category</option>');
            $.each(d.categories || [], function(i, c) { $cat.append('<option value="' + c.category_id + '">' + c.category_name + '</option>'); });
            var $unit = $('#ap_unit').empty().append('<option value="">Select Unit</option>');
            $.each(d.units || [], function(i, u) { $unit.append('<option value="' + u.unit_id + '">' + u.unit_name + '</option>'); });
            $('#ap_loading').hide(); $('#ap_form_body').show(); $('#ap_save_btn').prop('disabled', false);
        },
        error: function() {
            if (typeof ap_categories !== 'undefined') {
                var $cat = $('#ap_category_id').empty().append('<option value="">Select Category</option>');
                $.each(ap_categories, function(i, c) { $cat.append('<option value="' + c.category_id + '">' + c.category_name + '</option>'); });
            }
            if (typeof ap_units !== 'undefined') {
                var $unit = $('#ap_unit').empty().append('<option value="">Select Unit</option>');
                $.each(ap_units, function(i, u) { $unit.append('<option value="' + u.unit_id + '">' + u.unit_name + '</option>'); });
            }
            $('#ap_loading').hide(); $('#ap_form_body').show(); $('#ap_save_btn').prop('disabled', false);
        }
    });
}
function saveNewProduct() {
    var pname = $('#ap_product_name').val().trim();
    if (!pname) { alert('Product Name is required.'); return; }
    if (!$('#ap_category_id').val()) { alert('Category is required.'); return; }
    if (!$('#ap_unit').val()) { alert('Master Stock Unit is required.'); return; }
    if (!$('#ap_cost_price').val() || parseFloat($('#ap_cost_price').val()) <= 0) {
        alert('Fixed Purchase Price is required and must be greater than 0.');
        $('#ap_cost_price').focus();
        return;
    }
    $('#ap_save_btn').prop('disabled', true).text('Saving...');
    $.ajax({
        url: $('#ti_base_url').val() + 'save_product_ajax',
        type: 'POST',
        data: {
            product_id: $('#ap_barcode').val().trim(), product_name: pname,
            category_id: $('#ap_category_id').val(), subcategory_id: 0, brand_id: 0,
            product_type: $('#ap_product_type').val(), batchtype: $('#ap_batchtype').val(),
            defaultsaleprice: $('#ap_defaultsaleprice').val(), unit: $('#ap_unit').val(),
            store: $('#ap_store').val() || 1, supplier_id: $('#ap_supplier_id').val() || 0,
            stock: $('#ap_stock').val(), status: '1',
            ad: '', bd: '', printname: '', oop_id: '', vat: '0', sell_price: '0', cost_price: $('#ap_cost_price').val()
        },
        dataType: 'json',
        success: function(r) {
            $('#ap_save_btn').prop('disabled', false).text('Save Product');
            if (r.status === 'Success') {
                $('#addProductModal').modal('hide');
                // Auto-load the new product into the touch panel via barcode search
                setTimeout(function() {
                    $('#ti_barcode').val(r.product_code).trigger($.Event('keydown', { key: 'Enter', keyCode: 13 }));
                }, 400);
            } else {
                alert('Failed to save product: ' + (r.message || 'Unknown error'));
            }
        },
        error: function() {
            $('#ap_save_btn').prop('disabled', false).text('Save Product');
            alert('Failed to save product. Please try again.');
        }
    });
}
$('#addProductModal').on('hidden.bs.modal', function() {
    $('#ap_barcode').val('');
    $('#ap_product_name').val('');
    $('#ap_save_btn').text('Save Product').prop('disabled', false);
});
</script>
