<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Template_model extends CI_Model
{

    public function setting()
    {
        return $this->db->get('web_setting')->row();
    }

  public function bdtask_company_info()
{
    $encryption_key = Config::$encryption_key;
    
    $query = "SELECT 
        company_id,
        CAST(AES_DECRYPT(company_name, '{$encryption_key}') AS CHAR) AS company_name,
        CAST(AES_DECRYPT(email, '{$encryption_key}') AS CHAR) AS email,
        CAST(AES_DECRYPT(address, '{$encryption_key}') AS CHAR) AS address,
        CAST(AES_DECRYPT(mobile, '{$encryption_key}') AS CHAR) AS mobile,
        CAST(AES_DECRYPT(website, '{$encryption_key}') AS CHAR) AS website,
        CAST(AES_DECRYPT(vat_no, '{$encryption_key}') AS CHAR) AS vat_no,
        CAST(AES_DECRYPT(cr_no, '{$encryption_key}') AS CHAR) AS cr_no,
        instance_type,
        status
    FROM company_information
    WHERE status = 1";
    
    return $this->db->query($query)->result_array();
}

    public function bdtask_bank_list()
    {
        $this->db->select('*');
        $this->db->from('bank_add');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }


    /* Same sec_store-based scoping as Home_model's datatable version — stock
       is tracked per `store` (stock_details.store), not per branch, and
       admins (user_level2 == 1) see every store unscoped. Kept here too
       (not shared via a trait, matching this codebase's existing habit of
       duplicating small scoping helpers per model) so this count never
       drifts from Home_model::out_of_stock_datatable()'s own scoping. */
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

    public function out_of_stock_count()
    {
        $encryption_key = Config::$encryption_key;
        $store_where    = $this->_store_scope_where('sd.store');

        $raw_sql = "
            SELECT
                CAST(pi.max_stock_level     AS DECIMAL(18,4)) AS max_stock_level,
                CAST(pi.min_stock_level     AS DECIMAL(18,4)) AS min_stock_level,
                CAST(pi.reorder_stock_level AS DECIMAL(18,4)) AS reorder_stock_level,
                CAST(pi.reserve_stock_level AS DECIMAL(18,4)) AS reserve_stock_level,
                IFNULL(SUM(AES_DECRYPT(sd.stock, '$encryption_key')), 0) AS master_stock_qty
            FROM stock_details sd
            INNER JOIN product_information pi ON pi.id = CAST(sd.product AS UNSIGNED)
            WHERE pi.status = 1 {$store_where}
            GROUP BY pi.id
        ";

        $with_status_sql = "
            SELECT
                CASE
                    WHEN master_stock_qty <= reserve_stock_level                                                                     THEN 'Out of Stock'
                    WHEN master_stock_qty <= GREATEST(reorder_stock_level, reserve_stock_level)                                      THEN 'Reorder'
                    WHEN master_stock_qty <= GREATEST(min_stock_level, reorder_stock_level, reserve_stock_level)                     THEN 'Near Order'
                    WHEN master_stock_qty <= GREATEST(max_stock_level, min_stock_level, reorder_stock_level, reserve_stock_level)    THEN 'Sufficient Stock'
                    ELSE 'Overstock'
                END AS status
            FROM ($raw_sql) AS raw_data
        ";

        $result = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM ($with_status_sql) AS s WHERE s.status != 'Sufficient Stock'"
        )->row_array();

        return isset($result['cnt']) ? (int)$result['cnt'] : 0;
    }

    /* Per-status counts for the Stock Alert header dropdown (6.3 in the
       spec: Out of Stock / Reorder / Near Order / Overstock each shown
       with their own count) — same query shape as out_of_stock_count(),
       grouped instead of filtered to a single total. */
    public function out_of_stock_breakdown()
    {
        $encryption_key = Config::$encryption_key;
        $store_where    = $this->_store_scope_where('sd.store');

        $raw_sql = "
            SELECT
                CAST(pi.max_stock_level     AS DECIMAL(18,4)) AS max_stock_level,
                CAST(pi.min_stock_level     AS DECIMAL(18,4)) AS min_stock_level,
                CAST(pi.reorder_stock_level AS DECIMAL(18,4)) AS reorder_stock_level,
                CAST(pi.reserve_stock_level AS DECIMAL(18,4)) AS reserve_stock_level,
                IFNULL(SUM(AES_DECRYPT(sd.stock, '$encryption_key')), 0) AS master_stock_qty
            FROM stock_details sd
            INNER JOIN product_information pi ON pi.id = CAST(sd.product AS UNSIGNED)
            WHERE pi.status = 1 {$store_where}
            GROUP BY pi.id
        ";

        $with_status_sql = "
            SELECT
                CASE
                    WHEN master_stock_qty <= reserve_stock_level                                                                     THEN 'Out of Stock'
                    WHEN master_stock_qty <= GREATEST(reorder_stock_level, reserve_stock_level)                                      THEN 'Reorder'
                    WHEN master_stock_qty <= GREATEST(min_stock_level, reorder_stock_level, reserve_stock_level)                     THEN 'Near Order'
                    WHEN master_stock_qty <= GREATEST(max_stock_level, min_stock_level, reorder_stock_level, reserve_stock_level)    THEN 'Sufficient Stock'
                    ELSE 'Overstock'
                END AS status
            FROM ($raw_sql) AS raw_data
        ";

        $rows = $this->db->query(
            "SELECT status, COUNT(*) AS cnt FROM ($with_status_sql) AS s WHERE s.status != 'Sufficient Stock' GROUP BY status"
        )->result_array();

        $breakdown = ['Out of Stock' => 0, 'Reorder' => 0, 'Near Order' => 0, 'Overstock' => 0];
        foreach ($rows as $row) {
            $breakdown[$row['status']] = (int)$row['cnt'];
        }
        return $breakdown;
    }

    /* Shared row-fetch behind expiry_alert_count() and expiry_alert_breakdown()
       — same query/classification logic, just tallied differently. */
    private function _expiry_alert_classified_rows()
    {
        $encryption_key = Config::$encryption_key;

        $setting = $this->db->select('COALESCE(expiry_alert_days,0) AS expiry_alert_days', false)
            ->from('web_setting')
            ->order_by('setting_id', 'asc')
            ->limit(1)
            ->get()
            ->row();

        $expiry_alert_days = isset($setting->expiry_alert_days) ? (int)$setting->expiry_alert_days : 0;
        $store_where_sd    = $this->_store_scope_where('sd.store');
        $store_where_pd    = $this->_store_scope_where('pd.store');

        $this->db->select("
            sb.id,
            sb.edate,
            (
                SELECT IFNULL(SUM(CAST(AES_DECRYPT(sd.stock, '{$encryption_key}') AS DECIMAL(18,4))), 0)
                  FROM stock_details sd
                 WHERE sd.product = sb.product AND sd.batch = sb.id {$store_where_sd}
            ) + (
                SELECT IFNULL(SUM(CAST(AES_DECRYPT(pd.stock, '{$encryption_key}') AS DECIMAL(18,4))), 0)
                  FROM phystock_details pd
                 WHERE pd.product = sb.product AND pd.batch = sb.id {$store_where_pd}
            ) AS master_stock_qty
        ", false);
        $this->db->from('stockbatch sb');
        $this->db->where('sb.busage', 'single');
        $this->db->where('sb.status', 1);
        $this->db->where('sb.edate IS NOT NULL', null, false);
        $this->db->where('sb.edate !=', '0000-00-00');

        $rows = $this->db->get()->result_array();
        $today = date('Y-m-d');
        $classified = [];

        foreach ($rows as $row) {
            if (empty($row['edate'])) {
                continue;
            }

            $expiry_date = date('Y-m-d', strtotime($row['edate']));
            if (!$expiry_date) {
                continue;
            }

            $master_stock_qty = (float)$row['master_stock_qty'];
            if ($master_stock_qty <= 0) {
                continue;
            }

            $is_expired = (strtotime($today) >= strtotime($expiry_date));
            $alert_start = date('Y-m-d', strtotime($expiry_date . ' -' . $expiry_alert_days . ' day'));
            $is_to_be_expired = (!$is_expired && strtotime($today) >= strtotime($alert_start));

            if ($is_expired) {
                $classified[] = ['status' => 'Expired', 'expiry_date' => $expiry_date];
            } elseif ($is_to_be_expired) {
                $classified[] = ['status' => 'To be Expired', 'expiry_date' => $expiry_date];
            }
        }

        return $classified;
    }

    public function expiry_alert_count()
    {
        return count($this->_expiry_alert_classified_rows());
    }

    /* Expired / To be Expired counts, total, and the earliest upcoming
       expiry date — the header dropdown (6.2) only needs the two counts,
       but the pop-up's own 4 summary cards need all of this, so this one
       pass over the classified rows covers both call sites. */
    public function expiry_alert_breakdown()
    {
        $breakdown = ['Expired' => 0, 'To be Expired' => 0, 'total_batches' => 0, 'next_expiry_date' => null];
        foreach ($this->_expiry_alert_classified_rows() as $row) {
            $breakdown[$row['status']]++;
            $breakdown['total_batches']++;
            if ($breakdown['next_expiry_date'] === null || $row['expiry_date'] < $breakdown['next_expiry_date']) {
                $breakdown['next_expiry_date'] = $row['expiry_date'];
            }
        }
        return $breakdown;
    }
}