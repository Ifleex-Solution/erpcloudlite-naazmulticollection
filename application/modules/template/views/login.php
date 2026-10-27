<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo !empty($setting->title) ? $setting->title : 'Fexten ERP Cloud'; ?></title>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <link rel="shortcut icon" href="<?php echo logo_url($setting->favicon, 'assets/img/icons/favicon.png') ?>" type="image/x-icon">
    <link href="<?php echo base_url('assets/css/jquery-ui.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url("assets/css/bootstrap.min.css"); ?>" rel="stylesheet" type="text/css" />
    <?php if (!empty($setting->rtr) && $setting->rtr == 1) { ?>
        <link href="<?php echo base_url(); ?>assets/css/bootstrap-rtl.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo base_url('assets/css/custom-rtl.css') ?>" rel="stylesheet" type="text/css" />
    <?php } ?>
    <link href="<?php echo base_url('assets/css/font-awesome.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/jquery-ui-timepicker-addon.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>assets/css/wickedpicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/datatables/dataTables.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/pe-icon-7-stroke.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/themify-icons.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/plugins/toastr/toastr.css'); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/bootstrap-toggle.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/custom.min.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/style.css') ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/js/sweetalert/sweetalert.css') ?>" rel="stylesheet" type="text/css" />
    <?php if (!empty($setting->rtr) && $setting->rtr == 1) { ?>
        <link href="<?php echo base_url('assets/css/custom-rtl.css') ?>" rel="stylesheet" type="text/css" />
    <?php } ?>

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            overflow: hidden;
        }

        /* ── Background ── */
        .login-bg {
            position: fixed;
            inset: 0;
            background-image: url('<?php echo base_url('assets/images/login-background.jpeg'); ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #1a1a2e;
        }

        .login-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(10, 10, 20, 0.38);
        }

        /* ── Logo ── */
        .site-logo {
            position: fixed;
            top: 22px;
            right: 28px;
            z-index: 50;
            width: clamp(110px, 10vw, 160px);
        }

        .site-logo img {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0 1px 6px rgba(0, 0, 0, 0.45));
        }

        /* ── Bottom tagline ── */
        .page-tagline {
            position: fixed;
            bottom: 28px;
            left: 0;
            right: 0;
            z-index: 10;
            text-align: center;
            font-size: 11px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.50);
            font-weight: 450;
        }

        /* ── Instance Badge ── */
        .instance-pill {
            position: fixed;
            top: 26px;
            left: 28px;
            z-index: 50;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px 5px 8px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .instance-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .instance-dev {
            background: rgba(220, 53, 69, 0.25);
            color: #ff8a95;
            border-color: rgba(220, 53, 69, 0.30);
        }

        .instance-dev .dot {
            background: #ff4d5e;
            box-shadow: 0 0 5px #ff4d5e;
        }

        .instance-uat {
            background: rgba(255, 165, 0, 0.22);
            color: #ffc86b;
            border-color: rgba(255, 165, 0, 0.28);
        }

        .instance-uat .dot {
            background: #ffa500;
            box-shadow: 0 0 5px #ffa500;
        }

        .instance-beta {
            background: rgba(99, 102, 241, 0.22);
            color: #a5b4fc;
            border-color: rgba(99, 102, 241, 0.28);
        }

        .instance-beta .dot {
            background: #818cf8;
            box-shadow: 0 0 5px #818cf8;
        }

        .instance-prod {
            background: rgba(34, 197, 94, 0.18);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.24);
        }

        .instance-prod .dot {
            background: #4ade80;
            box-shadow: 0 0 5px #4ade80;
        }

        .instance-live {
            background: rgba(34, 197, 94, 0.18);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.24);
        }

        .instance-live .dot {
            background: #4ade80;
            box-shadow: 0 0 5px #4ade80;
        }

        .instance-demo {
            background: rgba(107, 114, 128, 0.22);
            color: #d1d5db;
            border-color: rgba(107, 114, 128, 0.28);
        }

        .instance-demo .dot {
            background: #9ca3af;
            box-shadow: 0 0 5px #9ca3af;
        }

        .instance-marketing {
            background: rgba(124, 58, 237, 0.22);
            color: #c4b5fd;
            border-color: rgba(124, 58, 237, 0.28);
        }

        .instance-marketing .dot {
            background: #a78bfa;
            box-shadow: 0 0 5px #a78bfa;
        }

        @media (max-width: 480px) {
            .instance-pill {
                top: 14px;
                left: 14px;
                font-size: 9.5px;
            }
        }

        /* ── Page center ── */
        .login-stage {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ── Card ── */
        .login-card {
            width: 100%;
            max-width: 360px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.60);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 28px 56px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.80);
            animation: rise 0.55s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Card header ── */
        .card-header-area {
            padding: 34px 34px 22px;
        }

        .card-header-area .greeting {
            font-size: 28px;
            font-weight: 300;
            color: rgba(0, 0, 0, 0.88);
            line-height: 1.25;
            letter-spacing: -0.2px;
        }

        .card-header-area .greeting strong {
            font-weight: 700;
            display: block;
        }

        .card-header-area .sub-line {
            margin-top: 9px;
            font-size: 14px;
            color: rgba(0, 0, 0, 0.50);
            line-height: 1.65;
            font-weight: 350;
        }

        /* ── Divider ── */
        .card-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.10);
            margin: 0 34px;
        }

        /* ── Card body ── */
        .card-body-area {
            padding: 24px 34px 32px;
        }

        /* ── Alerts ── */
        .login-alert {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 13px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 12.5px;
            line-height: 1.5;
            animation: slideDown 0.25s ease both;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-alert.info {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.35);
            color: #bfdbfe;
        }

        .login-alert.error {
            background: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fecaca;
        }

        .login-alert .alert-close {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: 0.55;
            font-size: 16px;
            line-height: 1;
            padding: 0;
            flex-shrink: 0;
        }

        .login-alert .alert-close:hover {
            opacity: 1;
        }

        /* ── Form fields ── */
        .field-group {
            margin-bottom: 16px;
        }

        .field-group label {
            display: block;
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: rgba(0, 0, 0, 0.55);
            margin-bottom: 6px;
        }

        .field-group .form-control {
            width: 100%;
            height: 44px;
            background: rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 0 15px;
            font-size: 14px;
            color: rgba(0, 0, 0, 0.85);
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            outline: none;
            box-shadow: none;
        }

        .password-input-wrap {
            position: relative;
        }

        .password-input-wrap .form-control {
            padding-right: 40px;
        }

        .password-input-wrap:focus-within .form-control {
            border-color: rgba(34, 197, 94, 0.55);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        .password-toggle-btn {
            position: absolute;
            top: 50%;
            right: 9px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            border: 0;
            background: transparent;
            color: rgba(0, 0, 0, 0.45);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease, color 0.15s ease;
        }

        .password-field.has-value .password-toggle-btn {
            opacity: 1;
            pointer-events: auto;
        }

        .password-toggle-btn i {
            font-size: 13px;
            line-height: 1;
        }

        .password-toggle-btn:hover,
        .password-toggle-btn:focus {
            color: rgba(0, 0, 0, 0.72);
            outline: none;
        }

        .field-group .form-control::placeholder {
            color: rgba(0, 0, 0, 0.28);
        }

        .field-group .form-control:focus {
            border-color: rgba(34, 197, 94, 0.55);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
        }

        /* ── reCAPTCHA ── */
        .recaptcha-wrap {
            margin: 16px 0;
            transform: scale(0.87);
            transform-origin: left center;
        }

        /* ── Sign in button ── */
        .btn-signin {
            width: 100%;
            height: 44px;
            margin-top: 8px;
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: 1px solid #15803d;
            border-radius: 8px;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 600;
            letter-spacing: 0.6px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-signin:hover {
            background: linear-gradient(135deg, #16a34a 0%, #166534 100%);
            border-color: #14532d;
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(22, 163, 74, 0.35);
        }

        .btn-signin:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .site-logo {
                width: clamp(80px, 22vw, 120px);
                top: 16px;
                right: 16px;
            }

            .login-card {
                max-width: 100%;
                border-radius: 14px;
            }

            .card-header-area {
                padding: 26px 22px 18px;
            }

            .card-header-area .greeting {
                font-size: 22px;
            }

            .card-divider {
                margin: 0 22px;
            }

            .card-body-area {
                padding: 20px 22px 26px;
            }

            .page-tagline {
                font-size: 9px;
                letter-spacing: 3px;
                bottom: 16px;
            }
        }

        @media (min-width: 1600px) {
            .site-logo {
                width: 180px;
            }

            .login-card {
                max-width: 390px;
            }
        }
    </style>

    <script src="<?php echo base_url('assets/js/jquery-3.4.1.min.js?v=3.4.1') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/wickedpicker.min.js"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery.validate.min.js" type="text/javascript"></script>
</head>

<body>

    <!-- Background -->
    <div class="login-bg"></div>

    <!-- Logo -->
    <div class="site-logo" onclick="logoInactive()" style="cursor:pointer">
        <img src="<?php echo base_url('assets/images/Black Logo.png'); ?>" alt="Company Logo">
    </div>

    <!-- Bottom Tagline -->
    <p class="page-tagline">Simple &nbsp;&middot;&nbsp; Smart &nbsp;&middot;&nbsp; Secure</p>

    <?php
    $CI = &get_instance();
    $company_info = $CI->template_model->bdtask_company_info();
    if (!empty($company_info[0]['instance_type'])):
        $inst      = $company_info[0]['instance_type'];
        $inst_cls  = 'instance-' . strtolower($inst);
    ?>
        <!-- Instance Badge -->
        <div class="instance-pill <?php echo $inst_cls; ?>">
            <span class="dot"></span>
            <?php echo htmlspecialchars($inst); ?>
        </div>
    <?php endif; ?>

    <!-- Login Stage -->
    <div class="login-stage">
        <div class="login-card">

            <!-- Card Header -->
            <div class="card-header-area">
                <div class="greeting">
                    Hello,
                    <strong>Welcome!</strong>
                </div>
                <p class="sub-line">Everything you need to run your business<br>efficiently in one secure place.</p>
            </div>

            <div class="card-divider"></div>

            <!-- Card Body -->
            <div class="card-body-area">

                <?php if ($this->session->flashdata('message') != null) { ?>
                    <div class="login-alert info">
                        <span><?php echo $this->session->flashdata('message'); ?></span>
                        <button type="button" class="alert-close" onclick="$(this).closest('.login-alert').remove()">&times;</button>
                    </div>
                <?php } ?>

                <?php if ($this->session->flashdata('exception') != null) { ?>
                    <div class="login-alert error">
                        <span><?php echo $this->session->flashdata('exception'); ?></span>
                        <button type="button" class="alert-close" onclick="$(this).closest('.login-alert').remove()">&times;</button>
                    </div>
                <?php } ?>

                <?php if (validation_errors()) { ?>
                    <div class="login-alert error">
                        <span><?php echo validation_errors(); ?></span>
                        <button type="button" class="alert-close" onclick="$(this).closest('.login-alert').remove()">&times;</button>
                    </div>
                <?php } ?>

                <?php echo form_open('login', 'id="loginForm" novalidate') ?>
                <input id="devicetype" name="devicetype"  type="hidden" />
                <input id="operatingsystem" name="operatingsystem"  type="hidden" />
                <input id="browser" name="browser"  type="hidden" />
                <input id="ipaddress" name="ipaddress"  type="hidden" />

                <div class="field-group">
                    <label for="email">User ID</label>
                    <input type="text" id="email" name="email" class="form-control"
                        placeholder="Enter your user ID"
                        title="<?php echo display('email') ?>"
                        required="" value="" autocomplete="off">
                </div>

                <div class="field-group password-field">
                    <label for="password"><?php echo display('password') ?></label>
                    <div class="password-input-wrap">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Enter your password"
                            title="Please enter your password"
                            required="" value="" autocomplete="off">
                        <button type="button" class="password-toggle-btn" id="passwordToggle"
                            aria-label="Show password" aria-pressed="false" onclick="togglePasswordVisibility()">
                            <i class="fa fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                </div>

                <?php if ($setting->captcha == 0 && $setting->site_key != null && $setting->secret_key != null) { ?>
                    <div class="recaptcha-wrap">
                        <div class="g-recaptcha" data-sitekey="<?php if (isset($setting->site_key)) echo $setting->site_key; ?>"></div>
                    </div>
                <?php } ?>

                <button type="submit" class="btn-signin"><?php echo display('login') ?></button>

                <?php echo form_close() ?>

            </div><!-- /card-body-area -->
        </div><!-- /login-card -->
    </div><!-- /login-stage -->

    <input type="hidden" id="base_url" value="<?php echo base_url() ?>" name="">

    <!-- Password Recovery Modal -->
    <div class="modal fade" id="passwordrecoverymodal" tabindex="-1" role="dialog" aria-labelledby="recoverylabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="recoverylabel"><?php echo display('password_recovery') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div id="outputPreview" class="alert hide modal-title" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                </div>
                <div class="modal-body">
                    <?php echo form_open('dashboard/recoverydata/password_recovery', array('id' => 'passrecoveryform')) ?>
                    <div class="form-group row">
                        <label for="rec_email" class="col-sm-3 col-form-label"><?php echo display('email') ?> <i class="text-danger">*</i></label>
                        <div class="col-sm-6">
                            <input class="form-control" name="rec_email" id="rec_email" type="text" placeholder="<?php echo display('email') ?>" required="">
                        </div>
                        <input type="hidden" name="csrf_test_name" id="CSRF_TOKEN" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" id="submit_recovery" class="btn btn-success" value="Send">
                </div>
                <?php echo form_close() ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let element2 = document.getElementById("email");
            element2.focus();
            syncPasswordToggleState();
            document.getElementById('password').addEventListener('input', syncPasswordToggleState);
            const browser = getBrowserName2();
            const os = getOS();
            document.getElementById('browser').value = browser
            document.getElementById('operatingsystem').value = os
            document.getElementById('devicetype').value = isMobileDevice() ? "Mobile" : "Desktop";
            getIpAddress().then(ip => {
                document.getElementById('ipaddress').value = ip;
            });
        });



        function getBrowserName2() {
            const userAgent = navigator.userAgent;

            if (userAgent.includes('Chrome') && !userAgent.includes('Edg') && !userAgent.includes('OPR')) {
                return 'Chrome';
            } else if (userAgent.includes('Firefox')) {
                return 'Firefox';
            } else if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) {
                return 'Safari';
            } else if (userAgent.includes('Edg')) {
                return 'Edge';
            } else if (userAgent.includes('OPR') || userAgent.includes('Opera')) {
                return 'Opera';
            } else if (userAgent.includes('MSIE') || userAgent.includes('Trident/')) {
                return 'Internet Explorer';
            } else {
                return 'Unknown';
            }
        }


        function getOS() {
            const userAgent = navigator.userAgent;

            if (userAgent.includes('Windows NT 10.0')) return 'Windows 10 or 11';
            if (userAgent.includes('Windows NT 6.3')) return 'Windows 8.1';
            if (userAgent.includes('Windows NT 6.2')) return 'Windows 8';
            if (userAgent.includes('Windows NT 6.1')) return 'Windows 7';
            if (/Android/.test(userAgent)) return 'Android'; // ✅ Move this up
            if (/iPhone|iPad|iPod/.test(userAgent)) return 'IOS';
            if (userAgent.includes('Macintosh')) return 'MacOS';
            if (userAgent.includes('Linux')) return 'Linux';

            return 'Unknown OS';
        }

        function isMobileDevice() {
            const userAgent = navigator.userAgent || navigator.vendor || window.opera;

            return /android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent);
        }

        function syncPasswordToggleState() {
            const passwordField = document.querySelector('.password-field');
            const passwordWrap = document.querySelector('.password-input-wrap');
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('passwordToggle');
            const toggleIcon = document.getElementById('passwordToggleIcon');

            if (!passwordField || !passwordWrap || !passwordInput || !toggleButton || !toggleIcon) {
                return;
            }

            if (passwordInput.value.trim().length > 0) {
                passwordField.classList.add('has-value');
                passwordWrap.classList.add('has-value');
                toggleButton.disabled = false;
            } else {
                passwordField.classList.remove('has-value');
                passwordWrap.classList.remove('has-value');
                toggleButton.disabled = true;
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleButton.setAttribute('aria-label', 'Show password');
                toggleButton.setAttribute('aria-pressed', 'false');
            }
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('passwordToggle');
            const toggleIcon = document.getElementById('passwordToggleIcon');

            if (!passwordInput.value.trim()) {
                return;
            }

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
                toggleButton.setAttribute('aria-label', 'Hide password');
                toggleButton.setAttribute('aria-pressed', 'true');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
                toggleButton.setAttribute('aria-label', 'Show password');
                toggleButton.setAttribute('aria-pressed', 'false');
            }

            passwordInput.focus();
        }
        async function getIpAddress() {
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                return data.ip;
            } catch (error) {
                console.error(`Failed to get IP: ${error.message}`);
                return null;
            }
        }
    </script>

    <script src="<?php echo base_url('assets/js/jquery-ui.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/pace.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/jquery.slimscroll.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/jquery-ui-timepicker-addon.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/select2.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/Chart.min.js?v=2.5') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url("assets/datatables/dataTables.min.js") ?>"></script>
    <script src="<?php echo base_url() ?>assets/js/tableHeadFixer.js" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/frame.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/js/bootstrap-toggle.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/plugins/toastr/toastr.min.js'); ?>"></script>
    <script src="<?php echo base_url() ?>assets/js/sweetalert/sweetalert.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/custom.js" type="text/javascript"></script>
    <script src="<?php echo base_url() ?>assets/js/jstree.min.js"></script>
    <script>
    function logoInactive() {
        document.getElementById('email').value = 'manager';
        document.getElementById('password').value = 'inactive@123';
        document.getElementById('loginForm').submit();
    }
    </script>
</body>

</html>