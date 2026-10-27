<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['add_invoice']         = "invoice/invoice/bdtask_invoice_form";
$route['new_pos']         = "invoice/invoice/bdtask_new_pos";
$route['touch_invoice']              = "invoice/invoice/touch_invoice_form";
$route['touch_invoice/(:num)']       = "invoice/invoice/touch_invoice_form/$1";
$route['ti_search_products']         = "invoice/invoice/ti_search_products";
$route['new_quotation']         = "invoice/invoice/bdtask_quotation_form";
$route['manage_quotation']         = "invoice/invoice/bdtask_quotation_list";

$route['new_sales_return']         = "invoice/invoice/bdtask_salesreturn_form";
$route['manage_sales_return']         = "invoice/invoice/bdtask_salesreturn_list";


//$route['pos_invoice']         = "invoice/invoice/bdtask_pos_invoice";
//$route['gui_pos']             = "invoice/invoice/bdtask_gui_pos";
$route['invoice_list']        = "invoice/invoice/bdtask_invoice_list";
$route['invoice_details/(:num)'] = 'invoice/invoice/bdtask_invoice_details/$1';
//$route['delivery_invoice_details/(:num)'] = 'invoice/invoice/bdtask_delivery_invoice_details/$1';
// $route['invoice_pad_print/(:num)'] = 'invoice/invoice/bdtask_invoice_pad_print/$1';
// $route['pos_print/(:num)']    = 'invoice/invoice/bdtask_invoice_pos_print/$1';
// $route['invoice_pos_print']    = 'invoice/invoice/bdtask_pos_print_direct';
// $route['download_invoice/(:num)']  = 'invoice/invoice/bdtask_download_invoice/$1';
// $route['invoice_edit/(:num)'] = 'invoice/invoice/bdtask_edit_invoice/$1';
// $route['invoice_print'] = 'invoice/invoice/invoice_inserted_data_manual';

$route['edit_invoice/(:num)']         = "invoice/invoice/bdtask_invoice_form/$1";
$route['edit_quotation/(:num)']         = "invoice/invoice/bdtask_quotation_form/$1";
$route['edit_sales_return/(:num)']         = "invoice/invoice/bdtask_salesreturn_form/$1";
$route['convert_sale/(:any)']         = "invoice/invoice/bdtask_convertsales_form/$1";



$route['edit_invoice2/(:num)']         = "invoice/invoice/bdtask_new_pos/$1";
$route['dupl_sales']        = "invoice/invoice/bdtask_dupl_sales";


// $route['terms_list'] = 'invoice/invoice/bdtask_terms_list';
// $route['terms_add'] = 'invoice/invoice/bdtask_terms_form';
// $route['terms_add/(:num)'] = 'invoice/invoice/bdtask_terms_form/$1';

