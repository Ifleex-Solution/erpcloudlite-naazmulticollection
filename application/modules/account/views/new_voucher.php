<div class="row">
    <div class="col-sm-12 col-md-12">
        <div class="panel panel-bd">
            <div class="panel-heading" id="style12">
                <div class="panel-title has-goback">
                <span id="title"><?php echo $title ?></span>

                
<a href="<?php echo base_url($type == 2 ? 'manage_receipt_voucher' : ($type == 3 ? 'manage_contra_voucher' : 'manage_payment_voucher')) ?>" class="btn btn-default btn-goback"><i class="fa fa-arrow-left"></i> Go Back</a>
</div>
            </div>
            <div class="panel-body">
                <div class="container mt-5">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label for="vo_no" class="col-sm-4 col-form-label"><?php echo display('voucher_type') ?></label>
                                <div class="col-sm-8">

                                    <?php if ($type == 1) { ?>
                                        <input type="text" name="type_name" id="type_name" value="Payment Voucher" class="form-control" readonly />

                                    <?php } ?>

                                    <?php if ($type == 2) { ?>
                                        <input type="text" name="type_name" id="type_name" value="Receipt Voucher" class="form-control" readonly />

                                    <?php } ?>

                                    <?php if ($type == 3) { ?>
                                        <input type="text" name="type_name" id="type_name" value="Contra Voucher" class="form-control" readonly />

                                    <?php } ?>

                                    <input type="hidden" name="type" id="type" value="<?php echo $type; ?>">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="supplier_sss" class="col-sm-4 col-form-label">Branch
                                    <i class="text-danger">*</i>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="branch" required name="branch" tabindex="3">


                                    </select>
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="ac" class="col-sm-4 col-form-label"><?php echo $from; ?>
                                    <i class="text-danger">*</i>
                                </label>
                                <div class="col-sm-8">
                                    <select name="cmbDebit" class="form-control" id="cmbDebit" tabindex="1">
                                        <option value="">Select an option</option>
                                        <?php foreach ($all_pmethod as $services) { ?>
                                            <option value="<?php echo $services['id']; ?>" <?php echo $voucher_info[0]['from'] ==  $services['id'] ? 'selected' : ''; ?>>
                                                <?php echo $services['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="date" class="col-sm-4 col-form-label"><?php echo display('date') ?></label>
                                <div class="col-sm-8">
                                    <input type="text" name="dtpDate" id="dtpDate" class="form-control datepicker" value="<?php
                                                                                                                            date_default_timezone_set('Asia/Colombo');

                                                                                                                            echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="txtRemarks" class="col-sm-4 col-form-label"><?php echo display('remark') ?></label>
                                <div class="col-sm-8">
                                    <textarea name="txtRemarks" id="txtRemarks" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div id="che" style="display:none;"> <input type="checkbox" id="check" onclick="showHideDiv('0')"> Cheque Transaction</div>
                            </div>
                            <div id="myDiv" style="display:none;">
                                <div class="form-group row">
                                    <input type="hidden" name="chequeid" id="chequeid" class="form-control" value="">

                                    <label for="date" class="col-sm-4 col-form-label">Cheque No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="chequeno" id="chequeno" class="form-control" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="effectivedate" class="col-sm-4 col-form-label"> Effective Date</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="effectivedate" id="effectivedate" class="form-control datepicker" value="<?php echo  date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="draftdate" class="col-sm-4 col-form-label"> Draft Date</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="draftdate" id="draftdate" class="form-control datepicker" value="<?php echo  date('Y-m-d') ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-sm-4 col-form-label"> <?php echo display('description') ?></label>
                                    <div class="col-sm-8">
                                        <textarea name="description" id="description" class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="debtAccVoucher">
                            <thead>
                                <tr>
                                    <th width="20%" class="text-center"><?php echo $to; ?>
                                        <i class="text-danger">*</i>
                                    </th>
                                    <th width="20%" class="text-center"><?php echo display('subtype') ?></th>
                                    <th width="30%" class="text-center"><?php echo display('ledger_comment') ?></th>
                                    <th width="20%" class="text-center"><?php echo display('amount') ?>
                                        <i class="text-danger">*</i>
                                    </th>
                                    <th width="10%" class="text-center"><?php echo display('action') ?></th>
                                </tr>
                            </thead>

                            <?php if ($id == 0) { ?>

                                <tbody id="debitvoucher">

                                    <tr>
                                        <td class="expenseincometd">
                                            <select name="cmbCode[]" id="cmbCode_1" class="form-control" required>
                                                <option value="">Please select One</option>
                                                <?php foreach ($acc as $services) { ?>
                                                    <option value="<?php echo $services['HeadCode']; ?>">
                                                        <?php echo $services['HeadName']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="subtype[]" id="subtype_1" class="form-control" disabled>
                                                <option value="">Please select One</option>
                                            </select>

                                        </td>
                                        <td><input type="hidden" name="isSubtype[]" id="isSubtype_1" value="1" />
                                            <input type="text" name="txtComment[]" value="" class="form-control " id="txtComment_1">
                                        </td>

                                        <td><input type="number" name="txtAmount[]" value="" required class="form-control total_price text-right" step=".01" id="txtAmount_1" onkeyup="calculationDebtv(1)">
                                        </td>
                                        <td>
                                            <button class="btn btn-danger red text-right" type="button" value="<?php echo display('delete') ?>" onclick="deleteRowDebtv(this)"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>

                                </tbody>
                            <?php } ?>



                            <?php if ($id != 0) { ?>
                                <tbody id="debitvoucher">
                                    <?php $sl = 1;
                                    $total = 0;
                                    foreach ($voucher_info as $voucher) { ?>
                                        <tr>
                                            <td class="expenseincometd">
                                                <select name="cmbCode[]" id="cmbCode_<?php echo $sl; ?>" required class="form-control">
                                                    <option value="">Please select One</option>

                                                    <?php foreach ($acc as $services) { ?>
                                                        <option value="<?php echo $services['HeadCode']; ?>" <?php echo $voucher['to'] == $services['HeadCode'] ? 'selected' : ''; ?>>
                                                            <?php echo $services['HeadName']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </td>

                                            <td>
                                                <select name="subtype[]" id="subtype_<?php echo $sl; ?>" class="form-control" disabled>
                                                    <option value="">Please select One</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" name="txtComment[]" value="<?php echo  $voucher['comment']; ?>" class="form-control " id="txtComment_<?php echo $sl; ?>">
                                            </td>


                                            <td><input type="number" name="txtAmount[]" required value="<?php echo $voucher['amount']; ?>" class="form-control total_price text-right" id="txtAmount_<?php echo $sl; ?>" onkeyup="calculationCreditv(<?php echo $sl; ?>)">
                                            </td>
                                            <td>
                                                <button class="btn btn-danger red text-right" type="button" value="<?php echo display('delete') ?>" onclick="deleteRowCreditv(this)"><i class="fa fa-trash-o"></i></button>
                                            </td>
                                        </tr>
                                    <?php $sl++;
                                        $total += $voucher->amount;
                                    } ?>
                                </tbody>
                            <?php } ?>

                            <tfoot>
                                <tr>
                                    <td>
                                        <input type="button" id="add_more" class="btn btn-info" name="add_more" onClick="addaccountDebt('debitvoucher');" value="<?php echo display('add_more') ?>" />
                                    </td>
                                    <td colspan="2" class="text-right"><label for="reason" class="  col-form-label"><?php echo display('total') ?></label>
                                    </td>
                                    <td class="text-right">
                                        <input type="text" id="grandTotal" class="form-control text-right " name="grand_total" value="" readonly="readonly" />
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" name="finyear" value="<?php echo financial_year(); ?>">
                    <div class="form-group form-group-margin row">

                        <div class="col-sm-12 text-right">

                            <button id="save_add" class="btn btn-success" name="add-invoice" onclick="save()">
                                <?php
                                echo empty($id)
                                    ? display('save')
                                    : (empty($pagetype) ? display('update') : display('save'));
                                ?>


                                <input type="hidden" name="" id="headoption" value="<option value=''> Please select</option><?php foreach ($acc as $services) { ?><option value='<?php echo $services['HeadCode']; ?>'><?php echo $services['HeadName']; ?></option><?php } ?>">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Cheques</h4>
                </div>
                <div class="modal-body">
                    <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Cheque No</th>
                                <th>Customer Name</th>
                                <th>Effective Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th></th>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/dist/jstree.min.js"></script>
<script src="<?php echo base_url('assets/dist/account.js') ?>" type="text/javascript"></script>
<?php
echo "<script>";
echo "let id = " . json_encode($id) . ";";
echo "let voucher_info=" . json_encode($voucher_info) . ";";
echo "let from=" . json_encode($from) . ";";
echo "let to=" . json_encode($to) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script>
    function handleTableClick(rowId, customerId, amount, chequeno, draftdate, effectivedate, description) {
        // Parse JSON string back to object
        // rowData = JSON.parse(rowData);
        // Handle click here
        // if (id == 0) {
        //     $('#pamount_by_method').val(amount);
        //     $('#pamount_by_method').prop('readonly', true);


        // } else {
        //     $('#pamount_by_method' + id).val(amount);
        //     $('#pamount_by_method' + id).prop('readonly', true);


        // }
        // $('#' + "che_" + id).hide();
        // $('#' + "myDiv_" + id).show();

        $('#chequeid').val(rowId);

        $('#chequeno').val(chequeno);
        $('#chequeno').prop('readonly', true);

        $('#draftdate').val(draftdate);
        $('#draftdate').prop('readonly', true);


        $('#effectivedate').val(effectivedate);
        $('#effectivedate').prop('readonly', true);



        $('#description').val(description);
        $('#txtAmount_1').val(amount);
        $('#txtAmount_1').prop('readonly', true);



        // $('#effectivedate' ).prop('readonly', true);



        $("#myDiv").show();
        $("#exampleModal").modal('hide');




        // You can access properties of rowData as needed
    }
    let type2 = ""
    if (usertype == 3) {
        document.getElementById('style12').style.backgroundColor = '#E0E0E0';
        const title = document.getElementById('title');
        title.style.color = 'blue';
        type2 = "B"

    } else {
        type2 = "A"

    }
    $(document).ready(function() {

        if (id > 0) {
            document.getElementById('dtpDate').value = voucher_info[0].date
            document.getElementById('txtRemarks').value = voucher_info[0].remark

            document.getElementById('grandTotal').value = voucher_info[0].total
            document.getElementById('type').value = voucher_info[0].type

            getBranchDropdown(voucher_info[0].branch);

        } else {
            getBranchDropdown(0);
        }
    });
    $(document).on('change', '#cmbDebit1', function() {
        $('#add_more').show();
        $('#chequeno').prop('readonly', false);
        // $('#description' + id).prop('readonly', false);
        $('#draftdate').prop('readonly', false);
        $('#effectivedate').prop('readonly', false);

        var x = document.getElementById("cmbDebit1").value;
        var is_credit_edit = '';
        var csrf_test_name = '';
        $('#chequeid').val("");

        $('#chequeno').val("");
        $('#draftdate').val("");
        $('#effectivedate').val("");
        $('#description').val("");
        $('#txtAmount_1').val("");
        $('#' + "che").hide();
        $("#myDiv").hide();
        $('#txtAmount_1').prop('readonly', false);



        var url = $('#base_url').val() + "purchase/purchase/bdtask_typeofthepayment/" + x;
        $.ajax({
            type: "post",
            url: url,
            data: {
                is_credit_edit: is_credit_edit,
                csrf_test_name: csrf_test_name
            },
            success: function(data) {
                var parsedData = JSON.parse(data);



                if (parsedData[0].HeadName === '3rd party cheque') {
                    $('#add_more').hide();
                    $("#exampleModal").modal('show');

                    var url1 = $('#base_url').val() + "purchase/purchase/getallcheques2";
                    $.ajax({
                        type: "post",
                        url: url1,
                        data: {
                            csrf_test_name: csrf_test_name,
                            is_credit_edit: is_credit_edit
                        },
                        success: function(data1) {

                            var parsedData1 = JSON.parse(data1);
                            $('#example').DataTable({
                                "bDestroy": true,
                                "data": parsedData1, // Use parsed data as the DataTable source
                                "columns": [{
                                        "data": "cheque_no"
                                    },
                                    {
                                        "data": "customer_name"
                                    },
                                    {
                                        "data": "effectivedate"
                                    },
                                    {
                                        "data": "amount"
                                    },
                                    {
                                        "data": "status"
                                    },
                                    {
                                        "data": null,
                                        "render": function(data, type, row) {
                                            // Check if row exists
                                            if (row) {
                                                // console.log(row.draftdate)
                                                // // Encode row data to prevent errors in JSON stringification
                                                // var draftDateString = row.draftdate.toISOString(); // or use any desired date format
                                                // var effectiveDateString = row.effectivedate.toISOString();

                                                return '<button class="btn btn-primary" onclick="handleTableClick(' + row.id + ',' + row.customer_id + ',' + row.amount + ',\'' + row.cheque_no + '\',\'' + row.draftdate + '\',\'' + row.effectivedate + '\',\'' + row.description + '\')">Call</button>';
                                            } else {
                                                return ''; // Return empty string if row is null or undefined
                                            }
                                        }
                                    }

                                ]
                            });
                        }
                    });
                } else if (parsedData[0].PHeadName === 'Cash at Bank') {
                    $('#' + "che").show();
                    $('#' + "myDiv").hide();
                    $('#chequeno').val("");
                    $('#description').val("");
                    $('#draftdate').val("");
                    var currentDate = new Date().toISOString().slice(0, 10);

                    // Set the value of the input field with id 'effectivedate'
                    document.getElementById('effectivedate').value = currentDate;




                } else {

                    $('#' + "che").hide();
                    $('#' + "myDiv").hide();
                    $('#chequeid').val("");
                    $('#chequeno').val("");
                    $('#description' + id).val("");
                    $('#draftdate').val("");
                    var currentDate = new Date().toISOString().slice(0, 10);

                    // Set the value of the input field with id 'effectivedate'
                    document.getElementById('effectivedate').value = currentDate;


                }


            }
        });
    });


    function showHideDiv(id) {
        var divId = "myDiv";
        if ($('#check').prop('checked')) {
            $('#' + divId).show();
            var currentDate = new Date().toISOString().slice(0, 10);

            // Set the value of the input field with id 'effectivedate'
            document.getElementById('effectivedate').value = currentDate;
            $('#add_more').hide();

        } else {
            $('#chequeid').val("");
            $('#' + divId).hide();
            $('#chequeno').val("");
            $('#description').val("");


        }
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
                var $branchDropdown = $('#branch');
                $branchDropdown.empty();
                $branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

                $.each(branches, function(index, branch) {
                    $branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id)
                        // getSalesOrderDropdown()
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

    function save() {

        const txtComment = document.getElementsByName("txtComment[]");


        arrItem = [];
        if (document.getElementById('branch').value == "") {
            alert("Branch Shouldn't be empty")
            return
        } else if (document.getElementById('cmbDebit').value == "") {
            alert(from + " Shouldn't be empty")
            return
        }

        for (let i = 1; i <= txtComment.length; i++) {
            if (document.getElementById('cmbCode_' + i).value == "") {
                alert(to + " Shouldn't be empty")
                return
            } else if (document.getElementById('txtAmount_' + i).value == "") {
                alert("Amount Shouldn't be empty")
                return
            }
            arrItem.push({
                to: document.getElementById('cmbCode_' + i).value,
                sto: document.getElementById('subtype_' + i).value != "" ? document.getElementById('subtype_' + i).value : 0,
                comment: document.getElementById('txtComment_' + i).value,
                amount: document.getElementById('txtAmount_' + i).value,
            })
        }
        $("#save_add").hide();


        if (id == 0) {
            $.ajax({
                url: $('#base_url').val() + 'account/accounts/save_voucher',
                type: 'POST',
                data: {
                    items: arrItem,
                    branch: document.getElementById('branch').value,
                    type: document.getElementById('type').value,
                    from: document.getElementById('cmbDebit').value,
                    date: document.getElementById('dtpDate').value,
                    remark: document.getElementById('txtRemarks').value,
                    total: document.getElementById('grandTotal').value,
                    type2:type2
                },
                success: function(response) {
                    datas = JSON.parse(response);
                    $("#save_add").show();
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.voucher_no, datas.type);
                },
                error: function(error) {
                    console.log(error)
                }
            });

        }else{
            $.ajax({
                url: $('#base_url').val() + 'account/accounts/update_voucher',
                type: 'POST',
                data: {
                    id:id,
                    items: arrItem,
                    branch: document.getElementById('branch').value,
                    type: document.getElementById('type').value,
                    from: document.getElementById('cmbDebit').value,
                    date: document.getElementById('dtpDate').value,
                    remark: document.getElementById('txtRemarks').value,
                    total: document.getElementById('grandTotal').value,
                    type2:type2
                },
                success: function(response) {
                    datas = JSON.parse(response);
                    $("#save_add").show();
                    if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
                    printRawHtml(datas.details, datas.voucher_no, datas.type);
                },
                error: function(error) {
                    console.log(error)
                }
            });
        }


    }

    function pdfDateStamp() {
        var d = new Date();
        var dd = String(d.getDate()).padStart(2, '0');
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        return dd + mm + d.getFullYear();
    }

    function printRawHtml(view, voucherNo, voucherType) {
        if (voucherNo) {
            var prefixMap = { 1: 'paymentvoucher', 2: 'receiptvoucher', 3: 'contravoucher' };
            var prefix = prefixMap[voucherType] || 'voucher';
            document.title = prefix + '_' + voucherNo + '_' + pdfDateStamp();
        }
        $(view).print({
            deferred: $.Deferred().done(function() {
                window.location.reload(true);
            })
        });
    }
</script>