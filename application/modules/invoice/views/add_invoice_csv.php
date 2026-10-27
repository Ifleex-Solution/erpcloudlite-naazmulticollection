<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.2/papaparse.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="row">
    <div class="col-sm-12 col-md-12">
        <!-- Multiple panels with drag & drop -->
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo display('csv_file_informaion') ?></h4>
                </div>
            </div>
            <div class="panel-body">
                <a href="<?php echo base_url('assets/data/csv/sample_invoice.csv') ?>" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample File</a>
                <span class="text-warning">The first line in downloaded csv file should remain as it is. Please do not change the order of columns.</span><br>The correct column order is <span class="text-info">
                    (Invoice ID,Sale Date, Branch, Incident Type,Customer ,Employee,Product,Store,Batch,Unit,Qty,Price Val,Discount,VAT,Sale Discount,Payment Type,Details )</span> &amp; you must follow this.<br>Please make sure the csv file is UTF-8 encoded and not saved with byte order mark (BOM).<p>The images should be uploaded in <strong>uploads</strong> folder.</p>
            </div>
        </div>.

    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Import Invoice CSV</h4>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group row">
                            <label for="upload_csv_file" class="col-sm-4 col-form-label"><?php echo display('upload_csv_file') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-8">
                                <input class="form-control" name="upload_csv_file" type="file" id="csvFile" placeholder="<?php echo display('upload_csv_file') ?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-6">
                        <input type="button" onclick="uploadCSV()" id="save_add" class="btn btn-primary btn-large" name="add-product" value="<?php echo display('submit') ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Uploaded Invoice Details</h4>
                </div>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
                    <thead>
                        <tr>
                            <th><?php echo display('sl') ?></th>
                            <th>Uploaded Id</th>
                            <th>Date</th>
                            <th>Uploaded By</th>
                            <th><?php echo display('action') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<?php
echo "<script>";
echo "let pmethods=" . json_encode($pmetods) . ";";
echo "let products=" . json_encode($products) . ";";
echo "let stores=" . json_encode($stores) . ";";
echo "let batches=" . json_encode($batches) . ";";
echo "let units=" . json_encode($units) . ";";
echo "let customers=" . json_encode($customers) . ";";
echo "let employees=" . json_encode($employees) . ";";
echo "let branches=" . json_encode($branches) . ";";
echo "let invoicesdetails=" . json_encode($invoicesdetails) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>

<script>
    $(document).ready(function() {
        console.log(batches)
        batches.push({
            id: "0",
            batchid: "Default"
        })
    });

    async function uploadCSV() {
        console.log(products);
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a CSV file.');
            return;
        }
        $("#save_add").hide();

        // Wrap Papa.parse in a Promise
        function parseCSV(file) {
            return new Promise((resolve, reject) => {
                Papa.parse(file, {
                    header: true,
                    transformHeader: (header) => header.replace(/\s+/g, ''),
                    complete: (results) => resolve(results.data),
                    error: (err) => reject(err)
                });
            });
        }

        // Wrap $.ajax in a Promise
        function ajaxRequest(url, data) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url,
                    type: 'POST',
                    data,
                    success: (res) => resolve(res),
                    error: (err) => reject(err)
                });
            });
        }

        try {
            let uploadeddata = await parseCSV(file);

            uploadeddata.sort((a, b) => {
                return (a.InvoiceID || '').localeCompare(b.InvoiceID || '');
            });

            let rownum = 2;
            let invoiceId = "";
            let tdis = 0;
            let tvat = 0;
            let ttotalprice = 0;
            let grandtotal = 0;
            let saleDiscount = 0;

            arrInvoice = [];
            arrItem = [];

            for (const item of uploadeddata) {
                if (!item.InvoiceID) continue;

                // ===== Product Check =====
                let productcheck = products?.find(
                    product => product.product_name.toLowerCase() === item.Product.toLowerCase()
                );
                if (!productcheck) {
                    alert(`Cannot find Product: ${item.Product}\nRow Number: ${rownum}\nColumn: Product`);
                    $("#save_add").show();
                    return;
                }

                // ===== Unit Check =====
                let unitcheck = units?.find(
                    unit => unit.unit_name.toLowerCase() === item.Unit.toLowerCase()
                );
                if (!unitcheck) {
                    alert(`Cannot find Unit: ${item.Unit}\nRow Number: ${rownum}\nColumn: Unit`);
                    $("#save_add").show();
                    return;
                }

                // ===== Call conversion API with async/await =====
                let response = await ajaxRequest($('#base_url').val() + 'stock/stock/conversion', {
                    product_id: productcheck.id,
                    unit: unitcheck.unit_id
                });
                
                let convertionratio = ""
                let conversionid=0;

                if (response != "not") {
                     let datas = JSON.parse(response);
                    convertionratio = datas[0].conversion_ratio,
                    conversionid=datas[0].conversionratio_id
                }

                // ===== Invoice Duplicate Check =====
                if (invoicesdetails) {
                    let invoicecheck = invoicesdetails.find(
                        invoice => invoice.sale_id.toLowerCase() === item.InvoiceID.toLowerCase()
                    );
                    if (invoicecheck) {
                        alert(`Invoice ID Already exists: ${item.InvoiceID}\nRow Number: ${rownum}\nColumn: Invoice`);
                        $("#save_add").show();
                        return;
                    }
                }

                // ===== Store Check =====
                let storecheck = stores?.find(
                    store => store.name.toLowerCase() === item.Store.toLowerCase()
                );
                if (!storecheck) {
                    alert(`Cannot find Store: ${item.Store}\nRow Number: ${rownum}\nColumn: Store`);
                    $("#save_add").show();
                    return;
                }

                // ===== Batch Check =====
                let batchcheck = batches?.find(
                    batch => batch.batchid.toLowerCase() === item.Batch.toLowerCase()
                );
                if (!batchcheck) {
                    alert(`Cannot find Batch: ${item.Batch}\nRow Number: ${rownum}\nColumn: Batch`);
                    $("#save_add").show();
                    return;
                }

                // ===== Payment Type Check =====
                let paymentcheck = pmethods?.find(
                    pmethod => pmethod.name.toLowerCase() === item.PaymentType.toLowerCase()
                );
                if (!paymentcheck) {
                    alert(`Cannot find Payment Type: ${item.PaymentType}\nRow Number: ${rownum}\nColumn: Payment Type`);
                    $("#save_add").show();
                    return;
                }

                // ===== Incident Type =====
                let incidentTypeId = 0;
                if (item.IncidentType.toLowerCase() === 'retail') {
                    incidentTypeId = 1;
                } else if (item.IncidentType.toLowerCase() === 'wholesale') {
                    incidentTypeId = 2;
                } else {
                    alert(`Cannot find Incident Type: ${item.IncidentType}\nRow Number: ${rownum}\nColumn: Incident Type`);
                    $("#save_add").show();
                    return;
                }

                // ===== Branch Check =====
                let branchcheck = branches?.find(
                    branch => branch.name.toLowerCase() === item.Branch.toLowerCase()
                );
                if (!branchcheck) {
                    alert(`Cannot find Branch: ${item.Branch}\nRow Number: ${rownum}\nColumn: Branch`);
                    $("#save_add").show();
                    return;
                }

                // ===== Employee Check =====
                let employeecheck = null;
                if (item.Employee) {
                    employeecheck = employees?.find(
                        employee => employee.last_name.toLowerCase() === item.Employee.toLowerCase()
                    );
                    if (!employeecheck) {
                        alert(`Cannot find Employee: ${item.Employee}\nRow Number: ${rownum}\nColumn: Employee`);
                        $("#save_add").show();
                        return;
                    }
                }

                // ===== Customer Check =====
                let customercheck = customers.find(
                    customer => customer.customer_name.toLowerCase() === item.Customer.toLowerCase()
                );
                if (!customercheck) {
                    alert(`Cannot find Customer: ${item.Customer}\nRow Number: ${rownum}\nColumn: Customer`);
                    $("#save_add").show();
                    return;
                }

                // ===== Invoice Grouping =====
                if (invoiceId !== item.InvoiceID) {
                    invoiceId = item.InvoiceID;
                    grandtotal = ttotalprice - parseFloat(saleDiscount) + tvat;
                    tdis += parseFloat(saleDiscount);

                    if (arrInvoice.length > 0) {
                        Object.assign(arrInvoice[arrInvoice.length - 1], {
                            total_discount_ammount: tdis,
                            total_vat_amnt: tvat,
                            total: ttotalprice,
                            grandTotal: grandtotal,
                            discount: parseFloat(saleDiscount)
                        });
                    }

                    arrInvoice.push({
                        invoiceId,
                        total_discount_ammount: 0,
                        total_vat_amnt: 0,
                        branch: branchcheck.id,
                        payment: paymentcheck.id,
                        customer_id: customercheck.customer_id,
                        employee_id: employeecheck?.id ?? null,
                        date: item.SaleDate,
                        details: item.Details,
                        incidenttype: incidentTypeId,
                        grandTotal: 0,
                        total: 0,
                        discount: 0,
                        type2: type2
                    });

                    tdis = 0;
                    tvat = 0;
                    ttotalprice = 0;
                    saleDiscount = item.SaleDiscount ? parseFloat(item.SaleDiscount.replace(/,/g, '')) : 0;
                    grandtotal = 0;
                }

                // ===== Item Calculation =====
                try {
                    if (!item.PriceVal) throw new Error("Price Val is empty");
                    if (!item.Qty) throw new Error("Qty is empty");

                    let price = parseFloat(item.Qty.replace(/,/g, '')) * parseFloat(item.PriceVal.replace(/,/g, ''));
                    let disc = item.Discount ? (price * parseFloat(item.Discount.replace(/,/g, ''))) / 100 : 0;
                    tdis += disc;

                    let totalprice = price - disc;
                    let vat = item.VAT ? (totalprice * parseFloat(item.VAT.replace(/,/g, ''))) / 100 : 0;
                    tvat += vat;
                    ttotalprice += totalprice;

                    if ([price, disc, vat].some(isNaN)) throw new Error("Invalid Price/Qty/Discount/VAT");

                    if (convertionratio != "") {
                        item.Qty = item.Qty / convertionratio
                    }

                    arrItem.push({
                        invoiceId: item.InvoiceID,
                        product: productcheck.id,
                        store: storecheck.id,
                        batch: batchcheck.id,
                        quantity: item.Qty,
                        product_rate: parseFloat(item.PriceVal.replace(/,/g, '')),
                        discount: parseFloat(item.Discount.replace(/,/g, '')) || 0,
                        discount_value: disc,
                        vat_percent: parseFloat(item.VAT.replace(/,/g, '')) || 0,
                        vat_value: vat,
                        total_price: totalprice,
                        total_discount: 0,
                        all_discount: 0,
                        unit:unitcheck.unit_id,
                        conversionid:conversionid

                    });
                } catch (err) {
                    alert(`${err.message}\nRow Number: ${rownum}`);
                    $("#save_add").show();
                    return;
                }

                rownum++;
            }

            // Finalize last invoice
            if (arrInvoice.length > 0) {
                grandtotal = ttotalprice - parseFloat(saleDiscount) + tvat;
                tdis += parseFloat(saleDiscount);
                Object.assign(arrInvoice[arrInvoice.length - 1], {
                    total_discount_ammount: tdis,
                    total_vat_amnt: tvat,
                    total: ttotalprice,
                    grandTotal: grandtotal,
                    discount: parseFloat(saleDiscount)
                });
            }

            console.log(arrItem);

            // ===== Save to server with async/await =====
            
            let saveResponse = await ajaxRequest($('#base_url').val() + 'invoice/invoice/save_sale2', {
                items: arrItem,
                invoice: arrInvoice
            });

            let datas = JSON.parse(saveResponse);
            $("#save_add").show();
            alert("Invoice Details saved Successfully");
            location.reload();
            
        } catch (error) {
            console.error(error);
            $("#save_add").show();
            alert("Error while uploading CSV");
        }
    }

    let type2 = '';
    $(document).ready(function() {
        if (usertype == 3) {
            // document.getElementById('style12').style.backgroundColor = '#E0E0E0';
            // const title = document.getElementById('title');
            // title.style.color = 'blue';
            type2 = "B"

        } else {
            type2 = "A"

        }

        var csrf_test_name = $('#CSRF_TOKEN').val();
        var base_url = $('#base_url').val();

        $('#stockdisposalnote').DataTable({
            responsive: true,

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": false,
                    "aTargets": [0, 1, 2, 3]
                },

            ],
            'processing': true,
            'serverSide': true,


            'lengthMenu': [
                [10, 25, 50, 100, 250, 500, 1000],
                [10, 25, 50, 100, 250, 500, 1000]
            ],

            dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
            buttons: [{
                extend: "copy",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want
                },
                className: "btn-sm prints"
            }, {
                extend: "csv",
                title: "Manage Sale",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                className: "btn-sm prints"
            }, {
                extend: "excel",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Manage Sale",
                className: "btn-sm prints"
            }, {
                extend: "pdf",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "Manage Sale",
                className: "btn-sm prints"
            }, {
                extend: "print",
                exportOptions: {
                    columns: [0, 1, 2, 3] //Your Colume value those you want print
                },
                title: "<center>Manage Sale</center>",
                className: "btn-sm prints"
            }],

            'serverMethod': 'post',
            'ajax': {
                'url': base_url + 'invoice/invoice/checkBulkUpload',
                data: {
                    csrf_test_name: csrf_test_name
                }
            },
            'columns': [{
                    data: 'sl'
                },

                {
                    data: 'uploaded_id'
                },
                {
                    data: 'date'
                },
                {
                    data: 'name'
                },
                {
                    data: 'button'
                },
            ],

        });
    });



    function exportToExcel(invoices) {
        if (confirm("Do you want to download this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'invoice/invoice/download_bulk',
                data: {
                    invoices: invoices.split(","),
                },
                success: function(data1) {


                    datas = JSON.parse(data1);

                    console.log(datas)


                    const ws = XLSX.utils.json_to_sheet(datas);

                    const wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");

                    XLSX.writeFile(wb, "data_export.csv");

                }
            });
        }

    }

    function deletesale(invoices, id) {
        if (confirm("Do you want to delete this record")) {
            $.ajax({
                type: "post",
                url: $('#base_url').val() + 'invoice/invoice/delete',
                data: {
                    id: id,
                    invoices: invoices.split(","),
                },
                success: function(data1) {

                    alert("Deleted successfully")
                    location.reload();
                }
            });
        }

    }
</script>