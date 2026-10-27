<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Stock extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'stock_model',
            'unitcost_model',
            'store/store_model',
            'product/product_model',
            'service/service_model',
            'purchase/purchase_model',

        ));

        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }




    // stock batch part
    public function bdtask_stockbatchlist()
    {
        if (!$this->permission1->method('stockbatchlist', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']      = display('stockbatchlist');
        $data['module']     = "stock";
        $data['page']       = "stockbatch_list";
        echo modules::run('template/layout', $data);
    }


    public function checkStockBatchList()
{
    try {
        $postData = $this->input->post();
        $data = $this->stock_model->stockbatchlist($postData);
        echo json_encode($data);
    } catch (Exception $e) {
        // Log the error
        log_message('error', 'StockBatch List Error: ' . $e->getMessage());
        // Return error to DataTables
        echo json_encode([
            'error' => $e->getMessage(),
            'draw' => $this->input->post('draw'),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
    }
}


    public function bdtask_stockbatch_form($id = null)
{
    $data['title'] = display('add_stockbatch');
    #-------------------------------#
    $this->form_validation->set_rules('batchid', "Batch Id", 'required');
    $this->form_validation->set_rules('busage', "Batch Usage", 'required');
    $this->form_validation->set_rules('status', "Status", 'required');
    $batchid = $this->input->post('batchid', true);
    $details = $this->input->post('details', true);
    $busage = $this->input->post('busage', true);
    $status  = $this->input->post('status', true);

    $edate_toggle = $this->input->post('edate_toggle', true);
    $edate_enabled = ($edate_toggle === 'yes') ? 1 : 0;

    if ($busage == "single") {
        $this->form_validation->set_rules('product', "product", 'required');
        $product = $this->input->post('product', true);
        $mdate = $this->input->post('mdate', true);
        $pdate = $this->input->post('pdate', true);
        $edate  = ($edate_enabled == 1) ? $this->input->post('edate', true) : '';
        $mrp  = $this->input->post('mrp', true);
    } else {
        $product = $this->input->post('product1', true);
        $mdate = $this->input->post('mdate1', true);
        $pdate = $this->input->post('pdate1', true);
        $edate  = ($edate_enabled == 1) ? $this->input->post('edate1', true) : '';
        $mrp  = $this->input->post('mrp1', true);
    }

    $encryption_key = Config::$encryption_key;
    $base_url = base_url();

    if (!$id) {
        if (!$this->permission1->method('add_stockbatch', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    } else {
        if (!$this->permission1->method('add_stockbatch', 'update')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
    }

    #-------------------------------#
    if ($this->form_validation->run() === true) {

        #if empty $id then insert data
        if (empty($id)) {

            $query = "
                INSERT INTO stockbatch
                (id, batchid, details, status,opening,busage,product,mdate,pdate,edate,mrp,edate_enabled)
                VALUES
                (
                  '{$id}',
                   AES_ENCRYPT('{$batchid}', '{$encryption_key}'),
                 '{$details}',
                 '{$status}',
                 0,
                  '{$busage}',
                  '{$product}',
                  '{$mdate}',
                  '{$pdate}',
                  '{$edate}',
                  AES_ENCRYPT('{$mrp}', '{$encryption_key}'),
                  '{$edate_enabled}'
                );";
            if ($this->db->query($query)) {
                if ($this->input->post('button', true) === "add-another") {
                    echo '
                <script type="text/javascript">
                alert("Batch Details Saved successfully");
                window.location.href = "' . $base_url . 'stockbatch_form";
               </script>';
                } else {
                    echo '
                <script type="text/javascript">
                alert("Batch Details Saved successfully");
                window.location.href = "' . $base_url . 'stockbatchlist";
               </script>';
                }
            }
        } else {
            $query = "
UPDATE stockbatch
SET
    batchid = AES_ENCRYPT('{$batchid}', '{$encryption_key}'),
    details = '{$details}',
    status  = '{$status}',
    busage  = '{$busage}',
    mrp = AES_ENCRYPT('{$mrp}', '{$encryption_key}'),
    pdate = '{$pdate}',
    mdate  = '{$mdate}',
    edate  = '{$edate}',
    edate_enabled = '{$edate_enabled}',
    product  = '{$product}'
WHERE id = '{$id}';
";
            if ($this->db->query($query)) {
                if ($this->input->post('button', true) === "add-another") {
                    echo '
                <script type="text/javascript">
                alert("Batch Details Updated successfully");
                window.location.href = "' . $base_url . 'stockbatch_form";
               </script>';
                } else {
                    echo '
                <script type="text/javascript">
                alert("Batch Details Updated successfully");
                window.location.href = "' . $base_url . 'stockbatchlist";
               </script>';
                }
            }
        }
    } else {
        // Prepare data for view
        if (!empty($id)) {
            $data['title'] = "Edit Stock Batch";
            $data['stockbatch'] = $this->stock_model->single_stockbatch_data($id);
            
            // Check if batch has transactions
            $data['has_transactions'] = $this->stock_model->check_batch_has_transactions($id);
        } else {
            $data['stockbatch'] = (object) [
                'id' => null,
                'batchid' => null,
                'details' => null,
                'busage' => null,
                'product' => null,
                'mdate' => null,
                'edate' => null,
                'edate_enabled' => 0,
                'pdate' => null,
                'mrp' => null,
                'status' => 1,
                'status_label' => 'Active'
            ];
            $data['has_transactions'] = false;
        }
        
        $data['products'] = $this->singleusage_product();
        $data['module'] = "stock";
        $data['page'] = "stockbatch_form";
        echo Modules::run('template/layout', $data);
    }
}

    public function getStockBatchById()
    {
        $encryption_key = Config::$encryption_key;
        $batchid    = $this->input->post('batchid', true);
        $product    = (int) $this->input->post('product', true);
        $exclude_id = (int) $this->input->post('exclude_id', true);

        $this->db->select('id');
        $this->db->from('stockbatch a');
        $this->db->where("a.batchid = AES_ENCRYPT('$batchid', '$encryption_key')", null, false);
        $this->db->where('a.product', $product);
        if ($exclude_id > 0) {
            $this->db->where('a.id !=', $exclude_id);
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode("not success");
        } else {
            echo json_encode("success");
        }
    }

    public function save_stockbatch_ajax()
    {
        if (!$this->permission1->method('add_stockbatch', 'create')->access()) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $batchid       = $this->input->post('batchid', true);
        $details       = $this->input->post('details', true);
        $busage        = $this->input->post('busage', true);
        $status        = $this->input->post('status', true);
        $edate_toggle  = $this->input->post('edate_toggle', true);
        $edate_enabled = ($edate_toggle === 'yes') ? 1 : 0;

        if (empty($batchid) || empty($busage) || $status === '' || $status === false) {
            echo json_encode(['success' => false, 'message' => 'Required fields missing']);
            return;
        }

        $encryption_key = Config::$encryption_key;

        // Check duplicate batchid + product combination
        $product = ($busage === 'single') ? ($this->input->post('product', true) ?: 0) : 0;
        $this->db->select('id');
        $this->db->from('stockbatch');
        $this->db->where("batchid = AES_ENCRYPT('$batchid', '$encryption_key')", null, false);
        $this->db->where('product', $product);
        if ($this->db->get()->num_rows() > 0) {
            echo json_encode(['success' => false, 'message' => 'Batch ID already exists for this product']);
            return;
        }

        if ($busage === 'single') {
            $mdate   = $this->input->post('mdate', true) ?: '';
            $pdate   = $this->input->post('pdate', true) ?: '';
            $edate   = ($edate_enabled == 1) ? ($this->input->post('edate', true) ?: '') : '';
            $mrp     = $this->input->post('mrp', true) ?: 0;
        } else {
            $product = 0; $mdate = ''; $pdate = ''; $edate = ''; $mrp = 0;
        }

        $query = "INSERT INTO stockbatch (batchid, details, status, opening, busage, product, mdate, pdate, edate, mrp, edate_enabled)
                  VALUES (
                    AES_ENCRYPT('{$batchid}', '{$encryption_key}'),
                    '{$details}', '{$status}', 0, '{$busage}', '{$product}',
                    '{$mdate}', '{$pdate}', '{$edate}',
                    AES_ENCRYPT('{$mrp}', '{$encryption_key}'),
                    '{$edate_enabled}'
                  )";

        if ($this->db->query($query)) {
            $new_id = $this->db->insert_id();
            echo json_encode([
                'success' => true,
                'id'      => $new_id,
                'batchid' => $batchid,
                'product' => (int)$product,
                'busage'  => $busage
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    public function check_duplicate_stockbatch()
    {
        $encryption_key = Config::$encryption_key;
        $value = trim($this->input->post('value', FALSE));
        $exclude_id = $this->input->post('exclude_id', TRUE);
        if ($value === '') { echo json_encode(['duplicate' => false]); return; }
        $value_safe = $this->db->escape_str($value);
        $sql = "SELECT id FROM stockbatch WHERE AES_DECRYPT(batchid, '{$encryption_key}') = '{$value_safe}'";
        if (!empty($exclude_id)) {
            $sql .= " AND id != '" . $this->db->escape_str($exclude_id) . "'";
        }
        $sql .= " LIMIT 1";
        $row = $this->db->query($sql)->row();
        echo json_encode(['duplicate' => (bool)$row, 'message' => $row ? 'A stock batch with this Batch Id already exists.' : '']);
    }

    public function bdtask_deletestockbatch($id = null)
    {

        $base_url = base_url();

    $delete_result = $this->stock_model->delete_stockbatch($id);
    
    if ($delete_result === 'referenced') {
        // Batch is referenced in transactions, cannot delete
        echo '<script type="text/javascript">
            alert("Cannot delete this stock batch because it is being used in stock transactions (stock_details or phystock_details). Please remove all related transactions first.");
            window.location.href = "' . $base_url . 'stockbatchlist";
        </script>';
    } else if ($delete_result === true) {
        // Successfully deleted
        echo '<script type="text/javascript">
            alert("Deleted successfully");
            window.location.href = "' . $base_url . 'stockbatchlist";
        </script>';
    } else {
        // Error occurred
        echo '<script type="text/javascript">
            alert("Something went wrong. The stock batch could not be deleted.");
            window.location.href = "' . $base_url . 'stockbatchlist";
        </script>';
    }
    
    }










    //Opening stock part


    public function bdtask_openingstockbatchlist()
    {
        $data['title']      = display('openingstockbatchlist');
        $data['module']     = "stock";
        $data['page']       = "openingstock_list";
        echo modules::run('template/layout', $data);
    }


    public function checkopeningstockbatchList()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->openingstockbatchlist($postData);
        echo json_encode($data);
    }

    public function bdtask_openingstock_form($id = null)
    {
        $data = array(
            'title'         => display('add_openingstock'),
        );
        $data["batches"] = $this->active_batch();
        $data['products'] = $this->active_product();
        $data['floor_list'] = $this->active_floor();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "openingstock_form";

        if ($id != null) {

            $data['title'] = "Edit Opening Stock";
        }
        echo modules::run('template/layout', $data);
    }

    public function active_batch()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('id, AES_DECRYPT(batchid , "' . $encryption_key . '") AS batchid,opening,busage');
        $this->db->from('stockbatch');
        //   $this->db->where('status', 1);
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
        $this->db->where('stock', 1);
        $this->db->where_not_in('id', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function singleusage_product()
    {
        $this->db->select('id,product_name');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $this->db->where('stock', 1);
        $this->db->where_in('batchtype', [1, 3]);


        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
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

    public function active_batches()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('id, AES_DECRYPT(batchid , "' . $encryption_key . '") AS batchid,opening');
        $this->db->from('stockbatch');
        $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function stocktype($id = null)
    {
        $this->db->select('stocktype,type');
        $this->db->from('adj_stock');
        $this->db->where('id', $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    public function active_productbyfloorandstore()
    {
        $this->db->select('pi.id, pi.product_name, sd.floor, sd.store');
        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.status', 1);
        $this->db->group_by('pi.id, sd.floor, sd.store');
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



    public function getProductById()
    {
        $this->db->select('*');
        $this->db->from('product_information a');
        $this->db->where('a.id', $this->input->post('productid'));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }



    public function getOpeningStockById()
    {

        $encryption_key = Config::$encryption_key;

        $this->db->select('sb.batchid,b.batch_id, b.product, b.store, b.floor, a.date AS date, a.details AS details, 
        AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock, pi.unit, 
    (SELECT   SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('ope_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('stockbatch sb', 'sb.id = b.batch_id');

        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'opening_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }

        return false;
    }

    public function save_openingstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;



        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'details' => $items[0]['details']
        );


        $this->db->insert('ope_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {


            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['store']}', 
     '{$item['floor']}', 
     AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
      AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
    AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
     'opening_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);
        }

        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }


    public function update_openingstock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'opening_stock')
            ->delete('stock_details');

        foreach ($items as $item) {
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
             'opening_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }

        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }

    public function delete_opetock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'opening_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('opening_stock');

        redirect("openingstockbatchlist");
    }



    //Stock Disposal part

    public function bdtask_stockdisposalnote_form($id = null)
    {
        $data = array(
            'title'         => display('new_stockdisposalnote'),
        );
        $data["batches"] = $this->active_batches();
        $data['products'] = $this->active_product();
        $data['floor_list'] = $this->active_floor();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "stockdisposalnote_form";

        if ($id != null) {

            $data['title'] = "Edit Stock Disposal Note";
        }
        echo modules::run('template/layout', $data);
    }

    // public function active_batches()
    // {
    //     $this->db->select('*');
    //     $this->db->from('stockbatch');
    //     $this->db->where('status', 1);
    //     $query = $this->db->get();
    //     if ($query->num_rows() > 0) {
    //         return $query->result_array();
    //     }
    //     return false;
    // }


    public function stock_batchdetailsbyprodid()
    {
        $this->db->select('sb.id, sb.batchid, pi.product_name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sb.status', 1);

        $this->db->group_by('sb.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function stock_batchdetailsbyprodidandstorefloor()
    {
        $this->db->select('sb.id, sb.batchid, pi.product_name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sd.store', $this->input->post('store', TRUE));
        $this->db->where('sd.floor', $this->input->post('floor', TRUE));
        $this->db->where('sb.status', 1);

        $this->db->group_by('sb.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function store_detailsbybatchidandprodid()
    {
        $this->db->select('s.id, s.name,pi.unit');
        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        // $this->db->where('sb.id', $this->input->post('batchid', TRUE));
        $this->db->where('s.status', 1);
        $this->db->group_by('s.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getproduct()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("pi.product_id,
    pi.product_name,pi.defaultsaleprice,
    pi.category_id,
    pi.product_vat,
    pi.serial_no,
    AES_DECRYPT(pi.price, '{$encryption_key}') AS price,
    pi.product_model,
    AES_DECRYPT(pi.cost_price, '{$encryption_key}') AS cost_price,
    pi.product_details,pi.ad,pi.bd,pi.addigit,pi.batchtype,
    pi.store,u.unit_id,u.unit_name,s.dstock,pi.stock");
        $this->db->from('product_information pi');
        $this->db->join('units u', 'u.unit_id = pi.unit', 'inner');
        $this->db->join('store s', 's.id = pi.store', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getproductSubUnitPrimary()
    {

        $this->db->select("sp.unit_id AS unit2,cr2.conversion_ratio,u.unit_name,u2.unit_name as unit2");
        $this->db->from('product_information pi');
        $this->db->join('units u2', 'u2.unit_id = pi.unit', 'inner');
        $this->db->join('subunit_product sp', 'sp.product_id = pi.id', 'left');
        $this->db->join('units u', 'u.unit_id = sp.unit_id', 'inner');
        $this->db->join('conversion_ratio cr2', 'sp.product_id = cr2.product', 'left');
        $this->db->where('sp.first', 1);
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('cr2.subunit = sp.unit_id');

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo json_encode(null);
        }
    }

    public function addStockbatchOpening()
    {
        $encryption_key = Config::$encryption_key;
        $query = "
            INSERT INTO stockbatchopening 
            (batch,product,mdate,edate,mrp,pdate ) 
            VALUES 
            (
             '{$this->input->post('batch', TRUE)}', 
             '{$this->input->post('prodid', TRUE)}', 
             '{$this->input->post('mdate', TRUE)}', 
             '{$this->input->post('edate', TRUE)}', 
             AES_ENCRYPT('{$this->input->post('mrp', TRUE)}', '{$encryption_key}'), 
              '{$this->input->post('pdate', TRUE)}'
            );
        ";
        $this->db->query($query);
        echo json_encode("Success");
    }

    public function getproductBatchCount()
    {
        $this->db->select("*");
        $this->db->from('stockbatchopening soa');
        $this->db->where('soa.product', $this->input->post('prodid', TRUE));
        $this->db->where('soa.batch', $this->input->post('batch', TRUE));
        $query = $this->db->get();
        echo json_encode($query->num_rows());
    }



    public function getVoucherNo()
    {
        $encryption_key = Config::$encryption_key;

        if( $this->input->post('type', TRUE)=="purchase"){
            $this->db->select('p.id,AES_DECRYPT( p.chalan_no , "' . $encryption_key . '")  as voucherno,AES_DECRYPT(si.supplier_name, "' . $encryption_key . '")  as supplier_name');
            $this->db->from('purchase_details pd');
            $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
            $this->db->where('s.id', $this->input->post('store', TRUE));
            $this->db->where('p.status', 0);
    
            $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
    
            $this->db->group_by('voucherno');

        }

        if( $this->input->post('type', TRUE)=="salesreturn"){
            $this->db->select('p.id,AES_DECRYPT( p.sales_return_id , "' . $encryption_key . '")  as voucherno');
            $this->db->from('sales_return_details pd');
            $this->db->join('sales_return p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.rstore', 'inner');
            $this->db->where('s.id', $this->input->post('store', TRUE));
            $this->db->where('p.status', 0);
    
            $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
    
            $this->db->group_by('voucherno');

        }

       
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }




    public function floor_detailsbybatchidprodidandstoreid()
    {
        $this->db->select('f.id, f.name');
        $this->db->from('stock_details sd');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        $this->db->join('floor f', 'f.id = sd.floor', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sb.id', $this->input->post('batchid', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        $this->db->where('f.status', 1);
        $this->db->group_by('f.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function avg_avstock()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('sum(AES_DECRYPT( sd.stock , "' . $encryption_key . '"))  as avgqty,pi.stock');
        $this->db->from('stock_details sd');
        // $this->db->join('stockbatch sb', 'sb.id = sd.batch', 'inner');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        // $this->db->join('floor f', 'f.id = sd.floor', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        // $this->db->where("AES_DECRYPT(sd.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        $this->db->where('sd.batch', $this->input->post('batch', TRUE));

        // $this->db->where('f.id', $this->input->post('floorid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function avg_avstockFormaster()
    {
        $encryption_key = Config::$encryption_key;



        $this->db->select("
    SUM(
        CASE
            WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '+' 
                THEN CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(10,2)) + 20
            WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '-' 
                THEN CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(10,2)) - 20
            WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '*' 
                THEN CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(10,2)) * 20
            WHEN sd.conversion_id IS NOT NULL AND cr2.convertiontype = '/' 
                THEN CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(10,2)) / 20
            ELSE CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(10,2))
        END
    ) AS adjusted_stock,
    sd.conversion_id,
    cr2.conversion_ratio
");

        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('conversion_ratio cr2', 'cr2.conversionratio_id = sd.conversion_id', 'left');
        $this->db->join('store s', 's.id = sd.store', 'inner');

        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));
        $this->db->where('sd.batch', $this->input->post('batch', TRUE));
        $this->db->group_by('sd.conversion_id');

        // $this->db->where('f.id', $this->input->post('floorid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function save_damstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;


        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'reason' => $items[0]['reason']
        );


        $this->db->insert('dam_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $quantity = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'dam_stock', 
             '{$inserted_id}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");
    }


    public function update_damstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'dam_stock')
            ->delete('stock_details');
        foreach ($items as $item) {

            $quantity = -$item['quantity'];

            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['store']}', 
             '{$item['floor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('{0}', '{$encryption_key}'),  
             'dam_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");


        $this->db->where('id', $items[0]['batch'])
            ->update('stockbatch', ['opening_stock_used' => "yes"]);

        echo json_encode("Success");
    }

    public function delete_damstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'dam_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('dam_stock');

        redirect("manage_stockdisposalnote");
    }

    public function bdtask_manage_stockdisposalnote()
    {
        $data['title']      = display('manage_stockdisposalnote');
        $data['module']     = "stock";
        $data['page']       = "manage_stockdisposalnote";
        echo modules::run('template/layout', $data);
    }

    public function checkdamstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->damagestock($postData);
        echo json_encode($data);
    }


    public function getDamageStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('b.batch_id, b.product, b.store, b.floor, a.date AS date, a.reason AS reason,AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock , pi.unit, 
    (SELECT   SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('dam_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'dam_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }


    //Stock Adjustment part

    public function bdtask_newstockadjustment_form($id = null)
    {
        $data = array(
            'title'         => display('new_stock_adjustment'),
        );
        $data['products'] = $this->active_product();
        $data["batches"] = $this->active_batch();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data['stockbatchopening'] = $this->stockbatchopening();
        $data['units'] = $this->active_units();
        $data['modal_products'] = $this->singleusage_product();


        $data['id'] = $id;
        $data['type'] = null;

        $data['module']      = 'stock';
        $data['page']    = "newstockadjustment_form";

        if (!$this->permission1->method('manage_stock_adjustment', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        if ($id != null) {
            $data['type'] = $this->stocktype($id);

            $data['title'] = "Edit Stock Adjustment";
        }
        echo modules::run('template/layout', $data);
    }

    public function bdtask_new_stock_form($id = null)
    {
        $data = array(
            'title' => 'New Inventory Transection',
        );
        $data['products'] = $this->active_product();
        $data["batches"] = $this->active_batch();
        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data['stockbatchopening'] = $this->stockbatchopening();
        $data['units'] = $this->active_units();
        $data['modal_products'] = $this->singleusage_product();
        $data['category_list'] = $this->product_model->active_category();
        $data['unit_list']     = $this->product_model->active_unit();
        $data['all_supplier']  = $this->purchase_model->supplier_list();

        $data['id'] = $id;
        $data['type'] = null;
        $data['source_request_no'] = null;

        $data['module'] = 'stock';
        $data['page']   = "new_inventory_transection";

        if (!$id) {
            if (!$this->permission1->method('new_inventory_transection', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('new_inventory_transection', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        if ($id != null) {
            $data['type']  = $this->stocktype($id);
            $data['title'] = "Edit New Inventory Transection";

            // If this transaction was converted from an Inventory Request,
            // show which one — read-only, editing doesn't change the link.
            $encryption_key = Config::$encryption_key;
            $source = $this->db->query("
                SELECT AES_DECRYPT(ir.request_no, '{$encryption_key}') AS request_no
                FROM adj_stock a
                INNER JOIN inventory_request ir ON ir.id = a.inventory_request_no
                WHERE a.id = {$id} AND a.inventory_request_no > 0
            ")->row();
            if ($source) {
                $data['source_request_no'] = $source->request_no;
            }
        }
        echo modules::run('template/layout', $data);
    }

    public function stockbatchopening()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select('id, batch,product');
        $this->db->from('stockbatchopening');
        //   $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    // Same voucher-number convention as Customer Receive/Supplier Payment/
    // Inventory Request: a 10-digit series per type (A starts at
    // 0000000001, B at 1000000001), next number = MAX(existing in that
    // type's series) + 1.
    private function _next_inventory_id($type2)
    {
        $encryption_key = Config::$encryption_key;
        $series_floor = ($type2 === 'B') ? 1000000000 : 0;
        $max_row = $this->db->query("
            SELECT MAX(CAST(SUBSTRING_INDEX(AES_DECRYPT(inventory_id, '{$encryption_key}'), '-', -1) AS UNSIGNED)) AS max_no
            FROM adj_stock
            WHERE inventory_id IS NOT NULL AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
        ")->row();
        $max_no = $max_row ? $max_row->max_no : null;
        $next_no = ($max_no && $max_no > $series_floor) ? ($max_no + 1) : ($series_floor + 1);
        return 'INV-' . str_pad($next_no, 10, '0', STR_PAD_LEFT);
    }

    // Unit Cost Logic Specification v1.1 — preview for Stock Disposal / Stock Adjustment
    // (Actual) rows: the cost these lines will be valued at on save (UCL-42, UCL-43). Read-only
    // on the form; the backend recomputes this independently at save time rather than trusting
    // whatever the page last fetched.
    public function getCurrentUnitCost()
    {
        $product = (int) $this->input->post('product', TRUE);
        $batch   = (int) $this->input->post('batch', TRUE);

        if (!$product || !$batch) {
            echo json_encode(['cost' => 0]);
            return;
        }

        echo json_encode(['cost' => $this->_current_cost_or_fallback($product, $batch)]);
    }

    // Unit Cost Logic Specification v1.1 (Phase 2) — costs the Actual-stock stock_details row
    // just inserted for an Opening Stock / Stock Adjustment (Actual) / Stock Disposal line, and
    // writes the result back onto that row. Store Transfer never reaches this method's callers
    // and has no cost effect either way (UCL-44) — guarded here too for safety.
    private function _record_inventory_cost($item, $type, $date, $voucher_id, $store)
    {
        if ($type === 'storetransfer') {
            return;
        }

        $stock_details_id = $this->db->insert_id();
        $qty = (float) $item['quantity'];
        $is_increase = ($item['adj'] == 'increase');

        if ($type === 'openingstock') {
            // UCL-40: user-entered Unit Cost, per the line's selected unit. The posted
            // linevalue is qty-in-display-unit x unitcost-in-display-unit, computed client
            // side — kept as-is per UCL-11 ("line value is never converted, only quantity").
            $line_cost  = isset($item['unitcost']) ? (float) $item['unitcost'] : 0.0;
            $line_value = (isset($item['linevalue']) && (float) $item['linevalue'] > 0)
                ? (float) $item['linevalue']
                : $qty * $line_cost;

            if ($line_cost <= 0 || $line_value <= 0) {
                throw new Exception('Unit Cost is required for Opening Stock and must be greater than 0.');
            }

            $args = [
                'product' => $item['product'], 'batch' => $item['batch'], 'date' => $date,
                'source_type' => 'opening_stock', 'source_no' => $voucher_id, 'line_id' => $stock_details_id,
                'store' => $store, 'qty_in' => $qty, 'value_in' => $line_value,
                'reset_on_purchase' => true,
            ];
        } else {
            // Stock Disposal (always a decrease) / Stock Adjustment Actual (increase or
            // decrease) — valued at the current cost, never typed (UCL-42, UCL-43).
            $line_cost  = $this->_current_cost_or_fallback($item['product'], $item['batch']);
            $line_value = $qty * $line_cost;
            $source_type = ($type === 'stockdisposal') ? 'disposal' : ($is_increase ? 'adj_increase' : 'adj_decrease');

            $args = [
                'product' => $item['product'], 'batch' => $item['batch'], 'date' => $date,
                'source_type' => $source_type, 'source_no' => $voucher_id, 'line_id' => $stock_details_id,
                'store' => $store,
            ];
            if ($is_increase) {
                $args['qty_in']  = $qty;
                $args['value_in'] = $line_value;
            } else {
                $args['qty_out']  = $qty;
                $args['value_out'] = $line_value;
            }
        }

        $result = $this->unitcost_model->record($args);

        // unit_cost / value follow this codebase's AES_ENCRYPT convention for money columns
        // (same as stock_details.stock) — raw query builder ->update() with an array would store
        // the plain literal, so these two are set via set(..., FALSE) to inject the SQL function.
        $encryption_key = Config::$encryption_key;
        $this->db->set('unit_cost', "AES_ENCRYPT('" . number_format((float) $line_cost, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('value', "AES_ENCRYPT('" . number_format((float) $line_value, 6, '.', '') . "', '{$encryption_key}')", FALSE);
        $this->db->set('unit_cost_id', $result['unit_cost_id']);
        $this->db->where('id', $stock_details_id)->update('stock_details');

        // Phase 5 / back-dated entry (UCL-50): this line's date may be earlier than movements of
        // the same Product+Batch already costed — record() above always blends against the
        // globally-latest state, which is wrong in that case. Detect it and immediately replay
        // the whole chain from this date forward so everything recomputes in correct order.
        if ($this->unitcost_model->has_later_movements($item['product'], $item['batch'], $date)) {
            $this->unitcost_model->rerun_chain($item['product'], $item['batch'], $date);
        }
    }

    // Unit Cost Logic Specification v1.1 — UCL-38, UCL-42: the current (last) unit cost of a
    // Product + Batch, or the product's Fixed Purchase Price when the batch has never been
    // costed yet. Used wherever a movement is valued "at current cost" without the user typing
    // one (Stock Disposal, Stock Adjustment Actual, and negative-stock sales).
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
    // Product+Batch pair in $pairs. Same helper as Purchase::_rerun_affected_pairs(), duplicated
    // for the same reason as _current_cost_or_fallback() above.
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

    public function save_adjstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');

        $lastupdate = date('Y-m-d H:i:s');
        $inventory_request_id = (int)$this->input->post('inventory_request_id', TRUE);

        $this->db->trans_begin();
        try {

        $inventory_id = $this->_next_inventory_id($items[0]['type2']);

        $this->db->query("
            INSERT INTO adj_stock (inventory_id, date, reason, type2, stocktype, type, inventory_request_no)
            VALUES (
                AES_ENCRYPT('{$inventory_id}', '{$encryption_key}'),
                " . $this->db->escape($items[0]['date']) . ",
                " . $this->db->escape($items[0]['reason']) . ",
                AES_ENCRYPT(" . $this->db->escape($items[0]['type2']) . ", '{$encryption_key}'),
                " . $this->db->escape($items[0]['stocktype']) . ",
                " . $this->db->escape($items[0]['type']) . ",
                " . $this->db->escape($inventory_request_id) . "
            )
        ");

        $inserted_id = $this->db->insert_id();

        if ($inventory_request_id > 0) {
            $this->db->query("UPDATE inventory_request SET status = 1 WHERE id = " . $this->db->escape($inventory_request_id));
        }

        foreach ($items as $item) {
    
            $quantity = ($item["adj"] == "increase") 
                ? $item['quantity'] 
                : -$item['quantity'];
    
            $qtystr = ($item["adj"] == "increase") 
                ? $item['aqty'] 
                : "-" . $item['aqty'];
    
            $insertStock = function($table) use ($item, $quantity, $inserted_id, $items, $encryption_key) {
                return "
                    INSERT INTO {$table}
                    (product, batch, store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($item['store']) . ",
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
                        'adj_stock',
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )
                ";
            };
    
            $insertAuditphy = function() use ($item, $items, $qtystr, $quantity, $inserted_id, $lastupdate, $encryption_key) {
                return "
                    INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        " . $this->db->escape($items[0]['type']) . ",
            
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($inserted_id) . ", '{$encryption_key}'),
            
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($item['store']) . ",
            
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
            
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
            
                        " . $this->db->escape($lastupdate) . ",
                           AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')

                    )
                ";
            };
            $insertAuditac = function() use ($item, $items, $qtystr, $quantity, $inserted_id, $lastupdate, $encryption_key) {
                return "
                    INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        " . $this->db->escape($items[0]['type']) . ",
            
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($inserted_id) . ", '{$encryption_key}'),
            
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($item['store']) . ",
            
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
            
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
            
                        " . $this->db->escape($lastupdate) . ",
                                         AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    )
                ";
            };
    
            if ($items[0]['stocktype'] == "actualstock") {

                $this->db->query($insertStock("stock_details"));
                $this->_record_inventory_cost($item, $items[0]['type'], $items[0]['date'], $inserted_id, $item['store']);
                $this->db->query($insertAuditac());


                if (in_array($items[0]['type'], ["storetransfer", "stockdisposal"])) {

                    $store = $this->db->select(
                        $item["adj"] == "increase" ? "auto_grn" : "auto_gdn"
                    )->from('store')->where('id', $item['store'])->get()->row();

                    $flag = ($item["adj"] == "increase") ? $store->auto_grn : $store->auto_gdn;

                    if ($flag == 0) {
                        $this->db->query($insertStock("phystock_details"));
                        $this->db->query($insertAuditphy());
                    }
                }

            } elseif ($items[0]['stocktype'] == "physicalstock") {

                $this->db->query($insertStock("phystock_details"));
                $this->db->query($insertAuditphy());

            } else {

                $this->db->query($insertStock("stock_details"));
                $this->_record_inventory_cost($item, $items[0]['type'], $items[0]['date'], $inserted_id, $item['store']);
                $this->db->query($insertAuditac());

                $this->db->query($insertStock("phystock_details"));
                $this->db->query($insertAuditphy());
            }
        }

        $this->db->query("
            INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES (
                'stock',
                'insert',
                " . $this->db->escape($inserted_id) . ",
                " . $this->db->escape($this->session->userdata('id')) . ",
                " . $this->db->escape($lastupdate) . "
            )
        ");
    
        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_transaction_print($inserted_id), 'voucher' => $this->_inventory_transaction_voucher($inserted_id)]);
    }




    public function save_openingstock_from_csv()
    {
        $enc        = Config::$encryption_key;
        $date       = $this->input->post('date',       TRUE);
        $reason     = $this->input->post('reason',     TRUE);
        $items_json = $this->input->post('items_json', TRUE);
        $items      = json_decode($items_json, true);

        if (empty($date) || !is_array($items) || count($items) === 0) {
            echo json_encode(['status'=>'Error','message'=>'Date and items are required']);
            return;
        }

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $userid     = $this->session->userdata('id');

        $this->db->trans_begin();
        try {

        $this->db->query("INSERT INTO adj_stock (date, reason, type2, stocktype, type)
            VALUES (
                " . $this->db->escape($date) . ",
                " . $this->db->escape($reason) . ",
                AES_ENCRYPT('A', '$enc'),
                'both',
                'openingstock'
            )");
        $adj_id = $this->db->insert_id();

        foreach ($items as $item) {
            $product      = (int)$item['product_id'];
            $store        = (int)$item['store_id'];
            $batch        = (int)$item['batch_id'];
            $unit         = (int)$item['unit_id'];
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            $qty          = (float)$item['qty'];          // already converted to master unit in JS
            $aqty         = isset($item['aqty']) ? $item['aqty'] : $qty;

            $stockSql = "INSERT INTO {TABLE}
                (product, batch, store, stock, type, pid, date, conversion_id, unit)
                VALUES (
                    " . $this->db->escape($product) . ",
                    " . $this->db->escape($batch) . ",
                    " . $this->db->escape($store) . ",
                    AES_ENCRYPT(" . $this->db->escape($qty) . ", '$enc'),
                    'adj_stock',
                    " . $this->db->escape($adj_id) . ",
                    " . $this->db->escape($date) . ",
                    " . $this->db->escape($conversionid) . ",
                    " . $this->db->escape($unit) . "
                )";
            $this->db->query(str_replace('{TABLE}', 'stock_details',   $stockSql));
            // Unit Cost Logic Specification v1.1 (Phase 7, DUP-03) — cost this line the same way
            // the live New Inventory screen does (_record_inventory_cost(), Phase 2), using the
            // uploaded Unit Cost. Adapter array because this CSV path's $item keys (product_id,
            // batch_id, qty, ...) differ from the live screen's (product, batch, quantity, ...).
            $this->_record_inventory_cost([
                'product'   => $product,
                'batch'     => $batch,
                'quantity'  => $qty,
                'unitcost'  => isset($item['unitcost']) ? (float) $item['unitcost'] : 0,
                'linevalue' => isset($item['linevalue']) ? (float) $item['linevalue'] : 0,
            ], 'openingstock', $date, $adj_id, $store);
            $this->db->query(str_replace('{TABLE}', 'phystock_details', $stockSql));

            /* actual stock audit row — matches UI insertAuditac() */
            $this->db->query("INSERT INTO audit_stock
                (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                 astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                VALUES (
                    " . $this->db->escape($product) . ",
                    " . $this->db->escape($batch) . ",
                    " . $this->db->escape($date) . ",
                    'Inventory Transaction',
                    'openingstock',
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($adj_id) . ", '$enc'),
                    " . $this->db->escape($adj_id) . ",
                    " . $this->db->escape($store) . ",
                    AES_ENCRYPT(" . $this->db->escape($aqty) . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($qty) . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    " . $this->db->escape($lastupdate) . ",
                    AES_ENCRYPT('A', '$enc')
                )");

            /* physical stock audit row — matches UI insertAuditphy() */
            $this->db->query("INSERT INTO audit_stock
                (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                 astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                VALUES (
                    " . $this->db->escape($product) . ",
                    " . $this->db->escape($batch) . ",
                    " . $this->db->escape($date) . ",
                    'Inventory Transaction',
                    'openingstock',
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($adj_id) . ", '$enc'),
                    " . $this->db->escape($adj_id) . ",
                    " . $this->db->escape($store) . ",
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($aqty) . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($qty) . ", '$enc'),
                    " . $this->db->escape($lastupdate) . ",
                    AES_ENCRYPT('A', '$enc')
                )");
        }

        $this->db->query("INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES ('stock', 'insert', " . $this->db->escape($adj_id) . ", " . $this->db->escape($userid) . ", " . $this->db->escape($lastupdate) . ")");

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['status'=>'Error','message'=>$e->getMessage()]);
            return;
        }
        echo json_encode(['status'=>'Success','id'=>$adj_id]);
    }

    public function save_stockadjustment_from_csv()
    {
        $enc           = Config::$encryption_key;
        $date          = $this->input->post('date',          TRUE);
        $incident_type = $this->input->post('incident_type', TRUE);
        $stock_type    = $this->input->post('stock_type',    TRUE);
        $reason        = $this->input->post('reason',        TRUE);
        $items_json    = $this->input->post('items_json',    TRUE);
        $items         = json_decode($items_json, true);

        $valid_types      = ['openingstock','storetransfer','stockdisposal','stockadjustment'];
        $valid_stocktypes = ['actualstock','physicalstock','both'];

        if (empty($date) || !is_array($items) || count($items) === 0) {
            echo json_encode(['status'=>'Error','message'=>'Date and items are required']); return;
        }
        if (!in_array($incident_type, $valid_types)) {
            echo json_encode(['status'=>'Error','message'=>'Invalid incident type']); return;
        }
        if (!in_array($stock_type, $valid_stocktypes)) {
            echo json_encode(['status'=>'Error','message'=>'Invalid stock type']); return;
        }

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $userid     = $this->session->userdata('id');

        $this->db->trans_begin();
        try {

        $this->db->query("INSERT INTO adj_stock (date, reason, type2, stocktype, type)
            VALUES (
                " . $this->db->escape($date)          . ",
                " . $this->db->escape($reason)         . ",
                AES_ENCRYPT('A', '$enc'),
                " . $this->db->escape($stock_type)     . ",
                " . $this->db->escape($incident_type)  . "
            )");
        $adj_id = $this->db->insert_id();

        foreach ($items as $item) {
            $product      = (int)$item['product_id'];
            $store        = (int)$item['store_id'];
            $batch        = (int)$item['batch_id'];
            $unit         = (int)$item['unit_id'];
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            $adj          = $item['adj'];
            $qty          = (float)$item['qty'];
            $aqty         = isset($item['aqty']) ? $item['aqty'] : (string)$qty;

            $signed_qty  = ($adj === 'increase') ?  $qty : -$qty;
            $signed_aqty = ($adj === 'increase') ? $aqty : '-' . $aqty;

            $stock_sql = "INSERT INTO {TABLE}
                (product, batch, store, stock, type, pid, date, conversion_id, unit)
                VALUES (
                    " . $this->db->escape($product)      . ",
                    " . $this->db->escape($batch)         . ",
                    " . $this->db->escape($store)         . ",
                    AES_ENCRYPT(" . $this->db->escape($signed_qty) . ", '$enc'),
                    'adj_stock',
                    " . $this->db->escape($adj_id)        . ",
                    " . $this->db->escape($date)           . ",
                    " . $this->db->escape($conversionid)   . ",
                    " . $this->db->escape($unit)           . "
                )";

            $audit_ac = "INSERT INTO audit_stock
                (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                 astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                VALUES (
                    " . $this->db->escape($product)       . ",
                    " . $this->db->escape($batch)          . ",
                    " . $this->db->escape($date)           . ",
                    'Inventory Transaction',
                    " . $this->db->escape($incident_type)  . ",
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($adj_id) . ", '$enc'),
                    " . $this->db->escape($adj_id)         . ",
                    " . $this->db->escape($store)          . ",
                    AES_ENCRYPT(" . $this->db->escape($signed_aqty) . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($signed_qty)  . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    " . $this->db->escape($lastupdate)     . ",
                    AES_ENCRYPT('A', '$enc')
                )";

            $audit_phy = "INSERT INTO audit_stock
                (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                 astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                VALUES (
                    " . $this->db->escape($product)       . ",
                    " . $this->db->escape($batch)          . ",
                    " . $this->db->escape($date)           . ",
                    'Inventory Transaction',
                    " . $this->db->escape($incident_type)  . ",
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($adj_id) . ", '$enc'),
                    " . $this->db->escape($adj_id)         . ",
                    " . $this->db->escape($store)          . ",
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($signed_aqty) . ", '$enc'),
                    AES_ENCRYPT('', '$enc'),
                    AES_ENCRYPT(" . $this->db->escape($signed_qty)  . ", '$enc'),
                    " . $this->db->escape($lastupdate)     . ",
                    AES_ENCRYPT('A', '$enc')
                )";

            if ($stock_type === 'actualstock') {
                $this->db->query(str_replace('{TABLE}', 'stock_details',   $stock_sql));
                $this->db->query($audit_ac);
                if (in_array($incident_type, ['storetransfer','stockdisposal'])) {
                    $col      = ($adj === 'increase') ? 'auto_grn' : 'auto_gdn';
                    $storeRow = $this->db->select($col)->from('store')->where('id', $store)->get()->row();
                    if ($storeRow && $storeRow->$col == 0) {
                        $this->db->query(str_replace('{TABLE}', 'phystock_details', $stock_sql));
                        $this->db->query($audit_phy);
                    }
                }
            } elseif ($stock_type === 'physicalstock') {
                $this->db->query(str_replace('{TABLE}', 'phystock_details', $stock_sql));
                $this->db->query($audit_phy);
            } else {
                $this->db->query(str_replace('{TABLE}', 'stock_details',    $stock_sql));
                $this->db->query($audit_ac);
                $this->db->query(str_replace('{TABLE}', 'phystock_details', $stock_sql));
                $this->db->query($audit_phy);
            }
        }

        $this->db->query("INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES ('stock', 'insert',
                " . $this->db->escape($adj_id) . ",
                " . $this->db->escape($userid) . ",
                " . $this->db->escape($lastupdate) . ")");

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['status'=>'Error','message'=>$e->getMessage()]);
            return;
        }
        echo json_encode(['status'=>'Success','id'=>$adj_id]);
    }

    public function update_adjstock()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');

        $lastupdate = date('Y-m-d H:i:s');
        $id = $this->input->post('id', TRUE);

        // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-edit state before anything
        // below mutates it. Every stock_details row for this voucher gets deleted and recreated
        // with a brand-new id below (unlike Purchase/Sale, whose detail rows keep their id across
        // an edit) so ALL costed lines, not just newly-added ones, need re-costing after the edit
        // (see the items loop below) in addition to the chain re-run.
        $old_adjstock_date_row = $this->db->select('date')->from('adj_stock')->where('id', $id)->get()->row();
        $old_adjstock_date = $old_adjstock_date_row ? $old_adjstock_date_row->date : $this->input->post('date', TRUE);
        $old_adjstock_pairs = $this->db->select('product, batch')->from('stock_details')->where('pid', $id)->where('type', 'adj_stock')->get()->result_array();

       $this->db->trans_begin();
        try {
        if ($this->input->post('oldType', TRUE) == "actualstock") {
            $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('stock_details');
            $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('phystock_details');
        } elseif ($this->input->post('oldType', TRUE) == "physicalstock") {
            $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('phystock_details');
        } else {
            $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('stock_details');
            $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('phystock_details');
        }
    
        $this->db->where('pid', $id)->where('scenario', 'Inventory Transaction')->delete('audit_stock');
    
        $this->db->query("
            UPDATE adj_stock SET
                date = " . $this->db->escape($items[0]['date']) . ",
                reason = " . $this->db->escape($items[0]['reason']) . ",
                stocktype = " . $this->db->escape($items[0]['stocktype']) . ",
                type = " . $this->db->escape($items[0]['type']) . "
            WHERE id = " . $this->db->escape($id) . "
        ");
    
        foreach ($items as $item) {
    
            $quantity = ($item["adj"] == "increase") 
                ? $item['quantity'] 
                : -$item['quantity'];
    
            $qtystr = ($item["adj"] == "increase") 
                ? $item['aqty'] 
                : "-" . $item['aqty'];
    
            $insertStock = function($table) use ($item, $quantity, $id, $items, $encryption_key) {
                return "
                    INSERT INTO {$table}
                    (product, batch, store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($item['store']) . ",
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
                        'adj_stock',
                        " . $this->db->escape($id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )
                ";
            };


            $insertAuditphy = function () use ($item, $items, $qtystr, $quantity, $id, $lastupdate, $encryption_key) {
                return "
        INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($items[0]['date']) . ",
            'Inventory Transaction',
            " . $this->db->escape($items[0]['type']) . ",

            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($id) . ", '{$encryption_key}'),

            " . $this->db->escape($id) . ",
            " . $this->db->escape($item['store']) . ",

            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),

            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),

            " . $this->db->escape($lastupdate) . ",
            AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        )
    ";
            };

            $insertAuditac = function () use ($item, $items, $qtystr, $quantity, $id, $lastupdate, $encryption_key) {
                return "
        INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($items[0]['date']) . ",
            'Inventory Transaction',
            " . $this->db->escape($items[0]['type']) . ",

            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($id) . ", '{$encryption_key}'),

            " . $this->db->escape($id) . ",
            " . $this->db->escape($item['store']) . ",

            AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),

            AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),

            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        )
    ";
            };

            if ($items[0]['stocktype'] == "actualstock") {

                $this->db->query($insertStock("stock_details"));
                $this->_record_inventory_cost($item, $items[0]['type'], $items[0]['date'], $id, $item['store']);
                $this->db->query($insertAuditac());

                if (in_array($items[0]['type'], ["storetransfer", "stockdisposal"])) {

                    $store = $this->db->select(
                        $item["adj"] == "increase" ? "auto_grn" : "auto_gdn"
                    )->from('store')->where('id', $item['store'])->get()->row();

                    $flag = ($item["adj"] == "increase") ? $store->auto_grn : $store->auto_gdn;

                    if ($flag == 0) {
                        $this->db->query($insertStock("phystock_details"));
                        $this->db->query($insertAuditphy());
                    }
                }

            } elseif ($items[0]['stocktype'] == "physicalstock") {

                $this->db->query($insertStock("phystock_details"));
                $this->db->query($insertAuditphy());

            } else {

                $this->db->query($insertStock("stock_details"));
                $this->_record_inventory_cost($item, $items[0]['type'], $items[0]['date'], $id, $item['store']);
                $this->db->query($insertAuditac());

                $this->db->query($insertStock("phystock_details"));
                $this->db->query($insertAuditphy());
            }
        }

        // Unit Cost Logic Specification v1.1 (Phase 5) — re-run every Product+Batch this voucher
        // touched, before or after the edit, from whichever date is earlier (UCL-50/UCL-52). The
        // fresh _record_inventory_cost() calls above already seeded unit_cost rows for the
        // recreated stock_details rows; this replays everything (this voucher's lines included)
        // in correct chronological order.
        $new_adjstock_date = $items[0]['date'];
        $from_date_for_cost = ($old_adjstock_date <= $new_adjstock_date) ? $old_adjstock_date : $new_adjstock_date;
        $affected_pairs = $old_adjstock_pairs;
        foreach ($items as $item) {
            $affected_pairs[] = ['product' => $item['product'], 'batch' => $item['batch']];
        }
        $this->_rerun_affected_pairs($affected_pairs, $from_date_for_cost);

        $this->db->query("
            INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES (
                'stock',
                'update',
                " . $this->db->escape($id) . ",
                " . $this->db->escape($this->session->userdata('id')) . ",
                " . $this->db->escape($lastupdate) . "
            )
        ");
    
        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_transaction_print($id), 'voucher' => $this->_inventory_transaction_voucher($id)]);
    }
    public function save_storetransfer()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $inventory_request_id = (int)$this->input->post('inventory_request_id', TRUE);

        $this->db->trans_begin();
        try {

        $inventory_id = $this->_next_inventory_id($items[0]['type2']);

        $this->db->query("
            INSERT INTO adj_stock (inventory_id, date, reason, type2, stocktype, type, inventory_request_no)
            VALUES (
                AES_ENCRYPT('{$inventory_id}', '{$encryption_key}'),
                " . $this->db->escape($items[0]['date']) . ",
                " . $this->db->escape($items[0]['reason']) . ",
                AES_ENCRYPT(" . $this->db->escape($items[0]['type2']) . ", '{$encryption_key}'),
                'actualstock',
                'storetransfer',
                " . $this->db->escape($inventory_request_id) . "
            )
        ");

        $inserted_id = $this->db->insert_id();

        if ($inventory_request_id > 0) {
            $this->db->query("UPDATE inventory_request SET status = 1 WHERE id = " . $this->db->escape($inventory_request_id));
        }

        foreach ($items as $item) {
            $from_qty    = -abs($item['quantity']);
            $to_qty      = abs($item['quantity']);
            $from_qtystr = '-' . $item['aqty'];
            $to_qtystr   = $item['aqty'];

            $insertStock = function($table, $store, $qty) use ($item, $inserted_id, $items, $encryption_key) {
                return "INSERT INTO {$table} (product, batch, store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        'adj_stock',
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )";
            };

            $insertAuditAc = function($store, $qty, $qtystr) use ($item, $items, $inserted_id, $lastupdate, $encryption_key) {
                return "INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        'storetransfer',
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($inserted_id) . ", '{$encryption_key}'),
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        " . $this->db->escape($lastupdate) . ",
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    )";
            };

            $insertAuditPhy = function($store, $qty, $qtystr) use ($item, $items, $inserted_id, $lastupdate, $encryption_key) {
                return "INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        'storetransfer',
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($inserted_id) . ", '{$encryption_key}'),
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        " . $this->db->escape($lastupdate) . ",
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    )";
            };

            // Decrease from from_store
            $this->db->query($insertStock("stock_details", $item['from_store'], $from_qty));
            $this->db->query($insertAuditAc($item['from_store'], $from_qty, $from_qtystr));
            $from_store_row = $this->db->select("auto_gdn")->from('store')->where('id', $item['from_store'])->get()->row();
            if ($from_store_row && $from_store_row->auto_gdn == 0) {
                $this->db->query($insertStock("phystock_details", $item['from_store'], $from_qty));
                $this->db->query($insertAuditPhy($item['from_store'], $from_qty, $from_qtystr));
            }

            // Increase in to_store
            $this->db->query($insertStock("stock_details", $item['to_store'], $to_qty));
            $this->db->query($insertAuditAc($item['to_store'], $to_qty, $to_qtystr));
            $to_store_row = $this->db->select("auto_grn")->from('store')->where('id', $item['to_store'])->get()->row();
            if ($to_store_row && $to_store_row->auto_grn == 0) {
                $this->db->query($insertStock("phystock_details", $item['to_store'], $to_qty));
                $this->db->query($insertAuditPhy($item['to_store'], $to_qty, $to_qtystr));
            }
        }

        $this->db->query("
            INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES (
                'stock',
                'insert',
                " . $this->db->escape($inserted_id) . ",
                " . $this->db->escape($this->session->userdata('id')) . ",
                " . $this->db->escape($lastupdate) . "
            )
        ");

        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode('Error');
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_transaction_print($inserted_id), 'voucher' => $this->_inventory_transaction_voucher($inserted_id)]);
    }

    public function update_storetransfer()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $id = $this->input->post('id', TRUE);

        $this->db->trans_begin();
        try {

        // Delete existing stock and audit records for this adjustment
        $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('stock_details');
        $this->db->where('pid', $id)->where('type', 'adj_stock')->delete('phystock_details');
        $this->db->where('pid', $id)->where('scenario', 'Inventory Transaction')->delete('audit_stock');

        $this->db->query("
            UPDATE adj_stock SET
                date     = " . $this->db->escape($items[0]['date']) . ",
                reason   = " . $this->db->escape($items[0]['reason']) . ",
                stocktype = 'actualstock',
                type     = 'storetransfer'
            WHERE id = " . $this->db->escape($id) . "
        ");

        foreach ($items as $item) {
            $from_qty    = -abs($item['quantity']);
            $to_qty      = abs($item['quantity']);
            $from_qtystr = '-' . $item['aqty'];
            $to_qtystr   = $item['aqty'];

            $insertStock = function($table, $store, $qty) use ($item, $id, $items, $encryption_key) {
                return "INSERT INTO {$table} (product, batch, store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        'adj_stock',
                        " . $this->db->escape($id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )";
            };

            $insertAuditAc = function($store, $qty, $qtystr) use ($item, $items, $id, $lastupdate, $encryption_key) {
                return "INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        'storetransfer',
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($id) . ", '{$encryption_key}'),
                        " . $this->db->escape($id) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        " . $this->db->escape($lastupdate) . ",
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    )";
            };

            $insertAuditPhy = function($store, $qty, $qtystr) use ($item, $items, $id, $lastupdate, $encryption_key) {
                return "INSERT INTO audit_stock
                    (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
                     astockstr, pstockstr, astock, pstock, lastupdateddate, type2)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        'Inventory Transaction',
                        'storetransfer',
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($id) . ", '{$encryption_key}'),
                        " . $this->db->escape($id) . ",
                        " . $this->db->escape($store) . ",
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qtystr) . ", '{$encryption_key}'),
                        AES_ENCRYPT('', '{$encryption_key}'),
                        AES_ENCRYPT(" . $this->db->escape($qty) . ", '{$encryption_key}'),
                        " . $this->db->escape($lastupdate) . ",
                        AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
                    )";
            };

            // Decrease from from_store
            $this->db->query($insertStock("stock_details", $item['from_store'], $from_qty));
            $this->db->query($insertAuditAc($item['from_store'], $from_qty, $from_qtystr));
            $from_store_row = $this->db->select("auto_gdn")->from('store')->where('id', $item['from_store'])->get()->row();
            if ($from_store_row && $from_store_row->auto_gdn == 0) {
                $this->db->query($insertStock("phystock_details", $item['from_store'], $from_qty));
                $this->db->query($insertAuditPhy($item['from_store'], $from_qty, $from_qtystr));
            }

            // Increase in to_store
            $this->db->query($insertStock("stock_details", $item['to_store'], $to_qty));
            $this->db->query($insertAuditAc($item['to_store'], $to_qty, $to_qtystr));
            $to_store_row = $this->db->select("auto_grn")->from('store')->where('id', $item['to_store'])->get()->row();
            if ($to_store_row && $to_store_row->auto_grn == 0) {
                $this->db->query($insertStock("phystock_details", $item['to_store'], $to_qty));
                $this->db->query($insertAuditPhy($item['to_store'], $to_qty, $to_qtystr));
            }
        }

        $this->db->query("
            INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
            VALUES (
                'stock',
                'update',
                " . $this->db->escape($id) . ",
                " . $this->db->escape($this->session->userdata('id')) . ",
                " . $this->db->escape($lastupdate) . "
            )
        ");

        if ($this->db->trans_status() === false) {
            throw new Exception('Database operation failed');
        }
        $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode('Error');
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_transaction_print($id), 'voucher' => $this->_inventory_transaction_voucher($id)]);
    }

    public function delete_adjstock($id = null)
    {
        $data = $this->stocktype($id);

        // Reopen the source Inventory Request (if any) so it's selectable
        // again in "New Inventory", mirroring how Sale/Purchase deletion
        // reopens their source Sales/Purchase Order.
        $source = $this->db->select('inventory_request_no')->from('adj_stock')->where('id', $id)->get()->row();
        if ($source && (int)$source->inventory_request_no > 0) {
            $this->db->query("UPDATE inventory_request SET status = 0 WHERE id = " . (int)$source->inventory_request_no);
        }

        // Unit Cost Logic Specification v1.1 (Phase 5) — capture pre-delete state (see
        // Purchase::delete_purchase() for the same reasoning).
        $old_adjstock_pairs = $this->db->select('product, batch')->from('stock_details')->where('pid', $id)->where('type', 'adj_stock')->get()->result_array();
        $old_adjstock_date_row = $this->db->select('date')->from('adj_stock')->where('id', $id)->get()->row();
        $old_adjstock_date = $old_adjstock_date_row ? $old_adjstock_date_row->date : null;

        if ($data['stocktype'] == "actualstock") {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('stock_details');

            if ($data['type'] == "storetransfer" || $data['type'] == "stockdisposal") {
                $this->db->where('pid', $id)
                    ->where('type', 'adj_stock')
                    ->delete('phystock_details');
            }
        } else 
        if ($data['stocktype'] == "physicalstock") {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        } else {
            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('stock_details');

            $this->db->where('pid', $id)
                ->where('type', 'adj_stock')
                ->delete('phystock_details');
        }
        $this->db->where('id', $id)
            ->delete('adj_stock');
        $this->db->where('pid',  $id)
            ->where('scenario', 'Inventory Transaction')
            ->delete('audit_stock');

        // Unit Cost Logic Specification v1.1 (Phase 5) — re-cost every Product+Batch this
        // deleted voucher had affected.
        $this->_rerun_affected_pairs($old_adjstock_pairs, $old_adjstock_date);

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate)
            VALUES (
                0,
                'stock',
                'delete',
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();
        echo '<script type="text/javascript">
        alert("Deleted successfully");
        window.location.href = "' . $base_url . 'manage_inventory_transection";
       </script>';
    }

    public function bdtask_manage_stock_adjustment()
    {
        $data['title']      = display('manage_stock_adjustment');
        $data['module']     = "stock";
        $data['page']       = "manage_stock_adjustment";
        if (!$this->permission1->method('manage_stock_adjustment', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function checkadjstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->adjstock($postData,$this->input->post('fdate'),$this->input->post('tdate'));
        echo json_encode($data);
    }

    public function bdtask_manage_inventory_transection()
    {
        $data['title']      = 'Manage Inventory Transection';
        $data['module']     = "stock";
        $data['page']       = "manage_inventory_transection";
        $data['total_product'] = 0;
        if (!$this->permission1->method('manage_inventory_transection', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function check_inventory_transection()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->inventory_transection($postData, $this->input->post('fdate'), $this->input->post('tdate'));
        echo json_encode($data);
    }


    public function getAdjStockById()
    {
        $encryption_key = Config::$encryption_key;
        $table = $this->input->post('type') == "physicalstock" ? 'phystock_details' : 'stock_details';

        // The Unit Cost the user entered on the line (Opening Stock) — only the Actual ledger
        // carries one; Physical stock is never costed (UCL-04).
        $unit_cost_sql = ($table === 'stock_details')
            ? "AES_DECRYPT(b.unit_cost, '{$encryption_key}') + 0"
            : "NULL";

        $this->db->select("
            b.product, 
            b.store, 
            a.date AS date, 
            a.reason AS reason,
         ROUND(
    CASE
        WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '+'
            THEN AES_DECRYPT(b.stock, '{$encryption_key}') + cr.conversion_ratio

        WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '-'
            THEN AES_DECRYPT(b.stock, '{$encryption_key}') - cr.conversion_ratio

        WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '*'
            THEN AES_DECRYPT(b.stock, '{$encryption_key}') * cr.conversion_ratio

        WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '/'
            THEN AES_DECRYPT(b.stock, '{$encryption_key}') / cr.conversion_ratio

        ELSE AES_DECRYPT(b.stock, '{$encryption_key}')
    END,
    6
) AS actualstock, 
            b.unit,
            a.type,
            b.batch,
            {$unit_cost_sql} AS unit_cost,
            b.conversion_id,cr.convertiontype,cr.conversion_ratio,
            (
                SELECT 
                    CASE
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                        ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
                    END
                FROM {$table} c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = b.conversion_id
                WHERE b.product = c.product
                  AND b.store = c.store
                  AND b.batch = c.batch
            ) AS avstock,pi.batchtype,pi.stock
        ");

        $this->db->from('adj_stock a');

        if ($this->input->post('type') == "physicalstock") {
            $this->db->join('phystock_details b', 'b.pid = a.id');
        } else {
            $this->db->join('stock_details b', 'b.pid = a.id');
        }

        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
        $this->db->where('b.pid', $this->input->post('pid'));
        // $this->db->where('cr.status', 1);

        $this->db->where('b.type', 'adj_stock');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }




    //Inventory Request part — same form/fields as New Inventory, but never
    //writes stock_details/phystock_details/audit_stock. It's a draft/order
    //document (status 0=open/1=converted) that "New Inventory" can later pull
    //in, mirroring the Sales Order -> Sale / Purchase Order -> Purchase
    //conversion pattern already used elsewhere in this codebase.

    // One-click entry point from Manage Inventory Request's "Convert" button
    // — mirrors Invoice::bdtask_convertsales_form(): opens the normal "New
    // Inventory" create form (not an edit of an existing adj_stock row — id
    // stays null for that), but with convert_request_id set so the view
    // auto-selects and loads that request instead of the user doing it
    // manually from the dropdown.
    public function bdtask_convertinventoryrequest_form($requestId = null)
    {
        if (!$this->permission1->method('new_inventory_transection', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }

        $request = $this->inventoryrequesttype($requestId);
        if (!$request || (int)$request['status'] !== 0) {
            echo '<script>alert("This request is no longer open for conversion."); window.location.href = "' . base_url() . 'manage_inventory_request";</script>';
            return;
        }

        $data = array(
            'title' => 'New Inventory Transection',
        );
        $data['products'] = $this->active_product();
        $data["batches"] = $this->active_batch();
        $data['store_list'] = $this->product_model->active_store();
        $data['stockbatchopening'] = $this->stockbatchopening();
        $data['units'] = $this->active_units();
        $data['modal_products'] = $this->singleusage_product();
        $data['category_list'] = $this->product_model->active_category();
        $data['unit_list']     = $this->product_model->active_unit();
        $data['all_supplier']  = $this->purchase_model->supplier_list();

        $data['id'] = null;
        $data['type'] = null;
        $data['convert_request_id'] = (int)$requestId;

        $data['module'] = 'stock';
        $data['page']   = "new_inventory_transection";

        echo modules::run('template/layout', $data);
    }

    public function bdtask_new_inventory_request_form($id = null)
    {
        $data = array(
            'title' => 'Inventory Transaction Request',
        );
        $data['products'] = $this->active_product();
        $data["batches"] = $this->active_batch();
        $data['store_list'] = $id ? $this->product_model->all_store() : $this->product_model->active_store();
        $data['stockbatchopening'] = $this->stockbatchopening();
        $data['units'] = $this->active_units();
        $data['modal_products'] = $this->singleusage_product();
        $data['category_list'] = $this->product_model->active_category();
        $data['unit_list']     = $this->product_model->active_unit();
        $data['all_supplier']  = $this->purchase_model->supplier_list();

        $data['id'] = $id;
        $data['type'] = null;

        $data['module'] = 'stock';
        $data['page']   = "new_inventory_request";

        if (!$id) {
            if (!$this->permission1->method('new_inventory_request', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('new_inventory_request', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        if ($id != null) {
            $data['type'] = $this->inventoryrequesttype($id);

            if ($data['type'] && (int)$data['type']['status'] === 1) {
                echo '<script>alert("This request has already been converted into an Inventory Transaction and can no longer be edited."); window.location.href = "' . base_url() . 'manage_inventory_request";</script>';
                return;
            }
            $data['title'] = "Edit Inventory Transaction Request";
        }
        echo modules::run('template/layout', $data);
    }

    public function inventoryrequesttype($id = null)
    {
        $this->db->select('stocktype,type,status');
        $this->db->from('inventory_request');
        $this->db->where('id', $id);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    public function save_inventory_request()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');

        $lastupdate = date('Y-m-d H:i:s');
        $type2 = $items[0]['type2'];

        $this->db->trans_begin();
        try {

            $series_floor = ($type2 === 'B') ? 1000000000 : 0;
            $max_row = $this->db->query("
                SELECT MAX(CAST(SUBSTRING_INDEX(AES_DECRYPT(request_no, '{$encryption_key}'), '-', -1) AS UNSIGNED)) AS max_no
                FROM inventory_request
                WHERE AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
            ")->row();
            $max_no = $max_row ? $max_row->max_no : null;
            $next_no = ($max_no && $max_no > $series_floor) ? ($max_no + 1) : ($series_floor + 1);
            $request_no = 'IR-' . str_pad($next_no, 10, '0', STR_PAD_LEFT);

            $this->db->query("
                INSERT INTO inventory_request (request_no, date, reason, type2, stocktype, type, status, createddate, userid)
                VALUES (
                    AES_ENCRYPT('{$request_no}', '{$encryption_key}'),
                    " . $this->db->escape($items[0]['date']) . ",
                    " . $this->db->escape($items[0]['reason']) . ",
                    AES_ENCRYPT(" . $this->db->escape($type2) . ", '{$encryption_key}'),
                    " . $this->db->escape($items[0]['stocktype']) . ",
                    " . $this->db->escape($items[0]['type']) . ",
                    0,
                    " . $this->db->escape($lastupdate) . ",
                    " . $this->db->escape((int)$this->session->userdata('id')) . "
                )
            ");

            $inserted_id = $this->db->insert_id();

            foreach ($items as $item) {
                $quantity = ($item["adj"] == "increase") ? $item['quantity'] : -$item['quantity'];
                $to_store = isset($item['to_store']) && $item['to_store'] !== '' ? (int)$item['to_store'] : 0;

                $this->db->query("
                    INSERT INTO inventory_request_details
                    (product, batch, store, to_store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($item['store']) . ",
                        " . $this->db->escape($to_store) . ",
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
                        'inventory_request',
                        " . $this->db->escape($inserted_id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )
                ");
            }

            $this->db->query("
                INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
                VALUES (
                    'inventory_request',
                    'insert',
                    " . $this->db->escape($inserted_id) . ",
                    " . $this->db->escape($this->session->userdata('id')) . ",
                    " . $this->db->escape($lastupdate) . "
                )
            ");

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database operation failed');
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode('Error');
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_request_print($inserted_id), 'voucher' => $this->_inventory_request_voucher($inserted_id)]);
    }

    public function update_inventory_request()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;
        date_default_timezone_set('Asia/Colombo');

        $lastupdate = date('Y-m-d H:i:s');
        $id = $this->input->post('id', TRUE);

        $this->db->trans_begin();
        try {
            $already = $this->inventoryrequesttype($id);
            if ($already && (int)$already['status'] === 1) {
                throw new Exception('This request has already been converted and cannot be edited.');
            }

            $this->db->where('pid', $id)->where('type', 'inventory_request')->delete('inventory_request_details');

            $this->db->query("
                UPDATE inventory_request SET
                    date = " . $this->db->escape($items[0]['date']) . ",
                    reason = " . $this->db->escape($items[0]['reason']) . ",
                    stocktype = " . $this->db->escape($items[0]['stocktype']) . ",
                    type = " . $this->db->escape($items[0]['type']) . "
                WHERE id = " . $this->db->escape($id) . "
            ");

            foreach ($items as $item) {
                $quantity = ($item["adj"] == "increase") ? $item['quantity'] : -$item['quantity'];
                $to_store = isset($item['to_store']) && $item['to_store'] !== '' ? (int)$item['to_store'] : 0;

                $this->db->query("
                    INSERT INTO inventory_request_details
                    (product, batch, store, to_store, stock, type, pid, date, conversion_id, unit)
                    VALUES (
                        " . $this->db->escape($item['product']) . ",
                        " . $this->db->escape($item['batch']) . ",
                        " . $this->db->escape($item['store']) . ",
                        " . $this->db->escape($to_store) . ",
                        AES_ENCRYPT(" . $this->db->escape($quantity) . ", '{$encryption_key}'),
                        'inventory_request',
                        " . $this->db->escape($id) . ",
                        " . $this->db->escape($items[0]['date']) . ",
                        " . $this->db->escape(isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0) . ",
                        " . $this->db->escape($item['unit']) . "
                    )
                ");
            }

            $this->db->query("
                INSERT INTO logs (screen, operation, pid, userid, lastupdatedate)
                VALUES (
                    'inventory_request',
                    'update',
                    " . $this->db->escape($id) . ",
                    " . $this->db->escape($this->session->userdata('id')) . ",
                    " . $this->db->escape($lastupdate) . "
                )
            ");

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database operation failed');
            }
            $this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->_log_error(__METHOD__, $e->getMessage());
            echo json_encode('Error');
            return;
        }
        echo json_encode(['success' => true, 'details' => $this->_build_inventory_request_print($id), 'voucher' => $this->_inventory_request_voucher($id)]);
    }

    // Edit-load for the Inventory Request form itself (reusing the batch/
    // conversion-ratio join shape of getAdjStockById, but against the single
    // inventory_request_details table — there's no actual/physical split
    // here since nothing is actually committed yet).
    public function getInventoryRequestForEdit()
    {
        $encryption_key = Config::$encryption_key;
        $stocktype = $this->input->post('type');
        $table = ($stocktype == 'physicalstock') ? 'phystock_details' : 'stock_details';

        // avstock = the real current on-hand balance for this product/store/
        // batch (summed across the actual committed stock ledger) — NOT this
        // request row's own entered quantity. Mirrors getAdjStockById()'s
        // subquery exactly, just sourced from the real stock table instead
        // of adj_stock's own detail rows.
        $this->db->select("
            b.product,
            b.store,
            b.to_store,
            a.date AS date,
            a.reason AS reason,
            ROUND(
                CASE
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '+' THEN AES_DECRYPT(b.stock, '{$encryption_key}') + cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '-' THEN AES_DECRYPT(b.stock, '{$encryption_key}') - cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '*' THEN AES_DECRYPT(b.stock, '{$encryption_key}') * cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '/' THEN AES_DECRYPT(b.stock, '{$encryption_key}') / cr.conversion_ratio
                    ELSE AES_DECRYPT(b.stock, '{$encryption_key}')
                END,
                6
            ) AS actualstock,
            b.unit,
            a.type,
            b.batch,
            b.conversion_id, cr.convertiontype, cr.conversion_ratio,
            (
                SELECT
                    CASE
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                        ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
                    END
                FROM {$table} c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = b.conversion_id
                WHERE b.product = c.product AND b.store = c.store AND b.batch = c.batch
            ) AS avstock,
            pi.batchtype, pi.stock
        ");
        $this->db->from('inventory_request a');
        $this->db->join('inventory_request_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'inventory_request');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    // Dropdown source for "New Inventory" — lists open (not-yet-converted)
    // requests, same role as getsalesorderidbybranch()/getpurchaseorderidbybranch()
    // (no branch filter: adj_stock/inventory transactions aren't branch-scoped
    // in this app — neither table has a branch column).
    public function getinventoryrequestlist()
    {
        $encryption_key = Config::$encryption_key;
        $result = $this->db->select("id, AES_DECRYPT(request_no, '{$encryption_key}') AS request_no")
            ->from('inventory_request')
            ->where('status', 0)
            ->order_by('id', 'desc')
            ->get()->result();
        echo json_encode($result);
    }

    // Pulls one Inventory Request's header + every line into the "New
    // Inventory" form — mirrors Invoice::getSalesOrderById()/
    // getPurchaseOrderById(): header fields duplicated on every returned row
    // so a single AJAX call can repopulate the whole form.
    public function getInventoryRequestById()
    {
        $encryption_key = Config::$encryption_key;
        $requestId = $this->input->post('id');

        $meta = $this->db->select('stocktype')->from('inventory_request')->where('id', $requestId)->get()->row();
        $table = ($meta && $meta->stocktype == 'physicalstock') ? 'phystock_details' : 'stock_details';

        // avstock = the real current on-hand balance for this product/store/
        // batch (summed across the actual committed stock ledger), used to
        // populate the AV.QTY column — not this request row's own entered
        // quantity (that's "actualstock"/QTY). Mirrors getAdjStockById()'s
        // subquery exactly, just sourced from the real stock table.
        $this->db->select("
            a.id AS request_id,
            a.date, a.reason, a.type, a.stocktype,
            b.product, b.store, b.to_store, b.unit, b.batch, b.conversion_id,
            cr.convertiontype, cr.conversion_ratio,
            ROUND(
                CASE
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '+' THEN AES_DECRYPT(b.stock, '{$encryption_key}') + cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '-' THEN AES_DECRYPT(b.stock, '{$encryption_key}') - cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '*' THEN AES_DECRYPT(b.stock, '{$encryption_key}') * cr.conversion_ratio
                    WHEN b.conversion_id IS NOT NULL AND cr.convertiontype = '/' THEN AES_DECRYPT(b.stock, '{$encryption_key}') / cr.conversion_ratio
                    ELSE AES_DECRYPT(b.stock, '{$encryption_key}')
                END,
                6
            ) AS actualstock,
            (
                SELECT
                    CASE
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) + cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) - cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) * cr2.conversion_ratio
                        WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/' THEN SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) / cr2.conversion_ratio
                        ELSE SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
                    END
                FROM {$table} c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = b.conversion_id
                WHERE b.product = c.product AND b.store = c.store AND b.batch = c.batch
            ) AS avstock,
            pi.batchtype, pi.stock
        ");
        $this->db->from('inventory_request a');
        $this->db->join('inventory_request_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
        $this->db->where('a.id', $requestId);
        $this->db->where('a.status', 0);
        $this->db->where('b.type', 'inventory_request');

        $query = $this->db->get();
        echo json_encode($query->result_array());
    }

    public function delete_inventoryrequest($id = null)
    {
        $data = $this->inventoryrequesttype($id);

        if ($data && (int)$data['status'] === 1) {
            echo '<script>alert("This request has already been converted into an Inventory Transaction and cannot be deleted."); window.location.href = "' . base_url() . 'manage_inventory_request";</script>';
            return;
        }

        $this->db->where('pid', $id)->where('type', 'inventory_request')->delete('inventory_request_details');
        $this->db->where('id', $id)->delete('inventory_request');

        $lastupdate = date('Y-m-d H:i:s');
        $this->db->query("
            INSERT INTO logs (id, screen, operation, pid, userid, lastupdatedate)
            VALUES (0, 'inventory_request', 'delete', '{$id}', '{$this->session->userdata('id')}', '{$lastupdate}');
        ");

        $base_url = base_url();
        echo '<script type="text/javascript">
        alert("Deleted successfully");
        window.location.href = "' . $base_url . 'manage_inventory_request";
       </script>';
    }

    // Status convention mirrors Sales Order (sales_order.status): 0 = Open,
    // 1 = Converted (locked — handled by save_adjstock/save_storetransfer),
    // 2 = Cancelled (soft-cancel — still editable/deletable, can be reopened).
    public function update_inventoryrequeststatuscancel($id = null)
    {
        $base_url = base_url();
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->query("UPDATE inventory_request SET status = 2 WHERE id = '{$id}'");

        $this->db->query("
            INSERT INTO logs (id, screen, operation, pid, userid, lastupdatedate)
            VALUES (0, 'inventory_request', 'update', '{$id}', '{$this->session->userdata('id')}', '{$lastupdate}');
        ");

        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_inventory_request";
       </script>';
    }

    public function update_inventoryrequeststatusredo($id = null)
    {
        $base_url = base_url();
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->query("UPDATE inventory_request SET status = 0 WHERE id = '{$id}'");

        $this->db->query("
            INSERT INTO logs (id, screen, operation, pid, userid, lastupdatedate)
            VALUES (0, 'inventory_request', 'update', '{$id}', '{$this->session->userdata('id')}', '{$lastupdate}');
        ");

        echo '<script type="text/javascript">
        alert("Status Updated successfully");
        window.location.href = "' . $base_url . 'manage_inventory_request";
       </script>';
    }

    public function bdtask_manage_inventory_request()
    {
        $data['title']  = 'Manage Inventory Transaction Request';
        $data['module'] = "stock";
        $data['page']   = "manage_inventory_request";
        if (!$this->permission1->method('manage_inventory_request', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function check_inventory_request()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->inventory_request($postData, $this->input->post('fdate'), $this->input->post('tdate'));
        echo json_encode($data);
    }

    // ── POS-style print, same pattern as Sale/Purchase/Customer Receive:
    // a private _build_*_print() renders a print-fragment view and returns
    // the HTML string; the save handler embeds it in its success response
    // for an immediate print, and a separate printX() AJAX endpoint lets the
    // manage list reprint an existing record on demand.

    // Voucher number for the PDF filename (inventorytransaction_<voucher>_
    // DDMMYYYY) — falls back to "#<id>" for pre-migration rows that never
    // got an inventory_id assigned.
    private function _inventory_transaction_voucher($id)
    {
        $encryption_key = Config::$encryption_key;
        $row = $this->db->query("
            SELECT AES_DECRYPT(inventory_id, '{$encryption_key}') AS inventory_id
            FROM adj_stock WHERE id = {$id}
        ")->row();
        return ($row && !empty($row->inventory_id)) ? $row->inventory_id : ('#' . $id);
    }

    private function _build_inventory_transaction_print($id)
    {
        $encryption_key = Config::$encryption_key;

        $header = $this->db->query("
            SELECT id, AES_DECRYPT(inventory_id, '{$encryption_key}') AS inventory_id, date, reason, type, stocktype
            FROM adj_stock
            WHERE id = {$id}
        ")->row_array();

        if (empty($header)) {
            return '';
        }

        $table = ($header['stocktype'] == 'physicalstock') ? 'phystock_details' : 'stock_details';

        // Sub-unit rows (conversion_id set) store the raw quantity in the
        // product's conversion unit — apply the same conversion formula the
        // live form uses (getActiveSubUnitEdit/getAdjStockById) so the
        // printed quantity matches what the screen actually shows, instead
        // of the unconverted raw number.
        $items = $this->db->query("
            SELECT
                pi.product_name,
                st.name AS store_name,
                AES_DECRYPT(sb.batchid, '{$encryption_key}') AS batchid,
                u.unit_name,
                ROUND(
                    CASE
                        WHEN sd.conversion_id IS NOT NULL AND cr.convertiontype = '+' THEN AES_DECRYPT(sd.stock, '{$encryption_key}') + cr.conversion_ratio
                        WHEN sd.conversion_id IS NOT NULL AND cr.convertiontype = '-' THEN AES_DECRYPT(sd.stock, '{$encryption_key}') - cr.conversion_ratio
                        WHEN sd.conversion_id IS NOT NULL AND cr.convertiontype = '*' THEN AES_DECRYPT(sd.stock, '{$encryption_key}') * cr.conversion_ratio
                        WHEN sd.conversion_id IS NOT NULL AND cr.convertiontype = '/' THEN AES_DECRYPT(sd.stock, '{$encryption_key}') / cr.conversion_ratio
                        ELSE AES_DECRYPT(sd.stock, '{$encryption_key}')
                    END,
                    6
                ) AS quantity
            FROM {$table} sd
            INNER JOIN product_information pi ON pi.id = sd.product
            INNER JOIN store st ON st.id = sd.store
            LEFT JOIN stockbatch sb ON sb.id = sd.batch
            LEFT JOIN units u ON u.unit_id = sd.unit
            LEFT JOIN conversion_ratio cr ON cr.conversionratio_id = sd.conversion_id
            WHERE sd.pid = {$id} AND sd.type = 'adj_stock'
            ORDER BY sd.id ASC
        ")->result_array();

        $this->load->model('service/service_model');
        $data = [
            'header'           => $header,
            'items'            => $items,
            'company_info'     => $this->service_model->company_info(),
            'currency_details' => $this->service_model->web_setting(),
        ];

        return $this->load->view('stock/inventory_transaction_print', $data, true);
    }

    public function printInventoryTransaction()
    {
        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }
        $details = $this->_build_inventory_transaction_print($id);
        if ($details === '') {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }
        echo json_encode(['success' => true, 'details' => $details, 'voucher' => $this->_inventory_transaction_voucher($id)]);
    }

    // Voucher number for the PDF filename (inventorytransactionrequest_
    // <voucher>_DDMMYYYY).
    private function _inventory_request_voucher($id)
    {
        $encryption_key = Config::$encryption_key;
        $row = $this->db->query("
            SELECT AES_DECRYPT(request_no, '{$encryption_key}') AS request_no
            FROM inventory_request WHERE id = {$id}
        ")->row();
        return ($row && !empty($row->request_no)) ? $row->request_no : ('#' . $id);
    }

    private function _build_inventory_request_print($id)
    {
        $encryption_key = Config::$encryption_key;

        $header = $this->db->query("
            SELECT id,
                   AES_DECRYPT(request_no, '{$encryption_key}') AS request_no,
                   date, reason, type, stocktype, status
            FROM inventory_request
            WHERE id = {$id}
        ")->row_array();

        if (empty($header)) {
            return '';
        }

        // Sub-unit rows (conversion_id set) store the raw quantity in the
        // product's conversion unit — apply the same conversion formula the
        // live form uses (getActiveSubUnitEdit/getInventoryRequestById) so
        // the printed quantity matches what the screen actually shows.
        $items = $this->db->query("
            SELECT
                pi.product_name,
                st.name AS store_name,
                AES_DECRYPT(sb.batchid, '{$encryption_key}') AS batchid,
                u.unit_name,
                ROUND(
                    CASE
                        WHEN ird.conversion_id IS NOT NULL AND cr.convertiontype = '+' THEN AES_DECRYPT(ird.stock, '{$encryption_key}') + cr.conversion_ratio
                        WHEN ird.conversion_id IS NOT NULL AND cr.convertiontype = '-' THEN AES_DECRYPT(ird.stock, '{$encryption_key}') - cr.conversion_ratio
                        WHEN ird.conversion_id IS NOT NULL AND cr.convertiontype = '*' THEN AES_DECRYPT(ird.stock, '{$encryption_key}') * cr.conversion_ratio
                        WHEN ird.conversion_id IS NOT NULL AND cr.convertiontype = '/' THEN AES_DECRYPT(ird.stock, '{$encryption_key}') / cr.conversion_ratio
                        ELSE AES_DECRYPT(ird.stock, '{$encryption_key}')
                    END,
                    6
                ) AS quantity
            FROM inventory_request_details ird
            INNER JOIN product_information pi ON pi.id = ird.product
            INNER JOIN store st ON st.id = ird.store
            LEFT JOIN stockbatch sb ON sb.id = ird.batch
            LEFT JOIN units u ON u.unit_id = ird.unit
            LEFT JOIN conversion_ratio cr ON cr.conversionratio_id = ird.conversion_id
            WHERE ird.pid = {$id} AND ird.type = 'inventory_request'
            ORDER BY ird.id ASC
        ")->result_array();

        $this->load->model('service/service_model');
        $data = [
            'header'           => $header,
            'items'            => $items,
            'company_info'     => $this->service_model->company_info(),
            'currency_details' => $this->service_model->web_setting(),
        ];

        return $this->load->view('stock/inventory_request_print', $data, true);
    }

    public function printInventoryRequest()
    {
        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }
        $details = $this->_build_inventory_request_print($id);
        if ($details === '') {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }
        echo json_encode(['success' => true, 'details' => $details, 'voucher' => $this->_inventory_request_voucher($id)]);
    }

















    //Store Transfer part

    public function bdtask_new_store_transfer($id = null)
    {
        $data = array(
            'title'         => display('new_store_transfer'),
        );
        $data['products'] = $this->active_productbyfloorandstore();
        $data['floor_list'] = $this->active_floor();
        $data["batches"] = $this->active_batches();

        if ($id) {
            $data['store_list'] = $this->product_model->all_store();
        } else {
            $data['store_list'] = $this->product_model->active_store();
        }
        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_store_transfer";

        if ($id != null) {

            $data['title'] = "Edit Store Transfer";
        }
        echo modules::run('template/layout', $data);
    }


    public function save_nststock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);


        $data = array(
            'id' => 0,
            'date'  => $items[0]['date'],
            'details' => $items[0]['details']
        );


        $this->db->insert('st_stock', $data);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {

            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['tstore']}', 
     '{$item['tfloor']}', 
     AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
      AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
    AES_ENCRYPT('0', '{$encryption_key}'),  
     'st_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);

            $quantity = -$item['quantity'];
            $query = "
    INSERT INTO stock_details 
    (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
    VALUES 
    (0, 
     '{$item['batch']}', 
     '{$item['product']}', 
     '{$item['fstore']}', 
     '{$item['ffloor']}', 
     AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
      AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
    AES_ENCRYPT('0', '{$encryption_key}'),  
     'st_stock', 
     '{$inserted_id}'
    );
";
            $this->db->query($query);
        }
        echo json_encode("Success");
    }


    public function update_nststock()
    {
        $encryption_key = Config::$encryption_key;

        $items = $this->input->post('items', TRUE);

        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'st_stock')
            ->delete('stock_details');
        foreach ($items as $item) {
            $query = "
            INSERT INTO stock_details 
            (id, product, store, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['tstore']}', 
             AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'), 
              AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'st_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);

            $quantity = -$item['quantity'];
            $query = "
            INSERT INTO stock_details 
            (id, batch_id, product, store, floor, expectedstock, actualstock, physicalstock, type, pid) 
            VALUES 
            (0, 
             '{$item['batch']}', 
             '{$item['product']}', 
             '{$item['fstore']}', 
             '{$item['ffloor']}', 
             AES_ENCRYPT('{$quantity}', '{$encryption_key}'), 
              AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            AES_ENCRYPT('0', '{$encryption_key}'),  
             'st_stock', 
             '{$this->input->post('id', TRUE)}'
            );
        ";
            $this->db->query($query);
        }
        echo json_encode("Success");




        echo json_encode("Success");
    }


    public function bdtask_manage_store_transfer()
    {
        $data['title']      = display('manage_store_transfer');
        $data['module']     = "stock";
        $data['page']       = "manage_store_transfer";
        echo modules::run('template/layout', $data);
    }

    public function checkststock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->ststock($postData);
        echo json_encode($data);
    }


    public function delete_ststock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'st_stock')
            ->delete('stock_details');

        $this->db->where('id', $id)
            ->delete('st_stock');

        redirect("manage_store_transfer");
    }

    public function getStStockById()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('b.batch_id, b.product, b.store, b.floor, a.date AS date, a.details AS details,AES_DECRYPT( b.actualstock , "' . $encryption_key . '") AS actualstock, pi.unit, 
    (SELECT SUM(AES_DECRYPT( c.actualstock , "' . $encryption_key . '")) AS actualstock 
     FROM stock_details c 
     WHERE c.batch_id = b.batch_id 
       AND b.product = c.product
       AND b.store = c.store 
       AND b.floor = c.floor) AS avstock');
        $this->db->from('st_stock a');
        $this->db->join('stock_details b', 'b.pid = a.id');
        $this->db->join('product_information pi', 'pi.id = b.product');
        $this->db->where('b.pid', $this->input->post('pid'));
        $this->db->where('b.type', 'st_stock');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }




    // GRN
    public function bdtask_newgrn_form($id = null)
    {
        $data = array(
            'title'         => "New Good Received Notes",
        );
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->active_storegrn();
        $data['all_supplier'] = $this->purchase_model->supplier_list();
        $data["batches"] = $this->active_batch();
        $data['units'] = $this->active_units();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_grn";

        if (!$id) {
            if (!$this->permission1->method('manage_grn', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('manage_grn', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }
        if ($id != null) {

            $data['title'] = "Edit Good Received Notes";
            $data['type']   =   $this->db->select("type")->from('grn_stock')->where('id', $id)->get()->row();

        }
        echo modules::run('template/layout', $data);
    }

    public function active_storegrn()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_grn', 1);
            $this->db->where_not_in('id', 1);

        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_grn', 1)
                ->where_not_in('store.id', 1)
                ->group_by('sec_store.storeid');
              

        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_storegrndrop()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where('auto_grn', 1);
            $this->db->where_not_in('id', 1);

        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_grn', 1)
                ->where_not_in('store.id', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    


    public function save_grn()
    {
        $items = $this->input->post('items', TRUE);
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $num = $this->number_generatorgrn($items[0]['type2']);
        $encryption_key = Config::$encryption_key;

        $query = "
        INSERT INTO grn_stock 
        (id,grn_id, date, detail, vehicleno, type, voucherno,lastupdateddate,createddate,supplier_id,type2,already) 
        VALUES 
        (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
         '{$items[0]['date']}',
         '{$items[0]['detail']}', 
           '{$items[0]['vehicleno']}',
         '{$items[0]['type']}',   
          '{$items[0]['voucherno']}',   
          '{$lastupdate}',
           '{$lastupdate}',
          '{$items[0]['supplier_id']}',
            AES_ENCRYPT('{$items[0]['type2']}', '{$encryption_key}'),0
        );";


        $this->db->trans_begin();
        try {
        $this->db->query($query);

        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;

            $query1 = "
            INSERT INTO phystock_details
            (id, product, store, stock, type, pid,date,conversion_id,unit,batch,pid2,invoicetype)
            VALUES
            (0,
             '{$item['product']}',
             '{$item['store']}',
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
             'grn_stock',
             '{$inserted_id}','{$items[0]['date']}',
              '{$conversionid}',
             '{$item['unit']}',
              '{$item['batch']}',
              '{$item['purchasedetailid']}',
               '{$item['invoicetype']}'
            );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($items[0]['date']) . ",
             'GRN',
            " . $this->db->escape($item['type']) . ",
            AES_ENCRYPT(" . $this->db->escape((string)$item['voucher_no']) . ", '{$encryption_key}'),
           AES_ENCRYPT(" . $this->db->escape((string)$num) . ", '{$encryption_key}'),
            " . $this->db->escape($inserted_id) . ",
            " . $this->db->escape($item['store']) . ",
             AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            " . $this->db->escape($lastupdate) . ",
              AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";

      

                $this->db->query($query1);
                $this->db->query($query2);
        }
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $supplier_info    =  $this->supplier_info($items[0]['supplier_id']);


        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'GRN', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

      $results=  $this->getgrnStockDetails($inserted_id);

        $data = array(
            'invoice_all_data' =>  $results,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $num,
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );


        $data['details'] = $this->load->view('stock/pos_print',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      
        $data['success'] = true;
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
        echo json_encode($data);
    }


    public function getgrnStockForPos()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    pi.product_name,
    pi.unit,u.unit_name,
    CAST(
        ROUND(
            CASE
                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') + cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') - cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') * cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') / cr2.conversion_ratio

                ELSE AES_DECRYPT(b.stock, '{$encryption_key}')
            END,
        6) AS UNSIGNED
    ) AS quantity,
    s.name AS store_name,
    a.date,
    a.vehicleno,
    CASE
        WHEN a.type = 'purchase'    THEN AES_DECRYPT(p.chalan_no, '{$encryption_key}')
        WHEN a.type = 'salesreturn' THEN AES_DECRYPT(sr.sales_return_id, '{$encryption_key}')
        ELSE NULL
    END AS voucher_no,
    CASE
        WHEN a.type = 'purchase'      THEN 'Purchase'
        WHEN a.type = 'salesreturn'   THEN 'Sales Return'
        WHEN a.type = 'storetransfer' THEN 'Store Transfer'
        ELSE a.type
    END AS type_name,

    AES_DECRYPT(a.grn_id, '{$encryption_key}') AS grn_id,
    a.supplier_id
", false);

        $this->db->from('grn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', 'left');
        $this->db->join('conversion_ratio cr2', 'cr2.conversionratio_id = b.conversion_id', 'left');
        $this->db->join('units u', 'u.unit_id = b.unit', "left");
        $this->db->join('purchase p', 'p.id = a.voucherno', 'left');
        $this->db->join('sales_return sr', 'sr.id = a.voucherno', 'left');
        $this->db->join('store s', 's.id = b.store', 'left');
        $this->db->join('product_information pi', 'pi.id = b.product', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = a.supplier_id', 'left');
        $this->db->where('b.pid', $this->input->post('id', TRUE));
        $this->db->where('b.type', 'grn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $supplier_info    =  $this->supplier_info($result[0]['supplier_id']);


        $data = array(
            'invoice_all_data' =>   $result,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $result[0]['date'],
            'invoiceno' => $result[0]['grn_id'],
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);

        echo json_encode($data);
    }

    

    public function getgrnStockDetails($id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    pi.product_name,
    pi.unit,u.unit_name,
    CAST(
        ROUND(
            CASE
                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') + cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') - cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') * cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                    THEN AES_DECRYPT(b.stock, '{$encryption_key}') / cr2.conversion_ratio

                ELSE AES_DECRYPT(b.stock, '{$encryption_key}')
            END,
        6) AS UNSIGNED
    ) AS quantity,
    s.name AS store_name,
    a.date,
    a.vehicleno,
    CASE
        WHEN a.type = 'purchase'    THEN AES_DECRYPT(p.chalan_no, '{$encryption_key}')
        WHEN a.type = 'salesreturn' THEN AES_DECRYPT(sr.sales_return_id, '{$encryption_key}')
        ELSE NULL
    END AS voucher_no,
    CASE
        WHEN a.type = 'purchase'      THEN 'Purchase'
        WHEN a.type = 'salesreturn'   THEN 'Sales Return'
        WHEN a.type = 'storetransfer' THEN 'Store Transfer'
        ELSE a.type
    END AS type_name,

    AES_DECRYPT(a.grn_id, '{$encryption_key}') AS grn_id,
    a.supplier_id
", false);

        $this->db->from('grn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', 'left');
        $this->db->join('conversion_ratio cr2', 'cr2.conversionratio_id = b.conversion_id', 'left');
        $this->db->join('units u', 'u.unit_id = b.unit', "left");
        $this->db->join('purchase p', 'p.id = a.voucherno', 'left');
        $this->db->join('sales_return sr', 'sr.id = a.voucherno', 'left');
        $this->db->join('store s', 's.id = b.store', 'left');
        $this->db->join('product_information pi', 'pi.id = b.product', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = a.supplier_id', 'left');
        $this->db->where('b.pid', $id);
        $this->db->where('b.type', 'grn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

       return $result;
    }



    public function update_grn()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->trans_begin();
        try {
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'grn_stock')
            ->delete('phystock_details');
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('scenario', 'GRN')
            ->delete('audit_stock');

        $data = array(
            'date' => $items[0]['date'],
            'detail' => $items[0]['detail'],
            'vehicleno' => $items[0]['vehicleno'],
            'voucherno' => $items[0]['voucherno'],
            'lastupdateddate' => $lastupdate,
            'supplier_id' => $items[0]['supplier_id'],
            'type' => $items[0]['type'],
            'already' => 0

        );

        $this->db->where('id', $this->input->post('id', TRUE));
        $this->db->update('grn_stock', $data);

        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;

            $query1 = "
            INSERT INTO phystock_details
            (id, product, store, stock, type, pid,date,conversion_id,unit,batch,pid2,invoicetype)
            VALUES
            (0,
             '{$item['product']}',
             '{$item['store']}',
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),
             'grn_stock',
             '{$this->input->post('id', TRUE)}','{$items[0]['date']}',
              '{$conversionid}',
             '{$item['unit']}',
              '{$item['batch']}',
              '{$item['purchasedetailid']}',
               '{$item['invoicetype']}'
            );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($items[0]['date']) . ",
             'GRN',
            " . $this->db->escape($item['type']) . ",
            AES_ENCRYPT(" . $this->db->escape((string)$item['voucher_no']) . ", '{$encryption_key}'),
          (SELECT grn_id FROM grn_stock WHERE id = " . $this->db->escape($this->input->post('id', TRUE)) . " LIMIT 1),
            " . $this->db->escape($this->input->post('id', TRUE)) . ",
            " . $this->db->escape($item['store']) . ",
             AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($item['aqty']) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT('{$item['quantity']}', '{$encryption_key}'),  
            " . $this->db->escape($lastupdate) . ",
              AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";


          $this->db->query($query1);
          $this->db->query($query2);
        }

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'grn', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}','{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $grn_id = $this->invoice_no($this->input->post('id', TRUE));
        $supplier_info    =  $this->supplier_info($items[0]['supplier_id']);


        $results=  $this->getgrnStockDetails($this->input->post('id', TRUE));

        $data = array(
            'invoice_all_data' =>   $results,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $grn_id[0]['grn_id'],
            'name'   => $supplier_info->supplier_name,
            'address' => $supplier_info->address,
            'mobile' => $supplier_info->mobile,
            'contact' => $supplier_info->contact,
            'emailnumber'  => $supplier_info->emailnumber,
            'email_address'  => $supplier_info->email_address,
            'title' => "Goods Received Note",
            'title1' => "GRN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print',  $data, true);
        $data['success'] = true;
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

        return $result = $this->db->select(" AES_DECRYPT(grn_id, '" . $encryption_key . "') AS grn_id")
            ->from('grn_stock')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function invoice_nogdn($id = null)
    {
        $encryption_key = Config::$encryption_key;

        return $result = $this->db->select(" AES_DECRYPT(gdn_id, '" . $encryption_key . "') AS gdn_id")
            ->from('gdn_stock')
            ->where('id', $id)
            ->get()
            ->result_array();
    }

    public function number_generatorgrn($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(grn_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('grn_stock');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 5000000001;
            } else {
                $invoice_no = 5500000001;
            }
        }
        return $invoice_no;
    }



    public function delete_grnstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'grn_stock')
            ->delete('phystock_details');

        $this->db->where('pid', $id)
            ->where('scenario', 'GRN')
            ->delete('audit_stock');

        $this->db->where('id', $id)
            ->delete('grn_stock');


        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
            VALUES (
                0, 
                'grn', 
                'delete', 
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();

        echo '<script type="text/javascript">
   alert("Deleted successfully");
   window.location.href = "' . $base_url . 'manage_grn";
  </script>';
    }


    public function checkgrnstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->grnstock($postData, $this->input->post('type2'), $this->input->post('storeid'),$this->input->post('fdate'),$this->input->post('tdate'));
        echo json_encode($data);
    }

    public function bdtask_manage_grn()
    {
        $data['title']      = "Manage Good Received Note";
        $data['module']     = "stock";
        $data['page']       = "manage_grn";
        if (!$this->permission1->method('manage_grn', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function getgrnStockById()
    {
        $encryption_key = Config::$encryption_key;

        if($this->input->post('type')=='purchase'){
            $this->db->select("pd.id as purchasedetailid,
            b.product,
            b.store,
            a.date AS date,
            a.detail AS details,
            AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,
            b.unit,
            a.vehicleno,
            a.voucherno,
            AES_DECRYPT(a.grn_id, '{$encryption_key}') AS grn_id,
            a.type,
            AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
            1 AS arquatity,
             (
                    SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                    FROM phystock_details c
                    WHERE b.product = c.product
                      AND b.store = c.store
                      AND b.batch = c.batch
                ) AS avstock,
            si1.supplier_id,
            b.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            b.batch,
            pi.batchtype
        ");
        
               
                $this->db->from('grn_stock a');
                $this->db->join('phystock_details b', 'b.pid = a.id', "left");
                $this->db->join('product_information pi', 'pi.id = b.product', "left");
                $this->db->join('purchase p', 'p.id = a.voucherno', "left");
                $this->db->join('purchase_details pd', 'pd.id = b.pid2', "left");
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
                $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', "left");
                $this->db->join('supplier_information si1', 'si1.supplier_id = a.supplier_id', "left");
        
                $this->db->where('b.pid', $this->input->post('pid'));
                $this->db->where('b.type', 'grn_stock');

        }

        if($this->input->post('type')=='salesreturn'){
            $this->db->select("pd.id as purchasedetailid,
            b.product,
            b.store,
            a.date AS date,
            a.detail AS details,
            AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,
            b.unit,
            a.vehicleno,
            a.voucherno,
            AES_DECRYPT(a.grn_id, '{$encryption_key}') AS grn_id,
            a.type,
            1 AS arquatity,
             (
                    SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                    FROM phystock_details c
                    WHERE b.product = c.product
                      AND b.store = c.store
                      AND b.batch = c.batch
                ) AS avstock,
            b.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            b.batch,
            pi.batchtype
        ");
        
               
                $this->db->from('grn_stock a');
                $this->db->join('phystock_details b', 'b.pid = a.id', "left");
                $this->db->join('product_information pi', 'pi.id = b.product', "left");
                $this->db->join('sales_return p', 'p.id = a.voucherno', "left");
                $this->db->join('sales_return_details pd', 'pd.id = b.pid2', "left");
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
            
        
                $this->db->where('b.pid', $this->input->post('pid'));
                $this->db->where('b.type', 'grn_stock');

        }

        if($this->input->post('type')=='storetransfer'){
            $this->db->select("
            b.product,
            b.store,
            a.date AS date,
            a.detail AS details,
            AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,
            b.unit,
            a.vehicleno,
            a.voucherno,
            AES_DECRYPT(a.grn_id, '{$encryption_key}') AS grn_id,
            a.type,
            1 AS arquatity,
             (
                    SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                    FROM phystock_details c
                    WHERE b.product = c.product
                      AND b.store = c.store
                      AND b.batch = c.batch
                ) AS avstock,
            b.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            b.batch,
            pi.batchtype
        ");
        
               
                $this->db->from('grn_stock a');
                $this->db->join('phystock_details b', 'b.pid = a.id', "left");
                $this->db->join('product_information pi', 'pi.id = b.product', "left");
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
        
                $this->db->where('b.pid', $this->input->post('pid'));
                $this->db->where('b.type', 'grn_stock');
        }

       
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }



    // GDN
    public function bdtask_manage_gdn()
    {
        $data['title']      = "Manage Good Dispatch Note";
        $data['module']     = "stock";
        $data['page']       = "manage_gdn";
        if (!$this->permission1->method('manage_gdn', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        echo modules::run('template/layout', $data);
    }

    public function bdtask_newgdn_form($id = null)
    {
        $data = array(
            'title'         => "New Good Dispatch Note",
        );
        $data['products'] = $this->active_product();
        $data['store_list'] = $this->active_storegdn();
        $data['all_customer'] = $this->customer_list();
        $data['all_employee'] = $this->employee_list();
        $data["batches"] = $this->active_batch();
        $data['units'] = $this->active_units();

        $data['id'] = $id;
        $data['module']      = 'stock';
        $data['page']    = "new_gdn";
        if (!$id) {
            if (!$this->permission1->method('manage_gdn', 'create')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        } else {
            if (!$this->permission1->method('manage_gdn', 'update')->access()) {
                $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
                redirect($previous_url);
            }
        }

        if ($id != null) {
            $data['type']   =   $this->db->select("type")->from('gdn_stock')->where('id', $id)->get()->row();

            $data['title'] = "Edit Good Dispatch Note";

        }
        echo modules::run('template/layout', $data);
    }

    public function employee_list()
    {
        // $maxid = $this->Accounts_model->getMaxFieldNumber('id', 'acc_vaucher', 'Vtype', 'DV', 'VNo');
        $query = $this->db->select('*')
            ->from('employee_history')
            ->where('status', '1')
            ->where_not_in('id',1) 
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
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

    public function customer_info($customer_id)
    {
        $encryption_key = Config::$encryption_key;

        return $this->db->select("a.customer_id as customer_id,
       AES_DECRYPT(a.customer_name, '{$encryption_key}') AS customer_name,
      AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS mobile,
       AES_DECRYPT(a.customer_address, '{$encryption_key}') AS address,
       AES_DECRYPT(a.address2, '{$encryption_key}') AS address2,
       AES_DECRYPT(a.customer_mobile, '{$encryption_key}') AS mobile,
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


    public function active_storegdn()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where_not_in('id', 1);
            $this->db->where('auto_gdn', 1);
            
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_gdn', 1)
                ->where_not_in('store.id', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function active_storegdndrop()
    {
        if ($this->session->userdata('user_level2') == 1) {
            $this->db->select('store.id,store.name AS name,0 as default');
            $this->db->from('store');
            $this->db->where('status', 1);
            $this->db->where_not_in('id', 1);
            $this->db->where('auto_gdn', 1);
        } else {
            $this->db->select("store.id,store.name AS name,sec_store.default")
                ->from('sec_store')
                ->join('store', 'store.id=sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->where('store.status', 1)
                ->where('store.auto_gdn', 1)
                ->where_not_in('store.id', 1)
                ->group_by('sec_store.storeid');
        }

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getVoucherNoSale()
    {
        $encryption_key = Config::$encryption_key;

        if ($this->input->post('type', TRUE) == "sale"||$this->input->post('type', TRUE) == "wholesale") {


            $this->db->select('p.id, AES_DECRYPT( p.sale_id  , "' . $encryption_key . '") as voucherno,AES_DECRYPT(si.customer_name  , "' . $encryption_key . '") as customer_name ');
            $this->db->from('sale_details pd');
            $this->db->join('sale p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
            $this->db->where('s.id', $this->input->post('store', TRUE));
            $this->db->where('p.status', 0);
            $this->db->where('p.incidenttype', $this->input->post('incidenttype', TRUE));

            $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));

            $this->db->group_by('voucherno');
        }

        if( $this->input->post('type', TRUE)=="purchasereturn"){
            $this->db->select('p.id,AES_DECRYPT( p.purchase_return_id , "' . $encryption_key . '")  as voucherno');
            $this->db->from('purchase_return_details pd');
            $this->db->join('purchase_return p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.rstore', 'inner');
            $this->db->where('s.id', $this->input->post('store', TRUE));
            $this->db->where('p.status', 0);
    
            $this->db->where("AES_DECRYPT(p.type2,'" . $encryption_key . "')", $this->input->post('type2', TRUE));
    
            $this->db->group_by('voucherno');

        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }

    public function getSaleByVoucherNo()
    {
        $encryption_key = Config::$encryption_key;
        $invoiceType = $this->input->post('invoicetype');
        if ($invoiceType == 3) {
            $this->db->select("
        pd.id AS saledetailid,
        AES_DECRYPT(pd.quantity, '{$encryption_key}') AS quantity, 
        p.customer_id,
        AES_DECRYPT(si.customer_name, '{$encryption_key}') AS customer_name,
        pi.id AS product_id,
        pi.product_name,
        pi.batchtype,
        pd.conversion_id,
        cr.convertiontype,
        cr.conversion_ratio,
        pd.batch,
        pd.unit,
        (
            SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
            FROM phystock_details c
            LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
            WHERE pd.product = c.product
              AND pd.store = c.store
              AND pd.batch = c.batch
        ) AS avstock,
        (
            SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
            FROM phystock_details c
            INNER JOIN gdn_stock gs ON c.pid = gs.id
            WHERE c.pid2 = pd.id 
              AND c.type = 'gdn_stock'
            AND c.invoicetype = " . $this->db->escape($invoiceType) . "
        ) AS arquatity
         
    ");
            $this->db->from('sale_details pd');
            $this->db->join('sale p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('pi.stock', 1);
            $this->db->where('pd.store', $this->input->post('store'));
        } else {
            $this->db->select("
            pd.id as saledetailid,
        AES_DECRYPT(pd.rqty, '{$encryption_key}') AS quantity,
            pi.id AS product_id,
            pi.product_name,
            pi.batchtype,
            pd.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            pd.batch,
            pd.unit,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                 LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.rstore = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN gdn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'gdn_stock'
                  AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);

            $this->db->from('purchase_return_details pd');
            $this->db->join('purchase_return p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('p.status', 0);
            $this->db->where('pi.stock', 1);
            $this->db->where('pd.rstore', $this->input->post('store'));
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function getSaleByVoucherNoAndProductId()
    {
        $encryption_key = Config::$encryption_key;
        $invoiceType = $this->input->post('invoicetype');

        if ($this->input->post('type') == "sale" || $this->input->post('type') == "wholesale") {

            $this->db->select("
        pd.id AS saledetailid,
        pd.batch,
        AES_DECRYPT(pd.quantity, '{$encryption_key}') AS quantity,
        p.customer_id,
    
        (
            SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
            FROM phystock_details c
            LEFT JOIN conversion_ratio cr2 
                ON cr2.conversionratio_id = pd.conversion_id
            WHERE pd.product = c.product
              AND pd.store = c.store
              AND pd.batch = c.batch
        ) AS avstock,
    
        (
            SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
            FROM phystock_details c
            INNER JOIN gdn_stock gs ON c.pid = gs.id
            WHERE c.pid2 = pd.id
              AND c.type = 'gdn_stock'
        ) AS arquatity
    ");

            $this->db->from('sale_details pd');
            $this->db->join('sale p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('customer_information si', 'si.customer_id = p.customer_id', 'inner');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('pd.product', $this->input->post('product'));
            $this->db->where('pd.batch', $this->input->post('batch'));
            $this->db->where('pd.id', $this->input->post('saledetailid'));
            $this->db->where('p.status', 0);
             $this->db->where('pi.stock', 1);
            $this->db->where('pd.store', $this->input->post('store'));
            $query = $this->db->get();
        }

        if ($this->input->post('type') == "purchasereturn") {
            $this->db->select("pd.id as saledetailid,
            AES_DECRYPT(pd.rqty, '{$encryption_key}') AS quantity,
            pi.id AS product_id,
            pi.product_name,pi.batchtype,
            pd.conversion_id,cr.convertiontype,cr.conversion_ratio,pd.batch,
            pd.unit,
            (
                SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.rstore = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN gdn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'gdn_stock'
                   AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);

            $this->db->from('purchase_return_details pd');
            $this->db->join('purchase_return p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            // $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('pd.product', $this->input->post('product'));
            $this->db->where('pd.batch', $this->input->post('batch'));
            $this->db->where('pd.id', $this->input->post('saledetailid'));
            $this->db->where('pi.stock', 1);
            $this->db->where('pd.rstore', $this->input->post('store'));
            $query = $this->db->get();
        }

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }




    public function save_gdn()
    {
        $items = $this->input->post('items', TRUE);
        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');
        $num = $this->number_generatorgdn($items[0]['type2']);
        $encryption_key = Config::$encryption_key;

        $query = "
        INSERT INTO gdn_stock 
        (id,gdn_id, date, detail, vehicleno, type, voucherno,lastupdateddate,createddate,type2,customer_id,employee_id,already) 
        VALUES 
        (0,  AES_ENCRYPT('{$num}', '{$encryption_key}')  , 
         '{$items[0]['date']}',
         '{$items[0]['detail']}', 
           '{$items[0]['vehicleno']}',
         '{$items[0]['type']}',   
          '{$items[0]['voucherno']}',   
          '{$lastupdate}',
          '{$lastupdate}',
            AES_ENCRYPT('{$items[0]['type2']}', '{$encryption_key}'),
             '{$items[0]['customer_id']}',
         '{$items[0]['employee_id']}',0
        );";


        $this->db->trans_begin();
        try {
        $this->db->query($query);
        $inserted_id = $this->db->insert_id();
        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            $quantity = -$item['quantity'];
            $quantitystr = "-".$item['aqty'];


            $query1= "
            INSERT INTO phystock_details
            (id, product, store, stock, type, pid,date,conversion_id,unit,batch,pid2,invoicetype) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
             'gdn_stock', 
             '{$inserted_id}','{$items[0]['date']}',
              '{$conversionid}', 
             '{$item['unit']}',
              '{$item['batch']}',
              '{$item['saledetailid']}',
               '{$item['invoicetype']}'
            );
        ";
        $query2 =  "INSERT INTO audit_stock
        (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
         astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
        VALUES (
            " . $this->db->escape($item['product']) . ",
            " . $this->db->escape($item['batch']) . ",
            " . $this->db->escape($items[0]['date']) . ",
             'GDN',
            " . $this->db->escape($item['type']) . ",
            AES_ENCRYPT(" . $this->db->escape((string)$item['voucher_no']) . ", '{$encryption_key}'),
           AES_ENCRYPT(" . $this->db->escape((string)$num) . ", '{$encryption_key}'),
            " . $this->db->escape($inserted_id) . ",
            " . $this->db->escape($item['store']) . ",
             AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT(" . $this->db->escape($quantitystr) . ", '{$encryption_key}'),
            AES_ENCRYPT('', '{$encryption_key}'),
            AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
            " . $this->db->escape($lastupdate) . ",
             AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
        );";

      

                $this->db->query($query1);
                $this->db->query($query2);
        }
        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();

        $customer_info    = $this->customer_info($items[0]['customer_id']);

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'GDN', 
            'insert', 
             '{$inserted_id}', 
            '{$this->session->userdata('id')}',  '{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $results=  $this->getgdnStockdetsils($inserted_id);


        $data = array(
            'invoice_all_data' =>  $results,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $num,
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->customer_address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print2',  $data, true);
        // $printdata       = $this->invoice_model->bdtask_invoice_pos_print_direct($inv_insert_id, "god");      
        $data['success'] = true;
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
        echo json_encode($data);
    }

    public function number_generatorgdn($type = null)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select_max("AES_DECRYPT(gdn_id,'" . $encryption_key . "')", 'id');
        $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", $type);
        $query      = $this->db->get('gdn_stock');
        $result     = $query->result_array();
        $invoice_no = $result[0]['id'];
        if ($invoice_no != '') {
            $invoice_no = $invoice_no + 1;
        } else {
            if ($type == "A") {
                $invoice_no = 6000000001;
            } else {
                $invoice_no = 6600000001;
            }
        }
        return $invoice_no;
    }



    public function update_gdn()
    {
        $items = $this->input->post('items', TRUE);
        $encryption_key = Config::$encryption_key;

        date_default_timezone_set('Asia/Colombo');
        $lastupdate = date('Y-m-d H:i:s');

        $this->db->trans_begin();
        try {
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('type', 'gdn_stock')
            ->delete('phystock_details');
        $this->db->where('pid', $this->input->post('id', TRUE))
            ->where('scenario', 'GDN')
            ->delete('audit_stock');

        $data = array(
            'date' => $items[0]['date'],
            'detail' => $items[0]['detail'],
            'vehicleno' => $items[0]['vehicleno'],
            'voucherno' => $items[0]['voucherno'],
            'lastupdateddate' => $lastupdate,
            'type' => $items[0]['type'],
            'customer_id' => $items[0]['customer_id'],
            'employee_id' => $items[0]['employee_id'],
            'already' => 0
        );

        $this->db->where('id', $this->input->post('id', TRUE));
        $this->db->update('gdn_stock', $data);

        foreach ($items as $item) {
            $conversionid = isset($item['conversionid']) && $item['conversionid'] !== '' ? (int)$item['conversionid'] : 0;
            $quantity = -$item['quantity'];
            $quantitystr = "-".$item['aqty'];


            $query1 = "INSERT INTO phystock_details
            (id, product, store, stock, type, pid,date,conversion_id,unit,batch,pid2,invoicetype) 
            VALUES 
            (0, 
             '{$item['product']}', 
             '{$item['store']}', 
            AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
             'gdn_stock', 
 '{$this->input->post('id', TRUE)}','{$items[0]['date']}',
  '{$conversionid}', 
             '{$item['unit']}',
              '{$item['batch']}',
              '{$item['saledetailid']}',
               '{$item['invoicetype']}'
            );
        ";
              $query2 =  "INSERT INTO audit_stock
              (product, batch, date, scenario, incident, pvoucher, voucher, pid, store,
               astockstr, pstockstr, astock, pstock, lastupdateddate,type2)
              VALUES (
                  " . $this->db->escape($item['product']) . ",
                  " . $this->db->escape($item['batch']) . ",
                  " . $this->db->escape($items[0]['date']) . ",
                   'GDN',
                  " . $this->db->escape($item['type']) . ",
                  AES_ENCRYPT(" . $this->db->escape((string)$item['voucher_no']) . ", '{$encryption_key}'),
                (SELECT gdn_id FROM gdn_stock WHERE id = " . $this->db->escape($this->input->post('id', TRUE)) . " LIMIT 1),
                  " . $this->db->escape($this->input->post('id', TRUE)) . ",
                  " . $this->db->escape($item['store']) . ",
                   AES_ENCRYPT('', '{$encryption_key}'),
                  AES_ENCRYPT(" . $this->db->escape($quantitystr) . ", '{$encryption_key}'),
                  AES_ENCRYPT('', '{$encryption_key}'),
                AES_ENCRYPT('{$quantity}', '{$encryption_key}'),  
                  " . $this->db->escape($lastupdate) . ",
                  AES_ENCRYPT('{$this->input->post('type2', TRUE)}', '{$encryption_key}')
              );";
      
      
                $this->db->query($query1);
                $this->db->query($query2);
        }


        $lastupdate = date('Y-m-d H:i:s');

        $query = "
        INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
        VALUES (
            0, 
            'gdn', 
            'update', 
            '{$this->input->post('id', TRUE)}', 
            '{$this->session->userdata('id')}','{$lastupdate}'
        );
    ";

        $this->db->query($query);

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $grn_id = $this->invoice_nogdn($this->input->post('id', TRUE));
        $customer_info    = $this->customer_info($items[0]['customer_id']);
        $results=  $this->getgdnStockdetsils($this->input->post('id', TRUE));



        $data = array(
            'invoice_all_data' =>  $results,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $this->input->post('date', TRUE),
            'invoiceno' => $grn_id[0]['gdn_id'],
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'ptype' => "",
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print2',  $data, true);
        $data['success'] = true;
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
        echo json_encode($data);
    }

    public function checkgdnstock()
    {
        $postData = $this->input->post();
        $data = $this->stock_model->gdnstock($postData, $this->input->post('type2'), $this->input->post('storeid'),$this->input->post('fdate'),$this->input->post('tdate'));

        echo json_encode($data);
    }

    public function delete_gdnstock($id = null)
    {

        $this->db->where('pid', $id)
            ->where('type', 'gdn_stock')
            ->delete('phystock_details');
        $this->db->where('pid', $id)
            ->where('scenario', 'GDN')
            ->delete('audit_stock');

        $this->db->where('id', $id)
            ->delete('gdn_stock');

        $lastupdate = date('Y-m-d H:i:s');

        $query = "
            INSERT INTO logs (id, screen, operation, pid, userid,lastupdatedate) 
            VALUES (
                0, 
                'gdn', 
                'delete', 
                '{$id}', 
                '{$this->session->userdata('id')}','{$lastupdate}'
            );
        ";

        $this->db->query($query);
        $base_url = base_url();

        echo '<script type="text/javascript">
        alert("Deleted successfully");
        window.location.href = "' . $base_url . 'manage_gdn";
       </script>';
    }

    public function getgdnStockById()
    {
        $encryption_key = Config::$encryption_key;

        if ($this->input->post('type') == 'sale'||$this->input->post('type') == 'wholesale') {


            $this->db->select("
        pd.id AS saledetailid,
        b.product,
        b.store,
        a.date AS date,
        a.detail AS details,
       AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,
        b.unit,
        a.vehicleno,
        a.voucherno,
        AES_DECRYPT(a.gdn_id, '{$encryption_key}') AS gdn_id,
        a.type,
        AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
        (
            SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
            FROM phystock_details c
            WHERE b.product = c.product
              AND b.store = c.store
              AND b.batch = c.batch
        ) AS avstock,
        a.employee_id,
        si1.customer_id,
        b.conversion_id,
        cr.convertiontype,
        cr.conversion_ratio,
        b.batch,
        pi.batchtype
    ");
            $this->db->from('gdn_stock a');
            $this->db->join('phystock_details b', 'b.pid = a.id');
            $this->db->join('product_information pi', 'pi.id = b.product');
            $this->db->join('sale s', 's.id = a.voucherno', 'left');
            $this->db->join('sale_details pd', 'pd.id = b.pid2', 'left');
            $this->db->join('customer_information ci', 'ci.customer_id = s.customer_id', 'left');
            $this->db->join('customer_information si1', 'si1.customer_id = a.customer_id', 'left');
            $this->db->join('conversion_ratio cr', 'b.conversion_id = cr.conversionratio_id', 'left');

            $this->db->where('b.pid', $this->input->post('pid'));
            $this->db->where('b.type', 'gdn_stock');
        }

        if ($this->input->post('type') == 'purchasereturn') {

            $this->db->select("
    pd.id AS saledetailid,
    b.product,
    b.store,
    a.date AS date,
    a.detail AS details,

    AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,

    b.unit,
    a.vehicleno,
    a.voucherno,
    AES_DECRYPT(a.gdn_id, '{$encryption_key}') AS gdn_id,
    a.type,

    1 AS arquatity,

    (
        SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
        FROM phystock_details c
        WHERE b.product = c.product
          AND b.store = c.store
          AND b.batch = c.batch
    ) AS avstock,

    b.conversion_id,
    cr.convertiontype,
    cr.conversion_ratio,

    b.batch,
    pi.batchtype
", false);

            $this->db->from('gdn_stock a');

            $this->db->join('phystock_details b', 'b.pid = a.id', 'left');

            $this->db->join('product_information pi', 'pi.id = b.product', 'left');

            $this->db->join('purchase_return p', 'p.id = a.voucherno', 'left');

            $this->db->join('purchase_return_details pd', 'pd.id = b.pid2', 'left');

            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');

            $this->db->where('b.pid', $this->input->post('pid'));
            $this->db->where('b.type', 'gdn_stock');
        }

        if($this->input->post('type')=='storetransfer'||$this->input->post('type')=='stockdisposal'){
            $this->db->select("
            b.product,
            b.store,
            a.date AS date,
            a.detail AS details,
            AES_DECRYPT(b.stock, '{$encryption_key}') AS actualstock,
            b.unit,
            a.vehicleno,
            a.voucherno,
            AES_DECRYPT(a.gdn_id, '{$encryption_key}') AS gdn_id,
            a.type,
            1 AS arquatity,
             (
                    SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                    FROM phystock_details c
                    WHERE b.product = c.product
                      AND b.store = c.store
                      AND b.batch = c.batch
                ) AS avstock,
            b.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            b.batch,
            pi.batchtype
        ");
        
               
                $this->db->from('gdn_stock a');
                $this->db->join('phystock_details b', 'b.pid = a.id', "left");
                $this->db->join('product_information pi', 'pi.id = b.product', "left");
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = b.conversion_id', 'left');
        
                $this->db->where('b.pid', $this->input->post('pid'));
                $this->db->where('b.type', 'gdn_stock');
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }



    public function getPurchaseByVoucherNo()
    {
        $encryption_key = Config::$encryption_key;
        $invoiceType = $this->input->post('invoicetype');


        if ($invoiceType == 1) {
            $this->db->select("
            pd.id as purchasedetailid,
        AES_DECRYPT(pd.quantity, '{$encryption_key}') AS quantity,
            p.supplier_id,
            CAST(AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS CHAR) AS supplier_name,
            pi.id AS product_id,
            pi.product_name,
            pi.batchtype,
            pd.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            pd.batch,
            pd.unit,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                 LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.store = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN grn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'grn_stock'
                  AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);

            $this->db->from('purchase_details pd');
            $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('p.status', 0);
             $this->db->where('pi.stock', 1);

            $this->db->where('pd.store', $this->input->post('store'));
        }else{
            $this->db->select("
            pd.id as purchasedetailid,
        AES_DECRYPT(pd.rqty, '{$encryption_key}') AS quantity,
            pi.id AS product_id,
            pi.product_name,
            pi.batchtype,
            pd.conversion_id,
            cr.convertiontype,
            cr.conversion_ratio,
            pd.batch,
            pd.unit,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                 LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.rstore = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
        
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN grn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'grn_stock'
                  AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);

            $this->db->from('sales_return_details pd');
            $this->db->join('sales_return p', 'p.id = pd.pid', 'inner');
            $this->db->join('store s', 's.id = pd.store', 'inner');
            $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
            $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
            $this->db->where('p.id', $this->input->post('voucherno'));
            $this->db->where('p.status', 0);
             $this->db->where('pi.stock', 1);
            $this->db->where('pd.rstore', $this->input->post('store'));

        }




       
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
    }

    public function getPurchaseByVoucherNoAndProductId()
    {
        $encryption_key = Config::$encryption_key;
    //     $this->db->select("
    //     AES_DECRYPT(pd.quantity, '{$encryption_key}') AS quantity,
    
    //     (
    //         SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
    //         FROM phystock_details c
    //         WHERE pd.product = c.product
    //           AND pd.store = c.store
    //          AND pd.batch = c.batch

    //     ) AS avstock,
    
    //     (
    //         SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}'))
    //         FROM phystock_details c
    //         INNER JOIN grn_stock gs ON c.pid = gs.id
    //         WHERE gs.voucherno=pd.pid and c.pid2 = pd.id
    //           AND c.type = 'grn_stock'
    //     ) AS arquatity
    // ");
    //     $this->db->from('purchase_details pd');
    //     $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
    //     $this->db->join('store s', 's.id = pd.store', 'inner');
    //     $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
    //     $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
    //     $this->db->where('p.id', $this->input->post('voucherno'));
    //     $this->db->where('pd.product', $this->input->post('product'));
    //     $this->db->where('p.status', 0);
    //     $this->db->where('pd.store', $this->input->post('store'));
    $invoiceType = $this->input->post('invoicetype');

        if($this->input->post('type')=="purchase"){
            $this->db->select("pd.id as purchasedetailid,
            AES_DECRYPT(pd.quantity, '{$encryption_key}') AS quantity,
            p.supplier_id,
            AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
            pi.id AS product_id,
            pi.product_name,pi.batchtype,
            pd.conversion_id,cr.convertiontype,cr.conversion_ratio,pd.batch,
            pd.unit,
            (
                SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.store = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN grn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'grn_stock'
                   AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);
        
                $this->db->from('purchase_details pd');
                $this->db->join('purchase p', 'p.id = pd.pid', 'inner');
                $this->db->join('store s', 's.id = pd.store', 'inner');
                $this->db->join('supplier_information si', 'si.supplier_id = p.supplier_id', 'inner');
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
                $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
                $this->db->where('p.id', $this->input->post('voucherno'));
                $this->db->where('pd.product', $this->input->post('product'));
                $this->db->where('pd.batch', $this->input->post('batch'));
                $this->db->where('pd.id', $this->input->post('purchasedetailid'));
                $this->db->where('p.status', 0);
                   $this->db->where('pi.stock', 1);
                $this->db->where('pd.store', $this->input->post('store'));
                $query = $this->db->get();

        }


        if($this->input->post('type')=="salesreturn"){
            $this->db->select("pd.id as purchasedetailid,
            AES_DECRYPT(pd.rqty, '{$encryption_key}') AS quantity,
            pi.id AS product_id,
            pi.product_name,pi.batchtype,
            pd.conversion_id,cr.convertiontype,cr.conversion_ratio,pd.batch,
            pd.unit,
            (
                SELECT  SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = pd.conversion_id
                WHERE pd.product = c.product
                  AND pd.rstore = c.store
                  AND pd.batch = c.batch
            ) AS avstock,
            (
                SELECT SUM(AES_DECRYPT(c.stock, '{$encryption_key}')) 
                FROM phystock_details c
                INNER JOIN grn_stock gs ON c.pid = gs.id
                WHERE c.pid2 = pd.id 
                  AND c.type = 'grn_stock'
                   AND c.invoicetype = " . $this->db->escape($invoiceType) . "
            ) AS arquatity
        ", false);
        
        $this->db->from('sales_return_details pd');
        $this->db->join('sales_return p', 'p.id = pd.pid', 'inner');
                $this->db->join('store s', 's.id = pd.store', 'inner');
                $this->db->join('conversion_ratio cr', 'cr.conversionratio_id = pd.conversion_id', 'left');
                $this->db->join('product_information pi', 'pi.id = pd.product', 'inner');
                // $this->db->where('p.id', $this->input->post('voucherno'));
                $this->db->where('pd.product', $this->input->post('product'));
                $this->db->where('pd.batch', $this->input->post('batch'));
                $this->db->where('pd.id', $this->input->post('purchasedetailid'));
                $this->db->where('pi.stock', 1);
                $this->db->where('pd.rstore', $this->input->post('store'));
                $query = $this->db->get();

        }


       
    
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }
        // $query = $this->db->get();

        // if ($query->num_rows() > 0) {
        //     echo json_encode($query->result_array());
        // }
    }



    public function avg_phystock()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select('sum(AES_DECRYPT( sd.stock , "' . $encryption_key . '"))  as avgqty');
        $this->db->from('phystock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('store s', 's.id = sd.store', 'inner');
        $this->db->where('pi.id', $this->input->post('prodid', TRUE));
        $this->db->where('sd.batch', $this->input->post('batch', TRUE));
        $this->db->where('s.id', $this->input->post('storeid', TRUE));

        // $this->db->where('f.id', $this->input->post('floorid', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }
        return false;
    }


    public function getgdnStockForPos()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    pi.product_name,
    pi.unit,u.unit_name,
    CAST(
        ROUND(
            CASE
                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) + cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) - cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) * cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) / cr2.conversion_ratio

                ELSE ABS(AES_DECRYPT(b.stock, '{$encryption_key}'))
            END,
        6) AS UNSIGNED
    ) AS quantity,
    s.name AS store_name,
    a.date,
    a.vehicleno,
    CASE
        WHEN a.type IN ('sale', 'wholesale') THEN AES_DECRYPT(sa.sale_id, '{$encryption_key}')
        WHEN a.type = 'purchasereturn' THEN AES_DECRYPT(sr.purchase_return_id, '{$encryption_key}')
        ELSE NULL
    END AS voucher_no,
    CASE
        WHEN a.type = 'sale' THEN 'Sale'
        WHEN a.type = 'wholesale' THEN 'Wholesale'
        WHEN a.type = 'storetransfer' THEN 'Store Transfer'
        WHEN a.type = 'purchasereturn' THEN 'Sales Return'
        WHEN a.type = 'stockdisposal' THEN 'Stock Disposal'
        ELSE a.type
    END AS type_name,
    AES_DECRYPT(a.gdn_id, '{$encryption_key}') AS gdn_id,
    a.customer_id
", false);

        $this->db->from('gdn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', 'left');
        $this->db->join('conversion_ratio cr2', 'cr2.conversionratio_id = b.conversion_id', 'left');
        $this->db->join('sale sa', 'sa.id = a.voucherno', 'left');
        $this->db->join('purchase_return sr', 'sr.id = a.voucherno', 'left');
        $this->db->join('units u', 'u.unit_id = b.unit', "left");
        $this->db->join('store s', 's.id = b.store', 'left');
        $this->db->join('product_information pi', 'pi.id = b.product', 'left');

        $this->db->where('b.pid', $this->input->post('id', TRUE));
        $this->db->where('b.type', 'gdn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

        $company_info     = $this->service_model->company_info();
        $currency_details = $this->service_model->web_setting();
        $customer_info    = $this->customer_info($result[0]['customer_id']);


        $data = array(
            'invoice_all_data' =>   $result,
            'company_info'    => $company_info,
            'currency_details' => $currency_details,
            'date'    => $result[0]['date'],
            'invoiceno' => $result[0]['gdn_id'],
            'title' => "Goods Dispatch Notes",
            'name'   => $customer_info->customer_name,
            'address' => $customer_info->address,
            'mobile' => $customer_info->mobile,
            'contact' => $customer_info->contact,
            'email_address'  => $customer_info->email_address,
            'title1' => "GDN No:",

        );

        $data['details'] = $this->load->view('stock/pos_print2',  $data, true);

        echo json_encode($data);
    }

    public function getgdnStockdetsils($id)
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
    pi.product_name,
    pi.unit,u.unit_name,
    CAST(
        ROUND(
            CASE
                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) + cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) - cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) * cr2.conversion_ratio

                WHEN b.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                    THEN ABS(AES_DECRYPT(b.stock, '{$encryption_key}')) / cr2.conversion_ratio

                ELSE ABS(AES_DECRYPT(b.stock, '{$encryption_key}'))
            END,
        6) AS UNSIGNED
    ) AS quantity,
    s.name AS store_name,
    a.date,
    a.vehicleno,
    CASE
        WHEN a.type IN ('sale', 'wholesale') THEN AES_DECRYPT(sa.sale_id, '{$encryption_key}')
        WHEN a.type = 'purchasereturn' THEN AES_DECRYPT(sr.purchase_return_id, '{$encryption_key}')
        ELSE NULL
    END AS voucher_no,
    CASE
        WHEN a.type = 'sale' THEN 'Sale'
        WHEN a.type = 'wholesale' THEN 'Wholesale'
        WHEN a.type = 'storetransfer' THEN 'Store Transfer'
        WHEN a.type = 'purchasereturn' THEN 'Purchase Return'
        ELSE a.type
    END AS type_name,
    AES_DECRYPT(a.gdn_id, '{$encryption_key}') AS gdn_id,
    a.customer_id
", false);

        $this->db->from('gdn_stock a');
        $this->db->join('phystock_details b', 'b.pid = a.id', 'left');
        $this->db->join('conversion_ratio cr2', 'cr2.conversionratio_id = b.conversion_id', 'left');
        $this->db->join('sale sa', 'sa.id = a.voucherno', 'left');
        $this->db->join('purchase_return sr', 'sr.id = a.voucherno', 'left');
        $this->db->join('units u', 'u.unit_id = b.unit', "left");
        $this->db->join('store s', 's.id = b.store', 'left');
        $this->db->join('product_information pi', 'pi.id = b.product', 'left');

        $this->db->where('b.pid', $id);
        $this->db->where('b.type', 'gdn_stock');
        $query = $this->db->get();
        $result = $query->result_array();

       
       return  $result;
    }


    public function conversion()
    {

        $encryption_key = Config::$encryption_key;
        $this->db->select('cr.conversionratio_id,cr.convertiontype,cr.conversion_ratio,cr.bd,cr.ad,cr.addigit,
        AES_DECRYPT( sp.subsell_price , "' . $encryption_key . '") AS subsell_price,  
        AES_DECRYPT( sp.subcost_price , "' . $encryption_key . '") AS subcost_price,sp.first');
        $this->db->from('subunit_product sp');
        $this->db->join('units u', 'u.unit_id = sp.unit_id');
        $this->db->join('conversion_ratio cr', 'cr.subunit	 = sp.unit_id');
        $this->db->where('cr.product', $this->input->post('product_id', TRUE));
        $this->db->where('cr.subunit', $this->input->post('unit', TRUE));
        $this->db->where('sp.product_id', $this->input->post('product_id', TRUE));
        $this->db->where('sp.unit_id', $this->input->post('unit', TRUE));
        $this->db->where('u.status', 1);
       // $this->db->where('cr.status', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo "not";
        }
    }

    public function getBatchPrice(){

        $encryption_key = Config::$encryption_key;
        $this->db->select('id,AES_DECRYPT( mrp , "' . $encryption_key . '")  as mrp');
        $this->db->from('stockbatch');
        $this->db->where('busage', "single");
        $this->db->where('product', $this->input->post('product', TRUE));
        $this->db->where('id', $this->input->post('batch', TRUE));
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        }

    }

     public function getBatchbyProductAndBatchtype()
    {
        $encryption_key = Config::$encryption_key;
        $currentDate = date('Y-m-d');
        if ($this->input->post('batchtype', TRUE) == 0) {
            $this->db->select('id,AES_DECRYPT( batchid , "' . $encryption_key . '")  as batchid');
            $this->db->from('stockbatch');
            $this->db->where('id', 1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            } else {
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 1) {
            $this->db->select('id,AES_DECRYPT( batchid , "' . $encryption_key . '")  as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', "single");
            $this->db->where('product', $this->input->post('product', TRUE));
            if (!$this->input->post('id', TRUE)) {

                $this->db->where('status', 1);
            }            // $this->db->where('edate >=', $currentDate);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 2) {

            $this->db->select('id,AES_DECRYPT( batchid , "' . $encryption_key . '")  as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', "multiple");
            if (!$this->input->post('id', TRUE)) {

                $this->db->where('status', 1);
            }
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 3) {


            if (!$this->input->post('id', TRUE)) {
                $query1 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'single')
                    ->where('product', $this->input->post('product', TRUE))
                    ->where('mdate <=', $currentDate)
                    ->group_start()
                    ->where('edate_enabled', 0)
                    ->or_where('edate >=', $currentDate)
                    ->group_end()
                    ->where('status', 1)->get_compiled_select();
            } else {
                $query1 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'single')
                    ->where('product', $this->input->post('product', TRUE))
                    ->get_compiled_select();
            }

            if (!$this->input->post('id', TRUE)) {
                $query2 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'multiple')
                    ->where('status', 1)
                    ->get_compiled_select();
            } else {
                $query2 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'multiple')
                    ->get_compiled_select();
            }



            $finalQuery = $this->db->query("$query1 UNION $query2");
            if ($finalQuery->num_rows() > 0) {
                echo json_encode($finalQuery->result_array());
            } else {
                echo "not";
            }
        }



        return false;
    }

    public function getBatchInStockByProductAndBatchtype()
    {
        $encryption_key = Config::$encryption_key;
        $currentDate    = date('Y-m-d');
        $product_id     = $this->input->post('product', TRUE);
        $escaped_pid = $this->db->escape($product_id);

        $stock_filter_single = "
            (stockbatch.id = 1 OR (
                SELECT IFNULL(SUM(CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(18,4))), 0)
                  FROM stock_details sd
                 WHERE sd.batch   = stockbatch.id
                   AND sd.product = CAST(stockbatch.product AS CHAR)
            ) > 0)";

        $stock_filter_multi = "
            (stockbatch.id = 1 OR (
                SELECT IFNULL(SUM(CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(18,4))), 0)
                  FROM stock_details sd
                 WHERE sd.batch   = stockbatch.id
                   AND sd.product = {$escaped_pid}
            ) > 0)";

        if ($this->input->post('batchtype', TRUE) == 0) {
            // batchtype 0: only the default batch (id=1) should be available
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid');
            $this->db->from('stockbatch');
            $this->db->where('id', 1);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            } else {
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 1) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', 'single');
            $this->db->where('product', $product_id);
            if (!$this->input->post('id', TRUE)) {
                $this->db->where('status', 1);
            }
            $this->db->where($stock_filter_single, NULL, FALSE);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            } else {
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 2) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', 'multiple');
            if (!$this->input->post('id', TRUE)) {
                $this->db->where('status', 1);
            }
            $this->db->where($stock_filter_multi, NULL, FALSE);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            } else {
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 3) {
            if (!$this->input->post('id', TRUE)) {
                $q1 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'single')
                    ->where('product', $product_id)
                    ->where('mdate <=', $currentDate)
                    ->group_start()
                    ->where('edate_enabled', 0)
                    ->or_where('edate >=', $currentDate)
                    ->group_end()
                    ->where('status', 1)
                    ->where($stock_filter_single, NULL, FALSE);
                $query1 = $q1->get_compiled_select();
            } else {
                $q1 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'single')
                    ->where('product', $product_id)
                    ->where($stock_filter_single, NULL, FALSE);
                $query1 = $q1->get_compiled_select();
            }

            if (!$this->input->post('id', TRUE)) {
                $q2 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'multiple')
                    ->where('status', 1)
                    ->where($stock_filter_multi, NULL, FALSE);
                $query2 = $q2->get_compiled_select();
            } else {
                $q2 = $this->db
                    ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                    ->from('stockbatch')
                    ->where('busage', 'multiple')
                    ->where($stock_filter_multi, NULL, FALSE);
                $query2 = $q2->get_compiled_select();
            }

            $finalQuery = $this->db->query("$query1 UNION $query2");
            if ($finalQuery->num_rows() > 0) {
                echo json_encode($finalQuery->result_array());
            } else {
                echo "not";
            }
        }

        return false;
    }

     public function getBatchbyProductAndBatchtype2()
    {
        $encryption_key = Config::$encryption_key;
        $active_batch_filter = '(edate IS NULL OR edate = \'\' OR edate >= CURDATE())';
        if ($this->input->post('batchtype', TRUE) == 1) {
            $this->db->select('id,AES_DECRYPT( batchid , "' . $encryption_key . '")  as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', "single");
            $this->db->where('product', $this->input->post('product', TRUE));
            $this->db->where('status', 1);
            $this->db->where($active_batch_filter, null, false);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 2) {

            $this->db->select('id,AES_DECRYPT( batchid , "' . $encryption_key . '")  as batchid');
            $this->db->from('stockbatch');
            $this->db->where('busage', "multiple");
            $this->db->where('status', 1);
            $this->db->where($active_batch_filter, null, false);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 3) {

            $query1 = $this->db
                ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                ->from('stockbatch')
                ->where('busage', 'single')
                ->where('product', $this->input->post('product', TRUE))
                ->where('status', 1)
                ->where($active_batch_filter, null, false)
                ->get_compiled_select();



            $query2 = $this->db
                ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid', FALSE)
                ->from('stockbatch')
                ->where('busage', 'multiple')
                ->where('status', 1)
                ->where($active_batch_filter, null, false)
                ->get_compiled_select();
            

            $finalQuery = $this->db->query("$query1 UNION $query2");
            if ($finalQuery->num_rows() > 0) {
                echo json_encode($finalQuery->result_array());
            } else {
                echo "not";
            }
        }


       
        return false;
    }

    public function usage_productjson()
    {

        if($this->input->post('batchtype', TRUE) == 1){
            $this->db->select('id,product_name');
            $this->db->from('product_information');
            $this->db->where('status', 1);
            $this->db->where_in('batchtype', [1, 3]);
    
    
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
    
                echo json_encode($query->result_array());
    
            }
        }

        if($this->input->post('batchtype', TRUE) == 2){
            $this->db->select('id,product_name');
            $this->db->from('product_information');
            $this->db->where('status', 1);
            $this->db->where_in('batchtype', [2, 3]);
    
    
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
    
                echo json_encode($query->result_array());
    
            }
        }
       
       
        return false;
    }

    public function getBatchbyBatchtype()
    {
        $encryption_key = Config::$encryption_key;
        // $currentDate = date('Y-m-d');
        if ($this->input->post('batchtype', TRUE) == 1) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE);
            $this->db->from('stockbatch');
            $this->db->where('busage', "single");
            $this->db->where('product', $this->input->post('product', TRUE));
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 2) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE);
            $this->db->from('stockbatch');
            $this->db->where('busage', "multiple");
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                echo json_encode($query->result_array());
            }else{
                echo "not";
            }
        } else if ($this->input->post('batchtype', TRUE) == 3) {
            $query1 = $this->db
                ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE)
                ->from('stockbatch')
                ->where('busage', 'single')
                ->where('product', $this->input->post('product', TRUE))
                ->get_compiled_select();

            $query2 = $this->db
                ->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE)
                ->from('stockbatch')
                ->where('busage', 'multiple')
                ->get_compiled_select();
            $finalQuery = $this->db->query("$query1 UNION $query2");
            if ($finalQuery->num_rows() > 0) {
                echo json_encode($finalQuery->result_array());
            }else{
                echo "not";
            }
        }


       
        return false;
    }

    /* ── Live Stock Report: product dropdown (all statuses) ── */
    public function getLivestockProductjson()
    {
        $batchtype = $this->input->post('batchtype', TRUE);
        if ($batchtype == 1) {
            $this->db->select('id, product_name, status');
            $this->db->from('product_information');
            $this->db->where_in('batchtype', [1, 3]);
            $query = $this->db->get();
        } elseif ($batchtype == 2) {
            $this->db->select('id, product_name, status');
            $this->db->from('product_information');
            $this->db->where_in('batchtype', [2, 3]);
            $query = $this->db->get();
        } else {
            echo "not"; return;
        }
        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo "not";
        }
    }

    /* ── Live Stock Report: batch dropdown (all statuses, labelled) ── */
    public function getLivestockBatchbyBatchtype()
    {
        $encryption_key = Config::$encryption_key;
        $batchtype      = $this->input->post('batchtype', TRUE);
        $product        = $this->input->post('product', TRUE);

        if ($batchtype == 1) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status');
            $this->db->from('stockbatch');
            $this->db->where('busage', 'single');
            $this->db->where('product', $product);
            $query = $this->db->get();
        } elseif ($batchtype == 2) {
            $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status');
            $this->db->from('stockbatch');
            $this->db->where('busage', 'multiple');
            $query = $this->db->get();
        } elseif ($batchtype == 3) {
            $q1 = $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE)
                ->from('stockbatch')->where('busage', 'single')->where('product', $product)->get_compiled_select();
            $q2 = $this->db->select('id, AES_DECRYPT(batchid, "' . $encryption_key . '") as batchid, status', FALSE)
                ->from('stockbatch')->where('busage', 'multiple')->get_compiled_select();
            $query = $this->db->query("$q1 UNION $q2");
        } else {
            echo "not"; return;
        }

        if ($query->num_rows() > 0) {
            echo json_encode($query->result_array());
        } else {
            echo "not";
        }
    }

    public function is_grnorgdnthere(){

        $this->db->select("po.id");
        $this->db->from('phystock_details po');
        $this->db->where('po.pid2', $this->input->post('id'));
        $this->db->where('po.invoicetype', $this->input->post('invoicetype'));



        $query = $this->db->get();

        echo json_encode($query->result_array());


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

    // Unit Cost Logic Specification v1.1 (Phase 8, UCL-90–UCL-93) — one-time migration tooling.
    // Gated the same way this controller already gates its other full-access-only branches
    // (`user_level2 == 1`) rather than a new permission row, since this is a single, rarely-used
    // admin action, not a day-to-day screen.
    private function _require_full_access()
    {
        if ($this->session->userdata('user_level2') != 1) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Not authorized.']);
            return false;
        }
        return true;
    }

    public function unit_cost_migration()
    {
        if ($this->session->userdata('user_level2') != 1) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']  = 'Unit Cost Migration';
        $data['module'] = "stock";
        $data['page']   = "unit_cost_migration";
        echo modules::run('template/layout', $data);
    }

    public function get_uncosted_opening_stock()
    {
        if (!$this->_require_full_access()) return;
        echo json_encode(['success' => true, 'rows' => $this->unitcost_model->find_uncosted_opening_stock()]);
    }

    public function save_opening_stock_cost_ajax()
    {
        if (!$this->_require_full_access()) return;
        $result = $this->unitcost_model->set_opening_stock_cost(
            $this->input->post('stock_details_id', TRUE),
            $this->input->post('unit_cost', TRUE)
        );
        echo json_encode($result);
    }

    public function run_unit_cost_migration_ajax()
    {
        if (!$this->_require_full_access()) return;
        echo json_encode($this->unitcost_model->rebuild_from_history());
    }

}