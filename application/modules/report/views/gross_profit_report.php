<!-- Gross Profit report -->
<style>
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear { display: none; }
    #btn-filter { position:relative; min-width:145px; transition:opacity .2s; }
    #btn-filter.btn-loading { color:transparent !important; pointer-events:none; opacity:.85; }
    #btn-filter.btn-loading::after { content:''; position:absolute; top:50%; left:50%; width:20px; height:20px; border-radius:50%; background:conic-gradient(#fff 0deg,rgba(255,255,255,.6) 100deg,rgba(255,255,255,.15) 200deg,transparent 300deg); -webkit-mask:radial-gradient(farthest-side,transparent calc(100% - 3px),#000 calc(100% - 3px)); mask:radial-gradient(farthest-side,transparent calc(100% - 3px),#000 calc(100% - 3px)); animation:btn_spin .75s linear infinite; }
    @keyframes btn_spin { from{transform:translate(-50%,-50%) rotate(0deg)} to{transform:translate(-50%,-50%) rotate(360deg)} }
    .panel.panel-bd.lobidrag { border:none !important; box-shadow:0 2px 8px rgba(0,0,0,.06),0 8px 24px rgba(0,0,0,.06) !important; border-radius:14px !important; overflow:hidden !important; }
    .panel.panel-bd.lobidrag .panel-heading { background:#fff !important; padding:14px 24px !important; border:none !important; border-bottom:2px solid #F1F5F9 !important; }
    .panel.panel-bd.lobidrag .panel-title { display:flex !important; align-items:center !important; justify-content:space-between !important; flex-wrap:wrap !important; gap:10px !important; margin:0 !important; }
    .panel.panel-bd.lobidrag .panel-body { padding:20px !important; background:#fff !important; margin-left:0 !important; }
    .panel.panel-bd.lobidrag .form-group { margin-bottom:16px !important; max-width:280px; margin-left:20px; }
    .panel.panel-bd.lobidrag label { font-size:13px !important; font-weight:600 !important; color:#374151 !important; display:block; margin-bottom:4px !important; }
    .panel.panel-bd.lobidrag .form-control { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; padding:8px 12px !important; font-size:13px !important; color:#374151 !important; background:#F8FAFC !important; height:auto !important; transition:border-color .16s,box-shadow .16s,background .16s !important; }
    .panel.panel-bd.lobidrag .form-control:focus { border-color:#8b5cf6 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; outline:none !important; }
    .panel.panel-bd.lobidrag .btn.btn-success { background:linear-gradient(135deg,#3b6cf0,#8b5cf6,#e661f3) !important; border:none !important; border-radius:8px !important; padding:9px 24px !important; font-size:13px !important; font-weight:600 !important; color:#fff !important; letter-spacing:.3px !important; transition:background .16s,box-shadow .16s !important; }
    .panel.panel-bd.lobidrag .btn.btn-success:hover { background:linear-gradient(135deg,#3159c7,#7c4fe0,#d14fd6) !important; box-shadow:0 4px 12px rgba(139,92,246,.35) !important; }
    .select2-container .select2-selection--single { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; background:#F8FAFC !important; height:38px !important; }
    .select2-container .select2-selection--single .select2-selection__rendered { color:#374151 !important; font-size:13px !important; line-height:36px !important; padding-left:10px !important; }
    .select2-container .select2-selection--single .select2-selection__arrow { height:36px !important; }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single { border-color:#8b5cf6 !important; background:#fff !important; box-shadow:0 0 0 3px rgba(139,92,246,.12) !important; }
    .select2-dropdown { border:1.5px solid #E2E8F0 !important; border-radius:8px !important; box-shadow:0 4px 16px rgba(0,0,0,.10) !important; margin-top:2px !important; }
    .select2-search--dropdown .select2-search__field { border:1.5px solid #E2E8F0 !important; border-radius:6px !important; font-size:13px !important; padding:5px 8px !important; }
    .select2-results__option { font-size:13px !important; padding:7px 12px !important; color:#374151 !important; }
    .select2-container--default .select2-results__option--highlighted[aria-selected] { background:#8b5cf6 !important; color:#fff !important; }
    .select2-container--default .select2-results__option[aria-selected=true] { background:#F3F0FE !important; color:#8b5cf6 !important; }
    .panel.panel-bd.lobidrag .panel-body .input-group,
    .panel.panel-bd.lobidrag .panel-body .form-control { width:100% !important; max-width:100% !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] { display:flex !important; flex-direction:row !important; flex-wrap:wrap !important; gap:14px 20px !important; max-width:600px !important; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div { flex:1 1 130px; max-width:200px; }
    .panel.panel-bd.lobidrag .panel-body > .form-group[style*="margin-bottom"] { max-width:100% !important; }
    .report-btn-row { margin-left:20px; margin-top:8px; }
    @media (max-width:576px) {
        .panel.panel-bd.lobidrag .panel-body { padding:16px !important; }
        .panel.panel-bd.lobidrag .panel-body > .form-group { max-width:100% !important; margin-left:0 !important; }
        .panel.panel-bd.lobidrag .panel-body > .form-group[style*="flex"] > div { max-width:100% !important; }
        .report-btn-row { margin-left:0; }
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4><?php
                        echo $title;
                        ?></h4>
                </div>
            </div>
            <br />
            <div class="panel-body">


                <?php
                date_default_timezone_set('Asia/Colombo');

                $today = date('Y-m-d');
                ?>
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
                <div class="form-group">
                    <label for="empid" class="mr-2 mb-0">Password</label>
                    <input type="password" tabindex="4" class="form-control" name="password" id="password" value="" style="width: 200px;" autocomplete="off">
                </div>

                <div class="report-btn-row">
                    <button type="button" id="btn-filter" class="btn btn-success" onclick="onFilterButtonClick()">
                        Generate Report
                    </button>
                </div>
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
    let type2 = '';
    let type = "";

    $(document).ready(function() {
        if (usertype == 3) {
            type2 = "B"

        } else {
            type2 = "A"

        }
    });



    function onFilterButtonClick() {

        type = type2;
        if (password_enable == "1") {

            if (document.getElementById('password').value == '') {
                alert("Password shouldn't be empty")
                return

            }

            $.ajax({
                url: $('#base_url').val() + 'dashboard/setting/checkpasswordReport',
                type: 'POST',
                data: {
                    password: document.getElementById('password').value,
                },
                success: function(response) {
                    if (JSON.parse(response) == "wrong password") {
                        alert("Wrong Password")
                        return
                    }

                    if (type == "A") {
                        if (JSON.parse(response) != "A") {
                            alert("Wrong Password")
                            return

                        }
                    }

                    var passwordtype = JSON.parse(response);
                    type = passwordtype;

                    generateReport()





                },
                error: function(error) {
                    console.log(error);
                }
            });

        } else {
            generateReport()
        }
    }

    function generateReport() {
        $.ajax({
            type: "post",
            url: $('#baseUrl2').val() + 'report/report/gross_profit_report',
            data: {
                from_date: $('#from_date').val(),
                to_date: document.getElementById('single_date_checkbox').checked ? $('#from_date').val() : $('#to_date').val(),
                empid: type,
                istype: document.getElementById('single_date_checkbox').checked,

            },
            success: function(data1) {
                datas = JSON.parse(data1);
                console.log(datas)
                window.open(`generate_gross_profit_report`, '_blank');
                // if (datas.length>0) {
                //     window.open(`generate_gross_profit_report`, '_blank');

                // } else {
                //     alert("There is no data available for the selected parameters.")
                // }


            }
        });
    }
</script>
<script>
    document.getElementById('single_date_checkbox').addEventListener('change', function() {
        let fromDate = document.getElementById('from_date');
        let toDate = document.getElementById('to_date');
        let toDateContainer = document.getElementById('to_date_container');
        if (this.checked) {
            toDate.value = fromDate.value;
            toDateContainer.style.display = 'none';
        } else {
            toDateContainer.style.display = 'block';
        }
    });
</script>