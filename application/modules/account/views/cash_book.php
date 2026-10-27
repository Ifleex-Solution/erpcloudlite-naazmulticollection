<!-- Cash Book -->
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    /* ── Panel redesign — matches Sale screen ── */
    .panel.panel-bd.lobidrag{border:none !important;box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important;border-radius:14px !important;overflow:hidden !important}
    .panel.panel-bd.lobidrag .panel-heading{background:#fff !important;padding:14px 24px !important;border:none !important;border-bottom:2px solid #F1F5F9 !important}
    .panel.panel-bd.lobidrag .panel-title{margin:0 !important}
    .panel.panel-bd.lobidrag .panel-title h4{color:#1E293B !important;font-size:15px !important;font-weight:600 !important;letter-spacing:.3px !important;margin:0 !important}
    .panel.panel-bd.lobidrag .panel-body{padding:28px 32px !important;background:#fff !important}
    .panel.panel-bd.lobidrag label{font-size:13px !important;font-weight:600 !important;color:#374151 !important}
    .panel.panel-bd.lobidrag .btn.btn-success{background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important;border:none !important;border-radius:8px !important;padding:9px 24px !important;font-size:13px !important;font-weight:600 !important;color:#fff !important;letter-spacing:.3px !important;transition:background .16s,box-shadow .16s !important}
    .panel.panel-bd.lobidrag .btn.btn-success:hover{background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important;box-shadow:0 4px 12px rgba(139,92,246,.35) !important}

    /* ── Input redesign — matches Sale screen ── */
    input.form-control {
        border: 1px solid #E2E8F0 !important;
        border-radius: 6px !important;
        color: #374151 !important;
        transition: border-color .18s, box-shadow .18s !important;
    }
    input.form-control:focus {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 0 3px rgba(139,92,246,.12) !important;
        outline: none !important;
    }

    /* ── Select2 dropdown redesign — matches Sale screen ── */
    .select2-container .select2-selection--single,
    .select2-container--default .select2-selection--single {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        background: #F8FAFC !important;
        height: 34px !important;
    }
    .select2-container .select2-selection--single .select2-selection__rendered {
        color: #374151 !important;
        font-size: 13px !important;
        line-height: 32px !important;
        padding-left: 10px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .select2-container .select2-selection--single .select2-selection__arrow b {
        border-color: #64748B transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
        margin-top: -1px !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #16A34A transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #16A34A !important;
        background: #fff !important;
        box-shadow: 0 0 0 3px rgba(22,163,74,.12) !important;
        outline: none !important;
    }
    .select2-selection__placeholder { color: #94A3B8 !important; }
    .select2-dropdown {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 16px rgba(0,0,0,.10) !important;
        margin-top: 2px !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1.5px solid #E2E8F0 !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        padding: 5px 8px !important;
    }
    .select2-results__option {
        font-size: 13px !important;
        padding: 7px 12px !important;
        color: #374151 !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background: #16A34A !important;
        color: #fff !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #F0FDF4 !important;
        color: #16A34A !important;
    }

    /* ── Responsiveness ── */
    .panel.panel-bd.lobidrag .panel-body .input-group,
    .panel.panel-bd.lobidrag .panel-body .form-control,
    .panel.panel-bd.lobidrag .panel-body select.form-control + .select2-container {
        width: 100% !important;
        max-width: 280px !important;
    }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 14px 20px !important;
        max-width: 600px !important;
    }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div {
        flex: 1 1 130px;
        max-width: 200px;
    }
    @media(max-width:767px){
        .panel.panel-bd.lobidrag .panel-body{padding:16px !important}
        .panel.panel-bd.lobidrag .panel-body .input-group,
        .panel.panel-bd.lobidrag .panel-body .form-control,
        .panel.panel-bd.lobidrag .panel-body select.form-control + .select2-container {
            max-width: 100% !important;
        }
        .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] {
            max-width: 100% !important;
        }
        .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div {
            flex: 1 1 100%;
            max-width: 100% !important;
        }
        .panel.panel-bd.lobidrag .btn.btn-success{width:100% !important;margin:6px 0 0 0 !important}
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php echo $title; ?></h4>
                </div>
            </div>
            <br />
            <div class="panel-body">

                <?php
                date_default_timezone_set('Asia/Colombo');
                $today = date('Y-m-d');
                ?>

                <!-- 1. Branch -->
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="branch" name="branch" style="width: 250px;" tabindex="1">
                        </select>
                    </div>
                </div>

                <!-- 2. Payment Nature -->
                <div class="form-group">
                    <label for="payment_nature">Payment Nature</label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select class="form-control" id="payment_nature" name="payment_nature" style="width: 250px;" tabindex="2" onchange="onNatureChange()">
                            <option value="">All Nature</option>
                            <option value="Cash Nature">Cash Nature</option>
                            <option value="Bank Nature">Bank Nature</option>
                        </select>
                    </div>
                </div>

                <!-- 3. Payment Type (filtered by Nature) -->
                <div class="form-group">
                    <label for="your_dropdown_id"><?php echo display('payment_type'); ?></label>
                    <div class="input-group mr-4" style="width: 250px;">
                        <select name="multipaytype[]" class="form-control" id="your_dropdown_id" style="width: 250px;" tabindex="3">
                            <option value="">All Payment Types</option>
                            <?php foreach ($all_pmethod as $services) { ?>
                                <option value="<?php echo $services['id']; ?>" data-nature="<?php echo htmlspecialchars($services['nature'] ?? ''); ?>"><?php echo $services['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- 4. Date Range -->
                <div class="form-group" style="margin-bottom: 10px;">
                    <input type="checkbox" id="single_date_checkbox" name="single_date_checkbox">
                    <label for="single_date_checkbox">Single Date</label>
                </div>
                <div class="form-group" style="display: flex; gap: 20px;">
                    <div>
                        <label for="from_date">From Date: </label>
                        <input type="text" name="from_date" class="form-control datepicker" id="from_date"
                            placeholder="<?php echo display('start_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                    </div>
                    <div id="to_date_container">
                        <div>
                            <label for="to_date">To Date:</label>
                            <input type="text" name="to_date" class="form-control datepicker" id="to_date"
                                placeholder="<?php echo display('end_date') ?>" value="<?php echo $today ?>" style="width: 200px;">
                        </div>
                    </div>
                </div>

                <!-- 5. Password -->
                <div class="form-group">
                    <label for="password" class="mr-2 mb-0">Password</label>
                    <input type="password" tabindex="4" class="form-control" name="password" id="password" value="" style="width: 200px;" autocomplete="off">
                </div>

                <button type="button" id="btn-filter" class="btn btn-success" onclick="onFilterButtonClick()">
                    Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<input type="hidden" name="baseUrl2" id="baseUrl2" class="baseUrl" value="<?php echo base_url(); ?>" />
<?php
echo "<script>";
echo "let password_enable=" . json_encode($this->session->userdata('password_enable')) . ";";
echo "let usertype=" . json_encode($this->session->userdata('user_level2')) . ";";
echo "</script>";
?>
<script src="<?php echo base_url('my-assets/js/admin_js/sales_report.js') ?>" type="text/javascript"></script>
<script>
    var allPaymentMethods = <?php echo json_encode(array_map(function($s) {
        return ['id' => $s['id'], 'name' => $s['name'], 'nature' => $s['nature'] ?? ''];
    }, $all_pmethod ?: [])); ?>;

    let type2 = '';
    let type  = "";

    $(document).ready(function() {
        $('select.form-control').select2({ placeholder: 'Select option', allowClear: true });
        getBranchDropdown(0);
        type2 = (usertype == 3) ? "B" : "A";
    });

    function getBranchDropdown(branchId) {
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
                    if (branch.default != 0) {
                        $branchDropdown.val(branch.id).trigger('change');
                    }
                });
                if (branchId > 0) {
                    $branchDropdown.val(branchId).trigger('change');
                }
            }
        });
    }

    function onNatureChange() {
        var selectedNature = $('#payment_nature').val();
        var $payType = $('#your_dropdown_id');
        $payType.empty();
        $payType.append('<option value="">All Payment Types</option>');
        allPaymentMethods.forEach(function(pm) {
            if (!selectedNature || pm.nature === selectedNature) {
                $payType.append('<option value="' + pm.id + '">' + pm.name + '</option>');
            }
        });
        $payType.val('').trigger('change');
    }

    function onFilterButtonClick() {
        type = type2;
        if (password_enable == "1") {
            if (document.getElementById('password').value == '') {
                alert("Password shouldn't be empty");
                return;
            }
            $.ajax({
                url: $('#base_url').val() + 'dashboard/setting/checkpasswordReport',
                type: 'POST',
                data: { password: document.getElementById('password').value },
                success: function(response) {
                    if (JSON.parse(response) == "wrong password") {
                        alert("Wrong Password");
                        return;
                    }
                    if (type == "A" && JSON.parse(response) != "A") {
                        alert("Wrong Password");
                        return;
                    }
                    type = JSON.parse(response);
                    generateReport();
                },
                error: function(error) { console.log(error); }
            });
        } else {
            generateReport();
        }
    }

    function generateReport() {
        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/cashbook_reportdata',
            data: {
                from_date: $('#from_date').val(),
                to_date: document.getElementById('single_date_checkbox').checked ? $('#from_date').val() : $('#to_date').val(),
                empid: type,
                istype: document.getElementById('single_date_checkbox').checked,
                payment: $('#your_dropdown_id').val(),
                payment_nature: $('#payment_nature').val(),
                branch: $('#branch').val()
            },
            success: function(data1) {
                datas = JSON.parse(data1);
                if (datas.length != 0) {
                    window.open('generate_cashbook', '_blank');
                } else {
                    alert("There is no data available for the selected parameters.");
                }
            }
        });
    }
</script>
<script>
    document.getElementById('single_date_checkbox').addEventListener('change', function() {
        let toDate          = document.getElementById('to_date');
        let toDateContainer = document.getElementById('to_date_container');
        if (this.checked) {
            toDate.value = document.getElementById('from_date').value;
            toDateContainer.style.display = 'none';
        } else {
            toDateContainer.style.display = 'block';
        }
    });
</script>
