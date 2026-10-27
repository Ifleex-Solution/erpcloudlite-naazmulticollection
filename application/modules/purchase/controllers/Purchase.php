<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Purchase extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'purchase_model',
            'account/Accounts_model',
            'product/product_model',
            'service/service_model',
            'stock/unitcost_model'
        ));
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }


    function bdtask_purchase_form($id = null)
    {
        $data['title']       = display('add_purchase');
        $data['all_supplier'] = $this->purchase_model->supplier_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['units'] = $this->active_units();
        $data['category_list'] = $this->product_model->active_category();
        $data['unit_list']     = $this->product_model->active_unit();

        // $data['products'] = $this->active_product();
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        // $data["batches"] = $this->active_batch();

        $data['module']      = "purchase";
        $data['page']        = "add_purchase_form";
        $data['pagetype']        = "";
        $data['id'] = $id;

        $has_access = $id != null
            ? $this->permission1->method('manage_purchase', 'update')->access()
            : $this->permission1->method('manage_purchase', 'create')->access();

        if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Purchase";
            }
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    function bdtask_purchasereturn_form($id = null)
    {
        $data['title']       = display('new_purchase_return');
        $data['all_supplier'] = $this->purchase_model->supplier_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data["batches"] = $this->active_batch();
        $data["units"] = $this->active_units();

        $data['module']      = "purchase";
        $data['page']        = "new_purchase_return";
        $data['id'] = $id;
        $has_access = $id != null
            ? $this->permission1->method('manage_purchase_return', 'update')->access()
            : $this->permission1->method('manage_purchase_return', 'create')->access();

        if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Purchase Return";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }


    function bdtask_convertpurchase_form($id = null)
    {
        // $data['title']       = display('add_purchase');
        $data['all_supplier'] = $this->purchase_model->supplier_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
         if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data["batches"] = $this->active_batch();
        $data["units"] = $this->active_units();

        $data['module']      = "purchase";
        $data['page']        = "add_purchase_form";
        $data['pagetype']        = "convert";
        $data['id'] = $id;

        $data['title'] = display('add_purchase');
        echo modules::run('template/layout', $data);
    }

    public function active_batch()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('id, AES_DECRYPT(batchid , "' . $encryption_key . '") AS batchid,opening');
        $this->db->from('stockbatch');
        //   $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }



    // public function pmethod_dropdown()
    // {
    //     $data = $this->db->select('*')
    //         ->from('acc_coa')
    //         // ->where('PHeadName', 'Cash')
    //         ->where('PHeadName', 'Cash at Bank')
    //         ->get()
    //         ->result();


    //     if (!empty($data)) {
    //         foreach ($data as $value)
    //             $list[$value->HeadCode] = $value->HeadName;
    //         return $list;
    //     } else {
    //         return false;
    //     }
    // }

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

    public function bdtask_showpaymentmodal()
    {
        $is_credit =  $this->input->post('is_credit_edit', TRUE);
        $data['is_credit'] = $is_credit;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown();
        } else {

            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown_new();
        }
        $this->load->view('purchase/newpaymentveiw', $data);
    }

    public function bdtask_showpaymentmodal1($id = null)
    {

        $is_credit =  $this->input->post('is_credit_edit', TRUE);
        $data['is_credit'] = $is_credit;
        $data['id'] = $id;
        if ($is_credit == 1) {
            # code...
            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown();
        } else {

            $data['all_pmethod'] = $this->purchase_model->pmethod_dropdown_new();
        }
        $this->load->view('purchase/newpaymentveiw', $data);
    }

    public function bdtask_typeofthepayment($id = null)
    {
        $data = $this->purchase_model->pmethodbyid($id);
        echo json_encode($data);
    }

    public function getallcheques()
    {
        $this->db->select('cheque.id, cheque.cheque_no, cheque.draftdate, cheque.effectivedate, cheque.description, cheque.amount, cheque.status, customer_information.customer_name, customer_information.customer_id');
        $this->db->from('cheque');
        $this->db->join('customer_information', 'customer_information.customer_id = cheque.receivedfrom', 'left');
        $this->db->where('(cheque.paidto = 0 OR cheque.paidto IS NULL)');
        $this->db->where_in('cheque.status', ['Valid', 'Pending']);
        $query = $this->db->get();
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function getallcheques2()
    {
        $this->db->select('cheque.id, cheque.cheque_no, cheque.draftdate, cheque.effectivedate, cheque.description, cheque.amount, cheque.status, customer_information.customer_name, customer_information.customer_id');
        $this->db->from('cheque');
        $this->db->join('customer_information', 'customer_information.customer_id = cheque.receivedfrom', 'left');
        $this->db->where('(cheque.paidto = 0 OR cheque.paidto IS NULL)');
        $this->db->where_in('cheque.status', ['Valid', 'Pending']);
        $query = $this->db->get();
        $result = $query->result_array();
        echo json_encode($result);
    }



    public function bdtask_purchase_list()
    {
        $data['title']      = display('manage_purchase');
        $data['total_purhcase'] = $this->purchase_model->count_purchase();
        $data['module']     = "purchase";
        $data['page']       = "purchase";

        if ($this->permission1->method('manage_purchase', 'read')->access()) {

            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }


    public function bdtask_purchasereturn_list()
    {
        $data['title']      = display('manage_purchase_return');
        $data['module']     = "purchase";
        $data['page']       = "manage_purchase_return";

        if ($this->permission1->method('manage_purchase_return', 'read')->access()) {

            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }



    public function bdtask_purchase_details($purchase_id = null)
    {
        $purchase = $this->purchase($purchase_id);
        $purchasedetails = $this->purchasedetails($purchase_id);
        $supplier_info    = $this->supplier_info($purchase[0]['supplier_id']);
        $company_info     = $this->company_info();
        $currency_details = $this->service_model->web_setting();
        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $purchase[0]['total'],
            'total_dis' => $purchase[0]['discount'] == "" ? "0.0" : $purchase[0]['discount'],
            'total_discount_ammount' =>  $purchase[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $purchase[0]['total_vat_amnt'],
            'grandTotal' =>  $purchase[0]['grandTotal'],
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info12'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $purchase[0]['date'],
            'details'    => "",
            'invoiceno' => $purchase[0]['purchase_id'],
            'payment' => "",
            'chalan_no' => $purchase[0]['chalan_no'],

        );

        $data['module']     = "purchase";
        $data['page']       = "purchase_detail";
        echo modules::run('template/layout', $data);
    }

    public function company_info()
    {
        $encryption_key = Config::$encryption_key;

        $data = $this->db->select("
		 company_id,
		 AES_DECRYPT(company_name, '{$encryption_key}') AS company_name,
		 AES_DECRYPT(email, '{$encryption_key}') AS email,
		 AES_DECRYPT(address, '{$encryption_key}') AS address,
		 AES_DECRYPT(mobile, '{$encryption_key}') AS mobile,
		 AES_DECRYPT(website, '{$encryption_key}') AS website,
				AES_DECRYPT(vat_no, '{$encryption_key}') AS vat_no,
		 AES_DECRYPT(cr_no, '{$encryption_key}') AS cr_no,
		 status
	 ")
            ->from('company_information')
            ->where('company_id', $this->session->userdata('user_level2'))
            ->get()
            ->result_array();
        return $data;
    }

    public function bdtask_purchase_edit_form($purchase_id = null)
    {
        $purchase_detail = $this->purchase_model->retrieve_purchase_editdata($purchase_id);
        $supplier_id = $purchase_detail[0]['supplier_id'];
        $supplier_list = $this->purchase_model->supplier_list();

        if (!empty($purchase_detail)) {
            $i = 0;
            foreach ($purchase_detail as $k => $v) {
                $i++;
                $purchase_detail[$k]['sl'] = $i;
            }
        }
        $multi_pay_data = $this->db->select('RevCodde, Debit')
            ->from('acc_vaucher')
            ->where('referenceNo', $purchase_detail[0]['purchase_id'])
            ->get()->result();



        $data = array(
            'title'             => display('purchase_edit'),
            'dbpurs_id'         => $purchase_detail[0]['dbpurs_id'],
            'purchase_id'       => $purchase_detail[0]['purchase_id'],
            'chalan_no'         => $purchase_detail[0]['chalan_no'],
            'supplier_name'     => $purchase_detail[0]['supplier_name'],
            'supplier_id'       => $purchase_detail[0]['supplier_id'],
            'grand_total'       => $purchase_detail[0]['grand_total_amount'],
            'purchase_details'  => $purchase_detail[0]['purchase_details'],
            'purchase_date'     => $purchase_detail[0]['purchase_date'],
            'total_discount'    => $purchase_detail[0]['total_discount'],
            'invoice_discount'  => $purchase_detail[0]['invoice_discount'],
            'total_vat_amnt'    => $purchase_detail[0]['total_vat_amnt'],
            'total'             => number_format($purchase_detail[0]['grand_total_amount'] + (!empty($purchase_detail[0]['total_discount']) ? $purchase_detail[0]['total_discount'] : 0), 2),
            'bank_id'           =>  $purchase_detail[0]['bank_id'],
            'purchase_info'     => $purchase_detail,
            'supplier_list'     => $supplier_list,
            'paid_amount'       => $purchase_detail[0]['paid_amount'],
            'due_amount'        => $purchase_detail[0]['due_amount'],
            'multi_paytype'     => $multi_pay_data,
            'is_credit'         => $purchase_detail[0]['is_credit'],
        );

        $data['all_pmethod']    = $this->purchase_model->pmethod_dropdown_new();


        $data['all_pmethodwith_cr'] = $this->purchase_model->pmethod_dropdown();
        $data['module']         = "purchase";
        $data['page']           = "edit_purchase_form";
        echo modules::run('template/layout', $data);
    }

    public function CheckPurchaseList()
    {
        $postData  = $this->input->post();
        $data = $this->purchase_model->getPurchaseList($postData);
        echo json_encode($data);
    }

    public function deletePurchase($purchase_id)
    {
        $parts = explode("_", $purchase_id);
        $this->db->where('referenceNo', $parts[0]);
        $this->db->delete('acc_vaucher');

        $this->db->where('referenceNo', $parts[0]);
        $this->db->delete('acc_transaction');

        $this->db->where('purchase_id', $parts[1]);
        $this->db->delete('product_purchase_details');

        $this->db->where('id', $parts[1]);
        $this->db->delete('product_purchase');

        echo json_encode("success");
    }

    public function testpur()
    {
        for ($i = 0; $i <= 500000; $i++) {

            $this->purchase_model->insert_purchasetest();
        }
    }

    public function bdtask_save_purchase()
    {
        $this->form_validation->set_rules('supplier_id', display('supplier'), 'required|max_length[15]');
        $this->form_validation->set_rules('chalan_no', display('invoice_no'), 'required|max_length[20]|is_unique[product_purchase.chalan_no]');
        $this->form_validation->set_rules('product_id[]', display('product'), 'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]', display('payment_type'), 'required');
        $this->form_validation->set_rules('product_quantity[]', display('quantity'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_rate[]', display('rate'), 'required|max_length[20]');
        $discount_per = $this->input->post('discount_per', TRUE);
        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
            redirect("add_purchase");
        } else {

            if ($this->form_validation->run() === true) {

                $purchase_data = $this->purchase_model->insert_purchase();
                $purchase_data = 1;



                if ($purchase_data == 1) {

                    $this->session->set_flashdata('message', display('save_successfully'));
                    redirect("purchase_list");
                }
                if ($purchase_data == 2) {

                    $this->session->set_flashdata('exception', 'Paid Amount Should Equal To Payment Amount');
                    redirect("add_purchase");
                }
                if ($purchase_data == 3) {

                    $this->session->set_flashdata('exception', display('ooops_something_went_wrong'));
                    redirect("add_purchase");
                }
            } else {
                $this->session->set_flashdata('exception', validation_errors());
                redirect("add_purchase");
            }
        }
    }





    public function bdtask_update_purchase()
    {
        date_default_timezone_set('Asia/Colombo');

        $purchase_id  = $this->input->post('purchase_id', TRUE);
        $dbpurs_id    = $this->input->post('dbpurs_id', TRUE);
        $this->form_validation->set_rules('supplier_id', display('supplier'), 'required|max_length[15]');
        $this->form_validation->set_rules('chalan_no', display('invoice_no'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_id[]', display('product'), 'required|max_length[20]');
        $this->form_validation->set_rules('multipaytype[]', display('payment_type'), 'required');
        $this->form_validation->set_rules('product_quantity[]', display('quantity'), 'required|max_length[20]');
        $this->form_validation->set_rules('product_rate[]', display('rate'), 'required|max_length[20]');
        $finyear = $this->input->post('finyear', true);
        if ($finyear <= 0) {
            $this->session->set_flashdata('exception', 'Please Create Financial Year First From Accounts > Financial Year.');
            redirect("add_purchase");
        } else {

            if ($this->form_validation->run() === true) {

                $paid_amount  = $this->input->post('paid_amount', TRUE);
                $due_amount   = $this->input->post('due_amount', TRUE);
                $bank_id      = $this->input->post('bank_id', TRUE);
                if (!empty($bank_id)) {
                    $bankname = $this->db->select('bank_name')->from('bank_add')->where('bank_id', $bank_id)->get()->row()->bank_name;
                    $bankcoaid   = $this->db->select('HeadCode')->from('acc_coa')->where('HeadName', $bankname)->get()->row()->HeadCode;
                }
                $p_id        = $this->input->post('product_id', TRUE);
                $supplier_id = $this->input->post('supplier_id', TRUE);
                $supinfo     = $this->db->select('*')->from('supplier_information')->where('supplier_id', $supplier_id)->get()->row();
                $sup_head    = $supinfo->supplier_id . '-' . $supinfo->supplier_name;
                $sup_coa     = $this->db->select('*')->from('acc_coa')->where('HeadName', $sup_head)->get()->row();
                $receive_by  = $this->session->userdata('id');
                $receive_date = date('Y-m-d');
                $createdate  = date('Y-m-d H:i:s');
                $multipayamount = $this->input->post('pamount_by_method', TRUE);
                $multipaytype = $this->input->post('multipaytype', TRUE);

                if ($multipaytype[0] == 0) {
                    $is_credit = 1;
                } else {
                    $is_credit = '';
                }
                $data = array(
                    'purchase_id'        => $purchase_id,
                    'chalan_no'          => $this->input->post('chalan_no', TRUE),
                    'supplier_id'        => $this->input->post('supplier_id', TRUE),
                    'grand_total_amount' => $this->input->post('grand_total_price', TRUE),
                    'total_discount'     => $this->input->post('discount', TRUE),
                    'invoice_discount'   => $this->input->post('total_discount', TRUE),
                    'total_vat_amnt'     => $this->input->post('total_vat_amnt', TRUE),
                    'purchase_date'      => $this->input->post('purchase_date', TRUE),
                    'purchase_details'   => $this->input->post('purchase_details', TRUE),
                    'paid_amount'        => $paid_amount,
                    'due_amount'         => $due_amount,
                    'bank_id'           =>  $this->input->post('bank_id', TRUE),
                    'payment_type'       =>  1,
                    'is_credit'          =>  $is_credit,
                );


                $predefine_account  = $this->db->select('*')->from('acc_predefine_account')->get()->row();
                $Narration          = "Purchase Voucher";
                $Comment            = "Purchase Voucher for supplier";
                $COAID              = $predefine_account->purchaseCode;

                if ($purchase_id != '') {
                    $this->db->where('id', $dbpurs_id);
                    $this->db->update('product_purchase', $data);

                    //account transaction update
                    $this->db->where('referenceNo', $purchase_id);
                    $this->db->delete('acc_vaucher');

                    $this->db->where('purchase_id', $dbpurs_id);
                    $this->db->delete('product_purchase_details');
                }


                $multipayamount = $this->input->post('pamount_by_method', TRUE);
                $multipaytype = $this->input->post('multipaytype', TRUE);

                if ($multipaytype && $multipayamount) {

                    if ($multipaytype[0] == 0) {

                        $amount_pay = $data['grand_total_amount'];
                        $amnt_type = 'Credit';
                        $reVID     = $predefine_account->supplierCode;
                        $subcode   = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                        $insrt_pay_amnt_vcher = $this->insert_purchase_debitvoucher($is_credit, $purchase_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID, $subcode);
                    } else {
                        $amnt_type = 'Debit';
                        for ($i = 0; $i < count($multipaytype); $i++) {

                            $reVID = $multipaytype[$i];
                            $amount_pay = $multipayamount[$i];

                            $insrt_pay_amnt_vcher = $this->insert_purchase_debitvoucher($is_credit, $purchase_id, $COAID, $amnt_type, $amount_pay, $Narration, $Comment, $reVID);
                        }

                        if ($data['due_amount'] > 0) {

                            $amount_pay2 = $data['due_amount'];
                            $amnt_type2 = 'Credit';
                            $reVID2     = $predefine_account->supplierCode;
                            $subcode2   = $this->db->select('*')->from('acc_subcode')->where('referenceNo', $supplier_id)->where('subTypeId', 4)->get()->row()->id;
                            $this->insert_purchase_debitvoucher(1, $purchase_id, $COAID, $amnt_type2, $amount_pay2, $Narration, $Comment, $reVID2, $subcode2);
                        }
                    }
                }

                $rate         = $this->input->post('product_rate', TRUE);
                $p_id         = $this->input->post('product_id', TRUE);
                $quantity     = $this->input->post('product_quantity', TRUE);
                $t_price      = $this->input->post('total_price', TRUE);
                $expiry_date  = $this->input->post('expiry_date', TRUE);
                $batch_no     = $this->input->post('batch_no', TRUE);
                $discountvalue = $this->input->post('discountvalue', TRUE);
                $vatpercent   = $this->input->post('vatpercent', TRUE);
                $vatvalue     = $this->input->post('vatvalue', TRUE);
                $discount_per = $this->input->post('discount_per', TRUE);

                $discount = $this->input->post('discount', TRUE);

                for ($i = 0, $n = count($p_id); $i < $n; $i++) {
                    $product_quantity = $quantity[$i];
                    $product_rate     = $rate[$i];
                    $product_id       = $p_id[$i];
                    $total_price      = $t_price[$i];
                    $disc             = $discount[$i];
                    $ba_no            = $batch_no[$i];
                    $exp_date         = $expiry_date[$i];
                    $dis_per          = $discount_per[$i];
                    $disval           = $discountvalue[$i];
                    $vatper           = $vatpercent[$i];
                    $vatval           = $vatvalue[$i];


                    $data1 = array(
                        'purchase_detail_id' => $this->generator(15),
                        'purchase_id'        => $dbpurs_id,
                        'product_id'         => $product_id,
                        'quantity'           => $product_quantity,
                        'rate'               => $product_rate,
                        'batch_id'           => $ba_no,
                        'expiry_date'        => $exp_date,
                        'total_amount'       => $total_price,
                        'discount'           => $dis_per,
                        'discount_amnt'      => $disval,
                        'vat_amnt_per'       => $vatper,
                        'vat_amnt'           => $vatval,
                        'status'             => 1
                    );

                    $product_price = array(

                        'supplier_price' => $product_rate
                    );

                    if (($quantity)) {

                        $this->db->insert('product_purchase_details', $data1);
                        $this->db->where('product_id', $product_id)->update('supplier_product', $product_price);
                    }
                }
                $setting_data = $this->db->select('is_autoapprove_v')->from('web_setting')->where('setting_id', 1)->get()->result_array();


                if ($setting_data[0]['is_autoapprove_v'] == 1) {

                    //$new = $this->autoapprove($purchase_id);
                    $vouchers = $this->db->select('referenceNo, VNo')->from('acc_vaucher')->where('referenceNo', $purchase_id)->where('status', 0)->get()->result();
                    foreach ($vouchers as $value) {
                        # code...
                        $data = $this->Accounts_model->approved_vaucher($value->VNo, 'active');
                    }
                }
                $this->session->set_flashdata('message', display('update_successfully'));
                redirect("purchase_list");
            } else {
                $this->session->set_flashdata('exception', validation_errors());
                redirect("purchase_edit/" . $purchase_id);
            }
        }
    }

    // insert purchase debitvoucher
    public function insert_purchase_debitvoucher($is_credit = null, $purchase_id = null, $dbtid = null, $amnt_type = null, $amnt = null, $Narration = null, $Comment = null, $reVID = null, $subcode = null)
    {

        date_default_timezone_set('Asia/Colombo');

        $fyear = financial_year();
        $VDate = date('Y-m-d');
        $CreateBy = $this->session->userdata('id');
        $createdate = date('Y-m-d H:i:s');
        if ($is_credit == 1) {
            $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'JV', 'VNo');
            $vaucherNo = "JV-" . ($maxid + 1);

            $debitinsert = array(
                'fyear'          =>  $fyear,
                'VNo'            =>  $vaucherNo,
                'Vtype'          =>  'JV',
                'referenceNo'    =>  $purchase_id,
                'VDate'          =>  $VDate,
                'COAID'          =>  $reVID,
                'Narration'      =>  $Narration,
                'ledgerComment'  =>  $Comment,
                'RevCodde'       =>  $dbtid,
                'subType'        =>  4,
                'subCode'        =>  $subcode,
                'isApproved'     =>  0,
                'CreateBy'       =>  $CreateBy,
                'CreateDate'     =>  $createdate,
                'status'         =>  0,
            );
        } else {
            $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
            $vaucherNo = "DV-" . ($maxid + 1);
            $debitinsert = array(
                'fyear'          =>  $fyear,
                'VNo'            =>  $vaucherNo,
                'Vtype'          =>  'DV',
                'referenceNo'    =>  $purchase_id,
                'VDate'          =>  $VDate,
                'COAID'          =>  $dbtid,
                'Narration'      =>  $Narration,
                'ledgerComment'  =>  $Comment,
                'RevCodde'       =>  $reVID,
                'isApproved'     =>  0,
                'CreateBy'       => $CreateBy,
                'CreateDate'     => $createdate,
                'status'         => 0,
            );
        }
        if ($amnt_type == 'Debit') {

            $debitinsert['Debit']  = $amnt;
            $debitinsert['Credit'] =  0.00;
        } else {

            $debitinsert['Debit']  = 0.00;
            $debitinsert['Credit'] =  $amnt;
        }


        $this->db->insert('acc_vaucher', $debitinsert);

        return true;
    }

    public function bdtask_product_search_by_supplier()
    {
        $supplier_id = $this->input->post('supplier_id', TRUE);
        $product_name = $this->input->post('product_name', TRUE);
        $product_info = $this->purchase_model->product_search_item($supplier_id, $product_name);
        if (!empty($product_info)) {
            $list[''] = '';
            foreach ($product_info as $value) {
                $json_product[] = array('label' => $value['product_name'] . '(' . $value['product_model'] . ')', 'value' => $value['product_id']);
            }
        } else {
            $json_product[] = 'No Product Found';
        }
        echo json_encode($json_product);
    }

    public function bdtask_retrieve_product_data()
    {
        $product_id  = $this->input->post('product_id', TRUE);
        $supplier_id = $this->input->post('supplier_id', TRUE);
        $product_info = $this->purchase_model->get_total_product($product_id, $supplier_id);

        echo json_encode($product_info);
    }

    public function product_supplier_check($product_id, $supplier_id)
    {
        $this->db->select('*');
        $this->db->from('supplier_product');
        $this->db->where('product_id', $product_id);
        $this->db->where('supplier_id', $supplier_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        return 0;
    }

    public function generator($lenth)
    {
        $number = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "N", "M", "O", "P", "Q", "R", "S", "U", "V", "T", "W", "X", "Y", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 34);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }




    //Purchase  Part

    function bdtask_new_purchase_order($id = null)
    {


        $data['title']       = display('new_purchase_order');
        $data['all_supplier'] = $this->purchase_model->supplier_list();
        $data['all_pmethod'] = $this->pmethod_dropdown();
        $data['products'] = $this->active_product();
        $data['category_list'] = $this->product_model->active_category();
        $data['unit_list']     = $this->product_model->active_unit();
        $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data["batches"] = $this->active_batch();

        $data['module']      = "purchase";
        $data['page']        = "new_purchase_order";
        $data['id'] = $id;

        $has_access = $id != null
            ? $this->permission1->method('manage_purchase_order', 'update')->access()
            : $this->permission1->method('manage_purchase_order', 'create')->access();

        if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Purchase Order";
            }
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    public function active_batches()
    {
        $this->db->select('*');
        $this->db->from('stockbatch');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_product()
    {
        $this->db->select('id,product_name');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_floor()
    {
        $this->db->select('st.id, st.name,s.id as storeid');
        $this->db->from('store s');
        $this->db->join('storefloor sf', 'sf.store = s.id');
        $this->db->join('floor st', 'st.id = sf.floor');
        $this->db->where('st.status', 1);


        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }


    // Unit Cost Logic Specification v1.1 — UCL-38, UCL-42: the current (last) unit cost of a
    // Product + Batch, or the product's Fixed Purchase Price when the batch has never been
    // costed yet.
    private function _current_cost_or_fallback($product, $batch)
    {
        $current = $this->unitcost_model->current($product, $batch);
        if ($current['cost'] > 0) {
            return $current['cost'];
        }

        $encryption_key = Config::$encryption_key;
        $row = $this->db->query("
            SELECT AES_DECRYPT(cost_price, '{$encryption_key}') AS cost_price
            FROM product_information WHERE id = {$product}
        ")->row();

        return $row ? (float) $row->cost_price : 0.0;
    }

    // Unit Cost Logic Specification v1.1 (Phase 5) — re-runs the cost chain once per distinct
    // Product+Batch pair in $pairs.
    private function _rerun_affected_pairs($pairs, $from_date)
    {
        if (empty($from_date)) {
            return;
        }
        $seen = [];
        foreach ($pairs as $pair) {
            $product = is_array($pair) ? $pair['product'] : $pair->product;
            $batch   = is_array($pair) ? $pair['batch']   : $pair->batch;
            $key = $product . ':' . $batch;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $this->unitcost_model->rerun_chain($product, $batch, $from_date);
        }
    }

    // Unit Cost Logic Specification v1.1 (Phase 3) — costs the purchase_details line just
    // inserted (UCL-20: a Purchase always resets the moving average to its own net unit cost
    // when the batch was at zero/negative stock beforehand; otherwise it blends in as normal).
    private function _record_purchase_cost($item, $date, $voucher_id, $store, $purchase_details_id)
    {
        if ($item['isstock'] != 1) {
            return;
        }

        $qty = (float) $item['quantity'];
        $line_value = (float) $item['total_price'];
        $line_cost = $qty > 0 ? ($line_value / $qty) : 0;

        $result = $this->unitcost_model->record([
            'product' => $item['product'], 'batch' => $item['batch'], 'date' => $date,
            'source_type' => 'purchase', 'source_no' => $voucher_id, 'line_id' => $purchase_details_id,
            'branch' => $this->input->post('branch', TRUE), 'store' => $store,
            'qty_in' => $qty, 'value_in' => $line_value,
            'reset_on_purchase' => true,
        ]);

        $encryption_key = Config::$encryption_key;
        $this->db->set('base_qty', "AES_ENCRYPT('" . number_format($qty, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('net_purchase_unit_cost', "AES_ENCRYPT('" . number_format($line_cost, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('unit_cost_id', $result['unit_cost_id']);
        $this->db->where('id', $purchase_details_id)->update('purchase_details');

        // Phase 5 / back-dated entry (UCL-50)
        if ($this->unitcost_model->has_later_movements($item['product'], $item['batch'], $date)) {
            $this->unitcost_model->rerun_chain($item['product'], $item['batch'], $date);
        }
    }

    // Unit Cost Logic Specification v1.1 (Phase 3) — costs the purchase_return_details line
    // just inserted, valued at the ORIGINAL purchase line's net unit cost when resolvable
    // (via $item['invoicedetail'], the purchase_details id being returned against), falling
    // back to the batch's current cost otherwise.
    private function _record_purchase_return_cost($item, $date, $voucher_id, $store, $return_details_id)
    {
        if ($item['isstock'] != 1) {
            return;
        }

        $encryption_key = Config::$encryption_key;
        $qty = (float) $item['quantity'];
        $source_detail_id = isset($item['invoicedetail']) ? (int) $item['invoicedetail'] : 0;
        $return_cost = 0;

        if ($source_detail_id > 0) {
            $row = $this->db->query("
                SELECT AES_DECRYPT(net_purchase_unit_cost, '{$encryption_key}') AS c
                FROM purchase_details WHERE id = {$source_detail_id}
            ")->row();
            if ($row && $row->c !== null) {
                $return_cost = (float) $row->c;
            }
        }
        if ($return_cost <= 0) {
            $return_cost = $this->_current_cost_or_fallback($item['product'], $item['batch']);
        }

        $return_value = $qty * $return_cost;

        $result = $this->unitcost_model->record([
            'product' => $item['product'], 'batch' => $item['batch'], 'date' => $date,
            'source_type' => 'purchase_return', 'source_no' => $voucher_id, 'line_id' => $return_details_id,
            'branch' => $this->input->post('branch', TRUE), 'store' => $store,
            'qty_out' => $qty, 'value_out' => $return_value,
        ]);

        $this->db->set('return_unit_cost', "AES_ENCRYPT('" . number_format($return_cost, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('returned_stock_value', "AES_ENCRYPT('" . number_format($return_value, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('unit_cost_id', $result['unit_cost_id']);
        $this->db->set('source_detail_id', $source_detail_id);
        $this->db->where('id', $return_details_id)->update('purchase_return_details');

        if ($this->unitcost_model->has_later_movements($item['product'], $item['batch'], $date)) {
            $this->unitcost_model->rerun_chain($item['product'], $item['batch'], $date);
        }
    }

    public function save_purchase()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        $num = $this->number_generator($this->input->post('type2', TRUE));

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $purchase_order_id = 0;
        if ($this->input->post('purchase_order_id', TRUE)) {
            $purchase_order_id = $this->input->post('purchase_order_id', TRUE);

            $query = "UPDATE purchase_order 
            SET  status = 1,
              type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
             WHERE id = '{$this->input->post('purchase_order_id', TRUE)}';";
            $this->db->query($query);
        } else {
            $purchase_order_id = 0;
        }

        $query = "
    INSERT INTO purchase 
    (id,purchase_id,chalan_no, date, details, type2, discount, total_discount_ammount, total_vat_amnt,
     grandTotal, total,supplier_id,payment_type,lastupdateddate,createddate,userid,already,branch,incidenttype,invoicetype,purchase_order_no) 
    VALUES 
    (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
    AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('date', TRUE)}',
     '{$this->input->post('details', TRUE)}',  
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('supplier_id', TRUE)}',
      '{$this->input->post('payment_type', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',0,
              '{$this->input->post('branch', TRUE)}',
              '{$this->input->post('incidenttype', TRUE)}',
              '{$this->input->post('invoicetype', TRUE)}',
                    '{$purchase_order_id}'


    );";
     $incidentType=   $this->input->post('incidenttype', TRUE)==1?"International Purchase":"Local Purchase";

    

        $this->db->trans_begin();
        try {
        $this->db->query($query);

        $inserted_id = $this->db->insert_id();

        // Credit / Credit VAT purchases owe the supplier money — track that in
        // supplier_due, mirroring how Sale tracks customer_due for credit sales.
        $purchase_invoicetype_val = $this->input->post('invoicetype', TRUE);
        if ($purchase_invoicetype_val === 'credit' || $purchase_invoicetype_val === 'credit_vat') {
            $this->db->query("
                INSERT INTO supplier_due (branch, supplier, purchase_id, purchase_return_id, supplier_payment_id, amount, type2)
                VALUES (
                    '{$this->input->post('branch', TRUE)}',
                    '{$this->input->post('supplier_id', TRUE)}',
                    '{$inserted_id}',
                    0,
                    0,
                    AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
                    AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                )
            ");
        }

        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            if ($item['isstock'] == 1) {

                $query1 = "
            INSERT INTO stock_details
            (id,product,batch, store, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['batch']}', 
             '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
             'purchase',
             '{$inserted_id}','{$this->input->post('date', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
              );

             
        ";

                $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($this->input->post('date', TRUE)) . ",
            'purchase',
                         " . $this->db->escape($incidentType) . ",
            AES_ENCRYPT('', '{$encryption_key}'),
          AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
            " . $this->db->escape($inserted_id) . ",
            " . $this->db->escape($item['store']) . ",
            AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($item['quantity']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";


                $this->db->query($query1);
                $this->db->query($query2);

            }


            $store   =   $this->db->select("auto_grn")->from('store ')->where('id', $item['store'])->get()->row();
            if ($store->auto_grn == 0) {
                if ($item['isstock'] == 1) {

                    $query1 = "
            INSERT INTO phystock_details 
            (id,product,batch, store, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}',
             '{$item['batch']}',  
             '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
             'purchase',
             '{$inserted_id}','{$this->input->post('date', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
            );
        ";

                    $query2 = "
                    INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($this->input->post('date', TRUE)) . ",
                        'purchase',
                        " . $this->db->escape($incidentType) . ",
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($item['store']) . ",
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($item['quantity']) . ", '{$encryption_key}'),
                        " . $this->db->escape($lastupdate) . ",
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    );";
                    $this->db->query($query1);
                    $this->db->query($query2);
                }
            }

            $query = "
            INSERT INTO purchase_details 
            (id, pid, product,batch, store, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2,conversion_id,unit) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['product']}',
              '{$item['batch']}',   
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
               '{$conversionid}',
                 '{$item['unit']}'

            );";

            $this->db->query($query);
            $this->_record_purchase_cost($item, $this->input->post('date', TRUE), $inserted_id, $item['store'], $this->db->insert_id());
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate)
        VALUES (
            0,
            'purchase',
            'insert',
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Save failed. Please try again.']);
            return;
        }

        $supplier_info    =  $this->supplier_info($this->input->post('supplier_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no($this->input->post('id', TRUE));
        $purchasedetails = $this->purchasedetails($inserted_id);


        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $num,
            'payment' => $this->input->post('payment', TRUE),
            'chalan_no' => $this->input->post('chalan_no', TRUE)

        );

        $data['details'] = $this->load->view('purchase/pos_print',  $data, true);
        $data['success'] = true;


        echo json_encode($data);
    }

    public function save_purchase_order()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        $num = $this->number_generator_po($this->input->post('type2', TRUE));

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $query = "
    INSERT INTO purchase_order 
    (id,purchase_id,chalan_no, date, details, type2, discount, total_discount_ammount, total_vat_amnt,
     grandTotal, total,supplier_id,lastupdateddate,createddate,userid,already,branch,incidenttype,invoicetype) 
    VALUES 
    (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
    AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('date', TRUE)}',
     '{$this->input->post('details', TRUE)}',  
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('supplier_id', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',0,
              '{$this->input->post('branch', TRUE)}',
              '{$this->input->post('incidenttype', TRUE)}',
              '{$this->input->post('invoicetype', TRUE)}'


    );";



        $this->db->trans_begin();
        try {
        $this->db->query($query);

        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;





            $query = "
            INSERT INTO purchase_order_details
            (id, pid, product,batch, store, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2,conversion_id,unit) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['product']}',
              '{$item['batch']}',   
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
               '{$conversionid}', 
                 '{$item['unit']}'

            );";

            $this->db->query($query);
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'purchase_order', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $supplier_info    =  $this->supplier_info($this->input->post('supplier_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no($this->input->post('id', TRUE));
        $purchasedetails = $this->purchaseorderdetails($inserted_id);


        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $num,
            'payment' => $this->input->post('payment', TRUE),
            'chalan_no' => $this->input->post('chalan_no', TRUE)

        );

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Save failed. Please try again.']);
            return;
        }

        $data['details'] = $this->load->view('purchase/pos_print2',  $data, true);
        $data['success'] = true;


        echo json_encode($data);
    }



    public function supplier_info($supplier_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.supplier_id as supplier_id,
       AES_DECRYPT(a.supplier_name, '{$encryption_key}') AS supplier_name,
      AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.address, '{$encryption_key}') AS address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.emailnumber, '{$encryption_key}') AS emailnumber,
       AES_DECRYPT(a.email_address, '{$encryption_key}') AS email_address,
       AES_DECRYPT(a.contact, '{$encryption_key}') AS contact,
       AES_DECRYPT(a.phone, '{$encryption_key}') AS phone,
       a.fax as fax,
       a.city as city,
       a.state as state,
       a.zip as zip,
       a.country as country")
            ->from('supplier_information a')
            ->where('supplier_id', $supplier_id)
            ->get()
            ->row();
    }

    public function invoice_no($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(purchase_id, '" . $encryption_key . "') AS purchase_id")
            ->from('purchase')
            ->where('id', $id)
            ->get()
            ->result_array();
    }


    public function invoice_no_po($id = null)
    {
        $encryption_key = Config::$encryption_key;

         $result = $this->db->select(" AES_DECRYPT(purchase_id, '" . $encryption_key . "') AS purchase_id")
            ->from('purchase_order')
            ->where('id', $id)
            ->get()
            ->result_array();

        return $result[0]['purchase_id'];
    }


    public function number_generator($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(purchase_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('purchase');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 4000000001;
            } else {
                $invoice_no = 4400000001;
            }
        }
        return $invoice_no;
    }

    public function number_generator_po($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(purchase_id,'" . $encryption_key . "')", 'id');
        // $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('purchase_order');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            $invoice_no = 2000000001;

        }
        return $invoice_no;
    }

    public function bdtask_manage_purchase_order()
    {
        $data['title']      = display('manage_purchase_order');
        $data['module']     = "purchase";
        $data['page']       = "manage_purchase_order";
        echo modules::run('template/layout', $data);
    }

    public function checkpurchase()
    {
        $postData = $this->input->post();
        $data = $this->purchase_model->purchase($this->input->post('branchid'), $postData, $this->input->post('type2'),$this->input->post('fdate'),$this->input->post('tdate'));

        echo json_encode($data);
    }

    public function checkpurchaseorder()
    {
        $postData = $this->input->post();
        $data = $this->purchase_model->purchase_order($this->input->post('branchid'), $postData, $this->input->post('type2'),$this->input->post('fdate'),$this->input->post('tdate'));

        echo json_encode($data);
    }

    public function checkpurchasereturn()
    {
        $postData = $this->input->post();
        $data = $this->purchase_model->purchase_return($this->input->post('branchid'), $postData, $this->input->post('type2'),$this->input->post('fdate'),$this->input->post('tdate'));

        echo json_encode($data);
    }




    public function update__purchase()
    {
        $items = $this->input->post('items', TRUE);

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');


        $encryption_key = Config::$encryption_key;

        // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-edit state before anything
        // below mutates it.
        $old_purchase_date_row = $this->db->select('date')->from('purchase')->where('id', $this->input->post('id', TRUE))->get()->row();
        $old_purchase_date = $old_purchase_date_row ? $old_purchase_date_row->date : $this->input->post('date', TRUE);
        $old_purchase_pairs = $this->db->select('product, batch')->from('purchase_details')->where('pid', $this->input->post('id', TRUE))->get()->result_array();

        $query = "
    UPDATE purchase
    SET
        date = '{$this->input->post('date', TRUE)}',
        chalan_no = AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        payment_type = '{$this->input->post('payment_type', TRUE)}',
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        supplier_id = '{$this->input->post('supplier_id', TRUE)}',
        lastupdateddate='{$lastupdate}',
        userid='{$this->session->userdata('id')}',
        already=0,
        incidenttype= '{$this->input->post('incidenttype', TRUE)}',
        invoicetype='{$this->input->post('invoicetype', TRUE)}',
        branch='{$this->input->post('branch', TRUE)}'
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        $this->db->trans_begin();
        try {
        $this->db->query($query);

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'purchase')
            ->delete('stock_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'purchase')
            ->delete('phystock_details');

        // $this->db->where('pid', $this->input->post('id', TRUE))
        //     ->delete('purchase_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('scenario', 'purchase')
            ->delete('audit_stock');

        $deletedIds = $this->input->post('deletedPurchaseDetails'); // array

        if (!empty($deletedIds)) {
            $this->db->where_in('id', $deletedIds);
            $this->db->delete('purchase_details');
        }
     $incidentType=   $this->input->post('incidenttype', TRUE)==1?"International Purchase":"Local Purchase";


        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            if ($item['isstock'] == 1) {

            $query1 = "
            INSERT INTO stock_details 
            (id,product, store,batch, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
              '{$item['batch']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
             'purchase',
             '{$this->input->post('id', TRUE)}','{$this->input->post('date', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
            );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($this->input->post('date', TRUE)) . ",
            'purchase',
              " . $this->db->escape($incidentType) . ",
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
            " . $this->db->escape($this->input->post('id', TRUE)) . ",
            " . $this->db->escape($item['store']) . ",
            AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($item['quantity']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";


                $this->db->query($query1);
                $this->db->query($query2);
            }

            $store   =   $this->db->select("auto_grn")->from('store ')->where('id', $item['store'])->get()->row();
            if ($store->auto_grn == 0) {
                if ($item['isstock'] == 1) {

                $query1 = "
                INSERT INTO phystock_details 
                (id,product, store,batch, stock, type, pid,date,conversion_id,unit) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['store']}', 
                  '{$item['batch']}', 
                  AES_ENCRYPT(" . $this->db->escape($item['quantity']) . ", '{$encryption_key}'),
                 'purchase',
                 '{$this->input->post('id', TRUE)}',
                 '{$this->input->post('date', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
                );
            ";
                $this->db->query($query1);

                $query2 = "
                INSERT INTO audit_stock
                (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                 astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
                VALUES (
                    " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
                    " . $this->db->escape($this->input->post('date', TRUE)) . ",
                    'purchase',
                                        " . $this->db->escape($incidentType) . ",
                    AES_ENCRYPT('', '{$encryption_key}'),
                    AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
                   " . $this->db->escape($this->input->post('id', TRUE)) . ",
                    " . $this->db->escape($item['store']) . ",
                    AES_ENCRYPT('', '{$encryption_key}'),
                    AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
                    AES_ENCRYPT('', '{$encryption_key}'),
                    AES_ENCRYPT(" . $this->db->escape($item['quantity']) . ", '{$encryption_key}'),
                    " . $this->db->escape($lastupdate) . ",
                     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                );";
                $this->db->query($query1);
                $this->db->query($query2);
                }
            }
                if ($item['purchasedetail'] == 0) {

                    $query = "
                    INSERT INTO purchase_details 
                    (
                        id,
                        pid,
                        product,
                        batch,
                        store,
                        quantity,
                        product_rate,
                        discount,
                        discount_value,
                        vat_percent,
                        vat_value,
                        total_price,
                        total_discount,
                        all_discount,
                        type2,
                        conversion_id,
                        unit
                    ) 
                    VALUES 
                    (
                        0,
                        '{$this->input->post('id', TRUE)}',
                        '{$item['product']}',
                        '{$item['batch']}',
                        '{$item['store']}',
                        AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
                        '{$conversionid}',
                        '{$item['unit']}'
                    )";

                    $this->db->query($query);
                    $this->_record_purchase_cost($item, $this->input->post('date', TRUE), $this->input->post('id', TRUE), $item['store'], $this->db->insert_id());

                } else {

                    $query = "
                    UPDATE purchase_details SET
                        pid = '{$this->input->post('id', TRUE)}',
                        product = '{$item['product']}',
                        batch = '{$item['batch']}',
                        store = '{$item['store']}',
                        quantity = AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
                        product_rate = AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
                        discount = AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'),
                        discount_value = AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'),
                        vat_percent = AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'),
                        vat_value = AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'),
                        total_price = AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'),
                        total_discount = AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'),
                        all_discount = AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
                        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
                        conversion_id = '{$conversionid}',
                        unit = '{$item['unit']}'
                    WHERE id = '{$item['purchasedetail']}'
                    ";



                    $this->db->query($query);
                }

            $logFilePath = 'logfile.log';
            $fileHandle = fopen($logFilePath, 'a');
            $logMessage = json_encode($query);
            fwrite($fileHandle, $logMessage . "\n");
            fclose($fileHandle);
        }

        // Unit Cost Logic Specification v1.1 (Phase 5) — re-run every Product+Batch this
        // voucher touched, before or after the edit, from whichever date is earlier.
        $new_purchase_pairs = $old_purchase_pairs;
        foreach ($items as $item) {
            $new_purchase_pairs[] = ['product' => $item['product'], 'batch' => $item['batch']];
        }
        $from_date_for_cost = ($old_purchase_date <= $this->input->post('date', TRUE)) ? $old_purchase_date : $this->input->post('date', TRUE);
        $this->_rerun_affected_pairs($new_purchase_pairs, $from_date_for_cost);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate)
        VALUES (
            0,
            'purchase',
            'update',
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Update failed. Please try again.']);
            return;
        }

        $supplier_info    =  $this->supplier_info($this->input->post('supplier_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no($this->input->post('id', TRUE));
        $purchasedetails = $this->purchasedetails($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $invoiceno,
            'payment' => $this->input->post('payment', TRUE),
            'chalan_no' => $this->input->post('chalan_no', TRUE)

        );

        $data['details'] = $this->load->view('purchase/pos_print',  $data, true);
        $data['success'] = true;


        echo json_encode($data);
    }

    public function update_purchase_order()
    {
        $items = $this->input->post('items', TRUE);

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');


        $encryption_key = Config::$encryption_key;


        $query = "
    UPDATE purchase_order
    SET 
        date = '{$this->input->post('date', TRUE)}',
        chalan_no = AES_ENCRYPT('{$this->input->post('chalan_no', TRUE)}', '{$encryption_key}'),
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        supplier_id = '{$this->input->post('supplier_id', TRUE)}',
        lastupdateddate='{$lastupdate}',
        userid='{$this->session->userdata('id')}',
        already=0,
        incidenttype= '{$this->input->post('incidenttype', TRUE)}',
        invoicetype='{$this->input->post('invoicetype', TRUE)}',
        branch='{$this->input->post('branch', TRUE)}'
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        $this->db->trans_begin();
        try {
        $this->db->query($query);




        $this->db->where('pid', $this->input->post('id', TRUE))
            ->delete('purchase_order_details');

       



        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;


              $query = "
            INSERT INTO purchase_order_details
            (id, pid, product,batch, store, quantity, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2,conversion_id,unit) 
            VALUES 
            (0, 
             '{$this->input->post('id', TRUE)}', 
             '{$item['product']}',
              '{$item['batch']}',   
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
               '{$conversionid}', 
                 '{$item['unit']}'

            );";

            $this->db->query($query);
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'purchase_order', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $supplier_info    =  $this->supplier_info($this->input->post('supplier_id', TRUE));
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $invoiceno = $this->invoice_no_po($this->input->post('id', TRUE));
        $purchasedetails = $this->purchaseorderdetails($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $this->input->post('total', TRUE),
            'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
            'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
            'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
            'grandTotal' => $this->input->post('grandTotal', TRUE),
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'details'    => $this->input->post('details', TRUE),
            'invoiceno' => $invoiceno,
            'payment' => $this->input->post('payment', TRUE),
            'chalan_no' => $this->input->post('chalan_no', TRUE)

        );

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Update failed. Please try again.']);
            return;
        }

        $data['details'] = $this->load->view('purchase/pos_print2',  $data, true);
        $data['success'] = true;


        echo json_encode($data);
    }

    public function getPurchaseById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    po.id, 
    pod.id as purchase_detail_id,
    si.supplier_id,
    po.date, 
    po.details, 
    po.payment_type, pi.product_name,
    AES_DECRYPT(po.chalan_no, '{$encryption_key}') AS chalan_no,
    AES_DECRYPT(po.discount, '{$encryption_key}') AS discount, 
    AES_DECRYPT(po.total_discount_ammount, '{$encryption_key}') AS total_discount_ammount, 
    AES_DECRYPT(po.total_vat_amnt, '{$encryption_key}') AS total_vat_amnt, 
    AES_DECRYPT(po.grandTotal, '{$encryption_key}') AS grandTotal, 
    AES_DECRYPT(po.total, '{$encryption_key}') AS total,
    pod.product,
    pod.store,
    pod.unit,  AES_DECRYPT(pi.cost_price, '{$encryption_key}') as cost_price,
    po.branch, pod.conversion_id,cr.convertiontype,cr.conversion_ratio,pod.batch,
   CAST( ROUND(
    CASE
        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '+' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') + cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '-' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') - cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '*' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') * cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '/' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') / cr.conversion_ratio

        ELSE AES_DECRYPT(pod.quantity, '{$encryption_key}')
    END,
    6) AS SIGNED
) AS quantity,
    AES_DECRYPT(pod.product_rate, '{$encryption_key}') AS product_rate,
    AES_DECRYPT(pod.discount, '{$encryption_key}') AS discount2,
    AES_DECRYPT(pod.discount_value, '{$encryption_key}') AS discount_value,
    AES_DECRYPT(pod.vat_percent, '{$encryption_key}') AS vat_percent,
    AES_DECRYPT(pod.vat_value, '{$encryption_key}') AS vat_value,
    AES_DECRYPT(pod.total_price, '{$encryption_key}') AS total_price,
    AES_DECRYPT(pod.total_discount, '{$encryption_key}') AS total_discount,
    AES_DECRYPT(pod.all_discount, '{$encryption_key}') AS all_discount,
    (
        SELECT 
            CASE
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
            END
        FROM stock_details c 
        LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pod.conversion_id
        WHERE pod.product = c.product
          AND pod.store = c.store 
          AND pod.batch = c.batch 
    ) AS avstock,pi.batchtype,po.invoicetype, po.incidenttype, AES_DECRYPT(pod1.purchase_id, '{$encryption_key}') AS purchase_order_no
");

        $this->db->from('purchase po');
        $this->db->join('supplier_information si', 'si.supplier_id = po.supplier_id', 'inner');
        $this->db->join('purchase_details pod', 'pod.pid = po.id', 'inner');
        $this->db->join('purchase_order pod1', 'pod1.id = po.purchase_order_no', 'left');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pod.conversion_id', 'left');
        $this->db->join('product_information pi', 'pi.id = pod.product', 'inner');

        $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function getPurchaseOrderById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    po.id, 
    si.supplier_id,
    po.date, 
    po.details,  pi.product_name,
    AES_DECRYPT(po.chalan_no, '{$encryption_key}') AS chalan_no,
        AES_DECRYPT(po.purchase_id, '{$encryption_key}') AS purchase_id,
    AES_DECRYPT(po.discount, '{$encryption_key}') AS discount, 
    AES_DECRYPT(po.total_discount_ammount, '{$encryption_key}') AS total_discount_ammount, 
    AES_DECRYPT(po.total_vat_amnt, '{$encryption_key}') AS total_vat_amnt, 
    AES_DECRYPT(po.grandTotal, '{$encryption_key}') AS grandTotal, 
    AES_DECRYPT(po.total, '{$encryption_key}') AS total,
    pod.product,
    pod.store,
    pod.unit,  AES_DECRYPT(pi.cost_price, '{$encryption_key}') as cost_price,
    po.branch, pod.conversion_id,cr.convertiontype,cr.conversion_ratio,pod.batch,
   CAST(  ROUND(
    CASE
        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '+' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') + cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '-' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') - cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '*' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') * cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '/' 
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') / cr.conversion_ratio

        ELSE AES_DECRYPT(pod.quantity, '{$encryption_key}')
    END,
    6) AS SIGNED
) AS quantity,
    AES_DECRYPT(pod.product_rate, '{$encryption_key}') AS product_rate,
    AES_DECRYPT(pod.discount, '{$encryption_key}') AS discount2,
    AES_DECRYPT(pod.discount_value, '{$encryption_key}') AS discount_value,
    AES_DECRYPT(pod.vat_percent, '{$encryption_key}') AS vat_percent,
    AES_DECRYPT(pod.vat_value, '{$encryption_key}') AS vat_value,
    AES_DECRYPT(pod.total_price, '{$encryption_key}') AS total_price,
    AES_DECRYPT(pod.total_discount, '{$encryption_key}') AS total_discount,
    AES_DECRYPT(pod.all_discount, '{$encryption_key}') AS all_discount,
    (
        SELECT 
            CASE
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
            END
        FROM stock_details c 
        LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pod.conversion_id
        WHERE pod.product = c.product
          AND pod.store = c.store 
          AND pod.batch = c.batch 
    ) AS avstock,pi.batchtype,po.invoicetype, po.incidenttype
");

        $this->db->from('purchase_order po');
        $this->db->join('supplier_information si', 'si.supplier_id = po.supplier_id', 'inner');
        $this->db->join('purchase_order_details pod', 'pod.pid = po.id', 'inner');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pod.conversion_id', 'left');
        $this->db->join('product_information pi', 'pi.id = pod.product', 'inner');

        $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function pos_print()
    {

        $purchase = $this->purchase($this->input->post('id', TRUE));
        $purchasedetails = $this->purchasedetails($this->input->post('id', TRUE));
        $supplier_info    = $this->supplier_info($purchase[0]['supplier_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $purchase[0]['total'],
            'total_dis' => $purchase[0]['discount'] == "" ? "0.0" : $purchase[0]['discount'],
            'total_discount_ammount' =>  $purchase[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $purchase[0]['total_vat_amnt'],
            'grandTotal' =>  $purchase[0]['grandTotal'],
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $purchase[0]['date'],
            'details'    => "",
            'invoiceno' => $purchase[0]['purchase_id'],
            'payment' => "",
            'chalan_no' => $purchase[0]['chalan_no'],
        );




        $data['details'] = $this->load->view('purchase/pos_print',  $data, true);


        echo json_encode($data);
    }


    public function pos_print2()
    {

        $purchase = $this->purchaseorder($this->input->post('id', TRUE));
        $purchasedetails = $this->purchaseorderdetails($this->input->post('id', TRUE));
        $supplier_info    = $this->supplier_info($purchase[0]['supplier_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();



        $data = array(
            'invoice_all_data' => $purchasedetails,
            'total' => $purchase[0]['total'],
            'total_dis' => $purchase[0]['discount'] == "" ? "0.0" : $purchase[0]['discount'],
            'total_discount_ammount' =>  $purchase[0]['total_discount_ammount'],
            'total_vat_amnt' =>  $purchase[0]['total_vat_amnt'],
            'grandTotal' =>  $purchase[0]['grandTotal'],
            'customer_info'   => $supplier_info,
            'supplier_name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    =>  $purchase[0]['date'],
            'details'    => "",
            'invoiceno' => $purchase[0]['purchase_id'],
            'payment' => "",
            'chalan_no' => $purchase[0]['chalan_no'],
        );




        $data['details'] = $this->load->view('purchase/pos_print2',  $data, true);


        echo json_encode($data);
    }

    public function purchase($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("AES_DECRYPT(purchase_id, '" . $encryption_key . "') AS purchase_id,
        AES_DECRYPT(chalan_no, '" . $encryption_key . "') AS chalan_no,
         AES_DECRYPT(total, '" . $encryption_key . "') AS total,
         AES_DECRYPT(discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount,
         AES_DECRYPT(total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt,supplier_id,
            AES_DECRYPT(grandTotal, '" . $encryption_key . "') AS grandTotal,date ")
            ->from('purchase')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function purchasedetails($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("pi.product_name,
       CAST(   ROUND(
    CASE
        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') + cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') - cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') * cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') / cr2.conversion_ratio

        ELSE AES_DECRYPT(sd.quantity, '{$encryption_key}')
    END,
    6) AS SIGNED
) AS quantity,u.unit_name,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(sd.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS vat_percent,
            AES_DECRYPT(sd.vat_value, '" . $encryption_key . "') AS vat_value,
             AES_DECRYPT(sd.total_price, '" . $encryption_key . "') AS total_price,
              AES_DECRYPT(sd.total_discount, '" . $encryption_key . "') AS total_discount,
                AES_DECRYPT(sd.all_discount, '" . $encryption_key . "') AS all_discount
            ,sd.store ")
            ->from('purchase_details sd')
            ->join('product_information pi', 'pi.id = sd.product', "left")
            ->join('conversion_ratio cr2', 'cr2.conversionratio_id = sd.conversion_id', "left")
            ->join('units u', 'u.unit_id = sd.unit', "left")

            ->where('pid', $id)
            ->get()
            ->result_array();
    }

    public function purchaseorder($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("AES_DECRYPT(purchase_id, '" . $encryption_key . "') AS purchase_id,
        AES_DECRYPT(chalan_no, '" . $encryption_key . "') AS chalan_no,
         AES_DECRYPT(total, '" . $encryption_key . "') AS total,
         AES_DECRYPT(discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount,
         AES_DECRYPT(total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt,supplier_id,
            AES_DECRYPT(grandTotal, '" . $encryption_key . "') AS grandTotal,date ")
            ->from('purchase_order')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function active_units()
    {
        $this->db->select('unit_id,unit_name');
        $this->db->from('units');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }



    public function purchaseorderdetails($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select("pi.product_name,
         CAST(  ROUND(
    CASE
        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') + cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') - cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') * cr2.conversion_ratio

        WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
            THEN AES_DECRYPT(sd.quantity, '{$encryption_key}') / cr2.conversion_ratio

        ELSE AES_DECRYPT(sd.quantity, '{$encryption_key}')
    END,
     6) AS SIGNED
) AS quantity,u.unit_name,
         AES_DECRYPT(sd.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(sd.discount, '" . $encryption_key . "') AS discount,
          AES_DECRYPT(sd.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(sd.vat_percent, '" . $encryption_key . "') AS vat_percent,
            AES_DECRYPT(sd.vat_value, '" . $encryption_key . "') AS vat_value,
             AES_DECRYPT(sd.total_price, '" . $encryption_key . "') AS total_price,
              AES_DECRYPT(sd.total_discount, '" . $encryption_key . "') AS total_discount,
                AES_DECRYPT(sd.all_discount, '" . $encryption_key . "') AS all_discount
            ,sd.store ")
            ->from('purchase_order_details sd')
            ->join('product_information pi', 'pi.id = sd.product', "left")
            ->join('conversion_ratio cr2', 'cr2.conversionratio_id = sd.conversion_id', "left")
            ->join('units u', 'u.unit_id = sd.unit', "left")

            ->where('pid', $id)
            ->get()
            ->result_array();
    }

    public function delete_category($id)
    {

        $productExists = $this->db->from('product_information')
            ->where('category_id', $id)
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('category_id', $id)
                ->delete('product_category');
            return $this->db->affected_rows() > 0;
        }
    }

    public function delete_purchase($id = null)
    {
        $productExists = $this->db->from('grn_stock')
            ->where('voucherno', $id)
            ->where('type', 'purchase')
            ->count_all_results();

            $productExists2 = $this->db->from('purchase_return')
            ->where('purchase_id', $id)
            ->count_all_results();

            $productExists3 = $this->db->from('supplier_payment_details')
            ->where('purchase_id', $id)
            ->count_all_results();

        $lastupdate = date('Y-m-d H:i:s');
        $base_url = base_url();
        $encryption_key = Config::$encryption_key;


        if ($productExists > 0 || $productExists3 > 0) {

            // $this->session->set_flashdata('exception', "Cannot delete this purchase detail because this purchase detail is linked to it or something went wrong");

            // redirect("purchase_list");

            echo '<script type="text/javascript">
            alert("Cannot delete this purchase detail because this purchase detail is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'purchase_list";
           </script>';
        }else if($productExists2>0){
            echo '<script type="text/javascript">
            alert("Cannot delete this purchase detail because this purchase detail is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'purchase_list";
           </script>';

        } else {


            $purchase   =   $this->db->select("purchase_order_no")->from('purchase')->where('id',  $id)->get()->row();

            // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-delete state.
            $old_purchase_pairs = $this->db->select('product, batch')->from('purchase_details')->where('pid', $id)->get()->result_array();
            $old_purchase_date_row = $this->db->select('date')->from('purchase')->where('id', $id)->get()->row();
            $old_purchase_date = $old_purchase_date_row ? $old_purchase_date_row->date : null;

            $query = "
            UPDATE purchase_order
            SET
                status = 0,
                lastupdateddate='{$lastupdate}',
              type2 = AES_ENCRYPT('C', '{$encryption_key}')
                WHERE id = '{$purchase->purchase_order_no}'";
          $this->db->query($query);


            $this->db->where('pid', $id)
            ->where('type', 'purchase')
                ->delete('stock_details');

            $this->db->where('pid', $id)
                ->where('scenario', 'purchase')
                ->delete('audit_stock');


            $this->db->where('pid', $id)
                ->where('type', 'purchase')
                ->delete('phystock_details');

            $this->db->where('pid', $id)
                ->delete('purchase_details');

            $this->db->where('purchase_id', $id)
                ->delete('supplier_due');

            $this->db->where('id', $id)
                ->delete('purchase');

            // Unit Cost Logic Specification v1.1 (Phase 5) — re-cost every Product+Batch this
            // deleted purchase had affected.
            $this->_rerun_affected_pairs($old_purchase_pairs, $old_purchase_date);

            // $reservation   =   $this->db->select("rperiod,id")->from('reservation')->where('purchase_no', $id)->get()->row();

            // $this->db->where('pid', $reservation->id)
            //     ->delete('reservation_details');
            // $this->db->where('id', $reservation->id)
            //     ->delete('reservation');


            $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'purchase', 
            'delete', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

            $this->db->query($query);

            // $this->session->set_flashdata('message', display('delete_successfully'));


            // redirect("purchase_list");

            echo '<script type="text/javascript">
            alert("Deleted successfully");
            window.location.href = "' . $base_url . 'purchase_list";
           </script>';
        }
    }

    public function delete_purchase_order($id = null)
    {
        

        $lastupdate = date('Y-m-d H:i:s');
        $base_url = base_url();

            $this->db->where('pid', $id)
                ->delete('purchase_order_details');



            $this->db->where('id', $id)
                ->delete('purchase_order');

            // $reservation   =   $this->db->select("rperiod,id")->from('reservation')->where('purchase_no', $id)->get()->row();

            // $this->db->where('pid', $reservation->id)
            //     ->delete('reservation_details');
            // $this->db->where('id', $reservation->id)
            //     ->delete('reservation');


            $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'purchase_order', 
            'delete', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

            $this->db->query($query);

            // $this->session->set_flashdata('message', display('delete_successfully'));


            // redirect("purchase_list");

            echo '<script type="text/javascript">
            alert("Deleted successfully");
            window.location.href = "' . $base_url . 'manage_purchase_order";
           </script>';
        
    }

    public function update_purchasestatus($id = null)
    {
        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE purchase
    SET 
        status = 1,
        lastupdateddate='{$lastupdate}'
    WHERE id = '{$id}';
";

        $this->db->query($query);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'sale', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        redirect("purchase_list");
    }

    public function update_purchasestatusredo($id = null)
    {
        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE purchase
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
            'sale', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        redirect("purchase_list");
    }

    public function update_purchaseorderstatusredo($id = null)
    {

        $base_url = base_url();

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE purchase_order
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
            'purchase_order', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_purchase_order";
       </script>';  
     }


    public function update_purchaseorderstatuscancel($id = null)
    {

        $base_url = base_url();

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');
        $query = "
    UPDATE purchase_order
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
            'purchase_order', 
            'update', 
            '{$id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);


        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_purchase_order";
       </script>';

    }

    public function getpurchaseidbybranch()
    {
        $encryption_key = Config::$encryption_key;

        $purchase_return = $this->db->select("id,AES_DECRYPT(s.chalan_no, '{$encryption_key}') AS purchase_id")
            ->from('purchase s')
            ->where("AES_DECRYPT(s.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE))
            ->where("s.branch", $this->input->post('branch', TRUE))
            ->where("s.id NOT IN (SELECT purchase_id FROM purchase_return)", null, false)
            ->where("s.id NOT IN (SELECT purchase_id FROM supplier_payment_details)", null, false)
            ->get()
            ->result();
        echo json_encode($purchase_return);
    }


    public function save_purchase_return()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        $num = $this->number_generatorsales_return($this->input->post('type2', TRUE));
        $lastupdate = date('Y-m-d H:i:s');

       

        
        $query = "
    INSERT INTO purchase_return 
    (id,purchase_return_id, date,rdate, details, type2, discount, total_discount_ammount, total_vat_amnt, grandTotal, total,supplier_id,payment_type,lastupdateddate,createddate,userid,
    incidenttype,already,branch,invoicetype,purchase_id) 
    VALUES 
    (0,AES_ENCRYPT('{$num}', '{$encryption_key}') , 
     '{$this->input->post('purchase_date', TRUE)}',
          '{$this->input->post('rdate', TRUE)}',
     '{$this->input->post('details', TRUE)}',  
     AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'), 
     AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('supplier_id', TRUE)}',
      '{$this->input->post('payment_type', TRUE)}',
      '{$lastupdate}',
      '{$lastupdate}','{$this->session->userdata('id')}',
       '{$this->input->post('incidenttype', TRUE)}',
        0,
        '{$this->input->post('branch', TRUE)}',
        '{$this->input->post('invoicetype', TRUE)}', '{$this->input->post('invoice_id', TRUE)}'
    );";




        $this->db->trans_begin();
        try {
        $this->db->query($query);



        $inserted_id = $this->db->insert_id();

        // A credit purchase return reduces what's owed to the supplier —
        // mirrors how Sale's sales_return reduces customer_due.
        $return_invoicetype_val = $this->input->post('invoicetype', TRUE);
        if ($return_invoicetype_val === 'credit' || $return_invoicetype_val === 'credit_vat') {
            $return_grand = $this->input->post('grandTotal', TRUE);
            $negative_grand = -abs((float)$return_grand);
            $this->db->query("
                INSERT INTO supplier_due (branch, supplier, purchase_id, purchase_return_id, supplier_payment_id, amount, type2)
                VALUES (
                    '{$this->input->post('branch', TRUE)}',
                    '{$this->input->post('supplier_id', TRUE)}',
                    '{$this->input->post('invoice_id', TRUE)}',
                    '{$inserted_id}',
                    0,
                    AES_ENCRYPT('{$negative_grand}', '{$encryption_key}'),
                    AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                )
            ");
        }

        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;

            $qu = $item['quantity'];
            $rqty = -$item['rqty'];
            $aqty ="-".$item['aqty'];



            if ($item['isstock'] == 1) {
            $query1 = "
            INSERT INTO stock_details 
            (id,product,batch, store, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['batch']}', 
             '{$item['rstore']}', 
             AES_ENCRYPT('{$rqty}', '{$encryption_key}'),'purchase_return',
             '{$inserted_id}','{$this->input->post('rdate', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
              );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($this->input->post('rdate', TRUE)) . ",
             'Purchase Return',
            'Purchase Return',
            (SELECT chalan_no FROM purchase WHERE id = " . $this->db->escape($this->input->post('invoice_id', TRUE)) . " LIMIT 1),
           AES_ENCRYPT('$num', '{$encryption_key}'),
            " . $this->db->escape($inserted_id) . ",
            " . $this->db->escape($item['rstore']) . ",
            AES_ENCRYPT(" . $this->db->escape($aqty) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($rqty) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            " . $this->db->escape($lastupdate) . "
        );";


                $this->db->query($query1);
                $this->db->query($query2);
            }



            $store   =   $this->db->select("auto_gdn")->from('store ')->where('id', $item['rstore'])->get()->row();
            if ($store->auto_gdn == 0) {
                if ($item['isstock'] == 1) {

                    $query1 = "
            INSERT INTO phystock_details 
            (id,product,batch, store, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}',
             '{$item['batch']}',  
             '{$item['rstore']}', 
            AES_ENCRYPT('{$rqty}', '{$encryption_key}'),
             'purchase_return',
             '{$inserted_id}','{$this->input->post('rdate', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
            );
        ";
                    $query2 = "
        INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($this->input->post('rdate', TRUE)) . ",
            'Purchase Return',
            'Purchase Return',
            (SELECT chalan_no FROM purchase WHERE id = " . $this->db->escape($this->input->post('invoice_id', TRUE)) . " LIMIT 1),
           AES_ENCRYPT('$num', '{$encryption_key}'),
            " . $this->db->escape($inserted_id) . ",
            " . $this->db->escape($item['rstore']) . ",
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape( $aqty) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($rqty) . ", '{$encryption_key}'),
            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";
                    $this->db->query($query1);
                    $this->db->query($query2);
                }
            }

            $query = "
            INSERT INTO purchase_return_details 
            (id, pid, product,batch, store, quantity,rstore, rqty,rdeduction, 
            product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2,conversion_id,unit) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['product']}', 
              '{$item['batch']}',
              '{$item['store']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
              '{$item['rstore']}', 
             AES_ENCRYPT('{$item['rqty']}', '{$encryption_key}'),
              AES_ENCRYPT('{$item['rdeduction']}', '{$encryption_key}'),  
             AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
             AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
             AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
               AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
               '{$conversionid}',
                 '{$item['unit']}'
            );";

            $this->db->query($query);
            $this->_record_purchase_return_cost($item, $this->input->post('rdate', TRUE), $inserted_id, $item['rstore'], $this->db->insert_id());
        }

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate)
        VALUES (
            0,
            'purchase_return',
            'insert',
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        // $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        // $company_info     = $this->service_model->company_info();
        // $currency_details = $this->service_model->web_setting();
        // $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        // $saledetails = $this->saledetails($inserted_id);


        // $data = array(
        //     'invoice_all_data' => $saledetails,
        //     'total' => $this->input->post('total', TRUE),
        //     'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
        //     'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
        //     'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
        //     'grandTotal' => $this->input->post('grandTotal', TRUE),
        //     'customer_info'   => $customer_info,
        //     'customer_name'   => $customer_info->customer_name,
        //     'customer_address' => $customer_info->customer_address,
        //     'customer_mobile' => $customer_info->customer_mobile,
        //     'customer_email'  => $customer_info->customer_email,
        //     'company_info'    => $company_info,
        //     'currency_details' => $currency_details,
        //     'date'    => $this->input->post('date', TRUE),
        //     'details'    => $this->input->post('details', TRUE),
        //     'invoiceno' => $num,
        //     'payment' => $this->input->post('payment', TRUE)
        // );

        //$data['details'] = $this->load->view('invoice/pos_print',  $data, true);
        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Save failed. Please try again.']);
            return;
        }
        // _build_purchase_return_print() already returns a json_encode()'d
        // string (same shape as _build_sales_return_print) — wrapping it
        // directly in another json_encode() double-encodes it, same bug
        // fixed earlier for Sales Return. Decode first so 'html'/'invoiceno'
        // come through as real values, not an escaped JSON string.
        $print_data = json_decode($this->_build_purchase_return_print($inserted_id), true);
        echo json_encode(['success' => true, 'html' => $print_data['details'] ?? '', 'invoiceno' => $print_data['invoiceno'] ?? '']);
    }

    public function number_generatorsales_return($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(purchase_return_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('purchase_return');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 7000000001;
            } else {
                $invoice_no = 7700000001;
            }
        }
        return $invoice_no;
    }


    public function getPurchaseReturnById()
    {

        $encryption_key = Config::$encryption_key;


        $this->db->select("
         po.id, 
         AES_DECRYPT(po.purchase_return_id, '$encryption_key') AS purchase_return_id,
         pod.id as invoice_detail_id,
         si.supplier_id,
         po.date, 
           po.branch, 
                      po.rdate, 
         po.details, 
 po.payment_type, 
          po.incidenttype,
         AES_DECRYPT(po.discount, '" . $encryption_key . "') AS discount, 
         AES_DECRYPT(po.total_discount_ammount, '" . $encryption_key . "') AS total_discount_ammount, 
         AES_DECRYPT(po.total_vat_amnt, '" . $encryption_key . "') AS total_vat_amnt, 
         AES_DECRYPT(po.grandTotal, '" . $encryption_key . "') AS grandTotal, 
         AES_DECRYPT(po.total, '" . $encryption_key . "') AS total,
         pod.product,
         pod.store,
                                    pod.rstore,
         pod.unit,  AES_DECRYPT(pi.cost_price, '{$encryption_key}') as cost_price,
    po.branch, pod.conversion_id,cr.convertiontype,cr.conversion_ratio,pod.batch,
 CAST( ROUND(
    CASE
        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '+'
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') + cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '-'
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') - cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '*'
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') * cr.conversion_ratio

        WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '/'
            THEN AES_DECRYPT(pod.quantity, '{$encryption_key}') / cr.conversion_ratio

        ELSE AES_DECRYPT(pod.quantity, '{$encryption_key}')
    END,
     6) AS SIGNED
) AS quantity,
    CAST(
    ROUND(
        CASE
            WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '+'
                THEN AES_DECRYPT(pod.rqty, '{$encryption_key}') + cr.conversion_ratio

            WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '-'
                THEN AES_DECRYPT(pod.rqty, '{$encryption_key}') - cr.conversion_ratio

            WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '*'
                THEN AES_DECRYPT(pod.rqty, '{$encryption_key}') * cr.conversion_ratio

            WHEN pod.conversion_id IS NOT NULL AND cr.convertiontype = '/'
                THEN AES_DECRYPT(pod.rqty, '{$encryption_key}') / cr.conversion_ratio

            ELSE AES_DECRYPT(pod.rqty, '{$encryption_key}')
        END,
        6
    ) AS SIGNED
) AS rqty,
         AES_DECRYPT(pod.product_rate, '" . $encryption_key . "') AS product_rate,
         AES_DECRYPT(pod.discount, '" . $encryption_key . "') AS discount2,
         AES_DECRYPT(pod.discount_value, '" . $encryption_key . "') AS discount_value,
         AES_DECRYPT(pod.vat_percent, '" . $encryption_key . "') AS vat_percent,
         AES_DECRYPT(pod.vat_value,'" . $encryption_key . "') AS vat_value,
         AES_DECRYPT(pod.total_price, '" . $encryption_key . "') AS total_price,
         AES_DECRYPT(pod.total_discount, '" . $encryption_key . "') AS total_discount,
         AES_DECRYPT(pod.all_discount,'" . $encryption_key . "') AS all_discount,
           (
        SELECT 
            CASE
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                WHEN pod.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
            END
        FROM stock_details c 
        LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pod.conversion_id
        WHERE pod.product = c.product
          AND pod.store = c.store 
          AND pod.batch = c.batch 
    ) AS avstock,pi.batchtype,po.invoicetype,AES_DECRYPT(sod.purchase_id, '{$encryption_key}') as purchase_id 
     ,AES_DECRYPT(pod.rdeduction, '{$encryption_key}') as rdeduction,AES_DECRYPT(sod.chalan_no, '{$encryption_key}') as chalan_no  ");
        $this->db->from('purchase_return po');
        $this->db->join('supplier_information si', 'si.supplier_id = po.supplier_id', 'inner');
        $this->db->join('purchase_return_details pod', 'pod.pid = po.id', 'inner');
        $this->db->join('purchase sod', 'sod.id = po.purchase_id', 'left');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pod.conversion_id', 'left');
        $this->db->join('product_information pi', 'pi.id = pod.product', 'inner');

        $this->db->where('po.id', $this->input->post('id'));


        // $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();



        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array(), JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode("Thayaan");
        }
    }


    public function update_purchase_return()
    {
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');


        $lastupdate = date('Y-m-d H:i:s');


        $query = "
    UPDATE purchase_return
    SET 
        date = '{$this->input->post('purchase_date', TRUE)}',
       rdate = '{$this->input->post('rdate', TRUE)}',
        type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
        payment_type = '{$this->input->post('payment_type', TRUE)}',
        details = '{$this->input->post('details', TRUE)}',
        discount = AES_ENCRYPT('{$this->input->post('discount', TRUE)}', '{$encryption_key}'),
        total_discount_ammount = AES_ENCRYPT('{$this->input->post('total_discount_ammount', TRUE)}', '{$encryption_key}'),
        total_vat_amnt = AES_ENCRYPT('{$this->input->post('total_vat_amnt', TRUE)}', '{$encryption_key}'),
        grandTotal = AES_ENCRYPT('{$this->input->post('grandTotal', TRUE)}', '{$encryption_key}'),
        total = AES_ENCRYPT('{$this->input->post('total', TRUE)}', '{$encryption_key}'),
        supplier_id = '{$this->input->post('supplier_id', TRUE)}',
         lastupdateddate='{$lastupdate}',
         userid='{$this->session->userdata('id')}',
          incidenttype= '{$this->input->post('incidenttype', TRUE)}',
          branch='{$this->input->post('branch', TRUE)}',
        invoicetype='{$this->input->post('invoicetype', TRUE)}',
         already=0
    WHERE id = '{$this->input->post('id', TRUE)}';
";

        // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-edit state before anything
        // below mutates it.
        $old_preturn_date_row = $this->db->select('rdate')->from('purchase_return')->where('id', $this->input->post('id', TRUE))->get()->row();
        $old_preturn_date = $old_preturn_date_row ? $old_preturn_date_row->rdate : $this->input->post('rdate', TRUE);
        $old_preturn_pairs = $this->db->select('product, batch')->from('purchase_return_details')->where('pid', $this->input->post('id', TRUE))->get()->result_array();

        $this->db->trans_begin();
        try {
        $this->db->query($query);


        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'purchase_return')
            ->delete('stock_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'purchase_return')
            ->delete('phystock_details');

        // $this->db->where('pid', $this->input->post('id', TRUE))
        //     ->delete('purchase_return_details');

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('scenario', 'Purchase Return')
            ->delete('audit_stock');


        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            $qu = $item['quantity'];
            $rqty = -$item['rqty'];
             $aqty ="-".$item['aqty'];

            if ($item['isstock'] == 1) {

            $query1 = "
            INSERT INTO stock_details 
            (id,product, store,batch, stock, type, pid,date,conversion_id,unit) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['rstore']}', 
                '{$item['batch']}', 
             AES_ENCRYPT('{$rqty}', '{$encryption_key}'),
             'purchase_return',
             '{$this->input->post('id', TRUE)}', '{$this->input->post('rdate', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
            );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($this->input->post('rdate', TRUE)) . ",
             'Purchase Return',
            'Purchase Return',
            AES_ENCRYPT(" . $this->db->escape($this->input->post('invoice_id1', TRUE)) . ", '{$encryption_key}'),
              (SELECT purchase_return_id FROM purchase_return WHERE id = " . $this->db->escape($this->input->post('id', TRUE)) . " LIMIT 1),
            " . $this->db->escape($this->input->post('id', TRUE)) . ",
            " . $this->db->escape($item['rstore']) . ",
            AES_ENCRYPT(" . $this->db->escape($aqty) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($rqty) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";


                $this->db->query($query1);
                $this->db->query($query2);
            }

            $store   =   $this->db->select("auto_gdn")->from('store ')->where('id', $item['rstore'])->get()->row();
            if ($store->auto_gdn == 0) {
                if ($item['isstock'] == 1) {

                $query1 = "
                INSERT INTO phystock_details 
                (id,product, store,batch, stock, type, pid,date,conversion_id,unit) 
                VALUES 
                (0, 
                 '{$item['product']}', 
                 '{$item['rstore']}', 
                  '{$item['batch']}', 
                 AES_ENCRYPT('{$rqty}', '{$encryption_key}'),
                 'purchase_return',
                 '{$this->input->post('id', TRUE)}','{$this->input->post('rdate', TRUE)}',
               '{$conversionid}', 
                 '{$item['unit']}'
                );
            ";
            $query2 = "
            INSERT INTO audit_stock
            (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
             astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
            VALUES (
                " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
                " . $this->db->escape($this->input->post('rdate', TRUE)) . ",
                'Purchase Return',
                'Purchase Return',
            AES_ENCRYPT(" . $this->db->escape($this->input->post('invoice_id1', TRUE)) . ", '{$encryption_key}'),
              (SELECT purchase_return_id FROM purchase_return WHERE id = " . $this->db->escape($this->input->post('id', TRUE)) . " LIMIT 1),
             " . $this->db->escape($this->input->post('id', TRUE)) . ",
                " . $this->db->escape($item['rstore']) . ",
                AES_ENCRYPT('', '{$encryption_key}'),
                AES_ENCRYPT(" . $this->db->escape( $aqty) . ", '{$encryption_key}'),
                AES_ENCRYPT('', '{$encryption_key}'),
                AES_ENCRYPT(" . $this->db->escape($rqty) . ", '{$encryption_key}'),
                " . $this->db->escape($lastupdate) . ",
                  AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
            );";
                        $this->db->query($query1);
                        $this->db->query($query2);
                }
            }



            // $query = "
            // INSERT INTO purchase_return_details 
            // (id, pid, product, store,batch, quantity, rstore, rqty,rdeduction, 
            // product_rate,discount,discount_value,vat_percent,vat_value,total_price,total_discount,all_discount,type2,conversion_id,unit) 
            // VALUES 
            // (0, 
            //  '{$this->input->post('id', TRUE)}', 
            //  '{$item['product']}', 
            //   '{$item['store']}', 
            //    '{$item['batch']}', 
            //  AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
            //    '{$item['rstore']}', 
            //  AES_ENCRYPT('{$item['rqty']}', '{$encryption_key}'),
            //   AES_ENCRYPT('{$item['rdeduction']}', '{$encryption_key}'),  
            //  AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
            //  AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'), 
            //  AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'), 
            //  AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'), 
            //  AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'), 
            //   AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'), 
            //   AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'), 
            //   AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
            //    AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
            //     '{$conversionid}', 
            //      '{$item['unit']}'
            // );";

            $query = "
UPDATE purchase_return_details SET
    pid = '{$this->input->post('id', TRUE)}',
    product = '{$item['product']}',
    store = '{$item['store']}',
    batch = '{$item['batch']}',
    quantity = AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
    rstore = '{$item['rstore']}',
    rqty = AES_ENCRYPT('{$item['rqty']}', '{$encryption_key}'),
    rdeduction = AES_ENCRYPT('{$item['rdeduction']}', '{$encryption_key}'),
    product_rate = AES_ENCRYPT('{$item['product_rate']}', '{$encryption_key}'),
    discount = AES_ENCRYPT('{$item['discount']}', '{$encryption_key}'),
    discount_value = AES_ENCRYPT('{$item['discount_value']}', '{$encryption_key}'),
    vat_percent = AES_ENCRYPT('{$item['vat_percent']}', '{$encryption_key}'),
    vat_value = AES_ENCRYPT('{$item['vat_value']}', '{$encryption_key}'),
    total_price = AES_ENCRYPT('{$item['total_price']}', '{$encryption_key}'),
    total_discount = AES_ENCRYPT('{$item['total_discount']}', '{$encryption_key}'),
    all_discount = AES_ENCRYPT('{$item['all_discount']}', '{$encryption_key}'),
    type2 = AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}'),
    conversion_id = '{$conversionid}',
    unit = '{$item['unit']}'
WHERE id = '{$item['invoicedetail']}'
";



            $this->db->query($query);
            $this->_record_purchase_return_cost($item, $this->input->post('rdate', TRUE), $this->input->post('id', TRUE), $item['rstore'], $item['invoicedetail']);
        }

        // Unit Cost Logic Specification v1.1 (Phase 5) — re-run every Product+Batch this
        // voucher touched, before or after the edit, from whichever date is earlier.
        $new_preturn_pairs = $old_preturn_pairs;
        foreach ($items as $item) {
            $new_preturn_pairs[] = ['product' => $item['product'], 'batch' => $item['batch']];
        }
        $from_date_for_cost = ($old_preturn_date <= $this->input->post('rdate', TRUE)) ? $old_preturn_date : $this->input->post('rdate', TRUE);
        $this->_rerun_affected_pairs($new_preturn_pairs, $from_date_for_cost);

        $query = "
    INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate)
    VALUES (
        0,
        'purchase_return',
        'update',
        '{$this->input->post('id', TRUE)}', 
        '{$this->session->userdata('id')}',  '{$lastupdate}'
    );
";

        $this->db->query($query);

        // $customer_info    =  $this->customer_info($this->input->post('customer_id', TRUE));
        // $company_info     = $this->service_model->company_info();
        // $currency_details = $this->service_model->web_setting();
        // $invoiceno = $this->invoice_no($this->input->post('id', TRUE));

        // $saledetails = $this->saledetails($this->input->post('id', TRUE));

        // $data = array(
        //     'invoice_all_data' => $saledetails,
        //     'total' => $this->input->post('total', TRUE),
        //     'total_dis' => $this->input->post('discount', TRUE) == "" ? "0.0" : $this->input->post('discount', TRUE),
        //     'total_discount_ammount' => $this->input->post('total_discount_ammount', TRUE),
        //     'total_vat_amnt' => $this->input->post('total_vat_amnt', TRUE),
        //     'grandTotal' => $this->input->post('grandTotal', TRUE),
        //     'customer_info'   => $customer_info,
        //     'customer_name'   => $customer_info->customer_name,
        //     'customer_address' => $customer_info->customer_address,
        //     'customer_mobile' => $customer_info->customer_mobile,
        //     'customer_email'  => $customer_info->customer_email,
        //     'company_info'    => $company_info,
        //     'currency_details' => $currency_details,
        //     'date'    => $this->input->post('date', TRUE),
        //     'details'    => $this->input->post('details', TRUE),
        //     'invoiceno' => $invoiceno[0]['sale_id'],
        //     'payment' => $this->input->post('payment', TRUE)
        // );

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Update failed. Please try again.']);
            return;
        }
        $print_data = json_decode($this->_build_purchase_return_print($this->input->post('id', TRUE)), true);
        echo json_encode(['success' => true, 'html' => $print_data['details'] ?? '', 'invoiceno' => $print_data['invoiceno'] ?? '']);
    }


    public function delete_purchasereturn($id = null)
    {
        $lastupdate = date('Y-m-d H:i:s');

        $productExists = $this->db->from('gdn_stock')
        ->where('voucherno', $id)
        ->where('type', 'purchasereturn')
        ->count_all_results();

        
        $base_url = base_url();


        if ($productExists > 0) {

            echo '<script type="text/javascript">
            alert("Cannot delete this purchase return detail because this sale detail is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'manage_purchase_return";
           </script>';
        } else {
            // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-delete state.
            $old_preturn_pairs = $this->db->select('product, batch')->from('purchase_return_details')->where('pid', $id)->get()->result_array();
            $old_preturn_date_row = $this->db->select('rdate')->from('purchase_return')->where('id', $id)->get()->row();
            $old_preturn_date = $old_preturn_date_row ? $old_preturn_date_row->rdate : null;

            $this->db->where('pid', $id)
                ->where('type', 'purchase_return')
                ->delete('stock_details');

            $this->db->where('pid', $id)
                ->where('type', 'purchase_return')
                ->delete('phystock_details');

            $this->db->where('pid', $id)
                ->delete('purchase_return_details');

            $this->db->where('purchase_return_id', $id)
                ->delete('supplier_due');

            $this->db->where('id', $id)
                ->delete('purchase_return');

                $this->db->where('pid', $id)
                ->where('scenario', 'Purchase Return')
                ->delete('audit_stock');

            // Unit Cost Logic Specification v1.1 (Phase 5) — re-cost every Product+Batch this
            // deleted purchase return had affected.
            $this->_rerun_affected_pairs($old_preturn_pairs, $old_preturn_date);

            $query = "
                INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
                VALUES (
                    0, 
                    'purchase_return', 
                    'update', 
                    '{$id}', 
                    '{$this->session->userdata('id')}',  '{$lastupdate}'
                );
            ";

            $this->db->query($query);


            echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'manage_purchase_return";
  </script>';
        }
    }

    public function save_supplier_ajax()
    {
        $encryption_key = Config::$encryption_key;
        $name  = $this->input->post('supplier_name', TRUE);
        $phone = $this->input->post('supplier_phone', TRUE);
        if (!$name) { echo json_encode(['error' => 'Name required']); return; }
        $query = "INSERT INTO supplier_information
            (supplier_name, mobile, supplier_calling_name, supplier_billing_name, nic_no, status)
            VALUES (
                AES_ENCRYPT('{$name}', '{$encryption_key}'),
                AES_ENCRYPT('{$phone}', '{$encryption_key}'),
                AES_ENCRYPT('{$name}', '{$encryption_key}'),
                AES_ENCRYPT('{$name}', '{$encryption_key}'),
                AES_ENCRYPT('', '{$encryption_key}'),
                1
            )";
        $this->db->query($query);
        echo json_encode(['inserted_id' => $this->db->insert_id(), 'supplier_name' => $name]);
    }

    private function _build_purchase_return_print($id)
    {
        $encryption_key = Config::$encryption_key;

        $header = $this->db->select("
            pr.id,
            AES_DECRYPT(pr.purchase_return_id, '{$encryption_key}') AS return_no,
            pr.supplier_id,
            pr.rdate AS date,
            AES_DECRYPT(pr.discount, '{$encryption_key}') AS discount,
            AES_DECRYPT(pr.total_discount_ammount, '{$encryption_key}') AS total_discount_ammount,
            AES_DECRYPT(pr.total_vat_amnt, '{$encryption_key}') AS total_vat_amnt,
            AES_DECRYPT(pr.grandTotal, '{$encryption_key}') AS grandTotal,
            AES_DECRYPT(pr.total, '{$encryption_key}') AS total,
            AES_DECRYPT(p.chalan_no, '{$encryption_key}') AS purchase_chalan_no
        ")
        ->from('purchase_return pr')
        ->join('purchase p', 'p.id = pr.purchase_id', 'left')
        ->where('pr.id', $id)
        ->get()->row_array();

        if (empty($header)) {
            return json_encode(['details' => '']);
        }

        $items = $this->db->select("
            pi.product_name,
            CAST(ROUND(CASE
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '+'
                    THEN (CAST(AES_DECRYPT(prd.quantity, '{$encryption_key}') AS CHAR) + 0) + cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '-'
                    THEN (CAST(AES_DECRYPT(prd.quantity, '{$encryption_key}') AS CHAR) + 0) - cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '*'
                    THEN (CAST(AES_DECRYPT(prd.quantity, '{$encryption_key}') AS CHAR) + 0) * cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '/'
                    THEN (CAST(AES_DECRYPT(prd.quantity, '{$encryption_key}') AS CHAR) + 0) / cr.conversion_ratio
                ELSE CAST(AES_DECRYPT(prd.quantity, '{$encryption_key}') AS CHAR) + 0
            END, 4) AS DECIMAL(18,4)) AS quantity,
            CAST(ROUND(CASE
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '+'
                    THEN (CAST(AES_DECRYPT(prd.rqty, '{$encryption_key}') AS CHAR) + 0) + cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '-'
                    THEN (CAST(AES_DECRYPT(prd.rqty, '{$encryption_key}') AS CHAR) + 0) - cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '*'
                    THEN (CAST(AES_DECRYPT(prd.rqty, '{$encryption_key}') AS CHAR) + 0) * cr.conversion_ratio
                WHEN prd.conversion_id IS NOT NULL AND cr.convertiontype = '/'
                    THEN (CAST(AES_DECRYPT(prd.rqty, '{$encryption_key}') AS CHAR) + 0) / cr.conversion_ratio
                ELSE CAST(AES_DECRYPT(prd.rqty, '{$encryption_key}') AS CHAR) + 0
            END, 4) AS DECIMAL(18,4)) AS rqty,
            AES_DECRYPT(prd.product_rate, '{$encryption_key}') AS product_rate,
            AES_DECRYPT(prd.rdeduction, '{$encryption_key}') AS rdeduction,
            AES_DECRYPT(prd.discount_value, '{$encryption_key}') AS discount_value,
            AES_DECRYPT(prd.vat_value, '{$encryption_key}') AS vat_value,
            AES_DECRYPT(prd.total_price, '{$encryption_key}') AS total_price,
            u.unit_name
        ")
        ->from('purchase_return_details prd')
        ->join('product_information pi', 'pi.id = prd.product', 'left')
        ->join('conversion_ratio cr', 'cr.conversionratio_id = prd.conversion_id', 'left')
        ->join('units u', 'u.unit_id = prd.unit', 'left')
        ->where('prd.pid', $id)
        ->get()->result_array();

        $supplier_info    = $this->supplier_info($header['supplier_id']);
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();

        $data = array(
            'invoice_all_data'       => $items,
            'total'                  => $header['total'],
            'total_dis'              => $header['discount'] == '' ? '0.0' : $header['discount'],
            'total_discount_ammount' => $header['total_discount_ammount'],
            'total_vat_amnt'         => $header['total_vat_amnt'],
            'grandTotal'             => $header['grandTotal'],
            'supplier_name'          => $supplier_info->supplier_name,
            'address'                => $supplier_info->address,
            'mobile'                 => $supplier_info->mobile,
            'emailnumber'            => $supplier_info->emailnumber,
            'email_address'          => $supplier_info->email_address,
            'contact'                => $supplier_info->contact,
            'company_info'           => $company_info,
            'currency_details'       => $currency_details,
            'date'                   => $header['date'],
            'invoiceno'              => $header['return_no'],
            'purchase_chalan_no'     => $header['purchase_chalan_no'],
            'details'                => '',
        );

        $data['details'] = $this->load->view('purchase/pos_print_purchase_return', $data, true);
        return json_encode($data);
    }

    public function pos_print_purchase_return()
    {
        echo $this->_build_purchase_return_print($this->input->post('id', TRUE));
    }
    private function _log_error($method, $message)
    {
        $log_dir = FCPATH . 'logs/';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        $log_file = $log_dir . date('Y-m-d') . '.log';
        $entry = '[' . date('Y-m-d H:i:s') . '] [' . $method . '] ' . $message . PHP_EOL;
        file_put_contents($log_file, $entry, FILE_APPEND | LOCK_EX);
    }

}
