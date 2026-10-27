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
                            <address class="margin-top10">
                                <strong><?php echo $company_info[0]['company_name'] ?></strong><br>
                                <span class="comp-web"><?php echo $company_info[0]['address'] ?></span><br>
                                <abbr class="font-bold">Mobile: </abbr><?php echo $company_info[0]['mobile'] ?><br>
                                <abbr><b>Email:</b></abbr> <?php echo $company_info[0]['email'] ?><br>
                            </address>
                        </div>

                        <div class="col-xs-4"></div>

                        <div class="col-xs-4 text-left">
                            <h2 class="m-t-0">Customer Receive</h2>
                            <div>
                                <abbr class="font-bold">Voucher No: </abbr><?php echo $header['voucher_no']; ?>
                            </div>
                            <div class="m-b-15">
                                <abbr class="font-bold">Date: </abbr><?php echo date("d-M-Y", strtotime($header['date'])); ?>
                            </div>
                            <?php if (!empty($header['payment_type_name'])): ?>
                            <div class="m-b-15">
                                <abbr class="font-bold">Payment Type: </abbr><?php echo $header['payment_type_name']; ?>
                            </div>
                            <?php endif; ?>
                            <span class="label label-success-outline m-r-15">Received From</span>
                            <address style="margin-top: 10px;">
                                <strong><?php echo $header['customer_name'] ?></strong><br>
                                <?php if (!empty($header['customer_address'])): ?>
                                    <?php echo $header['customer_address']; ?><br>
                                <?php endif; ?>
                                <?php if (!empty($header['customer_mobile'])): ?>
                                    <abbr class="font-bold">Mobile: </abbr><?php echo $header['customer_mobile']; ?><br>
                                <?php endif; ?>
                                <?php if (!empty($header['branch_name'])): ?>
                                    <abbr class="font-bold">Branch: </abbr><?php echo $header['branch_name']; ?><br>
                                <?php endif; ?>
                            </address>
                        </div>
                    </div>

                    <br />

                    <table class="invoice-details" width="100%;">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">Invoice No</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Settled Amount</th>
                        </tr>
                        <?php $sl = 1; foreach ($items as $item): ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $sl++; ?></td>
                                <td style="font-size: 14px;padding: 5px;"><?php echo $item['voucher_no']; ?></td>
                                <td style="text-align: right;font-size: 14px;padding: 5px;"><?php echo number_format((float)$item['settle_amount'], 2, '.', ','); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="2" style="text-align: right;font-size: 14px;padding: 5px;border-top: 2px solid #000;"><b>Total:</b></td>
                            <td style="text-align: right;font-size: 14px;padding: 5px;border-top: 2px solid #000;"><?php echo number_format((float)$header['total_amount'], 2, '.', ','); ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
