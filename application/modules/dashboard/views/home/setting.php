        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-bd lobidrag">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h4><?php echo display('update_setting');
                             ?> </h4>
                        </div>
                    </div>
                    <?php echo form_open_multipart('dashboard/setting/bdtask_create_settings', array('class' => 'form-vertical', 'id' => 'insert_setting')) ?>
                    <div class="panel-body">

                        <input type="hidden" name="id" value="<?php echo $sdata->setting_id?>">

                        <div class="form-group row">
                            <label for="logo" class="col-sm-3 col-form-label"><?php echo display('logo') ?></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="logo" id="logo" type="url" tabindex="1"
                                    placeholder="https://example.com/logo.png"
                                    value="<?php echo $sdata->logo ?>"
                                    onchange="document.getElementById('logo_preview').src = this.value;">
                                <div style="font-size:12px; color:#888; margin-top:4px;">Paste a direct image URL (e.g. a Google Drive direct-image link) — not a file upload.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-6">
                                <img id="logo_preview" src="<?php echo logo_url($sdata->logo, 'assets/img/icons/mini-logo.png') ?>" class="img img-responsive" height="100" width="100"
                                    onerror="this.style.visibility='hidden';" onload="this.style.visibility='visible';">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="invoice_logo" class="col-sm-3 col-form-label"><?php echo display('invoice_logo') ?> (Sale Logo)</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="invoice_logo" id="invoice_logo" type="url" tabindex="2"
                                    placeholder="https://example.com/sale-logo.png"
                                    value="<?php echo $sdata->invoice_logo ?>"
                                    onchange="document.getElementById('invoice_logo_preview').src = this.value;">
                                <div style="font-size:12px; color:#888; margin-top:4px;">Paste a direct image URL — not a file upload.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-6">
                                <img id="invoice_logo_preview" src="<?php echo logo_url($sdata->invoice_logo, 'assets/img/icons/mini-logo.png') ?>" class="img img-responsive" height="100" width="100"
                                    onerror="this.style.visibility='hidden';" onload="this.style.visibility='visible';">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="favicon" class="col-sm-3 col-form-label"><?php echo display('favicon') ?></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="favicon" id="favicon" type="url" tabindex="3"
                                    placeholder="https://example.com/favicon.png"
                                    value="<?php echo $sdata->favicon ?>"
                                    onchange="document.getElementById('favicon_preview').src = this.value;">
                                <div style="font-size:12px; color:#888; margin-top:4px;">Paste a direct image URL — not a file upload.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label"></label>
                            <div class="col-sm-6">
                                <img id="favicon_preview" src="<?php echo logo_url($sdata->favicon, 'assets/img/icons/favicon.png') ?>" class="img img-responsive" height="100" width="100"
                                    onerror="this.style.visibility='hidden';" onload="this.style.visibility='visible';">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="currency" class="col-sm-3 col-form-label"><?php echo display('currency') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="currency" id="currency" tabindex="5">
                                    <?php foreach($currency_list as $clist){?>
                                    <option value="<?php echo $clist->icon?>" <?php if ($sdata->currency == $clist->icon) {
                        echo "selected";
                    } ?>><?php echo $clist->currency_name.' '.$clist->icon;?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="time_zone" class="col-sm-3 col-form-label"><?php echo display('timezone') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <?php echo form_dropdown('timezone', $timezonelist,$sdata->timezone , array('class'=>'form-control')); ?>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="currency_position"
                                class="col-sm-3 col-form-label"><?php echo display('currency_position') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="currency_position" id="currency_position"
                                    tabindex="6">
                                    <option value=""><?php echo display('select_one') ?></option>
                                    <option value="0" <?php if ($sdata->currency_position == 0) {
                                    echo "selected";
                                } ?>><?php echo display('left') ?></option>
                                    <option value="1" <?php if ($sdata->currency_position == 1) {
                                    echo "selected";
                                } ?>><?php echo display('right') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="footer_text"
                                class="col-sm-3 col-form-label"><?php echo display('footer_text') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="footer_text" id="footer_text" type="text"
                                    placeholder="Foter Text" value="<?php echo $sdata->footer_text?>" tabindex="7">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="terms_conditions" class="col-sm-3 col-form-label">Sales Terms &amp; Conditions</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="terms_conditions" id="terms_conditions" rows="5"
                                    placeholder="Enter each term on a new line..."
                                    tabindex="8"><?php echo isset($sdata->terms_conditions) ? htmlspecialchars($sdata->terms_conditions) : ''; ?></textarea>
                                <small class="text-muted">Each line will appear as a separate point on the Sales invoice. Leave empty to hide.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="service_terms_conditions" class="col-sm-3 col-form-label">Service Terms &amp; Conditions</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="service_terms_conditions" id="service_terms_conditions" rows="5"
                                    placeholder="Enter each term on a new line..."
                                    tabindex="8"><?php echo isset($sdata->service_terms_conditions) ? htmlspecialchars($sdata->service_terms_conditions) : ''; ?></textarea>
                                <small class="text-muted">Each line will appear as a separate point on the Service invoice. Leave empty to hide.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="expiry_alert_days" class="col-sm-3 col-form-label">Expiry Alert Days <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="expiry_alert_days" id="expiry_alert_days" type="number"
                                    min="0" step="1" value="<?php echo isset($sdata->expiry_alert_days) ? $sdata->expiry_alert_days : 0; ?>" tabindex="8">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="credit_reminder_days" class="col-sm-3 col-form-label">Credit Reminder Days <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="credit_reminder_days" id="credit_reminder_days" type="number"
                                    min="0" step="1" value="<?php echo isset($sdata->credit_reminder_days) ? $sdata->credit_reminder_days : 0; ?>" tabindex="8">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="sale_credit_limit" class="col-sm-3 col-form-label">Sale Credit Limit</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="sale_credit_limit" id="sale_credit_limit" type="number"
                                    min="0" step="0.01" value="<?php echo isset($sdata->sale_credit_limit) ? $sdata->sale_credit_limit : 0; ?>" tabindex="8">
                                <div style="font-size:12px; color:#888; margin-top:4px;">Company-wide cap on total outstanding Sale credit due. 0 = no limit.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="service_credit_limit" class="col-sm-3 col-form-label">Service Credit Limit</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="service_credit_limit" id="service_credit_limit" type="number"
                                    min="0" step="0.01" value="<?php echo isset($sdata->service_credit_limit) ? $sdata->service_credit_limit : 0; ?>" tabindex="8">
                                <div style="font-size:12px; color:#888; margin-top:4px;">Company-wide cap on total outstanding Service credit due. 0 = no limit.</div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="language" class="col-sm-3 col-form-label"><?php echo display('language') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <?php
                        echo form_dropdown('language', $languageList, $sdata->language, 'class="form-control" id="language" tabindex="9"');
                        ?>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lrt" class="col-sm-3 col-form-label"><?php echo display('ltr_or_rtr') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="rtr" id="lrt" tabindex="10">
                                    <option value=""><?php echo display('select_one') ?></option>
                                    <option value="0" <?php if ($sdata->rtr == 0) {
    echo "selected";
} ?>><?php echo display('ltr') ?></option>
                                    <option value="1" <?php if ($sdata->rtr == 1) {
    echo "selected";
} ?>><?php echo display('rtr') ?></option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="discount_type"
                                class="col-sm-3 col-form-label"><?php echo display('discount_type') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="discount_type" id="discount_type" tabindex="11">
                                    <option value=""><?php echo display('select_one') ?></option>
                                    <option value="1" <?php if ($sdata->discount_type == 1) {
                                        echo "selected";
                                    } ?>><?php echo display('discount_percentage') ?> %</option>
                                                    <option value="2" <?php if ($sdata->discount_type == 2) {
                                        echo "selected";
                                    } ?>><?php echo display('discount') ?></option>
                                                    <option value="3" <?php if ($sdata->discount_type == 3) {
                                        echo "selected";
                                    } ?>><?php echo display('fixed_dis') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="discount_type"
                                class="col-sm-3 col-form-label"><?php echo display('invoice_qr_code') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                            <input type="checkbox" name="is_qr" class="is-qr-pos"  value="1" <?php if($sdata->is_qr == 1){echo 'checked';}?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="discount_type"
                                class="col-sm-3 col-form-label"><?php echo display('is_autoapprove_v') ?> <i
                                    class="text-danger">*</i></label>
                            <div class="col-sm-6">
                            <input type="checkbox" name="is_autoapprove_v" class="is-qr-pos"  value="1" <?php if($sdata->is_autoapprove_v == 1){echo 'checked';}?>>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Virtual Keyboard <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="checkbox" name="vk_enable" class="is-qr-pos" value="1" <?php if(isset($sdata->vk_enable) && $sdata->vk_enable == 1){ echo 'checked'; } ?>>
                                <span style="font-size:12px; color:#888; margin-left:6px;">Enable virtual keyboard on Touch Invoice</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Product Image Upload <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input type="checkbox" name="product_image_upload" class="is-qr-pos" value="1" <?php if(isset($sdata->product_image_upload) && $sdata->product_image_upload == 1){ echo 'checked'; } ?>>
                                <span style="font-size:12px; color:#888; margin-left:6px;">Enable product image upload in Add/Edit Product page</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Login Alert Pop-ups</label>
                            <div class="col-sm-9">
                                <div style="margin-bottom:10px;">
                                    <label style="font-weight:600;">
                                        <input type="checkbox" id="credit_alert_enable" name="credit_alert_enable" value="1" <?php if (isset($sdata->credit_alert_enable) && $sdata->credit_alert_enable == 1) { echo 'checked'; } ?>>
                                        Credit Alert Notification
                                    </label>
                                    <div style="font-size:12px; color:#888; margin-left:20px;">Unpaid invoices — shown as separate Customer and Supplier pop-ups</div>
                                    <div style="margin-left:20px; margin-top:6px;">
                                        <label style="display:block;">
                                            <input type="checkbox" class="credit-alert-sub" name="credit_alert_customer_enable" value="1" <?php if (isset($sdata->credit_alert_customer_enable) && $sdata->credit_alert_customer_enable == 1) { echo 'checked'; } ?>>
                                            Customer — pop-up 1 · unpaid customer invoices
                                        </label>
                                        <label style="display:block;">
                                            <input type="checkbox" class="credit-alert-sub" name="credit_alert_supplier_enable" value="1" <?php if (isset($sdata->credit_alert_supplier_enable) && $sdata->credit_alert_supplier_enable == 1) { echo 'checked'; } ?>>
                                            Supplier — pop-up 2 · unpaid supplier invoices
                                        </label>
                                    </div>
                                </div>
                                <div style="margin-bottom:10px;">
                                    <label style="font-weight:600;">
                                        <input type="checkbox" name="expiry_alert_enable" value="1" <?php if (isset($sdata->expiry_alert_enable) && $sdata->expiry_alert_enable == 1) { echo 'checked'; } ?>>
                                        Expiration Alert Notification
                                    </label>
                                    <div style="font-size:12px; color:#888; margin-left:20px;">Pop-up 3 · Expired / To be Expired batches</div>
                                </div>
                                <div>
                                    <label style="font-weight:600;">
                                        <input type="checkbox" name="stock_alert_enable" value="1" <?php if (isset($sdata->stock_alert_enable) && $sdata->stock_alert_enable == 1) { echo 'checked'; } ?>>
                                        Stock Alert Notification
                                    </label>
                                    <div style="font-size:12px; color:#888; margin-left:20px;">Pop-up 4 · Out of Stock / Reorder / Near Order / Overstock</div>
                                </div>
                            </div>
                        </div>
                        <script>
                            (function () {
                                function syncCreditSubToggles() {
                                    var masterOn = document.getElementById('credit_alert_enable').checked;
                                    document.querySelectorAll('.credit-alert-sub').forEach(function (cb) {
                                        cb.disabled = !masterOn;
                                    });
                                }
                                document.getElementById('credit_alert_enable').addEventListener('change', syncCreditSubToggles);
                                syncCreditSubToggles();
                            })();
                        </script>

                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">POS Print Type <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <select class="form-control" name="pos_print_type" id="pos_print_type">
                                    <option value="0" <?php if(isset($sdata->pos_print_type) && $sdata->pos_print_type == 0){ echo 'selected'; } ?>>PDF Print (A4 / A5)</option>
                                    <option value="1" <?php if(isset($sdata->pos_print_type) && $sdata->pos_print_type == 1){ echo 'selected'; } ?>>Thermal Print (80mm)</option>
                                    <option value="2" <?php if(isset($sdata->pos_print_type) && $sdata->pos_print_type == 2){ echo 'selected'; } ?>>Dot-matrix Print</option>
                                </select>
                                <span style="font-size:12px; color:#888; margin-top:4px; display:block;">Applies to Sale Invoice (Touch Invoice / POS) only</span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="example-text-input" class="col-sm-4 col-form-label"></label>
                            <div class="col-sm-6">
                                <input type="submit" id="add-setting" class="btn btn-success btn-large"
                                    name="add-setting" value="<?php echo display('save_changes') ?>" tabindex="13" />
                            </div>
                        </div>
                    </div>
                    <?php echo form_close() ?>
                </div>
            </div>
        </div>