<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>

<style>
    /* JS-required */
    .highlight { background-color: #8b5cf6 !important; color: #fff !important; }

    .col-big    { width: 15% !important; }
    .col-total  { width: 20% !important; }
    .col-medium { width: 8%  !important; }
    .vathidden  { width: 8%  !important; }
    .col-small  { width: 7%  !important; }

    .star-icon {
        background: linear-gradient(135deg,#28a745,#20c997);
        color: #fff; width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; box-shadow: 0 2px 5px rgba(0,0,0,.2);
        cursor: pointer; transition: .2s;
    }
    .star-icon:hover { transform: scale(1.1); }

    .star-icon-red {
        background: linear-gradient(135deg,#dc3545,#ff6b6b);
        color: #fff; width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 11px; box-shadow: 0 2px 5px rgba(0,0,0,.2);
        cursor: pointer; transition: .2s;
    }
    .star-icon-red:hover { transform: scale(1.1); }

    /* Panel */
    .inv-panel { border-radius: 6px; box-shadow: 0 2px 10px rgba(0,0,0,.1); }
    .inv-panel > .panel-heading { border-radius: 5px 5px 0 0; }

    /* Header */
    .inv-header { padding: 12px 18px !important; }
    .inv-header-flex { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    .inv-page-title { font-size: 16px; font-weight: 600; }
    .inv-header-btns { display: flex; gap: 6px; flex-wrap: wrap; }
    .inv-header-btns .btn.btn-info,
    .padding-lefttitle .btn.btn-info { background: #2563EB !important; border-color: #1D4ED8 !important; color: #fff !important; }
    .inv-header-btns .btn.btn-info:hover,
    .padding-lefttitle .btn.btn-info:hover { background: #1D4ED8 !important; }

    /* Form section */
    .inv-form-section { padding: 16px 18px 8px; }
    .inv-form-section .form-group { margin-bottom: 10px; }
    .inv-form-section label { font-weight: 600; font-size: 13px; }

    /* hide mobile labels on desktop */
    .td-mobile-label { display: none; }

    /* Table desktop */
    #saleTable { font-size: 12px; }
    #saleTable thead th { white-space: nowrap; font-size: 11px; padding: 8px 6px; font-weight: 700; }
    #saleTable tbody tr td { padding: 4px 5px; vertical-align: middle; }
    #saleTable tbody td .form-control { height: 30px; font-size: 12px; padding: 2px 6px; }
    #saleTable tbody td select.form-control { padding: 2px 4px; }
    #saleTable tfoot tr td { font-size: 13px; padding: 6px 8px; }
    #saleTable tfoot .form-control { height: 32px; font-size: 13px; }

    /* Batch column — desktop only */
    @media (min-width: 1025px) {
        #saleTable thead th:nth-child(3),
        #saleTable tbody td:nth-child(3) { min-width: 120px; width: 120px; }
    }

    /* Av.Qty column */
    #saleTable thead th:nth-child(5),
    #saleTable tbody td:nth-child(5) { min-width: 140px; width: 140px; }

    /* Delete button in rows 2-20 (btn-sm) — match row 1 size */
    #saleTable tbody td .btn-danger.btn-sm {
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857;
    }

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

        .table-responsive { overflow: visible !important; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }

        .td-mobile-label { display: block; }

        #saleTable tbody tr {
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
        #saleTable tbody tr[style*="table-row"] {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            width: 100% !important;
        }

        #saleTable tbody td {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 8px 12px;
            border: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            border-right: 1px solid #f0f0f0 !important;
            white-space: normal;
        }
        #saleTable tbody td:nth-child(even) { border-right: none !important; }

        #saleTable tbody td:last-child {
            grid-column: 1 / -1;
            border-bottom: none !important;
            border-right: none !important;
            padding: 0;
        }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button,
        #saleTable tbody td:last-child .btn {
            width: 100%; border-radius: 0; margin: 0; display: block;
        }
        #saleTable tbody td:last-child > div { width: 100% !important; }
        #saleTable tbody td:last-child > div .btn { flex: 1; }

        #saleTable tbody td .form-control,
        #saleTable tbody td > select,
        #saleTable tbody td > div,
        #saleTable tbody td .chosen-container,
        #saleTable tbody td .select2-container { width: 100% !important; box-sizing: border-box; }

        #saleTable tfoot {
            display: block; margin-top: 14px;
            border: 1px solid #e0e0e0; border-radius: 10px; background: #fff;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important;
            width: 100%; box-sizing: border-box;
            padding: 9px 14px;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px;
            margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] input[type="text"] {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: 34px !important; text-align: right;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px !important; font-weight: 700 !important; }
        #saleTable tfoot td.tfoot-btn-cell {
            display: block !important; order: -1; padding: 0;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td.tfoot-btn-cell .btn {
            display: block; width: 100%; border-radius: 0; margin: 0; padding: 11px; font-size: 16px;
        }

        .table-responsive .col-sm-6.table-bordered { width: 50% !important; margin-top: 14px; border-radius: 7px; }
        .form-group.row.text-right { margin-top: 12px; }
        .form-group.row.text-right .btn-success { padding: 10px 44px; font-size: 14px; border-radius: 6px; }
    }

    /* ── Mobile: ≤767px ── */
    @media (max-width: 767px) {
        .inv-header-flex { flex-direction: column; align-items: flex-start; }
        .inv-form-section .form-group.row { display: flex !important; align-items: center !important; flex-wrap: nowrap !important; margin-bottom: 10px !important; }
        .inv-form-section .form-group.row > label[class*="col-"] { flex: 0 0 38% !important; width: 38% !important; max-width: 38% !important; padding-right: 8px !important; white-space: normal !important; word-wrap: break-word !important; }
        .inv-form-section .form-group.row > div[class*="col-"] { flex: 1 1 auto !important; min-width: 0 !important; }

        .table-responsive .col-sm-6.table-bordered { width: 100% !important; float: none; box-sizing: border-box; }
        .form-group.row.text-right .col-sm-12 { text-align: center; }
        .form-group.row.text-right .btn-success { width: 100%; }

        .table-responsive { overflow: visible; }
        #saleTable { display: block; width: 100%; }
        #saleTable tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        #saleTable thead { display: none; }

        #saleTable tbody tr {
            display: block; width: 100%; box-sizing: border-box; margin-bottom: 16px;
            border: 1px solid #ebebeb; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #saleTable tbody tr[style*="table-row"] { display: block !important; width: 100% !important; }

        #saleTable tbody td {
            display: block; width: 100%; box-sizing: border-box; padding: 6px 10px;
            border: none !important; border-bottom: 1px solid #f0f0f0 !important; white-space: normal;
        }

        #saleTable tbody td:last-child { border-bottom: none !important; padding: 0; }
        #saleTable tbody td:last-child .td-mobile-label { display: none; }
        #saleTable tbody td:last-child button,
        #saleTable tbody td:last-child .btn {
            width: 100%; border-radius: 0; margin: 0; display: block;
        }
        #saleTable tbody td:last-child > div { width: 100% !important; display: flex !important; }
        #saleTable tbody td:last-child > div .btn { flex: 1; }

        .td-mobile-label {
            display: block; font-size: 10px; font-weight: 700; color: #999;
            text-transform: uppercase; letter-spacing: .4px; margin-bottom: 4px;
        }

        #saleTable tbody td .form-control,
        #saleTable tbody td input[type="text"],
        #saleTable tbody td input[type="number"],
        #saleTable tbody td select,
        #saleTable tbody td > div,
        #saleTable tbody td .chosen-container,
        #saleTable tbody td .select2-container { width: 100% !important; max-width: 100% !important; box-sizing: border-box; }

        #saleTable tbody td.vathidden[style*="table-cell"] {
            display: block !important; width: 100% !important;
            box-sizing: border-box; padding: 6px 10px;
            border-bottom: 1px solid #f0f0f0 !important;
        }

        #saleTable tfoot {
            display: block; margin-top: 14px;
            border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; overflow: hidden;
        }
        #saleTable tfoot tr { display: flex; flex-direction: column; }
        #saleTable tfoot td { display: none !important; border: none !important; padding: 0; }
        #saleTable tfoot td[data-label] {
            display: block !important; width: 100%; box-sizing: border-box;
            padding: 8px 12px; border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td[data-label]::before {
            content: attr(data-label);
            display: block; font-size: 10px; font-weight: 700;
            color: #999; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px;
        }
        #saleTable tfoot td[data-label] .form-control,
        #saleTable tfoot td[data-label] input[type="text"] {
            display: block; width: 100% !important; box-sizing: border-box;
            font-size: 13px; font-weight: 600; height: 34px !important; text-align: right;
        }
        #saleTable tfoot tr:last-child { background: #f7f7f7; border-top: 2px solid #ddd; }
        #saleTable tfoot tr:last-child td[data-label] { border-bottom: none !important; }
        #saleTable tfoot tr:last-child td[data-label]::before { color: #555; }
        #saleTable tfoot tr:last-child .form-control,
        #saleTable tfoot tr:last-child input[type="text"] { font-size: 15px !important; font-weight: 700 !important; }
        #saleTable tfoot td.tfoot-btn-cell {
            display: block !important; order: -1; padding: 0;
            border-bottom: 1px solid #f0f0f0 !important;
        }
        #saleTable tfoot td.tfoot-btn-cell .btn {
            display: block; width: 100%; border-radius: 0; margin: 0; padding: 11px; font-size: 16px;
        }
    }

    /* ── Table redesign — product screen style ── */
    #saleTable {
        border-collapse: collapse !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important;
        border: 1px solid #E2E8F0 !important;
    }
    #saleTable thead th {
        background: #F1F5F9 !important;
        color: #475569 !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: .7px !important;
        border-bottom: 2px solid #DDD6FE !important;
        border-top: none !important;
        padding: 11px 8px !important;
        white-space: nowrap !important;
    }
    #saleTable thead th .text-danger { color: #ef4444 !important; }
    #saleTable tbody td {
        padding: 8px 8px !important;
        font-size: 13px !important;
        color: #374151 !important;
        border-color: #F3F0FE !important;
        vertical-align: middle !important;
    }
    #saleTable tbody tr:hover td { background: #F3F0FE !important; }
    #saleTable tfoot td {
        background: #F8FAFC !important;
        border-top: 2px solid #DDD6FE !important;
        border-color: #F3F0FE !important;
    }
    #saleTable tfoot tr:last-child td {
        background: #F3F0FE !important;
        border-top: 2px solid #DDD6FE !important;
        font-weight: 700 !important;
    }

    /* ── Input redesign ── */
    input.form-control {
        border: 1px solid #E2E8F0 !important;
        border-radius: 6px !important;
        color: #374151 !important;
        transition: border-color .18s, box-shadow .18s !important;
    }
    input.form-control:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
        outline: none !important;
    }

    /* ── Select2 dropdown redesign ── */
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background: #F8FAFC !important;
        height: 34px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important; font-size: 13px !important; line-height: 32px !important; padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow { height: 32px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow b {
        border-color: #64748B transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important; margin-top: -1px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #16A34A transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #16A34A !important; background: #fff !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12) !important; outline: none !important;
    }
    .select2-selection__placeholder { color: #94A3B8 !important; }
    .select2-dropdown {
        border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
        box-shadow: 0 4px 16px rgba(0,0,0,.10) !important; margin-top: 2px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #E2E8F0 !important; border-radius: 6px !important;
        font-size: 13px !important; padding: 5px 8px !important;
    }
    .select2-results__option { font-size: 13px !important; padding: 7px 12px !important; color: #374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background: #8b5cf6 !important; color: #fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background: #F3F0FE !important; color: #8b5cf6 !important; }

    /* ── Modals — brand page style ── */
    #customerModel .modal-content,
    #addProductModal .modal-content {
        border-radius: 12px !important; border: none !important;
        box-shadow: 0 8px 32px rgba(0,0,0,.15) !important;
    }
    #customerModel .modal-header,
    #addProductModal .modal-header {
        background: #fff !important; border-bottom: 2px solid #F1F5F9 !important;
        padding: 16px 24px !important; border-radius: 12px 12px 0 0 !important;
    }
    #customerModel .modal-header h4,
    #addProductModal .modal-header h4 {
        font-size: 15px !important; font-weight: 700 !important;
        color: #1E293B !important; margin: 0 !important; text-align: left !important;
    }
    #customerModel .modal-header .close,
    #addProductModal .modal-header .close { color: #94A3B8 !important; opacity: 1 !important; }
    #customerModel .modal-header .close:hover,
    #addProductModal .modal-header .close:hover { color: #475569 !important; }
    #customerModel .modal-body,
    #addProductModal .modal-body { padding: 20px 24px !important; background: #fff !important; }
    #customerModel .modal-body .form-group label,
    #addProductModal .modal-body .form-group label {
        font-size: 13px !important; font-weight: 600 !important;
        color: #374151 !important; margin-bottom: 5px !important;
    }
    #customerModel .modal-body .form-control,
    #addProductModal .modal-body .form-control {
        border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
        font-size: 13px !important; color: #374151 !important;
        background: #F8FAFC !important; height: auto !important; padding: 8px 12px !important;
    }
    #customerModel .modal-body .form-control:focus,
    #addProductModal .modal-body .form-control:focus {
        border-color: #8b5cf6 !important; background: #fff !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important; outline: none !important;
    }
    #customerModel .modal-footer,
    #addProductModal .modal-footer {
        padding: 12px 24px !important; border-top: 2px solid #F1F5F9 !important;
        border-radius: 0 0 12px 12px !important; background: #fff !important;
    }
    #customerModel .modal-footer .btn-default,
    #addProductModal .modal-footer .btn-default {
        border-radius: 8px !important; font-size: 13px !important;
        font-weight: 500 !important; padding: 8px 18px !important;
    }
    #customerModel .modal-footer .btn-primary,
    #addProductModal .modal-footer .btn-primary {
        background: #16A34A !important; border: none !important; border-radius: 8px !important;
        font-size: 13px !important; font-weight: 600 !important;
        padding: 8px 18px !important; color: #fff !important;
        transition: background .15s, box-shadow .15s !important;
    }
    #customerModel .modal-footer .btn-primary:hover,
    #addProductModal .modal-footer .btn-primary:hover {
        background: #15803D !important; box-shadow: 0 4px 12px rgba(22,163,74,.30) !important;
    }
    #addProductModal .select2-container .select2-selection--single,
    #addProductModal .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
        background: #F8FAFC !important; height: 38px !important;
    }
    #addProductModal .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important; font-size: 13px !important; line-height: 36px !important; padding-left: 10px !important;
    }
    #addProductModal .select2-container--default.select2-container--focus .select2-selection--single,
    #addProductModal .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #16A34A !important; background: #fff !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12) !important;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title has-goback">
                    <span id="title"><?php echo $title ?></span>
					<span class="padding-lefttitle">
						<table>
							<tr>
								<td style="padding-left: 20px;">
									<button class="btn btn-info m-b-5 m-r-2" onclick="openAddProductModal()">
										<i class="fa fa-plus"></i> Add Product
									</button>
									<button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#customerModel">
										<i class="fa fa-user-plus"></i> Add Customer
									</button>
								</td>
							</tr>
						</table>



					</span>
                
<a href="<?php echo base_url('manage_quotation') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>

            <?php if (!empty($id)): ?>
            <div id="so_edit_banner" style="background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing <span id="so_edit_label">Sales Order #<?php echo (int)$id; ?></span> — changes are saved only when you click Update.
            </div>
            <?php endif; ?>

            <div class="panel-body inv-form-section">


                <!-- Row 1: Sale Date | Branch -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Sale Date
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d'); ?>
                                <input type="text" required tabindex="2" class="form-control datepicker" name="sale_date" value="<?php echo $date; ?>" id="date" />
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select class="form-control" id="branch" required name="branch" tabindex="3" onchange="getSalesOrderDropdown()">


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
                                    <option value="1">Retail</option>
                                    <option value="2">Wholesale</option>

                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Customer | Salesman -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Customer
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-6">
                                <select name="customer_id" id="customer_id" class="form-control " required="" tabindex="1">
                                    <option value="">Select an option</option>
                                    <?php foreach ($all_customer as $customer) { ?>
                                        <option value="<?php echo $customer['customer_id'] ?>">
                                            <?php echo $customer['customer_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Salesman
                            <i class="text-danger">*</i>

                            </label>
                            <div class="col-sm-6">
                                <select name="employee_id" id="employee_id" class="form-control " tabindex="1">
                                    <option value="">Select an option</option>
                                    <option value="1">N/A</option>
                                    <?php foreach ($all_employee as $employee) { ?>
                                        <option value="<?php echo $employee['id'] ?>">
                                            <?php echo $employee['first_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Product Group (full width) -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Product group
                            </label>
                            <div class="col-sm-6">
                                <div style='position: relative; display: inline-block;width:100%;'>
                                    <input class='form-control' type='text' id="productGroupInput" placeholder='Product Group...' onkeyup='handleProductGroupKeyPress(event,1)' autocomplete='off' />
                                    <div id='productGroupResults' style='  position: absolute; z-index: 99999 !important;max-height: 150px;  overflow-y: auto;border: 1px solid #ddd;z-index: 9999; background-color: #fff; border-radius: 4px;  box-shadow: 0 4px 6px rgba(0,0,0,0.1);' autocomplete='off'>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">


                </div>


                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="saleTable">
                        <thead>
                            <tr>
                                <th class="text-center col-big">Product<i
                                        class="text-danger">*</i></th>
                                <th class="text-center col-medium">Store<i class="text-danger">*</i>
                                </th>
                                <th class="text-center col-medium">Batch<i class="text-danger">*</i>
                                </th>
                                <th class="text-center col-small">Unit <i class="text-danger">*</i></th>
                                <th class="text-center col-small">Av.Qty</th>
                                <th class="text-center col-small">Qty<i
                                        class="text-danger">*</i></th>
                                <th class="text-center col-medium">Price val <i
                                        class="text-danger"> *</i></th>
                                <th class="text-center col-medium">Discount</th>
                                <th class="text-center col-medium">Dis.val</th>

                                <th class="text-center vathidden" id="vathidden">VAT.val</th>


                                <th class="text-center ">Total</th>

                                <th class="text-center"><?php echo display('action') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="addinvoiceItem">
                            <tr id="myRow1">
                                <td class="product_field">

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
                                    <input type="hidden" id="defaultsaleprice1" />
                                      <input type="hidden" id="mrpprice1" />
                                    <input type="hidden" id="groupId1" />
                                   <input type="hidden" id="parent1" />
                                    <input type="hidden" id="invoicegroup1" />
                                  <input type="hidden" id="isstock1" />



                                </td>

                                <td class="rate">
                                    <select class="form-control" id="store1" name="store[]" tabindex="3" onchange="product_search(1,'store')">
                                        <option value=""></option>
                                    </select>
                                </td>
                                <td class="rate">
                                    <select class="form-control" id="batch1" name="batch[]" tabindex="3" onchange="product_search(1,'batch')">
                                        <option value=""></option>
                                    </select>
                                </td>

                                <td class="qty">
                                    <select class="form-control" id="unit1" required name="unit1" onchange="product_search(1,'unit')" tabindex="3">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" id="conversionid1" />
                                    <input type="hidden" id="conversiontype1" />
                                    <input type="hidden" id="conversion_ratio1" />
                                </td>
                                <td class="qty">
                                    <input type="hidden" name="code[]" onkeyup="product_search(1,'code');"
                                        class="total_qntt_1 form-control text-right"
                                        id="code1" placeholder="0.00" min="0" readonly />
                                    <span id='codetype1' style="margin-left:5px"></span>
                                </td>



                                <td class="qty">
                                    <input type="text" name="product_quantity[]" id="qty1" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" placeholder="0.00" value="" tabindex="6" />
                                </td>
                                <td class="rate">
                                    <input type="text" name="product_rate[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="product_rate1" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                </td>

                                <td class="qty">
                                    <input type="text" name="discount_per[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="discount1" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                    <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">

                                </td>
                                <td class="rate">
                                    <input type="text" name="discountvalue[]" id="discount_value1" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  start-->

                                <td class="rate vathidden">
                                    <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(1);" onchange="calculate_sum(1);" id="vat_percent1" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                    <input type="text" name="vatvalue[]" id="vat_value1" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                </td>

                                <!-- VAT  end-->
                                <td class="product_field">
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
                                        <input type="hidden" id="defaultsaleprice<?php echo $i; ?>" />
                                        <input type="hidden" id="mrpprice<?php echo $i; ?>" />
                                        <input type="hidden" id="groupId<?php echo $i; ?>" />
                                        <input type="hidden" id="parent<?php echo $i; ?>" />
                                         <input type="hidden" id="invoicegroup<?php echo $i; ?>" />
                                        <input type="hidden" id="isstock<?php echo $i; ?>" />

                                  
                                    </td>



                                    <td class="rate">
                                        <select class="form-control" id="store<?php echo $i; ?>" name="store[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'store')">
                                            <option value=""></option>
                                        </select>
                                    </td>

                                    <td class="rate">
                                        <select class="form-control" id="batch<?php echo $i; ?>" name="batch[]" tabindex="3" onchange="product_search(<?php echo $i; ?>, 'batch')">
                                            <option value=""></option>
                                        </select>
                                    </td>



                                    <td class="qty">
                                        <select class="form-control" id="unit<?php echo $i; ?>" required name="unit<?php echo $i; ?>" onchange="product_search(<?php echo $i; ?>,'unit')" tabindex="3">
                                            <option value=""></option>
                                        </select>
                                        <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                        <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                        <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />
                                    </td>

                                    <td class="qty">
                                        <input type="hidden" name="code[]" onkeyup="product_search(<?php echo $i; ?>, 'code');" class="total_qntt_1 form-control text-right" id="code<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                        <span id='codetype<?php echo $i; ?>' style="margin-left:5px"></span>

                                    </td>

                                    <td class="qty">
                                        <input type="text" name="product_quantity[]" id="qty<?php echo $i; ?>" min="0" class="form-control text-right store_cal_1" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" placeholder="0.00" value="" tabindex="6" />
                                    </td>

                                    <td class="rate">
                                        <input type="text" name="product_rate[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="product_rate<?php echo $i; ?>" class="form-control product_rate_1 text-right" placeholder="0.00" value="" min="0" tabindex="7" />
                                    </td>

                                    <td class="qty">
                                        <input type="text" name="discount_per[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="discount<?php echo $i; ?>" class="form-control discount_1 text-right" min="0" tabindex="11" placeholder="0.00" />
                                        <input type="hidden" value="<?php echo $discount_type ?>" name="discount_type" id="discount_type">
                                    </td>

                                    <td class="rate">
                                        <input type="text" name="discountvalue[]" id="discount_value<?php echo $i; ?>" class="form-control text-right discount_value_1 total_discount_val" min="0" tabindex="12" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT start -->

                                    <td class="rate vathidden">
                                        <input type="hidden" name="vatpercent[]" onkeyup="calculate_sum(<?php echo $i; ?>);" onchange="calculate_sum(<?php echo $i; ?>);" id="vat_percent<?php echo $i; ?>" class="form-control vat_percent_1 text-right" min="0" tabindex="13" placeholder="0.00" />
                                        <input type="text" name="vatvalue[]" id="vat_value<?php echo $i; ?>" class="form-control vat_value1 text-right total_vatamnt" min="0" tabindex="14" placeholder="0.00" readonly />
                                    </td>

                                    <!-- VAT end -->

                                    <td class="product_field">
                                        <input class="form-control total_price text-right total_price_1" type="text" name="total_price[]" id="total_price<?php echo $i; ?>" value="0.00" readonly="readonly" />
                                        <input type="hidden" id="total_discount<?php echo $i; ?>" class="" />
                                        <input type="hidden" id="all_discount<?php echo $i; ?>" class="total_discount dppr" name="discount_amount[]" />
                                    </td>

                                    <td>
<div style="display:flex; align-items:center; gap:6px;">

    <button class='btn btn-danger btn-sm' type='button' onclick='deleteRow(<?php echo $i; ?>)'>
        <i class='fa fa-trash'></i>
    </button>

    <p id="isparent<?php echo $i; ?>" ><b>P</b></p>


</div>
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

                                <td class="text-right">
                                    <input type="text" id="Total" class="text-right form-control" name="total" value="0.00" readonly="readonly" />
                                </td>
                                <td> <button type="button" id="add_invoice_item" class="btn btn-info" name="add-invoice-item"
                                        onClick="addInputField('addinvoiceItem');" tabindex="9"><i class="fa fa-plus"></i></button>

                                    <input type="hidden" name="baseUrl" class="baseUrl" value="<?php echo base_url(); ?>" />
                                </td>
                            </tr>
                            <tr>

                                <td colspan="10" class="text-right vathidden"><b>Sale Discount:</b></td>
                                <td colspan="9" class="text-right vatshow"><b>Sale Discount:</b></td>

                                <td class="text-right">
                                    <input type="text" id="discount" class="text-right form-control discount total_discount_val" onkeyup="calculate_sum(1)" name="discount" placeholder="0.00" value="" />
                                </td>

                                <td>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('total_discount') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('total_discount') ?>:</b></td>


                                <td class="text-right">
                                    <input type="text" id="total_discount_ammount" class="form-control text-right" name="total_discount" value="0.00" readonly="readonly" />
                                </td>
                                <td>

                                </td>
                            </tr>
                            <tr>

                                <td class="text-right vathidden" colspan="10"><b><?php echo display('ttl_val') ?>:</b></td>

                                <td class="text-right vathidden">
                                    <input type="text" id="total_vat_amnt" class="form-control text-right" name="total_vat_amnt" value="0.00" readonly="readonly" />
                                </td>
                                <td class="text-right vathidden">

                                </td>
                            </tr>


                            <tr>
                                <td colspan="10" class="text-right vathidden"><b><?php echo display('grand_total') ?>:</b></td>
                                <td colspan="9" class="text-right vatshow"><b><?php echo display('grand_total') ?>:</b></td>


                                <td class="text-right">
                                    <input type="text" id="grandTotal" class="text-right form-control grandTotalamnt" name="grand_total_price" placeholder="0.00" value="00" readonly />
                                </td>
                                <td> </td>
                            </tr>

                        </tfoot>
                    </table>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
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
                    <p hidden id="pay-amount"></p>
                    <p hidden id="change-amount"></p>

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
<div id="customerModel" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add New Customer</h4>
			</div>

			<div class="modal-body">


				<div class="form-group">
					<label>Customer Name</label>
					<input type="text" required tabindex="2" class="form-control" name="customer_name" value="" id="customer_name" />
				</div>

				<div class="form-group">
					<label>Phone Number</label>
					<input type="text" required tabindex="2" class="form-control" name="customer_phone" value="" id="customer_phone" />
				</div>


			</div>


			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="quick_customer_save_btn" class="btn btn-primary" onclick="save_customer()">Save</button>
			</div>

		</div>

	</div>
</div>
<script>
$(function() {
    wireDuplicateCheck({
        input: '#customer_name',
        url: $('#base_url').val() + 'customer/customer/check_duplicate_customer',
        saveButtons: '#quick_customer_save_btn'
    });
});
</script>

<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let customers=" . json_encode($all_customer) . ";";
echo "let employees=" . json_encode($all_employee) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let batches=" . json_encode($batches) . ";";

echo "let pmethods=" . json_encode($all_pmethod) . ";";
echo "let vtinfo=" . json_encode($vtinfo) . ";";
echo "let ap_categories=" . json_encode($category_list ?: []) . ";";
echo "let ap_units=" . json_encode($unit_list ?: []) . ";";
echo "let ap_suppliers=" . json_encode($all_supplier ?: []) . ";";
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

     let colors = [
        "#FFB3BA",
        "#FFDFBA",
        "#FFFFBA",
        "#BAFFC9",
        "#BAE1FF",
        "#D7BAFF",
        "#FFBACD",
        "#C2FFBA",
        "#BAFFF3",
        "#FFD6BA"
    ];

    let colorIndex = 1;


    $(document).ready(function() {
        var $customerDropdown = $('#customer_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(customers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
        });
        $customerDropdown.val(1)

        var $invoiceTypeDropdown = $('#invoicetype');
        $invoiceTypeDropdown.empty(); // Clear existing options
        $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
        $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
        $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
        $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
        $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
        $invoiceTypeDropdown.val('cash');

        
        var $employeeDropdown = $('#employee_id');
                $employeeDropdown.empty();
                $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                $employeeDropdown.append('<option value="1">N/A</option>');
                $.each(employees, function(index, employee) {
                    $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name  + '</option>');
                });
          $employeeDropdown.val(1)

        var $incidenttypeDropdown = $('#incidenttype');
        $incidenttypeDropdown.empty();
        $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
        $incidenttypeDropdown.append('<option value="1">Retail</option>');
        $incidenttypeDropdown.append('<option value="2">Wholesale</option>');
        $incidenttypeDropdown.val(1)
          for (let j = 2; j <= 20; j++) {
            document.getElementById('myRow' + j).style.display = 'none';
            document.getElementById('isparent' + j).style.display = 'none';
       }


        document.querySelectorAll('.vathidden').forEach(el => {
            el.style.display = 'none';
        });

        let groupid = 0;

        if (id != null) {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getSalesOrderById',
                type: 'POST',
                data: {
                    id: id,
                    type2: type2
                },
                success: function(response) {
                    var sales = JSON.parse(response);
                    console.log(sales)
                    if (sales[0].sale_id && document.getElementById('so_edit_label')) {
                        document.getElementById('so_edit_label').textContent = 'Sales Order ' + sales[0].sale_id;
                    }
                    document.getElementById('date').value = sales[0].date;
                    document.getElementById('details').value = sales[0].details;

                    getBranchDropdown(sales[0].branch);


                    var $customerDropdown = $('#customer_id');
                    $customerDropdown.empty();
                    $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                    $.each(customers, function(index, customer) {
                        $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                    });
                    $customerDropdown.val(sales[0].customer_id)

                    var $employeeDropdown = $('#employee_id');
                    $employeeDropdown.empty();
                    $employeeDropdown.append('<option value="" disabled selected>Select Employee</option>'); // Add default option
                    $employeeDropdown.append('<option value="1">N/A</option>');

                    $.each(employees, function(index, employee) {
                        $employeeDropdown.append('<option value="' + employee.id + '">' + employee.first_name + '</option>');
                    });
                    $employeeDropdown.val(sales[0].employee_id)



                    var $incidenttypeDropdown = $('#incidenttype');
                    $incidenttypeDropdown.empty();
                    $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
                    $incidenttypeDropdown.append('<option value="1">Retail</option>');
                    $incidenttypeDropdown.append('<option value="2">Whole Sale</option>');
                    $incidenttypeDropdown.val(sales[0].incidenttype)

                    var $invoiceTypeDropdown = $('#invoicetype');
                    $invoiceTypeDropdown.empty(); // Clear existing options
                    $invoiceTypeDropdown.append('<option value="" disabled selected>Select Invoice Type</option>');
                    $invoiceTypeDropdown.append('<option value="cash">Cash</option>');
                    $invoiceTypeDropdown.append('<option value="credit">Credit</option>');
                    $invoiceTypeDropdown.append('<option value="cash_vat">Cash VAT</option>');
                    $invoiceTypeDropdown.append('<option value="credit_vat">Credit VAT</option>');
                    $invoiceTypeDropdown.val(sales[0].invoicetype);

                    document.getElementById('total_discount_ammount').value = sales[0].total_discount_ammount;
                    document.getElementById('total_vat_amnt').value = sales[0].total_vat_amnt;
                    document.getElementById('grandTotal').value = sales[0].grandTotal;
                    document.getElementById('Total').value = sales[0].total;
                    document.getElementById('discount').value = sales[0].discount;

                    // count = 1;
                      let a =0
                    for (let i = 0; i < sales.length; i++) {
                             a = a + 1;
                              if(i==0){
                                
                                 if(sales[i].group_id>0){
                                      a = 2;
                                    document.getElementById('myRow1').style.display = 'none';

                                 }
}


                        document.getElementById('myRow' + a).style.display = 'table-row';

                         getActiveStore(sales[i].store, a);

                        document.getElementById('qty' + a).value = sales[i].quantity;
                        document.getElementById('unit' + a).value = sales[i].unit;
                        document.getElementById('code' + a).value = sales[i].avstock;
                        document.getElementById('product_rate' + a).value = sales[i].product_rate;
                        document.getElementById('discount' + a).value = sales[i].discount2;
                        document.getElementById('discount_value' + a).value = sales[i].discount_value;
                        document.getElementById('mastercost_price' + a).value = sales[i].cost_price;

                         document.getElementById('product' + a).value = sales[i].product;
                            document.getElementById('productInput' + a).value = sales[i].product_name;
                              document.getElementById('groupId' + a).value = sales[i].group_id;
                            document.getElementById('parent' + a).value = sales[i].parent;
                            document.getElementById('invoicegroup' + a).value = sales[i].invoicegroup;

                 if( sales[i].group_id>0){
                                
                                if(groupid!=sales[i].group_id){
                                    groupid=sales[i].group_id
                                    colorIndex=colorIndex+1;
                                }
                                document.getElementById('myRow' + a).style.display = 'table-row';
                                var row = document.getElementById('myRow' + a);
                                row.style.display = 'table-row';
                                row.style.backgroundColor = colors[colorIndex];

                                if(sales[i].parent==1){
                                    if( sales[i].invoicegroup==1){
                                         document.getElementById('isparent' + a).style.display = 'block';

                                    }else{

                                    }

                                }


                            
                            }
                        // if (vtinfo.ischecked == 1) {
                        //     document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                        // }
                        if (sales[0].invoicetype == 'cash_vat' ||
                            sales[0].invoicetype == 'credit_vat' ||
                            sales[0].invoicetype == 'svat') {
                            document.getElementById('vat_percent' + a).value = sales[i].vat_percent;
                            document.querySelectorAll('.vathidden').forEach(el => {
                                el.style.display = 'table-cell';
                            });
                            document.querySelectorAll('.vatshow').forEach(el => {
                                el.style.display = 'none';
                            });
                        } else {
                            document.getElementById('vat_percent' + a).value = 0;

                        }
                        document.getElementById('vat_value' + a).value = sales[i].vat_value;
                        document.getElementById('total_price' + a).value = sales[i].total_price;
                        document.getElementById('total_discount' + a).value = sales[i].total_discount;
                        document.getElementById('all_discount' + a).value = sales[i].all_discount;

                        getActiveSubUnitEdit(sales[i].product, a, sales[i].unit, sales[i].conversion_id,
                            sales[i].conversion_ratio, sales[i].convertiontype,
                            sales[i].avstock)

                        // getBatchDropdown(batches, a, sales[i].batch)
                        getBatchDropdown(batches, a, sales[i].batch, sales[i].product, sales[i].batchtype)



                        count = count + 1;
                    }
                    colorIndex=colorIndex+1;

                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            getBranchDropdown(0);

        }
    });

     function save_customer(){

          $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/save_customer',
                type: 'POST',
                data: {
                    customer_name: document.getElementById('customer_name').value,
                   customer_phone: document.getElementById('customer_phone').value,
                },
                success: function(response) {
                     var customer_new = JSON.parse(response);
                     customers=customer_new.all_customer;

                      var $customerDropdown = $('#customer_id');
                        $customerDropdown.empty();
                        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
                        $.each(customers, function(index, customer) {
                            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
                        });
                        $customerDropdown.val(customer_new.inserted_id)
                        alert("Customer Information saved sucessfully")
                    $('#customerModel').modal('hide');
                    document.getElementById('customer_name').value=""
                    document.getElementById('customer_phone').value=""
                    


                   

                },
                error: function(error) {
                    console.log(error);
                }
            });

    }

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
            var $batchDropdown = $('#batch' + count);
            $batchDropdown.empty();

            var $storeDropdown = $('#store' + count);
            $storeDropdown.empty();

            var $unitDropdown = $('#unit' + count);
            $unitDropdown.empty();


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


    function handleProductGroupKeyPress(event) {

        const productElement = document.getElementById('productGroupInput');
        const query = productElement.value;


        if (event.key === 'ArrowDown') {
            //  $("#branchId").val("");
            // Move down in the list
            if (currentIndex < results.length - 1) {
                currentIndex++;
                highlightItemproductGroup(currentIndex);
            }

        } else if (event.key === 'ArrowUp') {
            //  $("#branchId").val("");
            // Move up in the list
            if (currentIndex > 0) {
                currentIndex--;
                highlightItemproductGroup(currentIndex);
            }
        } else if (event.key === 'Enter') {
            // if (document.getElementById('branchId').value != "") {

            //     // let element2 = document.getElementById("branchId");
            //     // element2.focus();

            // }
            // Select the highlighted item
            if (results.length > 0) {

                    // product_search(count, "product")

                    document.getElementById('productGroupInput').value = "";

                     for (let i = 1; i < count; i++) {
                       if (document.getElementById('myRow' + i).style.display != "none") {

                             if(document.getElementById('groupId' + i).value==results[currentIndex].id){
                                alert("This group is already there in the invoice")
                                return
                             }

                       }
                    }

                     document.getElementById('productGroupResults').innerHTML=''


                    getProductGroupDetailsById(results[currentIndex].id)
            } else {
                alert("Product shouldn't be empty")
                document.getElementById('productGroupInput').value = "";
                return
            }

        } else if (event.key === "Backspace") {
            // document.getElementById('productGroupInput').value = "";

        } else {
            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/getProductGroupByName',
                type: 'POST',
                data: {
                    group_name: query,
                },
                success: function(response) {
                    var products = JSON.parse(response);

                    results = products
                        .filter(product => product.group_name.toLowerCase().includes(query.toLowerCase()));
                    currentIndex = -1;
                    displayResultsProductGroup(results);

                },
                error: function(error) {
                    console.log(error);
                }
            });


        }

    }

    function displayResultsProductGroup(results) {
        const searchResultsDiv = document.getElementById('productGroupResults');
        searchResultsDiv.innerHTML = ''; // Clear previous results

        if (results.length === 0) {
            searchResultsDiv.innerHTML = '<div style="padding:8px;">No results found</div>';
        } else {
            results.forEach((item, index) => {

                const resultItem = document.createElement('div');

                resultItem.classList.add('resultItem');
                if(item.invoice_group==1){
                resultItem.textContent = item.group_name+" - "+"ig";

                }else{
                     resultItem.textContent = item.group_name;
                }

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
                    // document.getElementById('productGroupInput' + count).value = item.product_name;

                    // // set hidden product id
                    // document.getElementById('product' + count).value = item.id;

                    // // clear dropdown
                    searchResultsDiv.innerHTML = '';

                    // product_search(count, "product")

                    document.getElementById('productGroupInput').value = "";

                     for (let i = 1; i < count; i++) {
                       if (document.getElementById('myRow' + i).style.display != "none") {

                             if(document.getElementById('groupId' + i).value==item.id){
                                alert("This group is already there in the invoice")
                                return
                             }

                       }
                    }

                     document.getElementById('productGroupResults').innerHTML=''


                    getProductGroupDetailsById(item.id)


                });

                searchResultsDiv.appendChild(resultItem);
            });
        }
        currentIndex = 0
        highlightItemproductGroup(0);

    }

    function highlightItemproductGroup(index) {
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




    function getProductGroupDetailsById(groupId) {

        $.ajax({
            url: $('#base_url').val() + 'invoice/invoice/getProductGroupDetailsById',
            type: 'POST',
            data: {
                group_id: groupId,
            },
            success: function(response) {
                var groupdetails = JSON.parse(response);
                console.log(groupdetails)
                countPrevious = count ;

                if(document.getElementById('product1' ).value==""){
                    document.getElementById('myRow1').style.display = 'none';

                }

                groupdetails.forEach(function(group) {
                    document.getElementById('product' + count).value = group.product
                    document.getElementById('productInput' + count).value = group.product_name
                    document.getElementById('groupId' + count).value = group.pid
                    document.getElementById('parent' + count).value = group.parent
                    document.getElementById('invoicegroup' + count).value = group.invoice_group;




                    document.getElementById('myRow' + count).style.display = 'table-row';
                      if( group.parent==1){
                        if( group.invoice_group==1){
                               document.getElementById('isparent' + count).style.display = 'block';
                        }else{
                        }
                        }
                    var row = document.getElementById('myRow' + count);
                    row.style.display = 'table-row';
                    row.style.backgroundColor = colors[colorIndex];
                    getActiveStore(0, count);
                    product_group_search(count, 'group', group.unit, group.unit_name)
                    count = count + 1;

                   

                });

                setTimeout(() => {
                    groupdetails.forEach(function(group) {

                        document.getElementById('qty' + countPrevious).value = group.qty;
                        calculate_sum(countPrevious)
                        countPrevious = countPrevious+1 ;

                    });

                    }, 3000);

                colorIndex++;






            },
            error: function(error) {
                console.log(error);
            }
        });


    }

    

    function addInputField(t) {
        // if (count < 11) {
        document.getElementById('myRow' + count).style.display = 'table-row';
        getActiveStore(0, count);
        count = count + 1;

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

    function product_search(item, name) {

        if (name === "product") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('code' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            // var $storeDropdown = $('#store' + item);
            // $storeDropdown.empty();
            // document.getElementById('code' + item).value = "";
            // document.getElementById('qty' + item).value = "";
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

                               if(document.getElementById('isstock' + item).value==1){
                                  
                               avStock(item, document.getElementById('product' + item).value, product[0].store, 1, "", "")
                                }
                                //   document.getElementById('unit' + item).value = product[0].unit;
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        }), 1000);
                    getActiveStore(product[0].store, item);
                    getBatchDropdown(batches, item, 1, document.getElementById('product' + item).value.toString(), product[0].batchtype);
                    // avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")

                    document.getElementById('unit' + item).value = product[0].unit;
                    document.getElementById('mastercost_price' + item).value = product[0].price;
                    document.getElementById('defaultsaleprice' + item).value = product[0].defaultsaleprice;
                    document.getElementById('mrpprice' + item).value = "";


                    if (product[0].defaultsaleprice == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = product[0].price;

                    }

                    if (product[0].defaultsaleprice == 'custom') {
                        document.getElementById('product_rate' + item).value = 0;

                    }

                   if (product[0].defaultsaleprice == 'mrp') {
                        document.getElementById('product_rate' + item).value = product[0].price;
                        // setTimeout(
                        // getMrpPrice(item),1000)

                    }

                    // if (vtinfo.ischecked == 1) {
                    //     document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    // }
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

            if (document.getElementById('defaultsaleprice' + item).value == 'mrp') {
                getMrpPrice(item);
            }
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
             }            // avStock(item,document.getElementById('product' + item).value,document.getElementById('store' + item).value,0)
            // getActiveSubUnit(document.getElementById('product' + item).value,item)

        }
    }

   function product_group_search(item, name, groupUnitId, groupUnitName) {
        if (name === "group") {
            document.getElementById('qty' + item).value = "";
            document.getElementById('code' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('product_rate' + item).value = "";
            document.getElementById('discount' + item).value = "";
            document.getElementById('discount_value' + item).value = "";
            document.getElementById('vat_percent' + item).value = "";
            document.getElementById('vat_value' + item).value = "";
            document.getElementById('total_price' + item).value = "";
            document.getElementById('total_discount' + item).value = "";
            document.getElementById('all_discount' + item).value = "";
            // var $storeDropdown = $('#store' + item);
            // $storeDropdown.empty();
            // document.getElementById('code' + item).value = "";
            // document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#base_url').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
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

                                getActiveSubUnitForGroup(document.getElementById('product' + item).value, item, groupUnitId, groupUnitName)


                                //   document.getElementById('unit' + item).value = product[0].unit;
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        }), 1000);
                    getActiveStore(product[0].store, item);
                    getBatchDropdown(batches, item, 1, document.getElementById('product' + item).value.toString(), product[0].batchtype);
                    // avStock(item, document.getElementById('product' + item).value, product[0].store, 0, "", "")

                    document.getElementById('unit' + item).value = product[0].unit;

                    document.getElementById('defaultsaleprice' + item).value = product[0].defaultsaleprice;
                    document.getElementById('mastercost_price' + item).value = product[0].price;
                    document.getElementById('mrpprice' + item).value = "";




                    if (product[0].defaultsaleprice == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = product[0].price;

                    }

                    if (product[0].defaultsaleprice == 'custom') {
                        document.getElementById('product_rate' + item).value = 0;

                    }


                    if (product[0].defaultsaleprice == 'mrp') {
                        document.getElementById('product_rate' + item).value = product[0].price;
                        // setTimeout(
                        // getMrpPrice(item),1000)

                    }


                    // if (vtinfo.ischecked == 1) {
                    //     document.getElementById('vat_percent' + item).value = product[0].product_vat;
                    // }
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
    }


    function getMrpPrice(item) {
        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchPrice',
            type: 'POST',
            data: {
                product: document.getElementById('product' + item).value.toString(),
                batch: document.getElementById('batch' + item).value.toString(),

            },
            success: function(response2) {
             if (response2 != "") {
                    let product2 = JSON.parse(response2);
                    document.getElementById('mrpprice' + item).value = product2[0].mrp;
                    document.getElementById('product_rate' + item).value = product2[0].mrp;

                } else {
                    document.getElementById('mrpprice' + item).value = "";
                }



            },
            error: function(error) {
                console.log(error)
            }
        })
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

                    let sub = (stock[0].avgqty * conversion_ratio);
                    let sub2 = Math.floor((sub).toLocaleString());
                    if (isNaN(sub2)) {
                        sub = Number(sub).toFixed(6);
                        el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText
                    } else {
                        el.innerHTML = sub2 + " " + selectedText

                    }

                } else if (convertiontype == "/") {
                    document.getElementById('code' + item).value = (stock[0].avgqty / conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty / conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "+") {
                    document.getElementById('code' + item).value = (stock[0].avgqty + conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty + conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

                } else if (convertiontype == "-") {
                    document.getElementById('code' + item).value = (stock[0].avgqty - conversion_ratio).toFixed(2)
                    let sub = stock[0].avgqty - conversion_ratio;
                    el.innerHTML = (Math.floor(sub)).toLocaleString() + " " + selectedText

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
        if( document.getElementById('groupId' + num).value!=0){
            if(document.getElementById('parent' + num).value==1&&document.getElementById('isparent' + num).style.display != "none"){
                for (let i = 1; i < count; i++) {
                   if (document.getElementById('myRow' + i).style.display != "none") {

                      if(document.getElementById('groupId' + num).value==document.getElementById('groupId' + i).value){
                        document.getElementById('myRow' + i).style.display = 'none';
                        document.getElementById('qty' + i).value = 0;
                        document.getElementById('product_rate' + i).value = 0;
                        document.getElementById('discount' + i).value = 0;
                        document.getElementById('discount_value' + i).value = 0;
                        document.getElementById('vat_percent' + i).value = 0;
                        document.getElementById('vat_value' + i).value = 0;
                        document.getElementById('total_price' + i).value = 0;
                        document.getElementById('total_discount' + i).value = 0;
                        document.getElementById('all_discount' + i).value = 0;
                        calculate_sum(i)

                    }


                }
            }

            }
        }


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
        var vat_percent = $("#vat_percent" + sl).val();
        var code = $("#code" + sl).val();

        var vat_percent = 0;
        if (document.getElementById('invoicetype').value == 'cash_vat' ||
            document.getElementById('invoicetype').value == 'credit_vat' ||
            document.getElementById('invoicetype').value == 'svat') {
            vat_percent = $("#vat_percent" + sl).val();



        }

         let value = $("#code" + sl).val();   
         let number = parseInt(value.replace(/,/g, ''), 10);


        // if (parseInt(quantity) > parseInt(number)) {
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
                        getSalesOrderDropdown()

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

    function getSalesOrderDropdown() {

        var base_url = $('#base_url').val();

        $.ajax({
            type: "post",
            url: base_url + "invoice/invoice/getBranchNature",
            data: {
                branch: document.getElementById("branch").value
            },
            success: function(data) {

                var branch = JSON.parse(data);
                //   console.log(branch[0].nature)

                var $incidenttypeDropdown = $('#incidenttype');
                $incidenttypeDropdown.empty();
                $incidenttypeDropdown.append('<option value="" disabled selected>Select Incident Type</option>'); // Add default option
                $incidenttypeDropdown.append('<option value="1">Retail</option>');
                $incidenttypeDropdown.append('<option value="2">Wholesale</option>');

                if (branch[0].nature == "Retail") {
                    $incidenttypeDropdown.val(1)

                } else if (branch[0].nature == "Wholesale") {
                    $incidenttypeDropdown.val(2)

                }

                // var $branchDropdown = $('#sales_order_no');
                // $branchDropdown.empty();
                // $branchDropdown.append('<option value="" disabled selected>Select Sales Order</option>'); // Add default option

                // $.each(salesorder, function(index, branch) {
                //     $branchDropdown.append('<option value="' + branch.id + '">' + branch.sale_id + '</option>');

                // });




            }
        });
    }


    function restoreSaveBtn() {
        var $b = $('#save_add');
        $b.prop('disabled', false).removeClass('btn-save-anim-loading').html($b.data('origHtml'));
    }

    function save() {
        arrItem = [];
        grouparrItem = [];
        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {
                if (document.getElementById('customer_id').value == "" || document.getElementById('customer_id').value === " ") {
                    alert("Customer shouldn't be empty")
                    return
                } else if (document.getElementById('branch').value == "") {
                    alert("Branch shouldn't be empty")
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty")
                    return
                } else if (document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty")
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty")
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
                } else if (document.getElementById('employee_id').value == "") {
                    alert("Salesman shouldn't be empty")
                    return
                } 
                else {
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
                        product_name:document.getElementById('productInput' + i).value,
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
                        group: document.getElementById('groupId' + i).value,
                        parent: document.getElementById('parent' + i).value,
                        invoicegroup: document.getElementById('invoicegroup' + i).value,
                        conversionid: document.getElementById('conversionid' + i).value,
                    });


                     if( document.getElementById('parent' + i).value==1&& document.getElementById('invoicegroup' + i).value==1){
                       grouparrItem.push({
                          group: document.getElementById('groupId' + i).value,
                           product_rate: document.getElementById('product_rate' + i).value,
                             qty: document.getElementById('qty' + i).value,
                           discount: document.getElementById('discount' + i).value,
                        discount_value: document.getElementById('discount_value' + i).value,
                        vat_percent: document.getElementById('vat_percent' + i).value,
                        vat_value: document.getElementById('vat_value' + i).value,
                        total_price: document.getElementById('total_price' + i).value,
                        total_discount: document.getElementById('total_discount' + i).value,
                        all_discount: document.getElementById('all_discount' + i).value,
                        unit: document.getElementById('unit' + i).value,


                       })
                    }
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
                url: $('#base_url').val() + 'invoice/invoice/update_salesorder',
                type: 'POST',
                data: {
                    id: id,
                    items: arrItem,
                    grouparrItem:grouparrItem,
                    discount: document.getElementById('discount').value,
                    type2: 'C',
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    employee_id: document.getElementById('employee_id').value,
                    // payment_type: document.getElementById('your_dropdown_id').value,
                    // payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,

                },
                success: function(response) {
                    // alert("Invoice Details Updated Successfully")
                    // window.location.href = $('#base_url').val() + 'invoice_list';

                    datas = JSON.parse(response);
                    clearDetails()
                    restoreSaveBtn();

                    alert("Sales Order  Updated Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.invoiceno);


                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Update failed. Please check your inputs and try again.");
                    console.log(error)
                }
            });


        } else {

            $.ajax({
                url: $('#base_url').val() + 'invoice/invoice/save_salesorder',
                type: 'POST',
                data: {
                    items: arrItem,
                     grouparrItem:grouparrItem,
                    type2: 'C',
                    discount: document.getElementById('discount').value,
                    total_discount_ammount: document.getElementById('total_discount_ammount').value,
                    total_vat_amnt: document.getElementById('total_vat_amnt').value,
                    grandTotal: document.getElementById('grandTotal').value,
                    date: document.getElementById('date').value,
                    details: document.getElementById('details').value,
                    total: document.getElementById('Total').value,
                    customer_id: document.getElementById('customer_id').value,
                    // payment_type: document.getElementById('your_dropdown_id').value,
                    // payment: paymentdropdown.options[paymentdropdown.selectedIndex].text,
                    employee_id: document.getElementById('employee_id').value,
                    incidenttype: document.getElementById('incidenttype').value,
                    branch: document.getElementById('branch').value,
                    invoicetype: document.getElementById('invoicetype').value,
                },
                success: function(response) {
                    datas = JSON.parse(response);
                    clearDetails()
                    restoreSaveBtn();

                    alert("Sales Order  saved Successfully")
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.invoiceno);
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Save failed. Please check your inputs and try again.");
                    console.log(error)
                }
            });

        }







    }

    function clearDetails() {
        for (let i = 1; i < 20; i++) {
          

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
        document.getElementById('customer_id').value = ""

        var $customerDropdown = $('#customer_id');
        $customerDropdown.empty();
        $customerDropdown.append('<option value="" disabled selected>Select Customer</option>'); // Add default option
        $.each(customers, function(index, customer) {
            $customerDropdown.append('<option value="' + customer.customer_id + '">' + customer.customer_name + '</option>');
        });
    }

    function clearDetails2() {
        for (let i = 1; i < 20; i++) {
           
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

    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'salesorder_' + voucherNo + '_' + pdfDateStamp();
        }

        $(view).print({

            deferred: $.Deferred().done(function() {
                window.location.reload();
            })
        });
    }

    function getBatchDropdown(batches, item, value, product, batchtype) {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchInStockByProductAndBatchtype',
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
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
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
                // el.innerHTML = (Math.floor(avstock)).toLocaleString() + " " + selectedText


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
                                console.log(product2)
                                // document.getElementById('code' + item).value = avstock * product2[0].conversion_ratio;
                                document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                document.getElementById('bd' + item).value = product2[0].unit2
                                document.getElementById('ad' + item).value = product2[0].unit_name
                                let el = document.getElementById('codetype' + item);
                                el.style.color = '#8b5cf6';
                                el.style.fontWeight = 'bold';
                                el.innerHTML = ""
                                // let totalcount = Math.floor(document.getElementById('mconversion_ratio' + item).value * avstock / document.getElementById('mconversion_ratio' + item).value);
                                // let subcount = (Math.floor(document.getElementById('mconversion_ratio' + item).value * avstock % document.getElementById('mconversion_ratio' + item).value)).toLocaleString();


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
                                console.log(product2)
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

 function getActiveSubUnitForGroup(productId, item, groupUnitId, groupUnitName) {
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

                $.each(datas, function(index, store) {
                    if (store.unit_id) {
                        $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                    }
                });
                $subunitDropdown.val(groupUnitId)

                 if(document.getElementById('isstock' + item).value==1){
                      convertion(item, document.getElementById('product' + item).value, groupUnitId, groupUnitName)
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

                    if (document.getElementById('defaultsaleprice' + item).value == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = datas[0].subsell_price;
                    }

                    if (document.getElementById('defaultsaleprice' + item).value == 'mrp') {
                        var _mrpV = document.getElementById('mrpprice' + item).value;
                        if (document.getElementById('mrpprice' + item).value) {
                            document.getElementById('product_rate' + item).value = (_mrpV / datas[0].conversion_ratio).toFixed(2);
                        } else {
                            document.getElementById('product_rate' + item).value = datas[0].subsell_price;
                        }
                    }



                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value,
                        datas[0].convertiontype, datas[0].conversion_ratio)
                } else {
                    // alert("Conversion not found")
                    if (document.getElementById('defaultsaleprice' + item).value == 'fixedprice') {
                        document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                    }
                    if (document.getElementById('defaultsaleprice' + item).value == 'mrp') {
                        if (document.getElementById('mrpprice' + item).value) {
                            document.getElementById('product_rate' + item).value = document.getElementById('mrpprice' + item).value;
                        } else {
                            document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
                        }
                    }
                    getActiveSubUnit(document.getElementById('product' + item).value, item)
                    avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
                    // document.getElementById('product_rate' + item).value = document.getElementById('mastercost_price' + item).value;
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
                <div id="ap_success_msg" style="display:none;"></div>
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
    if (typeof stores !== 'undefined') {
        $.each(stores, function(i, s) { $store.append('<option value="' + s.id + '">' + s.name + '</option>'); });
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
        url: $('#base_url').val() + 'get_product_form_data',
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
        url: $('#base_url').val() + 'save_product_ajax',
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
                var pname2 = $('#ap_product_name').val().trim();
                var rowNum;
                var lastProd = document.getElementById('product' + (count - 1));
                if (lastProd && lastProd.value == '') {
                    rowNum = count - 1;
                } else {
                    rowNum = count;
                    document.getElementById('myRow' + rowNum).style.display = 'table-row';
                    count = count + 1;
                }
                document.getElementById('productInput' + rowNum).value = pname2;
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
$('#addProductModal').on('shown.bs.modal', function() {
    $('#addProductModal select.form-control').each(function() {
        if (!$(this).hasClass('select2-hidden-accessible')) {
            $(this).select2({ placeholder: 'Select option', allowClear: true, dropdownParent: $('#addProductModal') });
        }
    });
    $('#ap_product_name').focus();
});
$('#addProductModal').on('hidden.bs.modal', function() {
    $('#ap_barcode').val('');
    $('#ap_product_name').val('');
    $('#ap_save_btn').text('Save Product').prop('disabled', false);
});
$('#customerModel').on('shown.bs.modal', function() {
    $('#customer_name').focus();
});

/* ── Override window.alert to use toastr (brand page style) ── */
</script>