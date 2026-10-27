<div class="row">
    <div class="col-sm-12">
        <div class="panel">
            <div >
                <div class="panel-body print-font-size">
                    <div class="row">

                        <div class="col-xs-4">

                            <img style="width: 210px; height:79px;" src="<?php
                                                                            if (isset($currency_details[0]['invoice_logo'])) {
                                                                                echo logo_url($currency_details[0]['invoice_logo'], 'assets/img/icons/mini-logo.png');
                                                                            }
                                                                            ?>" class="img-bottom-m print-logo invoice-img-position" alt="">
                            <br>
                            <span
                                class="label label-success-outline m-r-15 p-10">Order To</span>
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
                                    <?php $text = base64_encode(display('invoice_no') . ': ' . $invoice_no . ' ' . display('customer_name') . ': ' . $customer_name);
                                    ?>
                                    <img src="http://chart.apis.google.com/chart?cht=qr&chs=250x250&chld=L|4&chl=<?php echo $text ?>"
                                        alt="">
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col-xs-4 text-left ">
                            <h2 class="m-t-0">Sales Order</h2>
                            <div>
                                <abbr class="font-bold">
                                   Order No: <span dir="ltr"></span>
                                </abbr>
                                <?php echo $invoiceno; ?>
                            </div>
                            <div class="m-b-15">
                                <abbr class="font-bold">Billing Date</abbr>
                                <?php echo date("d-M-Y", strtotime($date)); ?>
                                <br>
                            </div>

                            <span class="label label-success-outline m-r-15">Order From</span>

                            <address style="margin-top: 10px;" class="">
                                <strong class=""><?php echo $customer_name ?> </strong><br>
                                <?php if ($customer_address) { ?>
                                    <?php echo $customer_address; ?>
                                    <br>
                                <?php } ?>
                                <?php if ($customer_mobile) { ?>
                                    <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                    <?php echo $customer_mobile; ?>
                                    <br>
                                <?php }  ?>
                                <?php if ($customer_email) { ?>
                                    <abbr class="font-bold"><?php echo display('email') ?>: </abbr>
                                    <?php echo $customer_email; ?>
                                    <br>
                                <?php } ?>
                                <?php if (!empty($email_address)) { ?>
                                    <abbr class="font-bold"><?php echo display('vat_no') ?>: </abbr>
                                    <?php echo $email_address ?>
                                    <br>
                                <?php } ?>
                                <?php if (!empty($contact)) { ?>
                                    <abbr class="font-bold"><?php echo display('cr_no') ?>: </abbr>
                                    <?php echo $contact ?>
                                <?php } ?>


                            </address>
                        </div>
                    </div>

                    <br />

                    <table class="invoice-details" width="100%;">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Product Name</th>
                            <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Qty</th>
                            <th style=" border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Rate</th>
        
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px;font-size: 14px;text-align: right;">Total</th>
                        </tr>
                        <?php
                        $sl = 1;
                        $total_qty = 0;

                        foreach ($invoice_all_data as $invoice_data) { ?>

                            <tr>
                                <td style="text-align: center;"><?php echo $sl; ?></td>
                                <td style="width: 200px;font-size: 13px;padding: 5px;"><?php echo $invoice_data['product_name']; ?></td>
                                <td style="font-size: 14px;padding: 5px;text-align: right;text-align: right;"><?php echo $invoice_data['quantity']." ". $invoice_data['unit_name']; ?></td>
                                <td style="width: 200px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"><?php echo number_format($invoice_data['product_rate'], 2, '.', ','); ?></td>
                                <td style="width: 200px; text-align: right;font-size: 14px;padding: 5px;text-align: right;"> <?php echo number_format($invoice_data['total_price'], 2, '.', ','); ?></td>
                            </tr>
                        <?php
                            // $dis = is_numeric($invoice_data['discount_per']) ? $invoice_data['discount_per'] : 0;
                            // $total_discount = $total_discount + (float)$dis;
                            $total_qty = $total_qty + (float)$invoice_data['quantity'];
                            $sl++;
                        } ?>

                        <tr>
                             <td colspan="2" style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "><b></b></td>

                            <td style="border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px;text-align: right; ">  </td>
                            <td colspan="1" style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "><b>Total:</b></td>

                            <td style="text-align: right;border-bottom: 2px solid; border-top: 2px solid;font-size: 14px;padding: 5px; "> <?php echo number_format($total, 2, '.', ','); ?> </td>



                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px; "><b>Sale Discount:</b></td>

                            <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_dis, 2, '.', ','); ?></td>

                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px; "><b>Total Discount Amount:</b></td>

                            <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_discount_ammount, 2, '.', ','); ?></td>

                        </tr>
            
                        <tr>
                            <td colspan="4" style="text-align: right;font-size: 14px;padding: 5px; "><b>Total VAT Amount:</b></td>

                            <td style="text-align: right;font-size: 14px;padding: 5px; "> <?php echo number_format($total_vat_amnt, 2, '.', ','); ?></td>

                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;font: size 20px;padding: 5px;"><b>Grand Total:</b></td>

                            <td style="text-align: right;font: size 20px;padding: 5px; "> <?php echo number_format($grandTotal, 2, '.', ','); ?></td>

                        </tr>
                    </table>

                    <?php if (!empty($details)): ?>
                    <div style="margin-top:16px; padding:10px; border-top:1px solid #e4e5e7;">
                        <strong style="font-size:13px;">Details:</strong>
                        <p style="margin:6px 0 0; font-size:12px; white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($details)); ?></p>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>