<div id="dm-root">
<style>
/* ── Dot-Matrix POS Receipt ── */
@media print {
    @page { size: 210mm auto; margin: 4mm 6mm; }
    body, html { margin: 0; padding: 0; }
    .dm-no-print { display: none !important; }
    .dm-receipt { box-shadow: none !important; border: none !important; }
    .dm-receipt, .dm-receipt * {
        color: #000 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
.dm-receipt {
    width: 480px;
    max-width: 480px;
    margin: 0 auto;
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    font-weight: 700;
    color: #000;
    box-sizing: border-box;
    padding: 8px 10px;
    background: #fff;
    letter-spacing: 0.2px;
}
.dm-receipt * {
    box-sizing: border-box;
    color: #000;
    font-family: 'Courier New', Courier, monospace;
}

/* ── Header ── */
.dm-header { text-align: center; padding-bottom: 4px; }
.dm-company-name { font-size: 15px; font-weight: 900; margin: 0 0 2px; letter-spacing: 1px; }
.dm-company-sub  { font-size: 11px; font-weight: 700; margin: 1px 0; line-height: 1.4; }

/* ── Dividers ── */
.dm-divider        { border: none; border-top: 1px solid #000; margin: 5px 0; }
.dm-divider-dashed { border: none; border-top: 1px dashed #000; margin: 5px 0; }
.dm-divider-double { border: none; border-top: 3px double #000; margin: 5px 0; }

/* ── Info table ── */
.dm-info-table { width: 100%; border-collapse: collapse; margin-bottom: 3px; }
.dm-info-table td { padding: 1px 2px; vertical-align: top; font-size: 11px; line-height: 1.4; }
.dm-info-label  { font-weight: 900; white-space: nowrap; width: 100px; text-transform: uppercase; }
.dm-info-colon  { padding-right: 4px; white-space: nowrap; }
.dm-info-value  { word-break: break-word; font-weight: 700; }

/* ── Products table ── */
.dm-items-table { width: 100%; border-collapse: collapse; }
.dm-items-table th {
    font-size: 11px;
    font-weight: 900;
    padding: 3px 4px;
    border-top: 1px solid #000;
    border-bottom: 1px solid #000;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    background: #fff;
}
.dm-items-table td { font-size: 11px; font-weight: 700; padding: 3px 4px; vertical-align: top; }
.dm-items-table tbody tr:last-child td { border-bottom: 1px solid #000; }

.dm-th-sl    { text-align: center; width: 28px; }
.dm-th-name  { text-align: left; }
.dm-th-qty   { text-align: right; width: 60px; }
.dm-th-rate  { text-align: right; width: 72px; }
.dm-th-total { text-align: right; width: 72px; }

.dm-td-sl    { text-align: center; }
.dm-td-name  { text-align: left; }
.dm-td-qty   { text-align: right; }
.dm-td-rate  { text-align: right; }
.dm-td-total { text-align: right; }

/* ── Totals ── */
.dm-totals-table { width: 100%; border-collapse: collapse; margin-top: 2px; }
.dm-totals-table td { padding: 2px 4px; font-size: 11px; font-weight: 700; }
.dm-totals-label { text-align: right; padding-right: 8px; text-transform: uppercase; }
.dm-totals-value { text-align: right; white-space: nowrap; width: 80px; }
.dm-grand-total td { font-size: 13px !important; font-weight: 900 !important; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 3px 4px; }

/* ── Footer ── */
.dm-thankyou { text-align: center; font-weight: 900; margin: 6px 0 4px; font-size: 11px; letter-spacing: 0.5px; }
.dm-terms-title { font-weight: 900; font-size: 11px; margin: 6px 0 2px; text-transform: uppercase; }
.dm-terms-list  { margin: 0; padding-left: 14px; font-size: 11px; font-weight: 700; line-height: 1.5; }
.dm-poweredby   { text-align: center; font-size: 10px; font-weight: 700; margin: 4px 0 2px; }
</style>

<div class="dm-receipt">

    <!-- ── COMPANY HEADER ── -->
    <div class="dm-header">
        <p class="dm-company-name"><?php echo $company_info[0]['company_name']; ?></p>
        <p class="dm-company-sub"><?php echo $company_info[0]['address']; ?></p>
        <p class="dm-company-sub"><?php echo $company_info[0]['mobile']; ?></p>
        <?php if (!empty($company_info[0]['email'])): ?>
            <p class="dm-company-sub"><?php echo $company_info[0]['email']; ?></p>
        <?php endif; ?>
        <?php if (!empty($company_info[0]['website'])): ?>
            <p class="dm-company-sub"><?php echo $company_info[0]['website']; ?></p>
        <?php endif; ?>
    </div>

    <hr class="dm-divider">

    <!-- ── INVOICE INFO ── -->
    <table class="dm-info-table">
        <tr>
            <td class="dm-info-label">Invoice No</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $invoiceno; ?></td>
        </tr>
        <tr>
            <td class="dm-info-label">Date</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo date('d-M-Y', strtotime($date)); ?></td>
        </tr>
        <?php if (!empty($users_name)): ?>
        <tr>
            <td class="dm-info-label">User</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $users_name; ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <hr class="dm-divider-dashed">

    <!-- ── CUSTOMER INFO ── -->
    <table class="dm-info-table">
        <?php if (!empty($customer_name)): ?>
        <tr>
            <td class="dm-info-label">Customer</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $customer_name; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($customer_address)): ?>
        <tr>
            <td class="dm-info-label">Address</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $customer_address; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($customer_mobile)): ?>
        <tr>
            <td class="dm-info-label">Mobile</td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $customer_mobile; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($email_address)): ?>
        <tr>
            <td class="dm-info-label"><?php echo display('vat_no'); ?></td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $email_address; ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($contact)): ?>
        <tr>
            <td class="dm-info-label"><?php echo display('cr_no'); ?></td>
            <td class="dm-info-colon">:</td>
            <td class="dm-info-value"><?php echo $contact; ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- ── PRODUCTS TABLE ── -->
    <?php
    $sl = 1;
    $total_qty = 0;
    ?>
    <table class="dm-items-table">
        <thead>
            <tr>
                <th class="dm-th-sl">SL</th>
                <th class="dm-th-name">Product Name</th>
                <th class="dm-th-qty">Qty</th>
                <th class="dm-th-rate">Rate</th>
                <th class="dm-th-total">Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($invoice_all_data as $invoice_data): ?>
            <tr>
                <td class="dm-td-sl"><?php echo $sl; ?></td>
                <td class="dm-td-name"><?php echo $invoice_data['product_name']; ?></td>
                <td class="dm-td-qty"><?php echo $invoice_data['quantity'] . ' ' . $invoice_data['unit_name']; ?></td>
                <td class="dm-td-rate"><?php echo number_format((float)$invoice_data['product_rate'], 2, '.', ','); ?></td>
                <td class="dm-td-total"><?php echo number_format((float)$invoice_data['total_price'], 2, '.', ','); ?></td>
            </tr>
            <?php
            $total_qty += (float)$invoice_data['quantity'];
            $sl++;
            ?>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ── TOTALS ── -->
    <table class="dm-totals-table">
        <tr>
            <td class="dm-totals-label">Total:</td>
            <td class="dm-totals-value"><?php echo number_format((float)$total, 2, '.', ','); ?></td>
        </tr>
        <tr>
            <td class="dm-totals-label">Sale Discount:</td>
            <td class="dm-totals-value"><?php echo number_format((float)$total_dis, 2, '.', ','); ?></td>
        </tr>
        <tr>
            <td class="dm-totals-label">Total Discount:</td>
            <td class="dm-totals-value"><?php echo number_format((float)$total_discount_ammount, 2, '.', ','); ?></td>
        </tr>
        <?php if (!empty($total_vat_amnt) && (float)$total_vat_amnt != 0): ?>
        <tr>
            <td class="dm-totals-label">Total VAT:</td>
            <td class="dm-totals-value"><?php echo number_format((float)$total_vat_amnt, 2, '.', ','); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="dm-grand-total">
            <td class="dm-totals-label"><b>Grand Total:</b></td>
            <td class="dm-totals-value"><b><?php echo number_format((float)$grandTotal, 2, '.', ','); ?></b></td>
        </tr>
    </table>

    <hr class="dm-divider">

    <!-- ── THANK YOU ── -->
    <p class="dm-thankyou">** Thank You, Come Again! **</p>

    <!-- ── TERMS & CONDITIONS ── -->
    <?php
    $CI =& get_instance();
    $ws_dm = $CI->db->select('terms_conditions')->from('web_setting')->get()->row();
    $raw_terms = (!empty($ws_dm) && !empty($ws_dm->terms_conditions)) ? trim($ws_dm->terms_conditions) : '';
    $terms_lines = !empty($raw_terms)
        ? array_filter(explode("\n", $raw_terms), function($l){ return trim($l) !== ''; })
        : [];
    if (!empty($terms_lines)): ?>
        <p class="dm-terms-title">Terms &amp; Conditions</p>
        <ul class="dm-terms-list">
            <?php foreach ($terms_lines as $line): ?>
                <li><?php echo htmlspecialchars(trim($line)); ?></li>
            <?php endforeach; ?>
        </ul>
        <hr class="dm-divider-dashed">
    <?php endif; ?>

    <p class="dm-poweredby">Powered by: <b>Fexten Solutions (Pvt) Ltd.</b></p>

</div><!-- /.dm-receipt -->
</div><!-- /#dm-root -->

<div class="dm-no-print" style="text-align:center; margin-top:16px;">
    <button onclick="window.print()" style="padding:8px 22px; background:#333; color:#fff; border:none; border-radius:4px; font-family:Courier New,monospace; font-size:13px; cursor:pointer;">&#128424; Print</button>
</div>
