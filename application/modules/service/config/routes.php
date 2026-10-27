<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['add_service']           = "service/service/bdtask_service_form";
$route['manage_service']        = "service/service/bdtask_manage_service";
$route['edit_service/(:num)']   = "service/service/bdtask_edit_service/$1";
$route['service_invoice/(:any)']= "service/service/service_invoice_view/$1";
$route['manage_service_invoice/(:num)']= "service/service/manage_service_invoice/$1";

$route['add_service_invoice']   = "service/service/bdtask_service_invoice_form";
$route['service_details/(:num)'] = 'service/service/bdtask_service_details/$1';
$route['manage_service_invoice']= "service/service/manage_service_invoice";
$route['edit_service_invoice/(:any)']= "service/service/bdtask_service_invoice_form/$1";


$route['serviceorder_invoice']   = "service/service/bdtask_serviceorder_invoice_form";
$route['manage_serviceorder_invoice']= "service/service/manage_serviceorder_invoice";
$route['edit_serviceorder_invoice/(:any)']= "service/service/bdtask_serviceorder_invoice_form/$1";
$route['convert_service/(:any)']         = "service/service/bdtask_convertservice_form/$1";

$route['save_service_from_csv']  = "service/service/save_service_from_csv";
