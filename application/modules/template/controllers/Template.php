<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    
 define('UPDATE_INFO_URL','https://update.bdtask.com/saleserp/autoupdate/update_info');
 require_once("./vendor/Config.php");

class Template extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SESSION sql_mode = ""');
        $this->load->model(array(
            'template_model',
            'service/service_model',
            'dashboard/credit_alert_model'
        ));
    }
 
    public function layout($data)
    {  
        $id = $this->session->userdata('id');
        $settingdata           = $this->template_model->setting();
        // $company_data          = $this->template_model->bdtask_company_info();
        $data['setting']       = $settingdata;
        // $data['company_info']  = $company_data;
        // $data['company_name']  = $company_data[0]['company_name'];
        $data['discount_type'] = $settingdata->discount_type;
        $data['max_version']   = @file_get_contents(UPDATE_INFO_URL);
        $data['current_version'] = $this->current_version();
        $data['bank_list']     = $this->template_model->bdtask_bank_list();
        $data['currency']      = $settingdata->currency;
        $data['currency_details']    = $this->service_model->web_setting();
        
        // Ensure company_info includes instance_type
        // if (empty($company_data) || !isset($company_data[0]['instance_type'])) {
            $encryption_key = Config::$encryption_key;
            
            $company_id = $this->session->userdata('user_level2');

            $company_query = "SELECT 
                company_id,
                CAST(AES_DECRYPT(company_name, '{$encryption_key}') AS CHAR) AS company_name,
                CAST(AES_DECRYPT(header_text, '{$encryption_key}') AS CHAR) AS header_text,
                CAST(AES_DECRYPT(email, '{$encryption_key}') AS CHAR) AS email,
                CAST(AES_DECRYPT(address, '{$encryption_key}') AS CHAR) AS address,
                CAST(AES_DECRYPT(mobile, '{$encryption_key}') AS CHAR) AS mobile,
                CAST(AES_DECRYPT(website, '{$encryption_key}') AS CHAR) AS website,
                status
            FROM company_information
            WHERE status = 1 
            AND company_id = '$company_id'
            LIMIT 1";
            
            $data['company_info'] = $this->db->query($company_query)->result_array();

            $company_query = "SELECT 
            instance_type
        FROM company_information
        WHERE status = 1 
        LIMIT 1";

            $data['company_info2'] = $this->db->query($company_query)->result_array();
        // }

        $data['position']      = $settingdata->currency_position;
        $data['out_of_stocks'] = $this->template_model->out_of_stock_count();
        $data['expiry_alert_count'] = $this->template_model->expiry_alert_count();
        // Icon visibility is permission-only (header.php wraps each <li> in
        // a permission check already); the badge count itself is likewise
        // not gated by the Preference toggle — a disabled pop-up still
        // shows a live count on its icon, only the login pop-up itself
        // is suppressed (see Credit_alert::alert_counts()).
        $data['customer_credit_count'] = $this->credit_alert_model->customer_credit_count();
        $data['supplier_credit_count'] = $this->credit_alert_model->supplier_credit_count();
        $this->load->view('layout', $data);
    }
 
    public function login($data)
    { 
        $data['setting'] = $this->template_model->setting();
        $this->load->view('login', $data);
    }


    private function current_version(){

        //Current Version
        $product_version = '';
        $path = FCPATH.'system/core/compat/lic.php'; 
        if (file_exists($path)) {
            
            // Open the file
            $whitefile = file_get_contents($path);

            $file = fopen($path, "r");
            $i    = 0;
            $product_version_tmp = array();
            $product_key_tmp = array();
            while (!feof($file)) {
                $line_of_text = fgets($file);

                if (strstr($line_of_text, 'product_version')  && $i==0) {
                    $product_version_tmp = explode('=', strstr($line_of_text, 'product_version'));
                    $i++;
                }                
            }
            fclose($file);

            $product_version = trim(@$product_version_tmp[1]);
            $product_version = ltrim(@$product_version, '\'');
            $product_version = rtrim(@$product_version, '\';');

            return @$product_version;
            
        } else {
            //file is not exists
            return false;
        }
        
    }
 
}