<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// $route['reports/(:num)'] = 'report/report/bdtask_purchase_edit_form/$1';
// $route['closing_form']   = "report/report/bdtask_cash_closing";
// $route['closing_report'] = "report/report/bdtask_closing_report";
// $route['closing_report_search'] = "report/report/bdtask_closing_report_search";
// $route['todays_report']  = "report/report/bdtask_todays_report";
// $route['todays_customer_received']  = "report/report/bdtask_todays_customer_received";
// $route['todays_customerwise_received']  = "report/report/bdtask_customerwise_received";
// $route['datewise_sales_report']  = "report/report/bdtask_datewise_sales_report";
// $route['invoice_wise_due_report']= "report/report/bdtask_invoice_wise_due_report";
// $route['shipping_cost_report']= "report/report/bdtask_shippingcost_report";
// $route['sales_return']         = "report/report/bdtask_sales_return";
// $route['supplier_returns']      = "report/report/bdtask_supplier_return";
// $route['tax_report']           = "report/report/bdtask_tax_report";
// $route['profit_report']        = "report/report/bdtask_profit_report";

$route['sales_report']  = "report/report/bdtask_todays_sales_report";
$route['userwise_sales_report']  = "report/report/bdtask_userwise_sales_report";
$route['product_wise_sales_report']= "report/report/bdtask_sale_report_productwise";
$route['category_sales_report']= "report/report/bdtask_categorywise_sales_report";
$route['purchase_report']     = "report/report/bdtask_purchase_report";
$route['purchase_report_categorywise']= "report/report/bdtask_purchase_report_category_wise";
$route['purchase_report_productwise']= "report/report/bdtask_purchase_report_product_wise";
$route['stock']          = "report/report/bdtask_stock_report";
$route['live_stock_report']          = "report/report/bdtask_livestock_report";
$route['stock_audit_report']     = "report/report/bdtask_stock_audit_report";

$route['sales_order_report']  = "report/report/bdtask_sales_order_report";
$route['sales_return_report']  = "report/report/bdtask_sales_return_report";
$route['purchase_order_report']  = "report/report/bdtask_purchase_order_report";
$route['purchase_return_report']  = "report/report/bdtask_purchase_return_report";
$route['grn_report']  = "report/report/bdtask_grn_report";
$route['gdn_report']  = "report/report/bdtask_gdn_report";
$route['gross_profit_report']  = "report/report/bdtask_gross_profit_report";
$route['gross_profit_category_report']  = "report/report/bdtask_gross_profit_category_report";
$route['service_report']  = "report/report/bdtask_todays_service_report";
$route['product_batch_summary_report']  = "report/report/bdtask_product_batch_summary_report";
$route['service_order_report']  = "report/report/bdtask_service_order_report";

$route['payment_report']  = "report/report/bdtask_payment_report";
$route['receipt_report']  = "report/report/bdtask_receipt_report";
$route['contra_voucher_report']  = "report/report/bdtask_contra_voucher_report";

$route['customer_receive_report']  = "report/report/bdtask_customer_receive_report";
$route['customer_due_report']  = "report/report/bdtask_customer_due_report";
$route['generate_customerreceivereport']  = "report/report/generate_customerreceivereport";
$route['generate_customerduereport']  = "report/report/generate_customerduereport";

$route['supplier_payment_report']  = "report/report/bdtask_supplier_payment_report";
$route['supplier_due_report']  = "report/report/bdtask_supplier_due_report";
$route['generate_supplierpaymentreport']  = "report/report/generate_supplierpaymentreport";
$route['generate_supplierduereport']  = "report/report/generate_supplierduereport";




$route['generate_salesreportinvoice']  = "report/report/generate_salesreportinvoice";
$route['generate_salesreportproduct']  = "report/report/generate_salesreportproduct";
$route['generate_salesreportcategory']  = "report/report/generate_salesreportcategory";
$route['generate_purchasereportinvoice']  = "report/report/generate_purchasereportinvoice";
$route['generate_purchasereportcategory']  = "report/report/generate_purchasereportcategory";
$route['generate_salesreportemployee']  = "report/report/generate_salesreportemployee";
$route['generate_stockreport']  = "report/report/generate_stockreport";
$route['generate_cashbook']  = "report/report/generate_cashbook";
$route['generate_livestockreport']  = "report/report/generate_livestockreport";
$route['generate_salesorderreportinvoice']  = "report/report/generate_salesorderreportinvoice";
$route['generate_purchasereturnreportinvoice']  = "report/report/generate_purchasereturnreportinvoice";
$route['generate_purchaseorderreportinvoice']  = "report/report/generate_purchaseorderreportinvoice";
$route['generate_salesreturnreportinvoice']  = "report/report/generate_salesreturnreportinvoice";
$route['generate_salesorderreportinvoice']  = "report/report/generate_salesorderreportinvoice";
$route['generate_gdnreportinvoice']  = "report/report/generate_gdnreportinvoice";
$route['generate_servicereportinvoice']  = "report/report/generate_servicereportinvoice";
$route['generate_serviceorderreportinvoice']  = "report/report/generate_serviceorderreportinvoice";
$route['generate_grnreportinvoice']  = "report/report/generate_grnreportinvoice";
$route['generate_gross_profit_report']  = "report/report/generate_gross_profit_report";
$route['generate_grossprofitreportcategorywise']  = "report/report/generate_grossprofitreportcategorywise";
$route['generate_product_batch_summary_report']  = "report/report/generate_product_batch_summary_report";
$route['generate_purchasereportproduct']  = "report/report/generate_purchasereportproduct";
$route['generate_voucherReport']  = "report/report/generate_voucherReport";

$route['profit_report_invoicewise']          = "report/report/bdtask_profit_report_invoicewise";
$route['generate_profitreportinvoicewise']   = "report/report/generate_profitreportinvoicewise";

