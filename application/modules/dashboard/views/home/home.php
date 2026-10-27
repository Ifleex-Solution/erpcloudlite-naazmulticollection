<?php
if (isset($_POST['btnSearch'])) {
    $postdate = $_POST['alldata'];
}
$searchdate = (!empty($postdate) ? $postdate : date('F Y'));
?>

<style>
/* ── Dashboard shell ─────────────────────────────────────── */
.db { padding: 4px 0 24px; }

/* ── KPI cards ───────────────────────────────────────────── */
.db-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.05);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: transform .18s, box-shadow .18s;
    margin-bottom: 20px;
    height: calc(100% - 20px);
}
.db-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 24px rgba(0,0,0,.11);
}
.db-card-body {
    display: flex;
    align-items: center;
    padding: 22px 22px 18px;
    gap: 16px;
    flex: 1;
}
.db-card-icon {
    width: 56px; height: 56px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    color: #fff;
    flex-shrink: 0;
}
.db-card-icon.blue   { background: linear-gradient(135deg,#2563EB,#60A5FA); }
.db-card-icon.teal   { background: linear-gradient(135deg,#0D9488,#34D399); }
.db-card-icon.violet { background: linear-gradient(135deg,#7C3AED,#A78BFA); }
.db-card-icon.amber  { background: linear-gradient(135deg,#D97706,#FCD34D); }

.db-card-info { flex: 1; min-width: 0; }
.db-card-num {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -1px;
    color: #0F172A;
    margin-bottom: 5px;
}
.db-card-label {
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: .55px;
    text-transform: uppercase;
    color: #64748B;
}
.db-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 22px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none !important;
    border-top: 1px solid #F1F5F9;
    transition: background .14s;
}
.db-card-footer:hover { background: #F8FAFC; }
.db-card-footer.blue   { color: #2563EB; }
.db-card-footer.teal   { color: #0D9488; }
.db-card-footer.violet { color: #7C3AED; }
.db-card-footer.amber  { color: #D97706; }
.db-card-footer .arrow { font-size: 16px; line-height: 1; }

/* colored left accent strip */
.db-card.blue   { border-left: 4px solid #2563EB; }
.db-card.teal   { border-left: 4px solid #0D9488; }
.db-card.violet { border-left: 4px solid #7C3AED; }
.db-card.amber  { border-left: 4px solid #D97706; }

/* ── Chart panels ────────────────────────────────────────── */
.db-panel {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.05);
    overflow: hidden;
    margin-bottom: 20px;
    height: calc(100% - 20px);
    display: flex;
    flex-direction: column;
}
.db-panel-head {
    padding: 16px 22px 14px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.db-panel-title {
    font-size: 14px;
    font-weight: 700;
    color: #0F172A;
    margin: 0;
    letter-spacing: -.1px;
}
.db-panel-subtitle {
    font-size: 11.5px;
    color: #94A3B8;
    font-weight: 500;
    margin-top: 2px;
}
.db-panel-body {
    padding: 18px 20px;
    flex: 1;
}
.db-panel-body.no-pad { padding: 0; }

.db-see-all {
    font-size: 12px;
    font-weight: 600;
    padding: 5px 13px;
    border-radius: 20px;
    background: #EFF6FF;
    color: #2563EB;
    text-decoration: none;
    border: none;
    transition: background .14s;
    white-space: nowrap;
}
.db-see-all:hover { background: #DBEAFE; color: #1D4ED8; }

/* Pie filter row */
.db-filter-row {
    display: flex;
    align-items: center;
    gap: 6px;
}
.db-filter-row .form-control {
    font-size: 12.5px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #E2E8F0;
    padding: 0 10px;
    width: 148px;
}
.db-filter-btn {
    height: 32px;
    padding: 0 12px;
    font-size: 12px;
    font-weight: 600;
    background: #2563EB;
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background .14s;
    white-space: nowrap;
}
.db-filter-btn:hover { background: #1D4ED8; }

/* ── Today's sales table ─────────────────────────────────── */
.db-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.db-table thead th {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
    color: #94A3B8;
    padding: 10px 16px;
    border-bottom: 1px solid #F1F5F9;
    background: #FAFBFD;
    white-space: nowrap;
}
.db-table tbody tr {
    border-bottom: 1px solid #F8FAFC;
    transition: background .1s;
}
.db-table tbody tr:last-child { border-bottom: none; }
.db-table tbody tr:hover { background: #F8FAFC; }
.db-table tbody td {
    padding: 11px 16px;
    color: #334155;
    vertical-align: middle;
}
.db-table tfoot td {
    padding: 12px 16px;
    font-weight: 700;
    font-size: 13px;
    color: #0F172A;
    border-top: 2px solid #DDD6FE;
    background: #FAFBFD;
}
.db-invoice-link {
    color: #2563EB;
    font-weight: 600;
    text-decoration: none;
}
.db-invoice-link:hover { color: #1D4ED8; text-decoration: underline; }

.db-empty {
    text-align: center;
    padding: 32px;
    color: #94A3B8;
    font-size: 13px;
}
.db-empty i { font-size: 28px; display: block; margin-bottom: 8px; opacity: .4; }

/* canvas containers */
#chartContainer { height: 260px; }
#lineChart { width: 100% !important; height: 100% !important; }
#yearlyreport { width: 100% !important; }

/* section spacing */
.db-section { margin-bottom: 6px; }
</style>

<div class="db">

    <!-- ── KPI ROW ──────────────────────────────────────────── -->
    <div class="row db-section">

        <!-- Customers -->
        <div class="col-xs-12 col-sm-6 col-lg-3">
            <div class="db-card blue">
                <div class="db-card-body">
                    <div>
                        <div class="db-card-num">
                            <span class="count-number"><?php echo html_escape($total_customer) ?></span>
                        </div>
                        <div class="db-card-label"><?php echo display('total_customer') ?></div>
                    </div>
                    <div style="flex:1"></div>
                    <div class="db-card-icon blue"><i class="fa fa-users"></i></div>
                </div>
                <?php if ($this->permission1->method('manage_customer', 'read')->access()): ?>
                    <a href="<?php echo base_url('customer_list') ?>" class="db-card-footer blue">
                        <span>View Customers</span>                    </a>
                <?php else: ?>
                    <span class="db-card-footer blue" style="cursor:default">
                        <span>Customers</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Products -->
        <div class="col-xs-12 col-sm-6 col-lg-3">
            <div class="db-card teal">
                <div class="db-card-body">
                    <div>
                        <div class="db-card-num">
                            <span class="count-number"><?php echo html_escape($total_product) ?></span>
                        </div>
                        <div class="db-card-label"><?php echo display('total_product') ?></div>
                    </div>
                    <div style="flex:1"></div>
                    <div class="db-card-icon teal"><i class="fa fa-shopping-bag"></i></div>
                </div>
                <?php if ($this->permission1->method('manage_product', 'read')->access()): ?>
                    <a href="<?php echo base_url('product_list') ?>" class="db-card-footer teal">
                        <span>View Products</span>                    </a>
                <?php else: ?>
                    <span class="db-card-footer teal" style="cursor:default">
                        <span>Products</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Suppliers -->
        <div class="col-xs-12 col-sm-6 col-lg-3">
            <div class="db-card violet">
                <div class="db-card-body">
                    <div>
                        <div class="db-card-num">
                            <span class="count-number"><?php echo html_escape($total_suppliers) ?></span>
                        </div>
                        <div class="db-card-label"><?php echo display('total_supplier') ?></div>
                    </div>
                    <div style="flex:1"></div>
                    <div class="db-card-icon violet"><i class="fa fa-user"></i></div>
                </div>
                <?php if ($this->permission1->method('manage_supplier', 'read')->access()): ?>
                    <a href="<?php echo base_url('supplier_list') ?>" class="db-card-footer violet">
                        <span>View Suppliers</span>                    </a>
                <?php else: ?>
                    <span class="db-card-footer violet" style="cursor:default">
                        <span>Suppliers</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Today's Sales -->
        <div class="col-xs-12 col-sm-6 col-lg-3">
            <div class="db-card amber">
                <div class="db-card-body">
                    <div>
                        <div class="db-card-num">
                            <span class="count-number"><?php echo html_escape($total_sales) ?></span>
                        </div>
                        <div class="db-card-label"><?php echo display('total_invoice') ?> Today</div>
                    </div>
                    <div style="flex:1"></div>
                    <div class="db-card-icon amber"><i class="fa fa-money"></i></div>
                </div>
                <?php if ($this->permission1->method('manage_invoice', 'read')->access()): ?>
                    <a href="<?php echo base_url('invoice_list') ?>" class="db-card-footer amber">
                        <span>View Invoices</span>                    </a>
                <?php else: ?>
                    <span class="db-card-footer amber" style="cursor:default">
                        <span>Invoices</span>
                    </span>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- /KPI ROW -->

    <?php if (!($this->session->userdata('user_level2') == 3 && $this->session->userdata('password_enable') == "1")): ?>

    <!-- ── CHARTS ROW ────────────────────────────────────────── -->
    <div class="row db-section" style="display:flex;flex-wrap:wrap;align-items:stretch;">

        <!-- Bar Chart -->
        <div class="col-sm-12 col-md-7" style="display:flex;flex-direction:column;">
            <div class="db-panel" style="flex:1;">
                <div class="db-panel-head">
                    <div>
                        <div class="db-panel-title">Top Selling Products</div>
                        <div class="db-panel-subtitle">Trend Analysis — Bar Chart</div>
                    </div>
                    <a href="<?php echo base_url(); ?>dashboard/home/see_all_best_sales" class="db-see-all">See All</a>
                </div>
                <div class="db-panel-body" style="height:260px;position:relative;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-sm-12 col-md-5" style="display:flex;flex-direction:column;">
            <div class="db-panel" style="flex:1;">
                <div class="db-panel-head">
                    <div>
                        <div class="db-panel-title">Sales by Category</div>
                        <div class="db-panel-subtitle">Trend Analysis — Pie Chart</div>
                    </div>
                    <div class="db-filter-row">
                        <input type="text" class="form-control" value="<?php echo $searchdate; ?>" name="alldata" id="alldata">
                        <button class="db-filter-btn" onclick="searchTrendPie()">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="db-panel-body">
                    <div id="chartContainer" class="piechartcontainer"></div>
                </div>
            </div>
        </div>

    </div><!-- /CHARTS ROW -->

    <!-- ── YEARLY LINE CHART ─────────────────────────────────── -->
    <div class="row db-section">
        <div class="col-md-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <div class="db-panel-title">Monthly Sales &amp; Purchase Overview</div>
                        <div class="db-panel-subtitle">Full-year comparison — <?php echo date('Y') ?></div>
                    </div>
                </div>
                <div class="db-panel-body" style="position:relative;height:400px;flex:none;">
                    <canvas id="yearlyreport"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ── TODAY'S SALES TABLE ───────────────────────────────── -->
    <div class="row db-section">
        <div class="col-md-12">
            <div class="db-panel">
                <div class="db-panel-head">
                    <div>
                        <div class="db-panel-title"><?php echo display('todays_sales_report') ?></div>
                        <div class="db-panel-subtitle"><?php echo date('d M Y') ?></div>
                    </div>
                    <a href="<?php echo base_url(); ?>sales_report" class="db-see-all">See All</a>
                </div>
                <div class="db-panel-body no-pad">
                    <div style="overflow-x:auto;">
                        <table class="db-table">
                            <thead>
                                <tr>
                                    <th style="width:50px"><?php echo display('sl') ?></th>
                                    <th><?php echo display('invoice_no') ?></th>
                                    <th><?php echo display('customer_name') ?></th>
                                    <th>Payment Type</th>
                                    <th style="text-align:right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                date_default_timezone_set('Asia/Colombo');
                                $ttl_amount = $ttl_paid = $ttl_due = $ttl_discout = $ttl_receipt = 0;
                                $todays = date('Y-m-d');
                                if ($todays_sales_report):
                                    $sl = 0;
                                    foreach ($todays_sales_report as $single):
                                        $sl++;
                                ?>
                                <tr>
                                    <td style="color:#94A3B8;font-weight:600"><?php echo $sl; ?></td>
                                    <td>
                                        <a class="db-invoice-link"
                                           href="<?php echo base_url() . 'invoice_details/' . $single->invoice_id1; ?><?php echo html_escape($single->invoice_id); ?>">
                                            <?php echo html_escape($single->sale_id); ?>
                                        </a>
                                    </td>
                                    <td><?php echo html_escape($single->customer_name); ?></td>
                                    <td>
                                        <span style="display:inline-block;padding:2px 10px;border-radius:20px;background:#F1F5F9;font-size:12px;font-weight:600;color:#475569;">
                                            <?php echo html_escape($single->pay); ?>
                                        </span>
                                    </td>
                                    <td style="text-align:right;font-weight:600;font-variant-numeric:tabular-nums;">
                                        <?php
                                        $ttl_paid += $single->grandTotal;
                                        echo html_escape(number_format($single->grandTotal, '2', '.', ','));
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="5" class="db-empty">
                                        <i class="fa fa-inbox"></i>
                                        <?php echo display('not_found'); ?>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align:right"><?php echo display('total') ?>:</td>
                                    <td style="text-align:right;font-variant-numeric:tabular-nums;">
                                        <?php
                                        $ttl_paid_float = html_escape(number_format($ttl_paid, '2', '.', ','));
                                        echo ($position == 0) ? "$currency $ttl_paid_float" : "$ttl_paid_float $currency";
                                        ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

</div><!-- /db -->

<!-- ── hidden data inputs (unchanged) ───────────────────── -->
<input type="hidden" id="bestsalelabel"      value='<?php echo html_escape($chart_label); ?>'>
<input type="hidden" id="bestsaledata"       value='<?php echo html_escape($chart_data); ?>'>
<input type="hidden" id="bestsalemax"        value=''>
<input type="hidden" id="month"              value="<?php echo html_escape($month); ?>">
<input type="hidden" id="tlvmonthsale"       value="<?php echo html_escape($tlvmonthsale); ?>">
<input type="hidden" id="tlvmonthpurchase"   value="<?php echo html_escape($tlvmonthpurchase); ?>">
<input type="hidden" id="salspurhcaselabel"  value="<?php echo display('sales_and_purchase_report_summary'); ?>">

<script src="<?php echo base_url() ?>assets/js/Chart.min.js"    type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/canvasjs.min.js" type="text/javascript"></script>

<script>
    /* ── Pie Chart ─────────────────────────────────────────── */
    function searchTrendPie() {
        var dateValue = document.getElementById("alldata").value;
        var monthMap = {
            "January":1,"February":2,"March":3,"April":4,"May":5,"June":6,
            "July":7,"August":8,"September":9,"October":10,"November":11,"December":12
        };
        var parts       = dateValue.split(" ");
        var monthNumber = monthMap[parts[0]];
        var year        = parts[1];

        $.ajax({
            url: $('#base_url').val() + 'invoice/invoice/best_of_sale2',
            type: 'POST',
            data: { month: monthNumber, year: year },
            success: function(response) {
                var jsonData = JSON.parse(response);
                if (jsonData === "") {
                    document.getElementById("chartContainer").innerHTML =
                        '<p style="color:#94A3B8;text-align:center;padding:40px 0">No data for this period.</p>';
                } else {
                    var total = jsonData.reduce(function(s, i) { return s + parseInt(i.product_count); }, 0);
                    var dataPoints = jsonData.map(function(item) {
                        return {
                            y: (parseInt(item.product_count) / total),
                            label: item.category_name
                        };
                    });
                    new CanvasJS.Chart("chartContainer", {
                        animationEnabled: true,
                        data: [{
                            type: "pie",
                            startAngle: 240,
                            yValueFormatString: "#0.00%",
                            indexLabel: "{label} {y}%",
                            dataPoints: dataPoints
                        }]
                    }).render();
                }
            },
            error: function(e) { console.log(e); }
        });
    }

    $(function() {
        "use strict";
        $('#alldata').datepicker({
            changeMonth: true, changeYear: true,
            showButtonPanel: true, maxDate: "+0M", dateFormat: 'MM yy'
        }).focus(function() {
            var cal = $(this);
            $('.ui-datepicker-calendar').detach();
            $('.ui-datepicker-close').click(function() {
                var m = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var y = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                cal.datepicker('setDate', new Date(y, m, 1));
            });
        });
    });

    window.onload = function() { searchTrendPie(); };

    /* ── Line + Bar Charts ─────────────────────────────────── */
    $(function() {

        /* Monthly line chart */
        var mvar     = $("#month").val();
        var month    = mvar.substring(0, mvar.length - 1).split(",");
        var tmsl     = $("#tlvmonthsale").val();
        var sale     = tmsl.substring(0, tmsl.length - 1).split(",");
        var tmpurch  = $("#tlvmonthpurchase").val();
        var purchase = tmpurch.substring(0, tmpurch.length - 1).split(",");
        var label    = $("#salspurhcaselabel").val();

        new Chart(document.getElementById("yearlyreport"), {
            type: 'line',
            data: {
                labels: month,
                datasets: [
                    { data: sale,     label: "Sales",    borderColor: "#2563EB", backgroundColor: "rgba(37,99,235,.08)", fill: true, tension: .35, pointBackgroundColor: "#2563EB", pointRadius: 4 },
                    { data: purchase, label: "Purchase", borderColor: "#0D9488", backgroundColor: "rgba(13,148,136,.08)", fill: true, tension: .35, pointBackgroundColor: "#0D9488", pointRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { padding: 20, fontSize: 12 } },
                title:  { display: false },
                scales: {
                    xAxes: [{ gridLines: { color: '#F1F5F9' }, ticks: { fontColor: '#94A3B8', fontSize: 11 } }],
                    yAxes: [{ gridLines: { color: '#F1F5F9' }, ticks: { fontColor: '#94A3B8', fontSize: 11, maxTicksLimit: 6, callback: function(v) { return v >= 1000 ? (v/1000).toFixed(0)+'k' : v; } } }]
                }
            }
        });

        /* Best-selling bar chart */
        var bestslabel  = $("#bestsalelabel").val();
        var bestsalelabel = bestslabel.substring(0, bestslabel.length - 1).split(",");
        var bestsdata   = $("#bestsaledata").val();
        var bestsaledata  = bestsdata.substring(0, bestsdata.length - 1).split(",");
        bestsalelabel.pop(); bestsaledata.pop();

        var barColors = ['#2563EB','#0D9488','#7C3AED','#D97706','#DB2777','#EA580C','#16A34A','#0891B2','#9333EA','#CA8A04'];

        new Chart(document.getElementById("lineChart"), {
            type: 'bar',
            data: {
                labels: bestsalelabel,
                datasets: [{
                    label: "Sales",
                    backgroundColor: barColors,
                    borderRadius: 6,
                    data: bestsaledata
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: { mode: 'index', intersect: false },
                scales: {
                    xAxes: [{ gridLines: { display: false }, ticks: { fontColor: '#94A3B8', fontSize: 11 } }],
                    yAxes: [{ gridLines: { color: '#F1F5F9' }, ticks: { beginAtZero: true, fontColor: '#94A3B8', fontSize: 11 } }]
                },
                animation: {
                    duration: 1200, easing: 'easeOutQuart',
                    onComplete: function() {
                        var ci = this.chart, ctx = ci.ctx;
                        ctx.fillStyle = '#64748B';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.font = '11px system-ui';
                        this.data.datasets.forEach(function(ds, i) {
                            ci.getDatasetMeta(i).data.forEach(function(bar, idx) {
                                var v = ds.data[idx];
                                if (v) ctx.fillText(v, bar.x, bar.y - 4);
                            });
                        });
                    }
                }
            }
        });
    });
</script>

<?php if (!empty($just_logged_in)) { ?>
<script>
    // The modals themselves live at the body root (layout.php, loaded on
    // every page) — this just kicks off the mandatory, ordered walkthrough,
    // and only on the first page after login.
    $(document).ready(function () { startLoginAlertSequence(); });
</script>
<?php } ?>
