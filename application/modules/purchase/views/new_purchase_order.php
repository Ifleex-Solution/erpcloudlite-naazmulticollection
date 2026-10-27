<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    /* JS-required */
    .highlight { background-color: #8b5cf6 !important; color: #fff !important; }

    .star-icon {
        background: #5cb85c; color: #fff;
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; cursor: pointer; transition: opacity .2s;
    }
    .star-icon:hover { opacity: .8; }

    .star-icon-red {
        background: #d9534f; color: #fff;
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; cursor: pointer; transition: opacity .2s;
    }
    .star-icon-red:hover { opacity: .8; }

    /* Panel */
    .inv-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
    .inv-panel > .panel-heading { border-radius: 5px 5px 0 0; }

    /* Header */
    .inv-header { padding: 12px 18px !important; }
    .inv-header-flex { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .inv-page-title { font-size: 16px; font-weight: 600; }
    .inv-header-btns { display: flex; gap: 6px; flex-wrap: wrap; }
    .inv-header-btns .btn.btn-info { background: #2563EB !important; border-color: #1D4ED8 !important; color: #fff !important; }
    .inv-header-btns .btn.btn-info:hover { background: #1D4ED8 !important; }

    /* Form section */
    .inv-form-section { padding: 16px 18px 8px; }
    .inv-form-section .form-group { margin-bottom: 10px; }
    .inv-form-section label { font-weight: 600; font-size: 13px; }

    /* hide mobile labels on desktop */
    .td-mobile-label { display: none; }

    /* Table desktop */
    #purchaseTable { font-size: 12px; }
    #purchaseTable thead th { white-space: nowrap; font-size: 11px; padding: 8px 6px; font-weight: 700; }
    #purchaseTable tbody tr td { padding: 4px 5px; vertical-align: middle; }
    #purchaseTable tbody td .form-control { height: 30px; font-size: 12px; padding: 2px 6px; }
    #purchaseTable tbody td select.form-control { padding: 2px 4px; }
    #purchaseTable tfoot tr td { font-size: 13px; padding: 6px 8px; }
    #purchaseTable tfoot .form-control { height: 32px; font-size: 13px; }

    /* Batch column — desktop only */
    @media (min-width: 1025px) {
        #purchaseTable thead th:nth-child(3),
        #purchaseTable tbody td:nth-child(3) { min-width: 120px; width: 120px; }
    }

    /* Av.Qty column */
    #purchaseTable thead th:nth-child(5),
    #purchaseTable tbody td:nth-child(5) { min-width: 140px; width: 140px; }

    /* Delete button size consistency */
    #purchaseTable tbody td .btn-danger.btn-sm {
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857;
    }

    /* Column widths */
    .col-big { width: 15% !important; }
    .col-total { width: 20% !important; }
    .col-medium { width: 8% !important; }
    .vathidden { width: 8% !important; }
    .col-small { width: 7% !important; }

    /* ── Tablet: 768px – 1024px ── */
    @media (min-width: 768px) and (max-width: 1024px) {

        .inv-panel { border-radius: 10px; }
        .inv-header { padding: 14px 22px !important; }
        .inv-page-title { font-size: 17px; }

        .inv-form-section { padding: 18px 20px 14px; }

        .inv-form-section > .row:not(.form-group) {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 20px;
            margin: 0;
        }
        .inv-form-section > .row:not(.form-group) > [class*="col-sm"] {
            width: 100% !important;
            float: none !important;
            padding: 0;
        }
        .inv-form-section > .row:not(.form-group) > .col-sm-6:last-child { grid-column: 1 / -1; }

        .inv-form-section .form-group.row { display: block; margin: 0; padding: 0; }
        .inv-form-section .form-group.row label.col-form-label {
            display: block; width: 100% !important; float: none !important;
            font-size: 10.5px; font-weight: 700; color: #777;
            text-transform: uppercase; letter-spacing: .4px;
            padding: 0 0 5px 0; margin: 0;
        }
        .inv-form-section .form-group.row > div[class*="col-sm"] {
            display: block; width: 100% !important; float: none !important; padding: 0;
        }
        .inv-form-section .form-group.row .form-control { width: 100%; height: 38px; font-size: 13px; border-radius: 5px; }
        .inv-form-section .form-group.row div[style*="position: relative"] { width: 100% !important; }

        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #purchaseTable { display: block; width: 100%; }
        #purchaseTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #purchaseTable thead { display: none; }

        .td-mobile-label { display: block; }

        #purchaseTable tbody tr {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 24px;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #purchaseTable tbody tr[style*="table-row"] {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            width: 100% !important;
        }

        #purchaseTable tbody td {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 8px 12px;
            border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
            white-space: normal;
        }
        #purchaseTable tbody td.vathidden { width: 100% !important; }
        #purchaseTable tbody td:nth-child(even) { border-right: none !important; }

        #purchaseTable tbody td:last-child {
            grid-column: 1 / -1;
            border-bottom: none !important;
            border-right: none !important;
            padding: 0;
        }
        #purchaseTable tbody td:last-child .td-mobile-label { display: none; }
        #purchaseTable tbody td:last-child button,
        #purchaseTable tbody td:last-child .btn {
            width: 100%;
            border-radius: 0;
            margin: 0;
            display: block;
        }
        #purchaseTable tbody td:last-child > div { width: 100% !important; }
        #purchaseTable tbody td:last-child > div .btn { flex: 1; }

        #purchaseTable tbody td .form-control,
        #purchaseTable tbody td > select,
        #purchaseTable tbody td > div,
        #purchaseTable tbody td .chosen-container,
        #purchaseTable tbody td .select2-container { width: 100% !important; box-sizing: border-box; }

        #purchaseTable tfoot {
            display: block;
            margin-top: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
        }
        #purchaseTable tfoot tr { display: flex; flex-direction: column; }
        #purchaseTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #purchaseTable tfoot td[data-label] {
            display: block !important;
            width: 100%; box-sizing: border-box;
            padding: 9px 14px;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #purchaseTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px;
            margin-bottom: 5px;
        }
        #purchaseTable tfoot td[data-label] .form-control,
        #purchaseTable tfoot td[data-label] input[type="text"] {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: 34px !important; text-align: right;
        }
        #purchaseTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #purchaseTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #purchaseTable tfoot tr:last-child .form-control,
        #purchaseTable tfoot tr:last-child input[type="text"] { font-size: 15px !important; font-weight: 700 !important; }
        #purchaseTable tfoot td.tfoot-btn-cell {
            display: block !important;
            order: -1;
            padding: 0;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #purchaseTable tfoot td.tfoot-btn-cell .btn {
            display: block;
            width: 100%;
            border-radius: 0;
            margin: 0;
            padding: 11px;
            font-size: 16px;
        }
        /* VAT cell: hidden by default; shown only when JS sets display:table-cell */
        #purchaseTable tfoot td.vathidden[data-label] { display: none !important; }
        #purchaseTable tfoot td.vathidden[data-label][style*="table-cell"] { display: block !important; width: 100% !important; box-sizing: border-box; }

        .table-responsive .col-sm-3.table-bordered { width: 50% !important; margin-top: 14px; border-radius: 7px; }

        .form-group.row.text-right { margin-top: 12px; }
        .form-group.row.text-right .btn-success { padding: 10px 44px; font-size: 14px; border-radius: 6px; }
    }

    /* ── Mobile ── */
    @media (max-width: 767px) {
        .inv-header-flex { flex-direction: column; align-items: flex-start; }
        .inv-form-section .form-group.row { display: flex !important; align-items: center !important; flex-wrap: nowrap !important; margin-bottom: 10px !important; }
        .inv-form-section .form-group.row > label[class*="col-"] { flex: 0 0 38% !important; width: 38% !important; max-width: 38% !important; padding-right: 8px !important; white-space: normal !important; word-wrap: break-word !important; }
        .inv-form-section .form-group.row > div[class*="col-"] { flex: 1 1 auto !important; min-width: 0 !important; }

        .table-responsive .col-sm-3.table-bordered { width: 100% !important; float: none; box-sizing: border-box; }

        .form-group.row.text-right .col-sm-12 { text-align: center; }
        .form-group.row.text-right .btn-success { width: 100%; }

        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #purchaseTable { display: block; width: 100%; }
        #purchaseTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #purchaseTable thead { display: none; }

        #purchaseTable tbody tr {
            display: block;
            width: 100%;
            box-sizing: border-box;
            margin-bottom: 16px;
            border: 1px solid #ebebeb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #purchaseTable tbody tr[style*="table-row"] {
            display: block !important;
            width: 100% !important;
        }

        #purchaseTable tbody td {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 6px 10px;
            border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            white-space: normal;
        }
        #purchaseTable tbody td.vathidden { width: 100% !important; }

        #purchaseTable tbody td:last-child {
            border-bottom: none !important;
            padding: 0;
        }
        #purchaseTable tbody td:last-child .td-mobile-label { display: none; }
        #purchaseTable tbody td:last-child button,
        #purchaseTable tbody td:last-child .btn {
            width: 100%;
            border-radius: 0;
            margin: 0;
            display: block;
        }
        #purchaseTable tbody td:last-child > div {
            width: 100% !important;
            display: flex !important;
        }
        #purchaseTable tbody td:last-child > div .btn { flex: 1; }

        .td-mobile-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 4px;
        }

        #purchaseTable tbody td .form-control,
        #purchaseTable tbody td input[type="text"],
        #purchaseTable tbody td input[type="number"],
        #purchaseTable tbody td select,
        #purchaseTable tbody td > div,
        #purchaseTable tbody td .chosen-container,
        #purchaseTable tbody td .select2-container { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }

        #purchaseTable tbody td.vathidden[style*="table-cell"] {
            display: block !important;
            width: 100% !important;
            box-sizing: border-box;
            padding: 6px 10px;
            border-bottom: 1px solid #f0f0f0 !important;
        }

        #purchaseTable tfoot {
            display: block;
            margin-top: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }
        #purchaseTable tfoot tr { display: flex; flex-direction: column; }
        #purchaseTable tfoot td { display: none !important; border: none !important; padding: 0; }

        #purchaseTable tfoot td[data-label] {
            display: block !important;
            width: 100%;
            box-sizing: border-box;
            padding: 8px 12px;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #purchaseTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: .4px;
            margin-bottom: 5px;
        }
        #purchaseTable tfoot td[data-label] .form-control,
        #purchaseTable tfoot td[data-label] input[type="text"] {
            display: block;
            width: 100% !important;
            box-sizing: border-box;
            font-size: 13px;
            font-weight: 600;
            height: 34px !important;
            text-align: right;
        }

        #purchaseTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #purchaseTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #purchaseTable tfoot tr:last-child td[data-label]::before { color: #555; }
        #purchaseTable tfoot tr:last-child .form-control,
        #purchaseTable tfoot tr:last-child input[type="text"] {
            font-size: 15px !important;
            font-weight: 700 !important;
        }

        #purchaseTable tfoot td.tfoot-btn-cell {
            display: block !important;
            order: -1;
            padding: 0;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #purchaseTable tfoot td.tfoot-btn-cell .btn {
            display: block;
            width: 100%;
            border-radius: 0;
            margin: 0;
            padding: 11px;
            font-size: 16px;
        }
        /* VAT cell: hidden by default; shown only when JS sets display:table-cell */
        #purchaseTable tfoot td.vathidden[data-label] { display: none !important; }
        #purchaseTable tfoot td.vathidden[data-label][style*="table-cell"] { display: block !important; width: 100% !important; box-sizing: border-box; }
    }

    /* ── Table redesign — product screen style ── */
    #purchaseTable { border-collapse: collapse !important; border-radius: 10px !important; overflow: hidden !important; box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important; border: 1px solid #E2E8F0 !important; }
    #purchaseTable thead th { background: #F1F5F9 !important; color: #475569 !important; font-size: 11px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: .7px !important; border-bottom: 2px solid #DDD6FE !important; border-top: none !important; padding: 11px 8px !important; white-space: nowrap !important; }
    #purchaseTable thead th .text-danger { color: #ef4444 !important; }
    #purchaseTable tbody td { padding: 8px !important; font-size: 13px !important; color: #374151 !important; border-color: #F3F0FE !important; vertical-align: middle !important; }
    #purchaseTable tbody tr:hover td { background: #F3F0FE !important; }
    #purchaseTable tfoot td { background: #F8FAFC !important; border-top: 2px solid #DDD6FE !important; border-color: #F3F0FE !important; }
    #purchaseTable tfoot tr:last-child td { background: #F3F0FE !important; border-top: 2px solid #DDD6FE !important; font-weight: 700 !important; }

    /* ── Input redesign ── */
    input.form-control { border: 1px solid #E2E8F0 !important; border-radius: 6px !important; color: #374151 !important; transition: border-color .18s, box-shadow .18s !important; }
    input.form-control:focus { border-color: #8b5cf6 !important; box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important; }

    /* ── Select2 dropdown redesign ── */
    .select2-container .select2-selection--single, .select2-container--default .select2-selection--single { border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important; background: #F8FAFC !important; height: 34px !important; }
    .select2-container .select2-selection--single .select2-selection__rendered { color: #374151 !important; font-size: 13px !important; line-height: 32px !important; padding-left: 10px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow b { border-color: #64748B transparent transparent transparent !important; border-width: 5px 4px 0 4px !important; margin-top: -1px !important; }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b { border-color: transparent transparent #8b5cf6 transparent !important; border-width: 0 4px 5px 4px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single, .select2-container--default.select2-container--open .select2-selection--single { border-color: #8b5cf6 !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important; }
    .select2-selection__placeholder { color: #94A3B8 !important; }
    .select2-dropdown { border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important; box-shadow: 0 4px 16px rgba(0,0,0,.10) !important; margin-top: 2px !important; }
    .select2-search--dropdown .select2-search__field { border: 1.5px solid #E2E8F0 !important; border-radius: 6px !important; font-size: 13px !important; padding: 5px 8px !important; }
    .select2-results__option { font-size: 13px !important; padding: 7px 12px !important; color: #374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #8b5cf6 !important; color: #fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background: #F3F0FE !important; color: #8b5cf6 !important; }

    /* ── Modals — brand page style ── */
    #addProductModal .modal-content, #addSupplierModal .modal-content, #addBatchModal .modal-content { border-radius: 12px !important; border: none !important; box-shadow: 0 8px 32px rgba(0,0,0,.15) !important; }
    #addProductModal .modal-header, #addSupplierModal .modal-header, #addBatchModal .modal-header { background: #fff !important; border-bottom: 2px solid #F1F5F9 !important; padding: 16px 24px !important; border-radius: 12px 12px 0 0 !important; }
    #addProductModal .modal-header h4, #addSupplierModal .modal-header h4, #addBatchModal .modal-header h4 { font-size: 15px !important; font-weight: 700 !important; color: #1E293B !important; margin: 0 !important; text-align: left !important; }
    #addProductModal .modal-header .close, #addSupplierModal .modal-header .close, #addBatchModal .modal-header .close { color: #94A3B8 !important; opacity: 1 !important; }
    #addProductModal .modal-header .close:hover, #addSupplierModal .modal-header .close:hover, #addBatchModal .modal-header .close:hover { color: #475569 !important; }
    #addProductModal .modal-body, #addSupplierModal .modal-body, #addBatchModal .modal-body { padding: 20px 24px !important; background: #fff !important; }
    #addProductModal .modal-body .form-group label, #addSupplierModal .modal-body .form-group label, #addBatchModal .modal-body .form-group label { font-size: 13px !important; font-weight: 600 !important; color: #374151 !important; margin-bottom: 5px !important; }
    #addProductModal .modal-body .form-control, #addSupplierModal .modal-body .form-control, #addBatchModal .modal-body .form-control { border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important; font-size: 13px !important; color: #374151 !important; background: #F8FAFC !important; height: auto !important; padding: 8px 12px !important; }
    #addProductModal .modal-body .form-control:focus, #addSupplierModal .modal-body .form-control:focus, #addBatchModal .modal-body .form-control:focus { border-color: #8b5cf6 !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important; }
    #addProductModal .modal-footer, #addSupplierModal .modal-footer, #addBatchModal .modal-footer { padding: 12px 24px !important; border-top: 2px solid #F1F5F9 !important; border-radius: 0 0 12px 12px !important; background: #fff !important; }
    #addProductModal .modal-footer .btn-default, #addSupplierModal .modal-footer .btn-default, #addBatchModal .modal-footer .btn-default { border-radius: 8px !important; font-size: 13px !important; font-weight: 500 !important; padding: 8px 18px !important; }
    #addProductModal .modal-footer .btn-primary, #addSupplierModal .modal-footer .btn-primary, #addBatchModal .modal-footer .btn-success { background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important; border: none !important; border-radius: 8px !important; font-size: 13px !important; font-weight: 600 !important; padding: 8px 18px !important; color: #fff !important; transition: background .15s, box-shadow .15s !important; }
    #addProductModal .modal-footer .btn-primary:hover, #addSupplierModal .modal-footer .btn-primary:hover, #addBatchModal .modal-footer .btn-success:hover { background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important; box-shadow: 0 4px 12px rgba(139,92,246,.35) !important; }
    #addProductModal .select2-container .select2-selection--single, #addProductModal .select2-container--default .select2-selection--single { border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important; background: #F8FAFC !important; height: 38px !important; }
    #addProductModal .select2-container .select2-selection--single .select2-selection__rendered { color: #374151 !important; font-size: 13px !important; line-height: 36px !important; padding-left: 10px !important; }
    #addProductModal .select2-container--default.select2-container--focus .select2-selection--single, #addProductModal .select2-container--default.select2-container--open .select2-selection--single { border-color: #8b5cf6 !important; background: #fff !important; box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag inv-panel">
            <div class="panel-heading inv-header" id="style12">
                <div class="panel-title inv-header-flex has-goback">

                    <h4 id="title" class="inv-page-title" style="margin:0;">
                        <?php echo $title; ?>
                    </h4>

                    <span class="padding-lefttitle inv-header-btns">
                        <button class="btn btn-info m-b-5 m-r-2" onclick="openAddProductModal()">
                            <i class="ti-plus"></i> Add Product
                        </button>
                        <button class="btn btn-success m-b-5 m-r-2" onclick="openAddSupplierModal()">
                            <i class="fa fa-user"></i> Add Supplier
                        </button>

                        <?php if ($this->permission1->method('add_stockbatch', 'create')->access()) { ?>
                            <button class="btn btn-primary m-b-5 m-r-2" onclick="openStockbatch()">
                                <i class="ti-plus"></i> <?php echo display('add_stockbatch') ?>
                            </button>
                        <?php } ?>

                        <?php if ($this->permission1->method('stockbatchlist', 'create')->access()) { ?>
                            <button class="btn btn-warning m-b-5 m-r-2" onclick="manageStockbatch()">
                                <i class="fa fa-cube"></i> <?php echo display('stockbatchlist') ?>
                            </button>
                        <?php } ?>
                    </span>

                
<a href="<?php echo base_url('manage_purchase_order') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <?php if (!empty($id)): ?>
            <div id="po_edit_banner" style="background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing <span id="po_edit_label">Purchase Order #<?php echo (int)$id; ?></span> — changes are saved only when you click Update.
            </div>
            <?php endif; ?>

            <div class="panel-body inv-form-section">

                <!-- Row 1: Purchase Date | Branch -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label"><?php echo display('purchase_date') ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="purchase_date" value="<?php echo $date; ?>" id="date" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="branch" required name="branch" tabindex="3">


                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Invoice Type | Incident Type -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Invoice Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="invoicetype" required name="invoicetype" tabindex="3" onchange="incidetTypechange()">
                                    <option value=""></option>
                                    <option value="cash">Cash</option>
                                    <option value="credit">Credit</option>
                                  
                                    <option value="cash_vat">Cash VAT</option>
                                    <option value="credit_vat">Credit VAT</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="incidenttype" required name="incidenttype" tabindex="3">
                                    <option value=""></option>
                                    <option value="1">International Purchase</option>
                                    <option value="2">Local Purchase</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Supplier (full width) -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label"><?php echo display('supplier') ?>
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select name="supplier_id" id="supplier_id" class="form-control " required="" tabindex="1">
                                    <option value="">Select an option</option>

                                    <?php foreach ($all_supplier as $suppliers) { ?>
                                        <option value="<?php echo $suppliers['supplier_id'] ?>">
                                            <?php echo $suppliers['supplier_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>



                </div>


                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="purchaseTable">
                        <thead>
                            <tr>
                                <th class="text-center col-big">Product<i class="text-danger">*</i></th>

                                <th class="text-center col-medium">Store<i class="text-danger">*</i>
                                </th>
                                <th class="text-center col-medium">Batch<i class="text-danger">*</i>
                                </th>
                                <th class="text-center  col-small">Unit<i class="text-danger">*</i> </th>
                                <th class="text-center  col-small">Av.Qty</th>
                                <th class="text-center  col-small">Qty<i
                                        class="text-danger col-medium">*</i></th>
                                <th class="text-center col-medium">Price val <i
                                        class="text-danger"> *</i></th>
                                <th class="text-center col-medium">Discount</th>
                                <th class="text-center col-medium">Dis.val</th>

                                <th class="text-center vathidden" id="vathidden">VAT.val</th>


                                <th class="text-center col-big">Total</th>

                                <th class="text-center"><?php echo display('action') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">
                                    <span class="td-mobile-label">Product</span>
                                    <div style='position: relative; display: inline-block;width:100%;'>
                                        <input class='form-control' type='text' id="productInput1" placeholder='Product...' onkeyup='handleProductKeyPress(event,1)' autocomplete='off' />
                                        <input type='text' name='customer_id[]' id='product1' hidden />
                                        <div id='productResults1' style='  position: absolute; z-index: 99999 !important;max-height: 150px;  overflow-y: auto;border: 1px solid #ddd;z-index: 9999; background-color: #fff; border-radius: 4px;  box-shadow: 0 4px 6px rgba(0,0,0,0.1);' autocomplete='off'>

                                        </div>
                                    </div>
                                    <input type="hidden" id="mconversion_ratio1" />
                                    <input type="hidden" id="mastercost_price1" />
                                    <input type="hidden" id="bd1" />
                                    <input type="hidden" id="ad1" />
                                    <input type="hidden" id="isstock1" />



                                </td>

                                <td class="rate">
                                    <span class="td-mobile-label">Store</span>
                                    <select class="form-control" id="store1" name="store[]" tabindex="3" onchange="product_search(1,'store')">
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td class="rate">
                                    <span class="td-mobile-label">Batch</span>
                                    <select class="form-control" id="batch1" name="batch[]" tabindex="3" onchange="product_search(1,'batch')">
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="qty">
                                    <span class="td-mobile-label">Unit</span>
                                    <select class="form-control" id="unit1" required name="unit1" onchange="product_search(1,'unit')" tabindex="3">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" id="conversionid1" />
                                    <input type="hidden" id="conversiontype1" />
                                    <input type="hidden" id="conversion_ratio1" />
                                </td>
                                <td class="qty">
                                    <span class="td-mobile-label">Av.Qty</span>
                                    <input type="hidden" name="code[]" onkeyup="product_search(1,'code');"
                                        class="total_qntt_1 form-control text-right"
                                        id="code1" placeholder="0.00" min="0" readonly />
                                    <span id='codetype1' style="margin-left:5px"></span>

                                </td>



                                <td class="qty">
                                    <span class="td-mobile-label">Qty</span>
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />
                                </td>
                                <td class="rate">
                                    <span class="td-mobile-label">Price</span>
                                    <input type="text" name="product_rate[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="product_rate1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                </td>

                                <td class="qty">
                                    <span class="td-mobile-label">Discount</span>
                                    <input type="text" name="discount_per[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="discount1" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">

                                </td>
                                <td class="rate">
                                    <span class="td-mobile-label">Dis.val</span>
                                    <input type="text" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->



                                <td class="rate vathidden">
                                    <span class="td-mobile-label">VAT</span>
                                    <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />

                                    <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>
                                <!-- VAT  end-->
                                <td class="product_field">
                                    <span class="td-mobile-label">Total</span>
                                    <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price1" value="0.00" readonly="readonly" />

                                    <input type="hidden" id="total_discount1" class="" />
                                    <input type="hidden" id="all_discount1" class="total_discount dppr" name="discount_amount[]" />
                                </td>

                                <td>
                                </td>

                            </tr>

                            <?php
                            for ($i = 2; $i <= 20; $i++) {
                            ?>
                                <tr id="myRow<?php echo $i; ?>">
                                    <td class="product_field">
                                        <span class="td-mobile-label">Product</span>
                                        <div style='position: relative; display: inline-block;width:100%;'>
                                            <input class='form-control' type='text' id="productInput<?php echo $i; ?>" placeholder='Product...' onkeyup='handleProductKeyPress(event,<?php echo $i; ?>)' autocomplete='off' />
                                            <input type='text' name='customer_id[]' id='product<?php echo $i; ?>' hidden />
                                            <div id='productResults<?php echo $i; ?>' style='  position: absolute; z-index: 99999 !important;max-height: 150px;  overflow-y: auto;border: 1px solid #ddd;z-index: 9999; background-color: #fff; border-radius: 4px;  box-shadow: 0 4px 6px rgba(0,0,0,0.1);' autocomplete='off'>

                                            </div>
                                        </div>
                                        <input type="hidden" id="mconversion_ratio<?php echo $i; ?>" />
                                        <input type="hidden" id="mastercost_price<?php echo $i; ?>" />

                                        <input type="hidden" id="bd<?php echo $i; ?>" />
                                        <input type="hidden" id="ad<?php echo $i; ?>" />
                                        <input type="hidden" id="isstock<?php echo $i; ?>" />

                                    </td>



                                    <td class="rate">
                                        <span class="td-mobile-label">Store</span>
                                        <select class="form-control" id="store<?php echo $i; ?>" name="store[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'store')">
                                            <option value=""></option>
                                        </select>
                                    </td>

                                    <td class="rate">
                                        <span class="td-mobile-label">Batch</span>
                                        <select class="form-control" id="batch<?php echo $i; ?>" name="batch[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'batch')">
                                            <option value=""></option>
                                        </select>
                                    </td>



                                    <td class="qty">
                                        <span class="td-mobile-label">Unit</span>
                                        <select class="form-control" id="unit<?php echo $i; ?>" required name="unit<?php echo $i; ?>" onchange="product_search(<?php echo $i; ?>,'unit')" tabindex="3">
                                            <option value=""></option>
                                        </select>
                                        <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                        <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                        <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />
                                    </td>

                                    <td class="qty">
                                        <span class="td-mobile-label">Av.Qty</span>
                                        <input type="hidden" name="code[]" onkeyup="product_search(<?php echo $i; ?>, 'code');" class="total_qntt_1 form-control text-right" id="code<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                        <span id='codetype<?php echo $i; ?>' style="margin-left:5px"></span>

                                    </td>

                                    <td class="qty">
                                        <span class="td-mobile-label">Qty</span>
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />
                                    </td>

                                    <td class="rate">
                                        <span class="td-mobile-label">Price</span>
                                        <input type="text" name="product_rate[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="product_rate<?php echo $i; ?>" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                    </td>

                                    <td class="qty">
                                        <span class="td-mobile-label">Discount</span>
                                        <input type="text" name="discount_per[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="discount<?php echo $i; ?>" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                        <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    </td>

                                    <td class="rate">
                                        <span class="td-mobile-label">Dis.val</span>
                                        <input type="text" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT start -->

                                    <td class="rate vathidden">
                                        <span class="td-mobile-label">VAT</span>
                                        <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />

                                        <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT end -->
                                    <td class="product_field">
                                        <span class="td-mobile-label">Total</span>
                                        <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price<?php echo $i; ?>" value="0.00" readonly="readonly" />
                                        <input type="hidden" id="total_discount<?php echo $i; ?>" class="" />
                                        <input type="hidden" id="all_discount<?php echo $i; ?>" class="total_discount dppr" name="discount_amount[]" />
                                    </td>

                                    <td>
                                        <button class='btn btn-danger' type='button' value='Delete' onclick='deleteRow(<?php echo $i; ?>)'>
                                            <i class='fa fa-trash'></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>

                        </tbody>
                        <tfoot>
                            <tr>

                                <td colspan="10" class="text-right vathidden"><b><?php echo display('total') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('total') ?>:</b></td>

                                <td class="text-right" data-label="Total">
                                    <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />
                                </td>
                                <td class="tfoot-btn-cell"> <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item"
                                        onClick="addInputField('addinvoiceItem');" tabindex="9"><i class="fa fa-plus"></i></button>

                                    <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
                                </td>
                            </tr>
                            <tr>

                                <td colspan="10" class="text-right vathidden"><b>Purchase Discount:</b></td>
                                <td colspan="9" class="text-right vatshow"><b>Purchase Discount:</b></td>

                                <td class="text-right" data-label="Purchase Discount">
                                    <input type="text" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(1)" name="discount" placeholder="0.00" value="" />
                                </td>

                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('total_discount') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('total_discount') ?>:</b></td>


                                <td class="text-right" data-label="Total Discount">
                                    <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>

                                <td class="text-right vathidden" colspan="10"><b><?php echo display('ttl_val') ?>:</b></td>

                                <td class="text-right vathidden" data-label="VAT Value">
                                    <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />
                                </td>
                                <td class="text-right vathidden">

                                </td>
                            </tr>


                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('grand_total') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('grand_total') ?>:</b></td>


                                <td class="text-right" data-label="Grand Total">
                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>
                    <div class="col-sm-3 table-bordered p-20">
                        <div id="adddiscount" class="display-none">
                            <div class="row">


                                <!-- Details -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="details" class="col-form-label">
                                            <?php echo display('details'); ?>
                                        </label>
                                        <textarea
                                            class="form-control"
                                            tabindex="4"
                                            id="details"
                                            name="purchase_details"
                                            placeholder="<?php echo display('details'); ?>"
                                            rows="3"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <button id="save_add" class="btn btn-success btn-save-anim" name="add-invoice" onclick="save()">
                            <?php echo '<i class="fa ' . (empty($id) ? 'fa-save' : 'fa-pencil') . '"></i> ' . (empty($id) ? display('save') : display('update')); ?></button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let suppliers=" . json_encode($all_supplier) . ";";
echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "let batches=" . json_encode($batches) . ";";
echo "let ap_categories=" . json_encode($category_list ?: []) . ";";
echo "let ap_units=" . json_encode($unit_list ?: []) . ";";

echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";

echo "</script>";
?>
<script>
    $('body').addClass("sidebar-mini sidebar-collapse");
    let type2 = ""
    if (usertype == 3) {
        document.getElementById('style12').style.backgroundColor = '#E0E0E0';
        const title = document.getElementById('title');
        title.style.color = 'blue';
        type2 = "B"

    } else {
        type2 = "A"

    }
    let count = 2
    let batchModalRowNum = 0;
    let pendingBatchSelect = {};
    let mbResults = [];
    let mbCurrentIndex = -1;

    document.addEventListener("keydown", function(event) {
        if (event.key === "F2") { openStockbatch(); }
        if (event.key === "F3") { manageStockbatch(); }
    });

    function openStockbatch(rowNum) {
        batchModalRowNum = rowNum || 0;
        document.getElementById('mb_batchid').value = '';
        document.getElementById('mb_details').value = '';
        document.getElementById('mb_busage').value = '';
        document.getElementById('mb_status').value = '1';
        document.getElementById('mb_mdate').value = '';
        document.getElementById('mb_pdate').value = '';
        document.getElementById('mb_edate').value = '';
        document.getElementById('mb_edate_toggle').value = 'no';
        document.getElementById('mb_mrp').value = '';
        document.getElementById('mb_productInput').value = '';
        document.getElementById('mb_product').value = '';

        var $busageDropdown = $('#mb_busage');
        $busageDropdown.empty();
        $busageDropdown.append('<option value="" disabled selected>Select Batch Usage Type</option>');
        $busageDropdown.append('<option value="single">Single Usage</option>');
        $busageDropdown.append('<option value="multiple">Multiple Usage</option>');

        var $edateDropdown = $('#mb_edate_toggle');
        $edateDropdown.empty();
        $edateDropdown.append('<option value="no">Disable</option>');
        $edateDropdown.append('<option value="yes">Enable</option>');
        $edateDropdown.val('no');

        document.getElementById('mb_productResults').innerHTML = '';
        mbResults = [];
        mbCurrentIndex = -1;
        $('#mb_singleshow, #mb_singleshow1, #mb_singleshow2, #mb_singleshow3, #mb_singleshow4').hide();
        document.getElementById('mb_edate_row').style.display = 'none';
        $('#mb_save_btn').prop('disabled', false).text('Save Batch');
        $('#addBatchModal').modal('show');
    }

    function modalChangeBatchtype() {
        let busage = document.getElementById('mb_busage').value;
        if (busage === 'single') {
            $('#mb_singleshow, #mb_singleshow1, #mb_singleshow2, #mb_singleshow3, #mb_singleshow4').show();
        } else {
            $('#mb_singleshow, #mb_singleshow1, #mb_singleshow2, #mb_singleshow3, #mb_singleshow4').hide();
            document.getElementById('mb_edate_row').style.display = 'none';
        }
    }

    function modalToggleEdate() {
        if (document.getElementById('mb_edate_toggle').value === 'yes') {
            document.getElementById('mb_edate_row').style.display = 'block';
        } else {
            document.getElementById('mb_edate_row').style.display = 'none';
            document.getElementById('mb_edate').value = '';
        }
    }

    function handleMbProductKeyPress(event) {
        const query = document.getElementById('mb_productInput').value;
        if (event.key === 'ArrowDown') {
            if (mbCurrentIndex < mbResults.length - 1) { mbCurrentIndex++; highlightMbProduct(mbCurrentIndex); }
        } else if (event.key === 'ArrowUp') {
            if (mbCurrentIndex > 0) { mbCurrentIndex--; highlightMbProduct(mbCurrentIndex); }
        } else if (event.key === 'Enter') {
            if (mbResults.length > 0 && mbCurrentIndex >= 0) {
                document.getElementById('mb_productInput').value = mbResults[mbCurrentIndex].product_name;
                document.getElementById('mb_product').value = mbResults[mbCurrentIndex].id;
                document.getElementById('mb_productResults').innerHTML = '';
            }
        } else if (event.key === 'Backspace') {
            document.getElementById('mb_product').value = '';
            document.getElementById('mb_productResults').innerHTML = '';
        } else {
            if (!query) return;
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getProductByNameMB',
                type: 'POST',
                data: { product_name: query },
                success: function(response) {
                    let prods = JSON.parse(response);
                    mbResults = prods.filter(function(p) {
                        return p.product_name.toLowerCase().includes(query.toLowerCase());
                    });
                    mbCurrentIndex = -1;
                    displayMbProductResults(mbResults);
                },
                error: function(error) { console.log(error); }
            });
        }
    }

    function displayMbProductResults(results) {
        const searchResultsDiv = document.getElementById('mb_productResults');
        searchResultsDiv.innerHTML = '';
        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<div style="padding:8px;">No results found</div>';
        } else {
            results.forEach(function(item, index) {
                const resultItem = document.createElement('div');
                resultItem.classList.add('mbResultItem');
                resultItem.textContent = item.product_name;
                resultItem.style.padding = '8px';
                resultItem.style.cursor = 'pointer';
                resultItem.addEventListener('mouseover', function() { this.style.backgroundColor = "#8b5cf6"; this.style.color = "#ffff"; });
                resultItem.addEventListener('mouseout',  function() { this.style.backgroundColor = "#ffff"; this.style.color = "#000"; });
                resultItem.addEventListener('click', function() {
                    document.getElementById('mb_productInput').value = item.product_name;
                    document.getElementById('mb_product').value = item.id;
                    searchResultsDiv.innerHTML = '';
                });
                searchResultsDiv.appendChild(resultItem);
            });
        }
        mbCurrentIndex = 0;
        highlightMbProduct(0);
    }

    function highlightMbProduct(index) {
        const items = document.querySelectorAll('.mbResultItem');
        items.forEach(function(item, idx) {
            if (idx === index) {
                item.classList.add('highlight');
                item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function manageStockbatch() {
        window.open(
            $('#base_url').val() + "stockbatchlist",
            "popupWindow",
            "width=1000,height=800,scrollbars=yes"
        );
    }

    async function saveModalBatch() {
        let batchid = document.getElementById('mb_batchid').value.trim();
        let busage  = document.getElementById('mb_busage').value;
        let status  = document.getElementById('mb_status').value;
        let product = document.getElementById('mb_product').value;

        if (!batchid)  { alert('Batch ID is required'); return; }
        if (!busage)   { alert('Batch Usage Type is required'); return; }
        if (status === '') { alert('Status is required'); return; }
        if (busage === 'single' && !product) { alert('Product is required for Single Usage'); return; }

        try {
            let checkRes = await $.ajax({
                type: 'POST',
                url: $('#base_url').val() + 'stock/stock/getStockBatchById',
                data: { batchid: batchid }
            });
            if (JSON.parse(checkRes) !== 'success') { alert('Batch ID already exists'); return; }
        } catch (e) { alert('Error checking Batch ID'); return; }

        let edate_toggle = document.getElementById('mb_edate_toggle').value;
        let postData = {
            batchid:  batchid,
            details:  document.getElementById('mb_details').value,
            busage:   busage,
            status:   status,
            edate_toggle: edate_toggle,
            product:  busage === 'single' ? product : '0',
            mdate:    busage === 'single' ? document.getElementById('mb_mdate').value : '',
            pdate:    busage === 'single' ? document.getElementById('mb_pdate').value : '',
            edate:    (busage === 'single' && edate_toggle === 'yes') ? document.getElementById('mb_edate').value : '',
            mrp:      busage === 'single' ? (document.getElementById('mb_mrp').value || '0') : '0'
        };

        $('#mb_save_btn').prop('disabled', true).text('Saving...');

        $.ajax({
            type: 'POST',
            url: $('#base_url').val() + 'stock/stock/save_stockbatch_ajax',
            data: postData,
            success: function(res) {
                let result = JSON.parse(res);
                $('#mb_save_btn').prop('disabled', false).text('Save Batch');
                if (result.success) {
                    $('select[id^="batch"]').each(function() {
                        let rowNum  = this.id.replace('batch', '');
                        let rowProd = document.getElementById('product' + rowNum) ? document.getElementById('product' + rowNum).value : '';
                        if (result.busage === 'multiple' || !rowProd || rowProd == result.product) {
                            $(this).append('<option value="' + result.id + '">' + result.batchid + '</option>');
                        }
                    });
                    if (result.busage === 'single' && result.product > 0) {
                        let rowNum = count;
                        document.getElementById('myRow' + count).style.display = 'table-row';
                        getActiveStore(0, count);
                        count = count + 1;
                        $.ajax({
                            url: $('#base_url').val() + 'stock/stock/getproduct',
                            type: 'POST',
                            data: { prodid: result.product },
                            success: function(resp) {
                                let prod = JSON.parse(resp);
                                let productName = (prod && prod.length > 0) ? prod[0].product_name : 'Product #' + result.product;
                                alert('Batch saved successfully!\nBatch ID: ' + result.batchid + '\nProduct: ' + productName);
                                if (prod && prod.length > 0) {
                                    document.getElementById('productInput' + rowNum).value = prod[0].product_name;
                                    document.getElementById('product' + rowNum).value = result.product;
                                }
                                pendingBatchSelect[rowNum] = result.id;
                                product_search(rowNum, 'product1');
                            }
                        });
                    } else {
                        alert('Batch saved successfully!\nBatch ID: ' + result.batchid + '\nProduct: N/A (Multiple Usage)');
                    }
                    $('#addBatchModal').modal('hide');
                } else {
                    alert('Error: ' + result.message);
                }
            },
            error: function() {
                $('#mb_save_btn').prop('disabled', false).text('Save Batch');
                alert('Network error saving batch');
            }
        });
    }

    $(document).ready(function() {
        $('#addBatchModal').on('shown.bs.modal', function() {
            $('#addBatchModal .datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                beforeShow: function(input, inst) {
                    setTimeout(function() { inst.dpDiv.css('z-index', 99999); }, 0);
                }
            });
        });
    });

    function handleProductKeyPress(event, count) {

        const productElement = document.getElementById('productInput' + count);
        const query = productElement.value;


        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemproduct(currentIndex);
            }

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemproduct(currentIndex);
            }
        } else if (event.key === 'Enter') {

            // Select the highlighted item
            if (results.length > 0) {
                if (document.getElementById('productInput' + count).value == "") {
                    alert("Product shouldn't be empty")
                    return
                }

                document.getElementById('productInput' + count).value = results[currentIndex].product_name;
                document.getElementById('product' + count).value = results[currentIndex].id;
                document.getElementById('productResults' + count).innerHTML = '';

                product_search(count, "product")

                // document.getElementById('branchId').value = results[currentIndex].id;
            } else {
                alert("Product shouldn't be empty")
                document.getElementById('product' + count).value = "";
                document.getElementById('productInput' + count).value = "";
                return
            }

        } else if (event.key === "Backspace") {
            document.getElementById('product' + count).value = "";
            // document.getElementById('productInput' + count).value = "";
            var $batchDropdown = $('#batch' + count);
            $batchDropdown.empty();

            var $storeDropdown = $('#store' + count);
            $storeDropdown.empty();

            var $unitDropdown = $('#unit' + count);
            $unitDropdown.empty();

            document.getElementById('productResults' + count).innerHTML = ""

            document.getElementById('qty' + count).value = "";
            document.getElementById('code' + count).value = "";
            document.getElementById('codetype' + count).innerHTML = "";
            document.getElementById('unit' + count).value = "";
            document.getElementById('product_rate' + count).value = "";
            document.getElementById('discount' + count).value = "";
            document.getElementById('discount_value' + count).value = "";
            document.getElementById('vat_percent' + count).value = "";
            document.getElementById('vat_value' + count).value = "";
            document.getElementById('total_price' + count).value = "";
            document.getElementById('total_discount' + count).value = "";
            document.getElementById('all_discount' + count).value = "";
        } else {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getProductByName',
                type: 'POST',
                data: {
                    product_name: query,
                },
                success: function(response) {
                    var products = JSON.parse(response);

                    results = products
                        .filter(product => product.product_name.toLowerCase().includes(query.toLowerCase()));
                    currentIndex = -1;
                    displayResultsProduct(results, count);

                },
                error: function(error) {
                    console.log(error);
                }
            });


        }

    }


    function displayResultsProduct(results, count) {
        const searchResultsDiv = document.getElementById('productResults' + count);
        searchResultsDiv.innerHTML = ''; // Clear previous results

        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<div style="padding:8px;">No results found</div>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                resultItem.textContent = item.product_name;

                // style (optional but recommended)
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";

                // hover effect
                resultItem.addEventListener("mouseover", function() {
                    this.style.backgroundColor = "#8b5cf6";
                    this.style.color = "#ffff";

                });

                resultItem.addEventListener("mouseout", function() {
                    this.style.backgroundColor = "#ffff";
                    this.style.color = "#000";

                });

                // CLICK EVENT (IMPORTANT)
                resultItem.addEventListener("click", function() {

                    // set product name
                    document.getElementById('productInput' + count).value = item.product_name;

                    // set hidden product id
                    document.getElementById('product' + count).value = item.id;

                    // clear dropdown
                    searchResultsDiv.innerHTML = '';

                    product_search(count, "product")


                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproduct(0);

    }

    function highlightItemproduct(index) {
        const items = document.querySelectorAll('.resultItem');
        items.forEach((item, idx) => {
            if (idx === index) {

                item.classList.add('highlight');

                item.scrollIntoView({
                    behavior: "smooth",
                    block: "nearest"
                });

            } else {
                item.classList.remove('highlight');
            }
        });
    }

    function incidetTypechange() {
        clearDetails2()
        if (document.getElementById('invoicetype').value == 'cash_vat' ||
            document.getElementById('invoicetype').value == 'credit_vat' ||
            document.getElementById('invoicetype').value == 'svat') {
            document.querySelectorAll('.vathidden').forEach(el => {
                el.style.display = 'table-cell';
            });
            document.querySelectorAll('.vatshow').forEach(el => {
                el.style.display = 'none';
            });


        } else {

            document.querySelectorAll('.vathidden').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('.vatshow').forEach(el => {
                el.style.display = 'table-cell';
            });
        }

    }


    $(document).ready(function() {
        var $supplierDropdown = $('#supplier_id');
        $supplierDropdown.empty();
        $supplierDropdown.append('<option value="" disabled selected>Select Supplier</option>'); // Add default option
        $.each(suppliers, function(index, supplier) {
            $supplierDropdown.append('<option value="' + supplier.supplier_id + '">' + supplier.supplier_name + '</option>');
        });
        $supplierDropdown.val(1)

        var $invoiceTypeDropdown = $('#invoicetype');
        $invoiceTypeDropdown.empty(); // Clear existing options
        $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
        $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
        $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
        $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
        $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
        $invoiceTypeDropdown.val('cash');

        var $incidenttypeDropdown = $('#incidenttype');
        $incidenttypeDropdown.empty();
        $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
        $incidenttypeDropdown.append('<option value="1">International Purchase</option>');
        $incidenttypeDropdown.append('<option value="2">Local Purchase</option>');
        $incidenttypeDropdown.val(2)


        for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
        }

        document.querySelectorAll('.vathidden').forEach(el => {
            el.style.display = 'none';
        });

        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/getPurchaseOrderById',
                type: 'POST',
                data: {
                    id: id,
                    type2: type2
                },
                success: function(response) {
                    var purchases = JSON.parse(response);
                    document.getElementById('date').value = purchases[0].date;
                    document.getElementById('details').value = purchases[0].details;
                    if (purchases[0].chalan_no && document.getElementById('po_edit_label')) {
                        document.getElementById('po_edit_label').textContent = 'Purchase Order ' + purchases[0].chalan_no;
                    }

                    getBranchDropdown(purchases[0].branch);



                    $supplierDropdown.val(purchases[0].supplier_id)



                    var $invoiceTypeDropdown = $('#invoicetype');
                    $invoiceTypeDropdown.empty(); // Clear existing options
                    $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                    $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                    $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                    $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                    $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                    $invoiceTypeDropdown.val(purchases[0].invoicetype);


                    var $incidenttypeDropdown = $('#incidenttype');
                    $incidenttypeDropdown.empty();
                    $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
                    $incidenttypeDropdown.append('<option value="1">International Purchase</option>');
                    $incidenttypeDropdown.append('<option value="2">Local Purchase</option>');
                    $incidenttypeDropdown.val(purchases[0].incidenttype)

                    document.getElementById('total_discount_ammount').value = purchases[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').value = purchases[0].total_vat_amnt;
                    document.getElementById('grandTotal').value = purchases[0].grandTotal;
                    document.getElementById('Total').value = purchases[0].total;
                    document.getElementById('discount').value = purchases[0].discount;


                    // count = 1;
                    for (let i = 0; i < purchases.length; i++) {
                        let a = i + 1;
                        document.getElementById('myRow' + a).style.display = 'table-row';
                        // getActiveProduct(purchases[i].product, a);
                        getActiveStore(purchases[i].store, a);
                        document.getElementById('product' + a).value = purchases[i].product;
                        document.getElementById('productInput' + a).value = purchases[i].product_name;
                        document.getElementById('qty' + a).value = purchases[i].quantity;
                        document.getElementById('unit' + a).value = purchases[i].unit;
                        document.getElementById('code' + a).value = purchases[i].avstock;
                        document.getElementById('product_rate' + a).value = purchases[i].product_rate;
                        document.getElementById('discount' + a).value = purchases[i].discount2;
                        document.getElementById('discount_value' + a).value = purchases[i].discount_value;
                        document.getElementById('mastercost_price' + a).value = purchases[i].cost_price;


                        // if (vtinfo.ischecked == 1) {
                        //     document.getElementById('vat_percent' + a).value = purchases[i].vat_percent;
                        // }
                        if (purchases[0].invoicetype == 'cash_vat' ||
                            purchases[0].invoicetype == 'credit_vat' ||
                            purchases[0].invoicetype == 'svat') {
                            document.getElementById('vat_percent' + a).value = purchases[i].vat_percent;
                            document.querySelectorAll('.vathidden').forEach(el => {
                                el.style.display = 'table-cell';
                            });
                            document.querySelectorAll('.vatshow').forEach(el => {
                                el.style.display = 'none';
                            });
                        } else {
                            document.getElementById('vat_percent' + a).value = 0;

                        }
                        document.getElementById('vat_value' + a).value = purchases[i].vat_value;
                        document.getElementById('total_price' + a).value = purchases[i].total_price;
                        document.getElementById('total_discount' + a).value = purchases[i].total_discount;
                        document.getElementById('all_discount' + a).value = purchases[i].all_discount;

                        getActiveSubUnitEdit(purchases[i].product, a, purchases[i].unit, purchases[i].conversion_id,
                            purchases[i].conversion_ratio, purchases[i].convertiontype,
                            purchases[i].avstock)

                        //getBatchDropdown(batches, a, purchases[i].batch)
                        getBatchDropdown(batches, a, purchases[i].batch, purchases[i].product, purchases[i].batchtype)



                        count = count + 1;
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            }); // 2000 milliseconds = 2 seconds delay
        } else {
            getBranchDropdown(0);

        }
    });

    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, count);
        // getActiveProduct(0, count)
        count = count + 1;

    }

    function product_search(item, name) {
        if (name === "product") {
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            document.getElementById('code' + item).value = "";
            document.getElementById('qty' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";


            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    getActiveSubUnit(document.getElementById('product' + item).value, item)

                    setTimeout(
                        $.ajax({
                            url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                            type: 'POST',
                            data: {
                                prodid: document.getElementById('product' + item).value.toString(),
                            },
                            success: function(response2) {
                                if (response2 != "null") {
                                    let product2 = JSON.parse(response2);
                                    document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                    document.getElementById('bd' + item).value = product[0].unit_name
                                    document.getElementById('ad' + item).value = product2[0].unit_name
                                } else {
                                    document.getElementById('mconversion_ratio' + item).value = ""
                                    document.getElementById('bd' + item).value = ""
                                    document.getElementById('ad' + item).value = ""
                                }

                                document.getElementById('isstock' + item).value = product[0].stock

                                document.getElementById('codetype' + item).innerHTML = "";

                                if (document.getElementById('isstock' + item).value == 1) {

                                    avStock(item, document.getElementById('product' + item).value, product[0].store, 1, "", "")
                                }
                                //   document.getElementById('unit' + item).value = product[0].unit;
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        }), 1000);

                    getActiveStore(product[0].store, item);
                    getBatchDropdown(batches, item, 0, document.getElementById('product' + item).value.toString(), product[0].batchtype);
                    // avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('product_rate' + item).value = product[0].cost_price;
                    document.getElementById('mastercost_price' + item).value = product[0].cost_price;
                    if (document.getElementById('invoicetype').value == 'cash_vat' ||
                        document.getElementById('invoicetype').value == 'credit_vat' ||
                        document.getElementById('invoicetype').value == 'svat') {
                        document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    } else {
                        document.getElementById('vat_percent' + item).value = 0;

                    }
                    //document.getElementById('vat_value' + item).value = 0;



                },
                error: function(error) {
                    console.log(error)
                }
            });
        }



        if (name === "batch") {
            if(document.getElementById('isstock' + item).value==1){
                 avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
             } 
                         getActiveSubUnit(document.getElementById('product' + item).value, item)

        }


        if (name === "store") {
            if(document.getElementById('isstock' + item).value==1){
                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
             } 
                         getActiveSubUnit(document.getElementById('product' + item).value, item)

        }

        if (name === "unit") {
            let select = document.getElementById('unit' + item);
            let selectedText = select.options[select.selectedIndex].text;
            if(document.getElementById('isstock' + item).value==1){
            convertion(item, document.getElementById('product' + item).value, document.getElementById('unit' + item).value, selectedText)
             }              // avStock(item,document.getElementById('product' + item).value,document.getElementById('store' + item).value,0)
            // getActiveSubUnit(document.getElementById('product' + item).value,item)

        }
    }

    function avStock(item, product, store, batch, convertiontype, conversion_ratio) {
        document.getElementById('code' + item).value = "";
        document.getElementById('qty' + item).value = "";
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/avg_avstock',
            type: 'POST',
            data: {
                prodid: product,
                storeid: store,
                batch: batch
            },
            success: function(response) {
                let stock = JSON.parse(response);
                let el = document.getElementById('codetype' + item);
                el.style.color = '#8b5cf6';
                el.style.fontWeight = 'bold';
                el.innerHTML = ""
                let select = document.getElementById('unit' + item);
                let selectedText = select.options[select.selectedIndex].text;


                if (convertiontype == "*") {
                    document.getElementById('code' + item).value = (stock[0].avgqty * conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty * conversion_ratio;
                    let sub2 = Math.floor((sub).toLocaleString());
                    if (isNaN(sub2)) {
                        sub = Number(sub).toFixed(6);
                        el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText
                    } else {
                        el.innerHTML = sub2 + " " + selectedText

                    }

                } else if (convertiontype == "/") {
                    document.getElementById('code' + item).value = (stock[0].avgqty / conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty / conversion_ratio)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "+") {
                    document.getElementById('code' + item).value = (stock[0].avgqty + conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty + conversion_ratio)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "-") {
                    document.getElementById('code' + item).value = (stock[0].avgqty - conversion_ratio).toFixed(2)
                    el.innerHTML = (Math.floor(stock[0].avgqty - conversion_ratio)).toLocaleString() + " " + selectedText

                } else {

                    if (document.getElementById('mconversion_ratio' + item).value != "") {


                        let totalcount = 0;
                        let mas = document.getElementById('mconversion_ratio' + item).value * stock[0].avgqty / document.getElementById('mconversion_ratio' + item).value;
                        let subcount = 0;
                        let sub = document.getElementById('mconversion_ratio' + item).value * stock[0].avgqty % document.getElementById('mconversion_ratio' + item).value;


                        let mas2 = Math.floor((mas).toLocaleString());
                        if (isNaN(mas2)) {
                            mas = Number(mas).toFixed(6);
                            totalcount = (Math.floor(mas)).toLocaleString()
                        } else {
                            totalcount = mas2

                        }

                        let sub2 = Math.floor((sub).toLocaleString());
                        if (isNaN(sub2)) {
                            sub = Number(sub).toFixed(6);
                            subcount = (Math.floor(sub)).toLocaleString()
                        } else {
                            subcount = sub2

                        }
                        document.getElementById('code' + item).value = stock[0].avgqty == null ? 0 : totalcount;


                        el.innerHTML = totalcount + document.getElementById('bd' + item).value + " " + subcount + document.getElementById('ad' + item).value;
                    } else {
                        document.getElementById('code' + item).value = stock[0].avgqty == null ? 0 : stock[0].avgqty;
                        el.innerHTML = (Math.floor(stock[0].avgqty)).toLocaleString() + " " + selectedText

                    }
                }
            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';

        document.getElementById('qty' + num).value = 0;
        document.getElementById('product_rate' + num).value = 0;
        document.getElementById('discount' + num).value = 0;
        document.getElementById('discount_value' + num).value = 0;
        document.getElementById('vat_percent' + num).value = 0;
        document.getElementById('vat_value' + num).value = 0;
        document.getElementById('total_price' + num).value = 0;
        document.getElementById('total_discount' + num).value = 0;
        document.getElementById('all_discount' + num).value = 0;
        calculate_sum(num)
    }



    function calculate_sum(sl) {

        var p = 0;
        var v = 0;
        var gr_tot = 0;
        var dis = 0;
        var item_ctn_qty = $("#qty" + sl).val();
        var vendor_rate = $("#product_rate" + sl).val();

        var total_price = item_ctn_qty * vendor_rate;
        $("#total_price" + sl).val(total_price.toFixed(2));

        var quantity = $("#qty" + sl).val();
        var discount = $("#discount" + sl).val();
        var dis_type = $("#discount_type").val();
        var price_item = $("#product_rate" + sl).val();
        var vat_percent = 0;
        if (document.getElementById('invoicetype').value == 'cash_vat' ||
            document.getElementById('invoicetype').value == 'credit_vat' ||
            document.getElementById('invoicetype').value == 'svat') {
            vat_percent = $("#vat_percent" + sl).val();



        }
        var code = $("#code" + sl).val();


        // if (parseInt(quantity) > parseInt(code)) {
        //     $("#qty" + sl).val("");
        //     alert("Quantity shouldn't be greater than available quantity");
        //     return;
        // }

        if (quantity > 0 || discount > 0 || vat_percent > 0) {
            if (dis_type == 1) {
                var price = quantity * price_item;
                var disc = +(price * discount / 100);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var temp = price - disc;
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end
                var ttletax = 0;
                $("#total_price" + sl).val(temp);



            } else if (dis_type == 2) {
                var price = quantity * price_item;

                // Discount cal per product
                var disc = (discount * quantity);
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);

                //Total price calculate per product
                var temp = price - disc;
                $("#total_price" + sl).val(temp);
                // product wise vat start
                var vat = +(temp * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                var ttletax = 0;

            } else if (dis_type == 3) {
                var total_price = quantity * price_item;
                var disc = discount;
                // Discount cal per product
                $("#discount_value" + sl).val(disc);
                $("#all_discount" + sl).val(disc);
                //Total price calculate per product
                var price = total_price - disc;
                $("#total_price" + sl).val(price);
                // product wise vat start
                var vat = +(price * vat_percent / 100);
                $("#vat_value" + sl).val(vat);
                // product wise vat end

                $("#total_price" + sl).val(price);


                var ttletax = 0;

            }
        }

        //Total Price
        $(".total_price").each(function() {
            isNaN(this.value) || 0 == this.value.length || (gr_tot += parseFloat(this.value))
        });
        $(".discount").each(function() {
            isNaN(this.value) || 0 == this.value.length || (dis += parseFloat(this.value))
        });
        //Total Discount
        $(".total_discount_val").each(function() {
                isNaN(this.value) || 0 == this.value.length || (p += parseFloat(this.value))
            }),
            $("#total_discount_ammount").val(p.toFixed(2, 2)),

            $(".total_vatamnt").each(function() {
                isNaN(this.value) || 0 == this.value.length || (v += parseFloat(this.value))
            }),
            $("#total_vat_amnt").val(v.toFixed(2, 2)),

            $("#Total").val(gr_tot.toFixed(2, 2));
        var vatamnt = $("#total_vat_amnt").val();

        var gttl = gr_tot - dis;
        var grandtotal = parseFloat(gttl) + parseFloat(vatamnt);
        $("#grandTotal").val(grandtotal.toFixed(2, 2));
        // $("#pamount_by_method").val(grandtotal.toFixed(2, 2));

        // $('#paidAmount').val(grandtotal.toFixed(2, 2));

        var purchase_edit_page = $("#purchase_edit_page").val();
        $("#add_new_payment").empty();

        $("#pay-amount").text('0');
        //   $("#dueAmmount").val(0);

        if (purchase_edit_page == 1) {

            var base_url = $('#base_url').val();
            var is_credit_edit = $('#is_credit_edit').val();

            var csrf_test_name = $('[name="csrf_test_name"]').val();
            var gtotal = $(".grandTotalamnt").val();
            var url = base_url + "purchase/purchase/bdtask_showpaymentmodal";
            $.ajax({
                type: "post",
                url: url,
                data: {
                    is_credit_edit: is_credit_edit,
                    csrf_test_name: csrf_test_name
                },
                success: function(data) {


                    $('#add_new_payment').append(data);

                    //  $("#pamount_by_method").val(gtotal);
                    $("#add_new_payment_type").prop('disabled', false);
                    var card_typesl = $('.card_typesl').val();

                    if (card_typesl == 0) {
                        $("#add_new_payment_type").prop('disabled', true);
                    }

                }
            });

        }

    }

    function getActiveProduct(productId, item) {
        var $productDropdown = $('#product' + item);
        $productDropdown.empty();
        $productDropdown.append('<option value="" disabled selected>Select Product</option>'); // Add default option

        $.each(products, function(index, product) {
            $productDropdown.append('<option value="' + product.id + '">' + product.product_name + '</option>');
        });

        if (productId > 0) {
            {
                $productDropdown.val(productId)
            }
        }
    }




    function getActiveStore(storeId, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        if(storeId==1){
            $storeDropdown.append('<option value="1">N/A</option>');
        }
        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
            }
        }
    }

    function getStoresDropdown(stores, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });
    }

    function getBranchDropdown(branchId) {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "store/store/getbranchbyuserid",
            data: {
                // is_credit_edit: is_credit_edit,
                // csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var branches = JSON.parse(data);
                console.log(branches)
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id)
                    }
                });

                if (branchId > 0) {
                    {
                        $branchDropdown.val(branchId)
                    }
                }



            }
        });
    }


    function restoreSaveBtn() {
        var $b = $('#save_add');
        $b.prop('disabled', false).removeClass('btn-save-anim-loading').html($b.data('origHtml'));
    }

    function save() {
        arrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('supplier_id').value === "" || document.getElementById('supplier_id').value === " ") {
                    alert("Supplier shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('branch').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
                    return

                } else if (document.getElementById('unit' + i).value == "") {
                    alert("Unit shouldn't be empty")
                    return

                } else
                if (document.getElementById('product_rate' + i).value == "") {
                    alert("Price shouldn't be empty")
                    return
                } else if (document.getElementById('invoicetype').value == "") {
                    alert("Invoice Type shouldn't be empty")
                    return
                } else if (document.getElementById('incidenttype').value == "") {
                    alert("Incident Type shouldn't be empty")
                    return
                } else {
                    var dropdown = document.getElementById('product' + i);

                    let qty = 0;
                    if (document.getElementById('conversiontype' + i).value == "+") {
                        qty = document.getElementById('qty' + i).value - document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "-") {
                        qty = document.getElementById('qty' + i).value + document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "*") {
                        qty = document.getElementById('qty' + i).value / document.getElementById('conversion_ratio' + i).value
                    } else
                    if (document.getElementById('conversiontype' + i).value == "/") {
                        qty = document.getElementById('qty' + i).value * document.getElementById('conversion_ratio' + i).value
                    } else {
                        qty = document.getElementById('qty' + i).value
                    }


                    arrItem.push({
                        product: document.getElementById('product' + i).value,
                        product_name: document.getElementById('productInput' + i).value,
                        store: document.getElementById('store' + i).value,
                        quantity: qty,
                        product_rate: document.getElementById('product_rate' + i).value,
                        batch: document.getElementById('batch' + i).value,
                        discount: document.getElementById('discount' + i).value,
                        discount_value: document.getElementById('discount_value' + i).value,
                        vat_percent: document.getElementById('vat_percent' + i).value,
                        vat_value: document.getElementById('vat_value' + i).value,
                        total_price: document.getElementById('total_price' + i).value,
                        total_discount: document.getElementById('total_discount' + i).value,
                        all_discount: document.getElementById('all_discount' + i).value,
                        unit: document.getElementById('unit' + i).value,
                        conversionid: document.getElementById('conversionid' + i).value,
                    });
                }
            }

        }

        (function () {
            var $b = $('#save_add');
            if (typeof $b.data('origHtml') === 'undefined') $b.data('origHtml', $b.html());
            var isEdit = (id > 0);
            $b.prop('disabled', true).addClass('btn-save-anim-loading')
              .html('<i class="fa fa-spinner fa-spin"></i> ' + (isEdit ? 'Updating...' : 'Saving...'));
        })();

        if (id > 0) {
            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/update_purchase_order',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    type2: 'C',
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    chalan_no: 0,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,

                },
                success: function(response) {
                    try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Update failed. Please try again."));
                        return;
                    }
                    clearDetails()
                    restoreSaveBtn();
                    alert("Purchase Order Details Updated Successfully")
                    // window.location.href = $('#base_url').val() + 'purchase_list';
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.chalan_no, datas.invoiceno);
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Update failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'purchase/purchase/save_purchase_order',
                type: 'POST',
                data: {
                    items: arrItem,
                    type2: 'C',
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    supplier_id: document.getElementById('supplier_id').value,
                    chalan_no: 0,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,
                },
                success: function(response) {
                    try { datas = JSON.parse(response); } catch(e) { datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Save failed. Please try again."));
                        return;
                    }
                    clearDetails()
                    restoreSaveBtn();
                    alert("Purchase Order Details saved Successfully")
                    // window.location.href = $('#base_url').val() + 'purchase_list';
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.chalan_no, datas.invoiceno);
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Save failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });

        }

    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
            document.getElementById('productInput' + i).value = '';
            document.getElementById('product' + i).value = '';


            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

            $.each(stores, function(index, store) {
                $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            });

            var $batchDropdown = $('#batch' + i);
            $batchDropdown.empty();

            var $unitDropdown = $('#unit' + i);
            $unitDropdown.empty();

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
            document.getElementById('code' + i).value = "";
            document.getElementById('codetype' + i).innerHTML = "";
            document.getElementById('unit' + i).value = "";

            document.getElementById('product_rate' + i).value = "";
            document.getElementById('discount' + i).value = "";
            document.getElementById('discount_value' + i).value = "";
            document.getElementById('vat_percent' + i).value = "";
            document.getElementById('vat_value' + i).value = "";
            document.getElementById('total_price' + i).value = "";
            document.getElementById('total_discount' + i).value = "";
            document.getElementById('all_discount' + i).value = "";

        }
        document.getElementById('myRow1').style.display = 'table-row';

        document.getElementById('discount').value = ""
        document.getElementById('total_discount_ammount').value = ""
        document.getElementById('total_vat_amnt').value = ""
        document.getElementById('grandTotal').value = ""
        document.getElementById('date').value = ""
        document.getElementById('details').value = ""
        document.getElementById('Total').value = ""
        document.getElementById('supplier_id').value = ""

        var $customerDropdown = $('#supplier_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(suppliers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.supplier_id + '">' + customer.supplier_name + '</option>');
        });
    }

    function clearDetails2() {
        for (let i = 1; i < 20; i++) {
            document.getElementById('productInput' + i).value = '';
            document.getElementById('product' + i).value = '';


            var $storeDropdown = $('#store' + i);
            $storeDropdown.empty();
            $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

            $.each(stores, function(index, store) {
                $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
            });

            var $batchDropdown = $('#batch' + i);
            $batchDropdown.empty();

            var $unitDropdown = $('#unit' + i);
            $unitDropdown.empty();

            document.getElementById('myRow' + i).style.display = 'none';
            document.getElementById('qty' + i).value = "";
            document.getElementById('code' + i).value = "";
            document.getElementById('codetype' + i).innerHTML = "";
            document.getElementById('unit' + i).value = "";

            document.getElementById('product_rate' + i).value = "";
            document.getElementById('discount' + i).value = "";
            document.getElementById('discount_value' + i).value = "";
            document.getElementById('vat_percent' + i).value = "";
            document.getElementById('vat_value' + i).value = "";
            document.getElementById('total_price' + i).value = "";
            document.getElementById('total_discount' + i).value = "";
            document.getElementById('all_discount' + i).value = "";

        }
        document.getElementById('myRow1').style.display = 'table-row';

        document.getElementById('discount').value = ""
        document.getElementById('total_discount_ammount').value = ""
        document.getElementById('total_vat_amnt').value = ""
        document.getElementById('grandTotal').value = ""
        document.getElementById('Total').value = ""


    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, chalanNo, voucherNo) {
        var fileVoucher = (chalanNo && String(chalanNo).trim() !== '') ? chalanNo : voucherNo;
        if (fileVoucher) {
            document.title = 'purchaseorder_' + fileVoucher + '_' + pdfDateStamp();
        }

        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload();
            })
        });
    }

    function getBatchDropdown(batches, item, value, product, batchtype) {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchbyProductAndBatchtype',
            type: 'POST',
            data: {
                product: product,
                batchtype: batchtype,
               id:id
            },
            success: function(response2) {
                var $batchDropdown = $('#batch' + item);
                $batchDropdown.empty();
                $batchDropdown.append('<option value="" disabled selected>Select Batch</option>'); // Add default option
                if (response2 != "not") {
                    let batches2 = JSON.parse(response2);
                    // console.log(batches2)
                    $.each(batches2, function(index, batch) {
                        $batchDropdown.append('<option value="' + batch.id + '">' + batch.batchid + '</option>');
                    });
                }
                if (value === 0 || value === '0') {
                    // select first real batch (index 1 if "Select Batch" exists)
                    if ($batchDropdown.find('option').length > 1) {
                        $batchDropdown.prop('selectedIndex', 1);
                    }
                } else {
                    $batchDropdown.val(value);
                }




            },
            error: function(error) {
                console.log(error)
            }
        });




    }

    function getActiveSubUnit(productId, item) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                // alert("Invoice Details Updated Successfully")
                // window.location.href = $('#base_url').val() + 'invoice_list';
                datas = JSON.parse(response);
                console.log(datas)
                var $subunitDropdown = $('#unit' + item);
                document.getElementById('conversionid' + item).value = "";
                document.getElementById('conversiontype' + item).value = "";
                document.getElementById('conversion_ratio' + item).value = "";


                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>'); // Add default option
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');
                $subunitDropdown.val(datas[0].unit)

                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });



            },
            error: function(error) {
                console.log(error)
            }
        });
    }

    function getActiveSubUnitEdit(productId, item, value, conversion_id, conversion_ratio, cconvertiontype, avstock) {
        $.ajax({
            url: $('#base_url').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                datas = JSON.parse(response);
                var $subunitDropdown = $('#unit' + item);
                if (conversion_id != "0") {
                    document.getElementById('conversionid' + item).value = conversion_id;
                    document.getElementById('conversiontype' + item).value = cconvertiontype;
                    document.getElementById('conversion_ratio' + item).value = conversion_ratio;
                } else {
                    document.getElementById('conversionid' + item).value = "";
                    document.getElementById('conversiontype' + item).value = "";
                    document.getElementById('conversion_ratio' + item).value = "";
                }



                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select unit</option>'); // Add default option
                $subunitDropdown.append('<option value="' + datas[0].unit + '">' + datas[0].name2 + '</option>');

                $.each(datas, function(index, store) {
                    $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                });

                $subunitDropdown.val(value)

                document.getElementById('isstock' + item).value = datas[0].stock


                if (datas[0].stock == 0) {
                    return
                }

                let select = document.getElementById('unit' + item);
                let selectedText = select.options[select.selectedIndex].text;
                let el = document.getElementById('codetype' + item);
                el.style.color = '#8b5cf6';
                el.style.fontWeight = 'bold';
                let sub2 = Math.floor((parseFloat(avstock)).toLocaleString());

                if (isNaN(sub2)) {
                    avstock = Number(avstock).toFixed(6);
                    el.innerHTML = (Math.floor(avstock)).toLocaleString() + " " + selectedText
                } else {
                    el.innerHTML = sub2 + " " + selectedText

                }


                if (value == datas[0].unit) {

                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: productId,
                        },
                        success: function(response2) {
                            if (response2 != "null") {

                                let product2 = JSON.parse(response2); //console.log(adjStocks[i].actualstock*product2[0].conversion_ratio)
                                // document.getElementById('code' + item).value = avstock * product2[0].conversion_ratio;
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product2[0].unit2
                                document.getElementById('ad' + item).value = product2[0].unit_name
                                el.style.color = '#8b5cf6';
                                el.style.fontWeight = 'bold';
                                el.innerHTML = ""
                                let totalcount = 0;
                                let mas = document.getElementById('mconversion_ratio' + item).value * avstock / document.getElementById('mconversion_ratio' + item).value;
                                let subcount = 0;
                                let sub = document.getElementById('mconversion_ratio' + item).value * avstock % document.getElementById('mconversion_ratio' + item).value;


                                let mas2 = Math.floor((mas).toLocaleString());
                                if (isNaN(mas2)) {
                                    mas = Number(mas).toFixed(6);
                                    totalcount = (Math.floor(mas)).toLocaleString()
                                } else {
                                    totalcount = mas2

                                }

                                let sub2 = Math.floor((sub).toLocaleString());
                                if (isNaN(sub2)) {
                                    sub = Number(sub).toFixed(6);
                                    subcount = (Math.floor(sub)).toLocaleString()
                                } else {
                                    subcount = sub2

                                }
                                // if (stocktype != "both") {
                                document.getElementById('code' + item).value = avstock == null ? 0 : totalcount;
                                el.innerHTML = (totalcount + document.getElementById('bd' + item).value + " " + subcount + document.getElementById('ad' + item).value).toLocaleString();
                                // }
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = ""
                                document.getElementById('bd' + item).value = ""
                                document.getElementById('ad' + item).value = ""
                            }
                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                } else {
                    $.ajax({
                        url: $('#base_url').val() + 'stock/stock/getproductSubUnitPrimary',
                        type: 'POST',
                        data: {
                            prodid: productId,
                        },
                        success: function(response2) {
                            if (response2 != "null") {

                                let product2 = JSON.parse(response2); //console.log(adjStocks[i].actualstock*product2[0].conversion_ratio)
                                // document.getElementById('code' + item).value = avstock * product2[0].conversion_ratio;
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product2[0].unit2
                                document.getElementById('ad' + item).value = product2[0].unit_name
                            } else {
                                document.getElementById('mconversion_ratio' + item).value = ""
                                document.getElementById('bd' + item).value = ""
                                document.getElementById('ad' + item).value = ""
                            }
                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                }






            },
            error: function(error) {
                console.log(error)
            }
        });
    }


    function convertion(item, product, unit, unitname) {

        // if (unitname.split("-")[1] == "S") {
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/conversion',
            type: 'POST',
            data: {
                product_id: product,
                unit: unit
            },
            success: function(response) {
                // alert("Invoice Details Updated Successfully")
                // window.location.href = $('#base_url').val() + 'invoice_list';
                if (response != "not") {
                    datas = JSON.parse(response);
                    document.getElementById('conversiontype' + item).value = datas[0].convertiontype
                    document.getElementById('conversionid' + item).value = datas[0].conversionratio_id
                    document.getElementById('conversion_ratio' + item).value = datas[0].conversion_ratio;
                    document.getElementById('product_rate' + item).value = datas[0].subcost_price;



                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value,
                        datas[0].convertiontype, datas[0].conversion_ratio)
                } else {
                    // alert("Conversion not found")
                    getActiveSubUnit(document.getElementById('product' + item).value, item)
                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
                    document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                }

            },
            error: function(error) {
                console.log(error)
            }
        });
        // } else {
        //     getActiveSubUnit(document.getElementById('product' + item).value, item)
        //     avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")

        // }


    }
</script>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align:center; font-weight:600;">Add New Product</h4>
            </div>
            <div class="modal-body">
                <div id="ap_loading" style="text-align:center; padding:20px; display:none;"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div id="ap_success_msg" style="display:none;"></div>
                <div id="ap_form_body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Barcode / QR Code</label>
                                <input type="text" id="ap_barcode" class="form-control" placeholder="Leave blank to auto-generate">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Product Name <span class="text-danger">*</span></label>
                                <input type="text" id="ap_product_name" class="form-control" placeholder="Enter product name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Category <span class="text-danger">*</span></label>
                                <select id="ap_category_id" class="form-control">
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
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
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Batch Type</label>
                                <select id="ap_batchtype" class="form-control">
                                    <option value="1">Single</option>
                                    <option value="2">Multiple</option>
                                    <option value="3" selected>Both</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Default Sales Price</label>
                                <select id="ap_defaultsaleprice" class="form-control">
                                    <option value="fixedprice">Fixed Price</option>
                                    <option value="mrp">MRP</option>
                                    <option value="custom" selected>Custom</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Master Stock Unit <span class="text-danger">*</span></label>
                                <select id="ap_unit" class="form-control">
                                    <option value="">Select Unit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Stock</label>
                                <select id="ap_stock" class="form-control">
                                    <option value="1">Enable</option>
                                    <option value="0" selected>Disable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Default Store</label>
                                <select id="ap_store" class="form-control">
                                    <option value="1" selected>N/A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Supplier</label>
                                <select id="ap_supplier_id" class="form-control">
                                    <option value="">Select Supplier</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label style="font-weight:700;">Fixed Purchase Price <span class="text-danger">*</span></label>
                                <input type="number" id="ap_cost_price" class="form-control" placeholder="0.00" min="0.01" step="any">
                            </div>
                        </div>
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

    var $store = $('#ap_store').empty().append('<option value="1" selected>N/A</option>');
    if (stores) $.each(stores, function(i, s) { $store.append('<option value="'+s.id+'">'+s.name+'</option>'); });

    var $sup = $('#ap_supplier_id').empty().append('<option value="">Select Supplier</option>');
    if (suppliers) $.each(suppliers, function(i, s) { $sup.append('<option value="'+s.supplier_id+'">'+s.supplier_name+'</option>'); });

    $('#ap_loading').show();
    $('#ap_form_body').hide();
    $('#ap_save_btn').prop('disabled', true);

    $.ajax({
        url: '<?php echo base_url("get_product_form_data"); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var $cat = $('#ap_category_id').empty().append('<option value="">Select Category</option>');
            $.each(data.categories, function(i, c) { $cat.append('<option value="'+c.category_id+'">'+c.category_name+'</option>'); });

            var $unit = $('#ap_unit').empty().append('<option value="">Select Unit</option>');
            $.each(data.units, function(i, u) { $unit.append('<option value="'+u.unit_id+'">'+u.unit_name+'</option>'); });

            $('#ap_loading').hide();
            $('#ap_form_body').show();
            $('#ap_save_btn').prop('disabled', false);
        },
        error: function() {
            $('#ap_loading').hide();
            $('#ap_form_body').show();
            $('#ap_save_btn').prop('disabled', false);
        }
    });

    $('#addProductModal').modal('show');
}
function saveNewProduct() {
    var name = $('#ap_product_name').val().trim();
    var cat  = $('#ap_category_id').val();
    var unit = $('#ap_unit').val();
    if (!name) { alert('Product name is required.'); return; }
    if (!cat)  { alert('Category is required.'); return; }
    if (!unit) { alert('Master Stock Unit is required.'); return; }
    if (!$('#ap_cost_price').val() || parseFloat($('#ap_cost_price').val()) <= 0) {
        alert('Fixed Purchase Price is required and must be greater than 0.');
        $('#ap_cost_price').focus();
        return;
    }

    $('#ap_save_btn').prop('disabled', true).text('Saving...');
    $.ajax({
        url:  '<?php echo base_url("save_product_ajax"); ?>',
        type: 'POST',
        data: {
            product_id:       $('#ap_barcode').val().trim(),
            product_name:     name,
            category_id:      cat,
            product_type:     $('#ap_product_type').val(),
            batchtype:        $('#ap_batchtype').val(),
            defaultsaleprice: $('#ap_defaultsaleprice').val(),
            unit:             unit,
            stock:            $('#ap_stock').val(),
            store:            $('#ap_store').val() || 1,
            supplier_id:      $('#ap_supplier_id').val() || 0,
            status:           '1',
            subcategory_id:   0,
            brand_id:         0,
            oop_id:           '',
            vat:              '0',
            sell_price:       '0',
            cost_price:       $('#ap_cost_price').val()
        },
        dataType: 'json',
        success: function(r) {
            $('#ap_save_btn').prop('disabled', false).text('Save Product');
            if (r.status === 'Success') {
                var pname    = $('#ap_product_name').val().trim();
                var unitId   = $('#ap_unit').val();
                var unitName = $('#ap_unit option:selected').text();

                // Add to products array
                if (products) products.push({ id: r.id, product_name: pname, unit: unitId, unit_name: unitName });

                // Load into the table row
                var rowNum;
                var lastProd = document.getElementById('product' + (count - 1));
                if (lastProd && lastProd.value == '') {
                    rowNum = count - 1;
                    getActiveStore(0, rowNum);
                } else {
                    rowNum = count;
                    document.getElementById('myRow' + rowNum).style.display = 'table-row';
                    getActiveStore(0, rowNum);
                    count = count + 1;
                }
                document.getElementById('productInput' + rowNum).value = pname;
                document.getElementById('product' + rowNum).value = r.id;
                product_search(rowNum, 'product');
                $('#addProductModal').modal('hide');
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addBatchModal" tabindex="-1" role="dialog" aria-labelledby="addBatchModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="addBatchModalLabel">Add New Batch</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Batch ID <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mb_batchid" placeholder="Batch ID" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Details</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="mb_details" placeholder="Details" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Batch Usage Type <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="mb_busage" onchange="modalChangeBatchtype()">
                            <option value="">Select One</option>
                            <option value="single">Single Usage</option>
                            <option value="multiple">Multiple Usage</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row" id="mb_singleshow" style="display:none;">
                    <label class="col-sm-3 col-form-label">Product <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <div style="position:relative;display:inline-block;width:100%;">
                            <input class="form-control" type="text" id="mb_productInput" placeholder="Product..." onkeyup="handleMbProductKeyPress(event)" autocomplete="off" />
                            <input type="text" id="mb_product" hidden />
                            <div id="mb_productResults" style="position:absolute;z-index:99999!important;max-height:150px;overflow-y:auto;border:1px solid #ddd;background-color:#fff;border-radius:4px;box-shadow:0 4px 6px rgba(0,0,0,0.1);" autocomplete="off"></div>
                        </div>
                    </div>
                </div>
                <div class="form-group row" id="mb_singleshow1" style="display:none;">
                    <label class="col-sm-3 col-form-label">Manufacturing Date</label>
                    <div class="col-sm-9">
                        <input type="text" class="datepicker form-control" id="mb_mdate" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row" id="mb_singleshow2" style="display:none;">
                    <label class="col-sm-3 col-form-label">Expiry Date</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="mb_edate_toggle" onchange="modalToggleEdate()">
                            <option value="no" selected>Disable</option>
                            <option value="yes">Enable</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row" id="mb_edate_row" style="display:none;">
                    <label class="col-sm-3 col-form-label">Select Expiry Date</label>
                    <div class="col-sm-9">
                        <input type="text" class="datepicker form-control" id="mb_edate" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row" id="mb_singleshow3" style="display:none;">
                    <label class="col-sm-3 col-form-label">Packing Date</label>
                    <div class="col-sm-9">
                        <input type="text" class="datepicker form-control" id="mb_pdate" placeholder="YYYY-MM-DD" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row" id="mb_singleshow4" style="display:none;">
                    <label class="col-sm-3 col-form-label">MRP</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" id="mb_mrp" placeholder="0.00" min="0" step="0.01" autocomplete="off">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-form-label">Status <i class="text-danger">*</i></label>
                    <div class="col-sm-9">
                        <select class="form-control" id="mb_status">
                            <option value="">Select One</option>
                            <option value="1" selected>Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="mb_save_btn" onclick="saveModalBatch()">Save Batch</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="text-align:center; font-weight:600;">Add New Supplier</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label style="font-weight:700;">Supplier Name</label>
                    <input type="text" id="modal_supplier_name" class="form-control" placeholder="Enter supplier name">
                </div>
                <div class="form-group">
                    <label style="font-weight:700;">Phone Number</label>
                    <input type="text" id="modal_supplier_phone" class="form-control" placeholder="Enter phone number">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="quick_supplier_save_btn" class="btn btn-primary" onclick="saveNewSupplier()">Save</button>
            </div>
        </div>
    </div>
</div>
<script>
$(function() {
    wireDuplicateCheck({
        input: '#modal_supplier_name',
        url: $('#base_url').val() + 'supplier/supplier/check_duplicate_supplier',
        saveButtons: '#quick_supplier_save_btn'
    });
});
function openAddSupplierModal() {
    $('#modal_supplier_name').val('');
    $('#modal_supplier_phone').val('');
    $('#addSupplierModal').modal('show');
}
function saveNewSupplier() {
    var name  = $('#modal_supplier_name').val().trim();
    var phone = $('#modal_supplier_phone').val().trim();
    if (!name) { alert('Supplier name is required.'); return; }
    $.ajax({
        url:  '<?php echo base_url("save_supplier_ajax"); ?>',
        type: 'POST',
        data: { supplier_name: name, supplier_phone: phone },
        dataType: 'json',
        success: function(r) {
            if (r.inserted_id) {
                $('#supplier_id').append('<option value="' + r.inserted_id + '">' + r.supplier_name + '</option>');
                $('#supplier_id').val(r.inserted_id).trigger('change');
                $('#addSupplierModal').modal('hide');
            } else {
                alert('Failed to save supplier.');
            }
        },
        error: function() { alert('Failed to save supplier.'); }
    });
}
$('#addProductModal').on('shown.bs.modal', function() {
    $('#addProductModal select.form-control').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({ placeholder: 'Select option', allowClear: true, dropdownParent: $('#addProductModal') });
        }
    });
    $('#ap_product_name').focus();
});
$('#addSupplierModal').on('shown.bs.modal', function() {
    $('#modal_supplier_name').focus();
});
/* ── Override window.alert to use toastr (brand page style) ── */
</script>