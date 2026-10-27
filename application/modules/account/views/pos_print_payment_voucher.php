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
                                <span><?php echo $company_info[0]['address'] ?></span><br>
                                <abbr class="font-bold"><?php echo display('mobile') ?>: </abbr>
                                <?php echo $company_info[0]['mobile'] ?><br>
                                <abbr><b><?php echo display('email') ?>:</b></abbr>
                                <?php echo $company_info[0]['email'] ?><br>
                            </address>
                        </div>

                        <div class="col-xs-4"></div>

                        <div class="col-xs-4 text-left">
                            <h2 class="m-t-0">Payment Voucher</h2>
                            <div>
                                <abbr class="font-bold">Voucher No: </abbr><?php echo $voucher_no; ?>
                            </div>
                            <div>
                                <abbr class="font-bold">Date: </abbr><?php echo date("d-M-Y", strtotime($date)); ?>
                            </div>
                            <div>
                                <abbr class="font-bold">Payment Method: </abbr><?php echo $from_name; ?>
                            </div>
                            <?php if (!empty($remark)): ?>
                            <div>
                                <abbr class="font-bold">Remark: </abbr><?php echo $remark; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <br />

                    <table class="invoice-details" width="100%">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Payment</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 5px; font-size: 14px;">Comment</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px; text-align: right;">Amount</th>
                        </tr>
                        <?php $sl = 1; foreach ($items as $item): ?>
                        <tr>
                            <td style="padding: 5px; text-align: center;"><?php echo $sl; ?></td>
                            <td style="padding: 5px; font-size: 14px;"><?php echo $item['to_name']; ?></td>
                            <td style="padding: 5px; font-size: 14px;"><?php echo $item['comment']; ?></td>
                            <td style="padding: 5px; font-size: 14px; text-align: right;"><?php echo number_format($item['amount'], 2, '.', ','); ?></td>
                        </tr>
                        <?php $sl++; endforeach; ?>
                        <tr>
                            <td colspan="3" style="text-align: right; font-size: 14px; padding: 5px; border-top: 2px solid #000;"><b>Total:</b></td>
                            <td style="text-align: right; font-size: 14px; padding: 5px; border-top: 2px solid #000;"><?php echo number_format($total, 2, '.', ','); ?></td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
