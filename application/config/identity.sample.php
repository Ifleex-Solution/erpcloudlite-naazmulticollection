<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Copy to identity.php and fill in the values from the Fexten Identity Hub admin.
$config['enabled']           = TRUE;
$config['hub_url']           = 'http://localhost/fecten_identity_hub';
$config['product_id']        = 'prod_xxxxxxxxxxxxxxxx';
$config['client_id']         = '';
$config['client_secret']     = '';
$config['redirect_uri']      = '';     // empty = base_url()
$config['allow_local_login'] = TRUE;   // /login?local=1 password fallback

// How often (seconds) the ERP asks the hub whether this login's hub session is still alive.
// When the hub says it has ended, the ERP signs the user out too. 0 = never check.
// Default: 60 seconds.
$config['check_interval'] = 60;

// Special "manager" command logins (active123 / inactive123 / inactive@123 / clear123) in the login controller.
// Keep FALSE in production; set TRUE only on a development machine.
$config['enable_manager_commands'] = FALSE;
