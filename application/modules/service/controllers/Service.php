<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Service extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $timezone = $this->db->select('timezone')->from('web_setting')->get()->row();
        date_default_timezone_set($timezone->timezone);
        $this->load->model(array(
            'service_model',
            'account/Accounts_model'
        ));
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
    }

    public function bdtask_service_form()
    {
        $data['title']      = display('add_service');
        $data['module']     = 'service';
        $data['taxfield']   = $this->service_model->tax_fields();
        $data['vattaxinfo'] = $this->service_model->vat_tax_setting();
        // generate a preview service_code if DB column exists
        if ($this->db->field_exists('service_code', 'product_service')) {
            $data['service_code'] = $this->service_model->generate_service_code();
        } else {
            $data['service_code'] = '';
        }

        $data['page']       = 'add_service_form';

        if (!$this->permission1->method('manage_service', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function check_duplicate_service()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('service_id')->from('product_service')->where('service_name', $value);
        if (!empty($exclude_id)) { $this->db->where('service_id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A service with this name already exists.' : '']);
    }

    public function insert_service_quick()
    {
        $service_name = trim($this->input->post('service_name', TRUE));
        $charge       = trim($this->input->post('charge', TRUE));
        $service_vat  = trim($this->input->post('service_vat', TRUE));

        if (empty($service_name)) {
            echo json_encode(['status' => 'Error', 'message' => 'Service name is required']); return;
        }
        if ($charge === '') {
            echo json_encode(['status' => 'Error', 'message' => 'Charge is required']); return;
        }

        $data = [
            'service_name' => $service_name,
            'charge'       => $charge,
            'service_vat'  => $service_vat !== '' ? $service_vat : 0,
            'status'       => 1,
            'description'  => trim($this->input->post('description', TRUE)),
        ];

        $new_id = $this->service_model->service_entry($data);
        if ($new_id === FALSE) {
            echo json_encode(['status' => 'Error', 'message' => 'Service with this name already exists']);
        } else {
            echo json_encode(['status' => 'Success', 'id' => $new_id, 'service_name' => $service_name, 'charge' => $charge]);
        }
    }

    public function insert_service()
    {
        $tablecolumn = $this->db->list_fields('product_service');
        $num_column  = count($tablecolumn) - 7;
        $taxfield    = [];
        for ($i = 0; $i < $num_column; $i++) {
            $taxfield[$i] = 'tax' . $i;
        }
        foreach ($taxfield as $key => $value) {
            $data[$value] = $this->input->post($value) / 100;
        }
       
        $data['service_code'] = $this->input->post('service_code', true);
        $data['service_name'] = $this->input->post('service_name', true);
        $data['charge']       = $this->input->post('charge', true);
        $data['status']       = $this->input->post('status', true);

        $data['description']  = $this->input->post('description', true);
        $data['service_vat']  = $this->input->post('service_vat', true);

        $result = $this->service_model->service_entry($data);
        $base_url = base_url();

        if ($result) {

            // $this->session->set_flashdata(array('message' => display('successfully_added')));
            // redirect(base_url('manage_service'));

            if ($this->input->post('button', true) === "add-another") {
                echo '
                <script type="text/javascript">
                alert("Service Details Saved successfully");
                window.location.href = "' . $base_url . 'add_service";
               </script>';
                exit;
            } else {

                echo '<script type="text/javascript">
                alert("Service Details Saved successfully");
                window.location.href = "' . $base_url . 'manage_service";
               </script>';
            }
        } else {
            echo '<script type="text/javascript">
            alert("Service Details Already Inserted");
            window.location.href = "' . $base_url . 'add_service";
           </script>';
            redirect(base_url('add_service'));
        }
    }



    public function bdtask_manage_service()
    {
        $service_list = $this->service_model->service_list2();
        $tablecolumn  = $this->db->list_fields('product_service');
        $num_column   = count($tablecolumn) - 7;
        $data = array(
            'title'        => display('manage_service'),
            'service_list' => $service_list,
            'rowumber'     => $num_column,
            'taxfiled'     => $this->service_model->tax_fields(),
            'vattaxinfo'   => $this->service_model->vat_tax_setting(),
        );
        if (!$this->permission1->method('manage_service', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['module']   = 'service';
        $data['page']     = 'service';
        echo modules::run('template/layout', $data);
    }


    public function bdtask_edit_service($service_id)
    {
        if (!$this->permission1->method('manage_service', 'update')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $service_detail = $this->service_model->retrieve_service_editdata($service_id);
        $taxfield = $this->service_model->tax_fields();
        $i = 0;
        foreach ($taxfield as $taxs) {

            $tax = 'tax' . $i;
            $data[$tax] = $service_detail[0][$tax] * 100;
            $i++;
        }

        $data['title']         = display('service_edit');
        $data['vattaxinfo']    = $this->service_model->vat_tax_setting();
        $data['service_id']    = $service_detail[0]['service_id'];
        $data['service_code']  = !empty($service_detail[0]['service_code']) ? $service_detail[0]['service_code'] : '';
        $data['charge']        = $service_detail[0]['charge'];
        $data['service_name']  = $service_detail[0]['service_name'];
        $data['description']   = $service_detail[0]['description'];
        $data['service_vat']   = $service_detail[0]['service_vat'];
        $data['status']   = $service_detail[0]['status'];
        $data['servicedetails'] = $service_detail;
        $data['taxfield']      = $taxfield;
        $data['module']        = 'service';
        $data['page']          = 'edit_service_form';
        echo modules::run('template/layout', $data);
    }


    public function service_update()
    {
        if (!$this->permission1->method('manage_service', 'update')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $service_id  = $this->input->post('service_id', true);
        $tablecolumn = $this->db->list_fields('product_service');
        $num_column  = count($tablecolumn) - 7;
        $base_url = base_url();

       
        $data['service_name'] = $this->input->post('service_name', true);
        $data['charge']       = $this->input->post('charge', true);
        $data['description']  = $this->input->post('description', true);
        $data['service_vat']  = $this->input->post('service_vat', true);
        $data['service_code'] = $this->input->post('service_code', true);
        $data['status']  = $this->input->post('status', true);


        $this->service_model->update_service($data, $service_id);
        // $this->session->set_flashdata(array('message' => display('successfully_updated')));
        // redirect(base_url('manage_service'));

        echo '<script type="text/javascript">
                alert("Service Details Updated successfully");
                window.location.href = "' . $base_url . 'manage_service";
               </script>';
    }


    public function service_delete($service_id)
    {
        $secbranchcount = $this->db->from('service_details')
        ->where('service', $service_id)
        ->count_all_results();

        $base_url = base_url();

        if ($secbranchcount == 0) {

            if ($this->service_model->delete_service($service_id)) {
                echo '<script type="text/javascript">
                alert("Service Details Deleted successfully");
                window.location.href = "' . $base_url . 'manage_service";
               </script>';
            } else {
                echo '<script type="text/javascript">
                 alert("Something went wrong or The Service record is already referenced in another record.");
                 window.location.href = "' . $base_url . 'manage_service";
               </script>';
            }
        } else {
            echo '<script type="text/javascript">
                 alert("Something went wrong or The Service record is already referenced in another record.");
                window.location.href = "' . $base_url . 'manage_service";
               </script>';
        }
    }


    function uploadCsv_service()
    {
        $filename = $_FILES['upload_csv_file']['name'];
        $ext = end(explode('.', $filename));
        $ext = substr(strrchr($filename, '.'), 1);
        if ($ext == 'csv') {
            $count = 0;
            $fp = fopen($_FILES['upload_csv_file']['tmp_name'], 'r') or die("can't open file");

            if (($handle = fopen($_FILES['upload_csv_file']['tmp_name'], 'r')) !== FALSE) {

                while ($csv_line = fgetcsv($fp, 1024)) {
                    //keep this if condition if you want to remove the first row
                    for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                        $insert_csv = array();
                        $insert_csv['service_name'] = (!empty($csv_line[0]) ? $csv_line[0] : null);
                        $insert_csv['charge'] = (!empty($csv_line[1]) ? $csv_line[1] : null);
                        $insert_csv['description'] = (!empty($csv_line[2]) ? $csv_line[2] : null);
                    }
                    $servicedata = array(
                        'service_name'    => $insert_csv['service_name'],
                        'charge'          => $insert_csv['charge'],
                        'description'     => $insert_csv['description'],
                    );
                    if ($count > 0) {
                            $s_id = $this->service_model->service_entry($servicedata);
                            if ($s_id) {
                                $CreateBy = $this->session->userdata('id');
                                $createdate = date('Y-m-d H:i:s');

                                $coa = $this->service_model->headcode();

                                if ($coa->HeadCode != NULL) {
                                    $headcode = $coa->HeadCode + 1;
                                } else {
                                    $headcode = "122000001";
                                }
                            }
                    }
                    $count++;
                }
            }
            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('manage_service'));
        } else {
            $this->session->set_flashdata(array('exception' => 'Please Import Only Csv File'));
            redirect(base_url('add_service'));
        }
    }


    

  
    public function getservice_list()
    {
        echo json_encode($this->service_model->service_list2());
    }


    public function bdtask_showpaymentmodal()
    {
        $is_credit =  $this->input->post('is_credit_edit', TRUE);
        $data['is_credit'] = $is_credit;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->service_model->pmethod_dropdown();
        } else {

            $data['all_pmethod'] = $this->service_model->pmethod_dropdown_new();
        }
        $this->load->view('service/newpaymentveiw', $data);
    }

    public function customer_autocomplete()
    {
        $customer_id     = $this->input->post('customer_id', TRUE);
        $customer_info   = $this->service_model->customer_search($customer_id);

        if ($customer_info) {
            $json_customer[''] = '';
            foreach ($customer_info as $value) {
                $json_customer[] = array('label' => $value['customer_name'], 'value' => $value['customer_id']);
            }
        } else {
            $json_customer[] = 'No Record found';
        }
        echo json_encode($json_customer);
    }


    //customer previous due
    public function previous()
    {
        $customer_id = $this->input->post('customer_id', TRUE);
        $this->db->select("a.*,b.HeadCode,((select ifnull(sum(Debit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)-(select ifnull(sum(Credit),0) from acc_transaction where COAID= `b`.`HeadCode` AND IsAppove = 1)) as balance");
        $this->db->from('customer_information a');
        $this->db->join('acc_coa b', 'a.customer_id = b.customer_id', 'left');
        $this->db->where('a.customer_id', $customer_id);
        $result = $this->db->get()->result_array();
        $balance = $result[0]['balance'];
        $b = (!empty($balance) ? $balance : 0);
        if ($b) {
            echo  $b;
        } else {
            echo  $b;
        }
    }


    // Service retrieve
    public function retrieve_service_data_inv()
    {
        $service_id  = $this->input->post('service_id', true);
        $service_info = $this->service_model->get_total_service_invoic($service_id);
        echo json_encode($service_info);
    }

    public function autoapprove($invoice_id)
    {

        $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $invoice_id)->where('status', 0)->get()->result();
        foreach ($vouchers as $value) {
            # code...
            $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
        }
        return true;
    }



    // Service Invoice Entry
    public function insert_service_invoice()
    {

        // $finyear = $this->input->post('finyear', true);
        // if ($finyear <= 0) {
        //     $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
        //     redirect("add_service_invoice");
        // } else {
        $invoice_id = $this->service_model->invoice_entry();

        // $mailsetting = $this->db->select('*')->from('email_config')->get()->result_array();

        // $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
        // if ($setting_data[0]['is_autoapprove_v'] == 1) {

        //     $new = $this->autoapprove($invoice_id);
        // }

        // if ($mailsetting[0]['isservice'] == 1) {
        //     $mail = $this->invoice_pdf_generate($invoice_id);

        //     if ($mail == 0) {
        //         $this->session->set_userdata(array('exception' => display('please_config_your_mail_setting')));
        //     }
        // }

        // $this->session->set_userdata(array('message' => display('successfully_added')));
        // redirect(base_url('service_details/' . $invoice_id));
        // }

        $base_url = base_url();
        echo '
<script type="text/javascript">
    alert("Service Details Saved successfully");
    window.location.href = "' . $base_url . 'service_details/' . $invoice_id . '";
</script>';
    }



    public function invoice_pdf_generate($invoice_id = null)
    {
        $id = $invoice_id;
        $employee_list    = $this->service_model->employee_list();
        $service_inv_main = $this->service_model->service_invoice_updata($invoice_id);
        $customer_info    =  $this->service_model->customer_info($service_inv_main[0]['customer_id']);
        $taxinfo          = $this->service_model->service_invoice_taxinfo($invoice_id);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $taxfield         = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();

        $subTotal_quantity = 0;
        $subTotal_discount = 0;
        $subTotal_ammount  = 0;

        if (!empty($service_inv_main)) {
            foreach ($service_inv_main as $k => $v) {
                $service_inv_main[$k]['final_date'] = $this->occational->dateConvert($service_inv_main[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $service_inv_main[$k]['qty'];
                $subTotal_ammount  = $subTotal_ammount + $service_inv_main[$k]['total'];
            }

            $i = 0;
            foreach ($service_inv_main as $k => $v) {
                $i++;
                $service_inv_main[$k]['sl'] = $i;
            }
        }
        $name    = $customer_info->customer_name;
        $email   = $customer_info->customer_email;
        $data = array(
            'title'           => display('service_details'),
            'employee_list'   => $employee_list,
            'invoice_id'      => $service_inv_main[0]['voucher_no'],
            'final_date'      => $service_inv_main[0]['final_date'],
            'customer_id'     => $service_inv_main[0]['customer_id'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'details'         => $service_inv_main[0]['details'],
            'total_amount'    => $service_inv_main[0]['total_amount'],
            'total_discount'  => $service_inv_main[0]['total_discount'],
            'invoice_discount' => $service_inv_main[0]['invoice_discount'],
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'subTotal_quantity' => number_format($subTotal_quantity, 2, '.', ','),
            'total_tax'       => $service_inv_main[0]['total_tax'],
            'total_vat_amnt' => number_format($service_inv_main[0]['total_vat_amnt'], 2, '.', ','),
            'paid_amount'     => $service_inv_main[0]['paid_amount'],
            'due_amount'      => $service_inv_main[0]['due_amount'],
            'shipping_cost'   => $service_inv_main[0]['shipping_cost'],
            'invoice_detail'  => $service_inv_main,
            'taxvalu'         => $taxinfo,
            'taxes'           => $taxfield,
            'stotal'          => $service_inv_main[0]['total_amount'] - $service_inv_main[0]['previous'],
            'employees'       => $service_inv_main[0]['employee_id'],
            'previous'        => $service_inv_main[0]['previous'],
            'company_info'    => $company_info,
            'currency'        => $currency_details[0]['currency'],
            'position'        => $currency_details[0]['currency_position'],
            'discount_type'   => $currency_details[0]['discount_type'],
            'currency_details' => $currency_details,

        );
        $this->load->library('pdfgenerator');
        $html = $this->load->view('service/invoice_download', $data, true);
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents('assets/data/pdf/service/' . $id . '.pdf', $output);
        $file_path = getcwd() . '/assets/data/pdf/service/' . $id . '.pdf';
        $send_email = '';
        if (!empty($email)) {
            $send_email = $this->setmail($email, $file_path, $id, $name);

            if ($send_email) {
                return 1;
            } else {

                return 0;
            }
        }
        return 0;
    }


    public function setmail($email, $file_path, $id = null, $name = null)
    {
        $setting_detail = $this->db->select('*')->from('email_config')->get()->row();

        $subject = 'Service Purchase Information';
        $message = strtoupper($name) . '-' . $id;
        $config = array(
            'protocol'  => $setting_detail->protocol,
            'smtp_host' => $setting_detail->smtp_host,
            'smtp_port' => $setting_detail->smtp_port,
            'smtp_user' => $setting_detail->smtp_user,
            'smtp_pass' => $setting_detail->smtp_pass,
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'wordwrap'  => TRUE
        );

        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $this->email->set_mailtype("html");
        $this->email->from($setting_detail->smtp_user);
        $this->email->to($email);
        $this->email->subject($subject);
        $this->email->message($message);
        $this->email->attach($file_path);
        $check_email = $this->test_input($email);
        if (filter_var($check_email, FILTER_VALIDATE_EMAIL)) {
            if ($this->email->send()) {
                return true;
            } else {

                return false;
            }
        } else {

            return true;
        }
    }

    //Email testing for email
    public function test_input($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    public function service_invoice_data($invoice_id)
    {
        $employee_list    = $this->service_model->employee_list();
        $service_inv_main = $this->service_model->service_invoice_updata($invoice_id);
        $customer_info    =  $this->service_model->customer_info($service_inv_main[0]['customer_id']);
        $taxinfo          = $this->service_model->service_invoice_taxinfo($invoice_id);
        $taxfield         = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();

        $taxreg = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxreg as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;

        $total_discount_amount = 0;

        if (!empty($service_inv_main)) {
            foreach ($service_inv_main as $k => $v) {
                $service_inv_main[$k]['final_date'] = $this->occational->dateConvert($service_inv_main[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $service_inv_main[$k]['qty'];
                $subTotal_ammount = $subTotal_ammount + $service_inv_main[$k]['total'];
                $total_discount_amount = $service_inv_main[$k]['discount_amount'] + $total_discount_amount;
            }

            $i = 0;
            foreach ($service_inv_main as $k => $v) {
                $i++;
                $service_inv_main[$k]['sl'] = $i;
            }
        }
        $data = array(
            'title'         => display('service_details'),
            'employee_list' => $employee_list,
            'invoice_id'    => $service_inv_main[0]['voucher_no'],
            'final_date'    => $service_inv_main[0]['final_date'],
            'customer_id'   => $service_inv_main[0]['customer_id'],
            'customer_name' => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_info' => $customer_info,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email' => $customer_info->customer_email,
            'details'       => $service_inv_main[0]['details'],
            'total_amount'  => number_format($service_inv_main[0]['total_amount'], 2, '.', ','),
            'total_discount' => number_format($service_inv_main[0]['total_discount'], 2, '.', ','),
            'total_discount_cal' => ($service_inv_main[0]['total_discount'] ? $service_inv_main[0]['total_discount'] : 0),
            'total_vat_amnt' => number_format($service_inv_main[0]['total_vat_amnt'], 2, '.', ','),
            'total_vat_amnt_cal' => ($service_inv_main[0]['total_vat_amnt'] ? $service_inv_main[0]['total_vat_amnt'] : 0),
            'invoice_discount' => number_format($service_inv_main[0]['invoice_discount'], 2, '.', ','),
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'subTotal_amount_cal' => $subTotal_ammount,
            'subTotal_quantity' => number_format($subTotal_quantity, 2, '.', ','),
            'total_tax'     => number_format($service_inv_main[0]['total_tax'], 2, '.', ','),
            'paid_amount'   => number_format($service_inv_main[0]['paid_amount'], 2, '.', ','),
            'due_amount'    => number_format($service_inv_main[0]['due_amount'], 2, '.', ','),
            'shipping_cost' => number_format($service_inv_main[0]['shipping_cost'], 2, '.', ','),
            'invoice_detail' => $service_inv_main,
            'taxvalu'       => $taxinfo,
            'taxes'         => $taxfield,
            'stotal'        => $service_inv_main[0]['total_amount'] - $service_inv_main[0]['previous'],
            'employees'     => $service_inv_main[0]['employee_id'],
            'previous'      => number_format($service_inv_main[0]['previous'], 2, '.', ','),
            'previ_am'      => ($service_inv_main[0]['previous']),
            'tax_regno'     => $txregname,
            'product_discount' => $total_discount_amount

        );
        $data['module']     = 'service';
        $data['page']       = 'invoice_html';
        echo modules::run('template/layout', $data);
    }
    public function service_invoice_view($invoice_id)
    {
        $payment_method_list = $this->db->select('*')->from('acc_coa')->where('PHeadName', 'Cash In Boxes')->get()->result();
        $terms_list       = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $employee_list    = $this->service_model->employee_list();
        $service_inv_main = $this->service_model->service_invoice_updata($invoice_id);
        $customer_info    =  $this->service_model->customer_info($service_inv_main[0]['customer_id']);
        $taxinfo          = $this->service_model->service_invoice_taxinfo($invoice_id);
        $taxfield         = $this->db->select('tax_name,default_value')->from('tax_settings')->get()->result_array();

        $taxreg = $this->db->select('*')
            ->from('tax_settings')
            ->where('is_show', 1)
            ->get()
            ->result_array();
        $txregname = '';
        foreach ($taxreg as $txrgname) {
            $regname = $txrgname['tax_name'] . ' Reg No  - ' . $txrgname['reg_no'] . ', ';
            $txregname .= $regname;
        }
        $subTotal_quantity = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;

        if (!empty($service_inv_main)) {
            foreach ($service_inv_main as $k => $v) {
                $service_inv_main[$k]['final_date'] = $this->occational->dateConvert($service_inv_main[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $service_inv_main[$k]['qty'];
                $subTotal_ammount = $subTotal_ammount + $service_inv_main[$k]['total'];
            }

            $i = 0;
            foreach ($service_inv_main as $k => $v) {
                $i++;
                $service_inv_main[$k]['sl'] = $i;
            }
        }
        $user_id          = $service_inv_main[0]['sales_by'];
        $users            = $this->service_model->user_invoice_data($user_id);
        $data = array(
            'title'            => display('service_invoice'),
            'employee_list'    => $employee_list,
            'invoice_id'       => $service_inv_main[0]['voucher_no'],
            'final_date'       => $service_inv_main[0]['final_date'],
            'customer_id'      => $service_inv_main[0]['customer_id'],
            'customer_name'    => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile'  => $customer_info->customer_mobile,
            'customer_email'   => $customer_info->customer_email,
            'details'          => $service_inv_main[0]['details'],
            'total_amount'     => number_format($service_inv_main[0]['total_amount'], 2, '.', ','),
            'total_discount'   => number_format($service_inv_main[0]['total_discount'], 2, '.', ','),
            'total_vat_amnt'   => number_format($service_inv_main[0]['total_vat_amnt'], 2, '.', ','),
            'invoice_discount' => number_format($service_inv_main[0]['invoice_discount'], 2, '.', ','),
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'subTotal_quantity' => number_format($subTotal_quantity, 2, '.', ','),
            'total_vat'        => number_format($service_inv_main[0]['total_vat_amnt'] + $service_inv_main[0]['total_tax'], 2, '.', ','),
            'total_tax'        => number_format($service_inv_main[0]['total_tax'], 2, '.', ','),
            'paid_amount'      => number_format($service_inv_main[0]['paid_amount'], 2, '.', ','),
            'due_amount'       => number_format($service_inv_main[0]['due_amount'], 2, '.', ','),
            'shipping_cost'    => number_format($service_inv_main[0]['shipping_cost'], 2, '.', ','),
            'invoice_detail'   => $service_inv_main,
            'taxvalu'          => $taxinfo,
            'taxes'            => $taxfield,
            'stotal'           => $service_inv_main[0]['total_amount'] - $service_inv_main[0]['previous'],
            'employees'        => $service_inv_main[0]['employee_id'],
            'previous'         => number_format($service_inv_main[0]['previous'], 2, '.', ','),
            'tax_regno'        => $txregname,
            'users_name'       => $users->first_name . ' ' . $users->last_name,
            'all_discount'     => number_format($service_inv_main[0]['invoice_discount'] + $service_inv_main[0]['total_discount'], 2, '.', ','),
            'p_method_list'    => $payment_method_list,
            'terms_list'       => $terms_list,

        );
        $data['module']     = 'service';
        $data['page']       = 'pos_print';
        echo modules::run('template/layout', $data);
    }

    public function manage_service_invoice()
    {
        if (!$this->permission1->method('manage_service_invoice', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']         = display('manage_service_invoice');
        $config["base_url"]    = base_url('manage_service_invoice');
        $config["total_rows"]  = $this->db->count_all('service_invoice');
        $config["per_page"]    = 20;
        $config["uri_segment"] = 2;
        $config["last_link"]   = "Last";
        $config["first_link"]  = "First";
        $config['next_link']   = 'Next';
        $config['prev_link']   = 'Prev';
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"]  = $this->pagination->create_links();
        $data['module'] = "service";
        $data['service'] = $this->service_model->service_invoice_list($config["per_page"], $page);
        $data['page']   = "service_invoice";
        echo Modules::run('template/layout', $data);
    }


    public function service_invoice_edit($invoice_id)
    {
        $employee_list    = $this->service_model->employee_list();
        $service_inv_main = $this->service_model->service_invoice_updata($invoice_id);
        $customer_info    = $this->service_model->customer_info($service_inv_main[0]['customer_id']);



        $data = array(
            'title'           => display('update_service_invoice'),
            'employee_list'   => $employee_list,
            'dbserv_id'       => $service_inv_main[0]['dbserv_id'],
            'invoice_id'      => $service_inv_main[0]['voucher_no'],
            'date'            => $service_inv_main[0]['date'],
            'customer_id'     => $service_inv_main[0]['customer_id'],
            'customer_name'   => $customer_info->customer_name,
            'details'         => $service_inv_main[0]['details'],
            'total_amount'    => $service_inv_main[0]['total_amount'],
            'total_discount'  => $service_inv_main[0]['total_discount'],
            'total_vat_amnt'  => $service_inv_main[0]['total_vat_amnt'],
            'invoice_discount' => $service_inv_main[0]['invoice_discount'],
            'total_tax'       => $service_inv_main[0]['total_tax'],
            'paid_amount'     => $service_inv_main[0]['paid_amount'],
            'due_amount'      => $service_inv_main[0]['due_amount'],
            'shipping_cost'   => $service_inv_main[0]['shipping_cost'],
            'invoice_detail'  => $service_inv_main,
            'stotal'          => $service_inv_main[0]['total_amount'] - $service_inv_main[0]['previous'],
            'employees'       => $service_inv_main[0]['employee_id'],
            'previous'        => $service_inv_main[0]['previous'],
            'is_credit'       => $service_inv_main[0]['is_credit'],

        );
        $data['all_pmethod']  = $this->service_model->pmethod_dropdown_new();
        $data['all_pmethodwith_cr'] = $this->service_model->pmethod_dropdown();
        $data['module']       = 'service';
        $data['page']     = 'update_invoice_form';

        echo modules::run('template/layout', $data);
    }

    public function update_service_invoice()
    {
        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
            redirect("add_service_invoice");
        } else {
            $invoice_id = $this->service_model->invoice_update();

            $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();
            if ($setting_data[0]['is_autoapprove_v'] == 1) {

                $new = $this->autoapprove($invoice_id);
            }
            $mailsetting = $this->db->select('*')->from('email_config')->get()->result_array();

            if ($mailsetting[0]['isservice'] == 1) {
                $mail = $this->invoice_pdf_generate($invoice_id);
            }
            $this->session->set_flashdata(array('message' => display('successfully_updated')));
            redirect(base_url('service_details/' . $invoice_id));
        }
    }


    //pdf download service invoice details
    public function servicedetails_download($invoice_id = null)
    {
        $employee_list    = $this->service_model->employee_list();
        $currency_details = $this->service_model->web_setting();
        $service_inv_main = $this->service_model->service_invoice_updata($invoice_id);
        $customer_info =  $this->service_model->customer_info($service_inv_main[0]['customer_id']);
        $company_info = $this->service_model->company_info();
        $taxinfo = $this->service_model->service_invoice_taxinfo($invoice_id);
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();
        $subTotal_quantity = 0;
        $subTotal_discount = 0;
        $subTotal_ammount = 0;

        if (!empty($service_inv_main)) {
            foreach ($service_inv_main as $k => $v) {
                $service_inv_main[$k]['final_date'] = $this->occational->dateConvert($service_inv_main[$k]['date']);
                $subTotal_quantity = $subTotal_quantity + $service_inv_main[$k]['qty'];
                $subTotal_ammount = $subTotal_ammount + $service_inv_main[$k]['total'];
            }

            $i = 0;
            foreach ($service_inv_main as $k => $v) {
                $i++;
                $service_inv_main[$k]['sl'] = $i;
            }
        }
        $data = array(
            'title'         => display('service_details'),
            'employee_list' => $employee_list,
            'invoice_id'    => $service_inv_main[0]['voucher_no'],
            'final_date'    => $service_inv_main[0]['final_date'],
            'customer_id'   => $service_inv_main[0]['customer_id'],
            'customer_info' => $customer_info,
            'customer_name' => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email' => $customer_info->customer_email,
            'details'       => $service_inv_main[0]['details'],
            'total_amount'  => $service_inv_main[0]['total_amount'],
            'total_discount' => $service_inv_main[0]['total_discount'],
            'invoice_discount' => $service_inv_main[0]['invoice_discount'],
            'total_vat_amnt' => $service_inv_main[0]['total_vat_amnt'],
            'subTotal_ammount' => number_format($subTotal_ammount, 2, '.', ','),
            'subTotal_quantity' => number_format($subTotal_quantity, 2, '.', ','),
            'total_tax'     => $service_inv_main[0]['total_tax'],
            'paid_amount'   => $service_inv_main[0]['paid_amount'],
            'due_amount'    => $service_inv_main[0]['due_amount'],
            'shipping_cost' => $service_inv_main[0]['shipping_cost'],
            'invoice_detail' => $service_inv_main,
            'taxvalu'       => $taxinfo,
            'discount_type' => $currency_details[0]['discount_type'],
            'currency_details' => $currency_details,
            'currency'      => $currency_details[0]['currency'],
            'position'      => $currency_details[0]['currency_position'],
            'taxes'         => $taxfield,
            'stotal'        => $service_inv_main[0]['total_amount'] - $service_inv_main[0]['previous'],
            'employees'     => $service_inv_main[0]['employee_id'],
            'previous'      => $service_inv_main[0]['previous'],
            'company_info'  => $company_info,

        );



        $this->load->library('pdfgenerator');
        $dompdf = new DOMPDF();
        $page = $this->load->view('service/invoice_download', $data, true);
        $file_name = time();
        $dompdf->load_html($page);
        $dompdf->render();
        $output = $dompdf->output();
        file_put_contents("assets/data/pdf/service/$file_name.pdf", $output);
        $filename = $file_name . '.pdf';
        $file_path = base_url() . 'assets/data/pdf/service/' . $filename;

        $this->load->helper('download');
        force_download('./assets/data/pdf/service/' . $filename, NULL);
        redirect("manage_service_invoice");
    }


    public function service_invoic_delete($service_id, $dbserv_id)
    {
        if ($this->service_model->delete_service_invoice($service_id, $dbserv_id)) {
            $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
            $this->session->set_flashdata('exception', display('please_try_again'));
        }
        redirect("manage_service_invoice");
    }











    public function bdtask_service_invoice_form($id = null)
    {
        $data = array(
            'title'         => display('service_invoice'),
            'taxes'         => $this->service_model->tax_fields(),
        );
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['service_list'] = $this->service_model->service_list();
        $data['module']      = 'service';
        // $vatortax            = $this->service_model->vat_tax_setting();
        $data['page']    = "add_invoice_form";
       $data['pagetype']    = "";

                $data['id'] = $id;

     

         $has_access = $id != null
            ? $this->permission1->method('manage_service_invoice', 'update')->access()
            : $this->permission1->method('manage_service_invoice', 'create')->access();

         if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Service Invoice";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

    }

    public function customer_list()
    {
        $encryption_key = Config::$encryption_key;

        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select(' customer_id, AES_DECRYPT(customer_name,"' . $encryption_key . '") AS customer_name')
            ->from('customer_information')
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function employee_list()
    {
        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select('*')
            ->from('employee_history')
            ->where_not_in('id', 1)
            ->where('status', '1')
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

      public function pmethod_dropdown()
    {
        $this->db->select('*')
            ->from('payment_type')
            // ->where('PHeadName', 'Cash')
            ->where('status', '1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function save_service()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        $invoicetype_check = $this->input->post('invoicetype', TRUE);
        if ($invoicetype_check === 'credit' || $invoicetype_check === 'credit_vat') {
            $creditLimitError = $this->check_service_credit_limit($this->input->post('type2', TRUE), (float)$this->input->post('grandTotal', TRUE));
            if ($creditLimitError) {
                echo json_encode(['status' => 'error', 'message' => $creditLimitError]);
                return;
            }
        }

        $num = $this->number_generatorservice($this->input->post('type2', TRUE));
        $lastupdate = date('Y-m-d H:i:s');

        $service_order_no = 0;
        if ($this->input->post('service_order_no', TRUE)) {
            $service_order_no = $this->input->post('service_order_no', TRUE);

            $query = "UPDATE service_order SET  status = 1 ,
             type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            WHERE id = '{$this->input->post('service_order_no', TRUE)}';";
            $this->db->query($query);
        } else {
            $service_order_no = 0;
        }

        $query = "
    INSERT INTO service
    (id,service_id, date, eod_date, details, type2, discount, total_discount_ammount, total_vat_amnt, grandTotal, total,customer_id,employee_id,payment_type,invoicetype,lastupdateddate,createddate,userid,already,branch,service_order_id)
    VALUES
    (0,AES_ENCRYPT('{$num}', '{$encryption_key}') ,
     '{$this->input->post('date', TRUE)}',
     '{$this->input->post('eod_date', TRUE)}',
     '{$this->input->post('details', TRUE)}',
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('customer_id', TRUE)}',
     '{$this->input->post('employee_id', TRUE)}',
      '{$this->input->post('payment_type', TRUE)}',
      '{$this->input->post('invoicetype', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',
            0,
                   '{$this->input->post('branch', TRUE)}',
                   '{$service_order_no}'
    );";




        $this->db->query($query);



        $inserted_id = $this->db->insert_id();

        // Credit / Credit VAT service sales owe the customer's balance, same
        // as a credit product Sale — tracked via customer_due's service_id
        // column so Customer Receive can settle it too.
        $service_invoicetype_val = $this->input->post('invoicetype', TRUE);
        if ($service_invoicetype_val === 'credit' || $service_invoicetype_val === 'credit_vat') {
            $this->db->query("
                INSERT INTO customer_due (branch, customer, sale_id, service_id, sales_return_id, customer_receive_id, amount, type2)
                VALUES (
                    '{$this->input->post('branch', TRUE)}',
                    '{$this->input->post('customer_id', TRUE)}',
                    0,
                    '{$inserted_id}',
                    0,
                    0,
                    AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
                    AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                )
            ");
        }

        foreach ($items as $item) {
            $query = "
            INSERT INTO service_details 
            (id, pid, service, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['service']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";

            $this->db->query($query);
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'service', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        // $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $num,
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['terms_list'] = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $data['details'] = $this->load->view('service/pos_print',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");

        echo json_encode($data);
    }

    private function service_credit_due_total($type2, $excludeServiceId = null)
    {
        $encryption_key = Config::$encryption_key;
        $exclude = $excludeServiceId ? " AND cd.service_id != '" . (int)$excludeServiceId . "'" : '';
        $row = $this->db->query("
            SELECT IFNULL(SUM(t.due_amount), 0) AS total FROM (
                SELECT cd.service_id, SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                WHERE cd.service_id > 0
                  AND AES_DECRYPT(cd.type2, '{$encryption_key}') = '{$type2}'
                  {$exclude}
                GROUP BY cd.service_id
            ) t
        ")->row();
        return (float)$row->total;
    }

    private function check_service_credit_limit($type2, $newAmount, $excludeServiceId = null)
    {
        $ws = $this->db->select('service_credit_limit')->from('web_setting')->get()->row();
        $limit = $ws ? (float)$ws->service_credit_limit : 0;
        if ($limit <= 0) {
            return null;
        }
        $currentTotal = $this->service_credit_due_total($type2, $excludeServiceId);
        if (($currentTotal + $newAmount) > $limit) {
            return 'The company service credit limit (' . $limit . ') has been reached. This credit service invoice cannot be saved.';
        }
        return null;
    }

    public function number_generatorservice($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(service_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('service');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 1000000001;
            } else {
                $invoice_no = 3000000001;
            }
        }
        return $invoice_no;
    }

    public function customer_info($customer_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.customer_id as customer_id,
       AES_DECRYPT(a.customer_name, '{$encryption_key}') AS customer_name,
      AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_address, '{$encryption_key}') AS customer_address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS customer_mobile,
       AES_DECRYPT(a.customer_email, '{$encryption_key}') AS customer_email,

       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country")
            ->from('customer_information a')
            ->where('customer_id', $customer_id)
            ->get()
            ->row();
    }

       public function checkservice()
    {
        $postData = $this->input->post();
        $data = $this->service_model->service(
            $postData,
            $this->input->post('type2'),
            $this->input->post('branchid'),
            $this->input->post('fdate'),
            $this->input->post('tdate')
        );
        echo json_encode($data);
    }

   

      public function getServiceById()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select("
         po.id,
         po.status,
         si.customer_id,
         po.date,
         po.eod_date,
         po.branch,
         po.details,
         po.payment_type,
         po.invoicetype,
         po.employee_id,
         AES_DECRYPT(po.discount, '" . $encryption_key . "') AS discount, 
         AES_DECRYPT(sod.service_order_id, '" . $encryption_key . "') AS service_order_id, 
         AES_DECRYPT(po.total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount, 
         AES_DECRYPT(po.total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt, 
         AES_DECRYPT(po.grandTotal, '" . $encryption_key . "') AS grandTotal, 
         AES_DECRYPT(po.total, '" . $encryption_key . "') AS total,
         pod.service,
         AES_DECRYPT(pod.quantity, '" . $encryption_key . "') AS quantity,
         AES_DECRYPT(pod.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(pod.discount, '" . $encryption_key . "') AS discount2,
         AES_DECRYPT(pod.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(pod.vat_percent, '" . $encryption_key . "') AS vat_percent,
         AES_DECRYPT(pod.vat_value,'" . $encryption_key . "') AS vat_value,
         AES_DECRYPT(pod.total_price, '" . $encryption_key . "') AS total_price,
         AES_DECRYPT(pod.total_discount, '" . $encryption_key . "') AS total_discount,
         AES_DECRYPT(pod.all_discount,'" . $encryption_key . "') AS all_discount
     ");
        $this->db->from('service po');
        $this->db->join('customer_information si', 'si.customer_id = po.customer_id', 'inner');
        $this->db->join('service_details pod', 'pod.pid = po.id', 'inner');
        $this->db->join('service_order sod', 'sod.id = po.service_order_id', 'left');

        $this->db->join('product_service pi', 'pi.service_id  = pod.service', 'inner');

        $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();


        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }


      public function update_service()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');

        $serviceStatus = $this->db->select('status')->from('service')->where('id', $this->input->post('id', TRUE))->get()->row();
        if ($serviceStatus && (int)$serviceStatus->status == 2) {
            echo json_encode(['status' => 'error', 'message' => 'This invoice has been cancelled and can no longer be edited']);
            return;
        }

        $update_invoicetype_check = $this->input->post('invoicetype', TRUE);
        if ($update_invoicetype_check === 'credit' || $update_invoicetype_check === 'credit_vat') {
            $creditLimitError = $this->check_service_credit_limit($this->input->post('type2', TRUE), (float)$this->input->post('grandTotal', TRUE), $this->input->post('id', TRUE));
            if ($creditLimitError) {
                echo json_encode(['status' => 'error', 'message' => $creditLimitError]);
                return;
            }
        }


        $query = "
    UPDATE service
    SET
        date = '{$this->input->post('date', TRUE)}',
        eod_date = '{$this->input->post('eod_date', TRUE)}',
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        payment_type = '{$this->input->post('payment_type', TRUE)}',
        invoicetype = '{$this->input->post('invoicetype', TRUE)}',
        employee_id = '{$this->input->post('employee_id', TRUE)}',
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        customer_id = '{$this->input->post('customer_id', TRUE)}',
         lastupdateddate='{$lastupdate}',
         userid='{$this->session->userdata('id')}',
          branch='{$this->input->post('branch', TRUE)}',
         already=0
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        $this->db->query($query);

        // Sync customer_due: delete old record then re-insert if still credit
        // — mirrors update_sale()'s equivalent sync for the Sale screen.
        $update_service_id = $this->input->post('id', TRUE);
        $update_invoicetype = $this->input->post('invoicetype', TRUE);
        $this->db->where('service_id', $update_service_id)->delete('customer_due');
        if ($update_invoicetype === 'credit' || $update_invoicetype === 'credit_vat') {
            $this->db->query("
                INSERT INTO customer_due (branch, customer, sale_id, service_id, sales_return_id, customer_receive_id, amount, type2)
                VALUES (
                    '{$this->input->post('branch', TRUE)}',
                    '{$this->input->post('customer_id', TRUE)}',
                    0,
                    '{$update_service_id}',
                    0,
                    0,
                    AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
                    AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                )
            ");
        }



        $this->db->where('pid', $this->input->post('id', TRUE))
            ->delete('service_details');


        foreach ($items as $item) {
            $qu = -$item['quantity'];
         




            $query = "
            INSERT INTO service_details 
            (id, pid, service,quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
            VALUES 
            (0, 
             '{$this->input->post('id', TRUE)}', 
             '{$item['service']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";



            $this->db->query($query);
        }

        $query = "
    INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
    VALUES (
        0, 
        'service', 
        'update', 
        '{$this->input->post('id', TRUE)}', 
        '{$this->session->userdata('id')}',  '{$lastupdate}'
    );
";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $invoiceno[0]['service_id'],
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['terms_list'] = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $data['details'] = $this->load->view('service/pos_print',  $data, true);


        echo json_encode($data);
    }

        public function invoice_no($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(service_id, '" . $encryption_key . "') AS service_id")
            ->from('service')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

      public function invoice_no_order($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(service_order_id, '" . $encryption_key . "') AS service_order_id")
            ->from('service')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function delete_service($id = null)
    {
        $lastupdate = date('Y-m-d H:i:s');
        $encryption_key = Config::$encryption_key;


        $base_url = base_url();

        $serviceStatus = $this->db->select('status')->from('service')->where('id', $id)->get()->row();
        if ($serviceStatus && (int)$serviceStatus->status == 2) {
            echo '<script type="text/javascript">
            alert("This service invoice has been cancelled and can no longer be deleted");
            window.location.href = "' . $base_url . 'manage_service_invoice";
           </script>';
            return;
        }

        // Block delete if a Customer Receive has already settled against
        // this service sale — same protection Sale has for sale_id.
        $receiveExists = $this->db->from('customer_receive_details')
            ->where('service_id', $id)
            ->count_all_results();
        if ($receiveExists > 0) {
            echo '<script type="text/javascript">
            alert("Cannot delete this service detail because a customer receive has already been settled against it.");
            window.location.href = "' . $base_url . 'manage_service_invoice";
           </script>';
            return;
        }

        $service   =   $this->db->select("service_order_id")->from('service')->where('id',  $id)->get()->row();


                $query = "
            UPDATE service_order
            SET
                status = 0,
                lastupdateddate='{$lastupdate}',
              type2 = AES_ENCRYPT('C', '{$encryption_key}')
                WHERE id = '{$service->service_order_id}'";
          $this->db->query($query);



        $this->db->where('pid', $id)
            ->delete('service_details');

        $this->db->where('service_id', $id)
            ->delete('customer_due');

        $this->db->where('id', $id)
            ->delete('service');

        $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'service', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

        $this->db->query($query);


        echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'manage_service_invoice";
  </script>';
    }

    public function cancel_service($id = null)
    {
        if (!$this->permission1->method('manage_service_invoice', 'update')->access()) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            return;
        }

        $id = (int)($id ?? $this->input->post('id', TRUE));
        $lastupdate = date('Y-m-d H:i:s');
        $encryption_key = Config::$encryption_key;

        $service = $this->db->select('status')->from('service')->where('id', $id)->get()->row();
        if (!$service) {
            echo json_encode(['status' => 'error', 'message' => 'Invoice not found']);
            return;
        }
        if ((int)$service->status == 2) {
            echo json_encode(['status' => 'error', 'message' => 'This invoice is already cancelled']);
            return;
        }

        $receiveExists = $this->db->from('customer_receive_details')->where('service_id', $id)->count_all_results();
        if ($receiveExists > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot cancel this invoice because a customer receive has already been settled against it']);
            return;
        }

        $zero = "AES_ENCRYPT('0', '{$encryption_key}')";

        $this->db->query("
            UPDATE service SET
                grandTotal = {$zero}, total = {$zero}, discount = {$zero},
                total_discount_ammount = {$zero}, total_vat_amnt = {$zero},
                status = 2, lastupdateddate = '{$lastupdate}'
            WHERE id = '{$id}'
        ");

        $this->db->query("
            UPDATE service_details SET
                quantity = {$zero}, product_rate = {$zero}, discount = {$zero}, discount_value = {$zero},
                vat_percent = {$zero}, vat_value = {$zero}, total_price = {$zero},
                total_discount = {$zero}, all_discount = {$zero}
            WHERE pid = '{$id}'
        ");

        $this->db->where('service_id', $id)->delete('customer_due');

        $this->db->query("
            INSERT INTO logs (id, screen, operation, pid, userid, lastupdatedate)
            VALUES (0, 'service', 'cancel', '{$id}', '{$this->session->userdata('id')}', '{$lastupdate}')
        ");

        echo json_encode(['status' => 'success', 'message' => 'Invoice cancelled successfully']);
    }


    public function service_print()
    {
        $serviceId = $this->input->post('id', TRUE);
        $serviceStatus = $this->db->select('status')->from('service')->where('id', $serviceId)->get()->row();
        if ($serviceStatus && (int)$serviceStatus->status == 2) {
            echo json_encode(['error' => 'This invoice has been cancelled and can no longer be reprinted']);
            return;
        }

        $sale = $this->service($this->input->post('id', TRUE));
        $saledetails = $this->servicedetails($this->input->post('id', TRUE));
        $customer_info    = $this->customer_info($sale[0]['customer_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $saledetails,
            'total' => $sale[0]['total'],
            'total_dis' => $sale[0]['discount'] == "" ? "0.0" : $sale[0]['discount'],
            'total_discount_ammount' =>  $sale[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $sale[0]['total_vat_amnt'],
            'grandTotal' =>  $sale[0]['grandTotal'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $sale[0]['date'],
            'details'    => $sale[0]['details'] ?? "",
            'invoiceno' => $sale[0]['service_id'],
            'payment' => ""
        );

        $data['terms_list'] = $this->db->select('*')->from('seles_termscondi')->where('status', 1)->get()->result();
        $data['details'] = $this->load->view('service/pos_print',  $data, true);


        echo json_encode($data);
    }

    public function service($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("AES_DECRYPT(service_id, '" . $encryption_key . "') AS service_id,
         AES_DECRYPT(total, '" . $encryption_key . "') AS total,
         AES_DECRYPT(discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount,
         AES_DECRYPT(total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt,customer_id,
            AES_DECRYPT(grandTotal, '" . $encryption_key . "') AS grandTotal,date,details ")
            ->from('service')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function servicedetails($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("pi.service_name as product_name,AES_DECRYPT(sd.quantity, '" . $encryption_key . "') AS quantity,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(sd.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS vat_percent,
            AES_DECRYPT(sd.vat_value, '" . $encryption_key . "') AS vat_value,
             AES_DECRYPT(sd.total_price, '" . $encryption_key . "') AS total_price,
              AES_DECRYPT(sd.total_discount, '" . $encryption_key . "') AS total_discount,
                AES_DECRYPT(sd.all_discount, '" . $encryption_key . "') AS all_discount ")
            ->from('service_details sd')
            ->join('product_service pi', 'pi.service_id = sd.service', "left")
            ->where('pid', $id)
            ->get()
            ->result_array();
    }


    public function bdtask_service_details($invoice_id = null)
    {
        $sale = $this->service($invoice_id);
        $saledetails = $this->servicedetails($invoice_id);
        $customer_info    = $this->customer_info($sale[0]['customer_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $saledetails,
            'total' => $sale[0]['total'],
            'total_dis' => $sale[0]['discount'] == "" ? "0.0" : $sale[0]['discount'],
            'total_discount_ammount' =>  $sale[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $sale[0]['total_vat_amnt'],
            'grandTotal' =>  $sale[0]['grandTotal'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info2'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $sale[0]['date'],
            'details'    => "",
            'invoiceno' => $sale[0]['service_id'],
            'payment' => ""
        );

        $data['module']     = "service";
        $data['page']       = "invoice_html";
        echo modules::run('template/layout', $data);
    }



    public function bdtask_serviceorder_invoice_form($id = null)
    {
        $data = array(
            'title'         => display('serviceorder_invoice'),
            'taxes'         => $this->service_model->tax_fields(),
        );
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['service_list'] = $this->service_model->service_list();
        $data['module']      = 'service';
        // $vatortax            = $this->service_model->vat_tax_setting();
        $data['page']    = "add_serviceorder_form";
                $data['id'] = $id;

     

         $has_access = $id != null
            ? $this->permission1->method('manage_serviceorder_invoice', 'update')->access()
            : $this->permission1->method('manage_serviceorder_invoice', 'create')->access();

         if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Service Order";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

    }


    public function save_service_order()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        $num = $this->number_generatorserviceorder($this->input->post('type2', TRUE));
        $lastupdate = date('Y-m-d H:i:s');

        

        $query = "
    INSERT INTO service_order
    (id,service_order_id, date, eod_date, details, type2, discount, total_discount_ammount, total_vat_amnt, grandTotal, total,customer_id,employee_id,invoicetype,lastupdateddate,createddate,userid,already,branch)
    VALUES
    (0,AES_ENCRYPT('{$num}', '{$encryption_key}') ,
     '{$this->input->post('date', TRUE)}',
     '{$this->input->post('eod_date', TRUE)}',
     '{$this->input->post('details', TRUE)}',
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('customer_id', TRUE)}',
     '{$this->input->post('employee_id', TRUE)}',
     '{$this->input->post('invoicetype', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',
            0,
                   '{$this->input->post('branch', TRUE)}'
    );";




        $this->db->query($query);



        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $query = "
            INSERT INTO service_order_details 
            (id, pid, service, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['service']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";

            $this->db->query($query);
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'service_order', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        // $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $num,
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['details'] = $this->load->view('service/pos_print2',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      

        echo json_encode($data);
    }


    public function number_generatorserviceorder($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(service_order_id,'" . $encryption_key . "')", 'id');
        // $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('service_order');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 1000000001;

        }
        return $invoice_no;
    }

    public function manage_serviceorder_invoice()
    {
        if (!$this->permission1->method('manage_serviceorder_invoice', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']         = display('manage_serviceorder_invoice');
        $config["base_url"]    = base_url('manage_serviceorder_invoice');
        $config["total_rows"]  = $this->db->count_all('service_invoice');
        $config["per_page"]    = 20;
        $config["uri_segment"] = 2; 
        $config["last_link"]   = "Last";
        $config["first_link"]  = "First";
        $config['next_link']   = 'Next';
        $config['prev_link']   = 'Prev';
        $config['full_tag_open'] = "<ul class='pagination col-xs pull-right'>";
        $config['full_tag_close'] = "</ul>";
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $config['next_tag_open'] = "<li>";
        $config['next_tag_close'] = "</li>";
        $config['prev_tag_open'] = "<li>";
        $config['prev_tagl_close'] = "</li>";
        $config['first_tag_open'] = "<li>";
        $config['first_tagl_close'] = "</li>";
        $config['last_tag_open'] = "<li>";
        $config['last_tagl_close'] = "</li>";
        $this->pagination->initialize($config);
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        $data["links"]  = $this->pagination->create_links();
        $data['module'] = "service";
        $data['service'] = $this->service_model->service_invoice_list($config["per_page"], $page);
        $data['page']   = "manage_serviceorder";
        echo Modules::run('template/layout', $data);
    }

    public function checkserviceorder()
    {
        $postData = $this->input->post();
        $data = $this->service_model->serviceorder(
            $postData,
            $this->input->post('type2'),
            $this->input->post('branchid'),
            $this->input->post('fdate'),
            $this->input->post('tdate')
        );
        echo json_encode($data);
    }

    public function update_serviceeorderstatuscancel($id = null)
    {

        $base_url = base_url();

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE service_order
    SET 
        status = 2,
        lastupdateddate='{$lastupdate}'
    WHERE id = '{$id}';
";

        $this->db->query($query);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'service_order', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);


        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_serviceorder_invoice";
       </script>';

    }

    public function update_serviceorderstatusredo($id = null)
    {

        $base_url = base_url();

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE service_order
    SET 
        status = 0,
        lastupdateddate='{$lastupdate}'
    WHERE id = '{$id}';
";

        $this->db->query($query);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'service_order', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_serviceorder_invoice";
       </script>';  
     }

     public function getServiceOrderById()
     {
 
         $encryption_key = Config::$encryption_key;
 
         $this->db->select("
          po.id,
          si.customer_id,
          po.date,
          po.eod_date,
          po.branch,
          po.details,
          po.employee_id,
          po.invoicetype,
          AES_DECRYPT(po.service_order_id, '" . $encryption_key . "') AS service_order_id,
          AES_DECRYPT(po.discount, '" . $encryption_key . "') AS discount, 
          AES_DECRYPT(po.total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount, 
          AES_DECRYPT(po.total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt, 
          AES_DECRYPT(po.grandTotal, '" . $encryption_key . "') AS grandTotal, 
          AES_DECRYPT(po.total, '" . $encryption_key . "') AS total,
          pod.service,
          AES_DECRYPT(pod.quantity, '" . $encryption_key . "') AS quantity,
          AES_DECRYPT(pod.product_rate, '" . $encryption_key . "') AS product_rate,
          AES_DECRYPT(pod.discount, '" . $encryption_key . "') AS discount2,
          AES_DECRYPT(pod.discount_value, '" . $encryption_key . "') AS discount_value,
          AES_DECRYPT(pod.vat_percent, '" . $encryption_key . "') AS vat_percent,
          AES_DECRYPT(pod.vat_value,'" . $encryption_key . "') AS vat_value,
          AES_DECRYPT(pod.total_price, '" . $encryption_key . "') AS total_price,
          AES_DECRYPT(pod.total_discount, '" . $encryption_key . "') AS total_discount,
          AES_DECRYPT(pod.all_discount,'" . $encryption_key . "') AS all_discount
      ");
         $this->db->from('service_order po');
         $this->db->join('customer_information si', 'si.customer_id = po.customer_id', 'inner');
         $this->db->join('service_order_details pod', 'pod.pid = po.id', 'inner');
         // $this->db->join('quotation sod', 'sod.id = po.quotation_id', 'left');
 
         $this->db->join('product_service pi', 'pi.service_id  = pod.service', 'inner');
 
         $this->db->where('po.id', $this->input->post('id'));
 
         $query = $this->db->get();
 
 
         if ($query->num_rows() > 0) {
             echo json_encode($query->result_array());
         }
     }


     public function update_serviceorder()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');


        $query = "
    UPDATE service_order
    SET
        date = '{$this->input->post('date', TRUE)}',
        eod_date = '{$this->input->post('eod_date', TRUE)}',
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        employee_id = '{$this->input->post('employee_id', TRUE)}',
        invoicetype = '{$this->input->post('invoicetype', TRUE)}',
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        customer_id = '{$this->input->post('customer_id', TRUE)}',
         lastupdateddate='{$lastupdate}',
         userid='{$this->session->userdata('id')}',
          branch='{$this->input->post('branch', TRUE)}',
         already=0
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        $this->db->query($query);



        $this->db->where('pid', $this->input->post('id', TRUE))
            ->delete('service_order_details');


        foreach ($items as $item) {
            $qu = -$item['quantity'];
         




            $query = "
            INSERT INTO service_order_details 
            (id, pid, service,quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2) 
            VALUES 
            (0, 
             '{$this->input->post('id', TRUE)}', 
             '{$item['service']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";



            $this->db->query($query);
        }

        $query = "
    INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
    VALUES (
        0, 
        'service_order', 
        'update', 
        '{$this->input->post('id', TRUE)}', 
        '{$this->session->userdata('id')}',  '{$lastupdate}'
    );
";

        $this->db->query($query);

        $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no_order($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $items,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $invoiceno[0]['service_order_id'],
            'payment' => $this->input->post('payment', TRUE)
        );

        $data['details'] = $this->load->view('service/pos_print2',  $data, true);


        echo json_encode($data);
    }

    public function delete_serviceorder($id = null)
    {
        $lastupdate = date('Y-m-d H:i:s');

       
        $base_url = base_url();


            $this->db->where('pid', $id)
                ->delete('service_order_details');

            $this->db->where('id', $id)
                ->delete('service_order');

            $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'service_order', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

            $this->db->query($query);


            echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'manage_serviceorder_invoice";
  </script>';
        
    }

    public function getservicesorderidbybranch()
    {
        $encryption_key = Config::$encryption_key;

        $salesorder_reult = $this->db->select("id,AES_DECRYPT(service_order_id, '{$encryption_key}') AS service_order_id")
            ->from('service_order')
            ->where("status", 0)
            ->where("branch", $this->input->post('branch', TRUE))
            ->get()
            ->result();
        echo json_encode($salesorder_reult);
    }

     function bdtask_convertservice_form($id = null)
    {


        $data = array(
            'title'         => display('service_invoice'),
            'taxes'         => $this->service_model->tax_fields(),
        );
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['service_list'] = $this->service_model->service_list();
        $data['module']      = 'service';
        // $vatortax            = $this->service_model->vat_tax_setting();
        $data['page']    = "add_invoice_form";
        $data['pagetype']        = "convert";

        $data['id'] = $id;



        if ($this->permission1->method('manage_service_invoice', 'create')->access()) {

            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

       
    }


     public function service_order_print()
    {

        $sale = $this->serviceorder($this->input->post('id', TRUE));
        $saledetails = $this->serviceorderdetails($this->input->post('id', TRUE));
        $customer_info    = $this->customer_info($sale[0]['customer_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $saledetails,
            'total' => $sale[0]['total'],
            'total_dis' => $sale[0]['discount'] == "" ? "0.0" : $sale[0]['discount'],
            'total_discount_ammount' =>  $sale[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $sale[0]['total_vat_amnt'],
            'grandTotal' =>  $sale[0]['grandTotal'],
            'customer_info'   => $customer_info,
            'customer_name'   => $customer_info->customer_name,
            'customer_address' => $customer_info->customer_address,
            'customer_mobile' => $customer_info->customer_mobile,
            'customer_email'  => $customer_info->customer_email,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $sale[0]['date'],
            'details'    => $sale[0]['details'] ?? "",
            'invoiceno' => $sale[0]['service_id'],
            'payment' => ""
        );

        $data['details'] = $this->load->view('service/pos_print2',  $data, true);


        echo json_encode($data);
    }


     public function serviceorder($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("AES_DECRYPT(service_order_id, '" . $encryption_key . "') AS service_id,
         AES_DECRYPT(total, '" . $encryption_key . "') AS total,
         AES_DECRYPT(discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount,
         AES_DECRYPT(total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt,customer_id,
            AES_DECRYPT(grandTotal, '" . $encryption_key . "') AS grandTotal,date,details ")
            ->from('service_order')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function serviceorderdetails($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("pi.service_name as product_name,AES_DECRYPT(sd.quantity, '" . $encryption_key . "') AS quantity,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(sd.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS vat_percent,
            AES_DECRYPT(sd.vat_value, '" . $encryption_key . "') AS vat_value,
             AES_DECRYPT(sd.total_price, '" . $encryption_key . "') AS total_price,
              AES_DECRYPT(sd.total_discount, '" . $encryption_key . "') AS total_discount,
                AES_DECRYPT(sd.all_discount, '" . $encryption_key . "') AS all_discount ")
            ->from('service_order_details sd')
            ->join('product_service pi', 'pi.service_id = sd.service', "left")
            ->where('pid', $id)
            ->get()
            ->result_array();
    }

    /* ── CSV Upload: Service ─────────────────────────────────────── */
    public function save_service_from_csv()
    {
        $name   = trim($this->input->post('service_name', FALSE));
        if (empty($name)) { echo json_encode(['status'=>'Error','message'=>'Service name is required']); return; }
        $code   = trim($this->input->post('service_code', FALSE));
        if (empty($code)) { echo json_encode(['status'=>'Error','message'=>'Service ID is required']); return; }
        $exists = $this->db->where('service_code', $code)->count_all_results('product_service');
        if ($exists) { echo json_encode(['status'=>'Error','message'=>'Service ID already exists: '.$code]); return; }
        $charge = $this->input->post('charge', TRUE);
        $vat    = $this->input->post('service_vat', TRUE);
        $desc   = trim($this->input->post('description', FALSE));
        $status = (int)$this->input->post('status', TRUE);
        $data   = ['service_code'=>$code,'service_name'=>$name,'description'=>$desc,'charge'=>(float)$charge,'status'=>$status];
        if ($vat !== '' && $vat !== null) $data['service_vat'] = (float)$vat;
        $result = $this->service_model->service_entry($data);
        if ($result === FALSE) { echo json_encode(['status'=>'Error','message'=>'Service already exists: '.$name]); return; }
        echo json_encode(['status'=>'Success','id'=>$this->db->insert_id()]);
    }
}
