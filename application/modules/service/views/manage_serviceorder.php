<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading" id="style12">
				<div class="panel-title">
					<span id="title"><?php echo display('manage_serviceorder_invoice') ?></span>
					<span class="padding-lefttitle">
						<table>
							<tr>
								<td style="padding-left: 20px;">
									<button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
										<i class="ti-filter"></i> Filter
									</button>
								</td>
								<td style="padding-left: 5px;">
									<?php if ($this->permission1->method('manage_serviceorder_invoice', 'create')->access()) { ?>
										<a href="<?php echo base_url('serviceorder_invoice') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('serviceorder_invoice') ?> </a>
									<?php } ?>
								</td>
							</tr>
						</table>
					</span>
				</div>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="stockdisposalnote">
						<thead>
							<tr>
								<th><?php echo display('sl') ?></th>
								<th>Invoice No</th>
								<th>Customer</th>
								<th>Date</th>
								<th>Details</th>
								<th>Total Amount</th>
								<th>Status</th>
								<th><?php echo display('action') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<th colspan="5" class="text-right"><?php echo display('total') ?>:</th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<input type="hidden" id="total_product" value="<?php echo $total_product; ?>" name="">
	</div>
</div>

<div id="filterModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Filter Options</h4>
			</div>
			<div class="modal-body">
				<?php
				date_default_timezone_set('Asia/Colombo');
				$date = date('Y-m-d');
				?>
				<div class="form-group">
					<label>Branch</label>
					<select class="form-control" id="branch" name="branch" style="width: 400px;"></select>
				</div>
				<div class="form-group">
					<label>From Date</label>
					<input type="text" required class="form-control datepicker" name="fdate" value="<?php echo $date; ?>" id="fdate" />
				</div>
				<div class="form-group">
					<label>To Date</label>
					<input type="text" required class="form-control datepicker" name="tdate" value="<?php echo $date; ?>" id="tdate" />
				</div>
				<div class="form-group">
					<label>Password</label>
					<input type="password" class="form-control" id="password">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="filter()">Apply Filter</button>
			</div>
		</div>
	</div>
</div>

<?php
echo "<script>";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "let password_enable=" . json_encode($this->session->userdata('password_enable')) . ";";
echo "</script>";
?>
<script>
	let type2 = ""

	$(document).ready(function() {
		if (usertype == 3) {
			document.getElementById('style12').style.backgroundColor = '#E0E0E0';
			const title = document.getElementById('title');
			title.style.color = 'blue';
			type2 = "B"
		} else {
			type2 = "A"
		}

		getWholeData();
	});

	function filter() {
		$('#stockdisposalnote').DataTable().destroy();

		if (document.getElementById('fdate').value == '') {
			alert("From date shouldn't be empty")
			return
		}
		if (document.getElementById('branch').value == '' || !document.getElementById('branch').value) {
			alert("Branch shouldn't be empty")
			return
		}
		if (document.getElementById('tdate').value == '') {
			alert("To date shouldn't be empty")
			return
		}

		if (password_enable == "1") {
			if (document.getElementById('password').value == '') {
				alert("Password shouldn't be empty")
				return
			}
			$.ajax({
				url: $('#base_url').val() + 'dashboard/setting/checkpassword',
				type: 'POST',
				data: { password: document.getElementById('password').value },
				success: function(response) {
					var count = JSON.parse(response);
					if (count == 1) {
						document.getElementById('password').value = '';
						getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
						$('#filterModal').modal('hide');
					} else {
						alert("Wrong password")
					}
				},
				error: function(error) { console.log(error); }
			});
		} else {
			getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
			$('#filterModal').modal('hide');
		}
	}

	let branchId = 0;

	function getWholeData() {
		if ($.fn.DataTable.isDataTable('#stockdisposalnote')) {
			$('#stockdisposalnote').DataTable().clear().destroy();
		}

		var base_url = $('#base_url').val();

		$.ajax({
			type: "post",
			url: base_url + "store/store/getbranchbyuserid",
			success: function(data) {
				var branches = JSON.parse(data);
				var $branchDropdown = $('#branch');
				$branchDropdown.empty();
				$branchDropdown.append('<option value="" disabled selected>Select Branch</option>');

				$.each(branches, function(index, branch) {
					$branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
					if (branchId == 0 && branch.default != 0) {
						$branchDropdown.val(branch.id);
						branchId = branch.id;
					}
				});

				if (branchId > 0) {
					$branchDropdown.val(branchId);
				}

				if (password_enable == "0") {
					getInvoiceData(null, null);
				}
			}
		});
	}

	function getInvoiceData(fdate, tdate) {
		"use strict";
		var csrf_test_name = $('#CSRF_TOKEN').val();
		var base_url = $('#base_url').val();
		$('#stockdisposalnote').DataTable({
			responsive: true,
			"aaSorting": [[1, "desc"]],
			"columnDefs": [{ "bSortable": false, "aTargets": [0, 1, 2, 3, 4] }],
			processing: true,
			serverSide: true,
			lengthMenu: [[10, 25, 50, 100, 250, 500, 1000], [10, 25, 50, 100, 250, 500, 1000]],
			dom: "'<'col-sm-4'l><'col-sm-4 text-center'><'col-sm-4'>Bfrtip",
			buttons: [
				{ extend: "copy",  className: "btn-sm prints", exportOptions: { columns: [0,1,2,3,4,5,6] } },
				{ extend: "csv",   className: "btn-sm prints", title: "Manage Service Order", exportOptions: { columns: [0,1,2,3,4,5,6] } },
				{ extend: "excel", className: "btn-sm prints", title: "Manage Service Order", exportOptions: { columns: [0,1,2,3,4,5,6] } },
				{ extend: "pdf",   className: "btn-sm prints", title: "Manage Service Order", exportOptions: { columns: [0,1,2,3,4,5,6] } },
				{ extend: "print", className: "btn-sm prints", title: "<center>Manage Service Order</center>", exportOptions: { columns: [0,1,2,3,4,5,6] } }
			],
			serverMethod: 'post',
			ajax: {
				url: base_url + 'service/service/checkserviceorder',
				data: {
					csrf_test_name: csrf_test_name,
					type2: type2,
					branchid: document.getElementById("branch").value,
					fdate: fdate,
					tdate: tdate
				}
			},
			columns: [
				{ data: 'sl' },
				{ data: 'id' },
				{ data: 'customer_name' },
				{ data: 'date' },
				{ data: 'details' },
				{ data: 'grandTotal', class: "total_sale text-right", render: $.fn.dataTable.render.number(',', '.', 2) },
				{ data: 'status' },
				{ data: 'button' }
			],
			footerCallback: function(row, data, start, end, display) {
				var api = this.api();
				api.columns('.total_sale', { page: 'current' }).every(function() {
					var sum = this.data().reduce(function(a, b) {
						return (parseFloat(a) || 0) + (parseFloat(b) || 0);
					}, 0);
					$(this.footer()).html(' ' + sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
				});
			}
		});
	}

	function reprintInvoice(invoiceId) {
		if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
		if (confirm("Do you want to reprint this record")) {
			$.ajax({
				type: "post",
				url: $('#base_url').val() + 'service/service/service_order_print',
				data: { id: invoiceId },
				success: function(data1) {
					datas = JSON.parse(data1);
					printRawHtml(datas.details, datas.invoiceno);
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

	function printRawHtml(view, voucherNo) {
		if (voucherNo) {
			document.title = 'serviceorder_' + voucherNo + '_' + pdfDateStamp();
		}
		$(view).print({
			deferred: $.Deferred().done(function() {
				window.location.reload(true);
			})
		});
	}
</script>
