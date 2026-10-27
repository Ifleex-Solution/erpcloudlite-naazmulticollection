<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['new_customer_receive']          = "customer_receive/customer_receive/new_customer_receive";
$route['manage_customer_receive']       = "customer_receive/customer_receive/manage_customer_receive";
$route['getCustomerDueSummary']         = "customer_receive/customer_receive/getCustomerDueSummary";
$route['getCustomerDueInvoices']        = "customer_receive/customer_receive/getCustomerDueInvoices";
$route['saveCustomerReceive']           = "customer_receive/customer_receive/saveCustomerReceive";
$route['getManageCustomerReceive']      = "customer_receive/customer_receive/getManageCustomerReceive";
$route['deleteCustomerReceive']         = "customer_receive/customer_receive/deleteCustomerReceive";
$route['getCustomerReceiveForEdit']     = "customer_receive/customer_receive/getCustomerReceiveForEdit";
$route['printCustomerReceive']          = "customer_receive/customer_receive/printCustomerReceive";
