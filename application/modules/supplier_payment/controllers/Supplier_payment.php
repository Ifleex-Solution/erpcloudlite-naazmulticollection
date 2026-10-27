<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("./vendor/Config.php");

// Mirrors Customer_receive.php exactly, but settles Purchase invoices
// (reduced by Purchase Returns) against a supplier instead of a customer.
class Supplier_payment extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }

    public function new_supplier_payment()
    {
        if (!$this->permission1->method('new_supplier_payment', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $encryption_key = Config::$encryption_key;
        $suppliers = $this->db->select("supplier_id, AES_DECRYPT(supplier_name, '{$encryption_key}') AS supplier_name")
            ->from('supplier_information')
            ->where('status', '1')
            ->get()->result_array();

        // "Credit" isn't a valid way to pay a supplier (it means no payment was
        // actually made), so it's excluded here too, same as Customer Receive.
        $all_pmethod = $this->db->select('*')->from('payment_type')->where('status', '1')->where('name !=', 'Credit')->get()->result_array();

        $data['title']       = 'New Supplier Payment';
        $data['module']      = 'supplier_payment';
        $data['page']        = 'new_supplier_payment';
        $data['suppliers']   = $suppliers;
        $data['all_pmethod'] = $all_pmethod;
        echo modules::run('template/layout', $data);
    }

    public function manage_supplier_payment()
    {
        if (!$this->permission1->method('manage_supplier_payment', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']  = 'Manage Supplier Payment';
        $data['module'] = 'supplier_payment';
        $data['page']   = 'manage_supplier_payment';
        echo modules::run('template/layout', $data);
    }

    private function _type2()
    {
        $user_level = $this->session->userdata('user_level2');
        return ($user_level == 3) ? 'B' : 'A';
    }

    // AJAX: summary totals for header fields
    public function getSupplierDueSummary()
    {
        $encryption_key = Config::$encryption_key;
        $supplier_id    = $this->input->post('supplier_id', TRUE);
        $branch_id      = $this->input->post('branch_id', TRUE);
        $type2          = $this->_type2();

        if (!$supplier_id) {
            echo json_encode(['voucher_count' => 0, 'total_branch_due' => '0.00', 'total_due' => '0.00']);
            return;
        }

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');
        $branchFilter = '';
        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchFilter = " AND branch IN (" . implode(',', $branchids) . ")";
        }

        $base_sql = "
            SELECT purchase_id, SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) AS net_due
            FROM supplier_due
            WHERE supplier = '{$supplier_id}' AND purchase_id > 0
              AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'{$branchFilter}
            GROUP BY purchase_id
            HAVING SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
        ";

        $count_row     = $this->db->query("SELECT COUNT(*) AS cnt FROM ({$base_sql}) t")->row();
        $voucher_count = $count_row ? (int)$count_row->cnt : 0;

        $total_row = $this->db->query("SELECT IFNULL(SUM(net_due), 0) AS total FROM ({$base_sql}) t")->row();
        $total_due = number_format((float)$total_row->total, 2, '.', '');

        $branch_due = '0.00';
        if ($branch_id) {
            $branch_sql = "
                SELECT purchase_id, SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) AS net_due
                FROM supplier_due
                WHERE supplier = '{$supplier_id}' AND branch = '{$branch_id}' AND purchase_id > 0
                  AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
                GROUP BY purchase_id
                HAVING SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
            ";
            $branch_row = $this->db->query("SELECT IFNULL(SUM(net_due), 0) AS total FROM ({$branch_sql}) t")->row();
            $branch_due = number_format((float)$branch_row->total, 2, '.', '');
        }

        echo json_encode([
            'voucher_count'    => $voucher_count,
            'total_branch_due' => $branch_due,
            'total_due'        => $total_due,
        ]);
    }

    // AJAX: DataTable data for manage supplier payment
    public function getManageSupplierPayment()
    {
        $encryption_key = Config::$encryption_key;
        $postData   = $this->input->post();
        $type2      = ($this->input->post('type2') === 'B') ? 'B' : 'A';
        $branchid   = (int)$this->input->post('branchid');
        $fdate      = $this->input->post('fdate', TRUE);
        $tdate      = $this->input->post('tdate', TRUE);

        $draw        = $postData['draw'];
        $start       = (int)$postData['start'];
        $rowperpage  = (int)$postData['length'];
        $searchValue = $postData['search']['value'];

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $baseWhere = "AES_DECRYPT(sp.type2, '{$encryption_key}') = '{$type2}'";
        if ($fdate)     $baseWhere .= " AND sp.date >= '{$fdate}'";
        if ($tdate)     $baseWhere .= " AND sp.date <= '{$tdate}'";
        if ($branchid > 0) {
            $baseWhere .= " AND sp.branch = {$branchid}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $baseWhere .= " AND sp.branch IN (" . implode(',', $branchids) . ")";
        }

        $searchWhere = '';
        if ($searchValue != '') {
            $s = $this->db->escape_like_str($searchValue);
            $searchWhere = " AND (AES_DECRYPT(sp.voucher_no, '{$encryption_key}') LIKE '%{$s}%'
                OR sp.date LIKE '%{$s}%'
                OR AES_DECRYPT(si.supplier_name, '{$encryption_key}') LIKE '%{$s}%'
                OR AES_DECRYPT(b.name, '{$encryption_key}') LIKE '%{$s}%')";
        }

        $from = "FROM supplier_payment sp
                 LEFT JOIN supplier_information si ON si.supplier_id = sp.supplier
                 LEFT JOIN branch b ON b.id = sp.branch
                 LEFT JOIN payment_type pt ON pt.id = sp.payment_type";

        $totalRow = $this->db->query("SELECT COUNT(*) AS cnt {$from} WHERE {$baseWhere}")->row();
        $totalRecords = $totalRow ? (int)$totalRow->cnt : 0;

        $filteredRow = $this->db->query("SELECT COUNT(*) AS cnt {$from} WHERE {$baseWhere}{$searchWhere}")->row();
        $totalFiltered = $filteredRow ? (int)$filteredRow->cnt : 0;

        $limit = $rowperpage >= 0 ? "LIMIT {$start}, {$rowperpage}" : '';
        $sql = "SELECT
                    sp.id,
                    AES_DECRYPT(sp.voucher_no, '{$encryption_key}') AS voucher_no,
                    sp.date,
                    AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
                    IFNULL(AES_DECRYPT(b.name, '{$encryption_key}'), '') AS branch_name,
                    IFNULL(pt.name, '') AS payment_type_name,
                    CAST(AES_DECRYPT(sp.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total_amount
                {$from}
                WHERE {$baseWhere}{$searchWhere}
                ORDER BY sp.id DESC
                {$limit}";

        $rows = $this->db->query($sql)->result_array();

        $can_update = $this->permission1->method('manage_supplier_payment', 'update')->access();
        $can_delete = $this->permission1->method('manage_supplier_payment', 'delete')->access();

        $data = [];
        foreach ($rows as $i => $r) {
            $button = '';
            $button .= '<button type="button" class="btn btn-warning btn-xs" onclick="reprintSupplierPayment(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Print"><i class="fa fa-print" aria-hidden="true"></i></button> ';
            if ($can_update) {
                $button .= '<button type="button" class="btn btn-info btn-xs" onclick="editSupplierPayment(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if ($can_delete) {
                $button .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteSupplierPayment(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button>';
            }
            $data[] = [
                'sl'            => $start + $i + 1,
                'voucher_no'    => $r['voucher_no'],
                'date'          => $r['date'],
                'supplier_name' => $r['supplier_name'],
                'branch_name'   => $r['branch_name'],
                'payment_type_name' => $r['payment_type_name'],
                'total_amount'  => $r['total_amount'],
                'button'        => $button
            ];
        }

        echo json_encode([
            'draw'            => (int)$draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    // AJAX: save supplier payment
    public function saveSupplierPayment()
    {
        $encryption_key = Config::$encryption_key;

        $date         = $this->input->post('date',       TRUE);
        $branch       = (int)$this->input->post('branch',    TRUE);
        $supplier     = (int)$this->input->post('supplier',  TRUE);
        $payment_type = (int)$this->input->post('payment_type', TRUE);
        $invoices     = $this->input->post('invoices',   TRUE);
        $type2_raw    = $this->input->post('type2',      TRUE);
        $type2        = ($type2_raw === 'B') ? 'B' : 'A';
        $edit_id      = (int)$this->input->post('edit_id', TRUE);

        if (!$date || !$branch || !$supplier || !$payment_type || !$invoices) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        if ($edit_id > 0) {
            if (!$this->permission1->method('manage_supplier_payment', 'update')->access()) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to edit.']);
                return;
            }
            $old_type2 = $this->_type2();
            $old = $this->db->query("
                SELECT id FROM supplier_payment
                WHERE id = {$edit_id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$old_type2}'
            ")->row();
            if (!$old) {
                echo json_encode(['success' => false, 'message' => 'The payment being edited no longer exists. It may have already been edited or deleted elsewhere.']);
                return;
            }
        }

        $rows = json_decode($invoices, true);
        if (!$rows || !is_array($rows) || count($rows) === 0) {
            echo json_encode(['success' => false, 'message' => 'No purchase rows provided.']);
            return;
        }

        $total_amount = 0;
        foreach ($rows as $row) {
            $settle = (float)($row['settle_amount'] ?? 0);
            if ($settle <= 0) {
                echo json_encode(['success' => false, 'message' => 'Settle amount must be greater than 0 for all rows.']);
                return;
            }
            $total_amount += $settle;
        }

        $this->db->trans_start();

        if ($edit_id > 0) {
            $sp_id = $edit_id;
            $this->db->query("
                UPDATE supplier_payment
                SET date = '{$date}',
                    branch = {$branch},
                    supplier = {$supplier},
                    payment_type = {$payment_type},
                    total_amount = AES_ENCRYPT('{$total_amount}', '{$encryption_key}'),
                    type2 = AES_ENCRYPT('{$type2}', '{$encryption_key}')
                WHERE id = {$sp_id}
            ");

            $this->db->where('supplier_payment_id', $sp_id)->delete('supplier_payment_details');
            $this->db->where('supplier_payment_id', $sp_id)->delete('supplier_due');
        } else {
            // Same type-scoped series convention as Customer Receive: Type A
            // (live) runs SP-0000000001 upward; Type B (testing) runs
            // SP-1000000001 upward.
            $series_floor = ($type2 === 'B') ? 1000000000 : 0;
            $max_row = $this->db->query("
                SELECT MAX(CAST(SUBSTRING_INDEX(AES_DECRYPT(voucher_no, '{$encryption_key}'), '-', -1) AS UNSIGNED)) AS max_no
                FROM supplier_payment
                WHERE AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
            ")->row();
            $max_no = $max_row ? $max_row->max_no : null;
            $next_no = ($max_no && $max_no > $series_floor) ? ($max_no + 1) : ($series_floor + 1);
            $voucher_no = 'SP-' . str_pad($next_no, 10, '0', STR_PAD_LEFT);

            $this->db->query("
                INSERT INTO supplier_payment (voucher_no, date, branch, supplier, payment_type, total_amount, type2, created_by, created_at)
                VALUES (
                    AES_ENCRYPT('{$voucher_no}', '{$encryption_key}'),
                    '{$date}',
                    {$branch},
                    {$supplier},
                    {$payment_type},
                    AES_ENCRYPT('{$total_amount}', '{$encryption_key}'),
                    AES_ENCRYPT('{$type2}', '{$encryption_key}'),
                    " . (int)$this->session->userdata('user_id') . ",
                    NOW()
                )
            ");
            $sp_id = $this->db->insert_id();
        }

        foreach ($rows as $row) {
            $purchase_id = (int)$row['purchase_id'];
            $settle      = (float)$row['settle_amount'];
            $neg         = -$settle;

            $this->db->query("
                INSERT INTO supplier_payment_details (supplier_payment_id, purchase_id, settle_amount)
                VALUES ({$sp_id}, {$purchase_id}, AES_ENCRYPT('{$settle}', '{$encryption_key}'))
            ");

            $this->db->query("
                INSERT INTO supplier_due (branch, supplier, purchase_id, purchase_return_id, supplier_payment_id, amount, type2)
                VALUES ({$branch}, {$supplier}, {$purchase_id}, 0, {$sp_id}, AES_ENCRYPT('{$neg}', '{$encryption_key}'), AES_ENCRYPT('{$type2}', '{$encryption_key}'))
            ");
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
            return;
        }

        $saved = $this->db->query("
            SELECT AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no
            FROM supplier_payment
            WHERE id = {$sp_id}
        ")->row();

        echo json_encode([
            'success'    => true,
            'message'    => 'Supplier Payment saved successfully.',
            'voucher_no' => $saved ? $saved->voucher_no : null,
            'sp_id'      => $sp_id,
            'details'    => $this->_build_supplier_payment_print($sp_id),
        ]);
    }

    private function _build_supplier_payment_print($sp_id)
    {
        $encryption_key = Config::$encryption_key;

        $header = $this->db->query("
            SELECT sp.id,
                   AES_DECRYPT(sp.voucher_no, '{$encryption_key}') AS voucher_no,
                   sp.date,
                   CAST(AES_DECRYPT(sp.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total_amount,
                   AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
                   AES_DECRYPT(si.address, '{$encryption_key}') AS supplier_address,
                   AES_DECRYPT(si.mobile, '{$encryption_key}') AS supplier_mobile,
                   IFNULL(AES_DECRYPT(b.name, '{$encryption_key}'), '') AS branch_name,
                   IFNULL(pt.name, '') AS payment_type_name
            FROM supplier_payment sp
            LEFT JOIN supplier_information si ON si.supplier_id = sp.supplier
            LEFT JOIN branch b ON b.id = sp.branch
            LEFT JOIN payment_type pt ON pt.id = sp.payment_type
            WHERE sp.id = {$sp_id}
        ")->row_array();

        if (empty($header)) {
            return '';
        }

        $items = $this->db->query("
            SELECT spd.purchase_id,
                   IFNULL(AES_DECRYPT(p.purchase_id, '{$encryption_key}'), spd.purchase_id) AS voucher_no,
                   CAST(AES_DECRYPT(spd.settle_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS settle_amount
            FROM supplier_payment_details spd
            LEFT JOIN purchase p ON p.id = spd.purchase_id
            WHERE spd.supplier_payment_id = {$sp_id}
            ORDER BY spd.id ASC
        ")->result_array();

        $this->load->model('service/service_model');
        $data = [
            'header'           => $header,
            'items'            => $items,
            'company_info'     => $this->service_model->company_info(),
            'currency_details' => $this->service_model->web_setting(),
        ];

        return $this->load->view('supplier_payment/supplier_payment_print', $data, true);
    }

    public function printSupplierPayment()
    {
        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }

        $encryption_key = Config::$encryption_key;
        $type2 = $this->_type2();
        $exists = $this->db->query("
            SELECT id, AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no FROM supplier_payment
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
        ")->row();
        if (!$exists) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        echo json_encode(['success' => true, 'details' => $this->_build_supplier_payment_print($id), 'voucher_no' => $exists->voucher_no]);
    }

    private function _voidSupplierPayment($id)
    {
        $this->db->trans_begin();
        try {
            $this->db->where('supplier_payment_id', $id)->delete('supplier_payment_details');
            $this->db->where('supplier_payment_id', $id)->delete('supplier_due');
            $this->db->where('id', $id)->delete('supplier_payment');

            if ($this->db->trans_status() === false) {
                throw new Exception('Void failed');
            }
            $this->db->trans_commit();
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function deleteSupplierPayment()
    {
        if (!$this->permission1->method('manage_supplier_payment', 'delete')->access()) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to delete.']);
            return;
        }

        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }

        $encryption_key = Config::$encryption_key;
        $del_type2 = $this->_type2();
        $exists = $this->db->query("
            SELECT id FROM supplier_payment
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$del_type2}'
        ")->row();
        if (!$exists) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        if ($this->_voidSupplierPayment($id)) {
            echo json_encode(['success' => true, 'message' => 'Supplier Payment deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed. Please try again.']);
        }
    }

    public function getSupplierPaymentForEdit()
    {
        if (!$this->permission1->method('manage_supplier_payment', 'update')->access()) {
            echo json_encode(['success' => false, 'message' => 'You do not have permission to edit.']);
            return;
        }

        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }

        $encryption_key = Config::$encryption_key;
        $edit_type2     = $this->_type2();

        $record = $this->db->query("
            SELECT date, branch, supplier, payment_type, AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no
            FROM supplier_payment
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$edit_type2}'
        ")->row();
        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        $detailRows = $this->db->query("
            SELECT purchase_id, AES_DECRYPT(settle_amount, '{$encryption_key}') AS settle_amount
            FROM supplier_payment_details
            WHERE supplier_payment_id = {$id}
        ")->result_array();

        echo json_encode([
            'success'      => true,
            'id'           => $id,
            'voucher_no'   => $record->voucher_no,
            'details'      => $detailRows,
            'date'         => $record->date,
            'branch'       => $record->branch,
            'supplier'     => $record->supplier,
            'payment_type' => $record->payment_type,
        ]);
    }

    // AJAX: purchase rows for the table (distinct purchase_ids with positive balance)
    public function getSupplierDuePurchases()
    {
        $encryption_key = Config::$encryption_key;
        $supplier_id    = $this->input->post('supplier_id', TRUE);
        $exclude_sp_id  = (int)$this->input->post('exclude_sp_id', TRUE);
        $type2          = $this->_type2();

        if (!$supplier_id) {
            echo json_encode([]);
            return;
        }

        $excludeFilter = $exclude_sp_id > 0 ? " AND IFNULL(sd.supplier_payment_id,0) != {$exclude_sp_id}" : '';

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');
        $branchFilter = '';
        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchFilter = " AND IFNULL(p.branch, sd.branch) IN (" . implode(',', $branchids) . ")";
        }

        $sql = "
            SELECT
                sd.purchase_id,
                IFNULL(p.branch, sd.branch)                                     AS purchase_branch,
                IFNULL(AES_DECRYPT(p.purchase_id, '{$encryption_key}'), sd.purchase_id) AS voucher_no,
                IFNULL(p.date, '')                                               AS purchase_date,
                SUM(CASE
                    WHEN IFNULL(sd.purchase_return_id,0) = 0 AND IFNULL(sd.supplier_payment_id,0) = 0
                    THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                    ELSE 0 END)                                                  AS purchase_amount,
                ABS(SUM(CASE
                    WHEN IFNULL(sd.purchase_return_id,0) > 0
                    THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                    ELSE 0 END))                                                 AS returned_amount,
                ABS(SUM(CASE
                    WHEN IFNULL(sd.supplier_payment_id,0) > 0
                    THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                    ELSE 0 END))                                                 AS paid_amount,
                SUM(CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
            FROM supplier_due sd
            LEFT JOIN purchase p ON p.id = sd.purchase_id
            WHERE sd.supplier = '{$supplier_id}' AND sd.purchase_id > 0
              AND AES_DECRYPT(sd.type2, '{$encryption_key}') = '{$type2}'{$branchFilter}{$excludeFilter}
            GROUP BY sd.purchase_id
            HAVING SUM(CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
            ORDER BY sd.purchase_id DESC
        ";

        $rows = $this->db->query($sql)->result_array();

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'purchase_id'     => $r['purchase_id'],
                'branch'          => $r['purchase_branch'],
                'voucher_no'      => $r['voucher_no'],
                'purchase_date'   => $r['purchase_date'],
                'purchase_amount' => number_format((float)$r['purchase_amount'], 2, '.', ''),
                'returned_amount' => number_format((float)$r['returned_amount'], 2, '.', ''),
                'paid_amount'     => number_format((float)$r['paid_amount'],     2, '.', ''),
                'due_amount'      => number_format((float)$r['due_amount'],      2, '.', ''),
            ];
        }
        echo json_encode($result);
    }
}
