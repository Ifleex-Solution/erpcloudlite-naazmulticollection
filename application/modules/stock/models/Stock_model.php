<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Stock_model extends CI_Model
{


    // stock batch part
    public function create_stockbatch($data = [])
    {
        return $this->db->insert('stockbatch', $data);
    }

    public function stockbatchlist($postData = null)
{
    $encryption_key = Config::$encryption_key;

    $response = array();

    ## Read value
    $draw = $postData['draw'];
    $start = $postData['start'];
    $rowperpage = $postData['length']; // Rows display per page
    $columnIndex = isset($postData['order'][0]['column']) ? $postData['order'][0]['column'] : 0;
    $columnName = isset($postData['columns'][$columnIndex]['data']) ? $postData['columns'][$columnIndex]['data'] : 'id';
    $columnSortOrder = isset($postData['order'][0]['dir']) ? $postData['order'][0]['dir'] : 'desc';
    $searchValue = $postData['search']['value']; // Search value

    ## Search 
    $searchQuery = "";
    if ($searchValue != '') {
        $searchQuery = " (AES_DECRYPT(a.batchid,'".$encryption_key."') like '%" . $searchValue . "%' 
                         or a.details like '%" . $searchValue . "%' 
                         or a.busage like '%" . $searchValue . "%') ";
    }

    ## Total number of records without filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('stockbatch a');
    $records = $this->db->get()->result();
    $totalRecords = $records[0]->allcount;

    ## Total number of record with filtering
    $this->db->select('count(*) as allcount');
    $this->db->from('stockbatch a');
    if ($searchValue != '')
        $this->db->where($searchQuery);
    $records = $this->db->get()->result();
    $totalRecordwithFilter = $records[0]->allcount;

    ## Fetch records
    $this->db->select("a.id,
                       a.details,
                       AES_DECRYPT(a.batchid, '" . $encryption_key . "') AS batchid,
                       a.busage,
                       CASE
                           WHEN a.busage = 'single' THEN 'Single Usage'
                           WHEN a.busage = 'multiple' THEN 'Multiple Usage'
                           ELSE a.busage
                       END AS busage_label,
                       a.status,
                       IF(a.status = 1, 'Active', 'Inactive') as status_label,
                       p.product_name,
                       a.edate", false);
    $this->db->from('stockbatch a');
    $this->db->join('product_information p', 'p.id = a.product', 'left');
    if ($searchValue != '')
        $this->db->where($searchQuery);
    $this->db->order_by("CASE WHEN a.id = 1 THEN 0 ELSE 1 END", "ASC", false);
    $this->db->order_by("a.id", "DESC");
     $this->db->limit($rowperpage, $start);
    $records = $this->db->get()->result();
    
    $data = array();
    $sl = $start + 1;

    // Get CI instance to access permission
    $CI =& get_instance();
    $CI->load->library('permission1');

    foreach ($records as $record) {

        $button = '';
        $base_url = base_url();
        $jsaction = "return confirm('Are You Sure ?')";
        
        if ($CI->permission1->method('stockbatchlist', 'update')->access()) {
            $button .= '<a href="' . $base_url . 'stockbatch_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
        }
        
        if ($CI->permission1->method('stockbatchlist', 'delete')->access()) {
            if($record->id!=1){
                $button .= '<a href="' . $base_url . 'stock/stock/bdtask_deletestockbatch/' . $record->id . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }
        }

        if($record->status_label == "Active"){
            $status = '<span class="label label-success">'.$record->status_label.'</span>';
        } else {
            $status = '<span class="label label-danger">'.$record->status_label.'</span>';
        }

        $data[] = array(
            'sl'            => $sl,
            'batchid'       => $record->batchid,
            'busage'        => $record->busage_label,
            'product_name'  => $record->product_name ?? '',
            'edate'         => $record->edate ?? '',
            'status_label'  => $status,
            'button'        => $button,
        );

        $sl++;
    }

    ## Response
    $response = array(
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecordwithFilter,
        "data" => $data
    );

    return $response;
}


    public function single_stockbatch_data($id)
    {
        $encryption_key = Config::$encryption_key;
        return $this->db->select('id,details,
        AES_DECRYPT( batchid , "' . $encryption_key . '") AS batchid,
                AES_DECRYPT( mrp , "' . $encryption_key . '") AS mrp,
        details,
        pdate,
        mdate,
        edate,
        edate_enabled,
        product,
        IF(status = 1, "Active", "Inactive") as status_label,busage')
            ->from('stockbatch')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function update_stockbatch($data = [])
    {
        return $this->db->where('id', $data['id'])
            ->update('stockbatch', $data);
    }


    
    public function delete_stockbatch($id)
{
    try {
        $this->db->trans_start();

        // Check if the stockbatch is referenced in stock_details
        // stock_details uses 'batch' column (not 'batch_id')
        $stock_details_count = $this->db->where('batch', $id)
            ->from('stock_details')
            ->count_all_results();

        // Check if the stockbatch is referenced in phystock_details
        // phystock_details uses 'batch' column (not 'batch_id')
        $phystock_details_count = $this->db->where('batch', $id)
            ->from('phystock_details')
            ->count_all_results();

        // If batch is referenced in either table, prevent deletion
        if ($stock_details_count > 0 || $phystock_details_count > 0) {
            $this->db->trans_complete();
            return 'referenced'; // Return special status for referenced batch
        }

        // If not referenced, proceed with deletion
        $this->db->where('id', $id)
            ->delete("stockbatch");

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }
}

// Check if stock batch has any transactions
public function check_batch_has_transactions($batch_id)
{
    // Check in stock_details table
    $this->db->where('batch', $batch_id);
    $stock_count = $this->db->count_all_results('stock_details');
    
    // Check in phystock_details table
    $this->db->where('batch', $batch_id);
    $phystock_count = $this->db->count_all_results('phystock_details');
    
    // Return true if any transaction exists
    return ($stock_count > 0 || $phystock_count > 0);
}




    //Opening stockbatch
    public function openingstockbatchlist($postData = null)
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
            $searchQuery = " (sd.batch_id like '%" . $searchValue . "%' or sb.batchid like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ope_stock os');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ope_stock os');


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('os.id,sd.batch_id, sb.batchid, os.date');
        $this->db->from('ope_stock os');
        $this->db->join('stock_details sd', 'sd.pid = os.id');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch_id');
        $this->db->group_by('sd.batch_id');


        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($this->permission1->method('openingstockbatchlist', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'openingstock_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('openingstockbatchlist', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_opetock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'batch_id' => $record->batch_id,
                'batchid'  => $record->batchid,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }


    //Dam stockbatch
    public function damagestock($postData = null)
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
            $searchQuery = " (ds.date like '%" . $searchValue . "%' or ds.reason like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('dam_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('dam_stock ds');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id,ds.reason, ds.date');
        $this->db->from('dam_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($this->permission1->method('manage_stockdisposalnote', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'stockdisposalnote_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_stockdisposalnote', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_damstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'reason'  => $record->reason,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }



    //adj stockbatch
    public function adjstock($postData = null,$fdate,$tdate)
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
            $searchQuery = " (ds.stocktype like '%" . $searchValue .  "%' or ds.type like '%" . $searchValue .  "%' or ds.id like '%" . $searchValue .  "%' or ds.date like '%" . $searchValue . "%' or ds.reason like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');
        if($fdate){
            $this->db->where("ds.date>=", $fdate);
        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');
        if($fdate){
            $this->db->where("ds.date>=", $fdate);
        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);
        }

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id, ds.reason, ds.date,');
        $this->db->select("(CASE 
                WHEN ds.type = 'openingstock' THEN 'Opening Stock' 
                WHEN ds.type = 'storetransfer' THEN 'Store Transfer' 
                WHEN ds.type = 'stockdisposal' THEN 'Stock Disposal' 
                WHEN ds.type = 'stockadjustment' THEN 'Stock Adjustment' 
            END) AS type", false);
        $this->db->select("(CASE 
                WHEN ds.stocktype = 'actualstock' THEN 'Actual Stock' 
                WHEN ds.stocktype = 'physicalstock' THEN 'Physical Stock' 
                WHEN ds.stocktype = 'both' THEN 'Both' 
            END) AS stocktype", false);
        $this->db->from('adj_stock ds');
        if($fdate){
            $this->db->where("ds.date>=", $fdate);
        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by("ds.id", "desc");
        $this->db->order_by("ds.date", "desc");

        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($this->permission1->method('manage_stock_adjustment', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'newstockadjustment_form/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_stock_adjustment', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_adjstock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'id'     => $record->id,
                'reason'  => $record->reason,
                'stocktype' => $record->stocktype,
                'type' => $record->type,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }


    public function inventory_transection($postData = null, $fdate, $tdate)
    {
        $encryption_key = Config::$encryption_key;
        $response = array();

        $draw           = $postData['draw'];
        $start          = $postData['start'];
        $rowperpage     = $postData['length'];
        $columnIndex    = $postData['order'][0]['column'];
        $columnName     = $postData['columns'][$columnIndex]['data'];
        $columnSortOrder = $postData['order'][0]['dir'];
        $searchValue    = $postData['search']['value'];

        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (ds.stocktype like '%" . $searchValue . "%' or ds.type like '%" . $searchValue . "%' or ds.id like '%" . $searchValue . "%' or ds.date like '%" . $searchValue . "%' or ds.reason like '%" . $searchValue . "%' or AES_DECRYPT(ds.inventory_id, '{$encryption_key}') like '%" . $searchValue . "%' ) ";
        }

        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');
        if ($fdate) $this->db->where("ds.date>=", $fdate);
        if ($tdate) $this->db->where("ds.date<=", $tdate);
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        $this->db->select('count(*) as allcount');
        $this->db->from('adj_stock ds');
        if ($fdate) $this->db->where("ds.date>=", $fdate);
        if ($tdate) $this->db->where("ds.date<=", $tdate);
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        $this->db->select('ds.id, ds.reason, ds.date,');
        $this->db->select("AES_DECRYPT(ds.inventory_id, '{$encryption_key}') AS inventory_id", false);
        $this->db->select("(CASE
                WHEN ds.type = 'openingstock' THEN 'Opening Stock'
                WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
                WHEN ds.type = 'stockdisposal' THEN 'Stock Disposal'
                WHEN ds.type = 'stockadjustment' THEN 'Stock Adjustment'
            END) AS type", false);
        $this->db->select("(CASE
                WHEN ds.stocktype = 'actualstock' THEN 'Actual Stock'
                WHEN ds.stocktype = 'physicalstock' THEN 'Physical Stock'
                WHEN ds.stocktype = 'both' THEN 'Both'
            END) AS stocktype", false);
        $this->db->from('adj_stock ds');
        if ($fdate) $this->db->where("ds.date>=", $fdate);
        if ($tdate) $this->db->where("ds.date<=", $tdate);
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by("ds.id", "desc");
        $this->db->order_by("ds.date", "desc");
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl   = 1;
        $base_url  = base_url();
        $jsaction  = "return confirm('Are You Sure ?')";

        foreach ($records as $record) {
            $button = '';
            if ($this->permission1->method('manage_inventory_transection', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'new_inventory_transection/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_inventory_transection', 'delete')->access()) {
                $button .= '  <a href="' . $base_url . 'stock/stock/delete_adjstock/' . $record->id . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }
            $button .= ' <button type="button" class="btn btn-warning btn-xs" onclick="reprintInventoryTransaction(' . (int)$record->id . ')" data-toggle="tooltip" title="Print"><i class="fa fa-print" aria-hidden="true"></i></button>';

            $data[] = array(
                'sl'           => $sl,
                'id'           => $record->id,
                'inventory_id' => !empty($record->inventory_id) ? $record->inventory_id : ('#' . $record->id),
                'reason'       => $record->reason,
                'stocktype'    => $record->stocktype,
                'type'         => $record->type,
                'date'         => $record->date,
                'button'       => $button,
            );
            $sl++;
        }

        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordwithFilter,
            "aaData"                => $data
        );

        return $response;
    }

    // Inventory Request list — mirrors inventory_transection() above, reading
    // from inventory_request instead of adj_stock, plus request_no/status.
    public function inventory_request($postData = null, $fdate, $tdate)
    {
        $encryption_key = Config::$encryption_key;
        $response = array();

        $draw           = $postData['draw'];
        $start          = $postData['start'];
        $rowperpage     = $postData['length'];
        $searchValue    = $postData['search']['value'];

        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (ir.stocktype like '%" . $searchValue . "%' or ir.type like '%" . $searchValue . "%' or ir.id like '%" . $searchValue . "%' or ir.date like '%" . $searchValue . "%' or ir.reason like '%" . $searchValue . "%' or AES_DECRYPT(ir.request_no, '{$encryption_key}') like '%" . $searchValue . "%' ) ";
        }

        $this->db->select('count(*) as allcount');
        $this->db->from('inventory_request ir');
        if ($fdate) $this->db->where("ir.date>=", $fdate);
        if ($tdate) $this->db->where("ir.date<=", $tdate);
        if ($searchValue != '') $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;
        $totalRecordwithFilter = $totalRecords;

        $this->db->select("ir.id, AES_DECRYPT(ir.request_no, '{$encryption_key}') AS request_no, ir.reason, ir.date, ir.status,");
        $this->db->select("(CASE
                WHEN ir.type = 'openingstock' THEN 'Opening Stock'
                WHEN ir.type = 'storetransfer' THEN 'Store Transfer'
                WHEN ir.type = 'stockdisposal' THEN 'Stock Disposal'
                WHEN ir.type = 'stockadjustment' THEN 'Stock Adjustment'
            END) AS type", false);
        $this->db->from('inventory_request ir');
        if ($fdate) $this->db->where("ir.date>=", $fdate);
        if ($tdate) $this->db->where("ir.date<=", $tdate);
        if ($searchValue != '') $this->db->where($searchQuery);
        $this->db->order_by("ir.id", "desc");
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();

        $data = array();
        $sl   = 1;
        $base_url  = base_url();
        $jsaction  = "return confirm('Are You Sure ?')";

        foreach ($records as $record) {
            $button = '';

            // Same convention as Sales Order: status 1 (Converted) is fully
            // locked — no edit/delete/cancel/redo. Status 0 (Open) and 2
            // (Cancelled) both stay editable/deletable; 0 can be cancelled,
            // 2 can be reopened ("redo").
            if ($record->status != 1) {
                if ($record->status == 2 && $this->permission1->method('manage_inventory_request', 'update')->access()) {
                    $button .= ' <a style="margin-left:5px;" href="' . $base_url . 'stock/stock/update_inventoryrequeststatusredo/' . $record->id . '" class="btn btn-xs btn-warning" onclick="' . $jsaction . '"><i class="fa fa-repeat"></i></a>';
                }
                if ($record->status == 0 && $this->permission1->method('manage_inventory_request', 'update')->access()) {
                    $button .= ' <a style="margin-left:5px;" href="' . $base_url . 'stock/stock/update_inventoryrequeststatuscancel/' . $record->id . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-times"></i></a>';
                }
                if ($this->permission1->method('manage_inventory_request', 'update')->access()) {
                    $button .= ' <a style="margin-left:5px;" href="' . $base_url . 'new_inventory_request/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                }
                if ($this->permission1->method('manage_inventory_request', 'delete')->access()) {
                    $button .= ' <a style="margin-left:5px;" href="' . $base_url . 'stock/stock/delete_inventoryrequest/' . $record->id . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
                }
            }

            // Convert — same role/placement as Sales Order's convert_sale
            // button: only for open (status 0) requests, jumps straight into
            // New Inventory with this request pre-loaded.
            if ($record->status == 0) {
                $button .= ' <a style="margin-left:5px;" href="' . $base_url . 'convert_inventory_request/' . $record->id . '" class="btn btn-xs btn-success" onclick="' . $jsaction . '"><i class="fa fa-exchange"></i></a>';
            }
            $button .= ' <button type="button" style="margin-left:5px;" class="btn btn-warning btn-xs" onclick="reprintInventoryRequest(' . (int)$record->id . ')" data-toggle="tooltip" title="Print"><i class="fa fa-print" aria-hidden="true"></i></button>';

            // Inline colors instead of relying on .label-warning/.label-success/
            // .label-danger — those classes were resolving to black in this
            // theme, so the color is set explicitly here to guarantee it
            // renders as intended (yellow/green/red, matching Sales Order).
            $status_label = 'Requested';
            $status_color = '#f0ad4e';
            if ($record->status == 1) {
                $status_label = 'Processed';
                $status_color = '#5cb85c';
            } elseif ($record->status == 2) {
                $status_label = 'Cancelled';
                $status_color = '#d9534f';
            }
            $status_html = '<span class="label" style="background-color:' . $status_color . ';color:#fff;">' . $status_label . '</span>';

            $data[] = array(
                'sl'         => $sl,
                'id'         => $record->id,
                'request_no' => $record->request_no,
                'reason'     => $record->reason,
                'type'       => $record->type,
                'date'       => $record->date,
                'status'     => $status_html,
                'button'     => $button,
            );
            $sl++;
        }

        $response = array(
            "draw"                  => intval($draw),
            "iTotalRecords"         => $totalRecords,
            "iTotalDisplayRecords"  => $totalRecordwithFilter,
            "aaData"                => $data
        );

        return $response;
    }

    //adj stockbatch
    public function ststock($postData = null)
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
            $searchQuery = " (ds.date like '%" . $searchValue . "%' or ds.details like '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('st_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        //     ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('st_stock ds');

        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('ds.id,ds.details, ds.date');
        $this->db->from('st_stock ds');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        foreach ($records as $record) {


            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
            if ($this->permission1->method('manage_store_transfer', 'update')->access()) {
                $button .= ' <a href="' . $base_url . 'new_store_transfer/' . $record->id . '" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="' . display('update') . '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
            }
            if ($this->permission1->method('manage_store_transfer', 'delete')->access()) {

                $button .= '  <a href="' . $base_url . 'stock/stock/delete_ststock/' . $record->id . '" class="btn btn-xs btn-danger "  onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
            }



            $data[] = array(
                'sl'       => $sl,
                'details'  => $record->details,
                'date'     => $record->date,
                'button'   => $button,
            );

            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return $response;
    }




    //grn stockbatch
    public function grnstock($postData = null, $type2, $storeid,$fdate,$tdate)
    {
        $response = [];
        $encryption_key = Config::$encryption_key;
    
        ## Read DataTable parameters
        $draw = $postData['draw'] ?? 1;
        $start = $postData['start'] ?? 0;
        $rowperpage = $postData['length'] ?? 10;
        $columnIndex = $postData['order'][0]['column'] ?? 0;
        $columnName = $postData['columns'][$columnIndex]['data'] ?? 'ds.id';
        $columnSortOrder = $postData['order'][0]['dir'] ?? 'desc';
        $searchValue = $postData['search']['value'] ?? '';
    
        ## Get allowed stores for the user
        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id = sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();
    
        $storeids = $storeResult ? array_column($storeResult, 'id') : [];
    
        ## Build base query for counting total records
        $this->db->start_cache();
        $this->db->from('grn_stock ds');
        $this->db->join('purchase p', 'ds.voucherno = p.id', 'left');
        $this->db->join("phystock_details pd", "pd.pid = ds.id AND pd.type = 'grn_stock'", 'left');
        $this->db->join('sales_return sr', 'ds.voucherno = sr.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('supplier_information si1', 'si1.supplier_id = ds.supplier_id', 'left');
        $this->db->where("AES_DECRYPT(ds.type2,'{$encryption_key}') =", $type2);
    
        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } elseif ($this->session->userdata('user_level2') != 1) {
            $this->db->where_in('s.id', $storeids);
        }

        if($fdate){
            $this->db->where("ds.date>=", $fdate);

        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);

        }
    
        ## Apply search if provided
        if ($searchValue != '') {
            $this->db->where("(
                ds.date LIKE '%{$searchValue}%'
                OR ds.detail LIKE '%{$searchValue}%'
                OR AES_DECRYPT(ds.grn_id, '{$encryption_key}') LIKE '%{$searchValue}%'
                OR AES_DECRYPT(si1.supplier_name, '{$encryption_key}') LIKE '%{$searchValue}%'
            
                OR (
                    (ds.type = 'purchase' 
                        AND AES_DECRYPT(p.chalan_no, '{$encryption_key}') LIKE '%{$searchValue}%')
                    OR (ds.type != 'purchase' 
                        AND AES_DECRYPT(sr.sales_return_id, '{$encryption_key}') LIKE '%{$searchValue}%')
                )
            
                -- 🔥 Type search support
                OR (
                    CASE 
                        WHEN ds.type = 'purchase' THEN 'Purchase'
                        WHEN ds.type = 'salesreturn' THEN 'Sales Return'
                        WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
                        ELSE ds.type
                    END LIKE '%{$searchValue}%'
                )
            )", NULL, FALSE);
        }
    
        ## Count total records and filtered records
        $this->db->select("COUNT(DISTINCT ds.id) AS allcount");
        $totalRecords = $this->db->get()->row()->allcount ?? 0;
        $totalRecordwithFilter = $totalRecords; // Same because filter applied above
        $this->db->stop_cache();
    
        ## Fetch actual records
        $this->db->select("
        ds.id,
        AES_DECRYPT(ds.grn_id, '{$encryption_key}') AS grn_id,
        ds.detail,
        ds.date,
    
        CASE 
            WHEN ds.type = 'purchase' 
                THEN AES_DECRYPT(p.chalan_no, '{$encryption_key}')
            ELSE 
                AES_DECRYPT(sr.sales_return_id, '{$encryption_key}')
        END AS voucherno,
        CASE 
            WHEN ds.type = 'purchase' THEN 'Purchase'
            WHEN ds.type = 'salesreturn' THEN 'Sales Return'
            WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
            ELSE ds.type
        END AS type,
    
        s.name AS store,
        p.status AS status,
        AES_DECRYPT(si1.supplier_name, '{$encryption_key}') AS supplier_name
    ", false);
        $this->db->group_by('ds.id');
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
    
        $records = $this->db->get()->result();
        $this->db->flush_cache();
    
        ## Prepare response data
        $data = [];
        $sl = $start + 1;
        $base_url = base_url();
        foreach ($records as $record) {
            $button = '';
            $jsaction = "return confirm('Are You Sure ?')";
    
            if ($record->status == 0) {
                if ($this->permission1->method('manage_grn', 'update')->access()) {
                    $button .= '<a href="' . $base_url . 'new_grn/' . $record->id . '" class="btn btn-info btn-xs" title="' . display('update') . '"><i class="fa fa-pencil"></i></a>';
                }
                if ($this->permission1->method('manage_grn', 'delete')->access()) {
                    $button .= '<a href="' . $base_url . 'stock/stock/delete_grnstock/' . $record->id . '" class="btn btn-xs btn-danger" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
                }
            }
    
            $button .= '<button style="margin-left:5px;" class="btn btn-warning btn-xs" title="Reprint" onclick="reprintInvoice(' . $record->id . ')"><i class="fa fa-fax"></i></button>';
    
            $data[] = [
                'sl' => $sl++,
                'grn_id' => $record->grn_id,
                'details' => $record->detail,
                'date' => $record->date,
                'supplier_name' => $record->supplier_name,
                'voucherno' => $record->voucherno,
                'type' => $record->type,
                'store' => $record->store,
                'button' => $button
            ];
        }
    
        ## Response
        return [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ];
    }


    //grn stockbatch
    public function gdnstock($postData = null, $type2, $storeid,$fdate,$tdate)
    {
        $response = [];
        $encryption_key = Config::$encryption_key;
    
        ## Read DataTables parameters
        $draw = $postData['draw'] ?? 1;
        $start = $postData['start'] ?? 0;
        $rowperpage = $postData['length'] ?? 10;
        $columnIndex = $postData['order'][0]['column'] ?? 0;
        $columnName = $postData['columns'][$columnIndex]['data'] ?? 'ds.id';
        $columnSortOrder = $postData['order'][0]['dir'] ?? 'desc';
        $searchValue = $postData['search']['value'] ?? '';
    
        ## Get allowed stores for user
        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id = sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();
    
        $storeids = $storeResult ? array_column($storeResult, 'id') : [];
    
        ## Build search query
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = "(
                ds.date LIKE '%{$searchValue}%'
                OR ds.detail LIKE '%{$searchValue}%'
                OR AES_DECRYPT(ds.gdn_id,'{$encryption_key}') LIKE '%{$searchValue}%'
                OR AES_DECRYPT(ci1.customer_name,'{$encryption_key}') LIKE '%{$searchValue}%'
            
                OR (
                    (ds.type IN ('sale', 'wholesale')  
                        AND AES_DECRYPT(p.sale_id,'{$encryption_key}') LIKE '%{$searchValue}%')
                    OR (ds.type != 'sale' 
                        AND AES_DECRYPT(pr.purchase_return_id,'{$encryption_key}') LIKE '%{$searchValue}%')
                )
                OR (
                    CASE 
                        WHEN ds.type = 'sale' THEN 'Sale'
                        WHEN ds.type = 'wholesale' THEN 'Wholesale'
                        WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
                        WHEN ds.type = 'purchasereturn' THEN 'Purchase Return'
                        ELSE ds.type
                    END LIKE '%{$searchValue}%'
                )
            )";
        }
    
        // =========================================================
        // 1. TOTAL RECORDS (NO FILTER)
        // =========================================================
        $this->db->select("COUNT(DISTINCT ds.id) as allcount");
        $this->db->from('gdn_stock ds');
        $this->db->join('phystock_details pd', "pd.pid = ds.id AND pd.type = 'gdn_stock'", 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', 'left');
        $this->db->where("AES_DECRYPT(ds.type2,'{$encryption_key}') =", $type2);
    
        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
            $this->db->where_in('s.id', $storeids);
        }

        if($fdate){
            $this->db->where("ds.date>=", $fdate);

        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);

        }
    
        $result = $this->db->get()->row();
        $totalRecords = $result ? $result->allcount : 0;
    
        // =========================================================
        // 2. TOTAL RECORDS WITH FILTER
        // =========================================================
        $this->db->select("COUNT(DISTINCT ds.id) as allcount");
        $this->db->from('gdn_stock ds');
        $this->db->join('phystock_details pd', "pd.pid = ds.id AND pd.type = 'gdn_stock'", 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', 'left');
        $this->db->join('sale p', 'ds.voucherno = p.id', 'left');
        $this->db->join('purchase_return pr', 'ds.voucherno = pr.id', 'left');
        $this->db->where("AES_DECRYPT(ds.type2,'{$encryption_key}') =", $type2);
    
        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
            $this->db->where_in('s.id', $storeids);
        }

        if($fdate){
            $this->db->where("ds.date>=", $fdate);

        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);

        }
    
        if ($searchValue != '') {
            $this->db->where($searchQuery, NULL, FALSE);
        }
    
        $result = $this->db->get()->row();
        $totalRecordwithFilter = $result ? $result->allcount : 0;
    
        // =========================================================
        // 3. FETCH RECORDS
        // =========================================================
        $this->db->select("
            ds.id,
            AES_DECRYPT(ds.gdn_id,'{$encryption_key}') AS gdn_id,
            ds.detail,
            ds.date,
          CASE 
    WHEN ds.type IN ('sale', 'wholesale') THEN AES_DECRYPT(p.sale_id,'{$encryption_key}')
    ELSE AES_DECRYPT(pr.purchase_return_id,'{$encryption_key}')
END AS voucherno,
           CASE 
        WHEN ds.type = 'sale' THEN 'Sale'
        WHEN ds.type = 'wholesale' THEN 'Wholesale'
        WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
        WHEN ds.type = 'purchasereturn' THEN 'Purchase Return'
        ELSE ds.type
    END AS type,
            s.name AS store,
            p.status,
            AES_DECRYPT(ci1.customer_name,'{$encryption_key}') AS customer_name
        ", false);
    
        $this->db->from('gdn_stock ds');
        $this->db->join('sale p', 'ds.voucherno = p.id', 'left');
        $this->db->join('purchase_return pr', 'ds.voucherno = pr.id', 'left');
        $this->db->join('phystock_details pd', "pd.pid = ds.id AND pd.type = 'gdn_stock'", 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci1', 'ci1.customer_id = ds.customer_id', 'left');
    
        $this->db->where("AES_DECRYPT(ds.type2,'{$encryption_key}') =", $type2);
    
        if ($storeid > 0) {
            $this->db->where("s.id", $storeid);
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
            $this->db->where_in('s.id', $storeids);
        }

        if($fdate){
            $this->db->where("ds.date>=", $fdate);

        }

        if($tdate){
            $this->db->where("ds.date<=", $tdate);

        }
    
        if ($searchValue != '') {
            $this->db->where($searchQuery, NULL, FALSE);
        }
    
        $this->db->group_by('ds.id');
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
    
        $records = $this->db->get()->result();
    
        // =========================================================
        // 4. FORMAT DATA
        // =========================================================
        $data = [];
        $sl = 1;
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $jsaction = "return confirm('Are You Sure ?')";
    
            if ($record->status == 0) {
                if ($this->permission1->method('manage_gdn', 'update')->access()) {
                    $button .= '<a href="' . $base_url . 'new_gdn/' . $record->id . '" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i></a>';
                }
                if ($this->permission1->method('manage_gdn', 'delete')->access()) {
                    $button .= '<a href="' . $base_url . 'stock/stock/delete_gdnstock/' . $record->id . '" class="btn btn-danger btn-xs" onclick="' . $jsaction . '"><i class="fa fa-trash"></i></a>';
                }
            }
    
            $button .= '<button class="btn btn-warning btn-xs" onclick="reprintInvoice(' . $record->id . ')"><i class="fa fa-fax"></i></button>';
    
            $data[] = [
                'sl' => $sl,
                'gdn_id' => $record->gdn_id,
                'details' => $record->detail,
                'date' => $record->date,
                'voucherno' => $record->voucherno,
                'type' => $record->type,
                'store' => $record->store,
                'customer_name' => $record->customer_name,
                'button' => $button
            ];
    
            $sl++;
        }
    
        ## RESPONSE
        return [
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ];
    }
}