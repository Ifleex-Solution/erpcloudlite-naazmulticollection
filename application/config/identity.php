<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Fexten Identity Hub (single sign-on)
| -------------------------------------------------------------------------
| This file holds a client secret - it is git-ignored. Copy identity.sample.php
| to identity.php on each environment and fill in the values from the hub's
| Products / Clients admin pages.
*/

// Master switch. When FALSE the ERP uses its own username/password login only.
$config['enabled']       = TRUE;

// Base URL of the identity hub (no trailing slash)
$config['hub_url']       = 'https://auth.fextensolutions.com/';

$config['product_id']    = '';
$config['client_id']     = '';
$config['client_secret'] = '';

// Where the hub sends the browser back to. Must match a redirect URL registered
// on the product (or client) in the hub exactly. Empty = this app's base_url().
$config['redirect_uri']  = '';

// Keep the old username/password form reachable at /login?local=1 as a fallback
// (e.g. if the hub is down). Set FALSE to force every login through the hub.
$config['allow_local_login'] = TRUE;

// How often (seconds) the ERP asks the hub whether this login's hub session is still alive.
// When the hub says it has ended, the ERP signs the user out too. 0 = never check.
// Default: 60 seconds.
$config['check_interval'] = 60;

// Special "manager" command logins (active123 / inactive123 / inactive@123 / clear123) in the login controller.
// Keep FALSE in production; set TRUE only on a development machine.
$config['enable_manager_commands'] = FALSE;
