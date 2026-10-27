<script src="<?php echo base_url() ?>my-assets/js/admin_js/purchase.js" type="text/javascript"></script>
<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />

<?php if (isset($has_transactions) && $has_transactions): ?>
<div class="alert alert-warning">
    <strong>Warning!</strong> This conversion ratio has been used in transactions and cannot be modified. All fields are read-only.
</div>
<?php endif; ?>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title ?> </h4>
                </div>
            </div>

            <div class="panel-body">
                <?php echo form_open('conversionratio_form/' . $conversionratio->conversionratio_id, 'class="" id="subcategory_form"') ?>

                <input type="hidden" name="conversionratio_id" id="conversionratio_id" value="<?php echo $conversionratio->conversionratio_id ?>">

                <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label text-nowrap">Product <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">

                        <select class="form-control" id="product_id" <?php echo (isset($has_transactions) && $has_transactions) ? 'disabled' : 'required'; ?> name="product_id" tabindex="3" onchange="getproduct()">
                            <option value=""></option>
                            <?php if ($products) {



                            ?>
                                <?php foreach ($products as $categories) { ?>
                                    <option value="<?php echo $categories['id'] ?>" <?php if ($conversionratio->product     == $categories['id']) {
                                                                                        echo 'selected';
                                                                                    } ?>>
                                        <?php echo $categories['product_name'] ?></option>

                            <?php }
                            } ?>
                        </select>
                        <?php if (isset($has_transactions) && $has_transactions): ?>
                            <input type="hidden" name="product_id" value="<?php echo $conversionratio->product; ?>">
                        <?php endif; ?>
                    </div>
                </div>
                <!-- <div class="form-group row">
                    <label for="status" class="col-sm-2 text-right col-form-label">Convertion Type <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">
                        <select class="form-control" id="convertiontype" required name="convertiontype" tabindex="3">
                            <option value=""></option>
                            <option value="+" <?php if ($conversionratio->convertiontype == "+") echo 'selected'; ?>>Addition(+)</option>
                            <option value="-" <?php if ($conversionratio->convertiontype == "-") echo 'selected'; ?>>Subtraction(-)</option>
                            <option value="*" <?php if ($conversionratio->convertiontype == "*") echo 'selected'; ?>>Multiplication(*)</option>
                            <option value="/" <?php if ($conversionratio->convertiontype == "/") echo 'selected'; ?>>Division(/)</option>
                        </select>
                    </div>
                </div> -->
                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label text-nowrap">Master Stock Unit <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="unit" class="form-control" id="unit" placeholder="Unit" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label text-nowrap">Substock Unit <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">

                        <select class="form-control" id="subunit" <?php echo (isset($has_transactions) && $has_transactions) ? 'disabled' : 'required'; ?> name="subunit" tabindex="3">
                            <option value=""></option>
                        </select>
                        <?php if (isset($has_transactions) && $has_transactions): ?>
                            <input type="hidden" name="subunit" value="<?php echo $conversionratio->subunit; ?>">
                        <?php endif; ?>
                        <!-- <input type="text" name="subunit" class="form-control" id="subunit" placeholder="substock unit" readonly > -->
                    </div>
                </div>

                <div class="form-group row">
                    <label for="conversion_ratio" class="col-sm-2 text-right col-form-label text-nowrap">Conversion Ratio <i class="text-danger"> * </i>:</label>
                    <div class="col-sm-4">


                        <input type="text" name="conversion_ratio" class="form-control" id="conversion_ratio" placeholder="Conversion Ratio" value="<?php echo $conversionratio->conversion_ratio ?>" <?php echo (isset($has_transactions) && $has_transactions) ? 'readonly' : 'required'; ?>>
                    </div>
                </div>

               

                <div class="form-group row">

                    <div class="col-sm-6 text-right">

                        <?php if (!isset($has_transactions) || !$has_transactions): ?>
                        <button type="submit" class="btn btn-success ">
                            <?php echo (empty($conversionratio->conversionratio_id) ? display('save') : display('update')) ?></button>

                        <?php if (empty($conversionratio->conversionratio_id)) { ?>
                            <button type="submit" class="btn btn-success" name="add-another"><?php echo display('save_and_add_another') ?></button>
                        <?php } ?>
                        <?php else: ?>
                            <a href="<?php echo base_url(); ?>conversionratio_list" class="btn btn-default">Back to List</a>
                        <?php endif; ?>

                    </div>
                </div>


                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
<?php
echo "<script> ";
echo "let products=" . json_encode($products) . ";";
echo "let conversionratio=" . json_encode($conversionratio) . ";";
echo "let conversionratioId=" . json_encode($conversionratio->conversionratio_id) . ";";

echo "</script>";
?>

<script>
    $(document).ready(function() {
        if (conversionratioId > 0) {
            getproduct();
        }
    });

    function getproduct() {

        console.log(products)
        let product = products.find(product => product.id === document.getElementById('product_id').value);

        document.getElementById('unit').value = product.unit_name
        // document.getElementById('subunit').value=product.subunit
        $.ajax({
            url: $('#baseUrl2').val() + 'product/product/active_subunitsbyproductId',
            type: 'POST',
            data: {
                product_id: document.getElementById('product_id').value
            },
            success: function(response) {
                // alert("Invoice Details Updated Successfully")
                // window.location.href = $('#base_url').val() + 'invoice_list';
                datas = JSON.parse(response);
                var $subunitDropdown = $('#subunit');
                $subunitDropdown.empty();
                $subunitDropdown.append('<option value="" disabled selected>Select substock unit</option>'); // Add default option

                $.each(datas, function(index, store) {
                    $subunitDropdown.append('<option value="' + store.unit_id + '">' + store.unit_name + '</option>');
                });

                if (conversionratioId > 0) {
                    $subunitDropdown.val(conversionratio.subunit)

                }


            },
            error: function(error) {
                console.log(error)
            }
        });


    }
</script>