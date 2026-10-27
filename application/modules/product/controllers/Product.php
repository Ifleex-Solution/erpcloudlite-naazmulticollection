<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Product extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'product_model',
            'supplier/supplier_model',
            'hrm/country_model'
        ));
        $this->load->library('ciqrcode');
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }

    // category part
    function bdtask_category_list()
    {
        $data['title']      = "Category List";
        $data['module']     = "product";
        $data['page']       = "category_list";
        $data["category_list"] = $this->product_model->category_list();
        if (!$this->permission1->method('manage_category', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_category_form($id = null)
    {
        $data['title'] = display('add_category');
        #-------------------------------#
        $this->form_validation->set_rules('category_name', display('category_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['category'] = (object)$postData = [
            'category_id'      => $id,
            'category_name'    => $this->input->post('category_name', true),
            'status'           => $this->input->post('status', true),
        ];

        if (!$id) {
            if (!$this->permission1->method('manage_category', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('manage_category', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_category($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Category Details Saved successfully");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Category Details Saved successfully");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                }
            } else {
                if ($this->product_model->update_category($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Category Details Updated successfully");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Category Details Updated successfully");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'category_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = display('edit_category');
                $data['category'] = $this->product_model->single_category_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "category_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function check_duplicate_category()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('category_id')->from('product_category')->where('category_name', $value);
        if (!empty($exclude_id)) { $this->db->where('category_id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A category with this name already exists.' : '']);
    }

    public function bdtask_deletecategory($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_category($id)) {
            echo '<script type="text/javascript">
            alert("Category Details Deleted successfully");
            window.location.href = "' . $base_url . 'category_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this category because products are linked to it or something went wrong");
            window.location.href = "' . $base_url . 'category_list";
           </script>';
        }
    }

    // unit part
    function bdtask_unit_list()
    {
        $data['title']      = "Unit List";
        $data['module']     = "product";
        $data['page']       = "unit_list";
        $data["unit_list"] = $this->product_model->unit_list();
        if (!$this->permission1->method('manage_unit', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_unit_form($id = null)
    {
        $data['title'] = display('add_unit');
        #-------------------------------#
        $this->form_validation->set_rules('unit_name', display('unit_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['unit'] = (object)$postData = [
            'unit_id'      => $id,
            'unit_name'    => $this->input->post('unit_name', true),
            'unit_display_name'    => $this->input->post('unit_display_name', true),

            'status'       => $this->input->post('status', true),
        ];

        if (!$id) {
            if (!$this->permission1->method('manage_unit', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('manage_unit', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        $base_url = base_url();
        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_unit($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Unit Details Saved successfully");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Unit Details Saved successfully");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                }
            } else {


                if ($this->product_model->update_unit($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Unit Details Updated successfully");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Unit Details Updated successfully");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'unit_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = display('edit_unit');
                $data['unit'] = $this->product_model->single_unit_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "unit_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function check_duplicate_unit()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('unit_id')->from('units')->where('unit_name', $value);
        if (!empty($exclude_id)) { $this->db->where('unit_id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A unit with this name already exists.' : '']);
    }

    public function bdtask_deleteunit($id = null)
{
    $base_url = base_url();

    if ($this->product_model->delete_unit($id)) {
        echo '<script type="text/javascript">
        alert("Unit Details Deleted successfully");
        window.location.href = "' . $base_url . 'unit_list";
       </script>';
    } else {
        echo '<script type="text/javascript">
        alert("Cannot delete this Unit because it is being used in product information, stock transactions, subunit products, or conversion ratios. Please remove all references before deleting.");
        window.location.href = "' . $base_url . 'unit_list";
       </script>';
    }
}

    public function check_duplicate_product()
    {
        $value = trim($this->input->post('value', FALSE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('id')->from('product_information')->where('product_name', $value);
        if (!empty($exclude_id)) { $this->db->where('id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A product with this name already exists.' : '']);
    }

    public function getProductById()
    {
        $this->db->select('*');
        $this->db->from('product_information a');
        $this->db->where('a.product_id', $this->input->post('code'));
        $query = $this->db->get();
        $num_rows = $query->num_rows();
        if ($num_rows > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }

    public function getActivefloorBystoreId()
    {
        $data = $this->product_model->active_floorByStore($this->input->post('id', TRUE));
        echo json_encode($data);
    }


    // product part
    public function bdtask_product_form($id = null)
    {
        $data['title'] = display('add_product');
        $data['product_open']   = null;
        #-------------------------------#
        $this->form_validation->set_rules('product_name', display('product_name'), 'required|max_length[200]');
        $this->form_validation->set_rules('subcategory_id', 'Subcategory', 'required|max_length[20]');
        $this->form_validation->set_rules('unit', display('unit'), 'required');
        $this->form_validation->set_rules('status', "Status", 'required');
        $this->form_validation->set_rules('store', "Store", 'required');
        $this->form_validation->set_rules('product_type', 'Product Type', 'required');

        if (!$id) {
            if (!$this->permission1->method('manage_product', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('manage_product', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        $product_id = (!empty($this->input->post('product_id', TRUE)) ? $this->input->post('product_id', TRUE) : $this->generator(8));
        $sup_price = $this->input->post('supplier_price', TRUE);
        // $s_id      = $this->input->post('supplier_id',TRUE);
        $product_model = $this->input->post('model', TRUE);
        $taxfield = $this->db->select('tax_name,default_value')
            ->from('tax_settings')
            ->get()
            ->result_array();




        #-------------------------------#
        $data['product'] = (object)$postData = [
            'product_id'     => $this->input->post('product_id', TRUE),
            'product_name'   => $this->input->post('product_name', TRUE),
            'category_id'    => $this->input->post('category_id', TRUE),
            'unit'           => $this->input->post('unit', TRUE),
            'product_vat'            => $this->input->post('vat', TRUE),
            'serial_no'      => $this->input->post('serial_no', TRUE),
            'price'          => $this->input->post('price', TRUE) > 0 ? $this->input->post('price', TRUE) : 0.0,
            'product_model'  => $this->input->post('model', TRUE),
            'cost_price'  => $this->input->post('cost_price', TRUE),

            'product_details' => $this->input->post('description', TRUE),
            'store'  => $this->input->post('store', TRUE),
            // 'floor' => $this->input->post('floor', TRUE),
            // 'product_vat'    => $this->input->post('product_vat', TRUE),
            // 'image'          => (!empty($image) ? $image : 'my-assets/image/product.png'),
            'status'         => $this->input->post('status', TRUE),
            'supplier_id'    => $this->input->post('supplier_id', TRUE),
            'product_type'   => $this->input->post('product_type', TRUE),
            'stock'          => $this->input->post('stock', TRUE),
            'max_stock_level' => $this->input->post('max_stock_level', TRUE),
            'min_stock_level' => $this->input->post('min_stock_level', TRUE),
            'reorder_stock_level' => $this->input->post('reorder_stock_level', TRUE),
            'reserve_stock_level' => $this->input->post('reserve_stock_level', TRUE),
        ];

        $tablecolumn = $this->db->list_fields('tax_collection');
        $num_column = count($tablecolumn) - 4;
        if ($num_column > 0) {
            $txf = [];
            for ($i = 0; $i < $num_column; $i++) {
                $txf[$i] = 'tax' . $i;
            }
            foreach ($txf as $key => $value) {
                $postData[$value] = (!empty($this->input->post($value)) ? $this->input->post($value) : 0) / 100;
            }
        }
        $encryption_key = Config::$encryption_key;



        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                $query = "
    INSERT INTO product_information 
    (product_id, product_name, category_id, unit, product_vat, serial_no, price, product_model, cost_price, product_details, 
    store, status,sprice,subunit,scost_price,subcategory_id,brand_id,oop_id,printname,supplier_id,product_type,stock,max_stock_level,min_stock_level,reorder_stock_level,reserve_stock_level) 
    VALUES 
    ('{$this->input->post('product_id', TRUE)}',
     '{$this->input->post('product_name', TRUE)}',
     '{$this->input->post('category_id', TRUE)}',
     '{$this->input->post('unit', TRUE)}',
     '{$this->input->post('vat', TRUE)}',
     '{$this->input->post('serial_no', TRUE)}',
     AES_ENCRYPT('{$this->input->post('price', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('model', TRUE)}',
     AES_ENCRYPT('{$this->input->post('cost_price', TRUE)}', '{$encryption_key}'),
     '{$this->input->post('description', TRUE)}',
     '{$this->input->post('store', TRUE)}',
     '{$this->input->post('status', TRUE)}',
     AES_ENCRYPT('{$this->input->post('sprice', TRUE)}', '{$encryption_key}'),
          '{$this->input->post('subunit', TRUE)}',
     AES_ENCRYPT('{$this->input->post('scost_price', TRUE)}', '{$encryption_key}'),
          '{$this->input->post('subcategory_id', TRUE)}',
     '{$this->input->post('brand_id', TRUE)}',
     '{$this->input->post('oop_id', TRUE)}',
        '{$this->input->post('printname', TRUE)}',
    '{$this->input->post('supplier_id', TRUE)}',
    '{$this->input->post('product_type', TRUE)}',
    '{$this->input->post('stock', TRUE)}',
    '{$this->input->post('max_stock_level', TRUE)}',
    '{$this->input->post('min_stock_level', TRUE)}',
    '{$this->input->post('reorder_stock_level', TRUE)}',
    '{$this->input->post('reserve_stock_level', TRUE)}'

    );";


                if ($this->product_model->create_product($query)) {
                    if ($this->input->post('button', true) == "add-another") {
                        echo '
                        <script type="text/javascript">
                        alert("Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'product_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'product_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if ($this->input->post('button', true) == "add-another") {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'product_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'product_list";
                       </script>';
                    }
                }
            } else {
                $query = "
    UPDATE product_information 
    SET 
        product_name = '{$this->input->post('product_name', TRUE)}',
        category_id = '{$this->input->post('category_id', TRUE)}',
        unit = '{$this->input->post('unit', TRUE)}',
        product_vat = '{$this->input->post('vat', TRUE)}',
        serial_no = '{$this->input->post('serial_no', TRUE)}',
        price = AES_ENCRYPT('{$this->input->post('price', TRUE)}', '{$encryption_key}'),
        product_model = '{$this->input->post('model', TRUE)}',
        cost_price = AES_ENCRYPT('{$this->input->post('cost_price', TRUE)}', '{$encryption_key}'),
        product_details = '{$this->input->post('description', TRUE)}',
        store = '{$this->input->post('store', TRUE)}',
        status = '{$this->input->post('status', TRUE)}',
        sprice= AES_ENCRYPT('{$this->input->post('sprice', TRUE)}', '{$encryption_key}'),
        subunit=  '{$this->input->post('subunit', TRUE)}',
                printname=  '{$this->input->post('printname', TRUE)}',
        scost_price= AES_ENCRYPT('{$this->input->post('scost_price', TRUE)}', '{$encryption_key}'),
        supplier_id = '{$this->input->post('supplier_id', TRUE)}',
        product_type = '{$this->input->post('product_type', TRUE)}',
        stock = '{$this->input->post('stock', TRUE)}',
        max_stock_level = '{$this->input->post('max_stock_level', TRUE)}',
        min_stock_level = '{$this->input->post('min_stock_level', TRUE)}',
        reorder_stock_level = '{$this->input->post('reorder_stock_level', TRUE)}',
        reserve_stock_level = '{$this->input->post('reserve_stock_level', TRUE)}'
    WHERE id = '{$id}';
";
                if ($this->product_model->update_product($query)) {
                    if ($this->input->post('button', true) == "add-another") {
                        echo '
        <script type="text/javascript">
        alert("Product Details Updated successfully");
        window.location.href = "' . $base_url . 'product_form";
       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
        alert("Product Details Updated successfully");
        window.location.href = "' . $base_url . 'product_list";
       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if ($this->input->post('button', true) == "add-another") {
                        echo '
        <script type="text/javascript">
        alert("' . $message . '");
        window.location.href = "' . $base_url . 'product_form";
       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
        alert("' . $message . '");
        window.location.href = "' . $base_url . 'product_list";
       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']         = display('edit_product');
                $data['product']       = $this->product_model->single_product_data($id);
                $data['subunit_product'] = $this->product_model->single_subunit_product($id);
                foreach ($data['subunit_product'] as $row) {
                    $row->is_ratio_locked = !empty($row->conversionratio_id)
                        && $this->product_model->check_conversionratio_transactions($row->conversionratio_id);
                }
                $data['subunit_conversions'] = $this->product_model->get_conversionrations($id);
                $data['product_id']    = !empty($data['product']->product_id) ? $data['product']->product_id : '';
            } else {
                $sql3 = "SELECT MAX(product_id)+1 AS highest_product_id FROM product_information;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['productId'] = !empty($result3->highest_product_id) ? str_pad($result3->highest_product_id, 6, '0', STR_PAD_LEFT) : "000001";
            }
            $data['supplier']      = $this->product_model->supplier_list();
            $data['vattaxinfo']    = $this->product_model->vat_tax_setting();
            $data['id']            =  $id;
            $data['subcategory_list'] = $this->product_model->active_subcategory() ?: [];
            $data['category_list'] = $this->product_model->active_category();

            $data['brand_list'] = $this->product_model->active_brand();
            $data['oop_list'] = $this->product_model->active_oop();
            $data['country_list'] = $this->country_model->country();


            $data['store_list'] = $this->product_model->active_store();
            $data['unit_list']     = $this->product_model->active_unit();
            $data['supplier_pr']   = $this->product_model->supplier_product_list($id);
            $data['product_open']   = $this->product_model->product_opening($id);
            $data['vtinfo']   = $this->db->select('*')->from('vat_tax_setting')->get()->row();
            $data['taxfield']      = $taxfield;
            $ws = $this->db->select('product_image_upload')->from('web_setting')->get()->row();
            $data['product_image_upload'] = isset($ws->product_image_upload) ? (int)$ws->product_image_upload : 1;
            $data['module']        = "product";
            $data['page']          = "product_form";

            echo Modules::run('template/layout', $data);
        }
    }




    public function bdtask_product_list()
    {
        $data['title']         = display('manage_product');
        $data['total_product'] = $this->db->count_all("product_information");
        $data['module']        = "product";
        $data['page']          = "product_list";
        if (!$this->permission1->method('manage_product', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function CheckProductList()
    {
        $postData = $this->input->post();
        $data = $this->product_model->getProductList($postData);
        echo json_encode($data);
    }

    public function CheckProductGroupList()
    {
        $postData = $this->input->post();
        $data = $this->product_model->getProductGroupList($postData);
        echo json_encode($data);
    }

    public function bdtask_deleteproduct($id = null)
    {
        $base_url = base_url();
        if ($this->product_model->delete_product($id)) {
            echo '<script type="text/javascript">
            alert("Product Details Deleted successfully");
            window.location.href = "' . $base_url . 'product_list ";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Product  because this product Group is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'product_list";
           </script>';
        }
    }

     public function bdtask_deleteproductgroup($id = null)
    {
        $base_url = base_url();
        if ($this->product_model->delete_product_group($id)) {
            echo '<script type="text/javascript">
            alert("Product Group Details Deleted successfully");
            window.location.href = "' . $base_url . 'product_grouplist";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Product Group because this product is linked to it or something went wrong");
            window.location.href = "' . $base_url . 'product_grouplist";
           </script>';
        }
    }

    public function bdtask_csv_product()
    {
        $data['title']         = display('add_product_csv');
        $data['module']        = "product";
        $data['page']          = "add_product_csv";
        if (!$this->permission1->method('manage_product', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['categories']    = $this->product_model->active_category();
        $data['subcategories'] = $this->product_model->active_subcategory();
        $data['brands']        = $this->product_model->active_brand();
        $data['oops']          = $this->product_model->active_oop();
        $data['stores']        = $this->product_model->active_store();
        $data['units']         = $this->product_model->active_unit();
        $data['suppliers']     = $this->product_model->supplier_list();
        $data['all_product_ids'] = $this->product_model->all_product_ids();
        echo modules::run('template/layout', $data);
    }

    public function bdtask_data_loader()
    {
        $data['title']         = 'DUPL - Data up Loader';
        $data['module']        = 'product';
        $data['page']          = 'data_uploader';
        if (!$this->permission1->method('add_product_csv', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['categories']    = $this->product_model->active_category();
        $data['subcategories'] = $this->product_model->active_subcategory();
        $data['brands']        = $this->product_model->active_brand();
        $data['oops']          = $this->product_model->active_oop();
        $data['stores']        = $this->product_model->active_store();
        $data['units']         = $this->product_model->active_unit();
        $data['suppliers']     = $this->product_model->supplier_list();
        $data['products']                   = $this->product_model->all_products_for_csv();
        $data['all_product_names']          = $this->product_model->all_product_names();
        $data['product_subunits']           = $this->product_model->all_product_subunits();
        $data['existing_conversionratios']  = $this->product_model->all_existing_conversionratios();
        $data['all_brand_names']            = $this->product_model->all_brand_names();
        $data['all_category_names']         = $this->product_model->all_category_names();
        $data['all_subcategory_names']      = $this->product_model->all_subcategory_names();
        $data['all_unit_names']             = $this->product_model->all_unit_names();
        $this->load->model('account/accounts_model');
        $data['all_payment_method_names']   = $this->accounts_model->all_payment_method_names();
        $this->load->model('store/store_model');
        $data['all_branch_data']            = $this->store_model->all_branch_data();
        $data['all_store_data']             = $this->store_model->all_store_data();
        $this->load->model('customer/customer_model');
        $data['all_customer_names']         = $this->customer_model->all_customer_names();
        $this->load->model('supplier/supplier_model');
        $data['all_supplier_names']         = $this->supplier_model->all_supplier_names();
        $this->load->model('service/service_model');
        $data['all_service_names']          = $this->service_model->all_service_names();
        $data['all_service_codes']          = $this->service_model->all_service_codes();
        $data['all_productgroup_codes']     = $this->product_model->all_productgroup_codes();
        $data['all_stockbatches']           = $this->product_model->all_stockbatches_for_csv();
        $data['all_stockbatch_batchids']    = $this->product_model->all_stockbatch_batchids_for_csv();
        $data['all_conversion_ratios']      = $this->product_model->all_conversion_ratios_for_csv();
        $data['all_designation_names']      = $this->product_model->all_designation_names_for_csv();
        $data['all_designations']           = $this->product_model->all_designations_for_csv();
        $data['all_employee_ids']           = $this->product_model->all_employee_ids_for_csv();
        echo modules::run('template/layout', $data);
    }


    public function save_product_bulk_log()
    {
        $encryption_key = Config::$encryption_key;
        $product_ids    = $this->input->post('product_ids', TRUE);
        $lastupdate     = date('Y-m-d H:i:s');
        $ids_string     = is_array($product_ids) ? implode(',', $product_ids) : $product_ids;

        $this->db->query("INSERT INTO bulk_product_details (uploaded_id, date, uploadedby, product_ids)
            VALUES (NULL, '{$lastupdate}', '{$this->session->userdata('id')}', '{$ids_string}')");
        $inserted_id = $this->db->insert_id();
        $batch_label = 'PROD-' . $inserted_id;
        $this->db->query("UPDATE bulk_product_details SET uploaded_id = AES_ENCRYPT('{$batch_label}', '{$encryption_key}') WHERE id = {$inserted_id}");
        $csv_content  = $this->input->post('csv_content', FALSE);
        $csv_filename = $this->db->escape_str($this->input->post('csv_filename', FALSE) ?: 'upload.csv');
        if (!empty($csv_content)) {
            $csv_escaped = $this->db->escape_str($csv_content);
            $this->db->query("INSERT INTO bulk_upload_csv (batch_table, batch_id, original_csv, filename) VALUES ('bulk_product_details', {$inserted_id}, '{$csv_escaped}', '{$csv_filename}')");
        }
        echo json_encode('Success');
    }

    public function checkBulkProductUpload()
    {
        $postData = $this->input->post();
        $data     = $this->product_model->BulkProductUpload($postData);
        echo json_encode($data);
    }

    public function get_bulk_product_details($id)
    {
        $row = $this->db->select('product_ids')->where('id', (int)$id)->get('bulk_product_details')->row();
        if (!$row || empty($row->product_ids)) { echo json_encode([]); return; }

        $ids = array_filter(array_map('intval', explode(',', $row->product_ids)));
        if (empty($ids)) { echo json_encode([]); return; }

        $products = $this->db->select('id, product_id, product_name')
            ->where_in('id', $ids)
            ->get('product_information')
            ->result();

        echo json_encode($products);
    }

    public function delete_bulk_product($id)
    {
        $row = $this->db->select('product_ids')->where('id', (int)$id)->get('bulk_product_details')->row();

        if ($row && !empty($row->product_ids)) {
            $product_ids = array_filter(array_map('intval', explode(',', $row->product_ids)));
            if (!empty($product_ids)) {
                $this->db->where_in('product_id', $product_ids)->delete('subunit_product');
                $this->db->where_in('id', $product_ids)->delete('product_information');
            }
        }

        $this->db->where('id', (int)$id)->delete('bulk_product_details');
        echo json_encode('Success');
    }

    // ── Bulk Conversion Ratio CSV ─────────────────────────────────
    public function bdtask_csv_conversionratio()
    {
        $data['title']    = 'Bulk Conversion Ratio Upload';
        $data['module']   = 'product';
        $data['page']     = 'add_conversionratio_csv';
        if (!$this->permission1->method('add_product_csv', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['products'] = $this->product_model->active_product();
        $data['units']    = $this->product_model->active_unit();
        echo modules::run('template/layout', $data);
    }

    public function save_conversionratio_from_csv()
    {
        $product  = $this->input->post('product', TRUE);
        $subunit  = $this->input->post('subunit', TRUE);
        $ratio    = $this->input->post('conversion_ratio', TRUE);

        if ($this->product_model->check_duplicate_conversionratio($product, $subunit)) {
            echo json_encode(['status' => 'Error', 'message' => 'Duplicate: this product + subunit already exists']);
            return;
        }

        $row = ['product' => $product, 'subunit' => $subunit, 'conversion_ratio' => $ratio, 'convertiontype' => '*', 'status' => 1];
        if ($this->product_model->create_conversionratio($row)) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed']);
        }
    }

    public function save_conversionratio_bulk_log()
    {
        $encryption_key = Config::$encryption_key;
        $ids        = $this->input->post('conversionratio_ids', TRUE);
        $lastupdate = date('Y-m-d H:i:s');
        $ids_string = is_array($ids) ? implode(',', $ids) : $ids;

        $this->db->query("INSERT INTO bulk_conversionratio_details (uploaded_id, date, uploadedby, conversionratio_ids)
            VALUES (NULL, '{$lastupdate}', '{$this->session->userdata('id')}', '{$ids_string}')");
        $inserted_id = $this->db->insert_id();
        $batch_label = 'CR-' . $inserted_id;
        $this->db->query("UPDATE bulk_conversionratio_details SET uploaded_id = AES_ENCRYPT('{$batch_label}', '{$encryption_key}') WHERE id = {$inserted_id}");
        $csv_content  = $this->input->post('csv_content', FALSE);
        $csv_filename = $this->db->escape_str($this->input->post('csv_filename', FALSE) ?: 'upload.csv');
        if (!empty($csv_content)) {
            $csv_escaped = $this->db->escape_str($csv_content);
            $this->db->query("INSERT INTO bulk_upload_csv (batch_table, batch_id, original_csv, filename) VALUES ('bulk_conversionratio_details', {$inserted_id}, '{$csv_escaped}', '{$csv_filename}')");
        }
        echo json_encode('Success');
    }

    public function checkBulkConversionratioUpload()
    {
        $postData = $this->input->post();
        $data     = $this->product_model->BulkConversionratioUpload($postData);
        echo json_encode($data);
    }

    public function get_bulk_conversionratio_details($id)
    {
        $row = $this->db->select('conversionratio_ids')->where('id', (int)$id)->get('bulk_conversionratio_details')->row();
        if (!$row || empty($row->conversionratio_ids)) { echo json_encode([]); return; }

        $ids = array_filter(array_map('intval', explode(',', $row->conversionratio_ids)));
        if (empty($ids)) { echo json_encode([]); return; }

        $products = $this->db->select('cr.conversionratio_id, pi.product_name, um.unit_name as master_unit, us.unit_name as subunit_name, cr.conversion_ratio')
            ->from('conversion_ratio cr')
            ->join('product_information pi', 'pi.id = cr.product', 'left')
            ->join('units um', 'um.unit_id = pi.unit', 'left')
            ->join('units us', 'us.unit_id = cr.subunit', 'left')
            ->where_in('cr.conversionratio_id', $ids)
            ->get()->result();
        echo json_encode($products);
    }

    public function delete_bulk_conversionratio($id)
    {
        $row = $this->db->select('conversionratio_ids')->where('id', (int)$id)->get('bulk_conversionratio_details')->row();

        if ($row && !empty($row->conversionratio_ids)) {
            $ids = array_filter(array_map('intval', explode(',', $row->conversionratio_ids)));
            if (!empty($ids)) {
                $this->db->where_in('conversionratio_id', $ids)->delete('conversion_ratio');
            }
        }

        $this->db->where('id', (int)$id)->delete('bulk_conversionratio_details');
        echo json_encode('Success');
    }

    public function save_brand_from_csv()
    {
        $brand_name = trim($this->input->post('brand_name', FALSE));
        $status     = (int)$this->input->post('status', TRUE);
        if (empty($brand_name)) { echo json_encode(['status' => 'Error', 'message' => 'Brand name is required']); return; }
        $exists = $this->db->select('brand_id')->from('product_brand')
            ->where('LOWER(brand_name)', strtolower($brand_name))->get()->row();
        if ($exists) { echo json_encode(['status' => 'Error', 'message' => 'Brand already exists: ' . $brand_name]); return; }
        if ($this->product_model->create_brand(['brand_name' => $brand_name, 'status' => $status])) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed']);
        }
    }

    public function save_category_from_csv()
    {
        $category_name = trim($this->input->post('category_name', FALSE));
        $status        = (int)$this->input->post('status', TRUE);
        if (empty($category_name)) { echo json_encode(['status' => 'Error', 'message' => 'Category name is required']); return; }
        $exists = $this->db->select('category_id')->from('product_category')
            ->where('LOWER(category_name)', strtolower($category_name))->get()->row();
        if ($exists) { echo json_encode(['status' => 'Error', 'message' => 'Category already exists: ' . $category_name]); return; }
        if ($this->product_model->create_category(['category_name' => $category_name, 'status' => $status])) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed']);
        }
    }

    public function save_subcategory_from_csv()
    {
        $subcategory_name = trim($this->input->post('subcategory_name', FALSE));
        $category_id      = (int)$this->input->post('category_id', TRUE);
        $status           = (int)$this->input->post('status', TRUE);
        if (empty($subcategory_name) || empty($category_id)) {
            echo json_encode(['status' => 'Error', 'message' => 'Subcategory name and category are required']); return;
        }
        $exists = $this->db->select('subcategory_id')->from('product_subcategory')
            ->where('LOWER(subcategory_name)', strtolower($subcategory_name))
            ->where('category_id', $category_id)->get()->row();
        if ($exists) { echo json_encode(['status' => 'Error', 'message' => 'Subcategory already exists in this category']); return; }
        if ($this->product_model->create_subcategory(['subcategory_name' => $subcategory_name, 'category_id' => $category_id, 'status' => $status])) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed']);
        }
    }

    public function save_unit_from_csv()
    {
        $unit_name         = trim($this->input->post('unit_name', FALSE));
        $unit_display_name = trim($this->input->post('unit_display_name', FALSE));
        $status            = (int)$this->input->post('status', TRUE);
        if (empty($unit_name)) { echo json_encode(['status' => 'Error', 'message' => 'Unit name is required']); return; }
        $exists = $this->db->select('unit_id')->from('units')
            ->where('LOWER(unit_name)', strtolower($unit_name))->get()->row();
        if ($exists) { echo json_encode(['status' => 'Error', 'message' => 'Unit already exists: ' . $unit_name]); return; }
        $row = ['unit_name' => $unit_name, 'unit_display_name' => $unit_display_name ?: $unit_name, 'status' => $status];
        if ($this->product_model->create_unit($row)) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database insert failed']);
        }
    }

    /* ── Generic bulk-log helper ─────────────────────────────────── */
    private function _saveBulkLog($table, $idsField, $prefix, $ids)
    {
        $encryption_key = Config::$encryption_key;
        $lastupdate     = date('Y-m-d H:i:s');
        $ids_string     = is_array($ids) ? implode(',', $ids) : $ids;
        $this->db->query("INSERT INTO {$table} (uploaded_id, date, uploadedby, {$idsField})
            VALUES (NULL, '{$lastupdate}', '{$this->session->userdata('id')}', '{$ids_string}')");
        $inserted_id = $this->db->insert_id();
        $batch_label = $prefix . $inserted_id;
        $this->db->query("UPDATE {$table} SET uploaded_id = AES_ENCRYPT('{$batch_label}', '{$encryption_key}') WHERE id = {$inserted_id}");
        $csv_content  = $this->input->post('csv_content', FALSE);
        $csv_filename = $this->db->escape_str($this->input->post('csv_filename', FALSE) ?: 'upload.csv');
        if (!empty($csv_content)) {
            $csv_escaped = $this->db->escape_str($csv_content);
            $this->db->query("INSERT INTO bulk_upload_csv (batch_table, batch_id, original_csv, filename) VALUES ('{$table}', {$inserted_id}, '{$csv_escaped}', '{$csv_filename}')");
        }
        echo json_encode('Success');
    }

    public function download_bulk_csv($type = null, $id = null)
    {
        $id   = (int)$id;
        $type = preg_replace('/[^a-z]/', '', strtolower((string)$type));
        $row  = $this->db->select('original_csv, filename')
            ->where('batch_table', 'bulk_' . $type . '_details')
            ->where('batch_id', $id)
            ->get('bulk_upload_csv')->row();
        if (!$row || empty($row->original_csv)) { show_404(); return; }
        $filename = $row->filename ?: ('dupl_' . $type . '_' . $id . '.csv');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo $row->original_csv;
    }

    private function _getBulkDetails($id, $table, $idsField, $dataTable, $idCol, $labelCols)
    {
        $row = $this->db->select($idsField)->where('id', (int)$id)->get($table)->row();
        if (!$row || empty($row->$idsField)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->$idsField)));
        if (empty($ids)) { echo json_encode([]); return; }
        $records = $this->db->select($idCol . ', ' . implode(', ', $labelCols))->where_in($idCol, $ids)->get($dataTable)->result();
        echo json_encode($records);
    }

    private function _deleteBulkRecord($id, $table, $idsField, $dataTable, $idCol)
    {
        $id  = (int)$id;
        $row = $this->db->select($idsField)->where('id', $id)->get($table)->row();
        if ($row && !empty($row->$idsField)) {
            $ids = array_filter(array_map('intval', explode(',', $row->$idsField)));
            if (!empty($ids)) $this->db->where_in($idCol, $ids)->delete($dataTable);
        }
        $this->db->where('id', $id)->delete($table);
        echo json_encode('Success');
    }

    /* ── Brand bulk log ──────────────────────────────────────────── */
    public function save_brand_bulk_log()               { $this->_saveBulkLog('bulk_brand_details',        'brand_ids',        'BRAND-',  $this->input->post('brand_ids', TRUE)); }
    public function checkBulkBrandUpload()              { echo json_encode($this->product_model->BulkBrandUpload($this->input->post())); }
    public function get_bulk_brand_details($id = null)  { $this->_getBulkDetails($id, 'bulk_brand_details',        'brand_ids',        'product_brand',      'brand_id',      ['brand_name']); }
    public function delete_bulk_brand($id = null)       { $this->_deleteBulkRecord($id, 'bulk_brand_details',       'brand_ids',        'product_brand',      'brand_id'); }

    /* ── Category bulk log ───────────────────────────────────────── */
    public function save_category_bulk_log()               { $this->_saveBulkLog('bulk_category_details',    'category_ids',    'CAT-',    $this->input->post('category_ids', TRUE)); }
    public function checkBulkCategoryUpload()              { echo json_encode($this->product_model->BulkCategoryUpload($this->input->post())); }
    public function get_bulk_category_details($id = null)  { $this->_getBulkDetails($id, 'bulk_category_details',    'category_ids',    'product_category',   'category_id',   ['category_name']); }
    public function delete_bulk_category($id = null)       { $this->_deleteBulkRecord($id, 'bulk_category_details',   'category_ids',    'product_category',   'category_id'); }

    /* ── Subcategory bulk log ─────────────────────────────────────── */
    public function save_subcategory_bulk_log()               { $this->_saveBulkLog('bulk_subcategory_details', 'subcategory_ids', 'SUBCAT-', $this->input->post('subcategory_ids', TRUE)); }
    public function checkBulkSubcategoryUpload()              { echo json_encode($this->product_model->BulkSubcategoryUpload($this->input->post())); }
    public function get_bulk_subcategory_details($id = null)  {
        $row = $this->db->select('subcategory_ids')->where('id', (int)$id)->get('bulk_subcategory_details')->row();
        if (!$row || empty($row->subcategory_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->subcategory_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $records = $this->db->select('ps.subcategory_id, ps.subcategory_name, pc.category_name')
            ->from('product_subcategory ps')
            ->join('product_category pc', 'pc.category_id = ps.category_id', 'left')
            ->where_in('ps.subcategory_id', $ids)->get()->result();
        echo json_encode($records);
    }
    public function delete_bulk_subcategory($id = null)       { $this->_deleteBulkRecord($id, 'bulk_subcategory_details', 'subcategory_ids', 'product_subcategory', 'subcategory_id'); }

    /* ── Unit bulk log ───────────────────────────────────────────── */
    public function save_unit_bulk_log()               { $this->_saveBulkLog('bulk_unit_details',         'unit_ids',         'UNIT-',   $this->input->post('unit_ids', TRUE)); }
    public function checkBulkUnitUpload()              { echo json_encode($this->product_model->BulkUnitUpload($this->input->post())); }
    public function get_bulk_unit_details($id = null)  { $this->_getBulkDetails($id, 'bulk_unit_details',         'unit_ids',         'units',              'unit_id',       ['unit_name', 'unit_display_name']); }
    public function delete_bulk_unit($id = null)       { $this->_deleteBulkRecord($id, 'bulk_unit_details',        'unit_ids',         'units',              'unit_id'); }

    /* ── Payment Method bulk log ──────────────────────────────────── */
    public function save_paymentmethod_bulk_log()               { $this->_saveBulkLog('bulk_paymentmethod_details', 'paymentmethod_ids', 'PAY-', $this->input->post('paymentmethod_ids', TRUE)); }
    public function checkBulkPaymentMethodUpload()              { echo json_encode($this->product_model->BulkPaymentMethodUpload($this->input->post())); }
    public function get_bulk_paymentmethod_details($id = null)  { $this->_getBulkDetails($id, 'bulk_paymentmethod_details', 'paymentmethod_ids', 'payment_type',       'id',            ['name']); }
    public function delete_bulk_paymentmethod($id = null)       { $this->_deleteBulkRecord($id, 'bulk_paymentmethod_details', 'paymentmethod_ids', 'payment_type',       'id'); }

    /* ── Branch bulk log ─────────────────────────────────────────── */
    public function save_branch_bulk_log()               { $this->_saveBulkLog('bulk_branch_details', 'branch_ids', 'BRANCH-', $this->input->post('branch_ids', TRUE)); }
    public function checkBulkBranchUpload()              { echo json_encode($this->product_model->BulkBranchUpload($this->input->post())); }
    public function get_bulk_branch_details($id = null)  {
        $id  = (int)$id;
        $row = $this->db->select('branch_ids')->where('id', $id)->get('bulk_branch_details')->row();
        if (!$row || empty($row->branch_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->branch_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $enc = Config::$encryption_key;
        $records = $this->db->query("SELECT id, code, AES_DECRYPT(name,'$enc') AS name FROM branch WHERE id IN (".implode(',', $ids).")")->result();
        echo json_encode($records);
    }
    public function delete_bulk_branch($id = null)       { $this->_deleteBulkRecord($id, 'bulk_branch_details', 'branch_ids', 'branch', 'id'); }

    /* ── Store bulk log ──────────────────────────────────────────── */
    public function save_store_bulk_log()               { $this->_saveBulkLog('bulk_store_details', 'store_ids', 'STORE-', $this->input->post('store_ids', TRUE)); }
    public function checkBulkStoreUpload()              { echo json_encode($this->product_model->BulkStoreUpload($this->input->post())); }
    public function get_bulk_store_details($id = null)  { $this->_getBulkDetails($id, 'bulk_store_details', 'store_ids', 'store', 'id', ['code', 'name']); }
    public function delete_bulk_store($id = null)       { $this->_deleteBulkRecord($id, 'bulk_store_details', 'store_ids', 'store', 'id'); }

    /* ── Customer bulk log ───────────────────────────────────────── */
    public function save_customer_bulk_log()               { $this->_saveBulkLog('bulk_customer_details', 'customer_ids', 'CUST-', $this->input->post('customer_ids', TRUE)); }
    public function checkBulkCustomerUpload()              { echo json_encode($this->product_model->BulkCustomerUpload($this->input->post())); }
    public function get_bulk_customer_details($id = null)  {
        $id  = (int)$id;
        $row = $this->db->select('customer_ids')->where('id', $id)->get('bulk_customer_details')->row();
        if (!$row || empty($row->customer_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->customer_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $enc = Config::$encryption_key;
        $records = $this->db->query("SELECT customer_id, AES_DECRYPT(customer_name,'$enc') AS customer_name, AES_DECRYPT(customer_mobile,'$enc') AS mobile, AES_DECRYPT(customer_email,'$enc') AS email_address FROM customer_information WHERE customer_id IN (".implode(',', $ids).")")->result();
        echo json_encode($records);
    }
    public function delete_bulk_customer($id = null)       { $this->_deleteBulkRecord($id, 'bulk_customer_details', 'customer_ids', 'customer_information', 'customer_id'); }

    /* ── Supplier bulk log ───────────────────────────────────────── */
    public function save_supplier_bulk_log()               { $this->_saveBulkLog('bulk_supplier_details', 'supplier_ids', 'SUPP-', $this->input->post('supplier_ids', TRUE)); }
    public function checkBulkSupplierUpload()              { echo json_encode($this->product_model->BulkSupplierUpload($this->input->post())); }
    public function get_bulk_supplier_details($id = null)  {
        $id  = (int)$id;
        $row = $this->db->select('supplier_ids')->where('id', $id)->get('bulk_supplier_details')->row();
        if (!$row || empty($row->supplier_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->supplier_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $enc = Config::$encryption_key;
        $records = $this->db->query("SELECT supplier_id, AES_DECRYPT(supplier_name,'$enc') AS supplier_name, AES_DECRYPT(mobile,'$enc') AS mobile, AES_DECRYPT(emailnumber,'$enc') AS email_address FROM supplier_information WHERE supplier_id IN (".implode(',', $ids).")")->result();
        echo json_encode($records);
    }
    public function delete_bulk_supplier($id = null)       { $this->_deleteBulkRecord($id, 'bulk_supplier_details', 'supplier_ids', 'supplier_information', 'supplier_id'); }

    /* ── Service bulk log ────────────────────────────────────────── */
    public function save_service_bulk_log()               { $this->_saveBulkLog('bulk_service_details', 'service_ids', 'SVC-', $this->input->post('service_ids', TRUE)); }
    public function checkBulkServiceUpload()              { echo json_encode($this->product_model->BulkServiceUpload($this->input->post())); }
    public function get_bulk_service_details($id = null)  { $this->_getBulkDetails($id, 'bulk_service_details', 'service_ids', 'product_service', 'service_id', ['service_code', 'service_name', 'charge', 'service_vat']); }
    public function delete_bulk_service($id = null)       { $this->_deleteBulkRecord($id, 'bulk_service_details', 'service_ids', 'product_service', 'service_id'); }

    /* ── Product Group bulk log ──────────────────────────────────── */
    public function save_productgroup_from_csv()
    {
        $enc        = Config::$encryption_key;
        $groupcode  = trim($this->input->post('groupcode', FALSE));
        $name       = trim($this->input->post('name', FALSE));
        $status     = (int)$this->input->post('status', TRUE);
        $inv_group  = (int)$this->input->post('invoice_group', TRUE);
        $items_json = $this->input->post('items_json', TRUE);
        if (empty($groupcode) || empty($name)) { echo json_encode(['status'=>'Error','message'=>'Group code and name are required']); return; }
        $exists = $this->db->select('id')->from('product_group')->where('groupcode', $groupcode)->get()->row();
        if ($exists) { echo json_encode(['status'=>'Error','message'=>'Group code already exists: '.$groupcode]); return; }
        $this->db->insert('product_group', ['groupcode'=>$groupcode,'name'=>$name,'status'=>$status,'invoice_group'=>$inv_group]);
        $gid   = $this->db->insert_id();
        $items = json_decode($items_json, true);
        if (is_array($items)) {
            foreach ($items as $item) {
                $pid = (int)$item['product_id'];
                $uid = (int)$item['unit_id'];
                $qty = addslashes($item['qty']);
                $par = (int)$item['parent'];
                $this->db->query("INSERT INTO product_group_details (id,pid,product,qty,unit,parent) VALUES (0,$gid,$pid,AES_ENCRYPT('$qty','$enc'),$uid,$par)");
            }
        }
        echo json_encode(['status'=>'Success','id'=>$gid]);
    }
    public function save_productgroup_bulk_log()               { $this->_saveBulkLog('bulk_productgroup_details', 'productgroup_ids', 'PG-', $this->input->post('productgroup_ids', TRUE)); }
    public function checkBulkProductGroupUpload()              { echo json_encode($this->product_model->BulkProductGroupUpload($this->input->post())); }
    public function get_bulk_productgroup_details($id = null)  {
        $id  = (int)$id;
        $row = $this->db->select('productgroup_ids')->where('id', $id)->get('bulk_productgroup_details')->row();
        if (!$row || empty($row->productgroup_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->productgroup_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $records = $this->db->select('id, groupcode, name')->where_in('id', $ids)->get('product_group')->result();
        echo json_encode($records);
    }
    public function delete_bulk_productgroup($id = null)       { $this->_deleteBulkRecord($id, 'bulk_productgroup_details', 'productgroup_ids', 'product_group', 'id'); }

    /* ── Opening Stock bulk log ─────────────────────────────────── */
    public function save_openingstock_bulk_log()               { $this->_saveBulkLog('bulk_openingstock_details', 'openingstock_ids', 'OS-', $this->input->post('openingstock_ids', TRUE)); }
    public function checkBulkOpeningStockUpload()              { echo json_encode($this->product_model->BulkOpeningStockUpload($this->input->post())); }
    public function get_bulk_openingstock_details($id = null)  {
        $enc = Config::$encryption_key;
        $id  = (int)$id;
        $row = $this->db->select('openingstock_ids')->where('id', $id)->get('bulk_openingstock_details')->row();
        if (!$row || empty($row->openingstock_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->openingstock_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $id_list = implode(',', $ids);
        $records = $this->db->query("
            SELECT
                a.id          AS adj_id,
                a.date,
                a.reason,
                pi.product_name,
                s.name        AS store_name,
                CAST(AES_DECRYPT(sb.batchid, '$enc') AS CHAR) AS batch_name,
                u.unit_name,
                CAST(AES_DECRYPT(sd.stock, '$enc') AS DECIMAL(15,4)) AS qty
            FROM stock_details sd
            JOIN adj_stock           a  ON a.id        = sd.pid
            JOIN product_information pi ON pi.id       = sd.product
            JOIN store               s  ON s.id        = sd.store
            JOIN stockbatch          sb ON sb.id       = sd.batch
            JOIN units               u  ON u.unit_id   = sd.unit
            WHERE sd.pid IN ($id_list) AND sd.type = 'adj_stock'
            ORDER BY a.id, pi.product_name
        ")->result_array();
        echo json_encode($records);
    }
    public function delete_bulk_openingstock($id = null)       {
        $id  = (int)$id;
        $row = $this->db->select('openingstock_ids')->where('id', $id)->get('bulk_openingstock_details')->row();
        if ($row && !empty($row->openingstock_ids)) {
            $ids = array_filter(array_map('intval', explode(',', $row->openingstock_ids)));
            if (!empty($ids)) {
                $this->db->where_in('id', $ids)->delete('adj_stock');
                $this->db->where_in('pid', $ids)->where('type', 'adj_stock')->delete('stock_details');
                $this->db->where_in('pid', $ids)->where('type', 'adj_stock')->delete('phystock_details');
                $this->db->where_in('pid', $ids)->where('scenario', 'Inventory Transaction')->delete('audit_stock');
                $this->db->where_in('pid', $ids)->where('screen', 'stock')->delete('logs');
            }
        }
        $this->db->where('id', $id)->delete('bulk_openingstock_details');
    }

    /* ── Stock Adjustment bulk log ──────────────────────────────── */
    public function save_stockadjustment_bulk_log()
    {
        $this->_saveBulkLog('bulk_stockadjustment_details', 'stockadjustment_ids', 'SA-', $this->input->post('stockadjustment_ids', TRUE));
    }

    public function checkBulkStockAdjustmentUpload()
    {
        echo json_encode($this->product_model->BulkStockAdjustmentUpload($this->input->post()));
    }

    public function get_bulk_stockadjustment_details($id = null)
    {
        $enc = Config::$encryption_key;
        $id  = (int)$id;
        $row = $this->db->select('stockadjustment_ids')->where('id', $id)->get('bulk_stockadjustment_details')->row();
        if (!$row || empty($row->stockadjustment_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->stockadjustment_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $id_list = implode(',', $ids);
        $records = $this->db->query("
            SELECT
                a.id          AS adj_id,
                a.date,
                a.type,
                a.stocktype,
                a.reason,
                pi.product_name,
                s.name        AS store_name,
                CAST(AES_DECRYPT(sb.batchid, '$enc') AS CHAR) AS batch_name,
                u.unit_name,
                CAST(AES_DECRYPT(sd.stock, '$enc') AS CHAR)   AS qty
            FROM (
                SELECT product, batch, store, stock, pid, unit
                FROM stock_details WHERE pid IN ($id_list) AND type = 'adj_stock'
                UNION
                SELECT product, batch, store, stock, pid, unit
                FROM phystock_details WHERE pid IN ($id_list) AND type = 'adj_stock'
            ) sd
            JOIN adj_stock           a  ON a.id      = sd.pid
            JOIN product_information pi ON pi.id     = sd.product
            JOIN store               s  ON s.id      = sd.store
            JOIN stockbatch          sb ON sb.id     = sd.batch
            JOIN units               u  ON u.unit_id = sd.unit
            ORDER BY a.id, pi.product_name
        ")->result_array();
        echo json_encode($records);
    }

    public function delete_bulk_stockadjustment($id = null)
    {
        $id  = (int)$id;
        $row = $this->db->select('stockadjustment_ids')->where('id', $id)->get('bulk_stockadjustment_details')->row();
        if ($row && !empty($row->stockadjustment_ids)) {
            $ids = array_filter(array_map('intval', explode(',', $row->stockadjustment_ids)));
            if (!empty($ids)) {
                $this->db->where_in('id', $ids)->delete('adj_stock');
                $this->db->where_in('pid', $ids)->where('type', 'adj_stock')->delete('stock_details');
                $this->db->where_in('pid', $ids)->where('type', 'adj_stock')->delete('phystock_details');
                $this->db->where_in('pid', $ids)->where('scenario', 'Inventory Transaction')->delete('audit_stock');
                $this->db->where_in('pid', $ids)->where('screen', 'stock')->delete('logs');
            }
        }
        $this->db->where('id', $id)->delete('bulk_stockadjustment_details');
        echo json_encode('Success');
    }

    /* ── Stock Batch CSV upload ──────────────────────────────────── */
    public function save_stockbatch_from_csv()
    {
        $enc          = Config::$encryption_key;
        $batchid      = $this->db->escape_str($this->input->post('batchid',  FALSE));
        $busage       = $this->input->post('busage',       TRUE);
        $product      = (int)$this->input->post('product', TRUE);
        $edate_enabled= (int)$this->input->post('edate_enabled', TRUE);
        $mdate        = $this->input->post('mdate',        TRUE) ?: '';
        $pdate        = $this->input->post('pdate',        TRUE) ?: '';
        $edate        = $edate_enabled ? ($this->input->post('edate', TRUE) ?: '') : '';
        $mrp          = (float)$this->input->post('mrp',   TRUE);
        $details      = $this->db->escape_str($this->input->post('details',  FALSE) ?: '');
        $status       = (int)$this->input->post('status',  TRUE);

        if (empty($batchid) || empty($busage)) {
            echo json_encode(['status' => 'Error', 'message' => 'Required fields missing']);
            return;
        }

        /* duplicate check — batch ID must be unique per product */
        $check_product = ($busage === 'single') ? $product : 0;
        $exists = $this->db->query(
            "SELECT id FROM stockbatch WHERE batchid = AES_ENCRYPT('$batchid', '$enc') AND product = '$check_product' LIMIT 1"
        )->num_rows();
        if ($exists) {
            echo json_encode(['status' => 'Error', 'message' => 'Batch ID already exists for this product: "'.$batchid.'"']);
            return;
        }

        if ($busage !== 'single') {
            $product = 0; $mdate = ''; $pdate = ''; $edate = ''; $mrp = 0; $edate_enabled = 0;
        }

        $ok = $this->db->query(
            "INSERT INTO stockbatch (batchid, details, status, opening, busage, product, mdate, pdate, edate, mrp, edate_enabled)
             VALUES (
                AES_ENCRYPT('$batchid', '$enc'),
                '$details', '$status', 0, '$busage', '$product',
                '$mdate', '$pdate', '$edate',
                AES_ENCRYPT('$mrp', '$enc'),
                '$edate_enabled'
             )"
        );
        if ($ok) {
            echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'Database error']);
        }
    }

    public function save_stockbatch_bulk_log() { $this->_saveBulkLog('bulk_stockbatch_details', 'stockbatch_ids', 'SB-', $this->input->post('stockbatch_ids', TRUE)); }
    public function checkBulkStockBatchUpload() { echo json_encode($this->product_model->BulkStockBatchUpload($this->input->post())); }

    public function get_bulk_stockbatch_details($id = null)
    {
        $enc = Config::$encryption_key;
        $id  = (int)$id;
        $row = $this->db->select('stockbatch_ids')->where('id', $id)->get('bulk_stockbatch_details')->row();
        if (!$row || empty($row->stockbatch_ids)) { echo json_encode([]); return; }
        $ids = array_filter(array_map('intval', explode(',', $row->stockbatch_ids)));
        if (empty($ids)) { echo json_encode([]); return; }
        $id_list = implode(',', $ids);
        $records = $this->db->query("
            SELECT
                CAST(AES_DECRYPT(sb.batchid, '$enc') AS CHAR)  AS batchid,
                sb.busage,
                COALESCE(pi.product_name, '—')                  AS product_name,
                sb.mdate,
                sb.pdate,
                sb.edate,
                CAST(AES_DECRYPT(sb.mrp, '$enc') AS CHAR)       AS mrp,
                sb.details,
                IF(sb.status=1,'Active','Inactive')              AS status_label
            FROM stockbatch sb
            LEFT JOIN product_information pi ON pi.id = sb.product
            WHERE sb.id IN ($id_list)
            ORDER BY sb.id ASC
        ")->result_array();
        echo json_encode($records);
    }

    public function delete_bulk_stockbatch($id = null)
    {
        /* only removes the log record — batch entries stay in the system */
        $this->db->where('id', (int)$id)->delete('bulk_stockbatch_details');
    }

    /* ── Designation CSV upload ─────────────────────────────────── */
    public function save_designation_from_csv()
    {
        $designation = $this->input->post('designation', FALSE);
        $details     = $this->input->post('details',     FALSE);
        $status      = (int)$this->input->post('status', TRUE);

        if (!$designation) {
            echo json_encode(['status' => 'Error', 'message' => 'Designation is required']);
            return;
        }
        $exists = $this->db->where('LOWER(designation)', strtolower($designation))->count_all_results('designation');
        if ($exists) {
            echo json_encode(['status' => 'Error', 'message' => 'Designation already exists: ' . $designation]);
            return;
        }
        $this->db->insert('designation', [
            'designation' => $designation,
            'details'     => $details,
            'status'      => $status,
        ]);
        echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
    }

    public function save_designation_bulk_log()
    {
        $this->_saveBulkLog('bulk_designation_details', 'designation_ids', 'DG-', $this->input->post('designation_ids', TRUE));
    }

    public function checkBulkDesignationUpload() { echo json_encode($this->product_model->BulkDesignationUpload($this->input->post())); }

    public function get_bulk_designation_details($id = null)
    {
        $log = $this->db->where('id', (int)$id)->get('bulk_designation_details')->row_array();
        if (!$log) { echo json_encode([]); return; }
        $ids = array_filter(array_map('trim', explode(',', $log['designation_ids'] ?? '')));
        if (!$ids) { echo json_encode([]); return; }
        $rows = $this->db->select('id, designation, details, status')
            ->from('designation')
            ->where_in('id', $ids)
            ->get()->result_array();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'designation'  => $r['designation'],
                'details'      => $r['details'],
                'status_label' => $r['status'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>',
            ];
        }
        echo json_encode($out);
    }

    public function delete_bulk_designation($id = null)
    {
        $this->db->where('id', (int)$id)->delete('bulk_designation_details');
    }

    /* ── Employee CSV upload ────────────────────────────────────── */
    public function save_employee_from_csv()
    {
        $last_name  = $this->input->post('last_name',  FALSE);
        $first_name = $this->input->post('first_name', FALSE);
        $status     = (int)$this->input->post('status', TRUE);

        if (!$last_name) {
            echo json_encode(['status' => 'Error', 'message' => 'Employee ID is required']);
            return;
        }
        $exists = $this->db->where('LOWER(last_name)', strtolower($last_name))->count_all_results('employee_history');
        if ($exists) {
            echo json_encode(['status' => 'Error', 'message' => 'Employee ID already exists: ' . $last_name]);
            return;
        }
        $this->db->insert('employee_history', [
            'last_name'      => $last_name,
            'first_name'     => $first_name,
            'designation'    => (int)$this->input->post('designation',    TRUE),
            'rate_type'      => (int)$this->input->post('rate_type',      TRUE),
            'hrate'          => (float)$this->input->post('hrate',        TRUE),
            'blood_group'    => $this->input->post('blood_group',         TRUE),
            'phone'          => $this->input->post('phone',               TRUE),
            'email'          => $this->input->post('email',               TRUE),
            'address_line_1' => $this->input->post('address_line_1',      TRUE),
            'address_line_2' => $this->input->post('address_line_2',      TRUE),
            'state'          => $this->input->post('state',               TRUE),
            'country'        => $this->input->post('country',             TRUE),
            'city'           => $this->input->post('city',                TRUE),
            'zip'            => $this->input->post('zip',                 TRUE),
            'status'         => $status,
        ]);
        echo json_encode(['status' => 'Success', 'id' => $this->db->insert_id()]);
    }

    public function save_employee_bulk_log()
    {
        $this->_saveBulkLog('bulk_employee_details', 'employee_ids', 'EMP-', $this->input->post('employee_ids', TRUE));
    }

    public function checkBulkEmployeeUpload() { echo json_encode($this->product_model->BulkEmployeeUpload($this->input->post())); }

    public function get_bulk_employee_details($id = null)
    {
        $log = $this->db->where('id', (int)$id)->get('bulk_employee_details')->row_array();
        if (!$log) { echo json_encode([]); return; }
        $ids = array_filter(array_map('trim', explode(',', $log['employee_ids'] ?? '')));
        if (!$ids) { echo json_encode([]); return; }
        $rows = $this->db->select('eh.id, eh.last_name, eh.first_name, d.designation AS desig_name, eh.rate_type, eh.hrate, eh.phone, eh.email, eh.status')
            ->from('employee_history eh')
            ->join('designation d', 'd.id = eh.designation', 'left')
            ->where_in('eh.id', $ids)
            ->get()->result_array();
        $rateLabels = [1 => 'Hourly', 2 => 'Salary'];
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'emp_id'       => $r['last_name'],
                'emp_name'     => $r['first_name'],
                'designation'  => $r['desig_name'],
                'pay_type'     => $rateLabels[$r['rate_type']] ?? '—',
                'hrate'        => $r['hrate'],
                'phone'        => $r['phone'],
                'email'        => $r['email'],
                'status_label' => $r['status'] ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>',
            ];
        }
        echo json_encode($out);
    }

    public function delete_bulk_employee($id = null)
    {
        $this->db->where('id', (int)$id)->delete('bulk_employee_details');
    }

    function uploadCsv()
    {
        $filename = $_FILES['upload_csv_file']['name'];
        $basenameAndExtension = explode('.', $filename);
        $ext = end($basenameAndExtension);
        if ($ext == 'csv') {
            $count = 0;
            $fp = fopen($_FILES['upload_csv_file']['tmp_name'], 'r') or die("can't open file");

            if (($handle = fopen($_FILES['upload_csv_file']['tmp_name'], 'r')) !== FALSE) {

                while ($csv_line = fgetcsv($fp, 1024)) {
                    //keep this if condition if you want to remove the first row
                    for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                        $product_id = $this->generator(10);
                        $insert_csv = array();
                        $insert_csv['supplier_id'] = (!empty($csv_line[1]) ? $csv_line[1] : null);
                        $insert_csv['product_name'] = (!empty($csv_line[2]) ? $csv_line[2] : null);
                        $insert_csv['product_model'] = (!empty($csv_line[3]) ? $csv_line[3] : null);
                        $insert_csv['category_id'] = (!empty($csv_line[4]) ? $csv_line[4] : null);
                        $insert_csv['price'] = (!empty($csv_line[5]) ? $csv_line[5] : null);
                        $insert_csv['supplier_price'] = (!empty($csv_line[6]) ? $csv_line[6] : null);
                        $insert_csv['opening_stock'] = (!empty($csv_line[7]) ? $csv_line[7] : null);
                        $insert_csv['opening_batch'] = (!empty($csv_line[8]) ? $csv_line[8] : null);
                    }
                    // $check_supplier = $this -> db -> select('*') -> from('supplier_information') -> where('supplier_name', $insert_csv['supplier_id']) -> get() -> row();
                    // if (!empty($check_supplier)) {
                    //     $supplier_id = $check_supplier -> supplier_id;
                    // } else {
                    //     $supplierinfo = array(
                    //         'supplier_name' => $insert_csv['supplier_id'],
                    //         'address'           => '',
                    //         'mobile'            => '',
                    //         'details'           => '',
                    //         'status'            => 1
                    //     );
                    //     if ($count > 0) {
                    //         $this -> db -> insert('supplier_information', $supplierinfo);
                    //     }
                    //     $supplier_id = $this -> db -> insert_id();
                    //     $coa = $this -> supplier_model -> headcode();
                    //     if ($coa -> HeadCode != NULL) {
                    //         $headcode = $coa -> HeadCode + 1;
                    //     }
                    //     else {
                    //         $headcode = "21110000001";
                    //     }
                    //     $c_acc = $supplier_id.'-'.$insert_csv['supplier_id'];
                    //     $createby = $this -> session -> userdata('id');
                    //     $createdate = date('Y-m-d H:i:s');


                    //     $supplier_coa = [
                    //         'HeadCode'         => $headcode,
                    //         'HeadName'         => $c_acc,
                    //         'PHeadName'        => 'Suppliers',
                    //         'HeadLevel'        => '3',
                    //         'IsActive'         => '1',
                    //         'IsTransaction'    => '1',
                    //         'IsGL'             => '0',
                    //         'HeadType'         => 'L',
                    //         'IsBudget'         => '0',
                    //         'IsDepreciation'   => '0',
                    //         'supplier_id'      => $supplier_id,
                    //         'DepreciationRate' => '0',
                    //         'CreateBy'         => $createby,
                    //         'CreateDate'       => $createdate,
                    //     ];

                    //     if ($count > 0) {
                    //         $this -> db -> insert('acc_coa', $supplier_coa);
                    //     }
                    // }

                    $check_category = $this->db->select('*')->from('product_category')->where('category_name', $insert_csv['category_id'])->get()->row();
                    if (!empty($check_category)) {
                        $category_id = $check_category->category_id;
                    } else {
                        $categorydata = array(
                            'category_name'         => $insert_csv['category_id'],
                            'status'                => 1
                        );
                        if ($count > 0) {
                            $this->db->insert('product_category', $categorydata);
                            $category_id = $this->db->insert_id();
                        }
                    }
                    $data = array(
                        'product_id'    => $product_id,
                        'category_id'   => $category_id,
                        'product_name'  => $insert_csv['product_name'],
                        'product_model' => $insert_csv['product_model'],
                        'price'         => $insert_csv['price'],
                        'unit'          => '',
                        'tax'           => '',
                        'product_details' => 'Csv Product',
                        'image'         => 'my-assets/image/product.png',
                        'status'        => 1
                    );

                    if ($count > 0) {

                        $result = $this->db->select('*')
                            ->from('product_information')
                            ->where('product_name', $data['product_name'])
                            ->where('product_model', $data['product_model'])
                            ->where('category_id', $category_id)
                            ->get()
                            ->row();
                        if (empty($result)) {
                            $this->db->insert('product_information', $data);
                            $product_id = $product_id;
                        } else {
                            $product_id = $result->product_id;
                            $udata = array(
                                'product_id'     => $result->product_id,
                                'category_id'    => $category_id,
                                'product_name'   => $result->product_name,
                                'product_model'  => $insert_csv['product_model'],
                                'price'          => $insert_csv['price'],
                                'unit'           => '',
                                'tax'            => '',
                                'product_details' => 'Csv Uploaded Product',
                                'image'         => 'my-assets/image/product.png',
                                'status'        => 1
                            );
                            $this->db->where('product_id', $result->product_id);
                            $this->db->update('product_information', $udata);
                        }

                        $supp_prd = array(
                            'product_id'     => $product_id,
                            'supplier_id'    => 1,
                            'supplier_price' => $insert_csv['supplier_price'],
                            'products_model' => $insert_csv['product_model'],
                        );

                        $splprd = $this->db->select('*')
                            ->from('supplier_product')
                            ->where('supplier_id', 1)
                            ->where('product_id', $product_id)
                            ->get()
                            ->num_rows();

                        if ($splprd == 0) {
                            $this->db->insert('supplier_product', $supp_prd);
                        } else {
                            $supp_prd = array(
                                'supplier_id'    => 1,
                                'supplier_price' => $insert_csv['supplier_price'],
                                'products_model' => $insert_csv['product_model']
                            );
                            $this->db->where('product_id', $product_id);
                            $this->db->where('supplier_id', 1);
                            $this->db->update('supplier_product', $supp_prd);
                        }



                        $data1 = array(
                            'product_id'         => $product_id,
                            'quantity'           => $insert_csv['opening_stock'],
                            'batch_id'           =>  $insert_csv['opening_batch'],
                            'status'             => 1
                        );
                        $this->db->insert('product_purchase_details', $data1);
                    }
                    $count++;
                }
            }

            $this->session->set_flashdata(array('message' => display('successfully_added')));
            redirect(base_url('product_list'));
        } else {
            $this->session->set_flashdata(array('error_message' => 'Please Import Only Csv File'));
            redirect(base_url('bulk_products'));
        }
    }




    public function qrgenerator($product_id)
    {
        $config['cacheable'] = true; //boolean, the default is true
        $config['cachedir'] = ''; //string, the default is application/cache/
        $config['errorlog'] = ''; //string, the default is application/logs/
        $config['quality'] = true; //boolean, the default is true
        $config['size'] = '1024'; //interger, the default is 1024
        $config['black'] = array(224, 255, 255); // array, default is 
        $config['white'] = array(70, 130, 180); // array, default is array(0,0,0)
        $this->ciqrcode->initialize($config);
        $params['data'] = $product_id;
        $params['level'] = 'H';
        $params['size'] = 10;
        $image_name = $product_id . '.png';
        $params['savename'] = FCPATH . 'my-assets/image/qr/' . $image_name;
        $this->ciqrcode->generate($params);
        $product_info = $this->product_model->bdtask_barcode_productdata($product_id);
        $data = array(
            'title'           => display('qr_code'),
            'product_name'    => $product_info[0]['product_name'],
            'product_model'   => $product_info[0]['product_model'],
            'price'           => $product_info[0]['price'],
            'product_details' => $product_info[0]['product_details'],
            'qr_image'        => $image_name,
        );
        $data['module']        = "product";
        $data['page']          = "barcode_print_page";
        echo modules::run('template/layout', $data);
    }


    // bar code part
    // public function barcode_print($product_id)
    // {
    //     $product_info = $this->product_model->bdtask_barcode_productdata($product_id);

    //     $data = array(
    //         'title'           => display('barcode'),
    //         'product_id'      => $product_id,
    //         'product_name'    => $product_info[0]['product_name'],
    //         'product_model'   => $product_info[0]['product_model'],
    //         'price'           => $product_info[0]['price'],
    //         'product_details' => $product_info[0]['product_details'],

    //     );

    //     $data['module']        = "product";
    //     $data['page']          = "barcode_print_page";
    //     echo modules::run('template/layout', $data);
    // }


    public function bdtask_product_details($product_id = null)
    {
        $details_info = $this->product_model->bdtask_barcode_productdata($product_id);
        $purchaseData = $this->product_model->product_purchase_info($product_id);
        $totalPurchase = 0;
        $totalPrcsAmnt = 0;

        if (!empty($purchaseData)) {
            foreach ($purchaseData as $k => $v) {
                $purchaseData[$k]['final_date'] = $purchaseData[$k]['date'];
                $totalPrcsAmnt = ($totalPrcsAmnt + $purchaseData[$k]['total_amount']);
                $totalPurchase = ($totalPurchase + $purchaseData[$k]['quantity']);
            }
        }

        $salesData = $this->product_model->invoice_data($product_id);

        $totalSales = 0;
        $totaSalesAmt = 0;
        if (!empty($salesData)) {
            foreach ($salesData as $k => $v) {
                $salesData[$k]['final_date'] = $salesData[$k]['date'];
                $totalSales = ($totalSales + $salesData[$k]['quantity']);
                $totaSalesAmt = ($totaSalesAmt + $salesData[$k]['total_amount']);
            }
        }

        $stock = ($totalPurchase - $totalSales);
        $data = array(
            'title'               => display('product_details'),
            'product_name'        => $details_info[0]['product_name'],
            'product_model'       => $details_info[0]['product_model'],
            'price'               => $details_info[0]['price'],
            'purchaseTotalAmount' => number_format($totalPrcsAmnt, 2, '.', ','),
            'salesTotalAmount'    => number_format($totaSalesAmt, 2, '.', ','),
            'img'                 => $details_info[0]['image'],
            'total_purchase'      => $totalPurchase,
            'total_sales'         => $totalSales,
            'purchaseData'        => $purchaseData,
            'salesData'           => $salesData,
            'stock'               => $stock,
            'subunit_info'        => $this->product_model->get_subunit_with_ratio($product_id),
        );

        $data['module']        = "product";
        $data['page']          = "product_details";
        echo modules::run('template/layout', $data);
    }


    public function generator($lenth)
    {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 9);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }














    // Brand part
    function bdtask_brand_list()
    {
        $data['title']      = "Brand List";
        $data['module']     = "product";
        $data['page']       = "brand_list";
        $data["brand_list"] = $this->product_model->brand_list();
        if (!$this->permission1->method('brand_list', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_brand_form($id = null)
    {
        $data['title'] = display('add_brand');
        #-------------------------------#
        $this->form_validation->set_rules('brand_name', "Brand Name", 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['brand'] = (object)$postData = [
            'brand_id'      => $id,
            'brand_name'    => $this->input->post('brand_name', true),
            'status'           => $this->input->post('status', true),
        ];

        if (!$id) {
            if (!$this->permission1->method('brand_list', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('brand_list', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_brand($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Brand Details Saved successfully");
                        window.location.href = "' . $base_url . 'brand_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Brand Details Saved successfully");
                        window.location.href = "' . $base_url . 'brand_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'brand_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'brand_list";
                       </script>';
                    }
                }
            } else {
                if ($this->product_model->update_brand($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Brand Details Updated successfully");
                        window.location.href = "' . $base_url . 'brand_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Brand Details Updated successfully");
                        window.location.href = "' . $base_url . 'brand_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'brand_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'brand_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = "Edit Brand";
                $data['brand'] = $this->product_model->single_brand_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "brand_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function check_duplicate_brand()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('brand_id')->from('product_brand')->where('brand_name', $value);
        if (!empty($exclude_id)) { $this->db->where('brand_id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A brand with this name already exists.' : '']);
    }

    public function bdtask_deletebrand($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_brand($id)) {
             echo '<script type="text/javascript">
             alert("Brand Details Deleted successfully");
             window.location.href = "' . $base_url . 'brand_list";
            </script>';
        } else {
             echo '<script type="text/javascript">
             alert("Cannot delete this brand because products are linked to it or something went wrong");
             window.location.href = "' . $base_url . 'brand_list";
       </script>';
        }
    }









    // OOP part
    function bdtask_oop_list()
    {
        $data['title']      = "Origin Of Product List";
        $data['module']     = "product";
        $data['page']       = "oop_list";
        $data["oop_list"] = $this->product_model->oop_list();
        if (!$this->permission1->method('oop_list', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_oop_form($id = null)
    {
        $data['title'] = display('add_oop');
        #-------------------------------#
        $this->form_validation->set_rules('oop_name', "Origin Of Product", 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['oop'] = (object)$postData = [
            'oop_id'      => $id,
            'oop_name'    => $this->input->post('oop_name', true),
            'status'           => $this->input->post('status', true),
        ];

        if (!$this->permission1->method('oop_list', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_oop($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Origin Of Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'oop_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Origin Of Product Details Saved successfully");
                        window.location.href = "' . $base_url . 'oop_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'oop_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'oop_list";
                       </script>';
                    }
                }
            } else {
                if ($this->product_model->update_oop($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Origin Of Product Details Updated successfully");
                        window.location.href = "' . $base_url . 'oop_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Origin Of Product Details Updated successfully");
                        window.location.href = "' . $base_url . 'oop_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'oop_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'oop_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = "Edit Origin Of Product";
                $data['oop'] = $this->product_model->single_oop_data($id);
            }
            $data['module']   = "product";
            $data['page']     = "oop_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_deleteoop($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_oop($id)) {
            echo '<script type="text/javascript">
            alert("Origin Of Product Details Deleted successfully");
            window.location.href = "' . $base_url . 'oop_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Origin Of Product because products are linked to it or something went wrong");
            window.location.href = "' . $base_url . 'oop_list";
           </script>';
        }
    }






    // Sub Stock part
    function bdtask_subcategory_list()
    {
        $data['title']      = "Subcategory List";
        $data['module']     = "product";
        $data['page']       = "subcategory_list";
        $data["subcategory_list"] = $this->product_model->subcategory_list();
        if (!$this->permission1->method('subcategory_list', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_subcategory_form($id = null)
    {
        $data['title'] = display('add_subcategory');
        #-------------------------------#
        $this->form_validation->set_rules('subcategory_name', "Subcategory", 'required|max_length[200]');
        $this->form_validation->set_rules('status', display('status'), 'max_length[2]');
        #-------------------------------#
        $data['oop'] = (object)$postData = [
            'subcategory_id'      => $id,
            'subcategory_name'    => $this->input->post('subcategory_name', true),
            'category_id'    => $this->input->post('category_id', true),
            'status'           => $this->input->post('status', true),
        ];

        if (!$id) {
            if (!$this->permission1->method('subcategory_list', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('subcategory_list', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {

            #if empty $id then insert data
            if (empty($id)) {
                if ($this->product_model->create_subcategory($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Subcategory Details Saved successfully");
                        window.location.href = "' . $base_url . 'subcategory_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Subcategory Details Saved successfully");
                        window.location.href = "' . $base_url . 'subcategory_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'subcategory_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'subcategory_list";
                       </script>';
                    }
                }
            } else {
                if ($this->product_model->update_subcategory($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Subcategory Details Updated successfully");
                        window.location.href = "' . $base_url . 'subcategory_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Subcategory Details Updated successfully");
                        window.location.href = "' . $base_url . 'subcategory_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'subcategory_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'subcategory_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = "Edit Subcategory";
                $data['subcategory'] = $this->product_model->single_subcategory_data($id);
            }
            $data['category_list'] = $this->product_model->active_category();
            $data['module']   = "product";
            $data['page']     = "subcategory_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function check_duplicate_subcategory()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('subcategory_id')->from('product_subcategory')->where('subcategory_name', $value);
        if (!empty($exclude_id)) { $this->db->where('subcategory_id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A subcategory with this name already exists.' : '']);
    }

    public function bdtask_deletesubcategory($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_subcategory($id)) {
            echo '<script type="text/javascript">
            alert("Subcatgory Details Deleted successfully");
            window.location.href = "' . $base_url . 'subcategory_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Subcatgory because products are linked to it or something went wrong");
            window.location.href = "' . $base_url . 'subcategory_list";
           </script>';
        }
    }


    // conversion ratio part

    function bdtask_conversionratio_list()
    {
        $data['title']      = display('conversionratio_list');
        $data['module']     = "product";
        $data['page']       = "conversionratio_list";
        $data["conversionration_list"] = $this->product_model->conversionration_list();
        if (!$this->permission1->method('conversionratio_list', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }


    public function bdtask_conversionratio_form($id = null)
    {
        $data['title'] = display('conversionratio_form');
        
        // Check if editing and if transactions exist
        $data['has_transactions'] = false;
        if (!empty($id)) {
            $data['has_transactions'] = $this->product_model->check_conversionratio_transactions($id);
        }
        
        #-------------------------------#
        // $this->form_validation->set_rules('date', "Date", 'required|max_length[200]');
        $this->form_validation->set_rules('product_id', "Product", 'required');
        $this->form_validation->set_rules('subunit', "Subunit", 'required');
        $this->form_validation->set_rules('conversion_ratio', "Conversion Ratio", 'required');
       // $this->form_validation->set_rules('convertiontype', "Conversion Type", 'required');
        #-------------------------------#
        $data['conversion_ratio'] = (object)$postData = [
            'conversionratio_id'      => $id,
            // 'date'       => $this->input->post('date', true),
            'product'    => $this->input->post('product_id', true),
            'convertiontype'    => '*',
            'subunit'    => $this->input->post('subunit', true),
            'conversion_ratio'    => $this->input->post('conversion_ratio', true),
            'status'     => 1,
        ];

        if (!$this->permission1->method('conversionratio_list', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }


        $base_url = base_url();

        #-------------------------------#
        if ($this->form_validation->run() === true) {
            
            // Prevent editing if transactions exist
            if (!empty($id) && $data['has_transactions']) {
                echo '<script type="text/javascript">
                alert("Cannot update this Conversion Ratio because it has been used in transactions (purchases, sales, stock adjustments, etc.). Please create a new conversion ratio instead.");
                window.location.href = "' . $base_url . 'conversionratio_list";
               </script>';
                return;
            }

            #if empty $id then insert data
            if (empty($id)) {
                // Block duplicate: same product + subunit combination must be unique
                if ($this->product_model->check_duplicate_conversionratio($postData['product'], $postData['subunit'])) {
                    echo '<script type="text/javascript">
                    alert("A conversion ratio for this subunit already exists for the selected product. Each subunit can only have one conversion ratio per product.");
                    window.location.href = "' . $base_url . 'conversionratio_form";
                   </script>';
                    return;
                }

                if ($this->product_model->create_conversionratio($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Conversion Ratio Details Saved successfully");
                        window.location.href = "' . $base_url . 'conversionratio_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Conversion Ratio Details Saved successfully");
                        window.location.href = "' . $base_url . 'conversionratio_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'conversionratio_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'conversionratio_list";
                       </script>';
                    }
                }
            } else {
                // Block duplicate: check if another active conversion ratio exists for same product + subunit
                if ($this->product_model->check_duplicate_conversionratio($postData['product'], $postData['subunit'], $id)) {
                    echo '<script type="text/javascript">
                    alert("A conversion ratio for this subunit already exists for the selected product. Each subunit can only have one conversion ratio per product.");
                    window.location.href = "' . $base_url . 'conversionratio_list";
                   </script>';
                    return;
                }

                if ($this->product_model->update_conversionratio($postData)) {
                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("Conversion Ratio Details Updated successfully");
                        window.location.href = "' . $base_url . 'conversionratio_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("Conversion Ratio Details Updated successfully");
                        window.location.href = "' . $base_url . 'conversionratio_list";
                       </script>';
                    }
                } else {
                    $message = display('please_try_again');

                    if (isset($_POST['add-another'])) {
                        echo '
                        <script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'conversionratio_form";
                       </script>';
                        exit;
                    } else {

                        echo '<script type="text/javascript">
                        alert("' . $message . '");
                        window.location.href = "' . $base_url . 'conversionratio_list";
                       </script>';
                    }
                }
            }
        } else {
            if (!empty($id)) {
                $data['title']    = "Edit Conversion Ratio";
                $data['conversionratio'] = $this->product_model->single_conversionratio_data($id);
            }
            $data['products'] = $this->product_model->active_product();

            $data['module']   = "product";
            $data['page']     = "conversionratio_form";
            echo Modules::run('template/layout', $data);
        }
    }



    public function bdtask_deleteconversionratio($id = null)
    {
        $base_url = base_url();

        if ($this->product_model->delete_conversionratio($id)) {
            echo '<script type="text/javascript">
            alert("Conversion Ratio Details Deleted successfully");
            window.location.href = "' . $base_url . 'conversionratio_list";
           </script>';
        } else {
            echo '<script type="text/javascript">
            alert("Cannot delete this Conversion Ratio because it has been used in transactions (purchases, sales, stock adjustments, etc.). Please deactivate it instead or create a new one.");
            window.location.href = "' . $base_url . 'conversionratio_list";
           </script>';
        }
    }

    public function save_product()
    {
        $encryption_key = Config::$encryption_key;
        $log            = [];

        // ── Read POST input ───────────────────────────────────────────
        $entries = $this->input->post('entries', TRUE);

        $product_id          = trim((string)$this->input->post('product_id', TRUE));
        $generate_product_id = ($product_id === '');
        if ($generate_product_id) { $product_id = '0'; }

        $supplier_id         = $this->input->post('supplier_id', TRUE);
        $supplier_id         = ($supplier_id === '' || $supplier_id === null) ? 0 : $supplier_id;

        $vat                 = $this->input->post('vat', TRUE);
        $vat                 = ($vat === '' || $vat === null) ? 0 : $vat;

        $defaultsaleprice    = $this->input->post('defaultsaleprice', TRUE);
        $defaultsaleprice    = ($defaultsaleprice === '' || $defaultsaleprice === null) ? 'fixedprice' : $defaultsaleprice;

        $batchtype           = $this->input->post('batchtype', TRUE);
        $batchtype           = ($batchtype === '' || $batchtype === null) ? 1 : $batchtype;

        $ad                  = $this->input->post('ad', TRUE);
        $ad                  = ($ad === null) ? '' : $ad;

        $bd                  = $this->input->post('bd', TRUE);
        $bd                  = ($bd === null) ? '' : $bd;

        $stock               = $this->input->post('stock', TRUE);
        $stock               = ($stock === '' || $stock === null) ? 0 : $stock;

        $max_stock_level     = $this->input->post('max_stock_level', TRUE);
        $max_stock_level     = is_numeric($max_stock_level) ? $max_stock_level : 0;

        $min_stock_level     = $this->input->post('min_stock_level', TRUE);
        $min_stock_level     = is_numeric($min_stock_level) ? $min_stock_level : 0;

        $reorder_stock_level = $this->input->post('reorder_stock_level', TRUE);
        $reorder_stock_level = is_numeric($reorder_stock_level) ? $reorder_stock_level : 0;

        $reserve_stock_level = $this->input->post('reserve_stock_level', TRUE);
        $reserve_stock_level = is_numeric($reserve_stock_level) ? $reserve_stock_level : 0;

        // Read text fields without XSS filter — TRUE triggers rawurldecode which corrupts % in names
        $product_name   = $this->db->escape_str($this->input->post('product_name',  FALSE));
        $printname      = $this->db->escape_str($this->input->post('printname',     FALSE));
        $serial_no      = $this->db->escape_str($this->input->post('serial_no',     FALSE));
        $description    = $this->db->escape_str($this->input->post('description',   FALSE));
        $product_model  = $this->db->escape_str($this->input->post('product_model', FALSE));
        $product_type   = $this->db->escape_str($this->input->post('product_type',  FALSE));
        $category_id    = $this->input->post('category_id',    TRUE);
        $unit_val       = $this->input->post('unit',           TRUE);
        $sell_price     = $this->input->post('sell_price',     TRUE);
        $cost_price     = $this->input->post('cost_price',     TRUE);
        $store_val      = $this->input->post('store',          TRUE);
        $status_val     = $this->input->post('status',         TRUE);
        $subcategory_id = $this->input->post('subcategory_id', TRUE);
        $brand_id       = $this->input->post('brand_id',       TRUE);
        $oop_id         = $this->input->post('oop_id',         TRUE);

        // ── Duplicate name check ──────────────────────────────────────
        $dup_check = $this->db->select('id')->from('product_information')
            ->where('LOWER(product_name)', strtolower($this->input->post('product_name', FALSE)))
            ->get()->row();
        if ($dup_check) {
            echo json_encode(['status' => 'Error', 'message' => 'Product name already exists: ' . $this->input->post('product_name', FALSE)]);
            return;
        }

        // Unit Cost Logic Specification v1.1 (Phase 7) — UCL-46: Fixed Purchase Price is
        // mandatory and must be > 0 on every product creation path (full Add Product page and
        // every embedded quick-add popup alike), server-side as well as the client-side checks on
        // each screen, since the costing engine falls back to this value whenever a batch has
        // never been costed yet (UCL-38/UCL-42) — a 0 here silently breaks that fallback.
        if ($cost_price === '' || $cost_price === null || (float) $cost_price <= 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Fixed Purchase Price is required and must be greater than 0']);
            return;
        }

        // ── Transaction ───────────────────────────────────────────────
        $this->db->trans_begin();

        try {
            // Line 1: Insert product_information
            $log[] = 'Line 1: INSERT product_information — product="' . $this->input->post('product_name', FALSE) . '"';
            $insert_query = "INSERT INTO product_information
                (product_id, product_name, category_id, unit, product_vat, serial_no, price, product_model, cost_price,
                 product_details, store, status, subcategory_id, brand_id, oop_id, defaultsaleprice, ad, bd, batchtype,
                 printname, supplier_id, product_type, stock, max_stock_level, min_stock_level, reorder_stock_level, reserve_stock_level)
                VALUES
                ('{$product_id}', '{$product_name}', '{$category_id}', '{$unit_val}', '{$vat}', '{$serial_no}',
                 AES_ENCRYPT('{$sell_price}', '{$encryption_key}'), '{$product_model}',
                 AES_ENCRYPT('{$cost_price}', '{$encryption_key}'), '{$description}',
                 '{$store_val}', '{$status_val}', '{$subcategory_id}', '{$brand_id}', '{$oop_id}',
                 '{$defaultsaleprice}', '{$ad}', '{$bd}', '{$batchtype}', '{$printname}',
                 '{$supplier_id}', '{$product_type}', '{$stock}',
                 '{$max_stock_level}', '{$min_stock_level}', '{$reorder_stock_level}', '{$reserve_stock_level}')";

            if (!$this->db->query($insert_query)) {
                $err = $this->db->error();
                throw new Exception('Line 1 failed — ' . ($err['message'] ?? 'DB insert error'));
            }

            $inserted_id  = $this->db->insert_id();
            $product_code = $generate_product_id ? str_pad($inserted_id, 6, '0', STR_PAD_LEFT) : $product_id;
            $log[] = 'Line 1: OK — inserted_id=' . $inserted_id . ', product_code=' . $product_code;

            // Line 2: Update auto-generated product_id
            if ($generate_product_id) {
                $log[] = 'Line 2: UPDATE product_id to ' . $product_code;
                if (!$this->db->query("UPDATE product_information SET product_id = '{$product_code}' WHERE id = '{$inserted_id}'")) {
                    $err = $this->db->error();
                    throw new Exception('Line 2 failed — ' . ($err['message'] ?? 'DB update error'));
                }
                $log[] = 'Line 2: OK';
            }

            // Line 3+: Insert subunit records
            if (!empty($entries) && is_array($entries)) {
                foreach ($entries as $idx => $item) {
                    $lineNo = 3 + $idx;
                    $log[]  = "Line {$lineNo}: INSERT subunit_product — unit_id={$item['subunitid']}";

                    $subunit_query = "INSERT INTO subunit_product (product_id, unit_id, subsell_price, subcost_price, first)
                        VALUES ('{$inserted_id}', '{$item['subunitid']}',
                                AES_ENCRYPT('{$item['subsell_price']}', '{$encryption_key}'),
                                AES_ENCRYPT('{$item['subcost_price']}', '{$encryption_key}'),
                                '{$item['selectedInt']}')";

                    if (!$this->db->query($subunit_query)) {
                        $err = $this->db->error();
                        throw new Exception("Line {$lineNo} failed (subunit unit_id={$item['subunitid']}) — " . ($err['message'] ?? 'DB insert error'));
                    }
                    $log[] = "Line {$lineNo}: OK";

                    // Save conversion ratio into existing conversion_ratio table
                    if (!empty($item['conversion_ratio']) && $item['conversion_ratio'] > 0) {
                        $ratio = (float)$item['conversion_ratio'];
                        $unit  = (int)$item['subunitid'];
                        $this->db->query("DELETE FROM conversion_ratio WHERE product = '{$inserted_id}' AND subunit = '{$unit}'");
                        $this->db->query("INSERT INTO conversion_ratio (product, subunit, conversion_ratio, convertiontype, status) VALUES ('{$inserted_id}', '{$unit}', '{$ratio}', '*', 1)");
                    }
                }
            }

            $this->db->trans_commit();

            echo json_encode([
                'status'       => 'Success',
                'id'           => $inserted_id,
                'product_code' => $product_code,
            ]);

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $log[] = 'ROLLBACK: ' . $e->getMessage();
            $this->_write_product_upload_log('ERROR', $log);

            echo json_encode([
                'status'  => 'Error',
                'message' => $e->getMessage(),
                'log'     => $log,
            ]);
        }
    }

    private function _write_product_upload_log($level, array $steps)
    {
        $log_dir  = APPPATH . 'logs/product_upload/';
        if (!is_dir($log_dir)) { mkdir($log_dir, 0755, true); }

        $log_file = $log_dir . 'upload_' . date('Y-m-d') . '.log';
        $lines    = '[' . date('Y-m-d H:i:s') . '] [' . $level . ']' . PHP_EOL;
        foreach ($steps as $step) {
            $lines .= '    ' . $step . PHP_EOL;
        }
        $lines .= PHP_EOL;
        file_put_contents($log_file, $lines, FILE_APPEND | LOCK_EX);
    }

    public function get_product_form_data()
    {
        echo json_encode([
            'categories' => $this->product_model->active_category() ?: [],
            'units'      => $this->product_model->active_unit()     ?: [],
        ]);
    }




    public function update_product()
    {
        $encryption_key = Config::$encryption_key;
        $edit_id = (int)$this->input->post('id', TRUE);
        $entries = $this->input->post('entries', TRUE);

        $supplier_id = $this->input->post('supplier_id', TRUE);
        $supplier_id = ($supplier_id === '' || $supplier_id === null) ? 0 : $supplier_id;

        $vat = $this->input->post('vat', TRUE);
        $vat = ($vat === '' || $vat === null) ? 0 : $vat;

        $defaultsaleprice = $this->input->post('defaultsaleprice', TRUE);
        $defaultsaleprice = ($defaultsaleprice === '' || $defaultsaleprice === null) ? 'fixedprice' : $defaultsaleprice;

        $batchtype = $this->input->post('batchtype', TRUE);
        $batchtype = ($batchtype === '' || $batchtype === null) ? 1 : $batchtype;

        $ad = $this->input->post('ad', TRUE);
        $ad = ($ad === null) ? '' : $ad;

        $bd = $this->input->post('bd', TRUE);
        $bd = ($bd === null) ? '' : $bd;

        $stock = $this->input->post('stock', TRUE);
        $stock = ($stock === '' || $stock === null) ? 0 : $stock;

        $max_stock_level = $this->input->post('max_stock_level', TRUE);
        $max_stock_level = is_numeric($max_stock_level) ? $max_stock_level : 0;

        $min_stock_level = $this->input->post('min_stock_level', TRUE);
        $min_stock_level = is_numeric($min_stock_level) ? $min_stock_level : 0;

        $reorder_stock_level = $this->input->post('reorder_stock_level', TRUE);
        $reorder_stock_level = is_numeric($reorder_stock_level) ? $reorder_stock_level : 0;

        $reserve_stock_level = $this->input->post('reserve_stock_level', TRUE);
        $reserve_stock_level = is_numeric($reserve_stock_level) ? $reserve_stock_level : 0;

        // Unit Cost Logic Specification v1.1 (Phase 7) — UCL-46: same mandatory, > 0 check as
        // save_product() (see there for why).
        $cost_price_for_check = $this->input->post('cost_price', TRUE);
        if ($cost_price_for_check === '' || $cost_price_for_check === null || (float) $cost_price_for_check <= 0) {
            echo json_encode(['status' => 'Error', 'message' => 'Fixed Purchase Price is required and must be greater than 0']);
            return;
        }

        $query = "UPDATE product_information SET
            product_name = '{$this->input->post('product_name', TRUE)}',
            category_id = '{$this->input->post('category_id', TRUE)}',
            unit = '{$this->input->post('unit', TRUE)}',
            product_vat = '{$vat}',
            serial_no = '{$this->input->post('serial_no', TRUE)}',
            price = AES_ENCRYPT('{$this->input->post('sell_price', TRUE)}', '{$encryption_key}'),
            product_model = '{$this->input->post('product_model', TRUE)}',
            cost_price = AES_ENCRYPT('{$this->input->post('cost_price', TRUE)}', '{$encryption_key}'),
            product_details = '{$this->input->post('description', TRUE)}',
            store = '{$this->input->post('store', TRUE)}',
            status = '{$this->input->post('status', TRUE)}',
            subcategory_id = '{$this->input->post('subcategory_id', TRUE)}',
            brand_id = '{$this->input->post('brand_id', TRUE)}',
            oop_id = '{$this->input->post('oop_id', TRUE)}',
            defaultsaleprice = '{$defaultsaleprice}',
            ad = '{$ad}',
            bd = '{$bd}',
            batchtype = '{$batchtype}',
            printname = '{$this->input->post('printname', TRUE)}',
            supplier_id = '{$supplier_id}',
            product_type = '{$this->input->post('product_type', TRUE)}',
            stock = '{$stock}',
            max_stock_level = '{$max_stock_level}',
            min_stock_level = '{$min_stock_level}',
            reorder_stock_level = '{$reorder_stock_level}',
            reserve_stock_level = '{$reserve_stock_level}'
        WHERE id = '{$this->input->post('id', TRUE)}'";

        if ($this->db->query($query)) {
            if (!empty($entries) && is_array($entries)) {
                foreach ($entries as $item) {
                    $unit = (int)$item['subunitid'];
                    if ($item['id'] == 0) {
                        $subunit_query = "INSERT INTO subunit_product (product_id, unit_id, subsell_price, subcost_price, first) VALUES ('{$edit_id}', '{$unit}', AES_ENCRYPT('{$item['subsell_price']}', '{$encryption_key}'), AES_ENCRYPT('{$item['subcost_price']}', '{$encryption_key}'), '{$item['selectedInt']}')";
                        $this->db->query($subunit_query);
                    } else {
                        $subunit_query = "UPDATE subunit_product SET subsell_price = AES_ENCRYPT('{$item['subsell_price']}', '{$encryption_key}'), subcost_price = AES_ENCRYPT('{$item['subcost_price']}', '{$encryption_key}'), first = '{$item['selectedInt']}', product_id = '{$edit_id}', unit_id = '{$unit}' WHERE id = '{$item['id']}'";
                        $this->db->query($subunit_query);
                    }

                    // Save/update conversion ratio (skip if row is locked — used in stock records)
                    $is_locked = !empty($item['is_ratio_locked']) && $item['is_ratio_locked'];
                    if (!$is_locked && !empty($item['conversion_ratio']) && $item['conversion_ratio'] > 0) {
                        $ratio = (float)$item['conversion_ratio'];
                        $this->db->query("DELETE FROM conversion_ratio WHERE product = '{$edit_id}' AND subunit = '{$unit}'");
                        $this->db->query("INSERT INTO conversion_ratio (product, subunit, conversion_ratio, convertiontype) VALUES ('{$edit_id}', '{$unit}', '{$ratio}', '*')");
                    }
                }
            }

            echo json_encode(["status" => "Success"]);
        } else {
            $db_error = $this->db->error();
            $error_message = !empty($db_error['message']) ? $db_error['message'] : 'Database query failed';
            echo json_encode(["status" => "Error", "message" => $error_message]);
        }
    }
    public function update_subunit()
    {
        $encryption_key = Config::$encryption_key;
        $id         = (int)$this->input->post('id', TRUE);
        $unit_id    = (int)$this->input->post('unit_id', TRUE);
        $product_id = (int)$this->input->post('product_id', TRUE);
        $ratio      = (float)$this->input->post('conversion_ratio', TRUE);

        $query = "UPDATE subunit_product SET
            subsell_price = AES_ENCRYPT('{$this->input->post('subsell_price', TRUE)}', '{$encryption_key}'),
            subcost_price = AES_ENCRYPT('{$this->input->post('subcost_price', TRUE)}', '{$encryption_key}')
            WHERE id = '{$id}'";
        $this->db->query($query);

        // Update conversion ratio only if not locked (not used in stock records)
        $ratio_locked = (int)$this->input->post('ratio_locked', TRUE);
        if (!$ratio_locked && $ratio > 0 && $unit_id > 0 && $product_id > 0) {
            $this->db->query("DELETE FROM conversion_ratio WHERE product = '{$product_id}' AND subunit = '{$unit_id}'");
            $this->db->query("INSERT INTO conversion_ratio (product, subunit, conversion_ratio, convertiontype) VALUES ('{$product_id}', '{$unit_id}', '{$ratio}', '*')");
        }

        echo json_encode("Success");
    }

    public function check_product_unique()
    {
        $field      = $this->input->post('field', TRUE);
        $value      = $this->input->post('value', TRUE);
        $exclude_id = (int)$this->input->post('exclude_id', TRUE);

        if (!in_array($field, ['product_name', 'product_id'])) {
            echo json_encode(['exists' => false]);
            return;
        }

        $this->db->where($field, $value);
        if ($exclude_id > 0) {
            $this->db->where('id !=', $exclude_id);
        }
        $count = $this->db->count_all_results('product_information');
        echo json_encode(['exists' => $count > 0]);
    }

    public function delete_subunit()
    {
        $id = (int)$this->input->post('id', TRUE);

        // Lookup product_id and unit_id before deleting so we can remove the conversion ratio too
        $row = $this->db->select('product_id, unit_id')->where('id', $id)->get('subunit_product')->row();

        $this->db->query("DELETE FROM subunit_product WHERE id = '{$id}'");

        if ($row) {
            $this->db->query(
                "DELETE FROM conversion_ratio WHERE product = '{$row->product_id}' AND subunit = '{$row->unit_id}'"
            );
        }

        echo json_encode("Success");
    }

    public function active_subunitsbyproductId()
    {

        $this->db->select('sp.unit_id, u.unit_name, p.unit, u2.unit_name as name2,p.stock,sp.first');
        $this->db->from('subunit_product sp');
        $this->db->join('units u', 'u.unit_id = sp.unit_id','left');
        $this->db->join('product_information p', 'p.id = sp.product_id','left');
        $this->db->join('units u2', 'u2.unit_id = p.unit','left');
        $this->db->where('p.id', $this->input->post('product_id', TRUE));
        $this->db->where('u.status', 1);
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }else{
            $this->db->select('p.unit, u2.unit_name as name2, 0 as unit_id, "" as unit_name, p.stock', FALSE);
            $this->db->from('product_information p');
            $this->db->join('units u2', 'u2.unit_id = p.unit','left');
            $this->db->where('p.id', $this->input->post('product_id', TRUE)); 
            $this->db->where('u2.status', 1);

            $query1 = $this->db->get();
            if ($query1->num_rows() > 0) {
                echo json_encode($query1->result_array());
            }
        }
    }


    function bdtask_productgroup_form($id = null)
    {
        $data['title']       = display('add_product_group');
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->product_model->active_store();
        $data['module']      = "product";
        $data['page']        = "productgroup_form";

        $has_access = $id != null
            ? $this->permission1->method('product_grouplist', 'update')->access()
            : $this->permission1->method('product_grouplist', 'create')->access();

        if ($has_access) {
            if ($id != null) {

                $data['title'] = "Edit Product Group";
                $data['id'] = $id;

            }else{
                $sql3 = "SELECT MAX(id)+1 AS highest_product_id FROM product_group;";
                $query3 = $this->db->query($sql3);
                $result3 = $query3->row();
                $data['product_group_id'] = !empty($result3->highest_product_id) ? str_pad($result3->highest_product_id, 6, '0', STR_PAD_LEFT) : "000001";
            }
            // echo modules::run('template/layout', $data);
            echo modules::run('template/layout', $data);
        } else {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    public function check_duplicate_productgroup()
    {
        $value = trim($this->input->post('value', TRUE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $this->db->select('id')->from('product_group')->where('name', $value);
        if (!empty($exclude_id)) { $this->db->where('id !=', $exclude_id); }
        $row = $this->db->get()->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A product group with this name already exists.' : '']);
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


    public function save_productgroup()
    {
        if (!$this->permission1->method('product_grouplist', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $items = $this->input->post('items', TRUE);

        $encryption_key = Config::$encryption_key;

        $check = $this->db->query("
    SELECT id 
    FROM product_group 
    WHERE groupcode = '" . $this->input->post('product_group_id', TRUE) . "' 
");
        if ($check->num_rows() > 0) {

            echo json_encode("already");
        } else {
            $query = "
    INSERT INTO product_group 
    (id,groupcode, name,status,invoice_group) 
    VALUES 
    (0,
     '{$this->input->post('product_group_id', TRUE)}',
     '{$this->input->post('product_group_name', TRUE)}',  
     '{$this->input->post('status', TRUE)}',
      '{$this->input->post('invoicegroup', TRUE)}'
    );";

            $this->db->query($query);


            $inserted_id = $this->db->insert_id();
            foreach ($items as $item) {


                $query = "
            INSERT INTO product_group_details 
            (id, pid, product,qty,unit,parent) 
            VALUES 
            (0, 
             '{$inserted_id}', 
             '{$item['product']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
                 '{$item['unit']}',
                  '{$item['parent']}'
            );";

                $this->db->query($query);
            }



            echo json_encode("Success");
        }
    }

    public function bdtask_product_grouplist()
    {
        $data['title']         = display('product_grouplist');
        $data['total_product'] = $this->db->count_all("product_information");
        $data['module']        = "product";
        $data['page']          = "productgroup_list";
        if (!$this->permission1->method('product_grouplist', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function getProductGroupById()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select("
         po.id, 
         pod.product,
         pod.unit,
         po.groupcode,
         po.name,
          AES_DECRYPT(pod.qty, '{$encryption_key}') AS qty,
         po.status,pod.parent,po.invoice_group
     ");
        $this->db->from('product_group po');
        $this->db->join('product_group_details pod', 'pod.pid = po.id', 'inner');
        $this->db->where('po.id', $this->input->post('id'));

        $query = $this->db->get();


        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }


    public function update_productgroup()
    {
        if (!$this->permission1->method('product_grouplist', 'update')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);

        $check = $this->db->query("
        SELECT id 
        FROM product_group 
        WHERE groupcode = '" . $this->input->post('product_group_id', TRUE) . "' 
        AND id != '" . $this->input->post('id', TRUE) . "'
    ");
        if ($check->num_rows() > 0) {

            echo json_encode("already");
        } else {
            $query = "
    UPDATE product_group
    SET 
        groupcode =  '{$this->input->post('product_group_id', TRUE)}',
        name = '{$this->input->post('product_group_name', TRUE)}',
        status = '{$this->input->post('status', TRUE)}',
        invoice_group='{$this->input->post('invoicegroup', TRUE)}'
    WHERE id = '{$this->input->post('id', TRUE)}';";

            $this->db->query($query);

            $this->db->where('pid', $this->input->post('id', TRUE))
                ->delete('product_group_details');
            foreach ($items as $item) {
                $query = "
            INSERT INTO product_group_details 
            (id, pid, product,qty,unit,parent) 
            VALUES 
            (0, 
             '{$this->input->post('id', TRUE)}', 
             '{$item['product']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
                 '{$item['unit']}',
                  '{$item['parent']}'
            );";

                $this->db->query($query);
            }
            echo json_encode("Success");
        }
    }


    public function CheckProductListForLabelPrint()
    {
        $postData = $this->input->post();
        $data = $this->product_model->getProductListForLabelPrint($postData, $this->input->post('category'));
        echo json_encode($data);
    }

    public function label_print()
    {
        $this->load->model('service/service_model');
        $company_info = $this->service_model->company_info();
        $ws           = $this->service_model->web_setting();
        $data = array(
            'title'        => display('labelprint'),
            'category_list' => $this->product_model->active_category(),
            'currency'     => isset($ws[0]['currency'])          ? $ws[0]['currency']          : '',
            'position'     => isset($ws[0]['currency_position']) ? (int)$ws[0]['currency_position'] : 0,
            'company_name' => isset($company_info[0]['company_name']) ? $company_info[0]['company_name'] : '',
        );
        $data['module'] = "product";
        $data['page']   = "label_print";
        echo modules::run('template/layout', $data);
    }

    public function search_products_for_barcode()
    {
        $search   = $this->input->post('search', FALSE);
        $category = $this->input->post('category', TRUE);
        $data = $this->product_model->search_products_for_barcode($search, $category);
        echo json_encode($data);
    }

    public function get_product_batches()
    {
        $product_id = $this->input->post('product_id', TRUE);
        $search     = $this->input->post('search', FALSE);
        $data = $this->product_model->get_product_batches($product_id, $search);
        echo json_encode($data);
    }

    public function insertLabelPrint()
    {
        $supp_prd = array(
            'id' => $this->input->post('id'),
            'no_of_labels'    => $this->input->post('noOfLabel'),
            'product_code' => $this->input->post('productId'),
            'product' => $this->input->post('productName'),
            'category' => $this->input->post('categoryName'),
            'price' =>  number_format((float)$this->input->post('price'), 2, '.', ''),
        );

        $this->db->insert('labelprint', $supp_prd);

        echo json_encode("Success");
    }



    public function deleteLabelPrint()
    {
        $this->db->where('id', $this->input->post('id'))
            ->delete("labelprint");

        if ($this->db->affected_rows()) {
            echo json_encode("Success");
        } else {
            echo json_encode("");
        }
    }

    public function deletewholeLabelPrint()
    {
        if ($this->db->query('TRUNCATE TABLE labelprint')) {
            echo json_encode("Success");
        } else {
            echo json_encode("unsuccess");
        }
    }

    public function getLabelPrintData()
    {
        $query = $this->db->select('id, no_of_labels as noOfLabel, product_code as productId, 
                                product as productName, category as categoryName, 
                               price as price')
            ->from('labelprint')
            ->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo  json_encode([]);
        }
    }

    public function printsticker()
    {
        $_SESSION['barcodelabels'] = $this->input->post('labels');
        $_SESSION['cqty'] = 1;
        $this->db->query('TRUNCATE TABLE barcode_sticker');
        foreach ($_SESSION['barcodelabels'] as $label) {
            $noOfLabel = (int)$label['noOfLabel'];
            for ($i = 0; $i < $noOfLabel; $i++) {
                $supp_prd = array(
                    'product_id'   => $label['productId'],
                    'product_name' => $label['productName'],
                    'price'        => number_format((float)$label['price'], 2, '.', ''),
                    'batch_id'     => !empty($label['batchId'])   ? (int)$label['batchId']   : null,
                    'batch_name'   => !empty($label['batchName']) ? $label['batchName']       : null,
                );
                $this->db->insert('barcode_sticker', $supp_prd);
            }
        }
        echo json_encode($_SESSION['barcodelabels']);
    }

    public function barcode_print()
    {
        $this->load->model('service/service_model');
        $stickers     = $this->db->get('barcode_sticker')->result_array();
        $company_info = $this->service_model->company_info();
        $ws           = $this->service_model->web_setting();
        $cqty         = max(1, (int)($this->input->get('cqty') ?: 2));

        $data = array(
            'stickers'     => $stickers,
            'company_name' => isset($company_info[0]['company_name']) ? $company_info[0]['company_name'] : '',
            'currency'     => isset($ws[0]['currency'])          ? $ws[0]['currency']          : '',
            'position'     => isset($ws[0]['currency_position']) ? (int)$ws[0]['currency_position'] : 0,
            'cqty'         => $cqty,
        );
        // Render standalone — no ERP template wrapper, so print page is clean
        $this->load->view('barcode_print_page', $data);
    }

    public function upload_product_image()
    {
        $product_id = $this->input->post('product_id', TRUE);
        if (!$product_id) {
            echo json_encode(['status' => 'Error', 'message' => 'No product_id']);
            return;
        }

        $dir      = FCPATH . 'assets/img/products/';
        $filename = $product_id . '.jpg';
        $filepath = $dir . $filename;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Remove image
        if ($this->input->post('remove_image') == '1') {
            @unlink($filepath);
            $this->db->query("UPDATE product_information SET product_image = NULL WHERE id = '{$product_id}'");
            echo json_encode(['status' => 'Success']);
            return;
        }

        // Upload image file
        if (empty($_FILES['product_image']['tmp_name'])) {
            echo json_encode(['status' => 'Error', 'message' => 'No file received']);
            return;
        }

        $tmp = $_FILES['product_image']['tmp_name'];
        $allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($tmp);
        if (!in_array($mime, $allowed)) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid file type: ' . $mime]);
            return;
        }

        if (move_uploaded_file($tmp, $filepath)) {
            $rel_path = 'assets/img/products/' . $filename;
            $this->db->query("UPDATE product_information SET product_image = '{$rel_path}' WHERE id = '{$product_id}'");
            echo json_encode(['status' => 'Success', 'path' => $rel_path]);
        } else {
            echo json_encode(['status' => 'Error', 'message' => 'move_uploaded_file failed. Check folder permissions for: ' . $dir]);
        }
    }

    private function _saveProductImage($base64Data, $productId)
    {
        $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $base64Data);
        // POST decodes '+' as spaces — restore before base64_decode
        $base64Data = str_replace(' ', '+', $base64Data);
        $imageData  = base64_decode($base64Data, true);
        if (!$imageData) return false;

        $dir = FCPATH . 'assets/img/products/';
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $filename = $productId . '.jpg';
        if (file_put_contents($dir . $filename, $imageData) !== false) {
            return 'assets/img/products/' . $filename;
        }
        return false;
    }

    public function api_save_brand()
    {
        header('Content-Type: application/json');
        $id         = (int)$this->input->post('brand_id', true);
        $brand_name = trim($this->input->post('brand_name', true));
        $status     = $this->input->post('status', true);
        $button     = $this->input->post('button', true);

        if (empty($brand_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Brand Name is required']);
            return;
        }
        $postData = ['brand_id' => $id ?: null, 'brand_name' => $brand_name, 'status' => $status];

        if (empty($id)) {
            $ok       = $this->product_model->create_brand($postData);
            $msg      = $ok ? 'Brand saved successfully' : 'Failed to save. Please try again.';
            $redirect = ($ok && $button === 'add-another') ? 'brand_form' : 'brand_list';
        } else {
            $ok       = $this->product_model->update_brand($postData);
            $msg      = $ok ? 'Brand updated successfully' : 'Failed to update. Please try again.';
            $redirect = 'brand_list';
        }
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $msg, 'redirect' => $redirect]);
    }

    public function api_save_category()
    {
        header('Content-Type: application/json');
        $id            = (int)$this->input->post('category_id', true);
        $category_name = trim($this->input->post('category_name', true));
        $status        = $this->input->post('status', true);
        $button        = $this->input->post('button', true);

        if (empty($category_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Category Name is required']);
            return;
        }
        $postData = ['category_id' => $id ?: null, 'category_name' => $category_name, 'status' => $status];

        if (empty($id)) {
            $ok       = $this->product_model->create_category($postData);
            $msg      = $ok ? 'Category saved successfully' : 'Failed to save. Please try again.';
            $redirect = ($ok && $button === 'add-another') ? 'category_form' : 'category_list';
        } else {
            $ok       = $this->product_model->update_category($postData);
            $msg      = $ok ? 'Category updated successfully' : 'Failed to update. Please try again.';
            $redirect = 'category_list';
        }
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $msg, 'redirect' => $redirect]);
    }

    public function api_save_subcategory()
    {
        header('Content-Type: application/json');
        $id               = (int)$this->input->post('subcategory_id', true);
        $subcategory_name = trim($this->input->post('subcategory_name', true));
        $category_id      = (int)$this->input->post('category_id', true);
        $status           = $this->input->post('status', true);
        $button           = $this->input->post('button', true);

        if (empty($subcategory_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Subcategory Name is required']);
            return;
        }
        if (empty($category_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Category is required']);
            return;
        }
        $postData = [
            'subcategory_id'   => $id ?: null,
            'subcategory_name' => $subcategory_name,
            'category_id'      => $category_id,
            'status'           => $status,
        ];

        if (empty($id)) {
            $ok       = $this->product_model->create_subcategory($postData);
            $msg      = $ok ? 'Subcategory saved successfully' : 'Failed to save. Please try again.';
            $redirect = ($ok && $button === 'add-another') ? 'subcategory_form' : 'subcategory_list';
        } else {
            $ok       = $this->product_model->update_subcategory($postData);
            $msg      = $ok ? 'Subcategory updated successfully' : 'Failed to update. Please try again.';
            $redirect = 'subcategory_list';
        }
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $msg, 'redirect' => $redirect]);
    }

    public function api_save_unit()
    {
        header('Content-Type: application/json');
        $id                = (int)$this->input->post('unit_id', true);
        $unit_name         = trim($this->input->post('unit_name', true));
        $unit_display_name = trim($this->input->post('unit_display_name', true));
        $status            = $this->input->post('status', true);
        $button            = $this->input->post('button', true);

        if (empty($unit_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Unit Name is required']);
            return;
        }
        $postData = [
            'unit_id'           => $id ?: null,
            'unit_name'         => $unit_name,
            'unit_display_name' => $unit_display_name,
            'status'            => $status,
        ];

        if (empty($id)) {
            $ok       = $this->product_model->create_unit($postData);
            $msg      = $ok ? 'Unit saved successfully' : 'Failed to save. Please try again.';
            $redirect = ($ok && $button === 'add-another') ? 'unit_form' : 'unit_list';
        } else {
            $ok       = $this->product_model->update_unit($postData);
            $msg      = $ok ? 'Unit updated successfully' : 'Failed to update. Please try again.';
            $redirect = 'unit_list';
        }
        echo json_encode(['status' => $ok ? 'success' : 'error', 'message' => $msg, 'redirect' => $redirect]);
    }

}