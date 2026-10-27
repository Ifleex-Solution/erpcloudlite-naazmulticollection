<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Home_model extends CI_Model
{

    public function total_sales_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_amount) as totalsales");
        $this->db->from('invoice');
        $this->db->where('date >=', $days['start_date']);
        $this->db->where('date <=', $days['end_date']);
        $query = $this->db->get()->row();
        return (!empty($query->totalsales) ? $query->totalsales : 1);
    }
    public function yearmonthval($date)
    {
        list($month, $year) = explode(' ', $date);
        switch ($month) {
            case "January":
                $month = '01';
                break;
            case "February":
                $month = '02';
                break;
            case "March":
                $month = '03';
                break;
            case "April":
                $month = '04';
                break;
            case "May":
                $month = '05';
                break;
            case "June":
                $month = '06';
                break;
            case "July":
                $month = '07';
                break;
            case "August":
                $month = '08';
                break;
            case "September":
                $month = '09';
                break;
            case "October":
                $month = '10';
                break;
            case "November":
                $month = '11';
                break;
            case "December":
                $month = '12';
                break;
        }
        $fdate = $year . '-' . $month . '-' . '01';
        $lastday = date('t', strtotime($fdate));
        $edate = $year . '-' . $month . '-' . $lastday;
        $startd    = $fdate;
        $data['start_date'] = $startd;
        $data['end_date'] = $edate;
        return $data;
    }


    public function total_purchase_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(grand_total_amount) as totalpurchase");
        $this->db->from('product_purchase');
        $this->db->where('purchase_date >=', $days['start_date']);
        $this->db->where('purchase_date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalpurchase)) {
            return $query->row()->totalpurchase;
        } else {
            return 1;
        }
    }

    public function total_expense_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("*");
        $this->db->where('PHeadName', 'Expence');
        $this->db->from('acc_coa');
        $query = $this->db->get();
        $result =  $query->result_array();
        $totalamount = 0;
        foreach ($result as $expense) {
            $amount = $this->db->select('ifnull(sum(Debit),0) as amount')->from('acc_transaction')->where('VDate >=', $days['start_date'])->where('VDate <=', $days['end_date'])->where('COAID', $expense['HeadCode'])->get()->row();
            $totalamount = $totalamount + $amount->amount;
        }

        return (!empty($totalamount) ? $totalamount : 1);
    }


    // Total Employee Salary
    public function total_employee_salary($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_salary) as totalsalary");
        $this->db->from('employee_salary_payment');
        $this->db->where('payment_date >=', $days['start_date']);
        $this->db->where('payment_date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalsalary)) {
            return $query->row()->totalsalary;
        } else {
            return 1;
        }
    }

    public function total_service_amount($date = null)
    {
        $date = (!empty($date) ? $date : date('F Y'));
        $days = $this->yearmonthval($date);
        $this->db->select("sum(total_amount) as totalservice");
        $this->db->from('service_invoice');
        $this->db->where('date >=', $days['start_date']);
        $this->db->where('date <=', $days['end_date']);
        $query = $this->db->get();
        if (!empty($query->row()->totalservice)) {
            return $query->row()->totalservice;
        } else {
            return 1;
        }
    }


    public function yearly_invoice_report($month = null)
    {
        $encryption_key = Config::$encryption_key;
        $usertype       = $this->session->userdata('user_level2');
        $type2          = $usertype == 3 ? "B" : "A";

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = array_column($branchResult, 'id');

        // FALSE on select() stops CI3 adding backticks inside the aggregate expression
        $this->db->select("COALESCE(SUM(CAST(AES_DECRYPT(grandTotal, '$encryption_key') AS DECIMAL(18,2))), 0) AS total_sale", FALSE);
        $this->db->from('sale');
        $this->db->where("MONTH(date) = " . (int)$month, NULL, FALSE);
        $this->db->where("AES_DECRYPT(type2, '$encryption_key') = '$type2'", NULL, FALSE);
        $this->db->where('YEAR(date) = YEAR(CURRENT_TIMESTAMP)', NULL, FALSE);

        if ($usertype != 1 && !empty($branchids)) {
            $this->db->where_in('branch', $branchids);
        }

        return $this->db->get()->row();
    }

    public function yearly_purchase_report($month = null)
    {
        $encryption_key = Config::$encryption_key;
        $usertype       = $this->session->userdata('user_level2');
        $type2          = $usertype == 3 ? "B" : "A";

        $branchResult = $this->db->select("branch.id")
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()
            ->result();

        $branchids = array_column($branchResult, 'id');

        // FALSE on select() stops CI3 adding backticks inside the aggregate expression
        $this->db->select("COALESCE(SUM(CAST(AES_DECRYPT(grandTotal, '$encryption_key') AS DECIMAL(18,2))), 0) AS total_purchase", FALSE);
        $this->db->from('purchase');
        $this->db->where("MONTH(date) = " . (int)$month, NULL, FALSE);
        $this->db->where("AES_DECRYPT(type2, '$encryption_key') = '$type2'", NULL, FALSE);
        $this->db->where('YEAR(date) = YEAR(CURRENT_TIMESTAMP)', NULL, FALSE);

        if ($usertype != 1 && !empty($branchids)) {
            $this->db->where_in('branch', $branchids);
        }

        return $this->db->get()->row();
    }


    //    ======= its for  best_sales_products ===========
    public function best_sales_products()
    {
        $this->db->select('b.product_id, b.product_name, sum(a.quantity) as quantity');
        $this->db->from('invoice_details a');
        $this->db->join('product_information b', 'b.product_id = a.product_id');
        $this->db->group_by('b.product_id');
        $this->db->order_by('quantity', 'desc');
        $query = $this->db->get();
        return $query->result();
    }


    //Count todays_sales_report
    public function todays_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');
        $encryption_key = Config::$encryption_key;

        $usertype = $this->session->userdata('user_level2');

        $type2 = $usertype == 3 ? "B" : "A";
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


        $today = date('Y-m-d');
        $this->db->select("a.id as invoice_id1,a.*, AES_DECRYPT(b.customer_name, '" . $encryption_key . "') as customer_name, b.customer_id, AES_DECRYPT(a.sale_id, '" . $encryption_key . "') AS sale_id, AES_DECRYPT(a.grandTotal, '" . $encryption_key . "') AS grandTotal,c.name as pay");
        $this->db->from('sale a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->join('payment_type c', 'c.id = a.payment_type');

        $this->db->where('a.date', $today);
        $this->db->where("AES_DECRYPT(a.type2,'" . $encryption_key . "')", $type2);

        if ($this->session->userdata('user_level2') != 1) {
            $this->db->where_in('a.branch', $branchids);
        }

        $this->db->limit(50);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }
    //Count todays_sales_due_report
    public function todays_sales_due()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select('a.*,b.customer_name, b.customer_id, a.invoice_id,a.invoice');
        $this->db->from('invoice a');
        $this->db->join('customer_information b', 'b.customer_id = a.customer_id');
        $this->db->where('a.date', $today);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit(10);
        $this->db->order_by('a.invoice', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }
    //Count todays_purchase_due_report
    public function todays_purchase_due()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select('a.*,b.supplier_name, b.supplier_id, a.purchase_id,a.chalan_no');
        $this->db->from('product_purchase a');
        $this->db->join('supplier_information b', 'b.supplier_id = a.supplier_id');
        $this->db->where('a.purchase_date', $today);
        $this->db->where('a.due_amount >', 0);
        $this->db->limit(10);
        $this->db->order_by('a.id', 'desc');
        $query = $this->db->get()->result();
        return $query;
    }


    //Retrieve todays_total_sales_report
    public function todays_total_sales_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("a.date,a.invoice,b.invoice_id, sum(a.total_amount) as total_amt, sum(b.total_price) as total_sale,sum(`quantity`*`supplier_rate`) as total_supplier_rate,(SUM(total_price) - SUM(`quantity`*`supplier_rate`)) AS total_profit");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.invoice_id');
        $this->db->where('a.date', $today);
        $this->db->order_by('a.invoice_id', 'desc');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function todays_total_sales_amount()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("sum(total_amount) as total_amount");
        $this->db->from('invoice');
        $this->db->where('date', $today);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    // todays sales product
    public function todays_sale_product()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("c.product_name,c.price");
        $this->db->from('invoice a');
        $this->db->join('invoice_details b', 'b.invoice_id = a.invoice_id');
        $this->db->join('product_information c', 'c.product_id = b.product_id');
        $this->db->order_by('a.date', 'desc');
        $this->db->where('a.date', $today);
        $this->db->limit('3');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    //Retrieve todays_total_sales_report
    public function todays_total_purchase_report()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $this->db->select("sum(grand_total_amount) as ttl_purchase_amount");
        $this->db->from('product_purchase ');
        $this->db->where('purchase_date', $today);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

    public function best_saler_product_list()
    {
        $encryption_key = Config::$encryption_key;

        // $this->db->select("b.product_id, b.product_name, SUM(AES_DECRYPT(a.quantity,'" . $encryption_key . "')) AS quantity");
        // $this->db->from('sale_details a');
        // $this->db->join('sale s', 's.id = a.pid');
        // $this->db->join('product_information b', 'b.id = a.product');
        // $this->db->where('s.date >=', 'CURDATE() - INTERVAL 365 DAY', FALSE);
        // $this->db->group_by(['b.product_id', 'b.product_name']);
        // $this->db->order_by('quantity', 'DESC');
        // $query = $this->db->get();
        // return $query->result();


        $maxQuery = "
    SELECT MAX(product_total) AS max_quantity
    FROM (
        SELECT SUM(AES_DECRYPT(sd.quantity, '" . $encryption_key . "')) AS product_total
        FROM sale_details sd
        INNER JOIN sale s ON s.id = sd.pid
        WHERE s.date >= CURDATE() - INTERVAL 365 DAY
    ) AS sub
";

        $maxResult = $this->db->query($maxQuery)->row();
        $max_quantity = $maxResult->max_quantity ?? 1; // avoid divide-by-zero

        $this->db->select("
    b.product_id,
    b.product_name,
    ROUND(SUM(AES_DECRYPT(a.quantity,'" . $encryption_key . "')) / $max_quantity * 100, 2) AS quantity
", FALSE);

        $this->db->from('sale_details a');
        $this->db->join('sale s', 's.id = a.pid');
        $this->db->join('product_information b', 'b.id = a.product');
        $this->db->where('s.date >=', 'CURDATE() - INTERVAL 365 DAY', FALSE);
        $this->db->group_by(['b.product_id', 'b.product_name']);
        $this->db->order_by('quantity', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    public function out_of_stock()
    {

        // $this->db->select("a.unit,a.product_name,a.product_id,a.price,a.product_model,(select sum(quantity) from invoice_details where product_id= `a`.`product_id`) as 'totalSalesQnty',(select sum(quantity) from product_purchase_details where product_id= `a`.`product_id`) as 'totalBuyQnty'");
        // $this->db->from('product_information a');
        // $this->db->where(array('a.status' => 1));
        // $this->db->group_by('a.product_id');
        $encryption_key = Config::$encryption_key;

        $this->db->select(" 
        pi.id,
        pi.product_id,
        pi.product_name,
        pi.max_stock_level,
        pi.min_stock_level,
        pi.reorder_stock_level,
        pi.reserve_stock_level,
        pc.category_name,
        (
            SELECT IFNULL(SUM(AES_DECRYPT(sd.stock, '" . $encryption_key . "')), 0)
            FROM stock_details sd
            WHERE sd.product = pi.product_id OR sd.product = CAST(pi.id AS CHAR)
        ) AS master_stock_qty,cr.conversion_ratio,u1.unit_name as sub,u2.unit_name as master
    ", false);
        $this->db->from('product_information pi');
        $this->db->join('product_category pc', 'pc.category_id = pi.category_id', 'left');
        $this->db->join('subunit_product sp', 'sp.product_id = pi.id AND sp.first = 1', 'left');
        $this->db->join('units u1', 'u1.unit_id = sp.unit_id', 'left');
        $this->db->join('units u2', 'u2.unit_id = pi.unit', 'inner');
        $this->db->join('conversion_ratio cr', 'cr.product = pi.id AND u1.unit_id = cr.subunit', 'left');
        $this->db->where('pi.status', 1);
        $query = $this->db->get();
        $result = $query->result_array();

        $stock = [];
        foreach ($result as $stockproduct) {
            $master_stock_qty = (float)$stockproduct['master_stock_qty'];
            $reserve_level = (float)$stockproduct['reserve_stock_level'];
            $reorder_level = (float)$stockproduct['reorder_stock_level'];
            $min_level = (float)$stockproduct['min_stock_level'];
            $max_level = (float)$stockproduct['max_stock_level'];

            // Keep threshold order safe in case configuration was entered out of sequence.
            $reorder_level = max($reorder_level, $reserve_level);
            $min_level = max($min_level, $reorder_level);
            $max_level = max($max_level, $min_level);

            $status = 'Sufficient Stock';
            $status_class = 'btn-success';

            if ($master_stock_qty <= $reserve_level) {
                $status = 'Out of Stock';
                $status_class = 'btn-danger';
            } elseif ($master_stock_qty <= $reorder_level) {
                $status = 'Reorder';
                $status_class = 'btn-reorder';
            } elseif ($master_stock_qty <= $min_level) {
                $status = 'Near Order';
                $status_class = 'btn-near-order';
            } elseif ($master_stock_qty <= $max_level) {
                $status = 'Sufficient Stock';
                $status_class = 'btn-success';
            } else {
                $status = 'Overstock';
                $status_class = 'btn-info';
            }

            // Out of stock page should not show sufficient stock rows.
            if ($status === 'Sufficient Stock') {
                continue;
            }

            $stock[] = [
                'product_id' => $stockproduct['product_id'],
                'product_name' => $stockproduct['product_name'],
                'category_name' => $stockproduct['category_name'],
                'master_stock_qty' => $master_stock_qty,
                'status' => $status,
                'status_class' => $status_class,
                'conversion_ratio'=>$stockproduct['conversion_ratio'],
                'sub'=>$stockproduct['sub'],
                'master'=>$stockproduct['master']

            ];
        }

        return $stock;
    }

    /* Stock is tracked per `store` (stock_details.store), not per branch
       directly, so scoping uses sec_store (userid -> storeid) rather than
       sec_branch. Admins (user_level2 == 1) see every store, unscoped. */
    private function _store_scope_where($col_alias)
    {
        if ($this->session->userdata('user_level2') == 1) {
            return '';
        }
        $storeResult = $this->db->select('storeid')
            ->from('sec_store')
            ->where('userid', $this->session->userdata('id'))
            ->get()->result();
        $storeids = array_column($storeResult, 'storeid');
        if (empty($storeids)) {
            return " AND 1=0";
        }
        return " AND {$col_alias} IN (" . implode(',', $storeids) . ")";
    }

    public function out_of_stock_datatable($draw, $start, $length, $search, $order_col, $order_dir, $status_filter = '')
    {
        $encryption_key = Config::$encryption_key;
        $store_where    = $this->_store_scope_where('sd.store');

        $raw_sql = "
            SELECT
                pi.id,
                pi.product_id,
                pi.product_name,
                CAST(pi.max_stock_level     AS DECIMAL(18,4)) AS max_stock_level,
                CAST(pi.min_stock_level     AS DECIMAL(18,4)) AS min_stock_level,
                CAST(pi.reorder_stock_level AS DECIMAL(18,4)) AS reorder_stock_level,
                CAST(pi.reserve_stock_level AS DECIMAL(18,4)) AS reserve_stock_level,
                pc.category_name,
                IFNULL(SUM(AES_DECRYPT(sd.stock, '$encryption_key')), 0) AS master_stock_qty,
                cr.conversion_ratio,
                u1.unit_name AS sub,
                u2.unit_name AS master
            FROM stock_details sd
            INNER JOIN product_information pi  ON pi.id          = CAST(sd.product AS UNSIGNED)
            LEFT  JOIN product_category    pc  ON pc.category_id = pi.category_id
            LEFT  JOIN subunit_product     sp  ON sp.product_id  = pi.id AND sp.first = 1
            LEFT  JOIN units               u1  ON u1.unit_id     = sp.unit_id
            INNER JOIN units               u2  ON u2.unit_id     = pi.unit
            LEFT  JOIN conversion_ratio    cr  ON cr.product     = pi.id AND u1.unit_id = cr.subunit
            WHERE pi.status = 1 {$store_where}
            GROUP BY pi.id
        ";

        $with_status_sql = "
            SELECT *,
                CASE
                    WHEN master_stock_qty <= reserve_stock_level                                                          THEN 'Out of Stock'
                    WHEN master_stock_qty <= GREATEST(reorder_stock_level, reserve_stock_level)                          THEN 'Reorder'
                    WHEN master_stock_qty <= GREATEST(min_stock_level, reorder_stock_level, reserve_stock_level)         THEN 'Near Order'
                    WHEN master_stock_qty <= GREATEST(max_stock_level, min_stock_level, reorder_stock_level, reserve_stock_level) THEN 'Sufficient Stock'
                    ELSE 'Overstock'
                END AS status,
                CASE
                    WHEN master_stock_qty <= reserve_stock_level                                                          THEN 'btn-danger'
                    WHEN master_stock_qty <= GREATEST(reorder_stock_level, reserve_stock_level)                          THEN 'btn-reorder'
                    WHEN master_stock_qty <= GREATEST(min_stock_level, reorder_stock_level, reserve_stock_level)         THEN 'btn-near-order'
                    WHEN master_stock_qty <= GREATEST(max_stock_level, min_stock_level, reorder_stock_level, reserve_stock_level) THEN 'btn-success'
                    ELSE 'btn-info'
                END AS status_class
            FROM ($raw_sql) AS raw_data
        ";

        $filter_sql = "SELECT * FROM ($with_status_sql) AS s WHERE s.status != 'Sufficient Stock'";
        // Header dropdown rows (Out of Stock / Reorder / Near Order / Overstock) open this
        // same table pre-filtered to just that status, instead of showing everything mixed.
        $valid_statuses = ['Out of Stock', 'Reorder', 'Near Order', 'Overstock'];
        if (in_array($status_filter, $valid_statuses, true)) {
            $filter_sql .= " AND s.status = " . $this->db->escape($status_filter);
        }

        $search_sql = "";
        if (!empty($search)) {
            $s = $this->db->escape_like_str($search);
            $search_sql = " AND (s.product_name LIKE '%$s%' OR s.category_name LIKE '%$s%' OR s.product_id LIKE '%$s%' OR s.status LIKE '%$s%')";
        }

        $total    = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($filter_sql) AS c")->row_array()['cnt'];
        $filtered = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($filter_sql $search_sql) AS c")->row_array()['cnt'];

        $sortable = ['product_id', 'product_name', 'category_name', 'master_stock_qty', 'status'];
        $sort_col = isset($sortable[$order_col]) ? $sortable[$order_col] : 'product_name';
        $sort_dir = $order_dir === 'desc' ? 'DESC' : 'ASC';

        $start  = (int)$start;
        $length = (int)$length;
        $rows = $this->db->query(
            "SELECT * FROM ($filter_sql $search_sql) AS d ORDER BY $sort_col $sort_dir LIMIT $start, $length"
        )->result_array();

        $sl = $start + 1;
        foreach ($rows as &$row) {
            $row['sl'] = $sl++;
        }

        return ['draw' => (int)$draw, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows];
    }

    public function expiry_alert()
    {
        $encryption_key = Config::$encryption_key;

        $setting = $this->db->select('COALESCE(expiry_alert_days,0) AS expiry_alert_days', false)
            ->from('web_setting')
            ->order_by('setting_id', 'asc')
            ->limit(1)
            ->get()
            ->row();

        $expiry_alert_days = isset($setting->expiry_alert_days) ? (int)$setting->expiry_alert_days : 0;

        $this->db->select(" 
            sb.id AS batch_pk,
            sb.product AS product_id,
            CAST(AES_DECRYPT(sb.batchid, '{$encryption_key}') AS CHAR) AS batch_id,
            sb.edate AS expiry_date,
            IFNULL(pi.product_name, '') AS product_name,
            pi.unit AS master_unit,
            u.unit_name,
            (
            SELECT IFNULL(SUM(AES_DECRYPT(pd.stock, '" . $encryption_key . "')), 0)
                                FROM stock_details pd
                WHERE pd.product = sb.product
                  AND pd.batch = sb.id
            ) AS master_stock_qty,cr.conversion_ratio,u1.unit_name as sub,u2.unit_name as master
        ", false);
        $this->db->from('stockbatch sb');
        $this->db->join('product_information pi', '(pi.id = sb.product OR pi.product_id = sb.product)', 'left', false);
        $this->db->join('units u', 'u.unit_id = pi.unit', 'left');
        $this->db->join('subunit_product sp', 'sp.product_id = pi.id AND sp.first = 1', 'left');
        $this->db->join('units u1', 'u1.unit_id = sp.unit_id', 'left');
        $this->db->join('units u2', 'u2.unit_id = pi.unit', 'inner');
        $this->db->join('conversion_ratio cr', 'cr.product = pi.id AND u1.unit_id = cr.subunit', 'left');
        $this->db->where('sb.busage', 'single');
        $this->db->where('sb.status', 1);
        $this->db->where('sb.edate IS NOT NULL', null, false);
        $this->db->where('sb.edate !=', '0000-00-00');
        $this->db->order_by('sb.edate', 'asc');

        $rows = $this->db->get()->result_array();

        $today = date('Y-m-d');
        $result = [];
        $sl = 1;

        foreach ($rows as $row) {
            if (empty($row['expiry_date'])) {
                continue;
            }

            $expiry_date = date('Y-m-d', strtotime($row['expiry_date']));
            if (!$expiry_date) {
                continue;
            }

            $master_stock_qty = (float)$row['master_stock_qty'];

            $is_expired = (strtotime($today) >= strtotime($expiry_date));
            $alert_start = date('Y-m-d', strtotime($expiry_date . ' -' . $expiry_alert_days . ' day'));
            $is_to_be_expired = (!$is_expired && strtotime($today) >= strtotime($alert_start));

            if (!$is_expired && !$is_to_be_expired) {
                continue;
            }

            $status_text = $is_expired ? 'Expired' : 'To be Expired';
            $status_class = $is_expired ? 'label-danger' : 'label-warning';
            $unit_name = !empty($row['unit_name']) ? $row['unit_name'] : $row['master_unit'];

            $result[] = [
                'sl' => $sl++,
                'product_id' => $row['product_id'],
                'product_name' => !empty($row['product_name']) ? $row['product_name'] : '-',
                'batch_id' => $row['batch_id'],
                'expiry_date' => $expiry_date,
                'master_stock_qty' => number_format($master_stock_qty, 2, '.', ','),
                'unit_name' => $unit_name,
                'status_text' => $status_text,
                'status_class' => $status_class,
                'conversion_ratio'=>$row['conversion_ratio'],
                'sub'=>$row['sub'],
                'master'=>$row['master']
            ];
        }

        return $result;
    }

    public function expiry_alert_datatable($draw, $start, $length, $search, $order_col, $order_dir, $status_filter = '')
    {
        $encryption_key = Config::$encryption_key;

        $setting = $this->db->select('COALESCE(expiry_alert_days,0) AS expiry_alert_days', false)
            ->from('web_setting')
            ->order_by('setting_id', 'asc')
            ->limit(1)
            ->get()
            ->row();
        $expiry_alert_days = isset($setting->expiry_alert_days) ? (int)$setting->expiry_alert_days : 0;
        $store_where       = $this->_store_scope_where('sd.store');

        $raw_sql = "
            SELECT
                pi.product_id AS product_id,
                CAST(AES_DECRYPT(sb.batchid, '$encryption_key') AS CHAR) AS batch_id,
                DATE_FORMAT(sb.edate, '%Y-%m-%d') AS expiry_date,
                IFNULL(pi.product_name, '') AS product_name,
                IFNULL(SUM(AES_DECRYPT(sd.stock, '$encryption_key')), 0) AS master_stock_qty,
                cr.conversion_ratio,
                u1.unit_name AS sub,
                u2.unit_name AS master,
                CASE WHEN DATE(sb.edate) <= CURDATE() THEN 'Expired' ELSE 'To be Expired' END AS status_text,
                CASE WHEN DATE(sb.edate) <= CURDATE() THEN 'btn-danger' ELSE 'btn-warning'  END AS status_class
            FROM stockbatch sb
            LEFT  JOIN stock_details       sd ON sd.batch      = sb.id AND sd.product = CAST(sb.product AS CHAR)
            LEFT  JOIN product_information pi ON pi.id         = sb.product
            LEFT  JOIN subunit_product     sp ON sp.product_id = pi.id AND sp.first = 1
            LEFT  JOIN units               u1 ON u1.unit_id    = sp.unit_id
            INNER JOIN units               u2 ON u2.unit_id    = pi.unit
            LEFT  JOIN conversion_ratio    cr ON cr.product    = pi.id AND u1.unit_id = cr.subunit
            WHERE sb.busage = 'single'
              AND sb.status = 1
              AND sb.edate_enabled = 1
              AND sb.edate IS NOT NULL
              AND sb.edate != '0000-00-00'
              AND DATE(sb.edate) <= DATE_ADD(CURDATE(), INTERVAL $expiry_alert_days DAY)
              {$store_where}
            GROUP BY pi.id, sb.id
            HAVING master_stock_qty > 0
            ORDER BY pi.id, sb.id DESC
        ";

        // Header dropdown rows (Expired / To be Expired) open this same table
        // pre-filtered to just that status, instead of showing both mixed —
        // a structural filter, so (unlike the user's own search box below)
        // it applies to recordsTotal too, not just recordsFiltered.
        $status_sql = "";
        if (in_array($status_filter, ['Expired', 'To be Expired'], true)) {
            $status_sql = " AND s.status_text = " . $this->db->escape($status_filter);
        }

        $search_sql = "";
        if (!empty($search)) {
            $s = $this->db->escape_like_str($search);
            $search_sql = " AND (s.product_name LIKE '%$s%' OR s.product_id LIKE '%$s%' OR s.batch_id LIKE '%$s%' OR s.status_text LIKE '%$s%' OR s.expiry_date LIKE '%$s%')";
        }

        $total    = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($raw_sql) AS s WHERE 1=1 $status_sql")->row_array()['cnt'];
        $filtered = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($raw_sql) AS s WHERE 1=1 $status_sql $search_sql")->row_array()['cnt'];

        $sortable = ['product_id', 'product_name', 'batch_id', 'expiry_date', 'master_stock_qty', 'status_text'];
        $sort_col = isset($sortable[$order_col]) ? $sortable[$order_col] : 'expiry_date';
        $sort_dir = $order_dir === 'desc' ? 'DESC' : 'ASC';

        $start  = (int)$start;
        $length = (int)$length;
        $rows   = $this->db->query(
            "SELECT * FROM ($raw_sql) AS s WHERE 1=1 $status_sql $search_sql ORDER BY $sort_col $sort_dir LIMIT $start, $length"
        )->result_array();

        $sl = $start + 1;
        foreach ($rows as &$row) {
            $row['sl'] = $sl++;
        }

        return ['draw' => (int)$draw, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows];
    }

    public function notification()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->select("
        id,
    type,
    AES_DECRYPT(invoiceno, '" . $encryption_key . "') AS invoiceno,
    AES_DECRYPT(customername, '" . $encryption_key . "') AS customername,
    AES_DECRYPT(suppliername, '" . $encryption_key . "') AS suppliername,
    date,
    status,
    store,
    AES_DECRYPT(type2, '" . $encryption_key . "') AS type2
");
        $this->db->from('notification');
        $this->db->where('status', '0');
        $this->db->where('store', $this->session->userdata('email'));
        $this->db->order_by('id', 'DESC');

        $query = $this->db->get();
        $result = $query->result_array();

        return  $result;
    }


    public function profile_edit_data()
    {
        $user_id = $this->session->userdata('id');
        $this->db->select('a.*,b.username,a.logo');
        $this->db->from('users a');
        $this->db->join('user_login b', 'b.user_id = a.user_id');
        $this->db->where('a.user_id', $user_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    public function profile_update()
    {
        $logo = $this->fileupload->do_upload(
            './assets/img/user/',
            'logo'

        );

        $old_logo = $this->input->post('old_logo', true);
        $user_id = $this->session->userdata('id');
        $first_name = $this->input->post('first_name', true);
        $last_name = $this->input->post('last_name', true);
        $user_name = $this->input->post('user_name', true);
        $new_logo = (!empty($logo) ? $logo : $old_logo);

        return $this->db->query("UPDATE `users` AS `a`,`user_login` AS `b` SET `a`.`first_name` = '$first_name', `a`.`last_name` = '$last_name', `b`.`username` = '$user_name',`a`.`logo` = '$new_logo' WHERE `a`.`user_id` = '$user_id' AND `a`.`user_id` = `b`.`user_id`");
    }

    public function change_password($email, $old_password, $new_password)
    {
        $key = Config::$encryption_key;
        $user_name = md5("gef" . $new_password);
        $password = md5("gef" . $old_password);

        // Login (Auth_model) accepts the old password via EITHER the md5
        // `password` column OR the AES `encrypted_password` column — some
        // accounts only match on one of the two (encrypted_password was
        // added later and is the source of truth for those). Checking only
        // the md5 column here rejected a correct old password for any
        // account where the two had drifted out of sync.
        $this->db->select('id');
        $this->db->from('user_login');
        $this->db->where('username', $email);
        $this->db->where('status', 1);
        $this->db->group_start();
        $this->db->where('password', $password);
        $this->db->or_where(
            "CAST(AES_DECRYPT(encrypted_password,'$key') AS CHAR(255)) = " . $this->db->escape($old_password),
            NULL,
            FALSE
        );
        $this->db->group_end();
        $query = $this->db->get();
        $result = $query->result_array();

        if (count($result) == 1) {
            $this->db->where('username', $email);
            $this->db->update('user_login', array('password' => $user_name));

            // Keep encrypted_password in sync too, or it goes stale and
            // still decrypts to the OLD password after this change.
            $this->db->query(
                "UPDATE user_login SET encrypted_password = AES_ENCRYPT(?, '$key') WHERE username = ?",
                [$new_password, $email]
            );

            return true;
        }
        return false;
    }
}
