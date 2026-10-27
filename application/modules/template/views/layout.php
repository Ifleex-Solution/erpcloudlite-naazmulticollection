<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('includes/head') ?>
    <style>
        .page-loader-wrapper {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: 99999999 !important;
            background: rgba(255,255,255,0.97) !important;
            display: flex;
            align-items: center !important;
            justify-content: center !important;
            flex-direction: column !important;
        }
        .page-loader-wrapper .loader {
            position: static !important;
            top: auto !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 22px;
        }
        .preloader {
            position: relative;
            width: 72px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .spinner {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: conic-gradient(#8b5cf6 0deg, #a78bfa 100deg, #ddd6fe 190deg, transparent 300deg);
            -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #fff calc(100% - 7px));
            mask: radial-gradient(farthest-side, transparent calc(100% - 7px), #fff calc(100% - 7px));
            animation: spin 0.9s linear infinite;
            filter: drop-shadow(0 0 10px rgba(139, 92, 246, 0.45));
        }
        .spinner::after {
            content: '';
            position: absolute;
            top: 6px; right: 5px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #8b5cf6;
            box-shadow: 0 0 8px 2px rgba(139,92,246,0.5);
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .page-loader-wrapper p {
            margin: 0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #94A3B8 !important;
            letter-spacing: 2px !important;
            text-transform: uppercase !important;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">


    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner"></div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>

    <!-- Site wrapper -->
    <div class="wrapper">

        <header class="main-header">
            <?php $this->load->view('includes/header') ?>
        </header>


        <!-- Left side column. contains the sidebar -->
        <aside class="main-sidebar">
            <!-- sidebar -->
            <?php $this->load->view('includes/sidebar') ?>
        </aside>

        <!-- Mobile sidebar overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <!-- Main content -->
            <div class="content <?php $gui_segment = $this->uri->segment(1);
                                if ($gui_segment == 'gui_pos') {
                                    echo 'p-0';
                                } else {
                                    echo ' ';
                                }
                                ?>">



                <?php $gui_p = $this->uri->segment(1);
                if ($gui_p != 'gui_pos') {
                ?>
                    <div class="breadcrumb-box">

                        <ol class="breadcrumb">
                            <li><a href="#"><i class="pe-7s-home"></i> <?php echo display('home') ?></a></li>
                            <li><a href="#"><?php echo $module; ?></a></li>
                            <li class="active"><?php echo $title; ?></li>
                        </ol>
                    </div>
                <?php } ?>

                <!-- load messages -->
                <?php $this->load->view('includes/messages') ?>
                <div class="se-pre-con"></div>
                <!-- load custom page -->
                <?php echo $this->load->view($module . '/' . $page) ?>
            </div> <!-- /.content -->
        </div> <!-- /.content-wrapper -->
        <footer class="main-footer">
            <input type="hidden" name="" id="base_url" value="<?php echo base_url(); ?>">
            <div class="pull-right hidden-xs">
                <?php echo (!empty($setting->address) ? $setting->address : null) ?>
            </div>

            <strong>
                <?php echo (!empty($this->session->userdata('footer_text')) ? $this->session->userdata('footer_text') : null) ?>
            </strong>
            <a href="<?php echo current_url() ?>">
                <?php echo (!empty($setting->title) ? $setting->title : null) ?></a>
            <input type="hidden" id="base_url" value="<?php echo base_url(); ?>" name="">
            <input type="hidden" name="dis_type" id="discount_type" value="<?php echo $discount_type ?>">
            <input type="hidden" name="csrf_test_name" id="CSRF_TOKEN" value="<?php echo $this->security->get_csrf_hash(); ?>">
            <input type="hidden" id="currency" value="<?php echo $currency; ?>" name="">
        </footer>
    </div> <!-- ./wrapper -->

    <!-- Start Core Plugins-->
    <?php $this->load->view('includes/js') ?>

    <!-- calculator modal -->
    <div class="modal fade-scale" id="calculator" role="dialog">
        <div class="modal-dialog" id="calculatorcontent">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <div class="calcontainer">
                        <div class="screen">
                            <h1 id="mainScreen">0</h1>
                        </div>
                        <table class="cal-table">
                            <tr>
                                <td><button value="7" id="7" class="cal-btn" onclick="InputSymbol(7)">7</button></td>
                                <td><button value="8" id="8" class="cal-btn" onclick="InputSymbol(8)">8</button></td>
                                <td><button value="9" id="9" class="cal-btn" onclick="InputSymbol(9)">9</button></td>
                                <td><button onclick="DeleteLastSymbol()" class="cal-btn">CE</button></td>
                            </tr>
                            <tr>
                                <td><button value="4" id="4" class="cal-btn" onclick="InputSymbol(4)">4</button></td>
                                <td><button value="5" id="5" class="cal-btn" onclick="InputSymbol(5)">5</button></td>
                                <td><button value="6" id="6" class="cal-btn" onclick="InputSymbol(6)">6</button></td>
                                <td><button value="/" id="104" class="cal-btn" onclick="InputSymbol(104)">/</button></td>
                            </tr>
                            <tr>
                                <td><button value="1" id="1" class="cal-btn" onclick="InputSymbol(1)">1</button></td>
                                <td><button value="2" id="2" class="cal-btn" onclick="InputSymbol(2)">2</button></td>
                                <td><button value="3" id="3" class="cal-btn" onclick="InputSymbol(3)">3</button></td>
                                <td><button value="*" id="103" class="cal-btn" onclick="InputSymbol(103)">*</button></td>
                            </tr>
                            <tr>
                                <td><button value="0" id="0" class="cal-btn" onclick="InputSymbol(0)">0</button></td>
                                <td><button value="." id="128" class="cal-btn" onclick="InputSymbol(128)">.</button></td>
                                <td><button value="-" id="102" class="cal-btn" onclick="InputSymbol(102)">-</button></td>
                                <td><button value="+" id="101" class="cal-btn" onclick="InputSymbol(101)">+</button></td>
                            </tr>
                            <tr>
                                <td colspan="2"><button onclick="ClearScreen()" class="cal-btn">C</button></td>
                                <td colspan="1"><button onclick="CalculateTotal()" class="cal-btn">=</button></td>
                                <td colspan="1"><button data-dismiss="modal" class="cal-btn-danger"><i class="fa fa-power-off"></i></button></td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>




    <?php if ($this->session->userdata('isLogIn')): ?>
    <!-- Pusher JS SDK -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
    (function() {
        <?php
        $CI =& get_instance();
        $CI->config->load('pusher', true);
        $pusher_key     = $CI->config->item('pusher_key',     'pusher');
        $pusher_cluster = $CI->config->item('pusher_cluster', 'pusher');
        $pusher_app     = $CI->config->item('application',    'pusher');
        ?>
        var pusherKey     = '<?php echo $pusher_key; ?>';
        var pusherCluster = '<?php echo $pusher_cluster; ?>';
        var thisApp       = '<?php echo $pusher_app; ?>';
        if (pusherKey) {
            var pusher  = new Pusher(pusherKey, { cluster: pusherCluster });
            var channel = pusher.subscribe('admin-control');
            channel.bind('force-logout', function(data) {
                if (data && data.app && data.app !== thisApp) return;
                var base_url = $('#base_url').val();
                window.location.href = base_url + 'logout';
                // setTimeout(function() {
                //     window.location.href = base_url + 'logout';
                // }, 5000);
            });
        }
    })();
    </script>
    <?php if ($this->session->userdata('user_type') == 3): ?>
    <script>
    (function() {
        var IDLE_MS = 120 * 1000; // 2 minutes
        var idleTimer;
        var lastActivity = 0;

        function forceLogout() {
            var base_url = $('#base_url').val();
            window.location.href = base_url + 'logout';
        }

        function resetTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(forceLogout, IDLE_MS);
        }

        // Throttle: reset at most once per second to avoid excessive timer churn on mousemove/scroll
        function onActivity() {
            var now = Date.now();
            if (now - lastActivity > 1000) {
                lastActivity = now;
                resetTimer();
            }
        }

        ['click', 'mousemove', 'keydown', 'touchstart', 'scroll'].forEach(function(evt) {
            document.addEventListener(evt, onActivity, { passive: true });
        });

        resetTimer(); // start countdown on page load
    })();
    </script>
    <?php endif; ?>
    <script>
    (function heartbeat() {
        var base_url = $('#base_url').val();
        function ping() {
            $.post(base_url + 'dashboard/setting/heartbeat', {
                csrf_test_name: $('#CSRF_TOKEN').val()
            });
        }
        ping();
        setInterval(ping, 120000);

        // Mark offline on tab close or browser close
        var offlineSent = false;
        function sendOffline() {
            if (!offlineSent) {
                offlineSent = true;
                navigator.sendBeacon(base_url + 'dashboard/setting/go_offline');
            }
        }
        window.addEventListener('beforeunload', sendOffline);
        window.addEventListener('pagehide', sendOffline);
    })();
    </script>
    <?php
    $CI_sess =& get_instance();
    $CI_sess->load->library('identity_hub');
    $session_idle_s = (int)$CI_sess->config->item('sess_expiration');
    $session_poll_s = $CI_sess->session->userdata('sso_sid') ? $CI_sess->identity_hub->check_interval() : 0;
    ?>
    <script>
    // Session watcher: send the user to the login page as soon as their session has ended,
    // instead of leaving a dead page on screen until the next click.
    (function () {
        var base_url = $('#base_url').val();
        var IDLE_S = <?php echo $session_idle_s; ?>;   // ERP session length (config sess_expiration)
        var POLL_S = <?php echo $session_poll_s; ?>;   // 0 = not a hub login / checking off

        function toLogin() { window.location.href = base_url + 'login'; }

        // 1) ERP session: it ends after IDLE_S seconds with no activity. Keep it alive while the user
        //    is active (a keep-alive request at most every IDLE_S/3 s), and leave when they go idle.
        var lastActivity = Date.now(), lastServer = Date.now();
        function onActivity() {
            lastActivity = Date.now();
            if (IDLE_S > 0 && lastActivity - lastServer > (IDLE_S * 1000) / 3) {
                lastServer = lastActivity;
                $.post(base_url + 'dashboard/setting/heartbeat', { csrf_test_name: $('#CSRF_TOKEN').val() });
            }
        }
        ['click', 'mousemove', 'keydown', 'touchstart', 'scroll'].forEach(function (evt) {
            document.addEventListener(evt, onActivity, { passive: true });
        });
        if (IDLE_S > 0) {
            setInterval(function () {
                if ((Date.now() - lastActivity) / 1000 >= IDLE_S) { window.location.href = base_url + 'logout'; }
            }, 1000);
        }

        // 2) Hub session: poll the ERP, which asks the hub whether this login's hub session is still alive
        if (POLL_S > 0) {
            setInterval(function () {
                $.ajax({ url: base_url + 'dashboard/auth/session_status', type: 'POST', dataType: 'json', cache: false })
                    .done(function (r) { if (r && r.active === false) { toLogin(); } })
                    .fail(function (x) { if (x.status === 401 || x.status === 302) { toLogin(); } });
            }, POLL_S * 1000);
        }
    })();
    </script>
    <?php endif; ?>

    <div class="modal fade modal-success" id="cust_info" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <a href="#" class="close" data-dismiss="modal">&times;</a>
                    <h3 class="modal-title"><?php echo display('add_new_customer') ?></h3>
                </div>

                <div class="modal-body">
                    <div id="customeMessage" class="alert hide"></div>
                    <?php echo form_open('invoice/invoice/instant_customer', array('class' => 'form-vertical', 'id' => 'newcustomer')) ?>
                    <div class="panel-body">
                        <input type="hidden" name="csrf_test_name" id="" value="<?php echo $this->security->get_csrf_hash(); ?>">
                        <div class="form-group row">
                            <label for="customer_name" class="col-sm-4 col-form-label"><?php echo display('customer_name') ?> <i class="text-danger">*</i></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="customer_name" id="m_customer_name" type="text" placeholder="<?php echo display('customer_name') ?>" required="" tabindex="1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-4 col-form-label"><?php echo display('customer_email') ?></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="email" id="email" type="email" placeholder="<?php echo display('customer_email') ?>" tabindex="2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mobile" class="col-sm-4 col-form-label"><?php echo display('customer_mobile') ?></label>
                            <div class="col-sm-6">
                                <input class="form-control" name="mobile" id="mobile" type="number" placeholder="<?php echo display('customer_mobile') ?>" min="0" tabindex="3">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address " class="col-sm-4 col-form-label"><?php echo display('customer_address') ?></label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="address" id="address " rows="3" placeholder="<?php echo display('customer_address') ?>" tabindex="4"></textarea>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <a href="#" class="btn btn-danger" tabindex="5" data-dismiss="modal">Close</a>

                    <input type="submit" tabindex="6" class="btn btn-success" value="Submit">
                </div>
                <?php echo form_close() ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Login Alert Pop-ups (Credit/Expiration/Stock) — same body-root
         reasoning as Change Password below: these need to exist in the DOM
         on every page (the header icons can reopen them from anywhere),
         not just the dashboard page right after login. -->
    <?php $this->load->view('dashboard/home/login_alert_modals'); ?>

    <!-- Change Password popup (profile menu) — kept at the body root like
         the modals above, not inside header.php's <header class="main-header">,
         which has its own z-index/stacking context that trapped the modal
         below Bootstrap's backdrop and made its fields unclickable. -->
    <div id="changePasswordModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Change Password</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Old Password</label>
                        <div class="cp-pw-wrap">
                            <input type="password" class="form-control" id="cp_old_password">
                            <i class="fa fa-eye cp-pw-toggle" onclick="toggleCpPassword('cp_old_password', this)"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="cp-pw-wrap">
                            <input type="password" class="form-control" id="cp_new_password">
                            <i class="fa fa-eye cp-pw-toggle" onclick="toggleCpPassword('cp_new_password', this)"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <div class="cp-pw-wrap">
                            <input type="password" class="form-control" id="cp_repassword">
                            <i class="fa fa-eye cp-pw-toggle" onclick="toggleCpPassword('cp_repassword', this)"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="cp_save_btn" onclick="submitChangePassword()">
                        <i class="fa fa-key"></i> Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .cp-pw-wrap { position: relative; }
        .cp-pw-wrap .form-control { padding-right: 38px !important; }
        .cp-pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            cursor: pointer;
            font-size: 14px;
            transition: color 0.15s;
        }
        .cp-pw-toggle:hover { color: #8b5cf6; }
    </style>
    <script>
        function toggleCpPassword(inputId, iconEl) {
            var input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                iconEl.classList.remove('fa-eye');
                iconEl.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                iconEl.classList.remove('fa-eye-slash');
                iconEl.classList.add('fa-eye');
            }
        }
        function submitChangePassword() {
            var oldPassword = document.getElementById('cp_old_password').value;
            var newPassword = document.getElementById('cp_new_password').value;
            var repassword  = document.getElementById('cp_repassword').value;

            if (!oldPassword || !newPassword || !repassword) {
                alert('Please fill in all fields.');
                return;
            }
            if (newPassword.length < 6) {
                alert('New password must be at least 6 characters.');
                return;
            }
            if (newPassword !== repassword) {
                alert('New password and confirm password do not match.');
                return;
            }

            var $btn = $('#cp_save_btn');
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Changing...');

            $.ajax({
                type: 'POST',
                url: $('#base_url').val() + 'dashboard/home/ajax_change_password',
                data: {
                    old_password: oldPassword,
                    password: newPassword,
                    repassword: repassword,
                    csrf_test_name: $('#CSRF_TOKEN').val()
                },
                success: function (raw) {
                    var res;
                    try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; } catch (e) { res = { success: false }; }
                    $btn.prop('disabled', false).html('<i class="fa fa-key"></i> Change Password');
                    if (res.success) {
                        alert(res.message || 'Password changed successfully.');
                        $('#cp_old_password, #cp_new_password, #cp_repassword').val('');
                        $('#changePasswordModal').modal('hide');
                    } else {
                        alert(res.message || 'Could not change password.');
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html('<i class="fa fa-key"></i> Change Password');
                    alert('Server error. Please try again.');
                }
            });
        }
    </script>
</body>

</html>