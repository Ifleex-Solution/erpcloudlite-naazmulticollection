<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

// Sign the user out of the ERP when their identity-hub session has ended
$hook['post_controller_constructor'][] = array(
    'class'    => 'Identity_session_guard',
    'function' => 'check',
    'filename' => 'Identity_session_guard.php',
    'filepath' => 'hooks',
);
