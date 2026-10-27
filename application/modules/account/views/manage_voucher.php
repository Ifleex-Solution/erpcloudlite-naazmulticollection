<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading" id="style12">
				<div class="panel-title">
					<span id="title"><?php echo $title; ?></span>
					<span class="padding-lefttitle">
						<table>
							<tr>
								<td style="padding-left: 20px;">
									<button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
										<i class="ti-filter"></i> Filter
									</button>
								</td>
								<td style="padding-left: 5px;">
									<?php if ($this->permission1->method('new_payment_voucher', 'create')->access()&& $type==1) { ?>
										<a href="<?php echo base_url('new_payment_voucher') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_payment_voucher') ?> </a>
									<?php } ?>
                                    <?php if ($this->permission1->method('new_receipt_voucher', 'create')->access()&& $type==2) { ?>
										<a href="<?php echo base_url('new_receipt_voucher') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_receipt_voucher') ?> </a>
									<?php } ?>
                                    <?php if ($this->permission1->method('new_contra_voucher', 'create')->access()&& $type==3) { ?>
										<a href="<?php echo base_url('new_contra_voucher') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('new_contra_voucher') ?> </a>
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
								<th>Voucher Id</th>
								<th>Date</th>
								<th><?php echo $from; ?></th>
								<th><?php echo $to; ?></th>
								<th>Amount</th>
								<th><?php echo display('action') ?>
								</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
							<th colspan="5" class="text-right"><?php echo display('total') ?>:</th>

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

		<!-- Modal content-->
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Filter Options</h4>
			</div>

			<div class="modal-body">

				<?php
				date_default_timezone_set('Asia/Colombo');

				$date = date('Y-m-d'); ?>

				<div class="form-group">
					<label>Branch</label>
					<select class="form-control " id="branch" name="branch" tabindex="3" style="width: 400px;">
					</select>

				</div>

				<div class="form-group">
					<label>From Date</label>
					<input type="text" required tabindex="2" class="form-control datepicker" name="fdate" value="<?php echo $date; ?>" id="fdate" />
				</div>

				<div class="form-group">
					<label>To Date</label>
					<input type="text" required tabindex="2" class="form-control datepicker" name="tdate" value="<?php echo $date; ?>" id="tdate" />
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
echo "let type=" . json_encode($type) . ";";
echo "</script>";
?>
<script>
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
		// if (usertype == 3) {
		// 	document.getElementById('style12').style.backgroundColor = '#E0E0E0';
		// 	const title = document.getElementById('title');
		// 	title.style.color = 'blue';
		// 	type2 = "B"

		// } else {
		// 	type2 = "A"

		// }

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
				data: {
					password: document.getElementById('password').value,
				},
				success: function(response) {
					var count = JSON.parse(response);
					if (count==1) {
						document.getElementById('password').value = ''
						getVoucherData(document.getElementById('fdate').value, document.getElementById('tdate').value);
						$('#filterModal').modal('hide');

					} else {
						alert("Wrong password")
						return
					}



				},
				error: function(error) {
					console.log(error);
				}
			});

		} else {
			getVoucherData(document.getElementById('fdate').value, document.getElementById('tdate').value);
			$('#filterModal').modal('hide');
			return

		}



	}

	
	let branchId = 0;

	function getBranchDropdown(branch) {
		branchId = branch
		getVoucherData(fdate, tdate)
	}

	function getWholeData() {

		if ($.fn.DataTable.isDataTable('#stockdisposalnote')) {
			$('#stockdisposalnote').DataTable().clear().destroy();
		}

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
				console.log(branches)
				var $branchDropdown = $('#branch');
				$branchDropdown.empty();
				$branchDropdown.append('<option value="" disabled selected>Select Branch</option>'); // Add default option

				$.each(branches, function(index, branch) {
					$branchDropdown.append('<option value="' + branch.id + '">' + branch.name + '</option>');
					if (branchId == 0) {
						if (branch.default != 0) {
							$branchDropdown.val(branch.id)
							branchId = branch.id;
						}
					}

				});

				if (branchId > 0) {
					{
						$branchDropdown.val(branchId)
					}
				}

				if (password_enable == "0"){
					getVoucherData(null, null)
				}

			}
		});
	}

	function reprintVoucher(id) {
		if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
		var base_url = $('#base_url').val();
		$.ajax({
			url: base_url + 'account/accounts/pos_print_voucher',
			type: 'POST',
			data: { id: id },
			success: function(response) {
				var datas = JSON.parse(response);
				printRawHtml(datas.details, datas.voucher_no, datas.type);
			}
		});
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

	function getVoucherData(fdate, tdate) {
		"use strict";
		var csrf_test_name = $('#CSRF_TOKEN').val();
		var base_url = $('#base_url').val();
		var total_product = $("#total_product").val();
		$('#stockdisposalnote').DataTable({
			responsive: true,

			"aaSorting": [
				[1, "desc"]
			],
			"columnDefs": [{
					"bSortable": false,
					"aTargets": [0, 1, 2, 3, 4]
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
					columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want
				},
				className: "btn-sm prints"
			}, {
				extend: "csv",
				title: "Manage Sale",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
				},
				className: "btn-sm prints"
			}, {
				extend: "excel",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
				},
				title: "Manage Sale",
				className: "btn-sm prints"
			}, {
				extend: "pdf",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
				},
				title: "Manage Sale",
				className: "btn-sm prints"
			}, {
				extend: "print",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5] //Your Colume value those you want print
				},
				title: "<center>Manage Sale</center>",
				className: "btn-sm prints"
			}],

			'serverMethod': 'post',
			'ajax': {
				'url': base_url + 'account/accounts/voucherList',
				data: {
					csrf_test_name: csrf_test_name,
					type: type,
					branchid: document.getElementById("branch").value,
					fdate: fdate,
					tdate: tdate,
					type2:type2

				}
			},
			'columns': [{
					data: 'sl'
				},

				{
					data: 'voucher_id'
				},
				{
					data: 'date'
				},
				{
					data: 'from'
				},
				{
					data: 'to'
				},
				// {
				// 	data: 'grandTotal'
				{
					data: 'total',
					class: "total_sale text-right",
					render: $.fn.dataTable.render.number(',', '.', 2)
				},

				// },
				{
					data: 'button'
				},
			],
			"footerCallback": function(row, data, start, end, display) {
				var api = this.api();
				api.columns('.total_sale', {
					page: 'current'
				}).every(function() {
					var sum = this
						.data()
						.reduce(function(a, b) {
							var x = parseFloat(a) || 0;
							var y = parseFloat(b) || 0;
							return x + y;
						}, 0);
					$(this.footer()).html(' ' + sum.toLocaleString(undefined, {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					}));
				});
			}

		});
	}
</script>