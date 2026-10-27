<?php
defined('BASEPATH') or exit('No direct script access allowed');

#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Report_model extends CI_Model
{

    /**
     * Unit Cost Logic Specification v1.1 (Phase 6) — SQL fragment giving a derived table with
     * the latest (current) unit_cost row per Product+Batch: one row per (product, batch) with its
     * current Available Qty / Unit Cost / Available Value, decrypted and cast to DECIMAL. Meant
     * to be LEFT JOINed (never INNER — a Product+Batch with no cost history yet, e.g. never
     * purchased/opened before this feature existed, must still appear in a report with a blank/0
     * cost, not disappear) on "<alias>.product = <product col> AND <alias>.batch = <batch col>".
     * Uses ROW_NUMBER() (MariaDB 10.2+/MySQL 8+ window function, confirmed available on this
     * server) partitioned by (product, batch) ordered by the same (date, save_seq, id) ordering
     * Unitcost_model::current() uses, so this always agrees with what the costing engine itself
     * considers "current".
     */
    public function unit_cost_latest_sql($alias = 'uc')
    {
        $enc = Config::$encryption_key;
        return "(
            SELECT product, batch,
                   CAST(AES_DECRYPT(available_qty_after, '{$enc}') AS DECIMAL(18,6)) AS current_qty,
                   CAST(AES_DECRYPT(unit_cost_after, '{$enc}') AS DECIMAL(18,6)) AS current_cost,
                   CAST(AES_DECRYPT(available_value_after, '{$enc}') AS DECIMAL(18,6)) AS current_value
            FROM (
                SELECT product, batch, available_qty_after, unit_cost_after, available_value_after,
                       ROW_NUMBER() OVER (
                           PARTITION BY product, batch ORDER BY date DESC, save_seq DESC, id DESC
                       ) AS rn
                FROM unit_cost
            ) t
            WHERE rn = 1
        ) {$alias}";
    }

    /**
     * Same as unit_cost_latest_sql() but as at a given date (UCL-60) — the latest unit_cost row
     * per Product+Batch with date <= $as_of_date, for reports with a date-range filter
     * (Comprehensive Stock Report) where "current" state wouldn't respect the filter.
     */
    public function unit_cost_asof_sql($as_of_date, $alias = 'uc')
    {
        $enc = Config::$encryption_key;
        $as_of_date_esc = $this->db->escape($as_of_date);
        return "(
            SELECT product, batch,
                   CAST(AES_DECRYPT(available_qty_after, '{$enc}') AS DECIMAL(18,6)) AS current_qty,
                   CAST(AES_DECRYPT(unit_cost_after, '{$enc}') AS DECIMAL(18,6)) AS current_cost,
                   CAST(AES_DECRYPT(available_value_after, '{$enc}') AS DECIMAL(18,6)) AS current_value
            FROM (
                SELECT product, batch, available_qty_after, unit_cost_after, available_value_after,
                       ROW_NUMBER() OVER (
                           PARTITION BY product, batch ORDER BY date DESC, save_seq DESC, id DESC
                       ) AS rn
                FROM unit_cost
                WHERE date <= {$as_of_date_esc}
            ) t
            WHERE rn = 1
        ) {$alias}";
    }

    /**
     * Wraps unit_cost_latest_sql()/unit_cost_asof_sql() with a per-Product SUM — for reports that
     * aggregate across all of a product's batches into one row (Live Stock, Comprehensive Stock),
     * where "Unit Cost" isn't meaningful as a single number (it varies per batch) but "Stock
     * Value" (summed across batches) is.
     */
    public function unit_cost_product_value_sql($alias = 'ucv', $as_of_date = null)
    {
        $inner = $as_of_date ? $this->unit_cost_asof_sql($as_of_date, 'q') : $this->unit_cost_latest_sql('q');
        return "(SELECT product, SUM(current_value) AS stock_value FROM {$inner} GROUP BY product) {$alias}";
    }

    public function bdtask_getStock($postData = null)
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
            $searchQuery = " (a.product_name like '%" . $searchValue . "%' or a.product_model like '%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '') {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecords = $records;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_information a');
        if ($searchValue != '') {
            $this->db->where($searchQuery);
        }
        $this->db->group_by('a.product_id');
        $records = $this->db->get()->num_rows();
        $totalRecordwithFilter = $records;

        ## Fetch records
        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model
                ");
        $this->db->from('product_information a');
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->group_by('a.product_id');
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;
        foreach ($records as $record) {
            $stockin = $this->db->select('sum(quantity) as totalSalesQnty')->from('invoice_details')->where('product_id', $record->product_id)->get()->row();
            $stockout = $this->db->select('sum(quantity) as totalPurchaseQnty,Avg(rate) as purchaseprice')->from('product_purchase_details')->where('product_id', $record->product_id)->get()->row();


            $sprice = (!empty($record->price) ? $record->price : 0);
            $pprice = (!empty($stockout->purchaseprice) ? sprintf('%0.2f', $stockout->purchaseprice) : 0);
            $stock =  (!empty($stockout->totalPurchaseQnty) ? $stockout->totalPurchaseQnty : 0) - (!empty($stockin->totalSalesQnty) ? $stockin->totalSalesQnty : 0);
            $data[] = array(
                'sl'            =>   $sl,
                'product_name'  =>  $record->product_name,
                'product_model' =>  $record->product_model,
                'sales_price'   =>  sprintf('%0.2f', $sprice),
                'purchase_p'    =>  $pprice,
                'totalPurchaseQnty' => $stockout->totalPurchaseQnty,
                'totalSalesQnty' =>  $stockin->totalSalesQnty,
                'stok_quantity' => sprintf('%0.2f', $stock),

                'total_sale_price' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $sprice,
                'purchase_total' => ($stockout->totalPurchaseQnty - $stockin->totalSalesQnty) * $pprice,
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



    public function totalnumberof_product()
    {

        $this->db->select("a.*,
                a.product_name,
                a.product_id,
                a.product_model,
                c.supplier_price
                ");
        $this->db->from('product_information a');

        $this->db->join('supplier_product c', 'c.product_id = a.product_id', 'left');
        $this->db->group_by('a.product_id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->num_rows();
        }
        return false;
    }


    public function accounts_closing_data()
    {
        $last_closing_amount = $this->get_last_closing_amount();
        $cash_in = $this->cash_data_receipt();
        $cash_out = $this->cash_data();
        if ($last_closing_amount != null) {
            $last_closing_amount = $last_closing_amount[0]['amount'];
            $cash_in_hand = ($last_closing_amount + $cash_in) - $cash_out;
        } else {
            $last_closing_amount = 0;
            $cash_in_hand = $cash_in - $cash_out;
        }


        return array(
            "last_day_closing" => number_format($last_closing_amount, 2, '.', ','),
            "cash_in"          => number_format($cash_in, 2, '.', ','),
            "cash_out"         => number_format($cash_out, 2, '.', ','),
            "cash_in_hand"     => number_format($cash_in_hand, 2, '.', ',')
        );
    }

    public function get_last_closing_amount()
    {
        $sql = "SELECT amount FROM daily_closing WHERE date = (SELECT MAX(date) FROM daily_closing)";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if ($result) {
            return $result;
        } else {
            return FALSE;
        }
    }

    public function cash_data_receipt()
    {
        date_default_timezone_set('Asia/Colombo');

        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Debit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }


    public function cash_data()
    {
        date_default_timezone_set('Asia/Colombo');

        //-----------
        $cash = 0;
        $datse = date('Y-m-d');
        $this->db->select('sum(Credit) as amount');
        $this->db->from('acc_transaction');
        $this->db->where('COAID', 111000001);
        $this->db->where('VDate', $datse);
        $result_amount = $this->db->get();
        $amount = $result_amount->result_array();
        $cash += $amount[0]['amount'];
        return $cash;
    }

    //CLOSING ENTRY
    public function daily_closing_entry($data)
    {
        return $this->db->insert('daily_closing', $data);
    }



    public function get_closing_report()
    {
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }


    public function get_date_wise_closing_report($from_date, $to_date)
    {
        $dateRange = "DATE(datetime) BETWEEN '$from_date' AND '$to_date'";
        $this->db->select("* ,(opening_balance + amount_in) - amount_out as 'cash_in_hand'");
        $this->db->from('closing_records');
        $this->db->where('status', 1);
        $this->db->where($dateRange, NULL, FALSE);
        $this->db->order_by('datetime', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }


    //Retrieve todays_sales_report
    public function todays_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $usertype = $this->session->userdata('user_level2');

        $type2 = $usertype == 3 ? "B" : "A";
        $encryption_key = Config::$encryption_key;


        $today = date('Y-m-d');
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date', $today);
        $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $type2);

        $this->db->order_by('a.invoice_id', 'desc');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function getSalesReportList($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }

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
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $customer = $record->customer_name;

            $data[] = array(
                'date'                   => $record->date,
                'invoice_id'             => $record->invoice_id,
                'customer_name'          => $customer,
                'due_amount'             => $record->due_amount,
                'total_amount'     => $record->total_amount,
            );
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }

    //Retrieve all Report
    public function sales_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id = null,$incident_type=null)
    {
        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        
            $incidentFilter_p = $incidentFilter_r = '';
        if ($incident_type !== '' && $incident_type !== null&&$incident_type != 3 ) {
            $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND a.incidenttype = {$incidentEsc}";
            $incidentFilter_r = " AND a.incidenttype = 3";
        }else if($incident_type !== '' && $incident_type !== null&&$incident_type == 3 ) {
            // $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND a.incidenttype = 3";
            // $incidentFilter_r = " AND r.incidenttype = {$incidentEsc}";

        }

        // First Query (Sales)
        $sql1 = "
   SELECT 
        a.date,
        AES_DECRYPT(a.sale_id,'$encryption_key') AS invoiceno,
        CASE 
        WHEN a.incidenttype = 1 THEN 'Retail'
        ELSE 'Wholesale'
    END AS incidenttype,
        AES_DECRYPT(b.customer_name,'$encryption_key') AS customer_name,
        AES_DECRYPT(a.grandTotal,'$encryption_key') AS total
    FROM sale a
    JOIN customer_information b ON b.customer_id = a.customer_id
    WHERE a.date >= '$from_date'
    AND a.date <= '$to_date'
            {$incidentFilter_p}

    ";

        if ($empid != "All") {
            $sql1 .= " AND AES_DECRYPT(a.type2,'$encryption_key') = '$empid'";
        }

        if (!empty($customer_id)) {
            $sql1 .= " AND a.customer_id = '$customer_id'";
        }

        if ($branch) {
            $sql1 .= " AND a.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND a.branch IN ($branchList)";
            }
        }


        // Second Query (Sales Return)
        $sql2 = "
        SELECT 
            a.rdate AS date,
            AES_DECRYPT(s.sale_id,'$encryption_key') AS invoiceno,
            'Sales Return' AS incidenttype,
            AES_DECRYPT(b.customer_name,'$encryption_key') AS customer_name,
           - AES_DECRYPT(a.grandTotal,'$encryption_key') AS total
        FROM sales_return a
        JOIN sale s ON s.id = a.sales_id
        JOIN customer_information b ON b.customer_id = a.customer_id
        WHERE a.date >= '$from_date'
        AND a.date <= '$to_date'
                  {$incidentFilter_r}
        ";

        if ($empid != "All") {
            $sql2 .= " AND AES_DECRYPT(a.type2,'$encryption_key') = '$empid'";
        }

        if (!empty($customer_id)) {
            $sql2 .= " AND a.customer_id = '$customer_id'";
        }

        if ($branch) {
            $sql2 .= " AND a.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql2 .= " AND a.branch IN ($branchList)";
            }
        }


        // Final UNION Query
        $finalQuery = "
     ($sql1)
     UNION ALL
     ($sql2)
    ORDER BY date DESC
    ";

        $query = $this->db->query($finalQuery);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve todays_purchase_report
    public function todays_purchase_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date', $today);
        $this->db->order_by('a.purchase_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //    ======= its for  todays_customer_receipt ===========
    public function todays_customer_receipt($today = null)
    {
        $this->db->select('a.*,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('a.subType', 3);
        $this->db->where('a.Credit >', 0);
        $this->db->where('DATE(a.VDate)', $today);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function filter_customer_wise_receipt($custome_id = null, $from_date = null)
    {
        $this->db->select('a.Narration,b.HeadName,a.Credit,b.HeadName, c.name');
        $this->db->from('acc_transaction a');
        $this->db->join('acc_coa b', 'a.COAID=b.HeadCode');
        $this->db->join('acc_subcode c', 'a.subCode=c.id');
        $this->db->where('c.referenceNo', $custome_id);
        $this->db->where('a.Credit >', 0);
        $this->db->where('a.subType', 3);
        $this->db->where('DATE(a.VDate)', $from_date);
        $this->db->where('a.IsAppove', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function customerinfo_rpt($customer_id)
    {
        return $this->db->select('*')
            ->from('customer_information')
            ->where('customer_id', $customer_id)
            ->get()
            ->result_array();
    }


    // ======================= user sales report ================
    public function user_sales_report($from_date, $to_date, $user_id, $emp_id, $branch)
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }
        // Sales Query
        $sql1 = "
SELECT 
    a.employee_id,
    SUM(AES_DECRYPT(a.grandTotal,'$encryption_key')) AS amount,
    COUNT(a.id) AS total_invoice,
    b.first_name,
    b.last_name
FROM sale a
LEFT JOIN employee_history b ON b.id = a.employee_id
WHERE a.date >= '$from_date'
AND a.date <= '$to_date'
";

        if (!empty($user_id)) {
            $sql1 .= " AND a.employee_id = '$user_id'";
        }

        if ($emp_id != "All") {
            $sql1 .= " AND AES_DECRYPT(a.type2,'$encryption_key') = '$emp_id'";
        }

        if ($branch) {
            $sql1 .= " AND a.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND a.branch IN ($branchList)";
            }
        }

        $sql1 .= " GROUP BY a.employee_id";


        // Sales Return Query
        $sql2 = "
SELECT 
    a.employee_id,
    -SUM(AES_DECRYPT(a.grandTotal,'$encryption_key')) AS amount,
    COUNT(a.id) AS total_invoice,
    b.first_name,
    b.last_name
FROM sales_return a
LEFT JOIN employee_history b ON b.id = a.employee_id
WHERE a.rdate >= '$from_date'
AND a.rdate <= '$to_date'
";

        if (!empty($user_id)) {
            $sql2 .= " AND a.employee_id = '$user_id'";
        }

        if ($emp_id != "All") {
            $sql2 .= " AND AES_DECRYPT(a.type2,'$encryption_key') = '$emp_id'";
        }

        if ($branch) {
            $sql2 .= " AND a.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql2 .= " AND a.branch IN ($branchList)";
            }
        }

        $sql2 .= " GROUP BY a.employee_id";


        // Final UNION + GROUP AGAIN
        $finalQuery = "
SELECT 
    employee_id,
    SUM(amount) AS amount,
    SUM(total_invoice) AS total_invoice,
    first_name,
    last_name
FROM (
    ($sql1)
    UNION ALL
    ($sql2)
) AS combined
GROUP BY employee_id
ORDER BY amount DESC
";

        $query = $this->db->query($finalQuery);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function userList()
    {
        $this->db->select("*");
        $this->db->from('employee_history');
        $this->db->order_by('first_name', 'asc');
        $this->db->where_not_in('id', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function retrieve_dateWise_DueReports($from_date, $to_date)
    {
        $this->db->select("a.*,b.*,c.*");
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date BETWEEN "' . $from_date . '" and "' . $to_date . '"');
        $this->db->where('a.due_amount >', 0);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
    public function get_retrieve_dateWise_DueReports($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        // dd($datbetween);

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
            $searchQuery = " (a.date like '%" . $searchValue . "%' or a.invoice_id like '%" . $searchValue . "%' or a.total_amount like'%" . $searchValue . "%' or b.customer_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->distinct();
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->distinct();
        $this->db->select('count(*) as allcount');
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->distinct();
        $this->db->select("a.*,b.customer_id,b.customer_name");
        $this->db->from('invoice a');
        $this->db->join('invoice_details c', 'c.invoice_id = a.id');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.due_amount >', 0);
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $sales_amount = 0;
        // dd($records);
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $customer = $record->customer_name;

            $data[] = array(
                'date'                   => $record->date,
                'invoice_id'             => $record->invoice_id,
                'customer_name'          => $customer,
                'total_amount'           => $record->total_amount,
                'paid_amount'            => $record->paid_amount,
                'due_amount'             => $record->due_amount,
            );
            $sales_amount += $record->total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "sales_amount" => $sales_amount,
            "aaData" => $data
        );

        return $response;
    }



    // ================= Shipping cost ===========================
    public function retrieve_dateWise_Shippingcost($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }



    //Retrieve todays_purchase_report
    public function voucher_report($from_date, $to_date, $type2, $branch, $type = null, $from ,$to)
    {
        $encryption_key = Config::$encryption_key;
    
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();
    
        $branchids = [];
    
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }
    
        date_default_timezone_set('Asia/Colombo');
    
            $sql = "
        SELECT
            a.date,
            CASE
                WHEN a.type = '1' THEN 'Payment Voucher'
                WHEN a.type = '2' THEN 'Receipt Voucher'
                WHEN a.type = '3' THEN 'Contra Voucher'
                ELSE 'Unknown'
            END AS voucher_type,

            AES_DECRYPT(a.voucher_id,'$encryption_key') AS voucher_id,
            AES_DECRYPT(b.amount,'$encryption_key') AS amount,

            pt.name AS from_name,
            pr.name AS to_name

        FROM voucher a

        JOIN voucher_details b
            ON b.pid = a.id

        JOIN payment_type pt
            ON pt.id = a.`from`
        ";

        if ($type == 3) {

            $sql .= "
            JOIN payment_type pr
                ON pr.id = b.`to`
            ";

        } else {

            $sql .= "
            JOIN payment_receipt_type pr
                ON pr.id = b.`to`
            ";
        }

        $sql .= "
        WHERE a.date >= '$from_date'
        AND a.date <= '$to_date'
        ";
    
        // type2 filter
        if ($type2 != "All") {
            $sql .= " AND AES_DECRYPT(a.type2,'$encryption_key') = '$type2'";
        }
    
        // voucher type filter
        if (!empty($type)) {
            $sql .= " AND a.type = '$type'";
            if ($type == 3) {
                if (!empty($from)) {
                    $sql .= " AND a.from = '$from'";
                }
            }
        }
    
        if (!empty($to)) {
            $sql .= " AND b.to = '$to'";
        }
        // branch filter
        if ($branch) {
            $sql .= " AND a.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
    
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
    
                $sql .= " AND a.branch IN ($branchList)";
            }
        }
    
        $sql .= " ORDER BY a.date DESC";
    
        $query = $this->db->query($sql);
    
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    
        return false;
    }
    public function getReportList($postData = null)
    {

        $response = array();

        $fromdate = $this->input->post('fromdate');
        $todate   = $this->input->post('todate');
        if (!empty($fromdate)) {
            $datbetween = "(a.purchase_date BETWEEN '$fromdate' AND '$todate')";
        } else {
            $datbetween = "";
        }
        // dd($datbetween);

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
            $searchQuery = " (a.purchase_date like '%" . $searchValue . "%' or a.purchase_id like '%" . $searchValue . "%' or a.grand_total_amount like'%" . $searchValue . "%' or b.supplier_name like'%" . $searchValue . "%') ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select("a.*,b.supplier_id,b.supplier_name");
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where($datbetween);
        }
        if ($searchValue != '')
            $this->db->where($searchQuery);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();
        $sl = 1;

        $purchase_amount = 0;
        // dd($records);
        foreach ($records as $record) {
            $button = '';
            $base_url = base_url();
            $supplier = $record->supplier_name;

            $data[] = array(
                'purchase_date'          => $record->purchase_date,
                'purchase_id'            => $record->purchase_id,
                'supplier_name'          => $supplier,
                'due_amount'             => $record->due_amount,
                'grand_total_amount'     => $record->grand_total_amount,
            );
            $purchase_amount += $record->grand_total_amount;
            $sl++;
        }

        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecordwithFilter,
            "iTotalDisplayRecords" => $totalRecords,
            "purchase_amount" => $purchase_amount,
            "aaData" => $data
        );

        return $response;
    }


    public function category_list_product()
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

    public function active_store()
    {
        $this->db->select('*');
        $this->db->from('store');
        // $this->db->where('status', 1);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    

    public function active_store_stock()
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


    //    ============= its for purchase_report_category_wise ===============
    public function purchase_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch)
    {
        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        // Purchase Query
        $sql1 = "
SELECT
    b.id AS product,
    b.product_name,
    b.product_model,
    c.category_id,
    c.category_name,
    SUM(IFNULL(AES_DECRYPT(a.quantity,'$encryption_key'),0)) AS quantity,
    SUM(IFNULL(AES_DECRYPT(a.total_price,'$encryption_key'),0)) AS total_price,
    cr.conversion_ratio,
    u1.unit_name AS sub,
    u2.unit_name AS master
FROM purchase_details a
JOIN product_information b ON b.id = a.product
JOIN product_category c ON c.category_id = b.category_id
JOIN purchase d ON d.id = a.pid
LEFT  JOIN subunit_product  sp ON sp.product_id = b.id AND sp.first = 1
LEFT  JOIN units            u1 ON u1.unit_id    = sp.unit_id
INNER JOIN units            u2 ON u2.unit_id    = b.unit
LEFT  JOIN conversion_ratio cr ON cr.product    = b.id AND u1.unit_id = cr.subunit
WHERE d.date >= '$from_date'
AND d.date <= '$to_date'
";

        if ($product) {
            $sql1 .= " AND a.product = '$product'";
        }

        if ($category) {
            $sql1 .= " AND b.category_id = '$category'";
        }

        if ($empid != "All") {
            $sql1 .= " AND AES_DECRYPT(d.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql1 .= " AND d.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND d.branch IN ($branchList)";
            }
        }

        $sql1 .= " GROUP BY b.id, c.category_id, cr.conversion_ratio, u1.unit_name, u2.unit_name";


        // Purchase Return Query (NEGATIVE 🔥)
        $sql2 = "
SELECT
    b.id AS product,
    b.product_name,
    b.product_model,
    c.category_id,
    c.category_name,
    SUM(IFNULL(AES_DECRYPT(a.rqty,'$encryption_key'),0)) * -1 AS quantity,
    SUM(IFNULL(AES_DECRYPT(a.total_price,'$encryption_key'),0)) * -1 AS total_price,
    cr.conversion_ratio,
    u1.unit_name AS sub,
    u2.unit_name AS master
FROM purchase_return_details a
JOIN product_information b ON b.id = a.product
JOIN product_category c ON c.category_id = b.category_id
JOIN purchase_return d ON d.id = a.pid
LEFT  JOIN subunit_product  sp ON sp.product_id = b.id AND sp.first = 1
LEFT  JOIN units            u1 ON u1.unit_id    = sp.unit_id
INNER JOIN units            u2 ON u2.unit_id    = b.unit
LEFT  JOIN conversion_ratio cr ON cr.product    = b.id AND u1.unit_id = cr.subunit
WHERE d.rdate >= '$from_date'
AND d.rdate <= '$to_date'
";

        if ($product) {
            $sql2 .= " AND a.product = '$product'";
        }

        if ($category) {
            $sql2 .= " AND b.category_id = '$category'";
        }

        if ($empid != "All") {
            $sql2 .= " AND AES_DECRYPT(d.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql2 .= " AND d.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql2 .= " AND d.branch IN ($branchList)";
            }
        }

        $sql2 .= " GROUP BY b.id, c.category_id, cr.conversion_ratio, u1.unit_name, u2.unit_name";


        // Final UNION + GROUP
        $finalQuery = "
SELECT
    product,
    product_name,
    product_model,
    category_id,
    category_name,
    SUM(quantity) AS quantity,
    SUM(total_price) AS total_price,
    conversion_ratio,
    sub,
    master
FROM (
    ($sql1)
    UNION ALL
    ($sql2)
) AS combined
GROUP BY product, category_id, conversion_ratio, sub, master
ORDER BY quantity DESC
";

        $query = $this->db->query($finalQuery);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }


    //RETRIEVE DATE WISE SINGE PRODUCT REPORT
    public function retrieve_product_sales_report($from_date, $to_date, $product_id, $empid, $branch, $incident_type = '')
    {

        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

          $incidentFilter_p = $incidentFilter_r = '';
        if ($incident_type !== '' && $incident_type !== null&&$incident_type != 3 ) {
            $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND c.incidenttype = {$incidentEsc}";
            $incidentFilter_r = " AND c.incidenttype = 3";
        }else if($incident_type !== '' && $incident_type !== null&&$incident_type == 3 ) {
            // $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND c.incidenttype = 3";
            // $incidentFilter_r = " AND r.incidenttype = {$incidentEsc}";

        }

        // First Query (Sales)
        $sql1 = "
SELECT 
    b.product_name,
    c.date,
    AES_DECRYPT(c.sale_id,'$encryption_key') AS sale_id,
    CASE 
        WHEN c.incidenttype = 1 THEN 'Retail'
        ELSE 'Wholesale'
    END AS incidenttype,
    AES_DECRYPT(a.product_rate,'$encryption_key') AS product_rate,
    AES_DECRYPT(a.total_price,'$encryption_key') AS total,
    AES_DECRYPT(d.customer_name,'$encryption_key') AS customer_name,
    CAST( ROUND(  CASE
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '+' 
            THEN AES_DECRYPT(a.quantity, '{$encryption_key}') + cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '-' 
            THEN AES_DECRYPT(a.quantity, '{$encryption_key}') - cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '*' 
            THEN AES_DECRYPT(a.quantity, '{$encryption_key}') * cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '/' 
            THEN AES_DECRYPT(a.quantity, '{$encryption_key}') / cr2.conversion_ratio
        ELSE AES_DECRYPT(a.quantity, '{$encryption_key}')
    END
,  6) AS UNSIGNED ) AS quantity
,u.unit_name
FROM sale_details a
JOIN product_information b ON b.id = a.product
left JOIN conversion_ratio cr2 on cr2.conversionratio_id = a.conversion_id
JOIN units u ON u.unit_id = a.unit
JOIN sale c ON c.id = a.pid
JOIN customer_information d ON d.customer_id = c.customer_id
WHERE c.date >= '$from_date'
AND c.date <= '$to_date'
{$incidentFilter_p}
";

        // Filters
        if ($product_id) {
            $sql1 .= " AND a.product = '$product_id'";
        }

        if ($empid != "All") {
            $sql1 .= " AND AES_DECRYPT(c.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql1 .= " AND c.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND c.branch IN ($branchList)";
            }
        }


        // Second Query (Sales Return) ✅ FIXED TABLES
        $sql2 = "
SELECT 
    b.product_name,
    c.rdate AS date,
    AES_DECRYPT(s.sale_id,'$encryption_key') AS sale_id,
     'Sales Return' AS incidenttype,
    AES_DECRYPT(a.product_rate,'$encryption_key') AS product_rate,
   - AES_DECRYPT(a.total_price,'$encryption_key') AS total,
    AES_DECRYPT(d.customer_name,'$encryption_key') AS customer_name,
     CAST( ROUND(  CASE
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '+' 
            THEN AES_DECRYPT(a.rqty, '{$encryption_key}') + cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '-' 
            THEN AES_DECRYPT(a.rqty, '{$encryption_key}') - cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '*' 
            THEN AES_DECRYPT(a.rqty, '{$encryption_key}') * cr2.conversion_ratio
        WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '/' 
            THEN AES_DECRYPT(a.rqty, '{$encryption_key}') / cr2.conversion_ratio
        ELSE AES_DECRYPT(a.rqty, '{$encryption_key}')
    END
,  6) AS UNSIGNED ) AS quantity
,u.unit_name
FROM sales_return_details a
JOIN product_information b ON b.id = a.product
left JOIN conversion_ratio cr2 on cr2.conversionratio_id = a.conversion_id
JOIN units u ON u.unit_id = a.unit
JOIN sales_return c ON c.id = a.pid
JOIN sale s ON s.id = c.sales_id
JOIN customer_information d ON d.customer_id = c.customer_id
WHERE c.rdate >= '$from_date'
AND c.rdate <= '$to_date'
{$incidentFilter_r}
";

        // Filters
        if ($product_id) {
            $sql2 .= " AND a.product = '$product_id'";
        }

        if ($empid != "All") {
            $sql2 .= " AND AES_DECRYPT(c.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql2 .= " AND c.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql2 .= " AND c.branch IN ($branchList)";
            }
        }


        // Final UNION
        $finalQuery = "
       ($sql1)
          UNION ALL
        ($sql2)
        ORDER BY date DESC
        ";

        $query = $this->db->query($finalQuery);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function product_list()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->where('status', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function product_list_stock()
    {
        $this->db->select('*');
        $this->db->from('product_information');
        $this->db->where('status', 1);
        $this->db->where('stock', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    //    ============= its for sales_report_category_wise ===============
    public function sales_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch)
    {
        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        // Sales Query
        $sql1 = "
SELECT
    a.product,
    b.product_name,
    b.product_model,
    SUM(IFNULL(AES_DECRYPT(a.quantity,'$encryption_key'),0)) AS quantity,
    SUM(IFNULL(AES_DECRYPT(a.total_price,'$encryption_key'),0)) AS total_price,
    c.category_name,
    cr.conversion_ratio,
    u1.unit_name AS sub,
    u2.unit_name AS master
FROM sale_details a
LEFT  JOIN product_information b  ON b.id          = a.product
LEFT  JOIN product_category    c  ON c.category_id = b.category_id
JOIN  sale                     d  ON d.id          = a.pid
LEFT  JOIN subunit_product     sp ON sp.product_id = b.id AND sp.first = 1
LEFT  JOIN units               u1 ON u1.unit_id    = sp.unit_id
LEFT  JOIN units               u2 ON u2.unit_id    = b.unit
LEFT  JOIN conversion_ratio    cr ON cr.product    = b.id AND u1.unit_id = cr.subunit
WHERE d.date >= '$from_date'
AND d.date <= '$to_date'

";

        if ($product) {
            $sql1 .= " AND a.product = '$product'";
        }

        if ($category) {
            $sql1 .= " AND b.category_id = '$category'";
        }

        if ($empid != "All") {
            $sql1 .= " AND AES_DECRYPT(d.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql1 .= " AND d.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND d.branch IN ($branchList)";
            }
        }

        $sql1 .= " GROUP BY a.product";


        // Sales Return Query (NEGATIVE VALUES 🔥)
        $sql2 = "
SELECT
    a.product,
    b.product_name,
    b.product_model,
    SUM(IFNULL(AES_DECRYPT(a.rqty,'$encryption_key'),0)) * -1 AS quantity,
    SUM(IFNULL(AES_DECRYPT(a.total_price,'$encryption_key'),0)) * -1 AS total_price,
    c.category_name,
    cr.conversion_ratio,
    u1.unit_name AS sub,
    u2.unit_name AS master
FROM sales_return_details a
LEFT  JOIN product_information b  ON b.id          = a.product
LEFT  JOIN product_category    c  ON c.category_id = b.category_id
JOIN  sales_return             d  ON d.id          = a.pid
LEFT  JOIN subunit_product     sp ON sp.product_id = b.id AND sp.first = 1
LEFT  JOIN units               u1 ON u1.unit_id    = sp.unit_id
LEFT  JOIN units               u2 ON u2.unit_id    = b.unit
LEFT  JOIN conversion_ratio    cr ON cr.product    = b.id AND u1.unit_id = cr.subunit
WHERE d.rdate >= '$from_date'
AND d.rdate <= '$to_date'
";

        if ($product) {
            $sql2 .= " AND a.product = '$product'";
        }

        if ($category) {
            $sql2 .= " AND b.category_id = '$category'";
        }

        if ($empid != "All") {
            $sql2 .= " AND AES_DECRYPT(d.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql2 .= " AND d.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql2 .= " AND d.branch IN ($branchList)";
            }
        }

        $sql2 .= " GROUP BY a.product";


        // Final UNION + FINAL GROUP
        $finalQuery = "
SELECT
    product,
    product_name,
    product_model,
    SUM(quantity) AS quantity,
    SUM(total_price) AS total_price,
    category_name,
    conversion_ratio,
    sub,
    master
FROM (
    ($sql1)
    UNION ALL
    ($sql2)
) AS combined
GROUP BY product, conversion_ratio, sub, master
ORDER BY quantity DESC
";

        $query = $this->db->query($finalQuery);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
    }



    // sales return data
    public function sales_return_list($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.customer_name');
        $this->db->from('product_return a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('usablity', 1);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    // return supplier
    public function supplier_return($start, $end)
    {
        $this->db->select('a.net_total_amount,a.*,b.supplier_name');
        $this->db->from('product_return a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('usablity', 2);
        $this->db->where('a.date_return >=', $start);
        $this->db->where('a.date_return <=', $end);
        $this->db->order_by('a.date_return', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    // tax report query
    public function retrieve_dateWise_tax($from_date, $to_date)
    {
        $this->db->select("a.*");
        $this->db->from('invoice a');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        $this->db->group_by('a.invoice_id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    //Total profit report
    public function total_profit_report($start_date, $end_date)
    {
        $this->db->select("a.date,a.invoice,b.invoice_id, CAST(sum(total_price) AS DECIMAL(16,2)) as total_sale");
        $this->db->select('CAST(sum(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) as total_supplier_rate', FALSE);
        $this->db->select("CAST(SUM(total_price) - SUM(`quantity`*`supplier_rate`) AS DECIMAL(16,2)) AS total_profit");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.id');
        $this->db->where('a.date >=', $start_date);
        $this->db->where('a.date <=', $end_date);
        $this->db->group_by('b.invoice_id');
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    // public function profit_report_invoicewise($from_date, $to_date, $customer_id = null)
    // {
    //     $encryption_key = Config::$encryption_key;
    //     $this->db->select("a.date, a.invoice, b.invoice_id, AES_DECRYPT(c.customer_name, '$encryption_key') AS customer_name");
    //     $this->db->select('CAST(SUM(b.total_price) AS DECIMAL(16,2)) as total_sale', FALSE);
    //     $this->db->select('CAST(SUM(b.quantity * b.supplier_rate) AS DECIMAL(16,2)) as total_supplier_rate', FALSE);
    //     $this->db->select('CAST(SUM(b.total_price) - SUM(b.quantity * b.supplier_rate) AS DECIMAL(16,2)) AS total_profit', FALSE);
    //     $this->db->from('invoice a');
    //     $this->db->join('invoice_details b', 'b.invoice_id = a.id');
    //     $this->db->join('customer_information c', 'c.customer_id = a.customer_id', 'left');
    //     $this->db->where('a.date >=', $from_date);
    //     $this->db->where('a.date <=', $to_date);
    //     if (!empty($customer_id)) {
    //         $this->db->where('a.customer_id', $customer_id);
    //     }
    //     $this->db->group_by('b.invoice_id');
    //     $this->db->order_by('a.invoice', 'desc');
    //     $query = $this->db->get();
    //     if ($query->num_rows() > 0) {
    //         return $query->result_array();
    //     }
    //     return false;
    // }

      public function profit_report_invoicewise($from_date, $to_date, $emp_id, $branch, $customer_id = null, $incident_type = null, $employee_id = null)
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $p_branch        = !empty($branch)        ? "'$branch'"           : 'NULL';
        $p_from_date     = !empty($from_date)     ? "'$from_date'"        : 'NULL';
        $p_to_date       = !empty($to_date)       ? "'$to_date'"          : 'NULL';
        $p_type2         = ($emp_id != 'All')     ? "'$emp_id'"           : 'NULL';
        $p_customer      = !empty($customer_id)   ? (int)$customer_id     : 'NULL';
        $p_incident_type = !empty($incident_type) ? (int)$incident_type   : 'NULL';
        $p_employee      = !empty($employee_id)   ? (int)$employee_id     : 'NULL';

        if (empty($branch) && $this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchList  = implode(',', array_map(fn($b) => "'$b'", $branchids));
            $p_branch    = 'NULL';
            $extra_filter = " AND s.branch IN ($branchList)";
        } else {
            $extra_filter = '';
        }

        $sql = "
            -- Unit Cost Logic Specification v1.1 (Phase 6, UCL-75) — cost per invoice is each
            -- sale line's stored cost at the time it was sold (sale_details.line_cost_value, the
            -- moving-average cost per Product+Batch, Phase 4), not an unbounded all-time average
            -- purchase price across the product+batch's entire purchase history (what this CTE
            -- computed before — notably, with no date bound at all, so it didn't even respect
            -- this report's own date filter). UCL-76: Stock=Disable products never get a
            -- unit_cost (never tracked in stock_details), so they still fall back to Fixed
            -- Purchase Price x Qty.
            WITH sale_cost AS (
                SELECT
                    sd.pid,
                    SUM(
                        CASE WHEN pi.stock = 0
                            THEN (AES_DECRYPT(sd.quantity, '$encryption_key') + 0) * (AES_DECRYPT(pi.cost_price, '$encryption_key') + 0)
                            ELSE (AES_DECRYPT(sd.line_cost_value, '$encryption_key') + 0)
                        END
                    ) AS cost
                FROM sale_details sd
                INNER JOIN sale s ON s.id = sd.pid
                INNER JOIN product_information pi ON pi.id = sd.product
                WHERE (s.date >= $p_from_date OR $p_from_date IS NULL)
                  AND (s.date <= $p_to_date   OR $p_to_date   IS NULL)
                  AND ($p_branch IS NULL OR s.branch = $p_branch)
                  AND ($p_type2  IS NULL OR AES_DECRYPT(s.type2, '$encryption_key') = $p_type2)
                  AND ($p_customer IS NULL OR s.customer_id = $p_customer)
                  AND ($p_incident_type IS NULL OR s.incidenttype = $p_incident_type)
                  AND ($p_employee IS NULL OR s.employee_id = $p_employee)
                  $extra_filter
                GROUP BY sd.pid
            )
            SELECT
                AES_DECRYPT(s.sale_id,        '$encryption_key')                              AS sale_id,
                s.date                                                                         AS sale_date,
                ROUND(AES_DECRYPT(s.grandTotal, '$encryption_key'), 2)                        AS grand_total,
                ROUND(IFNULL(AES_DECRYPT(s.total_vat_amnt, '$encryption_key'), 0), 2)         AS vat_val,
                ROUND(AES_DECRYPT(s.grandTotal, '$encryption_key') - IFNULL(AES_DECRYPT(s.total_vat_amnt, '$encryption_key'), 0), 2) AS invoice_total,
                ROUND(sc.cost, 2)                                                              AS cost,
                ROUND(AES_DECRYPT(s.grandTotal, '$encryption_key') - IFNULL(AES_DECRYPT(s.total_vat_amnt, '$encryption_key'), 0) - sc.cost, 2) AS profit
            FROM sale s
            LEFT JOIN sale_cost  sc ON sc.pid  = s.id
            LEFT JOIN employee_history e ON e.id = s.employee_id
            WHERE (s.date >= $p_from_date OR $p_from_date IS NULL)
              AND (s.date <= $p_to_date   OR $p_to_date   IS NULL)
              AND ($p_branch IS NULL OR s.branch = $p_branch)
              AND ($p_type2  IS NULL OR AES_DECRYPT(s.type2, '$encryption_key') = $p_type2)
              AND ($p_customer IS NULL OR s.customer_id = $p_customer)
              AND ($p_incident_type IS NULL OR s.incidenttype = $p_incident_type)
              AND ($p_employee IS NULL OR s.employee_id = $p_employee)
              $extra_filter
            ORDER BY s.date DESC
        ";

        return $this->db->query($sql)->result_array();
    }
    
    public function payment_methods()
    {
        return $this->db->select('ac.*, IFNULL(pt.nature, "") AS nature')
            ->from('acc_coa ac')
            ->join('payment_type pt', 'LOWER(pt.name) = LOWER(ac.HeadName)', 'left')
            ->where('ac.PHeadName', 'Cash')
            ->or_where('ac.PHeadName', 'Cash at Bank')
            ->get()
            ->result();
    }

    public function received_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Debit) as total_received')
            ->from('acc_transaction')
            ->where('COAID', $headcode)
            ->where('CreateBy', $seller_id)
            ->where('VDate', date('Y-m-d'))
            ->where('IsAppove', 1)
            ->get()
            ->row();
        return ($data ? $data->total_received : 0);
    }

    public function paid_bypayment_method($seller_id, $headcode)
    {
        $data = $this->db->select('sum(Credit) as total_paid')
            ->from('acc_transaction')
            ->where('COAID', $headcode)
            ->where('CreateBy', $seller_id)
            ->where('VDate', date('Y-m-d'))
            ->where('IsAppove', 1)
            ->get()
            ->row();
        return ($data ? $data->total_paid : 0);
    }

    public function create_opening($data = [])
    {
        return $this->db->insert('closing_records', $data);
    }


    public function store_list()
    {
        $this->db->select('*');
        $this->db->from('store');
        $this->db->where('status', 1);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function sales_return_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id)
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $this->db->select("a.rdate as date,AES_DECRYPT(a.sales_return_id,'" . $encryption_key . "') AS return_id,AES_DECRYPT(b.sale_id,'" . $encryption_key . "') AS invoiceno,AES_DECRYPT(c.customer_name,'" . $encryption_key . "') AS customer_name,AES_DECRYPT(a.grandTotal,'" . $encryption_key . "') AS total");
        $this->db->from('sales_return a');
        $this->db->join('sale b', 'b.id = a.sales_id', 'left');
        $this->db->join('customer_information c', 'c.customer_id = a.customer_id', 'left');
        $this->db->where('a.rdate >=', $from_date);
        $this->db->where('a.rdate <=', $to_date);

        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $empid);
        }

        if ($customer_id) {
            $this->db->where('a.customer_id', $customer_id);
        }

        if ($branch) {
            $this->db->where('a.branch', $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
                $this->db->where_in('a.branch', $branchids);
            }
        }

        $this->db->order_by('a.rdate', 'desc');
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function sales_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id, $status = '', $incident_type = '')
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $this->db->select("a.date,
            AES_DECRYPT(a.sale_id,'" . $encryption_key . "') as invoiceno,
            AES_DECRYPT(b.customer_name, '{$encryption_key}') AS customer_name,
            AES_DECRYPT(a.grandTotal,'" . $encryption_key . "') as total,
            a.status,
            CASE
                WHEN a.status = 0 THEN 'Ordered'
                WHEN a.status = 1 THEN 'Sold'
                WHEN a.status = 2 THEN 'Cancelled'
                ELSE 'Unknown'
            END AS status_label");
        $this->db->from('sales_order a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);

        if ($empid != "All") {
            // $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $empid);
            $this->db->where(
                "AES_DECRYPT(a.type2, '$encryption_key') IN ('C', '$empid')");
        }

        if ($customer_id) {
            $this->db->where('a.customer_id', $customer_id);
        }

        if ($status !== '' && $status !== null) {
            $this->db->where('a.status', $status);
        }

        if ($incident_type !== '' && $incident_type !== null) {
            $this->db->where('a.incidenttype', $incident_type);
        }

        if ($branch) {
            $this->db->where('a.branch', $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                if (!empty($branchids)) {
                    $this->db->where_in('a.branch', $branchids);
                }
            }
        }

        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function retrieve_product_purchase_report($from_date, $to_date, $product_id, $empid, $branch,$incident_type)
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if ($branchResult) {
            $branchids = array_column($branchResult, 'id');
        }

         $incidentFilter_p = $incidentFilter_r = '';
        if ($incident_type !== '' && $incident_type !== null&&$incident_type != 3 ) {
            $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND c.incidenttype = {$incidentEsc}";
            $incidentFilter_r = " AND c.incidenttype = 3";
        }else if($incident_type !== '' && $incident_type !== null&&$incident_type == 3 ) {
            // $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND c.incidenttype = 3";
            // $incidentFilter_r = " AND r.incidenttype = {$incidentEsc}";

        }

        // 🔹 PURCHASE QUERY
        $sql1 = "
    SELECT
        b.product_name,
        c.date,
        AES_DECRYPT(c.chalan_no,'$encryption_key') AS chalan_no,
        CASE
            WHEN c.incidenttype = '1' THEN 'International Purchase'
            WHEN c.incidenttype = '2' THEN 'Local Purchase'
            ELSE 'Unknown'
        END AS incidenttype,
        AES_DECRYPT(a.product_rate,'$encryption_key') AS product_rate,
        AES_DECRYPT(a.total_price,'$encryption_key') AS total,
        AES_DECRYPT(d.supplier_name,'$encryption_key') AS supplier_name,
        CAST( ROUND( CASE
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                THEN AES_DECRYPT(a.quantity, '{$encryption_key}') + cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                THEN AES_DECRYPT(a.quantity, '{$encryption_key}') - cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                THEN AES_DECRYPT(a.quantity, '{$encryption_key}') * cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                THEN AES_DECRYPT(a.quantity, '{$encryption_key}') / cr2.conversion_ratio
            ELSE AES_DECRYPT(a.quantity, '{$encryption_key}')
        END, 6) AS UNSIGNED ) AS quantity,
        u.unit_name,
        cr_main.conversion_ratio,
        u_sub.unit_name AS sub,
        u_master.unit_name AS master
    FROM purchase_details a
    JOIN product_information b ON b.id = a.product
    LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = a.conversion_id
    JOIN units u ON u.unit_id = a.unit
    JOIN purchase c ON c.id = a.pid
    JOIN supplier_information d ON d.supplier_id = c.supplier_id
    LEFT JOIN subunit_product  sp_m    ON sp_m.product_id   = b.id AND sp_m.first = 1
    LEFT JOIN units            u_sub   ON u_sub.unit_id     = sp_m.unit_id
    LEFT JOIN units            u_master ON u_master.unit_id = b.unit
    LEFT JOIN conversion_ratio cr_main ON cr_main.product   = b.id AND u_sub.unit_id = cr_main.subunit
    WHERE c.date >= '$from_date'
    AND c.date <= '$to_date'
    {$incidentFilter_p}
    ";

        // 🔹 PURCHASE RETURN QUERY
        $sql2 = "
    SELECT
        b.product_name,
        c.rdate AS date,
        AES_DECRYPT(p.chalan_no,'$encryption_key') AS chalan_no,
        'Purchase Return' AS incidenttype,
        AES_DECRYPT(a.product_rate,'$encryption_key') AS product_rate,
        AES_DECRYPT(a.total_price,'$encryption_key') * -1 AS total,
        AES_DECRYPT(d.supplier_name,'$encryption_key') AS supplier_name,
        CAST( ROUND( CASE
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '+'
                THEN AES_DECRYPT(a.rqty, '{$encryption_key}') + cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '-'
                THEN AES_DECRYPT(a.rqty, '{$encryption_key}') - cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '*'
                THEN AES_DECRYPT(a.rqty, '{$encryption_key}') * cr2.conversion_ratio
            WHEN a.conversion_id IS NOT NULL AND cr2.convertiontype = '/'
                THEN AES_DECRYPT(a.rqty, '{$encryption_key}') / cr2.conversion_ratio
            ELSE AES_DECRYPT(a.rqty, '{$encryption_key}')
        END, 6) AS UNSIGNED ) AS quantity,
        u.unit_name,
        cr_main.conversion_ratio,
        u_sub.unit_name AS sub,
        u_master.unit_name AS master
    FROM purchase_return_details a
    JOIN product_information b ON b.id = a.product
    LEFT JOIN conversion_ratio cr2 ON cr2.conversionratio_id = a.conversion_id
    JOIN units u ON u.unit_id = a.unit
    JOIN purchase_return c ON c.id = a.pid
    JOIN purchase p ON p.id = c.purchase_id
    JOIN supplier_information d ON d.supplier_id = c.supplier_id
    LEFT JOIN subunit_product  sp_m     ON sp_m.product_id   = b.id AND sp_m.first = 1
    LEFT JOIN units            u_sub    ON u_sub.unit_id     = sp_m.unit_id
    LEFT JOIN units            u_master ON u_master.unit_id  = b.unit
    LEFT JOIN conversion_ratio cr_main  ON cr_main.product   = b.id AND u_sub.unit_id = cr_main.subunit
    WHERE c.rdate >= '$from_date'
    AND c.rdate <= '$to_date'
    {$incidentFilter_r}
    ";

        // 🔹 COMMON FILTERS
        if ($product_id) {
            $sql1 .= " AND a.product = '$product_id'";
            $sql2 .= " AND a.product = '$product_id'";
        }

        if ($empid != "All") {
            $sql1 .= " AND AES_DECRYPT(c.type2,'$encryption_key') = '$empid'";
            $sql2 .= " AND AES_DECRYPT(c.type2,'$encryption_key') = '$empid'";
        }

        if ($branch) {
            $sql1 .= " AND c.branch = '$branch'";
            $sql2 .= " AND c.branch = '$branch'";
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $branchList = implode(",", array_map(fn($b) => "'$b'", $branchids));
                $sql1 .= " AND c.branch IN ($branchList)";
                $sql2 .= " AND c.branch IN ($branchList)";
            }
        }

        // 🔥 FINAL UNION
        $finalQuery = "
    ($sql1)
    UNION ALL
    ($sql2)
    ORDER BY date DESC
    ";

        $query = $this->db->query($finalQuery);

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function gdn_reportinvoicewise($from_date, $to_date, $empid, $store, $incident_type, $customer_id)
    {
        $encryption_key = Config::$encryption_key;

        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id=sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();

        $storeids = [];
        if (isset($storeResult)) {
            $storeids = array_column($storeResult, 'id');
        }

        // Match exact query pattern from working Stock_model::gdnstock()
        $this->db->select("ds.id,
            AES_DECRYPT(ds.gdn_id,'$encryption_key') AS gdn_id,
            ds.date,
            CASE
                WHEN ds.type IN ('sale','wholesale') THEN AES_DECRYPT(p.sale_id,'$encryption_key')
                WHEN ds.type = 'purchasereturn'      THEN AES_DECRYPT(pr.purchase_return_id,'$encryption_key')
                ELSE ''
            END AS voucherno,
            CASE
                WHEN ds.type = 'sale' THEN 'Sale'
                WHEN ds.type = 'wholesale' THEN 'Wholesale'
                WHEN ds.type = 'stockdisposal' THEN 'Stock Disposal'
                WHEN ds.type = 'purchasereturn' THEN 'Purchase Return'
                WHEN ds.type='storetransfer' THEN 'Store Transfer'
                ELSE REPLACE(ds.type, '_', ' ')
            END AS incidenttype,
            s.name as store,
            AES_DECRYPT(ci.customer_name,'$encryption_key') as customer_name");
        $this->db->from('gdn_stock ds');
        $this->db->join('sale p', 'ds.voucherno = p.id AND ds.type IN (\'sale\',\'wholesale\')', 'left');
        $this->db->join('purchase_return pr', 'ds.voucherno = pr.id AND ds.type = \'purchasereturn\'', 'left');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('customer_information ci', 'ci.customer_id = ds.customer_id', 'left');

        $this->db->where('pd.type', 'gdn_stock');

        // Add date range filter
        $this->db->where('ds.date >=', $from_date);
        $this->db->where('ds.date <=', $to_date);

        // type2 filter (A or B based on user password)
        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $empid);
        }

        // Store filter - match exact pattern from Stock_model
        if ($store > 0) {
            $this->db->where('s.id', $store);
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $this->db->where_in('s.id', $storeids);
            }
        }

        // Optional filters
        if ($incident_type) {
            $this->db->where('ds.type', $incident_type);
        }

        if ($customer_id) {
            $this->db->where('ds.customer_id', $customer_id);
        }

        $this->db->order_by('ds.date', 'desc');
        $this->db->group_by('gdn_id');

        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function service_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id = null)
    {
        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $this->db->select("a.date,a.eod_date,AES_DECRYPT(a.service_id,'" . $encryption_key . "') as invoiceno, AES_DECRYPT(b.customer_name, '{$encryption_key}') AS customer_name,AES_DECRYPT(a.grandTotal,'" . $encryption_key . "')  as total");
        $this->db->from('service a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);
        if ($empid != "All") {
             $this->db->where("AES_DECRYPT(a.type2, '$encryption_key') IN ('C', '$empid')", null, false);
        }
        if (!empty($customer_id)) {
            $this->db->where('a.customer_id', $customer_id);
        }
        if ($branch) {
            $this->db->where("a.branch", $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {

                $this->db->where_in('a.branch', $branchids);
            }
        }
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function service_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id,$status)
    {
        $encryption_key = Config::$encryption_key;
        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

            $this->db->select("
            a.date,
            a.eod_date,
            AES_DECRYPT(a.service_order_id, '" . $encryption_key . "') AS invoiceno,
            AES_DECRYPT(b.customer_name, '" . $encryption_key . "') AS customer_name,
            AES_DECRYPT(a.grandTotal, '" . $encryption_key . "') AS total,
            COUNT(sod.id) AS item_count,
            CASE
                WHEN a.status = 0 THEN 'Ordered'
                WHEN a.status = 1 THEN 'Invoiced'
                ELSE 'Canceled'
            END AS status_label
        ", false);
        $this->db->from('service_order a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id', 'left');
        $this->db->join('service_order_details sod', 'sod.pid = a.id', 'left');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);

        if ($empid != "All") {
          $this->db->where("AES_DECRYPT(a.type2, '$encryption_key') IN ('C', '$empid')", null, false);

        }

        if ($customer_id) {
            $this->db->where('a.customer_id', $customer_id);
        }

        if ($status) {
            $this->db->where('a.status', $status);
        }

        if ($status==0) {
            $this->db->where('a.status', $status);
        }

        if ($branch) {
            $this->db->where('a.branch', $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                if (!empty($branchids)) {
                    $this->db->where_in('a.branch', $branchids);
                }
            }
        }

        $this->db->group_by('a.id');
        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function grn_reportinvoicewise($from_date, $to_date, $empid, $store, $incident_type, $supplier_id)
    {
        $encryption_key = Config::$encryption_key;

        $storeResult = $this->db->select("store.id")
            ->from('sec_store')
            ->join('store', 'store.id=sec_store.storeid')
            ->where('sec_store.userid', $this->session->userdata('id'))
            ->group_by('sec_store.storeid')
            ->get()
            ->result();

        $storeids = [];
        if (isset($storeResult)) {
            $storeids = array_column($storeResult, 'id');
        }

        $this->db->select("ds.id,
            AES_DECRYPT(ds.grn_id,'$encryption_key') AS grn_id,
            ds.date,
            CASE
                WHEN ds.type = 'purchase'    THEN AES_DECRYPT(p.chalan_no,'$encryption_key')
                WHEN ds.type = 'salesreturn' THEN AES_DECRYPT(sr.sales_return_id,'$encryption_key')
                ELSE ''
            END AS voucherno,
            CASE
                WHEN ds.type = 'purchase' THEN 'Purchase'
                WHEN ds.type = 'salesreturn' THEN 'Sales Return'
                WHEN ds.type = 'storetransfer' THEN 'Store Transfer'
                ELSE REPLACE(ds.type, '_', ' ')
            END AS incidenttype,
            s.name as store,
            AES_DECRYPT(si.supplier_name,'$encryption_key') as supplier_name");
        $this->db->from('grn_stock ds');
        $this->db->join('purchase p', 'ds.voucherno = p.id AND ds.type = \'purchase\'', 'left');
        $this->db->join('sales_return sr', 'ds.voucherno = sr.id AND ds.type = \'salesreturn\'', 'left');
        $this->db->join('phystock_details pd', 'pd.pid = ds.id', 'left');
        $this->db->join('store s', 's.id = pd.store', 'left');
        $this->db->join('supplier_information si', 'si.supplier_id = ds.supplier_id', 'left');

        $this->db->where('pd.type', 'grn_stock');
        $this->db->where('ds.date >=', $from_date);
        $this->db->where('ds.date <=', $to_date);

        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(ds.type2,'" . $encryption_key . "')", $empid);
        }

        if ($store > 0) {
            $this->db->where('s.id', $store);
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $this->db->where_in('s.id', $storeids);
            }
        }

        if (!empty($incident_type)) {
            $this->db->where('ds.type', $incident_type);
        }

        if (!empty($supplier_id)) {
            $this->db->where('ds.supplier_id', $supplier_id);
        }

        $this->db->order_by('ds.date', 'desc');
        $this->db->group_by('ds.id');

        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function purchase_return_reportinvoicewise($from_date, $to_date, $empid, $branch, $supplier_id)
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $this->db->select("po.rdate AS return_date,
            AES_DECRYPT(po.purchase_return_id, '{$encryption_key}') AS return_id,
            AES_DECRYPT(p.chalan_no, '{$encryption_key}') AS invoice_id,
            AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
            AES_DECRYPT(po.grandTotal, '{$encryption_key}') AS amount");
        $this->db->from('purchase_return po');
        $this->db->join('purchase p', 'p.id = po.purchase_id', 'left');
        $this->db->join('supplier_information si', 'si.supplier_id = po.supplier_id', 'left');
        $this->db->where('po.rdate >=', $from_date);
        $this->db->where('po.rdate <=', $to_date);

        if ($empid != "All") {
            $this->db->where("AES_DECRYPT(po.type2,'{$encryption_key}')", $empid);
        }

        if (!empty($supplier_id)) {
            $this->db->where('po.supplier_id', $supplier_id);
        }

        if (!empty($branch)) {
            $this->db->where('po.branch', $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('po.branch', $branchids);
            }
        }

        $this->db->order_by('po.rdate', 'desc');
        $this->db->order_by('return_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function purchase_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $supplier_id, $status = '', $incident_type = '')
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];
        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }

        $this->db->select("a.date,
            AES_DECRYPT(a.purchase_id, '{$encryption_key}') as invoiceno,
            AES_DECRYPT(b.supplier_name, '{$encryption_key}') as supplier_name,
            AES_DECRYPT(a.grandTotal, '{$encryption_key}') as total,
            a.status,
            CASE 
                WHEN a.status = 0 THEN 'Ordered'
                WHEN a.status = 1 THEN 'Purchased'
                WHEN a.status = 2 THEN 'Cancelled'
                ELSE 'Unknown'
            END AS status_label");
        $this->db->from('purchase_order a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id', 'left');
        $this->db->where('a.date >=', $from_date);
        $this->db->where('a.date <=', $to_date);

        if ($empid != "All") {
            $this->db->where(
                "AES_DECRYPT(a.type2, '$encryption_key') IN ('C', '$empid')");
        }

        if (!empty($supplier_id)) {
            $this->db->where('a.supplier_id', $supplier_id);
        }

        if ($status !== '' && $status !== null) {
            $this->db->where('a.status', $status);
        }

        if ($incident_type !== '' && $incident_type !== null) {
            $this->db->where('a.incidenttype', $incident_type);
        }

        if (!empty($branch)) {
            $this->db->where('a.branch', $branch);
        } else {
            if ($this->session->userdata('user_level2') != 1) {
                $this->db->where_in('a.branch', $branchids);
            }
        }

        $this->db->order_by('a.date', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function product_batch_summary_report($store, $category, $product, $supplier, $batch_type, $status)
    {
        $encryption_key = Config::$encryption_key;

        $setting = $this->db->select('COALESCE(expiry_alert_days,0) AS expiry_alert_days', false)
            ->from('web_setting')
            ->order_by('setting_id', 'asc')
            ->limit(1)
            ->get()
            ->row();

        $expiry_alert_days = isset($setting->expiry_alert_days) ? (int)$setting->expiry_alert_days : 0;

        $storeids = [];
        if (empty($store) && $this->session->userdata('user_level2') != 1) {
            $storeResult = $this->db->select('store.id')
                ->from('sec_store')
                ->join('store', 'store.id = sec_store.storeid')
                ->where('sec_store.userid', $this->session->userdata('id'))
                ->group_by('sec_store.storeid')
                ->get()
                ->result();

            if (!empty($storeResult)) {
                $storeids = array_column($storeResult, 'id');
            }
        }

        $this->db->select("
            pi.product_id,
            pi.product_name,
            pi.category_id,
            pi.supplier_id,
            pi.batchtype,
            pc.category_name,
            CAST(AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS CHAR) AS supplier_name,
            CAST(AES_DECRYPT(sb.batchid, '{$encryption_key}') AS CHAR) AS batch_id,
            SUM(AES_DECRYPT(sd.stock, '{$encryption_key}')) AS master_stock_qty,
            sb.busage,
            sb.mdate,
            sb.pdate,
            sb.edate,
            CAST(AES_DECRYPT(sb.mrp, '{$encryption_key}') AS DECIMAL(18,2)) AS mrp,
            cr.conversion_ratio,
            u1.unit_name AS sub,
            u2.unit_name AS master,
            uc.current_cost AS unit_cost,
            uc.current_value AS stock_value
        ", false);

        $this->db->from('stock_details sd');
        $this->db->join('product_information pi', 'pi.id = sd.product', 'inner');
        $this->db->join('product_category pc', 'pc.category_id = pi.category_id', 'inner');
        $this->db->join('stockbatch sb', 'sb.id = sd.batch', 'inner');
        $this->db->join('subunit_product sp', 'sp.product_id = pi.id AND sp.first = 1', 'left');
        $this->db->join('units u1', 'u1.unit_id = sp.unit_id', 'left');
        $this->db->join('units u2', 'u2.unit_id = pi.unit', 'left');
        $this->db->join('conversion_ratio cr', 'cr.product = pi.id AND u1.unit_id = cr.subunit', 'left');
        $this->db->join('supplier_information si', 'si.supplier_id = pi.supplier_id', 'left');
        // Unit Cost Logic Specification v1.1 (Phase 6) — this report already groups by
        // (pi.id, sb.id), exactly matching unit_cost's own (product, batch) key, so the latest
        // cost per group joins in cleanly with no fan-out.
        $this->db->join($this->unit_cost_latest_sql('uc'), 'uc.product = pi.id AND uc.batch = sb.id', 'left');

        $this->db->where('sb.status', 1);
        $this->db->where('pi.stock', 1);

        if (!empty($store)) {
            $this->db->where('sd.store', $store);
        } elseif (!empty($storeids)) {
            $this->db->where_in('sd.store', $storeids);
        }

        if (!empty($category)) {
            $this->db->where('pi.category_id', $category);
        }

        if (!empty($product)) {
            $this->db->where('pi.id', $product);
        }

        if (!empty($supplier)) {
            $this->db->where('pi.supplier_id', $supplier);
        }

        if (!empty($batch_type)) {
            $this->db->where('pi.batchtype', $batch_type);
        }

        $this->db->group_by('pi.id, sb.id');
        $this->db->order_by('pc.category_name', 'asc');
        $this->db->order_by('pi.product_name', 'asc');
        $this->db->order_by('sb.id', 'asc');

        $rows = $this->db->get()->result_array();

        $today = date('Y-m-d');
        $result = [];
        $sl = 1;

        foreach ($rows as $row) {
            $is_single = strtolower((string)$row['busage']) === 'single';
            $expiry_date = !empty($row['edate']) ? date('Y-m-d', strtotime($row['edate'])) : '';

            $status_text = 'Not Expired';
            $is_expired = !empty($expiry_date) && strtotime($today) >= strtotime($expiry_date);
            $alert_start = !empty($expiry_date) ? date('Y-m-d', strtotime($expiry_date . ' -' . $expiry_alert_days . ' day')) : '';
            $is_to_be_expired = !$is_expired && !empty($alert_start) && strtotime($today) >= strtotime($alert_start);

            if(empty($expiry_date)){
                $status_text = 'N/A';
            }else
            if ($is_expired) {
                $status_text = 'Expired';
            } elseif ($is_to_be_expired) {
                $status_text = 'To be Expired';
            }

            if (!empty($status)) {
                if ($status === 'expired' && $status_text !== 'Expired') continue;
                if ($status === 'to_be_expired' && $status_text !== 'To be Expired') continue;
                if ($status === 'not_expired' && $status_text !== 'Not Expired') continue;
            }

            $result[] = [
                'sl'               => $sl++,
                'category'         => !empty($row['category_name']) ? $row['category_name'] : '-',
                'product_id'       => !empty($row['product_id']) ? $row['product_id'] : '-',
                'product_name'     => !empty($row['product_name']) ? $row['product_name'] : '-',
                'supplier'         => !empty($row['supplier_name']) ? $row['supplier_name'] : '-',
                'batch_id'         => !empty($row['batch_id']) ? $row['batch_id'] : '-',
                'manufacture_date' => $is_single && !empty($row['mdate']) ? $row['mdate'] : 'n/a',
                'packing_date'     => $is_single && !empty($row['pdate']) ? $row['pdate'] : 'n/a',
                'expiry_date'      => $is_single && !empty($row['edate']) ? $row['edate'] : 'n/a',
                'mrp'              => is_numeric($row['mrp']) ? (float)$row['mrp'] : 0,
                'master_stock_qty' => (float)$row['master_stock_qty'],
                'status'           => $status_text,
                'conversion_ratio' => $row['conversion_ratio'],
                'master'           => $row['master'],
                'sub'              => $row['sub'],
                // Unit Cost Logic Specification v1.1 (Phase 6) — blank/0 when this batch has no
                // unit_cost history yet (never costed before this feature existed), not an error.
                'unit_cost'        => is_numeric($row['unit_cost']) ? (float)$row['unit_cost'] : 0,
                'stock_value'      => is_numeric($row['stock_value']) ? (float)$row['stock_value'] : 0,
            ];
        }

        return $result;
    }


     //Retrieve todays_purchase_report
    public function bdtask_purchase_report($from_date, $to_date, $empid, $branch, $supplier_id, $incident_type = '')
    {
        $encryption_key = Config::$encryption_key;

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id=sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = [];

        if (isset($branchResult)) {
            $branchids = array_column($branchResult, 'id');
        }


        date_default_timezone_set('Asia/Colombo');

        $fromEsc = $this->db->escape($from_date);
        $toEsc   = $this->db->escape($to_date);

        $empFilter_p = $empFilter_r = '';
        if ($empid != "All") {
            $empEsc      = $this->db->escape($empid);
            $empFilter_p = " AND AES_DECRYPT(a.type2,'{$encryption_key}') = {$empEsc}";
            $empFilter_r = " AND AES_DECRYPT(r.type2,'{$encryption_key}') = {$empEsc}";
        }

        $supplierFilter_p = $supplierFilter_r = '';
        $branchFilter_p   = $branchFilter_r   = '';
        if ($supplier_id) {
            $sid              = intval($supplier_id);
            $supplierFilter_p = " AND a.supplier_id = {$sid}";
            $supplierFilter_r = " AND r.supplier_id = {$sid}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchIn       = implode(',', array_map('intval', $branchids));
            $branchFilter_p = " AND a.branch IN ({$branchIn})";
            $branchFilter_r = " AND r.branch IN ({$branchIn})";
        }

        $incidentFilter_p = $incidentFilter_r = '';
        if ($incident_type !== '' && $incident_type !== null&&$incident_type != 3 ) {
            $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND a.incidenttype = {$incidentEsc}";
            $incidentFilter_r = " AND r.incidenttype = 3";
        }else if($incident_type !== '' && $incident_type !== null&&$incident_type == 3 ) {
            // $incidentEsc      = $this->db->escape($incident_type);
            $incidentFilter_p = " AND a.incidenttype = 3";
            // $incidentFilter_r = " AND r.incidenttype = {$incidentEsc}";

        }
        $sql = "
            SELECT a.date,
                   AES_DECRYPT(a.chalan_no,'{$encryption_key}') AS invoiceno,
                   AES_DECRYPT(b.supplier_name,'{$encryption_key}') AS supplier_name,
                   CAST(AES_DECRYPT(a.grandTotal,'{$encryption_key}') AS DECIMAL(18,2)) AS total,
                   'Purchase' AS type,
                    CASE
            WHEN  a.incidenttype = '1' THEN 'International Purchase'
            WHEN  a.incidenttype = '2' THEN 'Local Purchase'
            ELSE 'Unknown'
        END AS incidenttype
            FROM purchase a
            JOIN supplier_information b ON b.supplier_id = a.supplier_id
            WHERE a.date >= {$fromEsc} AND a.date <= {$toEsc}
              {$empFilter_p}{$supplierFilter_p}{$branchFilter_p}{$incidentFilter_p}

            UNION ALL

            SELECT r.date,
                   AES_DECRYPT(p.chalan_no,'{$encryption_key}') AS invoiceno,
                   AES_DECRYPT(b.supplier_name,'{$encryption_key}') AS supplier_name,
                   -CAST(AES_DECRYPT(r.grandTotal,'{$encryption_key}') AS DECIMAL(18,2)) AS total,
                   'Purchase Return' AS type,
                    'Purchase Return' AS incidenttype
            FROM purchase_return r
            JOIN purchase p ON p.id = r.purchase_id
            JOIN supplier_information b ON b.supplier_id = r.supplier_id
            WHERE r.date >= {$fromEsc} AND r.date <= {$toEsc}
              {$empFilter_r}{$supplierFilter_r}{$branchFilter_r}{$incidentFilter_r}
            ORDER BY date DESC
        ";

        $logFilePath = 'logfile.log';
        $fileHandle = fopen($logFilePath, 'a');
        $logMessage = json_encode($sql);
        fwrite($fileHandle, $logMessage . "\n");
        fclose($fileHandle);

        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }
}
