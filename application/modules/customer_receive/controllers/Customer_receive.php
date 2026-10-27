<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once("./vendor/Config.php");

class Customer_receive extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_receive_model');
        if (!$this->session->userdata('isLogIn'))
            redirect('login');
    }

    public function new_customer_receive()
    {
        if (!$this->permission1->method('new_customer_receive', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $encryption_key = Config::$encryption_key;
        $customers = $this->db->select("customer_id, AES_DECRYPT(customer_name, '{$encryption_key}') AS customer_name")
            ->from('customer_information')
            ->where('status', '1')
            ->get()->result_array();

        // "Credit" isn't a valid way to receive money (it means no payment was
        // actually collected), so it's excluded from this picker even though
        // it's a valid payment_type for the Sale screen.
        $all_pmethod = $this->db->select('*')->from('payment_type')->where('status', '1')->where('name !=', 'Credit')->get()->result_array();

        $data['title']       = 'New Customer Receive';
        $data['module']      = 'customer_receive';
        $data['page']        = 'new_customer_receive';
        $data['customers']   = $customers;
        $data['all_pmethod'] = $all_pmethod;
        echo modules::run('template/layout', $data);
    }

    public function manage_customer_receive()
    {
        if (!$this->permission1->method('manage_customer_receive', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']  = 'Manage Customer Receive';
        $data['module'] = 'customer_receive';
        $data['page']   = 'manage_customer_receive';
        echo modules::run('template/layout', $data);
    }

    private function _type2()
    {
        $user_level = $this->session->userdata('user_level2');
        return ($user_level == 3) ? 'B' : 'A';
    }

    // AJAX: summary totals for header fields
    public function getCustomerDueSummary()
    {
        $encryption_key = Config::$encryption_key;
        $customer_id    = $this->input->post('customer_id', TRUE);
        $branch_id      = $this->input->post('branch_id', TRUE);
        $source         = ($this->input->post('source', TRUE) === 'service') ? 'service' : 'product';
        $refCol         = ($source === 'service') ? 'service_id' : 'sale_id';
        $type2          = $this->_type2();

        if (!$customer_id) {
            echo json_encode(['voucher_count' => 0, 'total_branch_due' => '0.00', 'total_due' => '0.00']);
            return;
        }

        // Branch IDs accessible to this user (same scoping as Manage Sale / Manage Customer Receive)
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
            SELECT {$refCol}, SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) AS net_due
            FROM customer_due
            WHERE customer = '{$customer_id}' AND {$refCol} > 0
              AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'{$branchFilter}
            GROUP BY {$refCol}
            HAVING SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
        ";

        $count_row     = $this->db->query("SELECT COUNT(*) AS cnt FROM ({$base_sql}) t")->row();
        $voucher_count = $count_row ? (int)$count_row->cnt : 0;

        $total_row = $this->db->query("SELECT IFNULL(SUM(net_due), 0) AS total FROM ({$base_sql}) t")->row();
        $total_due = number_format((float)$total_row->total, 2, '.', '');

        $branch_due = '0.00';
        if ($branch_id) {
            $branch_sql = "
                SELECT {$refCol}, SUM(CAST(AES_DECRYPT(amount, '{$encryption_key}') AS DECIMAL(15,4))) AS net_due
                FROM customer_due
                WHERE customer = '{$customer_id}' AND branch = '{$branch_id}' AND {$refCol} > 0
                  AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
                GROUP BY {$refCol}
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

    // AJAX: DataTable data for manage customer receive
    public function getManageCustomerReceive()
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

        // Branch IDs accessible to this user
        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $baseWhere = "AES_DECRYPT(cr.type2, '{$encryption_key}') = '{$type2}'";
        if ($fdate)     $baseWhere .= " AND cr.date >= '{$fdate}'";
        if ($tdate)     $baseWhere .= " AND cr.date <= '{$tdate}'";
        if ($branchid > 0) {
            $baseWhere .= " AND cr.branch = {$branchid}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $baseWhere .= " AND cr.branch IN (" . implode(',', $branchids) . ")";
        }

        $searchWhere = '';
        if ($searchValue != '') {
            $s = $this->db->escape_like_str($searchValue);
            $searchWhere = " AND (AES_DECRYPT(cr.voucher_no, '{$encryption_key}') LIKE '%{$s}%'
                OR cr.date LIKE '%{$s}%'
                OR AES_DECRYPT(ci.customer_name, '{$encryption_key}') LIKE '%{$s}%'
                OR AES_DECRYPT(b.name, '{$encryption_key}') LIKE '%{$s}%')";
        }

        $from = "FROM customer_receive cr
                 LEFT JOIN customer_information ci ON ci.customer_id = cr.customer
                 LEFT JOIN branch b ON b.id = cr.branch
                 LEFT JOIN payment_type pt ON pt.id = cr.payment_type";

        // Total count
        $totalRow = $this->db->query("SELECT COUNT(*) AS cnt {$from} WHERE {$baseWhere}")->row();
        $totalRecords = $totalRow ? (int)$totalRow->cnt : 0;

        // Filtered count
        $filteredRow = $this->db->query("SELECT COUNT(*) AS cnt {$from} WHERE {$baseWhere}{$searchWhere}")->row();
        $totalFiltered = $filteredRow ? (int)$filteredRow->cnt : 0;

        // Data rows
        $limit = $rowperpage >= 0 ? "LIMIT {$start}, {$rowperpage}" : '';
        $sql = "SELECT
                    cr.id,
                    AES_DECRYPT(cr.voucher_no, '{$encryption_key}') AS voucher_no,
                    cr.date,
                    AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
                    IFNULL(AES_DECRYPT(b.name, '{$encryption_key}'), '') AS branch_name,
                    IFNULL(pt.name, '') AS payment_type_name,
                    CAST(AES_DECRYPT(cr.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total_amount
                {$from}
                WHERE {$baseWhere}{$searchWhere}
                ORDER BY cr.id DESC
                {$limit}";

        $rows = $this->db->query($sql)->result_array();

        $can_update = $this->permission1->method('manage_customer_receive', 'update')->access();
        $can_delete = $this->permission1->method('manage_customer_receive', 'delete')->access();

        $data = [];
        foreach ($rows as $i => $r) {
            $button = '';
            $button .= '<button type="button" class="btn btn-warning btn-xs" onclick="reprintCustomerReceive(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Print"><i class="fa fa-print" aria-hidden="true"></i></button> ';
            if ($can_update) {
                $button .= '<button type="button" class="btn btn-info btn-xs" onclick="editCustomerReceive(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></button> ';
            }
            if ($can_delete) {
                $button .= '<button type="button" class="btn btn-danger btn-xs" onclick="deleteCustomerReceive(' . (int)$r['id'] . ')" data-toggle="tooltip" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button>';
            }
            $data[] = [
                'sl'            => $start + $i + 1,
                'voucher_no'    => $r['voucher_no'],
                'date'          => $r['date'],
                'customer_name' => $r['customer_name'],
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

    // AJAX: save customer receive
    public function saveCustomerReceive()
    {
        $encryption_key = Config::$encryption_key;

        $date         = $this->input->post('date',       TRUE);
        $branch       = (int)$this->input->post('branch',    TRUE);
        $customer     = (int)$this->input->post('customer',  TRUE);
        $payment_type = (int)$this->input->post('payment_type', TRUE);
        $invoices     = $this->input->post('invoices',   TRUE);
        $type2_raw    = $this->input->post('type2',      TRUE);
        $type2        = ($type2_raw === 'B') ? 'B' : 'A';
        $edit_id      = (int)$this->input->post('edit_id', TRUE);
        // A receive settles either Sale of Product or Sale of Service
        // invoices, never both in one voucher.
        $source       = ($this->input->post('source', TRUE) === 'service') ? 'service' : 'product';

        if (!$date || !$branch || !$customer || !$payment_type || !$invoices) {
            echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
            return;
        }

        // Editing an existing receive: it must still exist (wasn't already
        // deleted/edited elsewhere in the meantime) before we update it.
        if ($edit_id > 0) {
            if (!$this->permission1->method('manage_customer_receive', 'update')->access()) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to edit.']);
                return;
            }
            $old_type2 = $this->_type2();
            $old = $this->db->query("
                SELECT id FROM customer_receive
                WHERE id = {$edit_id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$old_type2}'
            ")->row();
            if (!$old) {
                echo json_encode(['success' => false, 'message' => 'The receive being edited no longer exists. It may have already been edited or deleted elsewhere.']);
                return;
            }
        }

        $rows = json_decode($invoices, true);
        if (!$rows || !is_array($rows) || count($rows) === 0) {
            echo json_encode(['success' => false, 'message' => 'No invoice rows provided.']);
            return;
        }

        // Validate each row
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
            // Update the existing header in place — same id, same voucher_no;
            // only the editable fields change. created_by/created_at untouched.
            $cr_id = $edit_id;
            $this->db->query("
                UPDATE customer_receive
                SET date = '{$date}',
                    branch = {$branch},
                    customer = {$customer},
                    payment_type = {$payment_type},
                    total_amount = AES_ENCRYPT('{$total_amount}', '{$encryption_key}'),
                    type2 = AES_ENCRYPT('{$type2}', '{$encryption_key}')
                WHERE id = {$cr_id}
            ");

            // Settled invoices/amounts can change on every edit, so the detail
            // and due-offset rows are recreated from scratch against the same cr_id.
            $this->db->where('customer_receive_id', $cr_id)->delete('customer_receive_details');
            $this->db->where('customer_receive_id', $cr_id)->delete('customer_due');
        } else {
            // Separate 10-digit series per type: Type A (live) runs 0000000001
            // upward; Type B (testing) runs 1000000001 upward, so the two never
            // overlap. Next number = MAX(existing number in that type's range) + 1,
            // read from the numeric part after the last '-' (robust to any past
            // prefix format), falling back to the series floor when that type
            // has no rows yet.
            $series_floor = ($type2 === 'B') ? 1000000000 : 0;
            $max_row = $this->db->query("
                SELECT MAX(CAST(SUBSTRING_INDEX(AES_DECRYPT(voucher_no, '{$encryption_key}'), '-', -1) AS UNSIGNED)) AS max_no
                FROM customer_receive
                WHERE AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
            ")->row();
            $max_no = $max_row ? $max_row->max_no : null;
            $next_no = ($max_no && $max_no > $series_floor) ? ($max_no + 1) : ($series_floor + 1);
            $voucher_no = 'CR-' . str_pad($next_no, 10, '0', STR_PAD_LEFT);

            $this->db->query("
                INSERT INTO customer_receive (voucher_no, date, branch, customer, payment_type, total_amount, type2, created_by, created_at)
                VALUES (
                    AES_ENCRYPT('{$voucher_no}', '{$encryption_key}'),
                    '{$date}',
                    {$branch},
                    {$customer},
                    {$payment_type},
                    AES_ENCRYPT('{$total_amount}', '{$encryption_key}'),
                    AES_ENCRYPT('{$type2}', '{$encryption_key}'),
                    " . (int)$this->session->userdata('user_id') . ",
                    NOW()
                )
            ");
            $cr_id = $this->db->insert_id();
        }

        // Insert details + customer_due for each invoice. The "sale_id" key
        // in each row is really just "the settled record's id" — it means a
        // sale.id or a service.id depending on $source (see getCustomerDueInvoices).
        foreach ($rows as $row) {
            $ref_id  = (int)$row['sale_id'];
            $sale_id    = ($source === 'service') ? 0 : $ref_id;
            $service_id = ($source === 'service') ? $ref_id : 0;
            $settle  = (float)$row['settle_amount'];
            $neg     = -$settle;

            // detail row
            $this->db->query("
                INSERT INTO customer_receive_details (customer_receive_id, sale_id, service_id, settle_amount)
                VALUES ({$cr_id}, {$sale_id}, {$service_id}, AES_ENCRYPT('{$settle}', '{$encryption_key}'))
            ");

            // customer_due: negative amount to reduce due, customer_receive_id = cr_id
            $this->db->query("
                INSERT INTO customer_due (branch, customer, sale_id, service_id, sales_return_id, customer_receive_id, amount, type2)
                VALUES ({$branch}, {$customer}, {$sale_id}, {$service_id}, 0, {$cr_id}, AES_ENCRYPT('{$neg}', '{$encryption_key}'), AES_ENCRYPT('{$type2}', '{$encryption_key}'))
            ");
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
            return;
        }

        $saved = $this->db->query("
            SELECT AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no
            FROM customer_receive
            WHERE id = {$cr_id}
        ")->row();

        echo json_encode([
            'success'    => true,
            'message'    => 'Customer Receive saved successfully.',
            'voucher_no' => $saved ? $saved->voucher_no : null,
            'cr_id'      => $cr_id,
            'details'    => $this->_build_customer_receive_print($cr_id),
        ]);
    }

    // Builds the printable receipt HTML for a customer receive voucher —
    // same role as Invoice's _build_sales_return_print(), just for a
    // settlement voucher (invoice + settled amount rows) instead of product lines.
    private function _build_customer_receive_print($cr_id)
    {
        $encryption_key = Config::$encryption_key;

        $header = $this->db->query("
            SELECT cr.id,
                   AES_DECRYPT(cr.voucher_no, '{$encryption_key}') AS voucher_no,
                   cr.date,
                   CAST(AES_DECRYPT(cr.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total_amount,
                   AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
                   AES_DECRYPT(ci.customer_address, '{$encryption_key}') AS customer_address,
                   AES_DECRYPT(ci.customer_mobile, '{$encryption_key}') AS customer_mobile,
                   IFNULL(AES_DECRYPT(b.name, '{$encryption_key}'), '') AS branch_name,
                   IFNULL(pt.name, '') AS payment_type_name
            FROM customer_receive cr
            LEFT JOIN customer_information ci ON ci.customer_id = cr.customer
            LEFT JOIN branch b ON b.id = cr.branch
            LEFT JOIN payment_type pt ON pt.id = cr.payment_type
            WHERE cr.id = {$cr_id}
        ")->row_array();

        if (empty($header)) {
            return '';
        }

        // A receive settles either sale_id (product) or service_id (service)
        // rows — never both — so only one of these two joins ever matches.
        $items = $this->db->query("
            SELECT crd.sale_id, crd.service_id,
                   IFNULL(AES_DECRYPT(s.sale_id, '{$encryption_key}'), IFNULL(AES_DECRYPT(sv.service_id, '{$encryption_key}'), IF(crd.service_id > 0, crd.service_id, crd.sale_id))) AS voucher_no,
                   CAST(AES_DECRYPT(crd.settle_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS settle_amount
            FROM customer_receive_details crd
            LEFT JOIN sale s ON s.id = crd.sale_id AND crd.sale_id > 0
            LEFT JOIN service sv ON sv.id = crd.service_id AND crd.service_id > 0
            WHERE crd.customer_receive_id = {$cr_id}
            ORDER BY crd.id ASC
        ")->result_array();

        $this->load->model('service/service_model');
        $data = [
            'header'           => $header,
            'items'            => $items,
            'company_info'     => $this->service_model->company_info(),
            'currency_details' => $this->service_model->web_setting(),
        ];

        return $this->load->view('customer_receive/customer_receive_print', $data, true);
    }

    // AJAX: reprint an existing customer receive voucher from the manage list
    public function printCustomerReceive()
    {
        $id = (int)$this->input->post('id', TRUE);
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid record.']);
            return;
        }

        $encryption_key = Config::$encryption_key;
        $type2 = $this->_type2();
        $exists = $this->db->query("
            SELECT id, AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no FROM customer_receive
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$type2}'
        ")->row();
        if (!$exists) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        echo json_encode(['success' => true, 'details' => $this->_build_customer_receive_print($id), 'voucher_no' => $exists->voucher_no]);
    }

    // Reverses a customer receive: removes its detail rows and its customer_due
    // offset entries (restoring the original due), then removes the header row.
    // Caller is responsible for the permission check.
    private function _voidCustomerReceive($id)
    {
        $this->db->trans_begin();
        try {
            $this->db->where('customer_receive_id', $id)->delete('customer_receive_details');
            $this->db->where('customer_receive_id', $id)->delete('customer_due');
            $this->db->where('id', $id)->delete('customer_receive');

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

    // AJAX: delete a customer receive and reverse its due adjustments
    public function deleteCustomerReceive()
    {
        if (!$this->permission1->method('manage_customer_receive', 'delete')->access()) {
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
            SELECT id FROM customer_receive
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$del_type2}'
        ")->row();
        if (!$exists) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        if ($this->_voidCustomerReceive($id)) {
            echo json_encode(['success' => true, 'message' => 'Customer Receive deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed. Please try again.']);
        }
    }

    // AJAX: "edit" lookup — read-only. Hands back the receive's id/voucher_no/
    // date/branch/customer plus its settled invoice rows (sale_id + settle_amount)
    // so the New Customer Receive form can reopen pre-filled with the same
    // invoices/amounts. Nothing is deleted here: the old record is only voided
    // once the replacement actually saves (see saveCustomerReceive), so an
    // abandoned edit never leaves the user with neither the old nor a new record.
    public function getCustomerReceiveForEdit()
    {
        if (!$this->permission1->method('manage_customer_receive', 'update')->access()) {
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
            SELECT date, branch, customer, payment_type, AES_DECRYPT(voucher_no, '{$encryption_key}') AS voucher_no
            FROM customer_receive
            WHERE id = {$id} AND AES_DECRYPT(type2, '{$encryption_key}') = '{$edit_type2}'
        ")->row();
        if (!$record) {
            echo json_encode(['success' => false, 'message' => 'Record not found.']);
            return;
        }

        $detailRows = $this->db->query("
            SELECT sale_id, service_id, AES_DECRYPT(settle_amount, '{$encryption_key}') AS settle_amount
            FROM customer_receive_details
            WHERE customer_receive_id = {$id}
        ")->result_array();

        // A receive settles one source only — detect which from its own rows
        // (service_id > 0 on any row means this was a Sale of Service receive)
        // and normalize the wire field to "sale_id" either way, same as
        // getCustomerDueInvoices, so the frontend doesn't need to branch on it.
        $source = 'product';
        foreach ($detailRows as $k => $d) {
            if ((int)$d['service_id'] > 0) {
                $source = 'service';
                $detailRows[$k]['sale_id'] = $d['service_id'];
            }
            unset($detailRows[$k]['service_id']);
        }

        echo json_encode([
            'success'    => true,
            'id'         => $id,
            'voucher_no' => $record->voucher_no,
            'details'    => $detailRows,
            'date'       => $record->date,
            'branch'     => $record->branch,
            'customer'   => $record->customer,
            'payment_type' => $record->payment_type,
            'source'     => $source,
        ]);
    }

    // AJAX: invoice rows for the table (distinct sale_ids/service_ids with
    // positive balance). `source` selects which ledger column to read —
    // 'product' (Sale of Product, the default) or 'service' (Sale of Service).
    // A receive only ever settles one source at a time.
    public function getCustomerDueInvoices()
    {
        $encryption_key = Config::$encryption_key;
        $customer_id    = $this->input->post('customer_id', TRUE);
        $exclude_cr_id  = (int)$this->input->post('exclude_cr_id', TRUE);
        $source         = ($this->input->post('source', TRUE) === 'service') ? 'service' : 'product';
        $type2          = $this->_type2();

        if (!$customer_id) {
            echo json_encode([]);
            return;
        }

        // When re-opening an existing receive for edit, exclude its own due
        // adjustments so the figures shown are "as if" that old receive didn't
        // exist yet — it's only voided once the replacement actually saves.
        $excludeFilter = $exclude_cr_id > 0 ? " AND IFNULL(cd.customer_receive_id,0) != {$exclude_cr_id}" : '';

        // Deliberately not filtered to one branch here — this feeds Total Due,
        // which must reflect the customer's full due across all branches.
        // (The branch-specific invoice picker is filtered client-side instead.)
        // Branch IDs accessible to this user (same scoping as Manage Sale / Manage Customer Receive)
        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');
        $branchFilter = '';
        if ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchFilter = " AND IFNULL(s.branch, cd.branch) IN (" . implode(',', $branchids) . ")";
        }

        if ($source === 'service') {
            // Sale of Service: no sales_return equivalent (services aren't
            // "returned"), and the `service` table has no due_date column, so
            // returned_amount is always 0 and due_date is always blank.
            $sql = "
                SELECT
                    cd.service_id AS ref_id,
                    IFNULL(s.branch, cd.branch)                                           AS sale_branch,
                    IFNULL(AES_DECRYPT(s.service_id, '{$encryption_key}'), cd.service_id)  AS voucher_no,
                    IFNULL(s.date, '')                                                      AS invoice_date,
                    ''                                                                       AS due_date_calc,
                    SUM(CASE
                        WHEN IFNULL(cd.customer_receive_id,0) = 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)                                                          AS invoice_amount,
                    0                                                                        AS returned_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(cd.customer_receive_id,0) > 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END))                                                         AS paid_amount,
                    SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                LEFT JOIN service s ON s.id = cd.service_id
                WHERE cd.customer = '{$customer_id}' AND cd.service_id > 0
                  AND AES_DECRYPT(cd.type2, '{$encryption_key}') = '{$type2}'{$branchFilter}{$excludeFilter}
                GROUP BY cd.service_id
                HAVING SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
                ORDER BY cd.service_id DESC
            ";
        } else {
            $sql = "
                SELECT
                    cd.sale_id AS ref_id,
                    IFNULL(s.branch, cd.branch)                                      AS sale_branch,
                    IFNULL(AES_DECRYPT(s.sale_id, '{$encryption_key}'), cd.sale_id)  AS voucher_no,
                    IFNULL(s.date, '')                                                AS invoice_date,
                    IFNULL(DATE_FORMAT(DATE_ADD(s.date, INTERVAL IFNULL(s.due_date,0) DAY),'%Y-%m-%d'),'') AS due_date_calc,
                    SUM(CASE
                        WHEN IFNULL(cd.sales_return_id,0) = 0 AND IFNULL(cd.customer_receive_id,0) = 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)                                                   AS invoice_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(cd.sales_return_id,0) > 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END))                                                  AS returned_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(cd.customer_receive_id,0) > 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END))                                                  AS paid_amount,
                    SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                LEFT JOIN sale s ON s.id = cd.sale_id
                WHERE cd.customer = '{$customer_id}' AND cd.sale_id > 0
                  AND AES_DECRYPT(cd.type2, '{$encryption_key}') = '{$type2}'{$branchFilter}{$excludeFilter}
                GROUP BY cd.sale_id
                HAVING SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) > 0
                ORDER BY cd.sale_id DESC
            ";
        }

        $rows = $this->db->query($sql)->result_array();

        $result = [];
        foreach ($rows as $r) {
            // Field is still named "sale_id" on the wire (not "ref_id") so the
            // existing frontend row-building/settle logic works unchanged
            // regardless of source — it's just treated as an opaque id there.
            $result[] = [
                'sale_id'         => $r['ref_id'],
                'branch'          => $r['sale_branch'],
                'voucher_no'      => $r['voucher_no'],
                'invoice_date'    => $r['invoice_date'],
                'due_date'        => $r['due_date_calc'],
                'invoice_amount'  => number_format((float)$r['invoice_amount'],  2, '.', ''),
                'returned_amount' => number_format((float)$r['returned_amount'], 2, '.', ''),
                'paid_amount'     => number_format((float)$r['paid_amount'],     2, '.', ''),
                'due_amount'      => number_format((float)$r['due_amount'],      2, '.', ''),
            ];
        }
        echo json_encode($result);
    }
}
