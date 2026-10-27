<style>
    /* select2 wraps #inventory_request_no in its own generated container
       right after it in the DOM — the <select>'s own inline width doesn't
       reach that widget, so the size has to be set on the container itself. */
    #inventory_request_no + .select2-container { width: 260px !important; max-width: 100%; }

    /* Header row (title/buttons) — a plain CSS rule rather than inline
       styles, so the mobile media query below (same selector, same
       specificity, declared later) can actually override it instead of
       losing to an inline style. */
    #style12 .padding-lefttitle {
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    #inventory_request_header_field {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        gap: 8px;
        vertical-align: middle;
    }

    .product_field { width: 200px; }
    .field { width: 150px; }
    .field2 { width: 100px; }
    .unit { width: 100px; }
    .qty { width: 120px; }

    .td-mobile-label { display: none; }

    /* ── Big-screen table layout: without this, columns with no explicit
       width (Av.Qty/Adj.Qty/Action) absorb all leftover horizontal space —
       Action in particular was stretching to half the screen for a single
       icon button. Shrink those to fit their content instead. ── */
    #normalinvoice { table-layout: auto; }
    #normalinvoice th:last-child, #normalinvoice td:last-child { width: 1%; white-space: nowrap; }
    #normalinvoice th, #normalinvoice td { vertical-align: middle; }

    #normalinvoice.hide-adjtype-col .adjtype-col,
    #normalinvoice.hide-adjtype-col #th_adjtype { display: none !important; }

    #normalinvoice.hide-store-col .store-col,
    #normalinvoice.hide-store-col #th_store { display: none !important; }

    /* ── Panel header responsive ── */
    @media (max-width: 1024px) {
        #style12 .panel-title {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }
        #style12 .panel-title > span:first-child {
            flex: 1 1 100%;
            font-size: 16px;
            font-weight: 600;
        }
        #style12 .padding-lefttitle {
            flex: 1 1 100%;
            padding-left: 0 !important;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        #style12 .padding-lefttitle .btn {
            flex: 1 1 auto;
            text-align: center;
            margin: 0 !important;
        }
        #inventory_request_header_field {
            flex: 1 1 100%;
            white-space: normal;
        }
        #inventory_request_header_field label {
            flex: 1 1 100%;
        }
        #inventory_request_no + .select2-container {
            width: 100% !important;
        }
    }

    /* ── Tablet 768–1024px ── */
    @media (min-width: 768px) and (max-width: 1024px) {
        .inv-form-section .form-group.row label.col-form-label {
            display: block; width: 100% !important; float: none !important;
            font-size: 10.5px; font-weight: 700; color: #777; text-transform: uppercase; margin-bottom: 2px;
        }
        .inv-form-section .form-group.row .col-sm-8 {
            width: 100% !important; float: none !important;
        }

        /* Needs to beat style.css's global ".panel-body .table-responsive"
           rule — same reason as the mobile breakpoint below. */
        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #normalinvoice { display: block; width: 100%; }
        #normalinvoice thead { display: none; }
        #normalinvoice tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        .td-mobile-label { display: block; font-size: 10px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 3px; }

        #normalinvoice tbody tr {
            display: grid; grid-template-columns: 1fr 1fr;
            width: 100%; box-sizing: border-box; margin-bottom: 20px;
            border: 1px solid #e0e0e0; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #normalinvoice tbody tr[style*="table-row"] { display: grid !important; grid-template-columns: 1fr 1fr; width: 100% !important; }

        /* Reset fixed column widths so grid 1fr 1fr takes effect */
        #normalinvoice tbody td,
        #normalinvoice tbody td.product_field,
        #normalinvoice tbody td.field,
        #normalinvoice tbody td.field2,
        #normalinvoice tbody td.unit,
        #normalinvoice tbody td.qty {
            display: block; width: auto !important; min-width: 0;
            box-sizing: border-box; padding: 10px 12px;
            border: none !important;
            border-right: 1px solid #f0f0f0 !important;
            border-bottom: 1px solid #f0f0f0 !important;
        }

        /* Force all dropdowns & inputs to fill the cell */
        #normalinvoice tbody td .form-control,
        #normalinvoice tbody td .chosen-container,
        #normalinvoice tbody td .chosen-container .chosen-single,
        #normalinvoice tbody td select { width: 100% !important; max-width: 100% !important; }

        #normalinvoice tbody td:last-child { grid-column: 1 / -1; border-bottom: none !important; border-right: none !important; padding: 0; }
        #normalinvoice tbody td:last-child .td-mobile-label { display: none; }
        #normalinvoice tbody td:last-child button { width: 100%; border-radius: 0; margin: 0; display: block; padding: 10px; font-size: 15px; }

        /* Qty alone: span full width when Adj.Type visible or Store hidden */
        #normalinvoice:not(.hide-adjtype-col) tbody .qty-input-col { grid-column: 1 / -1; }
        #normalinvoice.hide-store-col tbody .qty-input-col { grid-column: 1 / -1; }

        #normalinvoice tfoot { display: block; margin-top: 14px; }
        #normalinvoice tfoot tr { display: block; }
        #normalinvoice tfoot td { display: block !important; padding: 0; border: none !important; }
        #normalinvoice tfoot .btn { display: block; width: 100%; border-radius: 6px; padding: 11px; font-size: 15px; }
    }

    /* ── Mobile ≤767px ── */
    @media (max-width: 767px) {
        .inv-form-section .form-group.row label.col-form-label {
            display: block; width: 100% !important; float: none !important;
            font-size: 10.5px; font-weight: 700; color: #777; text-transform: uppercase; margin-bottom: 2px;
        }
        .inv-form-section .form-group.row .col-sm-8 {
            width: 100% !important; float: none !important;
        }

        .panel.panel-bd.lobidrag .panel-body .table-responsive { overflow: visible !important; }
        #normalinvoice { display: block; width: 100%; }
        #normalinvoice thead { display: none; }
        #normalinvoice tbody { display: block; width: 100%; padding: 4px 2px; background: #f4f6f8; border-radius: 8px; }
        .td-mobile-label { display: block; font-size: 10px; font-weight: 700; color: #999; text-transform: uppercase; margin-bottom: 3px; }
        #normalinvoice tbody tr {
            display: block; width: 100%; box-sizing: border-box; margin-bottom: 14px;
            border: 1px solid #e0e0e0; border-radius: 10px;
            overflow: hidden; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.07);
        }
        #normalinvoice tbody tr[style*="table-row"] { display: block !important; width: 100% !important; }
        #normalinvoice tbody td,
        #normalinvoice tbody td.product_field,
        #normalinvoice tbody td.field,
        #normalinvoice tbody td.field2,
        #normalinvoice tbody td.unit,
        #normalinvoice tbody td.qty {
            display: block; width: 100% !important; box-sizing: border-box; padding: 8px 12px;
            border: none !important; border-bottom: 1px solid #f0f0f0 !important; white-space: normal;
        }
        #normalinvoice tbody td .form-control,
        #normalinvoice tbody td .chosen-container,
        #normalinvoice tbody td .chosen-container .chosen-single,
        #normalinvoice tbody td select { width: 100% !important; max-width: 100% !important; }
        #normalinvoice tbody td:last-child { border-bottom: none !important; padding: 0; }
        #normalinvoice tbody td:last-child .td-mobile-label { display: none; }
        #normalinvoice tbody td:last-child button { width: 100%; border-radius: 0; margin: 0; display: block; padding: 10px; font-size: 15px; }

        #normalinvoice tfoot { display: block; margin-top: 14px; }
        #normalinvoice tfoot tr { display: block; }
        #normalinvoice tfoot td { display: block !important; padding: 0; border: none !important; }
        #normalinvoice tfoot .btn { display: block; width: 100%; border-radius: 6px; padding: 11px; font-size: 15px; }
    }

/* ── Panel & form styling ── */
.panel.panel-bd.lobidrag {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06) !important;
    border-radius: 14px !important;
    overflow: hidden !important;
    margin-bottom: 24px !important;
}
.panel.panel-bd.lobidrag .panel-heading {
    background: #ffffff !important;
    padding: 14px 22px !important;
    border: none !important;
    border-bottom: 2px solid #F1F5F9 !important;
}
.panel.panel-bd.lobidrag .panel-title {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .panel-title > span:first-child {
    color: #1E293B !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
}
.panel.panel-bd.lobidrag .panel-body { background: #ffffff !important; }
.inv-form-section .form-group { margin-bottom: 14px !important; align-items: center !important; }
.inv-form-section .col-form-label {
    font-size: 13px !important; font-weight: 600 !important;
    color: #374151 !important; text-align: left !important;
    padding-right: 14px !important; padding-top: 8px !important;
}
.inv-form-section .form-control {
    border: 1.5px solid #E2E8F0 !important; border-radius: 8px !important;
    padding: 8px 12px !important; font-size: 13px !important;
    color: #374151 !important; background: #F8FAFC !important;
    height: auto !important;
}
.inv-form-section .form-control:focus {
    border-color: #8b5cf6 !important; background: #fff !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,0.12) !important; outline: none !important;
}
#save_add {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important; border: none !important;
    border-radius: 8px !important; padding: 9px 28px !important;
    font-size: 13px !important; font-weight: 600 !important; color: #fff !important;
}
#save_add:hover { background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important; box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important; }
/* ── Table redesign — matches add_invoice screen ── */
#normalinvoice {
    border-collapse: collapse !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    box-shadow: 0 2px 8px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04) !important;
    border: 1px solid #E2E8F0 !important;
}
#normalinvoice thead th {
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
#normalinvoice thead th .text-danger { color: #ef4444 !important; }
#normalinvoice tbody td {
    padding: 6px 8px !important;
    font-size: 13px !important;
    color: #374151 !important;
    border-color: #F3F0FE !important;
    vertical-align: middle !important;
}
#normalinvoice tbody tr:hover td { background: #F3F0FE !important; }
#normalinvoice tfoot td {
    background: #F8FAFC !important;
    border-top: 2px solid #DDD6FE !important;
    border-color: #F3F0FE !important;
    padding: 55px 16px !important;
    vertical-align: top !important;
}
#normalinvoice tbody td .form-control {
    height: 30px !important;
    font-size: 12px !important;
    padding: 2px 6px !important;
    border: 1px solid #E2E8F0 !important;
    border-radius: 6px !important;
    background: #F8FAFC !important;
}
#normalinvoice tbody td .form-control:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
    outline: none !important;
    background: #fff !important;
}
#normalinvoice tbody td input.form-control {
    border: 1px solid #E2E8F0 !important;
    border-radius: 6px !important;
    color: #374151 !important;
}
#normalinvoice tbody td input.form-control:focus {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
    outline: none !important;
}
#normalinvoice tfoot .btn.btn-info {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 7px 18px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #fff !important;
}
#normalinvoice tfoot .btn.btn-info:hover { background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important; }

/* ── Select2 styling ── */
.select2-container .select2-selection--single{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;background:#F8FAFC !important;height:38px !important}
.select2-container .select2-selection--single .select2-selection__rendered{color:#374151 !important;font-size:13px !important;line-height:36px !important;padding-left:10px !important}
.select2-container .select2-selection--single .select2-selection__arrow{height:36px !important}
.select2-container--default.select2-container--focus .select2-selection--single,.select2-container--default.select2-container--open .select2-selection--single{border-color:#8b5cf6 !important;background:#fff !important;box-shadow:0 0 0 3px rgba(139,92,246,.12) !important;outline:none !important}
.select2-dropdown{border:1.5px solid #E2E8F0 !important;border-radius:8px !important;box-shadow:0 4px 16px rgba(0,0,0,.10) !important;margin-top:2px !important}
.select2-search--dropdown .select2-search__field{border:1.5px solid #E2E8F0 !important;border-radius:6px !important;font-size:13px !important;padding:5px 8px !important}
.select2-results__option{font-size:13px !important;padding:7px 12px !important;color:#374151 !important}
.select2-container--default .select2-results__option--highlighted[aria-selected]{background:#8b5cf6 !important;color:#fff !important}
.select2-container--default .select2-results__option[aria-selected=true]{background:#F3F0FE !important;color:#8b5cf6 !important}
.select2-selection__placeholder{color:#94A3B8 !important}

/* ── Header action buttons ── */
.btn-inv-product {
    background: #2563EB;
    color: #ffffff;
    border: 1.5px solid #1D4ED8;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 7px 16px;
    letter-spacing: 0.2px;
    box-shadow: 0 1px 4px rgba(37,99,235,0.18);
    transition: none;
}
.btn-inv-product:hover, .btn-inv-product:focus {
    background: #1D4ED8;
    border-color: #1E40AF;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(37,99,235,0.28);
}
.btn-inv-product i { color: #ffffff; margin-right: 4px; }

.btn-inv-batchlist {
    background: #F59E0B;
    color: #ffffff;
    border: 1.5px solid #D97706;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    padding: 7px 16px;
    letter-spacing: 0.2px;
    box-shadow: 0 1px 4px rgba(245,158,11,0.18);
    transition: none;
}
.btn-inv-batchlist:hover, .btn-inv-batchlist:focus {
    background: #D97706;
    border-color: #B45309;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(245,158,11,0.28);
}
.btn-inv-batchlist i { color: #ffffff; margin-right: 4px; }

/* Unit Cost Logic Specification v1.1 — Store Transfer and Stock Adjustment (Physical)
   have no cost effect (UCL-44), so their Unit Cost / Value columns are hidden. */
#normalinvoice.hide-cost-col .unitcost-col,
#normalinvoice.hide-cost-col .value-col,
#normalinvoice.hide-cost-col #th_unitcost,
#normalinvoice.hide-cost-col #th_value { display: none !important; }
</style>




<div class="row">

    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading" id="style12">
                <div class="panel-title has-goback">
                    <span><?php echo $title; ?></span>

                    <span class="padding-lefttitle">
                        <?php if (empty($id)): ?>
                        <span id="inventory_request_header_field">
                            <label for="inventory_request_no" style="margin:0; font-weight:600; font-size:13px;">Inventory Transaction Request</label>
                            <select class="form-control" id="inventory_request_no" name="inventory_request_no" onchange="getInventoryRequestDetails(this.value)" style="display:inline-block; vertical-align:middle;" >
                                <option value="">Select</option>
                            </select>
                        </span>
                        <input type="hidden" id="inventory_request_id" value="0" />
                        <?php endif; ?>
                        <button class="btn btn-inv-product m-b-5 m-r-2" onclick="openAddProductModal()"><i class="fa fa-plus"></i> Add Product</button>
                        <?php if ($this->permission1->method('add_stockbatch', 'create')->access()) { ?>
                            <button class="btn btn-primary m-b-5 m-r-2" onclick="openStockbatch()"><i class="ti-plus"> </i> <?php echo display('add_stockbatch') ?> </button>
                        <?php } ?>
                        <?php if ($this->permission1->method('stockbatchlist', 'create')->access()) { ?>
                            <button class="btn btn-inv-batchlist m-b-5 m-r-2" onclick="manageStockbatch()"><i class="fa fa-cube"></i> <?php echo display('stockbatchlist') ?> </button>
                        <?php } ?>
                    </span>
                
<a href="<?php echo base_url('manage_inventory_transection') ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>


            </div>

            <?php if (!empty($id)): ?>
            <div style="background:#FEF3C7; color:#92400E; border:1px solid #FBBF24; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-pencil"></i> Editing Inventory Transaction #<?php echo (int)$id; ?> — changes are saved only when you click Update.
            </div>
            <?php if (!empty($source_request_no)): ?>
            <div style="background:#DBEAFE; color:#1E40AF; border:1px solid #93C5FD; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-exchange"></i> Converted from Inventory Transaction Request <?php echo html_escape($source_request_no); ?>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <div id="convert_request_banner" style="display:none; background:#DBEAFE; color:#1E40AF; border:1px solid #93C5FD; border-radius:6px; padding:10px 16px; margin:16px 20px 0; font-size:13px; font-weight:600;">
                <i class="fa fa-exchange"></i> Converting from Inventory Transaction Request <span id="convert_request_label"></span>
            </div>

            <input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
            <div class="panel-body inv-form-section" style="margin: 20px;">

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="date" class="col-sm-4 col-form-label">Date
                                <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <?php
                                date_default_timezone_set('Asia/Colombo');

                                $date = date('Y-m-d');
                                ?>

                                <?php if (!empty($id)) { ?>
                                    <input class="form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" readonly />
                                <?php } else { ?>
                                    <input class="datepicker form-control" type="text" size="50" name="invoice_date" id="date" required value="<?php echo html_escape($date); ?>" tabindex="4" />

                                <?php } ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Incident Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="type" required name="type" tabindex="3" onchange="change_type()">
                                    <option value=""></option>
                                    <option value="openingstock" selected>Opening Stock</option>
                                    <option value="storetransfer">Store Transfer</option>
                                    <option value="stockdisposal">Stock Disposal</option>
                                    <option value="stockadjustment">Stock Adjustment</option>

                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6" id="stocktype_row" style="display:none;">
                        <div class="form-group row">
                            <label for="supplier_sss" class="col-sm-4 col-form-label">Stock Type
                                <i class="text-danger">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control" id="stocktype" required name="type" tabindex="3" onchange="quantity_calculate(0,'stocktype')">
                                    <!-- <option value=""></option>
                                    <option value="actualstock">Actual Stock</option>
                                    <option value="physicalstock">Physical Stock</option>
                                    <option value="both">Both</option> -->

                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="col-sm-6" id="from_store_row" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">From Store <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="from_store" name="from_store" onchange="syncStoreDropdowns('from_store','to_store')">
                                    <option value="">Select Store</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="to_store_row" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label">To Store <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control" id="to_store" name="to_store" onchange="syncStoreDropdowns('to_store','from_store')">
                                    <option value="">Select Store</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="address" class="col-sm-4 col-form-label">Reason
                            </label>
                            <div class="col-sm-8">

                                <textarea tabindex="" class="form-control" id="reason" name="reason" placeholder="Reasons" rows="3"></textarea>

                            </div>
                        </div>
                    </div>

                </div>


            </div>

            <div style="margin: 20px;">
                <table class="table table-bordered table-hover" id="normalinvoice">
                    <thead>
                        <tr>
                            <th class="text-center product_field">Product <i
                                    class="text-danger">*</i></th>
                            <th class="text-center product_field" id="th_store">Store <i class="text-danger">*</i>
                            </th>
                            <th class="text-center product_field">Batch <i class="text-danger">*</i>
                            </th>
                            <th class="text-center product_field">Unit <i class="text-danger">*</i></th>
                            <th class="text-center ">Av.Qty</th>
                            <th class="text-center " id="th_adjtype">Adj.Type <i
                                    class="text-danger">*</i></th>
                            <th class="text-center " id="th_adjqty">Adj.Qty <i
                                    class="text-danger">*</i></th>

                            <th class="text-center " id="th_unitcost">Unit Cost</th>
                            <th class="text-center " id="th_value">Value</th>

                            <th class="text-center"><?php echo display('action') ?></th>
                        </tr>
                    </thead>
                    <tbody id="addinvoiceItem">
                        <tr id="myRow1">
                            <td class="product_field">

                                <div style='position: relative; display: inline-block; width:100%;'>
                                    <input class='form-control' type='text' id="productInput1" placeholder='Product...' onkeyup='handleProductKeyPress(event,1)' autocomplete='off' />
                                    <input type='hidden' name='product[]' id='product1' />
                                    <div id='productResults1' style='position: absolute; z-index: 99999 !important; max-height: 150px; overflow-y: auto; border: 1px solid #ddd; background-color: #fff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'></div>
                                </div>

                                <input type="hidden" id="mconversion_ratio1" />
                                <input type="hidden" id="bd1" />
                                <input type="hidden" id="ad1" />


                            </td>
                            <td class="field store-col">
                                <select class="form-control" id="store1" required name="store[]" tabindex="3" onchange="quantity_calculate(1,'store')">
                                    <option value=""></option>
                                </select>
                            </td>
                            <td class="batch">
                                <select class="form-control" id="batch1" required name="batch[]" tabindex="3" onchange="quantity_calculate(1,'batch')">
                                    <option value=""></option>
                                </select>
                            </td>




                            <td class="unit">
                                <!-- <input type="text" name="unit[]" onkeyup="quantity_calculate(1,'unit');"
                                    class="total_qntt_1 form-control text-right"
                                    id="unit1" value="" min="0" readonly /> -->

                                <select class="form-control" id="unit1" required name="unit1" onchange="quantity_calculate(1,'unit')" tabindex="3">
                                    <option value=""></option>
                                </select>
                                <input type="hidden" id="conversionid1" />
                                <input type="hidden" id="conversiontype1" />
                                <input type="hidden" id="conversion_ratio1" />




                            </td>
                            <td class="qty">
                                <input type="hidden" name="code[]" required onkeyup="quantity_calculate(1,'code');"
                                    class="total_qntt_1 form-control text-right"
                                    id="code1" placeholder="0.00" min="0" readonly />
                                <span id='codetype1' style="margin-left:5px"></span>

                            </td>
                            <td class="field2 adjtype-col">


                                <select class="form-control" id="adj1" required name="adj[]" tabindex="4" onchange="quantity_calculate(1,'adj')">
                                    <option value=""></option>

                                    <!-- <option value="increase">Increase</option>
                                    <option value="decrease">Decrease</option> -->


                                </select>

                            </td>
                            <td class="qty qty-input-col">
                                <input type="number" name="qty[]" required onkeyup="quantity_calculate(1,'qty');"
                                    class="total_qntt_1 form-control text-right"
                                    id="qty1" placeholder="0.00" min="0" min="0" oninput="if(this.value < 0) this.value = ''" tabindex="5" />
                            </td>

                            <td class="unitcost-col">
                                <input type="number" step="0.01" min="0" class="form-control text-right" id="unitcost1" placeholder="0.00" oninput="costRowChanged(1)" tabindex="6" />
                            </td>
                            <td class="value-col">
                                <input type="text" class="form-control text-right" id="value1" readonly tabindex="-1" />
                            </td>

                            <td>
                                <button class='btn btn-danger' type='button' onclick='deleteRow(1)'>
                                    <i class='fa fa-trash'></i>
                                </button>
                            </td>
                        </tr>

                        <?php
                        // Assuming you want to generate 5 rows dynamically
                        for ($i = 2; $i <= 20; $i++) {
                        ?>
                            <tr id="myRow<?php echo $i; ?>" style="display:none;">
                                <td class="product_field">
                                    <div style='position: relative; display: inline-block; width:100%;'>
                                        <input class='form-control' type='text' id="productInput<?php echo $i; ?>" placeholder='Product...' onkeyup='handleProductKeyPress(event,<?php echo $i; ?>)' autocomplete='off' />
                                        <input type='hidden' name='product[]' id='product<?php echo $i; ?>' />
                                        <div id='productResults<?php echo $i; ?>' style='position: absolute; z-index: 99999 !important; max-height: 150px; overflow-y: auto; border: 1px solid #ddd; background-color: #fff; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'></div>
                                    </div>
                                    <input type="hidden" id="mconversion_ratio<?php echo $i; ?>" />
                                    <input type="hidden" id="bd<?php echo $i; ?>" />
                                    <input type="hidden" id="ad<?php echo $i; ?>" />

                                </td>
                                <td class="field store-col">
                                    <select name="store[]" class="form-control" id="store<?php echo $i; ?>" tabindex="3" onchange="quantity_calculate(<?php echo $i; ?>, 'store')" required>
                                        <option value="">Select Store</option>
                                    </select>
                                </td>
                                <td class="batch">
                                    <select class="form-control" id="batch<?php echo $i; ?>" required name="batch[]" tabindex="3" onchange="quantity_calculate(<?php echo $i; ?>,'batch')">
                                        <option value=""></option>
                                    </select>
                                </td>




                                <td class="unit">
                                    <!-- <input type="text" name="unit[]" onkeyup="quantity_calculate(, 'unit');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="unit" value="" min="0" readonly /> -->

                                    <select class="form-control" id="unit<?php echo $i; ?>" required name="unit<?php echo $i; ?>" onchange="quantity_calculate(<?php echo $i; ?>,'unit')" tabindex="3">
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" id="conversionid<?php echo $i; ?>" />
                                    <input type="hidden" id="conversiontype<?php echo $i; ?>" />
                                    <input type="hidden" id="conversion_ratio<?php echo $i; ?>" />



                                </td>

                                <td class="qty">
                                    <input type="hidden" name="code[]" required onkeyup="quantity_calculate(<?php echo $i; ?>,'code');"
                                        class="total_qntt_1 form-control text-right"
                                        id="code<?php echo $i; ?>" placeholder="0.00" min="0" readonly />
                                    <span id='codetype<?php echo $i; ?>' style="margin-left:5px"></span>

                                </td>
                                <td class="field2 adjtype-col">


                                    <select class="form-control" id="adj<?php echo $i; ?>" required name="adj[]" tabindex="4" onchange="quantity_calculate(<?php echo $i; ?>,'adj')">
                                        <option value=""></option>
                                        <!-- <option value="increase">Increase</option>
                                        <option value="decrease">Decrease</option>
 -->

                                    </select>
                                </td>
                                <td class="qty qty-input-col">
                                    <input type="number" name="qty[]" onkeyup="quantity_calculate(<?php echo $i; ?>, 'qty');"
                                        onchange="quantity_calculate(1);" class="total_qntt_1 form-control text-right"
                                        id="qty<?php echo $i; ?>" placeholder="0.00" min="0" tabindex="5" />
                                </td>

                                <td class="unitcost-col">
                                    <input type="number" step="0.01" min="0" class="form-control text-right" id="unitcost<?php echo $i; ?>" placeholder="0.00" oninput="costRowChanged(<?php echo $i; ?>)" tabindex="6" />
                                </td>
                                <td class="value-col">
                                    <input type="text" class="form-control text-right" id="value<?php echo $i; ?>" readonly tabindex="-1" />
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
                            <td colspan="10">
                                <button type="button" id="add_invoice_item" class="btn btn-info"
                                    name="add-invoice-item" onClick="addInputField('addinvoiceItem');"><i
                                        class='fa fa-plus'></i> Add New Item</button>
                            </td>


                        </tr>
                        <tr id="total_value_row" style="display:none;">
                            <td class="product_field"></td>
                            <td class="field store-col"></td>
                            <td class="batch"></td>
                            <td class="unit"></td>
                            <td class="qty"></td>
                            <td class="field2 adjtype-col"></td>
                            <td class="qty qty-input-col"></td>
                            <td class="unitcost-col text-right"><strong>Total Value</strong></td>
                            <td class="value-col"><input type="text" id="total_value" class="form-control text-right" readonly tabindex="-1" /></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>



                <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                <p hidden id="old-amount"><?php echo 0; ?></p>
                <p hidden id="pay-amount"></p>
                <p hidden id="change-amount"></p>
            </div>
            <div class="form-group row text-right" style="margin-right: 5px;">
                <div class="col-sm-12 p-20">
                    <!-- <input type="button" id="add_invoice" class="btn btn-success" name="add-invoice"
                        value="Save" tabindex="17"  /> -->

                    <button id="save_add" class="btn btn-success btn-save-anim" name="add-invoice" onclick="save()">
                        <?php echo '<i class="fa ' . (empty($id) ? 'fa-save' : 'fa-pencil') . '"></i> ' . (empty($id) ? display('save') : display('update')); ?></button>




                </div>
            </div>
        </div>
    </div>
</div>


<script>
(function () {
    var colLabels = ['Product', 'Store', 'Batch', 'Unit', 'Av.Qty', 'Adj.Type', 'Qty', 'Action'];
    document.querySelectorAll('#normalinvoice tbody tr').forEach(function (row) {
        row.querySelectorAll('td').forEach(function (td, i) {
            if (!colLabels[i]) return;
            var lbl = document.createElement('span');
            lbl.className = 'td-mobile-label';
            lbl.textContent = colLabels[i];
            td.insertBefore(lbl, td.firstChild);
        });
    });
})();
</script>

<?php
echo "<script>";
echo "var id = " . json_encode($id) . ";";
echo "let convertRequestId = " . json_encode(isset($convert_request_id) ? $convert_request_id : null) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let type=" . json_encode($type) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let stores=" . json_encode($store_list) . ";";
echo "let units=" . json_encode($units) . ";";
echo "let stockbatchopening=" . json_encode($stockbatchopening) . ";";
echo "let modalProducts=" . json_encode($modal_products ? $modal_products : []) . ";";
echo "let ap_categories=" . json_encode($category_list ?: []) . ";";
echo "let ap_units=" . json_encode($unit_list ?: []) . ";";
echo "let ap_suppliers=" . json_encode($all_supplier ?: []) . ";";
echo "</script>";
?>

<script>
    let count = 2
    let pendingBatchSelect = {};
    let type2 = (usertype == 3) ? "B" : "A";

    let oldType = "";
    document.addEventListener("keydown", function(event) {
        // Check if the pressed key is F2
        if (event.key === "F2") {
            window.open(
                $('#baseUrl2').val() + "stockbatch_form",
                "popupWindow",
                "width=1000,height=800,scrollbars=yes"
            );
        }
        if (event.key === "F3") {
            window.open(
                $('#baseUrl2').val() + "stockbatchlist",
                "popupWindow",
                "width=1000,height=800,scrollbars=yes"
            );
        }
    });

    $(document).ready(function() {
        // document.getElementById('showBtn1').style.display = 'none';

        // Apply Select2 to static header form selects
        $('#type').select2({placeholder: 'Select option', allowClear: true});
        $('#type').on('select2:select select2:unselect', function() { change_type(); });

        if (usertype == 3) {
            document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            const title = document.getElementById('title');
            title.style.color = 'blue';
        }
        //  $('body').addClass("sidebar-mini sidebar-collapse");

        getInventoryRequestDropdown(convertRequestId);
    });

    // Lists open (not-yet-converted) Inventory Requests so one can be pulled
    // into this New Inventory transaction — mirrors how Sale/Purchase load
    // their Sales/Purchase Order dropdown. When preselectId is set (arrived
    // here via the Manage Inventory Request "Convert" button), it's selected
    // and loaded automatically once the options are in, the same way
    // convert_sale/convert_purchase auto-load their source order.
    function getInventoryRequestDropdown(preselectId) {
        var $dd = $('#inventory_request_no');
        if ($dd.length === 0) return;
        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/getinventoryrequestlist',
            type: 'POST',
            success: function(response) {
                var requests = JSON.parse(response);
                $dd.find('option:not(:first)').remove();
                $.each(requests, function(index, r) {
                    $dd.append('<option value="' + r.id + '">' + r.request_no + '</option>');
                });
                if (preselectId) {
                    $dd.val(preselectId).trigger('change');
                }
            },
            error: function(error) { console.log(error); }
        });
    }

    // Pulls one Inventory Request's header + every line into this form —
    // same role as getSalesOrderDetails()/purchaseOrderDetails() in the
    // Sale/Purchase screens.
    function getInventoryRequestDetails(id) {
        if (!id) {
            document.getElementById('inventory_request_id').value = 0;
            document.getElementById('convert_request_banner').style.display = 'none';
            return;
        }
        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/getInventoryRequestById',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                var rows = JSON.parse(response);
                if (!rows || rows.length === 0) {
                    alert("This request could not be loaded (it may have already been converted).");
                    document.getElementById('convert_request_banner').style.display = 'none';
                    return;
                }

                document.getElementById('inventory_request_id').value = id;
                document.getElementById('convert_request_label').textContent = $('#inventory_request_no option:selected').text();
                document.getElementById('convert_request_banner').style.display = 'block';
                document.getElementById('date').value = rows[0].date;
                document.getElementById('reason').value = rows[0].reason;
                $('#type').val(rows[0].type).trigger('change');
                change_type();
                document.getElementById('stocktype').value = rows[0].stocktype;
                oldType = rows[0].stocktype;

                for (let i = 2; i <= 20; i++) {
                    document.getElementById('myRow' + i).style.display = 'none';
                }
                count = 1;

                // Store Transfer requests capture one From/To Store pair for
                // the whole request (not per-row) — change_type() above
                // already populated #from_store/#to_store's option lists for
                // this incident type, so just select the saved values.
                if (rows[0].type === 'storetransfer') {
                    $('#from_store').val(rows[0].store).trigger('change');
                    syncStoreDropdowns('from_store', 'to_store');
                    $('#to_store').val(rows[0].to_store).trigger('change');
                }

                for (let i = 0; i < rows.length; i++) {
                    let a = i + 1;
                    document.getElementById('myRow' + a).style.display = 'table-row';
                    getActiveProduct(rows[i].product, a);
                    getActiveStore(rows[i].store, a);
                    document.getElementById('qty' + a).value = Math.abs(rows[i].actualstock);
                    getAdjDropdown(rows[i].actualstock > 0 ? "increase" : "decrease", a);
                    getActiveSubUnitEdit(rows[i].product, a, rows[i].unit, rows[i].conversion_id, rows[i].conversion_ratio, rows[i].convertiontype, rows[i].avstock, rows[i].stocktype);
                    getBatchDropdown(null, a, rows[i].batch, rows[i].product, rows[i].batchtype);
                    count = count + 1;
                }
            },
            error: function(error) { console.log(error); }
        });
    }

    function openStockbatch(rowNum) {
        if (document.getElementById('stocktype').value == "") {
            alert("Stock Type shouldn't be empty");
            return
        } else if (document.getElementById('type').value == "") {
            alert("Type shouldn't be empty");
            return
        } else {
            // Reset all modal fields
            document.getElementById('mb_batchid').value = '';
            document.getElementById('mb_details').value = '';
            document.getElementById('mb_busage').value = '';
            document.getElementById('mb_status').value = '1';
            document.getElementById('mb_mdate').value = '';
            document.getElementById('mb_pdate').value = '';
            document.getElementById('mb_edate_toggle').value = 'no';
            document.getElementById('mb_edate').value = '';
            document.getElementById('mb_edate_row').style.display = 'none';
            document.getElementById('mb_mrp').value = '';
            $('#mb_product').val('');
            $('#mb_singleshow, #mb_singleshow1, #mb_singleshow2, #mb_singleshow3, #mb_singleshow4').hide();
            $('#mb_save_btn').prop('disabled', false).text('Save Batch');
            $('#addBatchModal').modal('show');

        }

    }

    function openRowBatch(rowNum) {
        openStockbatch(rowNum);
    }

    function manageStockbatch() {
        window.open(
            $('#baseUrl2').val() + "stockbatchlist",
            "popupWindow",
            "width=1000,height=800,scrollbars=yes"
        );
    }



    function change_type() {
        var incidentType = document.getElementById('type').value;
        var $stocktypeDropdown = $('#stocktype');
        var table = document.getElementById('normalinvoice');
        var stocktypeRow = document.getElementById('stocktype_row');
        var fromStoreRow = document.getElementById('from_store_row');
        var toStoreRow = document.getElementById('to_store_row');
        var thAdjQty = document.getElementById('th_adjqty');

        // Reset all toggles
        stocktypeRow.style.display = '';
        fromStoreRow.style.display = 'none';
        toStoreRow.style.display = 'none';
        table.classList.remove('hide-adjtype-col', 'hide-store-col');
        thAdjQty.innerHTML = 'Adj.Qty <i class="text-danger">*</i>';
        $stocktypeDropdown.empty();

        if (incidentType === "openingstock") {
            $stocktypeDropdown.append('<option value="both" selected>Both</option>');
            stocktypeRow.style.display = 'none';
            table.classList.add('hide-adjtype-col');
            thAdjQty.innerHTML = 'Qty <i class="text-danger">*</i>';
        } else if (incidentType === "storetransfer") {
            $stocktypeDropdown.append('<option value="actualstock" selected>Actual Stock</option>');
            stocktypeRow.style.display = 'none';
            fromStoreRow.style.display = '';
            toStoreRow.style.display = '';
            populateStoreSelect('from_store');
            populateStoreSelect('to_store');
            table.classList.add('hide-adjtype-col', 'hide-store-col');
            thAdjQty.innerHTML = 'Qty <i class="text-danger">*</i>';
        } else if (incidentType === "stockdisposal") {
            $stocktypeDropdown.append('<option value="actualstock" selected>Actual Stock</option>');
            stocktypeRow.style.display = 'none';
            table.classList.add('hide-adjtype-col');
            thAdjQty.innerHTML = 'Qty <i class="text-danger">*</i>';
        } else if (incidentType === "stockadjustment") {
            $stocktypeDropdown.append('<option value="" disabled selected>Select </option>');
            $stocktypeDropdown.append('<option value="actualstock">Actual Stock</option>');
            $stocktypeDropdown.append('<option value="physicalstock">Physical Stock</option>');
            $stocktypeDropdown.append('<option value="both">Both</option>');
        }

        clearTable();
        adj_type(1);
        updateCostColumnState();
    }

    function populateStoreSelect(elementId) {
        var $sel = $('#' + elementId);
        $sel.empty();
        $sel.append('<option value="">Select Store</option>');
        stores.forEach(function(s) {
            $sel.append('<option value="' + s.id + '">' + s.name + '</option>');
        });
    }

    function syncStoreDropdowns(changedId, otherId) {
        var selectedVal = document.getElementById(changedId).value;
        var $other = $('#' + otherId);
        var currentOtherVal = $other.val();
        $other.empty();
        $other.append('<option value="">Select Store</option>');
        stores.forEach(function(s) {
            if (s.id != selectedVal) {
                $other.append('<option value="' + s.id + '">' + s.name + '</option>');
            }
        });
        if (currentOtherVal && currentOtherVal !== selectedVal) {
            $other.val(currentOtherVal);
        }

        // When from_store changes, recalculate avstock for all visible rows
        if (changedId === 'from_store' && selectedVal) {
            for (let i = 1; i < count; i++) {
                var row = document.getElementById('myRow' + i);
                if (row && row.style.display !== 'none') {
                    var prod = document.getElementById('product' + i).value;
                    var batch = document.getElementById('batch' + i).value;
                    var ctype = document.getElementById('conversiontype' + i).value;
                    var cratio = document.getElementById('conversion_ratio' + i).value;
                    if (prod) {
                        avStock(i, prod, selectedVal, batch || 1, ctype, cratio);
                    }
                }
            }
        }
    }

    var batchNum = 0;
    var batchdetails = [];

    function showpopup(num) {
        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/getproductBatchCount',
            type: 'POST',
            data: {
                prodid: document.getElementById('product' + num).value.toString(),
                batch: document.getElementById('batch' + num).value.toString(),

            },
            success: function(response2) {
                if (response2 == 0) {

                    document.getElementById('produId').value = document.getElementById('product' + num).value.toString()
                    document.getElementById('batchuId').value = document.getElementById('batch' + num).value.toString()

                    // $("#model2").modal('show');
                }
                //

                //   document.getElementById('unit' + item).value = product[0].unit;
            },
            error: function(error) {
                console.log(error)
            }
        });

    }

    function closeBatch() {
        if (confirm("Do you want to save these details")) {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/addStockbatchOpening',
                type: 'POST',
                data: {
                    prodid: document.getElementById('produId').value.toString(),
                    batch: document.getElementById('batchuId').value.toString(),
                    mdate: document.getElementById('mdate').value,
                    edate: document.getElementById('edate').value,
                    mrp: document.getElementById('mrp').value,
                    pdate: document.getElementById('pdate').value,

                },
                success: function(response2) {
                    alert("Product batch details added successfully");
                    document.getElementById('produId').value = "";
                    document.getElementById('batchuId').value = "";
                    document.getElementById('mdate').value = "";
                    document.getElementById('edate').value = "";
                    document.getElementById('mrp').value = "";
                    document.getElementById('pdate').value = "";
                    $("#model2").modal('hide');

                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

    }

    function adj_type(num) {
        var incidentType = document.getElementById('type').value;
        var $adjtypeDropdown = $('#adj' + num);
        $adjtypeDropdown.empty();

        if (incidentType === "openingstock") {
            $adjtypeDropdown.append('<option value="increase" selected>Increase</option>');
        } else if (incidentType === "storetransfer") {
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>');
            $adjtypeDropdown.append('<option value="increase">Increase</option>');
            $adjtypeDropdown.append('<option value="decrease">Decrease</option>');
        } else if (incidentType === "stockdisposal") {
            $adjtypeDropdown.append('<option value="decrease" selected>Decrease</option>');
        } else if (incidentType === "stockadjustment") {
            $adjtypeDropdown.append('<option value="" disabled selected>Select </option>');
            $adjtypeDropdown.append('<option value="increase">Increase</option>');
            $adjtypeDropdown.append('<option value="decrease">Decrease</option>');
        }
    }

    $(document).ready(function() {

        for (let i = 2; i <= 20; i++) {
            document.getElementById('myRow' + i).style.display = 'none';

        }
        if (id != null) {
            document.getElementById('type').value = type.type;
            change_type()
            document.getElementById('stocktype').value = type.stocktype;

            oldType = type.stocktype;


            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getAdjStockById',
                type: 'POST',
                data: {
                    pid: id,
                    type: type.stocktype
                },
                success: function(response) {
                    var adjStocks = JSON.parse(response);
                    count = 1;

                    if (type.type === 'storetransfer') {
                        // Separate decrease (from_store) and increase (to_store) rows
                        var fromStoreId = null, toStoreId = null;
                        var fromRows = [];
                        adjStocks.forEach(function(row) {
                            if (parseFloat(row.actualstock) < 0) {
                                if (!fromStoreId) fromStoreId = row.store;
                                fromRows.push(row);
                            } else {
                                if (!toStoreId) toStoreId = row.store;
                            }
                        });

                        // Populate From Store and To Store global selects
                        populateStoreSelect('from_store');
                        if (fromStoreId) $('#from_store').val(fromStoreId);
                        syncStoreDropdowns('from_store', 'to_store');
                        if (toStoreId) $('#to_store').val(toStoreId);

                        // Load one row per product (from_store rows only)
                        for (var i = 0; i < fromRows.length; i++) {
                            var a = i + 1;
                            document.getElementById('myRow' + a).style.display = 'table-row';
                            getActiveProduct(fromRows[i].product, a);
                            document.getElementById('qty' + a).value = Math.abs(fromRows[i].actualstock);
                            if (type.stocktype != "both") {
                                document.getElementById('code' + a).value = fromRows[i].avstock;
                            }
                            getActiveSubUnitEdit(fromRows[i].product, a, fromRows[i].unit, fromRows[i].conversion_id, fromRows[i].conversion_ratio, fromRows[i].convertiontype, fromRows[i].avstock, type.stocktype);
                            getBatchDropdown(null, a, fromRows[i].batch, fromRows[i].product, fromRows[i].batchtype);
                            document.getElementById('date').value = fromRows[i].date;
                            document.getElementById('reason').value = fromRows[i].reason;
                            count = count + 1;
                        }

                    } else {
                        for (let i = 0; i < adjStocks.length; i++) {
                            let a = i + 1;
                            document.getElementById('myRow' + a).style.display = 'table-row';
                            // Restore the saved Unit Cost before getActiveProduct()'s async
                            // response runs — its Opening Stock default only fills an empty field,
                            // so without this an edit would silently replace the originally
                            // entered cost with today's Fixed Purchase Price.
                            if (type.type === 'openingstock' && parseFloat(adjStocks[i].unit_cost) > 0) {
                                document.getElementById('unitcost' + a).value = parseFloat(adjStocks[i].unit_cost);
                            }
                            getActiveProduct(adjStocks[i].product, a);
                            getActiveStore(adjStocks[i].store, a);
                            document.getElementById('qty' + a).value = Math.abs(adjStocks[i].actualstock);
                            if (type.stocktype != "both") {
                                document.getElementById('code' + a).value = adjStocks[i].avstock;
                            }
                            if (type.type === 'openingstock') {
                                costRowChanged(a);
                            }
                            getAdjDropdown(adjStocks[i].actualstock > 0 ? "increase" : "decrease", a);
                            getActiveSubUnitEdit(adjStocks[i].product, a, adjStocks[i].unit, adjStocks[i].conversion_id, adjStocks[i].conversion_ratio, adjStocks[i].convertiontype, adjStocks[i].avstock, type.stocktype);
                            getBatchDropdown(null, a, adjStocks[i].batch, adjStocks[i].product, adjStocks[i].batchtype);
                            document.getElementById('date').value = adjStocks[i].date;
                            document.getElementById('reason').value = adjStocks[i].reason;
                            count = count + 1;
                        }
                        updateCostColumnState();
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        } else {
            change_type();
        }


    });


    function addInputField(t) {
        if (document.getElementById('type').value == "") {
            alert("Please select incident type");
        } else {
            document.getElementById('myRow' + count).style.display = 'table-row';
            getActiveStore(0, count);
            count = count + 1;

            // if (document.getElementById('type').value === "openingstock") {
            //     document.getElementById('showBtn' + count).style.display = 'inline-block';
            // }
            adj_type(count)
            updateCostColumnState();
        }


    }

    function restoreSaveBtn() {
        var $b = $('#save_add');
        $b.prop('disabled', false).removeClass('btn-save-anim-loading').html($b.data('origHtml'));
    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    /* ── Print after save/update — same pattern as Sale/Customer Receive ── */
    function printRawHtml(view, voucherNo) {
        if (voucherNo) {
            document.title = 'inventorytransaction_' + voucherNo + '_' + pdfDateStamp();
        }
        $(view).print({
            deferred: $.Deferred().done(function() {
                window.location.href = $('#baseUrl2').val() + 'manage_inventory_transection';
            })
        });
    }

    function save() {
        var arrItem2 = [];
        var incidentType = document.getElementById('type').value;
        var fromStore = document.getElementById('from_store') ? document.getElementById('from_store').value : '';
        var toStore = document.getElementById('to_store') ? document.getElementById('to_store').value : '';

        if (incidentType === "storetransfer") {
            if (!fromStore) { alert("From Store shouldn't be empty"); return; }
            if (!toStore) { alert("To Store shouldn't be empty"); return; }
        }

        for (let i = 1; i < count; i++) {
            if (document.getElementById('myRow' + i).style.display != "none") {

                if (document.getElementById('stocktype').value == "") {
                    alert("Stock Type shouldn't be empty");
                    return
                } else if (incidentType == "") {
                    alert("Type shouldn't be empty");
                    return
                } else if (document.getElementById('product' + i).value == "") {
                    alert("Product shouldn't be empty (row " + i + ")");
                    return
                } else if (incidentType !== "storetransfer" && document.getElementById('store' + i).value == "") {
                    alert("Store shouldn't be empty (row " + i + ")");
                    return

                } else if (document.getElementById('qty' + i).value == "") {
                    alert("Quantity shouldn't be empty (row " + i + ")");
                    return

                } else if (incidentType !== "storetransfer" && document.getElementById('adj' + i).value == "") {
                    alert("Adj Type shouldn't be empty (row " + i + ")");
                    return
                } else if (incidentType === "openingstock" && !(parseFloat(document.getElementById('unitcost' + i).value) > 0)) {
                    alert("Unit Cost is required and must be greater than 0 (row " + i + ")");
                    return
                } else {
                    batch = batchdetails.find(batch => batch.batchnum == i);

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

                    var rowStore = (incidentType === "storetransfer") ? fromStore : document.getElementById('store' + i).value;
                    var rowAdj = document.getElementById('adj' + i).value;
                    var unitName = units.find(unit => unit.unit_id == document.getElementById('unit' + i).value);
                    var aqtyStr = document.getElementById('qty' + i).value + " " + (unitName ? unitName.unit_name : '');

                    // UCL-40/UCL-11: for Opening Stock, line value is qty-in-display-unit x
                    // unitcost-in-display-unit, computed here rather than from the base-unit
                    // converted qty above — the value is never converted, only the quantity.
                    var rowUnitCost = document.getElementById('unitcost' + i) ? document.getElementById('unitcost' + i).value : 0;
                    var rowLineValue = (parseFloat(document.getElementById('qty' + i).value) || 0) * (parseFloat(rowUnitCost) || 0);

                    if (batch == undefined) {
                        arrItem2.push({
                            product: document.getElementById('product' + i).value,
                            store: rowStore,
                            from_store: fromStore,
                            to_store: toStore,
                            quantity: qty,
                            date: document.getElementById('date').value,
                            reason: document.getElementById('reason').value,
                            adj: rowAdj,
                            unit: document.getElementById('unit' + i).value,
                            conversionid: document.getElementById('conversionid' + i).value,
                            type: incidentType,
                            stocktype: document.getElementById('stocktype').value,
                            type2: "A",
                            batch: document.getElementById('batch' + i).value,
                            mdate: "",
                            edate: "",
                            mrp: "",
                            aqty: aqtyStr,
                            unitcost: rowUnitCost,
                            linevalue: rowLineValue,
                        });

                    } else {
                        arrItem2.push({
                            product: document.getElementById('product' + i).value,
                            store: rowStore,
                            from_store: fromStore,
                            to_store: toStore,
                            quantity: document.getElementById('qty' + i).value,
                            date: document.getElementById('date').value,
                            reason: document.getElementById('reason').value,
                            adj: rowAdj,
                            unit: document.getElementById('unit' + i).value,
                            conversionid: document.getElementById('conversionid' + i).value,
                            type: incidentType,
                            stocktype: document.getElementById('stocktype').value,
                            type2: "A",
                            batch: document.getElementById('batch' + i).value,
                            mdate: batch.mdate,
                            edate: batch.edate,
                            mrp: batch.mrp,
                            aqty: aqtyStr,
                            unitcost: rowUnitCost,
                            linevalue: rowLineValue,
                        });
                    }

                }
            }

        }
        console.log(arrItem2)

        let check2 = valcheck();


        if (!check2) {
            alert("You can't use the same (product, batch, store) in multiple rows");
            return
        }

        (function () {
            var $b = $('#save_add');
            if (typeof $b.data('origHtml') === 'undefined') $b.data('origHtml', $b.html());
            var isEdit = (id > 0);
            $b.prop('disabled', true).addClass('btn-save-anim-loading')
              .html('<i class="fa fa-spinner fa-spin"></i> ' + (isEdit ? 'Updating...' : 'Saving...'));
        })();

        var isTransfer = (incidentType === "storetransfer");

        if (id > 0) {
            var updateUrl = isTransfer
                ? 'stock/stock/update_storetransfer'
                : 'stock/stock/update_adjstock';
            $.ajax({
                url: $('#baseUrl2').val() + updateUrl,
                type: 'POST',
                data: {
                    items: arrItem2,
                    id: id,
                    oldType: oldType
                },
                success: function(response) {
                    try { var datas = JSON.parse(response); } catch(e) { var datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Update failed. Please try again."));
                        return;
                    }
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    restoreSaveBtn();
                    alert("Inventory Transaction Updated Successfully");
                    if (datas.details) {
                        printRawHtml(datas.details, datas.voucher);
                    } else {
                        window.location.href = $('#baseUrl2').val() + 'manage_inventory_transection';
                    }
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Update failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });

        } else {
            var saveUrl = isTransfer
                ? 'stock/stock/save_storetransfer'
                : 'stock/stock/save_adjstock';
            var $invReqId = document.getElementById('inventory_request_id');
            $.ajax({
                url: $('#baseUrl2').val() + saveUrl,
                type: 'POST',
                data: {
                    items: arrItem2,
                    inventory_request_id: $invReqId ? $invReqId.value : 0
                },
                success: function(response) {
                    try { var datas = JSON.parse(response); } catch(e) { var datas = {}; }
                    if (datas.success === false) {
                        restoreSaveBtn();
                        alert("Error: " + (datas.error || "Save failed. Please try again."));
                        return;
                    }
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    restoreSaveBtn();
                    alert("Inventory Transaction Added Successfully");
                    if (datas.details) {
                        printRawHtml(datas.details, datas.voucher);
                    } else {
                        window.location.href = $('#baseUrl2').val() + 'manage_inventory_transection';
                    }
                },
                error: function(error) {
                    restoreSaveBtn();
                    alert("Error: Save failed. Please check your inputs and try again.");
                    console.log(error);
                }
            });

        }







    }

    function deleteRow(num) {
        document.getElementById('myRow' + num).style.display = 'none';
        updateTotalValue();
    }

    function valcheck() {
        arrItem = [];

        if (count > 2) {
            for (let i = 1; i < count; i++) {
                if (document.getElementById('myRow' + i).style.display != "none") {
                    let check = arrItem.find(item => item.product == document.getElementById('product' + i).value &&
                        item.store == document.getElementById('store' + i).value && item.batch == document.getElementById('batch' + i).value);

                    if (check != undefined) {
                        if (check.product != '') {
                            return false
                        } else {
                            arrItem.push({
                                product: document.getElementById('product' + i).value,
                                store: document.getElementById('store' + i).value,
                                batch: document.getElementById('batch' + i).value


                            });
                        }

                    } else {
                        arrItem.push({
                            product: document.getElementById('product' + i).value,
                            store: document.getElementById('store' + i).value,
                            batch: document.getElementById('batch' + i).value

                        });
                    }
                }

            }

        }
        return true;

    }

    function clearTable() {
        //document.getElementById('receivedfrom').value = ""

        for (let i = 2; i <= 20; i++) {
            document.getElementById('myRow' + i).style.display = 'none';
        }
        document.getElementById('productInput1').value = '';
        document.getElementById('product1').value = '';
        document.getElementById('productResults1').innerHTML = '';
        getActiveStore(0, 1);
        var $batchDropdown = $('#batch1');
        $batchDropdown.empty();
        var $subunitDropdown = $('#unit1');
        $subunitDropdown.empty();
        //getAdjDropdown("", 1)
        adj_type(1)
        document.getElementById('qty' + 1).value = "";
        document.getElementById('unit' + 1).value = "";
        document.getElementById('code' + 1).value = "";
        document.getElementById('codetype' + 1).innerHTML = ""
        if (document.getElementById('unitcost1')) document.getElementById('unitcost1').value = "";
        if (document.getElementById('value1')) document.getElementById('value1').value = "";
        updateTotalValue();

    }

    // Unit Cost Logic Specification v1.1 — shows/hides the Unit Cost + Value columns and the
    // Total Value footer, and decides whether Unit Cost is user-editable (Opening Stock, UCL-40)
    // or a read-only current-cost preview (Stock Disposal / Stock Adjustment Actual, UCL-42,
    // UCL-43). Store Transfer and Stock Adjustment (Physical) have no cost effect (UCL-44).
    function updateCostColumnState() {
        var incidentType = document.getElementById('type').value;
        var stocktypeVal = document.getElementById('stocktype') ? document.getElementById('stocktype').value : '';
        var table = document.getElementById('normalinvoice');
        var totalValueRow = document.getElementById('total_value_row');

        var showCost = incidentType !== '' && incidentType !== 'storetransfer' &&
            !(incidentType === 'stockadjustment' && stocktypeVal === 'physicalstock');
        var editable = (incidentType === 'openingstock');

        table.classList.toggle('hide-cost-col', !showCost);
        totalValueRow.style.display = showCost ? '' : 'none';

        for (var i = 1; i < count; i++) {
            var $uc = document.getElementById('unitcost' + i);
            if (!$uc) continue;
            $uc.readOnly = !editable;
            if (!editable) {
                $uc.placeholder = '0.00';
            }
        }
    }

    // Unit Cost Logic Specification v1.1 — recomputes a row's Value (Qty x Unit Cost) and the
    // Total Value footer. Called on qty change, on unitcost input, and whenever a row's cost is
    // set programmatically (default fill or fetched preview).
    function costRowChanged(item) {
        var qtyEl = document.getElementById('qty' + item);
        var ucEl  = document.getElementById('unitcost' + item);
        var valEl = document.getElementById('value' + item);
        if (!qtyEl || !ucEl || !valEl) return;

        var qty = parseFloat(qtyEl.value) || 0;
        var uc  = parseFloat(ucEl.value) || 0;
        valEl.value = (qty * uc).toFixed(2);
        updateTotalValue();
    }

    function updateTotalValue() {
        var total = 0;
        for (var i = 1; i < count; i++) {
            var row = document.getElementById('myRow' + i);
            if (row && row.style.display !== 'none') {
                var valEl = document.getElementById('value' + i);
                if (valEl) total += parseFloat(valEl.value) || 0;
            }
        }
        var totalEl = document.getElementById('total_value');
        if (totalEl) totalEl.value = total.toFixed(2);
    }

    // Stock Disposal / Stock Adjustment (Actual) preview: the cost the row will be valued at on
    // save (UCL-42, UCL-43) — read-only, never typed. Opening Stock rows are skipped here; their
    // Unit Cost is user-entered (set once in the 'product' branch above as a default only).
    function refreshRowCurrentCost(item) {
        var incidentType = document.getElementById('type').value;
        var stocktypeVal = document.getElementById('stocktype') ? document.getElementById('stocktype').value : '';
        if (incidentType === '' || incidentType === 'storetransfer' || incidentType === 'openingstock') return;
        if (incidentType === 'stockadjustment' && stocktypeVal === 'physicalstock') return;

        var prod  = document.getElementById('product' + item).value;
        var batch = document.getElementById('batch' + item).value;
        if (!prod || !batch) return;

        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/getCurrentUnitCost',
            type: 'POST',
            data: { product: prod, batch: batch },
            dataType: 'json',
            success: function (res) {
                var $uc = document.getElementById('unitcost' + item);
                if ($uc) {
                    $uc.value = parseFloat((res && res.cost) || 0).toFixed(2);
                    costRowChanged(item);
                }
            }
        });
    }

    function quantity_calculate(item, name) {


        if (name === "stocktype") {
            clearTable();
            updateCostColumnState();
        }

        if (document.getElementById('stocktype').value == "") {
            alert("Stock Type shouldn't be empty");
            document.getElementById('productInput' + item).value = '';
            document.getElementById('product' + item).value = '';
            document.getElementById('productResults' + item).innerHTML = '';
            getActiveStore(0, item);
            // getAdjDropdown("", item)
            adj_type(item)
            document.getElementById('qty' + item).value = "";
            document.getElementById('unit' + item).value = "";
            document.getElementById('code' + item).value = "";
            return
        }
        if (name === "product") {
            var $storeDropdown = $('#store' + item);
            $storeDropdown.empty();
            //getAdjDropdown(0, item)
            adj_type(item)
            document.getElementById('code' + item).value = "";
            document.getElementById('qty' + item).value = "";
            getStoresDropdown(stores, item);
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/getproduct',
                type: 'POST',
                data: {
                    prodid: document.getElementById('product' + item).value.toString(),
                },
                success: function(response) {
                    let product = JSON.parse(response);
                    // UCL-40: Opening Stock defaults Unit Cost to the product's Fixed Purchase
                    // Price unless the user already typed one. Disposal/Adjustment rows fetch
                    // the current cost once a batch is picked (refreshRowCurrentCost, 'batch').
                    if (document.getElementById('type').value === 'openingstock') {
                        var $openingUc = document.getElementById('unitcost' + item);
                        if ($openingUc && !$openingUc.value) {
                            $openingUc.value = parseFloat(product[0].cost_price || 0).toFixed(2);
                            costRowChanged(item);
                        }
                    }
                    $.ajax({
                        url: $('#baseUrl2').val() + 'stock/stock/getproductSubUnitPrimary',
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

                            var effectiveStore = (document.getElementById('type').value === 'storetransfer')
                                ? document.getElementById('from_store').value
                                : product[0].store;
                            avStock(item, document.getElementById('product' + item).value, effectiveStore, 1, "", "")
                            refreshRowCurrentCost(item);

                            //   document.getElementById('unit' + item).value = product[0].unit;
                        },
                        error: function(error) {
                            console.log(error)
                        }
                    });
                    getBatchDropdown(null, item, 1, document.getElementById('product' + item).value.toString(), product[0].batchtype);
                    getActiveStore(product[0].store, item);
                    getActiveSubUnit(document.getElementById('product' + item).value, item)
                    //   document.getElementById('unit' + item).value = product[0].unit;
                },
                error: function(error) {
                    console.log(error)
                }
            });
        }

        if (name === "batch") {
            var batchEffectiveStore = (document.getElementById('type').value === 'storetransfer')
                ? document.getElementById('from_store').value
                : document.getElementById('store' + item).value;
            avStock(item, document.getElementById('product' + item).value, batchEffectiveStore, document.getElementById('batch' + item).value, "", "")
            refreshRowCurrentCost(item);
            getActiveSubUnit(document.getElementById('product' + item).value, item)
        }


        if (name === "store") {
            avStock(item, document.getElementById('product' + item).value, document.getElementById('store' + item).value, document.getElementById('batch' + item).value, "", "")
            getActiveSubUnit(document.getElementById('product' + item).value, item)

        }

        if (name === "unit") {
            let select = document.getElementById('unit' + item);
            let selectedText = (select && select.selectedIndex >= 0 && select.options[select.selectedIndex])
                ? select.options[select.selectedIndex].text : '';
            convertion(item, document.getElementById('product' + item).value, document.getElementById('unit' + item).value, selectedText)
            // avStock(item,document.getElementById('product' + item).value,document.getElementById('store' + item).value,0)
            // getActiveSubUnit(document.getElementById('product' + item).value,item)

        }



        if (name === "qty") {
            if (document.getElementById("qty" + item).value == "-") {
                document.getElementById("qty" + item).value = "";
            }

            if (id == null) {
                let qty = 0;
                if (document.getElementById("adj" + item).value === "decrease") {
                    qty = parseInt(document.getElementById("code" + item).value) - parseInt(document.getElementById("qty" + item).value)

                }


                if (qty < 0) {
                    document.getElementById("qty" + item).value = "";
                    alert("While decreasing, qty shouldn't be less than available qty");
                }
            }

        }



    }

    function avStock(item, product, store, batch, convertiontype, conversion_ratio, bd, ad, addigit) {
        document.getElementById('code' + item).value = "";
        document.getElementById('qty' + item).value = "";
        //  getAdjDropdown(0, item);
        adj_type(item);

        if (document.getElementById('stocktype').value == "actualstock") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_avstock',
                type: 'POST',
                data: {
                    prodid: product,
                    storeid: store,
                    batch: batch

                    // type2: "A"

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
                        // el.innerHTML = (Math.floor(stock[0].avgqty * conversion_ratio)).toLocaleString() + " " + selectedText
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
        } else if (document.getElementById('stocktype').value == "physicalstock") {
            $.ajax({
                url: $('#baseUrl2').val() + 'stock/stock/avg_phystock',
                type: 'POST',
                data: {
                    prodid: product,
                    storeid: store,
                    batch: batch
                    // type2: "A"

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
                        // el.innerHTML = (Math.floor(stock[0].avgqty * conversion_ratio)).toLocaleString() + " " + selectedText
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

    }

    function getActiveProduct(productId, item) {
        if (productId > 0) {
            document.getElementById('product' + item).value = productId;
            var found = Array.isArray(products) ? products.find(function(p) { return p.id == productId; }) : undefined;
            if (found) {
                document.getElementById('productInput' + item).value = found.product_name;
            } else {
                $.ajax({
                    url: $('#baseUrl2').val() + 'stock/stock/getproduct',
                    type: 'POST',
                    data: { prodid: productId.toString() },
                    success: function(response) {
                        var prod = JSON.parse(response);
                        if (prod && prod.length > 0) {
                            document.getElementById('productInput' + item).value = prod[0].product_name;
                        }
                    }
                });
            }
        } else {
            document.getElementById('productInput' + item).value = '';
            document.getElementById('product' + item).value = '';
            if (document.getElementById('productResults' + item)) {
                document.getElementById('productResults' + item).innerHTML = '';
            }
        }
    }

    var inv_results = [];
    var inv_currentIndex = -1;

    function handleProductKeyPress(event, count) {
        const productElement = document.getElementById('productInput' + count);
        const query = productElement.value;

        if (event.key === 'ArrowDown') {
            if (inv_currentIndex < inv_results.length - 1) {
                inv_currentIndex++;
                highlightItemproduct(inv_currentIndex);
            }
        } else if (event.key === 'ArrowUp') {
            if (inv_currentIndex > 0) {
                inv_currentIndex--;
                highlightItemproduct(inv_currentIndex);
            }
        } else if (event.key === 'Enter') {
            if (inv_results.length > 0) {
                var idx = inv_currentIndex >= 0 ? inv_currentIndex : 0;
                document.getElementById('productInput' + count).value = inv_results[idx].product_name;
                document.getElementById('product' + count).value = inv_results[idx].id;
                document.getElementById('productResults' + count).innerHTML = '';
                quantity_calculate(count, 'product');
            }
        } else if (event.key === 'Backspace') {
            document.getElementById('product' + count).value = '';
            document.getElementById('productResults' + count).innerHTML = '';
            $('#batch' + count).empty();
            $('#unit' + count).empty();
            document.getElementById('qty' + count).value = '';
            document.getElementById('code' + count).value = '';
            document.getElementById('codetype' + count).innerHTML = '';
        } else {
            $.ajax({
                url: $('#baseUrl2').val() + 'invoice/invoice/getProductByNameStock',
                type: 'POST',
                data: { product_name: query },
                success: function(response) {
                    var prods = JSON.parse(response);
                    inv_results = prods.filter(function(p) {
                        return p.product_name.toLowerCase().includes(query.toLowerCase());
                    });
                    inv_currentIndex = -1;
                    displayResultsProduct(inv_results, count);
                },
                error: function(error) { console.log(error); }
            });
        }
    }

    function displayResultsProduct(items, count) {
        var searchResultsDiv = document.getElementById('productResults' + count);
        searchResultsDiv.innerHTML = '';
        if (items.length === 0) {
            searchResultsDiv.innerHTML = '<div style="padding:8px;">No results found</div>';
        } else {
            items.forEach(function(item, index) {
                var resultItem = document.createElement('div');
                resultItem.classList.add('resultItem');
                resultItem.textContent = item.product_name;
                resultItem.style.padding = '8px';
                resultItem.style.cursor = 'pointer';
                resultItem.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#8b5cf6';
                    this.style.color = '#fff';
                });
                resultItem.addEventListener('mouseout', function() {
                    this.style.backgroundColor = '';
                    this.style.color = '';
                });
                resultItem.addEventListener('click', function() {
                    document.getElementById('productInput' + count).value = item.product_name;
                    document.getElementById('product' + count).value = item.id;
                    searchResultsDiv.innerHTML = '';
                    quantity_calculate(count, 'product');
                });
                searchResultsDiv.appendChild(resultItem);
            });
        }
    }

    function highlightItemproduct(index) {
        var items = document.querySelectorAll('.resultItem');
        items.forEach(function(el, i) {
            el.style.backgroundColor = i === index ? '#8b5cf6' : '';
            el.style.color = i === index ? '#fff' : '';
        });
    }




    function getActiveStore(storeId, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option
        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });

        if (storeId > 0) {
            {
                $storeDropdown.val(storeId)
            }
        }
    }


    function getAdjDropdown(adjId, item) {
        var $adjDropdown = $('#adj' + item);
        $adjDropdown.empty();
        $adjDropdown.append('<option value="" disabled selected>Select </option>'); // Add default option

        $adjDropdown.append('<option value="increase">Increase</option>');
        $adjDropdown.append('<option value="decrease">Decrease</option>');



        if (adjId != "") {

            $adjDropdown.val(adjId)

        }

    }

    function getStoresDropdown(stores, item) {
        var $storeDropdown = $('#store' + item);
        $storeDropdown.empty();
        $storeDropdown.append('<option value="" disabled selected>Select store</option>'); // Add default option

        $.each(stores, function(index, store) {
            $storeDropdown.append('<option value="0">Default</option>');
            $storeDropdown.append('<option value="' + store.id + '">' + store.name + '</option>');
        });
    }

    function getBatchDropdown(batches, item, value, product, batchtype) {


        $.ajax({
            url: $('#base_url').val() + 'stock/stock/getBatchbyProductAndBatchtype2',
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
                // If a new batch was just created from this row's button, auto-select it
                if (pendingBatchSelect[item] !== undefined) {
                    $batchDropdown.val(pendingBatchSelect[item]);
                    delete pendingBatchSelect[item];
                    quantity_calculate(item, 'batch');
                } else if (value === 0 || value === '0') {
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
            url: $('#baseUrl2').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: productId
            },
            success: function(response) {
                datas = JSON.parse(response);
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

    function convertion(item, product, unit, unitname) {
        $.ajax({
            url: $('#baseUrl2').val() + 'stock/stock/conversion',
            type: 'POST',
            data: {
                product_id: product,
                unit: unit
            },
            success: function(response) {
                var effectiveStore = (document.getElementById('type').value === 'storetransfer')
                    ? document.getElementById('from_store').value
                    : document.getElementById('store' + item).value;

                if (response != "not") {
                    datas = JSON.parse(response);
                    document.getElementById('conversiontype' + item).value = datas[0].convertiontype
                    document.getElementById('conversionid' + item).value = datas[0].conversionratio_id
                    document.getElementById('conversion_ratio' + item).value = datas[0].conversion_ratio;

                    avStock(item, document.getElementById('product' + item).value, effectiveStore, document.getElementById('batch' + item).value,
                        datas[0].convertiontype, datas[0].conversion_ratio)
                } else {
                    getActiveSubUnit(document.getElementById('product' + item).value, item)
                    avStock(item, document.getElementById('product' + item).value, effectiveStore, document.getElementById('batch' + item).value, "", "")

                }

            },
            error: function(error) {
                console.log(error)
            }
        });


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

    async function saveModalBatch() {
        let batchid = document.getElementById('mb_batchid').value.trim();
        let busage = document.getElementById('mb_busage').value;
        let status = document.getElementById('mb_status').value;
        let product = document.getElementById('mb_product').value;

        if (!batchid) {
            alert('Batch ID is required');
            return;
        }
        if (!busage) {
            alert('Batch Usage Type is required');
            return;
        }
        if (status === '') {
            alert('Status is required');
            return;
        }
        if (busage === 'single' && !product) {
            alert('Product is required for Single Usage');
            return;
        }

        // Check duplicate batch ID
        try {
            let checkRes = await $.ajax({
                type: 'POST',
                url: $('#baseUrl2').val() + 'stock/stock/getStockBatchById',
                data: {
                    batchid: batchid
                }
            });
            if (JSON.parse(checkRes) !== 'success') {
                alert('Batch ID already exists');
                return;
            }
        } catch (e) {
            alert('Error checking Batch ID');
            return;
        }

        let edate_toggle = document.getElementById('mb_edate_toggle').value;
        let postData = {
            batchid: batchid,
            details: document.getElementById('mb_details').value,
            busage: busage,
            status: status,
            edate_toggle: edate_toggle,
            product: busage === 'single' ? product : '0',
            mdate: busage === 'single' ? document.getElementById('mb_mdate').value : '',
            pdate: busage === 'single' ? document.getElementById('mb_pdate').value : '',
            edate: (busage === 'single' && edate_toggle === 'yes') ? document.getElementById('mb_edate').value : '',
            mrp: busage === 'single' ? (document.getElementById('mb_mrp').value || '0') : '0'
        };

        $('#mb_save_btn').prop('disabled', true).text('Saving...');

        $.ajax({
            type: 'POST',
            url: $('#baseUrl2').val() + 'stock/stock/save_stockbatch_ajax',
            data: postData,
            success: function(res) {
                let result = JSON.parse(res);
                $('#mb_save_btn').prop('disabled', false).text('Save Batch');
                if (result.success) {
                    // Add new batch option to matching batch dropdowns
                    $('select[id^="batch"]').each(function() {
                        let rowNum = this.id.replace('batch', '');
                        let rowProd = $('#product' + rowNum).val();
                        if (result.busage === 'multiple' || !rowProd || rowProd == result.product) {
                            $(this).append('<option value="' + result.id + '">' + result.batchid + '</option>');
                        }
                    });

                    let productName = 'N/A (Multiple Usage)';
                    if (result.busage === 'single' && result.product > 0) {
                        let found = Array.isArray(products) ? products.find(function(p) {
                            return p.id == result.product;
                        }) : undefined;
                        productName = found ? found.product_name : 'Product #' + result.product;
                    }

                    alert('Batch saved! ID: ' + result.batchid + ' | Product: ' + productName);

                    // If opened from a row button (single usage), directly set product + batch
                    if (result.busage === 'single' && result.product > 0) {
                        let rowNum = count;
                        document.getElementById('myRow' + count).style.display = 'table-row';
                        getActiveStore(0, count);
                        count = count + 1;
                        // Populate product dropdown and select the saved product
                        getActiveProduct(result.product, rowNum);
                        adj_type(rowNum);
                        // Fetch product record to get batchtype + default store
                        $.ajax({
                            url: $('#baseUrl2').val() + 'stock/stock/getproduct',
                            type: 'POST',
                            data: {
                                prodid: result.product
                            },
                            success: function(resp) {
                                let prod = JSON.parse(resp);
                                if (prod && prod.length > 0) {
                                    getActiveStore(prod[0].store, rowNum);
                                    getActiveSubUnit(result.product, rowNum);
                                    // Pass result.id as value so getBatchDropdown auto-selects it
                                    getBatchDropdown(null, rowNum, result.id, result.product, prod[0].batchtype);
                                }
                            }
                        });
                    } else {
                        batchModalRowNum = 0;
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

    // Wrapped in ready() — modal HTML is rendered after this script block,
    // so the #addBatchModal element doesn't exist yet when this script runs inline.
    $(document).ready(function() {
        $('#addBatchModal').on('shown.bs.modal', function() {
            $('#addBatchModal .datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        inst.dpDiv.css('z-index', 99999);
                    }, 0);
                }
            });
            // Populate product dropdown (products array already loaded from controller)
            let $sel = $('#mb_product');
            if ($sel.find('option').length <= 1) {
                $.each(products, function(i, p) {
                    $sel.append('<option value="' + p.id + '">' + p.product_name + '</option>');
                });
            }
            // Apply Select2 to modal selects
            $('#mb_busage, #mb_edate_toggle, #mb_status, #mb_product').select2({
                placeholder: 'Select option', allowClear: true, dropdownParent: $('#addBatchModal')
            });
            $('#mb_busage').off('select2:select select2:unselect').on('select2:select select2:unselect', function() { modalChangeBatchtype(); });
            $('#mb_edate_toggle').off('select2:select select2:unselect').on('select2:select select2:unselect', function() { modalToggleEdate(); });
        });
    });

    function getActiveSubUnitEdit(productId, item, value, conversion_id, conversion_ratio, cconvertiontype, avstock, stocktype) {
        $.ajax({
            url: $('#baseUrl2').val() + 'product/product/active_subunitsbyproductId',
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

                if (stocktype != "both") {
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
                            url: $('#baseUrl2').val() + 'stock/stock/getproductSubUnitPrimary',
                            type: 'POST',
                            data: {
                                prodid: productId,
                            },
                            success: function(response2) {
                                if (response2 != "null") {

                                    let product2 = JSON.parse(response2);
                                    document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                    document.getElementById('bd' + item).value = product2[0].unit2
                                    document.getElementById('ad' + item).value = product2[0].unit_name
                                    let el = document.getElementById('codetype' + item);
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
                                    if (stocktype != "both") {
                                        document.getElementById('code' + item).value = avstock == null ? 0 : totalcount;
                                        el.innerHTML = (totalcount + document.getElementById('bd' + item).value + " " + subcount + document.getElementById('ad' + item).value).toLocaleString();
                                    }
                                } else {
                                    document.getElementById('mconversion_ratio' + item).value = ""
                                    document.getElementById('bd' + item).value = ""
                                    document.getElementById('ad' + item).value = ""
                                }
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        });
                    } else {
                        $.ajax({
                            url: $('#baseUrl2').val() + 'stock/stock/getproductSubUnitPrimary',
                            type: 'POST',
                            data: {
                                prodid: productId,
                            },
                            success: function(response2) {
                                if (response2 != "null") {

                                    let product2 = JSON.parse(response2);
                                    document.getElementById('mconversion_ratio' + item).value = product2[0].conversion_ratio
                                    document.getElementById('bd' + item).value = product2[0].unit2
                                    document.getElementById('ad' + item).value = product2[0].unit_name
                                } else {
                                    document.getElementById('mconversion_ratio' + item).value = ""
                                    document.getElementById('bd' + item).value = ""
                                    document.getElementById('ad' + item).value = ""
                                }
                            },
                            error: function(error) {
                                console.log(error)
                            }
                        });
                    }


                }



            },
            error: function(error) {
                console.log(error)
            }
        });
    }
</script>

<!-- Add Batch Modal -->
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
                        <select class="form-control" id="mb_product">
                            <option value=""></option>
                        </select>
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
    $('#ap_barcode').val('').prop('readonly', false);
    $('#ap_product_name').val('');
    $('#ap_product_type').val('N/A');
    $('#ap_batchtype').val('3');
    $('#ap_defaultsaleprice').val('custom');
    $('#ap_stock').val('0');
    $('#ap_cost_price').val('');
    $('#ap_success_msg').hide().html('');
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
        url: $('#baseUrl2').val() + 'get_product_form_data',
        type: 'GET', dataType: 'json',
        success: function(d) {
            var $cat = $('#ap_category_id').empty().append('<option value="">Select Category</option>');
            $.each(d.categories || [], function(i, c) { $cat.append('<option value="' + c.category_id + '">' + c.category_name + '</option>'); });
            var $unit = $('#ap_unit').empty().append('<option value="">Select Unit</option>');
            $.each(d.units || [], function(i, u) { $unit.append('<option value="' + u.unit_id + '">' + u.unit_name + '</option>'); });
            $('#ap_loading').hide();
            $('#ap_form_body').show();
            $('#ap_save_btn').prop('disabled', false);
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
            $('#ap_loading').hide();
            $('#ap_form_body').show();
            $('#ap_save_btn').prop('disabled', false);
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
        url: $('#baseUrl2').val() + 'save_product_ajax',
        type: 'POST',
        data: {
            product_id:       $('#ap_barcode').val().trim(),
            product_name:     pname,
            category_id:      $('#ap_category_id').val(),
            subcategory_id:   0,
            brand_id:         0,
            product_type:     $('#ap_product_type').val(),
            batchtype:        $('#ap_batchtype').val(),
            defaultsaleprice: $('#ap_defaultsaleprice').val(),
            unit:             $('#ap_unit').val(),
            store:            $('#ap_store').val() || 1,
            supplier_id:      $('#ap_supplier_id').val() || 0,
            stock:            $('#ap_stock').val(),
            status:           '1',
            ad:               '',
            bd:               '',
            printname:        '',
            oop_id:           '',
            vat:              '0',
            sell_price:       '0',
            cost_price:       $('#ap_cost_price').val()
        },
        dataType: 'json',
        success: function(r) {
            $('#ap_save_btn').prop('disabled', false).text('Save Product');
            if (r.status === 'Success') {
                var pname2 = $('#ap_product_name').val().trim();

                // Add new product to the products array so it's searchable
                products.push({ id: r.id, product_name: pname2 });

                // Find a row with no product selected or use a new row
                var rowNum = null;
                for (var i = 1; i < count; i++) {
                    var row = document.getElementById('myRow' + i);
                    if (row && row.style.display !== 'none' && document.getElementById('product' + i).value === '') {
                        rowNum = i;
                        break;
                    }
                }
                if (rowNum === null) {
                    rowNum = count;
                    document.getElementById('myRow' + rowNum).style.display = 'table-row';
                    getActiveStore(0, rowNum);
                    count = count + 1;
                }
                document.getElementById('productInput' + rowNum).value = pname2;
                document.getElementById('product' + rowNum).value = r.id;
                quantity_calculate(rowNum, 'product');
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
