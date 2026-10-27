<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Product_model extends CI_Model
{

    public function category_list()
    {
        return $this->db->select('*')
            ->from('product_category')
            ->get()
            ->result();
    }


    public function create_category($data = [])
    {
        return $this->db->insert('product_category', $data);
    }

    public function vat_tax_setting()
    {
        $this->db->select('*');
        $this->db->from('vat_tax_setting');
        $query   = $this->db->get();
        return $query->row();
    }



    public function update_category($data = [])
    {
        return $this->db->where('category_id', $data['category_id'])
            ->update('product_category', $data);
    }

    public function single_category_data($id)
    {
        return $this->db->select('*')
            ->from('product_category')
            ->where('category_id', $id)
            ->get()
            ->row();
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


    // unit part
    public function unit_list()
    {
        return $this->db->select('*')
            ->from('units')
            ->get()
            ->result();
    }


    public function create_unit($data = [])
    {
        return $this->db->insert('units', $data);
    }



    public function update_unit($data = [])
    {
        return $this->db->where('unit_id', $data['unit_id'])
            ->update('units', $data);
    }

    public function single_unit_data($id)
    {
        return $this->db->select('*')
            ->from('units')
            ->where('unit_id', $id)
            ->get()
            ->row();
    }


    
    public function delete_unit($id)
{
    $array = explode("1234", $id);
    $unit_id = $array[1];
    $unit_name = $array[0];

    // Check if unit is used in product_information table
    $productExists = $this->db->from('product_information')
        ->where('unit', $unit_id)
        ->count_all_results();

    if ($productExists > 0) {
        return false;
    }

    // Check if unit is used in stock_details table
    $stockDetailsExists = $this->db->from('stock_details')
        ->where('unit', $unit_id)
        ->count_all_results();

    if ($stockDetailsExists > 0) {
        return false;
    }

    // Check if unit is used in phystock_details table
    $phyStockDetailsExists = $this->db->from('phystock_details')
        ->where('unit', $unit_id)
        ->count_all_results();

    if ($phyStockDetailsExists > 0) {
        return false;
    }

    // Check if unit is used in subunit_product table
    $subunitProductExists = $this->db->from('subunit_product')
        ->where('unit_id', $unit_id)
        ->count_all_results();

    if ($subunitProductExists > 0) {
        return false;
    }

    // Check if unit is used in conversion_ratio table (subunit column)
    $conversionRatioExists = $this->db->from('conversion_ratio')
        ->where('subunit', $unit_id)
        ->count_all_results();

    if ($conversionRatioExists > 0) {
        return false;
    }

    // If no references exist, proceed to delete the unit
    $this->db->where('unit_id', $unit_id)
        ->delete('units');
    
    return $this->db->affected_rows() > 0;
}



    public function supplier_list()
    {
        $encryption_key = Config::$encryption_key;
        
        $this->db->select("supplier_id, AES_DECRYPT(supplier_name, '". $encryption_key."') as supplier_name");
        $this->db->from('supplier_information');
        $this->db->order_by('supplier_name', 'asc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_category()
    {
        $this->db->select('*');
        $this->db->from('product_category');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function active_product()
    {
        $this->db->select('p.id,p.product_name,p.unit,u.unit_name');
        $this->db->from('product_information p');
        $this->db->join('units u', 'u.unit_id = p.unit');
        $this->db->where('p.status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function all_products_for_csv()
    {
        $this->db->select('p.id,p.product_name,p.unit,u.unit_name');
        $this->db->from('product_information p');
        $this->db->join('units u', 'u.unit_id = p.unit');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return [];
    }


    public function active_subcategory()
    {
        $this->db->select('*');
        $this->db->from('product_subcategory');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function active_brand()
    {
        $this->db->select('*');
        $this->db->from('product_brand');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function active_oop()
    {
        $this->db->select('*');
        $this->db->from('product_oop');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }


    public function active_floorByStore($id)
    {
        $this->db->select('st.id, st.name');
        $this->db->from('store s');
        $this->db->join('storefloor sf', 'sf.store = s.id');
        $this->db->join('floor st', 'st.id = sf.floor');
        $this->db->where('s.id', $id);
        $this->db->where('st.status', 1);


        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function active_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->where('status', 1);
        $this->db->where_not_in('id', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

     public function all_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        // $this->db->where('status', 1);
        $this->db->where_not_in('id', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_unit()
    {
        $this->db->select('*');
        $this->db->from('units');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }

    public function supplier_product_list($id)
    {
        $this->db->select('*');
        $this->db->from('supplier_product');
        $this->db->where('product_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return FALSE;
    }


    public function product_opening($id)
    {
        $this->db->select('*');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $id);
        $this->db->where('purchase_id IS NULL');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return null;
        }
    }



    public function single_product_data($id)
    {
        $encryption_key = Config::$encryption_key;

        return 
        $this->db->select("
    product_id,
    product_name,
    category_id,
    unit,
    product_vat,
    serial_no,
    AES_DECRYPT(price, '{$encryption_key}') AS price,
    product_model,
    AES_DECRYPT(cost_price, '{$encryption_key}') AS cost_price,
    product_details,
    subcategory_id,
    store,
    IF(status = 1, 'Active', 'Inactive') as status_label,
     subcategory_id,brand_id,oop_id,
        defaultsaleprice,ad,bd,addigit,batchtype,printname,
        supplier_id,product_type,stock,
        max_stock_level,min_stock_level,reorder_stock_level,reserve_stock_level,
        product_image


")
            ->from('product_information')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function all_product_names()
    {
        return $this->db->select('product_name')
            ->from('product_information')
            ->get()->result_array();
    }

    public function all_product_ids()
    {
        return $this->db->select('product_id')
            ->from('product_information')
            ->get()->result_array();
    }

    public function all_brand_names()
    {
        return $this->db->select('brand_name')
            ->from('product_brand')
            ->get()->result_array();
    }

    public function all_category_names()
    {
        return $this->db->select('category_name')
            ->from('product_category')
            ->get()->result_array();
    }

    public function all_subcategory_names()
    {
        return $this->db->select('subcategory_name, category_id')
            ->from('product_subcategory')
            ->get()->result_array();
    }

    public function all_unit_names()
    {
        return $this->db->select('unit_name')
            ->from('units')
            ->get()->result_array();
    }

    public function all_existing_conversionratios()
    {
        return $this->db->select('product, subunit')
            ->from('conversion_ratio')
            ->where('status', 1)
            ->get()->result_array();
    }

    public function all_product_subunits()
    {
        return $this->db->select('product_id, unit_id')
            ->from('subunit_product')
            ->get()->result_array();
    }

    public function single_subunit_product($id)
    {
        $encryption_key = Config::$encryption_key;

        return
        $this->db->select("sp.id,
    sp.unit_id,
    AES_DECRYPT(sp.subsell_price, '{$encryption_key}') AS subsell_price,
    AES_DECRYPT(sp.subcost_price, '{$encryption_key}') AS subcost_price,
    sp.first,
    u.unit_name,
    COALESCE(cr.conversion_ratio, '') AS conversion_ratio,
    cr.conversionratio_id
")
            ->from('subunit_product sp')
            ->join('units u', 'u.unit_id = sp.unit_id')
            ->join('conversion_ratio cr', "cr.product = sp.product_id AND cr.subunit = sp.unit_id", 'left')
            ->where('sp.product_id', $id)
            ->get()
            ->result();
    }

    public function get_subunit_with_ratio($id)
    {
        $encryption_key = Config::$encryption_key;

        return
        $this->db->select("sp.id,
    sp.unit_id,
    u.unit_name,
    AES_DECRYPT(sp.subsell_price, '{$encryption_key}') AS subsell_price,
    AES_DECRYPT(sp.subcost_price, '{$encryption_key}') AS subcost_price,
    sp.first,
    COALESCE(cr.conversion_ratio, '') AS conversion_ratio
")
            ->from('subunit_product sp')
            ->join('units u', 'u.unit_id = sp.unit_id')
            ->join('conversion_ratio cr', "cr.product = sp.product_id AND cr.subunit = sp.unit_id", 'left')
            ->where('sp.product_id', $id)
            ->get()
            ->result();
    }

    public function get_conversionrations($id)
    {
        return
        $this->db->select("cr.subunit")
            ->from('conversion_ratio cr')
            ->where('cr.product', $id)
            ->get()
            ->result();
    }


    public function create_product($query = null)
    {

        $this->db->query($query);
        return true;

       
    }


    public function update_product($query = null)
    {
        $this->db->query($query);
        return true;
    }

    public function getProductList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (product_id like '%" . $searchValue . "%' or product_name like '%" . $searchValue . "%' or category_name like '%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        $this->db->join('product_category c', 'c.category_id = a.category_id');


        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('a.product_id', $searchValue);
            $this->db->or_like('a.product_name', $searchValue);
            // $this->db->or_like('c.subcategory_name', $searchValue);
            $this->db->group_end();
        }
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        $this->db->join('product_category c', 'c.category_id = a.category_id');

        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('a.product_id', $searchValue);
            $this->db->or_like('a.product_name', $searchValue);
            // $this->db->or_like('c.subcategory_name', $searchValue);
            $this->db->group_end();
        }
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;
        $encryption_key = Config::$encryption_key;

        ## Fetch records
        $this->db->select("
        a.id as id,
                a.product_name as product_name,
                a.product_id as product_id,c.category_name as category_name ,
                AES_DECRYPT(a.price, '{$encryption_key}') AS price ,
                AES_DECRYPT(a.cost_price, '{$encryption_key}') AS cost_price,
IF(a.status = 1, 'Active', 'Inactive') as status_label,s.name as sname");
        $this->db->from('product_information a');
        $this->db->join('store s', 's.id = a.store');

        $this->db->join('product_category c', 'c.category_id = a.category_id');


        if ($searchValue != '') {
            $this->db->group_start();
            $this->db->like('a.product_id', $searchValue);
            $this->db->or_like('a.product_name', $searchValue);
            // $this->db->or_like('c.subcategory_name', $searchValue);
            $this->db->group_end();
        }
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()
        ->result();




        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            // $image = '<img src="' . $base_url . $record->image . '" class="img img-responsive" height="50" width="50">';

            if ($this->permission1->method('manage_product', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'product_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_product', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'product/product/bdtask_deleteproduct/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }

            // $button .= '  <a href="' . $base_url . 'qrcode/' . $record->id . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('qr_code') . '"><i class="fa fa-qrcode" aria-hidden="true"></i></a>';

            // $button .= '  <a href="' . $base_url . 'barcode/' . $record->id . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('barcode') . '"><i class="fa fa-barcode" aria-hidden="true"></i></a>';


            $product_name = '<a href="' . $base_url . 'product_details/' . $record->id . '">' . $record->product_name . '</a>';
            $supplier = '<a href="' . $base_url . 'supplier_ledgerinfo/' . $record->supplier_id . '">' . $record->supplier_name . '</a>';

            if($record->status_label=="Active"){
                $status='<span class="label label-success"  >'.$record->status_label.'</a>';

            }
            else{
                $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

            }
            $data[] = array(
                'sl'               => $sl,
                'product_name'     => $record->product_name,
                'category_name'     => $record->category_name,
                'product_id'       => $record->product_id,
                'price'            =>$record->price!=""? number_format($record->price, 2, '.', ','):0,
                'cost_price'       =>$record->cost_price!=""? number_format($record->cost_price, 2, '.', ','):0,
                'sname'            =>$record->sname,
                'status'           => $status,
                'button'           => $button,

            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }

    public function getProductGroupList($postData = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (groupcode like '%" . $searchValue . "%' or name like '%" . $searchValue  . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_group a');
        if ($searchValue != '')
        $this->db->where($searchQuery);


      
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_group a');

        if ($searchValue != '')
        $this->db->where($searchQuery);

        
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;
        $encryption_key = Config::$encryption_key;

        ## Fetch records
        $this->db->select("
        a.id as id,
                a.groupcode as groupcode,
                a.name as name ,
          IF(a.status = 1, 'Active', 'Inactive') as status_label");
        $this->db->from('product_group a');
        if ($searchValue != '')
        $this->db->where($searchQuery);


      
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()
        ->result();




        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            // $image = '<img src="' . $base_url . $record->image . '" class="img img-responsive" height="50" width="50">';

            if ($this->permission1->method('product_grouplist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'edit_product_group/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('product_grouplist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'product/product/bdtask_deleteproductgroup/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }

            // $button .= '  <a href="' . $base_url . 'qrcode/' . $record->id . '" class="btn btn-success btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('qr_code') . '"><i class="fa fa-qrcode" aria-hidden="true"></i></a>';

            // $button .= '  <a href="' . $base_url . 'barcode/' . $record->id . '" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('barcode') . '"><i class="fa fa-barcode" aria-hidden="true"></i></a>';


            
            if($record->status_label=="Active"){
                $status='<span class="label label-success"  >'.$record->status_label.'</a>';

            }
            else{
                $status='<span class="label label-danger"  >'.$record->status_label.'</a>';

            }
            $data[] = array(
                'sl'               => $sl,
                'groupcode'     => $record->groupcode,
                'name'            => $record->name,
                'status'           => $status,
                'button'           => $button,

            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );

        return $response;
    }


    public function delete_product($id)
    {

        $productExists = $this->db->from('stock_details')
            ->where('product', $id)
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('product_id', $id)
            ->delete('subunit_product');
            $this->db->where('id', $id)
            ->delete("product_information");
            return $this->db->affected_rows() > 0;
        }
    }

    public function check_product($id)
    {
        $this->db->select('*');
        $this->db->from('product_purchase_details');
        $this->db->where('product_id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return FALSE;
    }

    public function bdtask_barcode_productdata($id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("id as id,
                product_name as product_name,
                product_id as product_id,
                AES_DECRYPT(price, '{$encryption_key}') AS price ,
                AES_DECRYPT(cost_price, '{$encryption_key}') AS cost_price")
            ->from('product_information')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function product_purchase_info($product_id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("a.*,b.*,AES_DECRYPT(b.quantity, '". $encryption_key."') as quantity,
        AES_DECRYPT(b.total_price, '". $encryption_key."') as total_amount, AES_DECRYPT(a.chalan_no, '". $encryption_key."') as chalan_no,AES_DECRYPT(a.purchase_id, '". $encryption_key."') as purchase_id,
        AES_DECRYPT(c.supplier_name, '". $encryption_key."') as supplier_name");
        $this->db->from('purchase a');
        $this->db->join('purchase_details b', 'b.pid = a.id');
        $this->db->join('supplier_information c', 'c.supplier_id = a.supplier_id');
        $this->db->where('b.product', $product_id);
        $this->db->order_by('a.date', 'desc');
        $this->db->group_by('a.id');
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function invoice_data($product_id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("a.*,b.*,AES_DECRYPT(b.quantity, '". $encryption_key."') as quantity,
                AES_DECRYPT(b.total_price, '". $encryption_key."') as total_amount, AES_DECRYPT(a.sale_id, '". $encryption_key."') as sale_id,
        AES_DECRYPT(c.customer_name, '". $encryption_key."') as customer_name");
        $this->db->from('sale a');
        $this->db->join('sale_details b', 'b.pid = a.id');
        $this->db->join('customer_information c', 'c.customer_id = a.customer_id');
        $this->db->where('b.product', $product_id);
        $this->db->order_by('a.date', 'desc');
        $this->db->limit(30);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }







     //brand 
    public function brand_list()
    {
        return $this->db->select('*')
            ->from('product_brand')
            ->get()
            ->result();
    }


    public function create_brand($data = [])
    {
        return $this->db->insert('product_brand', $data);
    }

  
    public function update_brand($data = [])
    {
        return $this->db->where('brand_id', $data['brand_id'])
            ->update('product_brand', $data);
    }

    public function single_brand_data($id)
    {
        return $this->db->select('*')
            ->from('product_brand')
            ->where('brand_id', $id)
            ->get()
            ->row();
    }

    public function delete_brand($id)
    {

        // $productExists = $this->db->from('product_information')
        //     ->where('category_id', $id)
        //     ->count_all_results();

        // if ($productExists > 0) {
        //     return false;
        // } 
        
        // else {
            // No products linked, proceed to delete the category
           // $this->db->where('brand_id', $id)
            //    ->delete('product_brand');
           // return $this->db->affected_rows() > 0;
        // }

        $productExists = $this->db->from('product_information')
        ->where('brand_id', $id)
        ->count_all_results();

    if ($productExists > 0) {
        return false;
    } else {
        // No products linked, proceed to delete the brand
        $this->db->where('brand_id', $id)
            ->delete('product_brand');
        return $this->db->affected_rows() > 0;
    }
        
    }





     //OOP 
     public function oop_list()
     {
         return $this->db->select('*')
             ->from('product_oop')
             ->get()
             ->result();
     }
 
 
     public function create_oop($data = [])
     {
         return $this->db->insert('product_oop', $data);
     }
 
   
     public function update_oop($data = [])
     {
         return $this->db->where('oop_id', $data['oop_id'])
             ->update('product_oop', $data);
     }
 
     public function single_oop_data($id)
     {
         return $this->db->select('*')
             ->from('product_oop')
             ->where('oop_id', $id)
             ->get()
             ->row();
     }
 
     public function delete_oop($id)
     {
         $productExists = $this->db->from('product_information')
             ->where('oop_id', $id)
             ->count_all_results();

         if ($productExists > 0) {
             return false;
         }

         $this->db->where('oop_id', $id)
             ->delete('product_oop');
         return $this->db->affected_rows() > 0;
     }







     //Subcategory 
     public function subcategory_list()
     {
         return $this->db->select('product_subcategory.*, product_category.category_name')
             ->from('product_subcategory')
             ->join('product_category', 'product_category.category_id = product_subcategory.category_id', 'left')
             ->get()
             ->result();
     }
 
 
     public function create_subcategory($data = [])
     {
         return $this->db->insert('product_subcategory', $data);
     }
 
   
     public function update_subcategory($data = [])
     {
         return $this->db->where('subcategory_id', $data['subcategory_id'])
             ->update('product_subcategory', $data);
     }
 
     public function single_subcategory_data($id)
     {
         return $this->db->select('*')
             ->from('product_subcategory')
             ->where('subcategory_id', $id)
             ->get()
             ->row();
     }
 
     public function delete_subcategory($id = null)
     {
 
         // $productExists = $this->db->from('product_information')
         //     ->where('category_id', $id)
         //     ->count_all_results();
 
         // if ($productExists > 0) {
         //     return false;
         // } 
         
         // else {
             // No products linked, proceed to delete the category
             // $this->db->where('subcategory_id', $id)
             //     ->delete('product_subcategory');
            // return $this->db->affected_rows() > 0;
         // }

         // First check if any products are linked to this subcategory
          $this->db->where('subcategory_id', $id);
          $linked_products = $this->db->count_all_results('product_information');
    
          if ($linked_products > 0) {
            return false; // Products are linked, cannot delete
          }
    
          // If no products are linked, proceed with deletion
          $this->db->where('subcategory_id', $id);
          return $this->db->delete('product_subcategory');
     }


      //Conversion Ratio 
      public function conversionration_list(){
        return $this->db->select('
        conversion_ratio.conversionratio_id,
        conversion_ratio.product,
        conversion_ratio.subunit,
        conversion_ratio.conversion_ratio,
        product_information.product_name,
        product_information.unit as master_unit_id,
        u_master.unit_name as master_unit_name,
        u_sub.unit_name as subunit_name
        ')
        ->from('conversion_ratio')
        ->join('product_information', 'product_information.id = conversion_ratio.product', 'inner')
        ->join('units u_master', 'u_master.unit_id = product_information.unit', 'left')
        ->join('units u_sub', 'u_sub.unit_id = conversion_ratio.subunit', 'left')
        ->get()
        ->result();
      }
  
  
      public function create_conversionratio($data = [])
      {
          return $this->db->insert('conversion_ratio', $data);
      }

      public function check_duplicate_conversionratio($product_id, $subunit, $exclude_id = null)
      {
          $this->db->from('conversion_ratio')
              ->where('product', $product_id)
              ->where('subunit', $subunit)
              ->where('status', 1);

          if (!empty($exclude_id)) {
              $this->db->where('conversionratio_id !=', $exclude_id);
          }

          return $this->db->count_all_results() > 0;
      }

    
      public function update_conversionratio($data = [])
      {
        if($this->check_conversionratio_transactions($data['conversionratio_id'])){
            return false;
        }

        return $this->db->where('conversionratio_id', $data['conversionratio_id'])
              ->update('conversion_ratio', $data);
      }
  
      public function single_conversionratio_data($id)
      {
          return $this->db->select('*')
              ->from('conversion_ratio')
              ->where('conversionratio_id', $id)
              ->get()
              ->row();
      }

      public function check_conversionratio_transactions($conversionratio_id)
    {
        // Get conversion ratio details
        $conversion = $this->db->select('*')
            ->from('conversion_ratio')
            ->where('conversionratio_id', $conversionratio_id)
            ->get()
            ->row();
        
        if (!$conversion) {
            return false;
        }
        
        // Check if this specific conversion ratio has been used in stock_details
        $stock_details_check = $this->db->from('stock_details')
            ->where('conversion_id', $conversionratio_id)
            ->count_all_results();
        if ($stock_details_check > 0) {
            return true;
        }
        
        // Check if this specific conversion ratio has been used in phystock_details
        $phystock_details_check = $this->db->from('phystock_details')
            ->where('conversion_id', $conversionratio_id)
            ->count_all_results();
        if ($phystock_details_check > 0) {
            return true;
        }
        
        // Check if this specific conversion ratio has been used in sale_details
        $sale_details_check = $this->db->from('sale_details')
            ->where('conversion_id', $conversionratio_id)
            ->count_all_results();
        if ($sale_details_check > 0) {
            return true;
        }
        
        return false;
    }
  
      public function delete_conversionratio($id)
      {
  
          // $productExists = $this->db->from('product_information')
          //     ->where('category_id', $id)
          //     ->count_all_results();
  
          // if ($productExists > 0) {
          //     return false;
          // } 
          
          // else {
              // No products linked, proceed to delete the category
              // $this->db->where('conversionratio_id', $id)
              //     ->delete('conversion_ratio');
              // return $this->db->affected_rows() > 0;
          // }

            if ($this->check_conversionratio_transactions($id)) {
                return false; // Cannot delete if transactions exist
            }
            
            $this->db->where('conversionratio_id', $id)
                ->delete('conversion_ratio');
            
            return $this->db->affected_rows() > 0;
      }

        public function getProductListForLabelPrint($postData = null, $category = null)
    {

        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value


        ## Search 
        $searchQuery = "";
        

        if ($searchValue != '') {
            $searchQuery = " (a.product_id like '%" . $searchValue . "%' or a.product_name like '%" . $searchValue . "%' or a.product_model like '%" . $searchValue . "%' or a.price like'%" . $searchValue . "%' ) ";

        }

        if($searchQuery!=""&&$category != null){
            $searchQuery=$searchQuery."AND";
        }else{
            $searchQuery=$searchQuery."";
        }

        if ($category != null) {
            // Only category condition
            $searchQuery = $searchQuery."  a.category_id = " . $category . "";
        } 

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*, 
  ROUND(a.price, 2) as price,
                   a.product_name, 
                   a.product_id, 
                   a.product_model, 
                   a.product_vat, 
                   a.image, 
                    ps.category_name AS category_name
                ");
        $this->db->from('product_information a');
        $this->db->join('product_category ps', 'ps.category_id = a.category_id', 'left');
        

        if ($searchQuery != '')
            $this->db->where($searchQuery);
        $this->db->order_by("a.product_id", "Desc");
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();




        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            $image = '<img src="' . $base_url . $record->image . '" class="img img-responsive" height="50" width="50">';
            $button .= '<span style="color:white" >  aa</span>          <button style="margin-left 40px;" class="btn btn-info btn-xs" 
            onclick=\'openLabelCount(' .  $record->id . ', ' .
                json_encode($record->product_id) . ', ' .
                json_encode($record->product_name) . ', ' .
                json_encode($record->category_name) . ', ' .
                $record->price . ')\'
            data-toggle="tooltip" 
            data-placement="left" 
            title="Add product">
            <i class="fa fa-plus" aria-hidden="true"></i> 
          </button>';


            $data[] = array(
                'sl'               =>  $record->product_id,
                'product_name'     => $record->product_name,
                'product_model'    => $record->product_model,
                'price'            => $record->price,
                'category'         => $record->category_name,
                'image'            => $image,
                'button'           => $button,

            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data
        );
        return $response;
    }

    public function delete_product_group($id)
    {

        $productExists = $this->db->from('sale_group_details')
            ->where('groupid', $id)
            ->count_all_results();

        if ($productExists > 0) {
            return false;
        } else {
            // No products linked, proceed to delete the category
            $this->db->where('id', $id)
            ->delete('product_group');
            $this->db->where('pid', $id)
            ->delete("product_group_details");
            return $this->db->affected_rows() > 0;
        }
    }

    public function BulkProductUpload($postData = null)
    {
        $encryption_key        = Config::$encryption_key;
        $draw                  = $postData['draw'];
        $start                 = $postData['start'];
        $rowperpage            = $postData['length'];
        $columnSortOrder       = $postData['order'][0]['dir'];
        $searchValue           = $postData['search']['value'];

        $searchQuery = '';
        if ($searchValue != '') {
            $searchQuery = " (po.id LIKE '%" . $searchValue . "%'
                OR po.date LIKE '%" . $searchValue . "%'
                OR AES_DECRYPT(po.uploaded_id,'" . $encryption_key . "') LIKE '%" . $searchValue . "%'
                OR si.first_name LIKE '%" . $searchValue . "%'
                OR si.last_name LIKE '%" . $searchValue . "%') ";
        }

        $this->db->select('count(*) as allcount')->from('bulk_product_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecords = $this->db->get()->result()[0]->allcount;

        $this->db->select('count(*) as allcount')->from('bulk_product_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecordwithFilter = $this->db->get()->result()[0]->allcount;

        $this->db->select('po.id,
            AES_DECRYPT(po.uploaded_id,"' . $encryption_key . '") AS uploaded_id,
            po.date, si.first_name, si.last_name, po.product_ids')
            ->from('bulk_product_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by('po.id', 'desc')->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = [];
        $sl   = 1;
        foreach ($records as $record) {
            $button = '<button class="btn btn-success btn-xs" onclick="downloadOriginalCSV(\'product\',' . $record->id . ')" title="Download uploaded CSV"><i class="fa fa-download"></i> CSV</button>';
            $data[]  = [
                'sl'          => $sl,
                'uploaded_id' => $record->uploaded_id,
                'date'        => $record->date,
                'name'        => $record->first_name . ' ' . $record->last_name,
                'button'      => $button,
            ];
            $sl++;
        }

        return [
            'draw'                 => intval($draw),
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData'               => $data,
        ];
    }

    public function BulkConversionratioUpload($postData = null)
    {
        $encryption_key        = Config::$encryption_key;
        $draw                  = $postData['draw'];
        $start                 = $postData['start'];
        $rowperpage            = $postData['length'];
        $columnSortOrder       = $postData['order'][0]['dir'];
        $searchValue           = $postData['search']['value'];

        $searchQuery = '';
        if ($searchValue != '') {
            $searchQuery = " (po.id LIKE '%" . $searchValue . "%'
                OR po.date LIKE '%" . $searchValue . "%'
                OR AES_DECRYPT(po.uploaded_id,'" . $encryption_key . "') LIKE '%" . $searchValue . "%'
                OR si.first_name LIKE '%" . $searchValue . "%'
                OR si.last_name LIKE '%" . $searchValue . "%') ";
        }

        $this->db->select('count(*) as allcount')->from('bulk_conversionratio_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecords = $this->db->get()->result()[0]->allcount;

        $this->db->select('count(*) as allcount')->from('bulk_conversionratio_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecordwithFilter = $this->db->get()->result()[0]->allcount;

        $this->db->select('po.id,
            AES_DECRYPT(po.uploaded_id,"' . $encryption_key . '") AS uploaded_id,
            po.date, si.first_name, si.last_name, po.conversionratio_ids')
            ->from('bulk_conversionratio_details po')
            ->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by('po.id', 'desc')->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = [];
        $sl   = 1;
        foreach ($records as $record) {
            $button = '<button class="btn btn-success btn-xs" onclick="downloadOriginalCSV(\'conversionratio\',' . $record->id . ')" title="Download uploaded CSV"><i class="fa fa-download"></i> CSV</button>';
            $data[]  = [
                'sl'          => $sl,
                'uploaded_id' => $record->uploaded_id,
                'date'        => $record->date,
                'name'        => $record->first_name . ' ' . $record->last_name,
                'button'      => $button,
            ];
            $sl++;
        }

        return [
            'draw'                 => intval($draw),
            'iTotalRecords'        => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordwithFilter,
            'aaData'               => $data,
        ];
    }

    private function _bulkUploadList($postData, $table, $idsCol, $detailsFn, $detailsUrl)
    {
        $encryption_key  = Config::$encryption_key;
        $draw            = $postData['draw'];
        $start           = $postData['start'];
        $rowperpage      = $postData['length'];
        $searchValue     = $postData['search']['value'];
        $searchQuery     = '';
        if ($searchValue != '') {
            $searchQuery = "(po.id LIKE '%" . $searchValue . "%'
                OR po.date LIKE '%" . $searchValue . "%'
                OR AES_DECRYPT(po.uploaded_id,'" . $encryption_key . "') LIKE '%" . $searchValue . "%'
                OR si.first_name LIKE '%" . $searchValue . "%'
                OR si.last_name LIKE '%" . $searchValue . "%')";
        }

        $this->db->select('count(*) as allcount')->from($table . ' po')->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecords = $this->db->get()->result()[0]->allcount;

        $this->db->select('count(*) as allcount')->from($table . ' po')->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $totalRecordwithFilter = $this->db->get()->result()[0]->allcount;

        $this->db->select("po.id, AES_DECRYPT(po.uploaded_id,'" . $encryption_key . "') AS uploaded_id, po.date, si.first_name, si.last_name")
            ->from($table . ' po')->join('users si', 'si.user_id = po.uploadedby', 'left');
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by('po.id', 'desc')->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = [];
        $sl   = 1;
        $csvType = preg_replace('/_details$/', '', preg_replace('/^bulk_/', '', $table));
        foreach ($records as $record) {
            $button = '<button class="btn btn-success btn-xs" onclick="downloadOriginalCSV(\'' . $csvType . '\',' . $record->id . ')" title="Download uploaded CSV"><i class="fa fa-download"></i> CSV</button>';
            $data[] = [
                'sl'          => $sl,
                'uploaded_id' => $record->uploaded_id,
                'date'        => $record->date,
                'name'        => $record->first_name . ' ' . $record->last_name,
                'button'      => $button,
            ];
            $sl++;
        }
        return ['draw' => intval($draw), 'iTotalRecords' => $totalRecords, 'iTotalDisplayRecords' => $totalRecordwithFilter, 'aaData' => $data];
    }

    public function BulkBrandUpload($postData)        { return $this->_bulkUploadList($postData, 'bulk_brand_details',       'brand_ids',         'showBulkBrandDetails',         'get_bulk_brand_details/'); }
    public function BulkCategoryUpload($postData)     { return $this->_bulkUploadList($postData, 'bulk_category_details',    'category_ids',      'showBulkCategoryDetails',      'get_bulk_category_details/'); }
    public function BulkSubcategoryUpload($postData)  { return $this->_bulkUploadList($postData, 'bulk_subcategory_details', 'subcategory_ids',   'showBulkSubcategoryDetails',   'get_bulk_subcategory_details/'); }
    public function BulkUnitUpload($postData)         { return $this->_bulkUploadList($postData, 'bulk_unit_details',        'unit_ids',          'showBulkUnitDetails',          'get_bulk_unit_details/'); }
    public function BulkPaymentMethodUpload($postData){ return $this->_bulkUploadList($postData, 'bulk_paymentmethod_details','paymentmethod_ids','showBulkPaymentMethodDetails', 'get_bulk_paymentmethod_details/'); }
    public function BulkBranchUpload($postData)       { return $this->_bulkUploadList($postData, 'bulk_branch_details',      'branch_ids',        'showBulkBranchDetails',        'get_bulk_branch_details/'); }
    public function BulkStoreUpload($postData)        { return $this->_bulkUploadList($postData, 'bulk_store_details',       'store_ids',         'showBulkStoreDetails',         'get_bulk_store_details/'); }
    public function BulkCustomerUpload($postData)     { return $this->_bulkUploadList($postData, 'bulk_customer_details',    'customer_ids',      'showBulkCustomerDetails',      'get_bulk_customer_details/'); }
    public function BulkSupplierUpload($postData)     { return $this->_bulkUploadList($postData, 'bulk_supplier_details',    'supplier_ids',      'showBulkSupplierDetails',      'get_bulk_supplier_details/'); }
    public function BulkServiceUpload($postData)      { return $this->_bulkUploadList($postData, 'bulk_service_details',     'service_ids',       'showBulkServiceDetails',       'get_bulk_service_details/'); }
    public function BulkProductGroupUpload($postData)    { return $this->_bulkUploadList($postData, 'bulk_productgroup_details',   'productgroup_ids',   'showBulkProductGroupDetails',   'get_bulk_productgroup_details/'); }
    public function BulkOpeningStockUpload($postData)    { return $this->_bulkUploadList($postData, 'bulk_openingstock_details',   'openingstock_ids',   'showBulkOpeningStockDetails',   'get_bulk_openingstock_details/'); }
    public function BulkStockBatchUpload($postData)         { return $this->_bulkUploadList($postData, 'bulk_stockbatch_details',       'stockbatch_ids',       'showBulkStockBatchDetails',       'get_bulk_stockbatch_details/'); }
    public function BulkStockAdjustmentUpload($postData)    { return $this->_bulkUploadList($postData, 'bulk_stockadjustment_details', 'stockadjustment_ids', 'showBulkStockAdjustmentDetails', 'get_bulk_stockadjustment_details/'); }

    public function all_productgroup_codes() {
        return $this->db->select('groupcode')->from('product_group')->get()->result_array();
    }

    public function all_stockbatches_for_csv() {
        $enc = Config::$encryption_key;
        return $this->db->query("SELECT id, AES_DECRYPT(batchid,'$enc') AS batchid, busage, product FROM stockbatch WHERE status=1 ORDER BY id ASC")->result_array();
    }

    public function all_stockbatch_batchids_for_csv() {
        $enc = Config::$encryption_key;
        return $this->db->query("SELECT id, AES_DECRYPT(batchid,'$enc') AS batchid FROM stockbatch ORDER BY id ASC")->result_array();
    }

    public function all_conversion_ratios_for_csv() {
        return $this->db->select('conversionratio_id, product, subunit, convertiontype, conversion_ratio')
            ->from('conversion_ratio')
            ->where('status', 1)
            ->get()->result_array();
    }

    public function all_designation_names_for_csv() {
        return $this->db->select('id, designation')->from('designation')->get()->result_array();
    }

    public function all_designations_for_csv() {
        return $this->db->select('id, designation')->from('designation')->where('status', 1)->get()->result_array();
    }

    public function all_employee_ids_for_csv() {
        return $this->db->select('id, last_name')->from('employee_history')->get()->result_array();
    }

    public function BulkDesignationUpload($postData) { return $this->_bulkUploadList($postData, 'bulk_designation_details', 'designation_ids', 'showBulkDesignationDetails', 'get_bulk_designation_details/'); }
    public function BulkEmployeeUpload($postData)    { return $this->_bulkUploadList($postData, 'bulk_employee_details',    'employee_ids',    'showBulkEmployeeDetails',    'get_bulk_employee_details/'); }

    public function search_products_for_barcode($search = '', $category = null) {
        $encryption_key = Config::$encryption_key;
        $this->db->select("
            a.id,
            a.product_id,
            a.product_name,
            pc.category_name,
            CAST(AES_DECRYPT(a.price, '{$encryption_key}') AS CHAR) AS price
        ");
        $this->db->from('product_information a');
        $this->db->join('product_category pc', 'pc.category_id = a.category_id', 'left');
        if (!empty($search)) {
            $s = $this->db->escape_str($search);
            $this->db->where("(a.product_id LIKE '%{$s}%' OR a.product_name LIKE '%{$s}%')");
        }
        if (!empty($category)) {
            $this->db->where('a.category_id', (int)$category);
        }
        $this->db->order_by('a.product_name', 'ASC');
        $this->db->limit(20);
        return $this->db->get()->result_array();
    }

    public function get_product_batches($product_id, $search = '') {
        $encryption_key = Config::$encryption_key;
        $pid = (int)$product_id;
        $sql = "SELECT id,
                    CAST(AES_DECRYPT(batchid, '{$encryption_key}') AS CHAR) AS batch_name,
                    busage, edate
                FROM stockbatch
                WHERE status = 1
                AND (product = {$pid} OR (product = 0 AND busage = 'multiple'))
                AND (edate IS NULL OR edate = '' OR edate >= CURDATE())";
        if (!empty($search)) {
            $s = $this->db->escape_str($search);
            $sql .= " HAVING batch_name LIKE '%{$s}%'";
        }
        $sql .= " ORDER BY busage DESC, id ASC LIMIT 10";
        return $this->db->query($sql)->result_array();
    }
}