<?php
defined('BASEPATH') or exit('No direct script access allowed');

/* Customer/Supplier Credit Alert data — due amounts come from the
   customer_due/supplier_due ledger tables (the same source Report.php's
   customer_due_reportinvoicewise()/supplier_due_reportinvoicewise() already
   use), NOT a due_amount column on invoice/sale/purchase — none of those
   tables carry one; customer_due/supplier_due is the only place "due" is
   actually tracked (+sale/-purchase, -return, -receive/-payment postings).
   Due Date = transaction date + the customer's/supplier's own credit
   period (customer_information.due_date / supplier_information.due_date),
   since neither sale nor purchase stores a per-transaction credit period
   of its own (sale does via sale.due_date, purchase does not — using the
   master-table column for both keeps the two queries symmetric). */
class Credit_alert_model extends CI_Model
{
    public function web_setting()
    {
        return $this->db->select('*')->from('web_setting')->get()->row();
    }

    private function branch_scope_where($alias_date_table, $alias_due_table)
    {
        if ($this->session->userdata('user_level2') == 1) {
            return '';
        }

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        if (empty($branchids)) {
            return " AND 1=0";
        }
        return " AND IFNULL({$alias_date_table}.branch, {$alias_due_table}.branch) IN (" . implode(',', $branchids) . ")";
    }

    public function customer_credit_datatable($draw, $start, $length, $search, $order_col, $order_dir)
    {
        $encryption_key = Config::$encryption_key;
        $branch_where    = $this->branch_scope_where('s', 'cd');

        $sql = "
            SELECT * FROM (
                SELECT
                    cd.sale_id,
                    IFNULL(AES_DECRYPT(s.sale_id, '{$encryption_key}'), cd.sale_id) AS invoice_no,
                    IFNULL(s.date, '') AS invoice_date,
                    AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
                    DATE_ADD(IFNULL(s.date, CURDATE()), INTERVAL IFNULL(ci.due_date, 0) DAY) AS due_date,
                    SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                LEFT JOIN sale s ON s.id = cd.sale_id
                LEFT JOIN customer_information ci ON ci.customer_id = IFNULL(s.customer_id, cd.customer)
                WHERE cd.sale_id > 0 {$branch_where}
                GROUP BY cd.sale_id
            ) t
            WHERE t.due_amount > 0.0001
        ";

        return $this->_paginate($sql, $draw, $start, $length, $search,
            ['invoice_date', 'invoice_no', 'invoice_date', 'due_date', 'due_amount'],
            $order_col, $order_dir,
            ['customer_name', 'invoice_no']
        );
    }

    public function supplier_credit_datatable($draw, $start, $length, $search, $order_col, $order_dir)
    {
        $encryption_key = Config::$encryption_key;
        $branch_where    = $this->branch_scope_where('p', 'sd');

        $sql = "
            SELECT * FROM (
                SELECT
                    sd.purchase_id,
                    IFNULL(AES_DECRYPT(p.purchase_id, '{$encryption_key}'), sd.purchase_id) AS invoice_no,
                    IFNULL(p.date, '') AS invoice_date,
                    AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
                    DATE_ADD(IFNULL(p.date, CURDATE()), INTERVAL IFNULL(si.due_date, 0) DAY) AS due_date,
                    SUM(CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM supplier_due sd
                LEFT JOIN purchase p ON p.id = sd.purchase_id
                LEFT JOIN supplier_information si ON si.supplier_id = IFNULL(p.supplier_id, sd.supplier)
                WHERE sd.purchase_id > 0 {$branch_where}
                GROUP BY sd.purchase_id
            ) t
            WHERE t.due_amount > 0.0001
        ";

        return $this->_paginate($sql, $draw, $start, $length, $search,
            ['invoice_date', 'invoice_no', 'invoice_date', 'due_date', 'due_amount'],
            $order_col, $order_dir,
            ['supplier_name', 'invoice_no']
        );
    }

    private function _paginate($sql, $draw, $start, $length, $search, $sortable, $order_col, $order_dir, $search_cols)
    {
        $search_sql = '';
        if (!empty($search)) {
            $s = $this->db->escape_like_str($search);
            $like = [];
            foreach ($search_cols as $col) {
                $like[] = "t.$col LIKE '%$s%'";
            }
            $search_sql = ' AND (' . implode(' OR ', $like) . ')';
        }

        $total    = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($sql) AS c")->row_array()['cnt'];
        $filtered = (int)$this->db->query("SELECT COUNT(*) AS cnt FROM ($sql $search_sql) AS c")->row_array()['cnt'];

        $sort_col = isset($sortable[$order_col]) ? $sortable[$order_col] : 'due_date';
        $sort_dir = $order_dir === 'desc' ? 'DESC' : 'ASC';

        $start  = (int)$start;
        $length = (int)$length;
        $rows = $this->db->query(
            "SELECT * FROM ($sql $search_sql) AS d ORDER BY $sort_col $sort_dir LIMIT $start, $length"
        )->result_array();

        $sl = $start + 1;
        foreach ($rows as &$row) {
            $row['sl']     = $sl++;
            $row['status'] = 'Not Paid';
        }

        return ['draw' => (int)$draw, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows];
    }

    public function customer_credit_count()
    {
        return $this->customer_credit_summary()['count'];
    }

    /* Count + total due amount + distinct customers + oldest due date — the
       header dropdown (6.1) only needs count+total, but the pop-up's own
       4 summary cards (section "Shared design elements" / Figure 1 in the
       spec) need all four, so this one query covers both call sites. */
    public function customer_credit_summary()
    {
        $encryption_key = Config::$encryption_key;
        $branch_where    = $this->branch_scope_where('s', 'cd');
        $row = $this->db->query("
            SELECT COUNT(*) AS cnt, IFNULL(SUM(t.due_amount), 0) AS total,
                   COUNT(DISTINCT t.customer_id) AS parties, MIN(t.due_date) AS oldest_date
            FROM (
                SELECT cd.sale_id, IFNULL(s.customer_id, cd.customer) AS customer_id,
                       DATE_ADD(IFNULL(s.date, CURDATE()), INTERVAL IFNULL(ci.due_date, 0) DAY) AS due_date,
                       SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                LEFT JOIN sale s ON s.id = cd.sale_id
                LEFT JOIN customer_information ci ON ci.customer_id = IFNULL(s.customer_id, cd.customer)
                WHERE cd.sale_id > 0 {$branch_where}
                GROUP BY cd.sale_id
            ) t WHERE t.due_amount > 0.0001
        ")->row_array();
        return [
            'count' => (int)$row['cnt'], 'total' => (float)$row['total'],
            'parties' => (int)$row['parties'], 'oldest_date' => $row['oldest_date'],
        ];
    }

    public function supplier_credit_count()
    {
        return $this->supplier_credit_summary()['count'];
    }

    public function supplier_credit_summary()
    {
        $encryption_key = Config::$encryption_key;
        $branch_where    = $this->branch_scope_where('p', 'sd');
        $row = $this->db->query("
            SELECT COUNT(*) AS cnt, IFNULL(SUM(t.due_amount), 0) AS total,
                   COUNT(DISTINCT t.supplier_id) AS parties, MIN(t.due_date) AS oldest_date
            FROM (
                SELECT sd.purchase_id, IFNULL(p.supplier_id, sd.supplier) AS supplier_id,
                       DATE_ADD(IFNULL(p.date, CURDATE()), INTERVAL IFNULL(si.due_date, 0) DAY) AS due_date,
                       SUM(CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM supplier_due sd
                LEFT JOIN purchase p ON p.id = sd.purchase_id
                LEFT JOIN supplier_information si ON si.supplier_id = IFNULL(p.supplier_id, sd.supplier)
                WHERE sd.purchase_id > 0 {$branch_where}
                GROUP BY sd.purchase_id
            ) t WHERE t.due_amount > 0.0001
        ")->row_array();
        return [
            'count' => (int)$row['cnt'], 'total' => (float)$row['total'],
            'parties' => (int)$row['parties'], 'oldest_date' => $row['oldest_date'],
        ];
    }

    /* Thin wrappers around Template_model's own counting (that's where the
       header badge counts already live, see Template.php:70-72) so this
       endpoint and the per-page-load header badges never drift apart —
       both call through to the exact same methods. */
    public function expiry_alert_count()
    {
        $this->load->model('template/template_model');
        return $this->template_model->expiry_alert_count();
    }

    public function stock_alert_count()
    {
        $this->load->model('template/template_model');
        return $this->template_model->out_of_stock_count();
    }

    public function expiry_alert_breakdown()
    {
        $this->load->model('template/template_model');
        return $this->template_model->expiry_alert_breakdown();
    }

    public function stock_alert_breakdown()
    {
        $this->load->model('template/template_model');
        return $this->template_model->out_of_stock_breakdown();
    }

}
