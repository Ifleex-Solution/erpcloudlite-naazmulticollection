<?php
defined('BASEPATH') OR exit('No direct script access allowed');



$route['download_bulk_csv/([a-z]+)/(:num)'] = "product/product/download_bulk_csv/$1/$2";

$route['category_form']        = "product/product/bdtask_category_form";
$route['category_form/(:num)'] = 'product/product/bdtask_category_form/$1';
$route['category_list']        = "product/product/bdtask_category_list";

$route['unit_form']            = "product/product/bdtask_unit_form";
$route['unit_form/(:num)']     = 'product/product/bdtask_unit_form/$1';
$route['unit_list']            = "product/product/bdtask_unit_list";

$route['product_form']         = "product/product/bdtask_product_form";
$route['product_form/(:any)']  = "product/product/bdtask_product_form/$1";
$route['product_list']         = "product/product/bdtask_product_list";
$route['save_product_ajax']      = "product/product/save_product";
$route['get_product_form_data']  = "product/product/get_product_form_data";
$route['barcode/(:any)']       = "product/product/barcode_print/$1";
$route['qrcode/(:any)']        = "product/product/qrgenerator/$1";
$route['bulk_products']               = "product/product/bdtask_csv_product";
$route['save_product_bulk_log']       = "product/product/save_product_bulk_log";
$route['checkBulkProductUpload']      = "product/product/checkBulkProductUpload";
$route['delete_bulk_product/(:num)']               = "product/product/delete_bulk_product/$1";
$route['get_bulk_product_details/(:num)']          = "product/product/get_bulk_product_details/$1";
$route['data_uploader']                            = "product/product/bdtask_data_loader";
$route['bulk_conversionratio']                     = "product/product/bdtask_csv_conversionratio";
$route['save_conversionratio_from_csv']            = "product/product/save_conversionratio_from_csv";
$route['save_conversionratio_bulk_log']            = "product/product/save_conversionratio_bulk_log";
$route['checkBulkConversionratioUpload']           = "product/product/checkBulkConversionratioUpload";
$route['get_bulk_conversionratio_details/(:num)']  = "product/product/get_bulk_conversionratio_details/$1";
$route['delete_bulk_conversionratio/(:num)']       = "product/product/delete_bulk_conversionratio/$1";
$route['product_details/(:any)']= "product/product/bdtask_product_details/$1";


$route['brand_form']        = "product/product/bdtask_brand_form";
$route['brand_form/(:num)'] = 'product/product/bdtask_brand_form/$1';
$route['brand_list']        = "product/product/bdtask_brand_list";


$route['oop_form']        = "product/product/bdtask_oop_form";
$route['oop_form/(:num)'] = 'product/product/bdtask_oop_form/$1';
$route['oop_list']        = "product/product/bdtask_oop_list";



$route['subcategory_form']        = "product/product/bdtask_subcategory_form";
$route['subcategory_form/(:num)'] = 'product/product/bdtask_subcategory_form/$1';
$route['subcategory_list']        = "product/product/bdtask_subcategory_list";

$route['conversionratio_form']        = "product/product/bdtask_conversionratio_form";
$route['conversionratio_form/(:num)'] = 'product/product/bdtask_conversionratio_form/$1';
$route['conversionratio_list']        = "product/product/bdtask_conversionratio_list";


$route['add_product_group']         = "product/product/bdtask_productgroup_form";
$route['edit_product_group/(:any)']  = "product/product/bdtask_productgroup_form/$1";
$route['product_grouplist']         = "product/product/bdtask_product_grouplist";

$route['upload_product_image'] = "product/product/upload_product_image";
$route['labelprint']           = "product/product/label_print";
$route['barcode']       = "product/product/barcode_print";

$route['save_brand_from_csv']          = "product/product/save_brand_from_csv";
$route['save_category_from_csv']       = "product/product/save_category_from_csv";
$route['save_subcategory_from_csv']    = "product/product/save_subcategory_from_csv";
$route['save_unit_from_csv']           = "product/product/save_unit_from_csv";

$route['save_brand_bulk_log']                      = "product/product/save_brand_bulk_log";
$route['checkBulkBrandUpload']                     = "product/product/checkBulkBrandUpload";
$route['get_bulk_brand_details/(:num)']            = "product/product/get_bulk_brand_details/$1";
$route['delete_bulk_brand/(:num)']                 = "product/product/delete_bulk_brand/$1";

$route['save_category_bulk_log']                   = "product/product/save_category_bulk_log";
$route['checkBulkCategoryUpload']                  = "product/product/checkBulkCategoryUpload";
$route['get_bulk_category_details/(:num)']         = "product/product/get_bulk_category_details/$1";
$route['delete_bulk_category/(:num)']              = "product/product/delete_bulk_category/$1";

$route['save_subcategory_bulk_log']                = "product/product/save_subcategory_bulk_log";
$route['checkBulkSubcategoryUpload']               = "product/product/checkBulkSubcategoryUpload";
$route['get_bulk_subcategory_details/(:num)']      = "product/product/get_bulk_subcategory_details/$1";
$route['delete_bulk_subcategory/(:num)']           = "product/product/delete_bulk_subcategory/$1";

$route['save_unit_bulk_log']                       = "product/product/save_unit_bulk_log";
$route['checkBulkUnitUpload']                      = "product/product/checkBulkUnitUpload";
$route['get_bulk_unit_details/(:num)']             = "product/product/get_bulk_unit_details/$1";
$route['delete_bulk_unit/(:num)']                  = "product/product/delete_bulk_unit/$1";

$route['save_paymentmethod_bulk_log']              = "product/product/save_paymentmethod_bulk_log";
$route['checkBulkPaymentMethodUpload']             = "product/product/checkBulkPaymentMethodUpload";
$route['get_bulk_paymentmethod_details/(:num)']    = "product/product/get_bulk_paymentmethod_details/$1";
$route['delete_bulk_paymentmethod/(:num)']         = "product/product/delete_bulk_paymentmethod/$1";

$route['save_productgroup_from_csv']               = "product/product/save_productgroup_from_csv";

$route['save_branch_bulk_log']                     = "product/product/save_branch_bulk_log";
$route['checkBulkBranchUpload']                    = "product/product/checkBulkBranchUpload";
$route['get_bulk_branch_details/(:num)']           = "product/product/get_bulk_branch_details/$1";
$route['delete_bulk_branch/(:num)']                = "product/product/delete_bulk_branch/$1";

$route['save_store_bulk_log']                      = "product/product/save_store_bulk_log";
$route['checkBulkStoreUpload']                     = "product/product/checkBulkStoreUpload";
$route['get_bulk_store_details/(:num)']            = "product/product/get_bulk_store_details/$1";
$route['delete_bulk_store/(:num)']                 = "product/product/delete_bulk_store/$1";

$route['save_customer_bulk_log']                   = "product/product/save_customer_bulk_log";
$route['checkBulkCustomerUpload']                  = "product/product/checkBulkCustomerUpload";
$route['get_bulk_customer_details/(:num)']         = "product/product/get_bulk_customer_details/$1";
$route['delete_bulk_customer/(:num)']              = "product/product/delete_bulk_customer/$1";

$route['save_supplier_bulk_log']                   = "product/product/save_supplier_bulk_log";
$route['checkBulkSupplierUpload']                  = "product/product/checkBulkSupplierUpload";
$route['get_bulk_supplier_details/(:num)']         = "product/product/get_bulk_supplier_details/$1";
$route['delete_bulk_supplier/(:num)']              = "product/product/delete_bulk_supplier/$1";

$route['save_service_bulk_log']                    = "product/product/save_service_bulk_log";
$route['checkBulkServiceUpload']                   = "product/product/checkBulkServiceUpload";
$route['get_bulk_service_details/(:num)']          = "product/product/get_bulk_service_details/$1";
$route['delete_bulk_service/(:num)']               = "product/product/delete_bulk_service/$1";

$route['save_productgroup_bulk_log']               = "product/product/save_productgroup_bulk_log";
$route['checkBulkProductGroupUpload']              = "product/product/checkBulkProductGroupUpload";
$route['get_bulk_productgroup_details/(:num)']     = "product/product/get_bulk_productgroup_details/$1";
$route['delete_bulk_productgroup/(:num)']          = "product/product/delete_bulk_productgroup/$1";

$route['save_openingstock_bulk_log']               = "product/product/save_openingstock_bulk_log";
$route['checkBulkOpeningStockUpload']              = "product/product/checkBulkOpeningStockUpload";
$route['get_bulk_openingstock_details/(:num)']     = "product/product/get_bulk_openingstock_details/$1";
$route['delete_bulk_openingstock/(:num)']          = "product/product/delete_bulk_openingstock/$1";

$route['save_stockbatch_from_csv']                 = "product/product/save_stockbatch_from_csv";
$route['save_stockbatch_bulk_log']                 = "product/product/save_stockbatch_bulk_log";
$route['checkBulkStockBatchUpload']                = "product/product/checkBulkStockBatchUpload";
$route['get_bulk_stockbatch_details/(:num)']       = "product/product/get_bulk_stockbatch_details/$1";
$route['delete_bulk_stockbatch/(:num)']            = "product/product/delete_bulk_stockbatch/$1";

$route['save_designation_from_csv']                = "product/product/save_designation_from_csv";
$route['save_designation_bulk_log']                = "product/product/save_designation_bulk_log";
$route['checkBulkDesignationUpload']               = "product/product/checkBulkDesignationUpload";
$route['get_bulk_designation_details/(:num)']      = "product/product/get_bulk_designation_details/$1";
$route['delete_bulk_designation/(:num)']           = "product/product/delete_bulk_designation/$1";

$route['save_employee_from_csv']                   = "product/product/save_employee_from_csv";
$route['save_employee_bulk_log']                   = "product/product/save_employee_bulk_log";
$route['checkBulkEmployeeUpload']                  = "product/product/checkBulkEmployeeUpload";
$route['get_bulk_employee_details/(:num)']         = "product/product/get_bulk_employee_details/$1";
$route['delete_bulk_employee/(:num)']              = "product/product/delete_bulk_employee/$1";

$route['save_stockadjustment_bulk_log']                = "product/product/save_stockadjustment_bulk_log";
$route['checkBulkStockAdjustmentUpload']               = "product/product/checkBulkStockAdjustmentUpload";
$route['get_bulk_stockadjustment_details/(:num)']      = "product/product/get_bulk_stockadjustment_details/$1";
$route['delete_bulk_stockadjustment/(:num)']           = "product/product/delete_bulk_stockadjustment/$1";