<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once("./vendor/Config.php");

class Credit_alert extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('credit_alert_model');
    }

    public function customer_credit()
    {
        $data['title'] = 'Credit Alert Notification';
        if (!$this->permission1->method('credit_alert', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['module'] = "dashboard";
        $data['page']   = "home/credit_alert_customer";
        echo Modules::run('template/layout', $data);
    }

    public function customer_credit_data()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order_col = isset($this->input->post('order')[0]['column']) ? (int)$this->input->post('order')[0]['column'] : 4;
        $order_dir = isset($this->input->post('order')[0]['dir'])    ? $this->input->post('order')[0]['dir']        : 'asc';

        $result = $this->credit_alert_model->customer_credit_datatable($draw, $start, $length, $search, $order_col, $order_dir);
        echo json_encode($result);
    }

    public function supplier_credit()
    {
        $data['title'] = 'Credit Alert Notification';
        if (!$this->permission1->method('credit_alert', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['module'] = "dashboard";
        $data['page']   = "home/credit_alert_supplier";
        echo Modules::run('template/layout', $data);
    }

    public function supplier_credit_data()
    {
        $draw      = $this->input->post('draw');
        $start     = $this->input->post('start');
        $length    = $this->input->post('length');
        $search    = $this->input->post('search')['value'] ?? '';
        $order_col = isset($this->input->post('order')[0]['column']) ? (int)$this->input->post('order')[0]['column'] : 4;
        $order_dir = isset($this->input->post('order')[0]['dir'])    ? $this->input->post('order')[0]['dir']        : 'asc';

        $result = $this->credit_alert_model->supplier_credit_datatable($draw, $start, $length, $search, $order_col, $order_dir);
        echo json_encode($result);
    }

    /* Single combined endpoint the login popup sequence calls once, so it
       knows up front which of the 4 pop-ups to queue without 4 separate
       round-trips. Each count is 0 when the user lacks permission or the
       Preference toggle for that alert is off, so the JS side never needs
       to know about permissions/toggles itself — it just skips zero counts. */
    public function alert_counts()
    {
        $settings = $this->credit_alert_model->web_setting();

        $counts = array(
            'customer_credit' => 0,
            'supplier_credit' => 0,
            'expiration'      => 0,
            'stock'           => 0,
        );

        $has_credit = $this->permission1->method('credit_alert', 'create')->access();
        if ($has_credit && !empty($settings->credit_alert_enable)) {
            if (!empty($settings->credit_alert_customer_enable)) {
                $counts['customer_credit'] = $this->credit_alert_model->customer_credit_count();
            }
            if (!empty($settings->credit_alert_supplier_enable)) {
                $counts['supplier_credit'] = $this->credit_alert_model->supplier_credit_count();
            }
        }

        if ($this->permission1->method('expiry_alert', 'create')->access() && !empty($settings->expiry_alert_enable)) {
            $counts['expiration'] = $this->credit_alert_model->expiry_alert_count();
        }

        if ($this->permission1->method('stock_alert', 'create')->access() && !empty($settings->stock_alert_enable)) {
            $counts['stock'] = $this->credit_alert_model->stock_alert_count();
        }

        echo json_encode($counts);
    }

    /* Content for the 3 header-icon dropdowns (sections 6.1/6.2/6.3 of the
       spec) — fetched lazily by the JS the first time any icon is opened,
       not on every page load, since the breakdown queries (especially
       Expiration's) are heavier than the plain badge counts already loaded
       with every page via Template.php. Icon/dropdown visibility is
       permission-only (not toggle-gated, same as the badge counts) — a
       disabled pop-up still shows a live dropdown if the user has
       permission, only the login pop-up itself is suppressed. */
    public function header_dropdown_data()
    {
        $data = array(
            'credit_alert'  => null,
            'expiry_alert'  => null,
            'stock_alert'   => null,
        );

        if ($this->permission1->method('credit_alert', 'create')->access()) {
            $data['credit_alert'] = array(
                'customer' => $this->credit_alert_model->customer_credit_summary(),
                'supplier' => $this->credit_alert_model->supplier_credit_summary(),
            );
        }

        if ($this->permission1->method('expiry_alert', 'create')->access()) {
            $data['expiry_alert'] = $this->credit_alert_model->expiry_alert_breakdown();
        }

        if ($this->permission1->method('stock_alert', 'create')->access()) {
            $data['stock_alert'] = $this->credit_alert_model->stock_alert_breakdown();
        }

        echo json_encode($data);
    }

}
