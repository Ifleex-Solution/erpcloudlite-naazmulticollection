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
                            <h2 class="m-t-0">Inventory Transaction</h2>
                            <div>
                                <abbr class="font-bold">Transaction No: </abbr><?php echo !empty($header['inventory_id']) ? $header['inventory_id'] : '#' . (int)$header['id']; ?>
                            </div>
                            <div class="m-b-15">
                                <abbr class="font-bold">Date: </abbr><?php echo date("d-M-Y", strtotime($header['date'])); ?>
                            </div>
                            <div class="m-b-15">
                                <abbr class="font-bold">Incident Type: </abbr><?php
                                    $typeLabels = [
                                        'openingstock'   => 'Opening Stock',
                                        'storetransfer'  => 'Store Transfer',
                                        'stockdisposal'  => 'Stock Disposal',
                                        'stockadjustment'=> 'Stock Adjustment',
                                    ];
                                    echo $typeLabels[$header['type']] ?? $header['type'];
                                ?>
                            </div>
                            <?php if (!empty($header['reason'])): ?>
                            <div class="m-b-15">
                                <abbr class="font-bold">Reason: </abbr><?php echo html_escape($header['reason']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <br />

                    <table class="invoice-details" width="100%;">
                        <tr>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">SL.</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">Product</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">Store</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;">Batch</th>
                            <th style="border-bottom: 2px solid; border-top: 2px solid; padding: 10px; font-size: 14px;text-align: right;">Quantity</th>
                        </tr>
                        <?php $sl = 1; foreach ($items as $item): ?>
                            <tr>
                                <td style="text-align: center;"><?php echo $sl++; ?></td>
                                <td style="font-size: 14px;padding: 5px;"><?php echo $item['product_name']; ?></td>
                                <td style="font-size: 14px;padding: 5px;"><?php echo $item['store_name']; ?></td>
                                <td style="font-size: 14px;padding: 5px;"><?php echo !empty($item['batchid']) ? $item['batchid'] : '-'; ?></td>
                                <td style="text-align: right;font-size: 14px;padding: 5px;"><?php echo number_format((float)$item['quantity'], 0, '.', ',') . ' ' . $item['unit_name']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
