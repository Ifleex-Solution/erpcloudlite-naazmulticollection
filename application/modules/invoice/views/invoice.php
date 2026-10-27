<style>
/* === Panel card === */
.panel.panel-bd.lobidrag {
    border: none !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 8px 24px rgba(0,0,0,0.06) !important;
    border-radius: 14px !important;
    overflow: visible !important;
    margin-bottom: 24px !important;
}
.panel.panel-bd.lobidrag .panel-heading {
    background: #ffffff !important;
    padding: 12px 20px !important;
    border: none !important;
    border-bottom: 2px solid #F1F5F9 !important;
    border-radius: 14px 14px 0 0 !important;
    overflow: hidden !important;
}
.panel.panel-bd.lobidrag .panel-title {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: nowrap !important;
    gap: 10px !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .panel-title > span:first-child {
    color: #1E293B !important;
    font-size: 15px !important;
    font-weight: 600 !important;
    letter-spacing: 0.3px !important;
    white-space: nowrap !important;
}
/* === Header button area: flatten <table> wrapper into flex === */
.panel.panel-bd.lobidrag .padding-lefttitle,
.panel.panel-bd.lobidrag .padding-lefttitle table,
.panel.panel-bd.lobidrag .padding-lefttitle tbody,
.panel.panel-bd.lobidrag .padding-lefttitle tr {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    border: none !important;
    background: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle td {
    display: block !important;
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn {
    border-radius: 8px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 6px 14px !important;
    white-space: nowrap !important;
    margin: 0 !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary {
    background: linear-gradient(135deg, #3b6cf0, #8b5cf6, #e661f3) !important;
    border: none !important;
    color: #fff !important;
    letter-spacing: 0.3px !important;
    transition: background 0.15s, box-shadow 0.15s !important;
    text-decoration: none !important;
}
.panel.panel-bd.lobidrag .padding-lefttitle .btn.btn-primary:hover {
    background: linear-gradient(135deg, #3159c7, #7c4fe0, #d14fd6) !important;
    box-shadow: 0 4px 12px rgba(139,92,246,0.35) !important;
}
.panel.panel-bd.lobidrag .panel-body {
    background: #ffffff !important;
    padding: 20px 22px !important;
    border-radius: 0 0 14px 14px !important;
    overflow: hidden !important;
}
/* === Table header === */
#stockdisposalnote thead th {
    background: #F1F5F9 !important;
    color: #475569 !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.7px !important;
    border-bottom: 2px solid #DDD6FE !important;
    border-top: none !important;
    padding: 11px 14px !important;
    white-space: nowrap !important;
}
/* === Table rows === */
#stockdisposalnote tbody td {
    padding: 10px 14px !important;
    font-size: 13px !important;
    color: #374151 !important;
    border-color: #F3F0FE !important;
    vertical-align: middle !important;
    white-space: nowrap !important;
}
/* Invoice / Order No column — blue (td text + any anchor inside) */
#stockdisposalnote tbody td:nth-child(2),
#stockdisposalnote tbody td:nth-child(2) a {
    color: #8b5cf6 !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
}
/* Row hover */
#stockdisposalnote tbody tr:hover td {
    background: #F3F0FE !important;
}
/* === Horizontal scroll === */
.panel.panel-bd.lobidrag .panel-body .table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
}
#stockdisposalnote {
    width: auto !important;
    min-width: 100% !important;
}
/* === DataTable toolbar === */
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
.panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter {
    margin-bottom: 10px !important;
}
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons { margin-bottom: 8px !important; }
.panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn {
    border-radius: 6px !important;
    font-size: 12px !important;
    padding: 5px 12px !important;
}
/* === Status labels === */
.label-success { background:#16A34A !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
.label-danger  { background:#DC2626 !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
.label-warning { background:#F59E0B !important; color:#fff !important; padding:3px 9px !important; border-radius:4px !important; font-size:11px !important; font-weight:600 !important; display:inline-block !important; line-height:1.4 !important; border:none !important; }
/* === Mobile === */
@media (max-width: 767px) {
    .panel.panel-bd.lobidrag .panel-heading { padding: 10px 14px !important; }
    .panel.panel-bd.lobidrag .panel-title { flex-wrap: wrap !important; gap: 8px !important; }
    .panel.panel-bd.lobidrag .panel-body { padding: 10px 8px !important; }
    #stockdisposalnote thead th { font-size: 10px !important; padding: 8px 8px !important; }
    #stockdisposalnote tbody td { font-size: 12px !important; padding: 8px 8px !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_length,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_filter { width: 100% !important; text-align: left !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_info,
    .panel.panel-bd.lobidrag .dataTables_wrapper .dataTables_paginate { width: 100% !important; text-align: center !important; float: none !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons { width: 100% !important; }
    .panel.panel-bd.lobidrag .dataTables_wrapper .dt-buttons .btn { margin-bottom: 4px !important; }
}
</style>
<style>
#stockdisposalnote tbody td .btn,
#stockdisposalnote tbody td a.btn {
    padding: 2px 6px !important;
    font-size: 12px !important;
    line-height: 1.5 !important;
    border-radius: 3px !important;
    margin-left: 3px !important;
}
#stockdisposalnote tbody td .btn i,
#stockdisposalnote tbody td a.btn i {
    font-size: 11px !important;
}
@media(max-width:767px){
    #stockdisposalnote tbody td .btn,
    #stockdisposalnote tbody td a.btn {
        padding: 2px 5px !important;
        font-size: 11px !important;
        margin: 1px 1px !important;
    }
}
</style>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-bd lobidrag">
			<div class="panel-heading" id="style12">
				<div class="panel-title">
					<span id="title"><?php echo display('manage_invoice') ?></span>
					<span class="padding-lefttitle">
						<table>
							<tr>
								<td style="padding-left: 20px;">
									<button class="btn btn-success m-b-5 m-r-2" data-toggle="modal" data-target="#filterModal">
										<i class="ti-filter"></i> Filter
									</button>
								</td>
								<td style="padding-left: 5px;">
									<?php if ($this->permission1->method('manage_invoice', 'create')->access()) { ?>
										<a href="<?php echo base_url('add_invoice') ?>" class="btn btn-primary m-b-5 m-r-2"><i class="ti-plus"> </i> <?php echo display('add_invoice') ?> </a>
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
								<th>Payment Type</th>
								<th>Details</th>
								<th>Incident Type</th>
								<th>Total Amount</th>
								<th><?php echo display('action') ?>
								</th>
							</tr>
						</thead>
						<tbody>

						</tbody>
						<tfoot>
							<th colspan="7" class="text-right"><?php echo display('total') ?>:</th>

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
				data: {
					password: document.getElementById('password').value,
				},
				success: function(response) {
					var count = JSON.parse(response);
					if (count==1) {
						document.getElementById('password').value = ''
						getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
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
			getInvoiceData(document.getElementById('fdate').value, document.getElementById('tdate').value);
			$('#filterModal').modal('hide');
			return

		}



	}

	function cancelInvoiceRow(invoiceId) {
		if (!confirm("This will permanently zero out this invoice's amounts, cancel it, and it can no longer be edited, deleted, or reprinted. This cannot be undone. Continue?")) {
			return;
		}
		$.ajax({
			type: "post",
			url: $('#base_url').val() + 'invoice/invoice/cancel_sale',
			data: {
				id: invoiceId,
				csrf_test_name: $('#CSRF_TOKEN').val()
			},
			success: function(resp) {
				var data = JSON.parse(resp);
				alert(data.message);
				if (data.status === 'success' && $.fn.DataTable.isDataTable('#stockdisposalnote')) {
					$('#stockdisposalnote').DataTable().ajax.reload(null, false);
				}
			},
			error: function() {
				alert('Something went wrong while cancelling this invoice');
			}
		});
	}

	function reprintInvoice(invoiceId) {
		if (type2 === "B") alert("You are using the TESTING ENVIRONMENT, but it is connected to the live database. Printing or executing this transaction may modify actual inventory counts. Testing materials and printouts are confidential and must remain within the organisation. Do not share them with external parties.");  /* __testing_guard_added__ */
		if (confirm("Do you want to reprint this record")) {
			$.ajax({
				type: "post",
				url: $('#base_url').val() + 'invoice/invoice/pos_print',
				data: {
					id: invoiceId,

				},
				success: function(data1) {
					datas = JSON.parse(data1);
					if (datas.error) {
						alert(datas.error);
						return;
					}
					printRawHtml(datas.details, datas.invoiceno);

				}
			});
		}

	}
	let branchId = 0;

	function getBranchDropdown(branch) {
		branchId = branch
		getInvoiceData(fdate, tdate)
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
					getInvoiceData(null, null)
				}

			}
		});
	}

	function getInvoiceData(fdate, tdate) {
		"use strict";
		var csrf_test_name = $('#CSRF_TOKEN').val();
		var base_url = $('#base_url').val();
		var total_product = $("#total_product").val();
		$('#stockdisposalnote').DataTable({
			responsive: false,

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
					columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want
				},
				className: "btn-sm prints"
			}, {
				extend: "csv",
				title: "Manage Sale",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
				},
				className: "btn-sm prints"
			}, {
				extend: "excel",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
				},
				title: "Manage Sale",
				className: "btn-sm prints"
			}, {
				extend: "pdf",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
				},
				title: "Manage Sale",
				className: "btn-sm prints"
			}, {
				extend: "print",
				exportOptions: {
					columns: [0, 1, 2, 3, 4, 5, 6] //Your Colume value those you want print
				},
				title: "<center>Manage Sale</center>",
				className: "btn-sm prints"
			}],

			'serverMethod': 'post',
			'ajax': {
				'url': base_url + 'invoice/invoice/checksales',
				data: {
					csrf_test_name: csrf_test_name,
					type2: type2,
					branchid: document.getElementById("branch").value,
					fdate: fdate,
					tdate: tdate

				}
			},
			'columns': [{
					data: 'sl'
				},

				{
					data: 'id'
				},
				{
					data: 'customer_name'
				},
				{
					data: 'date'
				},
				{
					data: 'paymenttype'
				},
				{
					data: 'details'
				},
				{
					data: 'incidenttype'
				},
				// {
				// 	data: 'grandTotal'
				{
					data: 'grandTotal',
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


	function pdfDateStamp() {
		var d = new Date();
		var dd = String(d.getDate()).padStart(2, '0');
		var mm = String(d.getMonth() + 1).padStart(2, '0');
		return dd + mm + d.getFullYear();
	}

	function printRawHtml(view, voucherNo) {
		if (voucherNo) {
			document.title = 'salesinvoice_' + voucherNo + '_' + pdfDateStamp();
		}
		$(view).print({

			deferred: $.Deferred().done(function() {
				window.location.reload(true);
			})
		});
	}

	// Fire Pusher only after user confirms delete (inline onclick sets isDefaultPrevented if cancelled)
	$(document).on('click', 'a[href*="delete_sale"]', function(e) {
		if (e.isDefaultPrevented()) return; // user clicked Cancel — stop here
		e.preventDefault();
		var href = this.href;
		fetch('https://fexten.com/fexten_cloud_hub/modules/pusher/trigger.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				channel: 'notifications',
				event:   'record.saved',
				data:    { module: 'Sale', action: 'deleted' }
			})
		})
		.catch(function() {})
		.finally(function() {
			window.location.href = href;
		});
	});
</script>