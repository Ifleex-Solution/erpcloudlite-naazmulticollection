<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div>
                <div class="panel-body print-font-size">
                    <div class="row">

                        <div class="col-xs-4">
                            <img style="width: 210px; height:79px;" src="<?php
                                if (isset($currency_details[0]['invoice_logo'])) {
                                    echo logo_url($currency_details[0]['invoice_logo'], 'assets/img/icons/mini-logo.png');
                                }
                            ?>" class="img-bottom-m print-logo invoice-img-position" alt="">
                            <br>
                            <span class="label label-success-outline m-r-15 p-10">Return To</span>
                            <address class="margin-top10">
                                <strong class=""><?php echo $company_info[0]['company_name'] ?></strong><br>
                                <span class="comp-web"><?php echo $company_info[0]['address'] ?></span><br>
                                <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                <?php echo $company_info[0]['mobile'] ?><br>
                                <abbr><b><?php echo display('email') ?>:</b></abbr>
                                <?php echo $company_info[0]['email'] ?><br>
                                <abbr><b><?php echo display('website') ?>:</b></abbr>
                                <span class="comp-web"><?php echo $company_info[0]['website'] ?></span><br>
                            </address>
                        </div>

                        <div class="col-xs-4">
                            <?php $web_setting = $this->db->select("*")->from("web_setting")->get()->row();
                            if ($web_setting->is_qr == 1) { ?>
                                <div class="print-qr">
                                    <?php $text = base64_encode('Return No: ' . $invoiceno . ' Customer: ' . $customer_name); ?>
                                    <img src="http://chart.apis.google.com/chart?cht=qr&chs=250x250&chld=L|4&chl=<?php echo $text ?>" alt="">
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col-xs-4 text-left">
                            <h2 class="m-t-0">Sales Return</h2>
                            <div>
                                <abbr class="font-bold">Return No: </abbr><?php echo $invoiceno; ?>
                            </div>
                            <?php if (!empty($sale_invoice_no)): ?>
                            <div>
                                <abbr class="font-bold">Sale ID: </abbr><?php echo $sale_invoice_no; ?>
                            </div>
                            <?php endif; ?>
                            <div class="m-b-15">
                                <abbr class="font-bold"><?php echo display('billing_date') ?></abbr>
                                <?php echo date("d-M-Y", strtotime($date)); ?>
                                <br>
                            </div>
                            <span class="label label-success-outline m-r-15">Return From</span>
                            <address style="margin-top: 10px;" class="">
                                <strong class=""><?php echo $customer_name ?> </strong><br>
                                <?php if ($customer_address) { ?>
                                    <?php echo $customer_address; ?><br>
                                <?php } ?>
                                <?php if ($customer_mobile) { ?>
                                    <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                    <?php echo $customer_mobile; ?><br>
                                <?php } ?>
                                <?php if ($customer_email) { ?>
                                    <abbr class="font-bold"><?php echo display('email') ?>: </abbr>
                                    <?php echo $customer_email; ?><br>
                                <?php } ?>
                            </address>
                        </div>
                    </div>

                    <br />

                    <table class="invoice-details" width="100%;">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Product Name</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Qty</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Refund%</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Return Amount</th>
                        </tr>
                        <?php
                        $sl = 1;
                        $total_qty = 0;
                        foreach ($invoice_all_data as $invoice_data) { ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $sl; ?></td>
                                <td style="width: 200px;font-size: 13px;padding: 5px;"><?php echo $invoice_data['product_name']; ?></td>
                                <td style="font-size: 14px;padding: 5px;text-align: right;"><?php echo number_format((float)$invoice_data['rqty'], 0, '.', '') . " " . $invoice_data['unit_name']; ?></td>
                                <td style="text-align: right;font-size: 14px;padding: 5px;"><?php echo !empty($invoice_data['rdeduction']) ? number_format($invoice_data['rdeduction'], 2, '.', ',') : '—'; ?></td>
                                <td style="width: 200px; text-align: right;font-size: 14px;padding: 5px;"> <?php echo number_format($invoice_data['total_price'], 2, '.', ','); ?></td>
                            </tr>
                        <?php
                            $total_qty = $total_qty + (float)$invoice_data['rqty'];
                            $sl++;
                        } ?>

                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px;border-top: 2px solid #000;"><b>Total:</b></td>
                            <td style="text-align: right;font-size: 14px;padding: 5px;border-top: 2px solid #000;"> <?php echo number_format($total, 2, '.', ','); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px;"><b>Total VAT:</b></td>
                            <td style="text-align: right;font-size: 14px;padding: 5px;"> <?php echo number_format($total_vat_amnt, 2, '.', ','); ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px;"><b>Grand Total:</b></td>
                            <td style="text-align: right;font-size: 14px;padding: 5px;"> <?php echo number_format($grandTotal, 2, '.', ','); ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
