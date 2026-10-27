<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");
require_once(__DIR__ . '/TCPDF-main/tcpdf.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class SalesReportInvoicewise extends TCPDF
{
    // Override the Header() method to remove the line
    public $pageTotal = 0;

    public function Header()
    {
        $this->pageTotal = 0;
    }

    // Page footer
    public function Footer()
    {
        $this->SetY(-12);
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 5, 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    public function updatePageTotal($amount)
    {
        $this->pageTotal += $amount;
    }
}

class StockReport extends TCPDF
{
    // Override the Header() method to remove the line
    public $pageTotal = 0;

    public function Header()
    {
        $this->pageTotal = 0;
    }

    public function updatePageTotal($amount)
    {
        $this->pageTotal += $amount;
    }
}

class Report extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            'report_model',
            'service/service_model'

        ));
        if (! $this->session->userdata('isLogIn'))
            redirect('login');
    }

    /*product stock part*/
    function bdtask_livestock_report()
    {
        $data['title']         = display('live_stock_report');
        $data['product_list']  = $this->report_model->product_list_stock();
        $data['category_list'] = $this->report_model->category_list_product();
        $data['store_list']    = $this->report_model->active_store_stock();
        $_SESSION['reporttype'] =   1;
        $data['product_group_list'] = $this->db
            ->select('id, name')
            ->from('product_group')
            ->where('status', 1)
            ->get()
            ->result_array();

        $data['module'] = "report";
        $data['page']   = "live_stock_report";
        echo modules::run('template/layout', $data);
    }

    function bdtask_stock_report()
    {
        $data['title']         = display('stock_report');
        $data['product_list']  = $this->report_model->product_list_stock();
        $data['category_list'] = $this->report_model->category_list_product();
        $data['store_list']    = $this->report_model->active_store_stock();
        $_SESSION['reporttype'] =   1;
        $data['product_group_list'] = $this->db
            ->select('id, name')
            ->from('product_group')
            ->where('status', 1)
            ->get()
            ->result_array();

        $data['module'] = "report";
        $data['page']   = "stock_report";
        echo modules::run('template/layout', $data);
    }

    public function getProductsByGroup()
    {
        $group_id = (int)$this->input->post('group_id');
        $products = $this->db
            ->select('pi.id, pi.product_name')
            ->from('product_group_details pgd')
            ->join('product_information pi', 'pi.id = pgd.product', 'inner')
            ->where('pgd.pid', $group_id)
            ->where('pi.status', 1)
            ->get()
            ->result_array();
        echo json_encode($products);
    }

    public function bdtask_checkStocklist()
    {
        // GET data
        $postData = $this->input->post();
        $data = $this->report_model->bdtask_getStock($postData);
        echo json_encode($data);
    }


    public function bdtask_cash_closing()
    {
        $data['title']      = "Reports | Daily Closing";
        $data['info']       = $this->report_model->accounts_closing_data();
        $data['pay_methods'] = $this->report_model->payment_methods();
        $data['module']     = "report";
        $data['page']       = "closing_form";
        echo modules::run('template/layout', $data);
    }

    public function add_daily_closing()
    {

        $closedata = $this->db->select('*')->from('daily_closing')->where('date', date('Y-m-d'))->get()->num_rows();
        if ($closedata > 0) {
            $this->session->set_flashdata(array('exception' => 'Already Closed Today'));
            redirect(base_url('closing_form'));
        }
        $todays_date = date("Y-m-d");
        $data = array(
            'last_day_closing'  =>  str_replace(',', '', $this->input->post('last_day_closing', TRUE)),
            'cash_in'           =>  str_replace(',', '', $this->input->post('cash_in', TRUE)),
            'cash_out'          =>  str_replace(',', '', $this->input->post('cash_out', TRUE)),
            'date'              =>  $todays_date,
            'amount'            =>  str_replace(',', '', $this->input->post('cash_in_hand', TRUE)),
            'status'            =>      1
        );
        $invoice_id = $this->report_model->daily_closing_entry($data);


        $this->session->set_flashdata(array('message' => display('successfully_added')));
        redirect(base_url('closing_report'));
    }


    public function bdtask_closing_report()
    {
        $daily_closing_data = $this->report_model->get_closing_report();
        $i = 0;

        if (!empty($daily_closing_data)) {
            foreach ($daily_closing_data as $k => $v) {
                $daily_closing_data[$k]['final_date'] = $this->occational->dateConvert(date("Y-m-d", strtotime($daily_closing_data[$k]['datetime'])));
            }
        }
        $data = array(
            'title'              => display('closing_report'),
            'daily_closing_data' => $daily_closing_data,
        );
        $data['module']   = "report";
        $data['page']     = "closing_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_closing_report_search()
    {
        $from_date = $this->input->get('from_date');
        $to_date = $this->input->get('to_date');
        $daily_closing_data = $this->report_model->get_date_wise_closing_report($from_date, $to_date);

        $i = 0;
        if (!empty($daily_closing_data)) {
            foreach ($daily_closing_data as $k => $v) {
                $daily_closing_data[$k]['final_date'] = $this->occational->dateConvert(date("Y-m-d", strtotime($daily_closing_data[$k]['datetime'])));
            }
            foreach ($daily_closing_data as $k => $v) {
                $i++;
                $daily_closing_data[$k]['sl'] = $i;
            }
        }

        $data = array(
            'title'              => display('closing_report'),
            'daily_closing_data' => $daily_closing_data,
            'from_date'          => $from_date,
            'to_date'            => $to_date,

        );

        $data['module']   = "report";
        $data['page']     = "closing_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_todays_report()
    {
        $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }

        $purchase_report = $this->report_model->todays_purchase_report();
        $purchase_amount = 0;
        if (!empty($purchase_report)) {
            $i = 0;
            foreach ($purchase_report as $k => $v) {
                $i++;
                $purchase_report[$k]['sl'] = $i;
                $purchase_report[$k]['prchse_date'] = $this->occational->dateConvert($purchase_report[$k]['purchase_date']);
                $purchase_amount = $purchase_amount + $purchase_report[$k]['grand_total_amount'];
            }
        }

        $data = array(
            'title'           => display('todays_report'),
            'sales_report'    => $sales_report,
            'sales_amount'    => number_format($sales_amount, 2, '.', ','),
            'purchase_amount' => number_format($purchase_amount, 2, '.', ','),
            'purchase_report' => $purchase_report,
            'date'            => $today = date('Y-m-d'),
        );

        $data['module']   = "report";
        $data['page']     = "todays_report";
        echo modules::run('template/layout', $data);
    }


    //    ============ its for todays_customer_receipt =============
    public function bdtask_todays_customer_received()
    {
        date_default_timezone_set('Asia/Colombo');

        $today = date('Y-m-d');
        $all_customer = $this->db->select('*')->from('customer_information')->get()->result();
        $todays_customer_receipt = $this->report_model->todays_customer_receipt($today);
        $data = array(
            'title'                   => "Received From Customer",
            'all_customer'            => $all_customer,
            'todays_customer_receipt' => $todays_customer_receipt,
            'today'                   => $today,
            'customer_id'             => '',
        );
        $data['module']   = "report";
        $data['page']     = "todays_customer_receipt";
        echo modules::run('template/layout', $data);
    }


    //    ============ its for todays_customer_receipt =============
    public function bdtask_customerwise_received()
    {
        date_default_timezone_set('Asia/Colombo');

        $customer_id = $this->input->post('customer_id', TRUE);
        $from_date   = $this->input->post('from_date', TRUE);
        $today       = date('Y-m-d');
        $all_customer = $this->db->select('*')->from('customer_information')->get()->result();
        $filter_customer_wise_receipt = $this->report_model->filter_customer_wise_receipt($customer_id, $from_date);
        $data = array(
            'title'                   => "Received From Customer",
            'all_customer'            => $all_customer,
            'todays_customer_receipt' => $filter_customer_wise_receipt,
            'today'                   => $from_date,
            'customer_info'           => $this->report_model->customerinfo_rpt($customer_id),
            'customer_id'            => $customer_id,
        );

        $data['module']   = "report";
        $data['page']     = "todays_customer_receipt";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_todays_sales_report()
    {
        // $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!$this->permission1->method('todays_sales_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
         $_SESSION['reporttype'] =   1;
        $data = array(
            'title'        => display('sales_report'),
            'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "sales_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_datewise_sales_report()
    {
        $from_date = $this->input->get('from_date');
        $to_date  = $this->input->get('to_date');
        $_SESSION['reporttype'] =   1;
        $sales_report = $this->report_model->retrieve_dateWise_SalesReports($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }
        $data = array(
            'title'        => display('sales_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "sales_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_userwise_sales_report()
    {
        $user_id = (!empty($this->input->get('user_id')) ? $this->input->get('user_id') : '');
        $star_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $end_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        if (!$this->permission1->method('user_wise_sales_report', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] =   1;

        // $sales_report = $this->report_model->user_sales_report($star_date, $end_date, $user_id);
        // $sales_amount = 0;
        // if (!empty($sales_report)) {
        //     $i = 0;
        //     foreach ($sales_report as $k => $v) {
        //         $i++;
        //         $sales_report[$k]['sl'] = $i;

        //         $sales_amount += $sales_report[$k]['amount'];
        //     }
        // }
        $user_list = $this->report_model->userList();
        $data = array(
            'title'         => display('user_wise_sales_report'),
            // 'sales_amount'  => number_format($sales_amount, 2, '.', ','),
            // 'sales_report'  => $sales_report,
            'from'          => $this->occational->dateConvert($star_date),
            'to'            => $this->occational->dateConvert($end_date),
            'user_list'     => $user_list,
            'user_id'       => $user_id,
        );
        $data['module']   = "report";
        $data['page']     = "user_wise_sales_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_invoice_wise_due_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));

        $data = array(
            'title'        => display('due_report'),
            'from_date'    => $from_date,
            'to_date'      => $to_date,

        );
        $_SESSION['reporttype'] =   1;

        $data['module']   = "report";
        $data['page']     = "due_report";
        echo modules::run('template/layout', $data);
    }

    // ================= Customer Receive Report =================
    // Same "filter panel -> fetch + cache in session -> open generate_* PDF in
    // a new tab" flow as Sales Report / Sales Order Report, not an inline
    // DataTable, to stay consistent with how every other report in this
    // module presents itself.
    public function bdtask_customer_receive_report()
    {
        if (!$this->permission1->method('customer_receive_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] = 1;
        $data['title']  = display('customer_receive_report');
        $data['module'] = "report";
        $data['page']   = "customer_receive_report";
        echo modules::run('template/layout', $data);
    }

    // AJAX: fetch + cache in session for the PDF generator (generate_customerreceivereport)
    public function customer_receive_reportinvoicewise()
    {
        $encryption_key = Config::$encryption_key;
        $from_date   = $this->input->post('from_date', TRUE);
        $to_date     = $this->input->post('to_date', TRUE);
        $empid       = $this->input->post('empid', TRUE); // effective type2, possibly password-escalated
        $branch      = (int)$this->input->post('branch', TRUE);
        $customer_id = (int)$this->input->post('customer_id', TRUE);
        $type2       = ($empid === 'B') ? 'B' : 'A';

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $where = "AES_DECRYPT(cr.type2, '{$encryption_key}') = '{$type2}'";
        if ($from_date)        $where .= " AND cr.date >= '{$from_date}'";
        if ($to_date)          $where .= " AND cr.date <= '{$to_date}'";
        if ($customer_id > 0)  $where .= " AND cr.customer = {$customer_id}";
        if ($branch > 0) {
            $where .= " AND cr.branch = {$branch}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $where .= " AND cr.branch IN (" . implode(',', $branchids) . ")";
        }

        $rows = $this->db->query("
            SELECT
                cr.date,
                AES_DECRYPT(cr.voucher_no, '{$encryption_key}') AS invoiceno,
                AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
                CAST(AES_DECRYPT(cr.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total
            FROM customer_receive cr
            LEFT JOIN customer_information ci ON ci.customer_id = cr.customer
            WHERE {$where}
            ORDER BY cr.date DESC, cr.id DESC
        ")->result_array();

        $_SESSION['customer_receive_reportdata'] = $rows;
        $_SESSION['crr_istype']   = $this->input->post('istype', TRUE);
        $_SESSION['crrfrom_date'] = $from_date;
        $_SESSION['crrto_date']   = $to_date;

        echo json_encode($rows);
    }

    public function generate_customerreceivereport()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Customer Receive Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $pdf->SetMargins(15, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Customer Receive Report", $_SESSION['crr_istype'], $_SESSION['crrfrom_date'], $_SESSION['crrto_date']);

        $printHead = function () use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            $pdf->Cell(45, 7, 'Voucher Date', 1, 0, 'L', true);
            $pdf->Cell(45, 7, 'Voucher No', 1, 0, 'L', true);
            $pdf->Cell(52, 7, 'Customer Name', 1, 0, 'L', true);
            $pdf->Cell(43, 7, 'Amount', 1, 0, 'R', true);
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };
        $printHead();

        $data       = isset($_SESSION['customer_receive_reportdata']) ? $_SESSION['customer_receive_reportdata'] : [];
        $lineHeight = 10;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;
        $fill       = false;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Customer Receive Report", $_SESSION['crr_istype'], $_SESSION['crrfrom_date'], $_SESSION['crrto_date']);
                $printHead();
            }
            $total   += $row['total'];
            $patotal += $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(45, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(45, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(52, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(43, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(142, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(43, 10, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $filename = "Customer Receive Report_" . date('Y-m-d') . ".pdf";
        $pdf->Output($filename, 'I');
    }

    // ================= Customer Due Report =================
    public function bdtask_customer_due_report()
    {
        if (!$this->permission1->method('customer_due_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] = 1;
        $data['title']  = display('customer_due_report');
        $data['module'] = "report";
        $data['page']   = "customer_due_report";
        echo modules::run('template/layout', $data);
    }

    // AJAX: fetch + cache in session for the PDF generator (generate_customerduereport).
    // One row per invoice (sale), aggregated the same way as Customer_receive's
    // getCustomerDueInvoices (invoice amount vs. paid amount vs. remaining due,
    // all derived from the customer_due ledger).
    public function customer_due_reportinvoicewise()
    {
        $encryption_key = Config::$encryption_key;
        $from_date   = $this->input->post('from_date', TRUE);
        $to_date     = $this->input->post('to_date', TRUE);
        $empid       = $this->input->post('empid', TRUE);
        $branch      = (int)$this->input->post('branch', TRUE);
        $customer_id = (int)$this->input->post('customer_id', TRUE);
        $status      = $this->input->post('status', TRUE); // '' | 'paid' | 'due'
        $type2       = ($empid === 'B') ? 'B' : 'A';

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $where = "cd.sale_id > 0 AND AES_DECRYPT(cd.type2, '{$encryption_key}') = '{$type2}'";
        if ($from_date)        $where .= " AND s.date >= '{$from_date}'";
        if ($to_date)          $where .= " AND s.date <= '{$to_date}'";
        if ($customer_id > 0)  $where .= " AND cd.customer = {$customer_id}";
        if ($branch > 0) {
            $where .= " AND IFNULL(s.branch, cd.branch) = {$branch}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $where .= " AND IFNULL(s.branch, cd.branch) IN (" . implode(',', $branchids) . ")";
        }

        $statusWhere = '';
        if ($status === 'paid') {
            $statusWhere = " WHERE t.due_amount <= 0.0001";
        } elseif ($status === 'due') {
            $statusWhere = " WHERE t.due_amount > 0.0001";
        }

        $rows = $this->db->query("
            SELECT * FROM (
                SELECT
                    cd.sale_id,
                    IFNULL(AES_DECRYPT(s.sale_id, '{$encryption_key}'), cd.sale_id) AS invoiceno,
                    IFNULL(s.date, '') AS date,
                    AES_DECRYPT(ci.customer_name, '{$encryption_key}') AS customer_name,
                    SUM(CASE
                        WHEN IFNULL(cd.sales_return_id,0) = 0 AND IFNULL(cd.customer_receive_id,0) = 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END) AS invoice_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(cd.sales_return_id,0) > 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)) AS returned_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(cd.customer_receive_id,0) > 0
                        THEN CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)) AS paid_amount,
                    SUM(CAST(AES_DECRYPT(cd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM customer_due cd
                LEFT JOIN sale s ON s.id = cd.sale_id
                LEFT JOIN customer_information ci ON ci.customer_id = IFNULL(s.customer_id, cd.customer)
                WHERE {$where}
                GROUP BY cd.sale_id
            ) t
            {$statusWhere}
            ORDER BY t.date DESC, t.sale_id DESC
        ")->result_array();

        foreach ($rows as $k => $r) {
            $rows[$k]['status'] = ((float)$r['due_amount'] > 0.0001) ? 'Not Paid' : 'Paid';
        }

        $_SESSION['customer_due_reportdata'] = $rows;
        $_SESSION['cdr_istype']   = $this->input->post('istype', TRUE);
        $_SESSION['cdrfrom_date'] = $from_date;
        $_SESSION['cdrto_date']   = $to_date;

        echo json_encode($rows);
    }

    public function generate_customerduereport()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Customer Due Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $pdf->SetMargins(15, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Customer Due Report", $_SESSION['cdr_istype'], $_SESSION['cdrfrom_date'], $_SESSION['cdrto_date']);

        $printHead = function () use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            $pdf->Cell(20, 7, 'Invoice ID',   1, 0, 'L', true);
            $pdf->Cell(20, 7, 'Date',         1, 0, 'L', true);
            $pdf->Cell(24, 7, 'Customer',     1, 0, 'L', true);
            $pdf->Cell(23, 7, 'Invoice Amt',  1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Returned Amt', 1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Paid Amt',     1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Due Amt',      1, 0, 'R', true);
            $pdf->Cell(29, 7, 'Status',       1, 0, 'C', true);
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };
        $printHead();

        $data          = isset($_SESSION['customer_due_reportdata']) ? $_SESSION['customer_due_reportdata'] : [];
        $lineHeight    = 10;
        $maxY          = 270;
        $patotal       = 0; $totalInvoice  = 0;
        $patotal_r     = 0; $totalReturned = 0;
        $patotal_p     = 0; $totalPaid     = 0;
        $patotal_d     = 0; $totalDue      = 0;
        $fill          = false;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0; $patotal_r = 0; $patotal_p = 0; $patotal_d = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Customer Due Report", $_SESSION['cdr_istype'], $_SESSION['cdrfrom_date'], $_SESSION['cdrto_date']);
                $printHead();
            }
            $totalInvoice  += $row['invoice_amount'];  $patotal   += $row['invoice_amount'];
            $totalReturned += $row['returned_amount']; $patotal_r += $row['returned_amount'];
            $totalPaid     += $row['paid_amount'];     $patotal_p += $row['paid_amount'];
            $totalDue      += $row['due_amount'];      $patotal_d += $row['due_amount'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->SetTextColor(30, 41, 59);
            $pdf->Cell(20, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(20, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(24, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(23, 8, number_format($row['invoice_amount'], 2),  1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['returned_amount'], 2), 1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['paid_amount'], 2),     1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['due_amount'], 2),      1, 0, 'R', true);
            if ($row['status'] === 'Paid') { $pdf->SetTextColor(21, 128, 61); } else { $pdf->SetTextColor(220, 38, 38); }
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->Cell(29, 8, $row['status'], 1, 1, 'C', true);
            $pdf->SetTextColor(30, 41, 59);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(64, 10, "Total:", 1, 0, 'L', true);
        $pdf->Cell(23, 10, number_format($totalInvoice, 2),  1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalReturned, 2), 1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalPaid, 2),     1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalDue, 2),      1, 0, 'R', true);
        $pdf->Cell(29, 10, '', 1, 1, 'C', true);
        $pdf->updatePageTotal($patotal);

        $filename = "Customer Due Report_" . date('Y-m-d') . ".pdf";
        $pdf->Output($filename, 'I');
    }

    // ================= Supplier Payment Report =================
    // Mirrors Customer Receive Report exactly, but against supplier_payment
    // instead of customer_receive.
    public function bdtask_supplier_payment_report()
    {
        if (!$this->permission1->method('supplier_payment_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] = 1;
        $data['title']  = display('supplier_payment_report');
        $data['module'] = "report";
        $data['page']   = "supplier_payment_report";
        echo modules::run('template/layout', $data);
    }

    public function supplier_payment_reportinvoicewise()
    {
        $encryption_key = Config::$encryption_key;
        $from_date   = $this->input->post('from_date', TRUE);
        $to_date     = $this->input->post('to_date', TRUE);
        $empid       = $this->input->post('empid', TRUE);
        $branch      = (int)$this->input->post('branch', TRUE);
        $supplier_id = (int)$this->input->post('supplier_id', TRUE);
        $type2       = ($empid === 'B') ? 'B' : 'A';

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $where = "AES_DECRYPT(sp.type2, '{$encryption_key}') = '{$type2}'";
        if ($from_date)        $where .= " AND sp.date >= '{$from_date}'";
        if ($to_date)          $where .= " AND sp.date <= '{$to_date}'";
        if ($supplier_id > 0)  $where .= " AND sp.supplier = {$supplier_id}";
        if ($branch > 0) {
            $where .= " AND sp.branch = {$branch}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $where .= " AND sp.branch IN (" . implode(',', $branchids) . ")";
        }

        $rows = $this->db->query("
            SELECT
                sp.date,
                AES_DECRYPT(sp.voucher_no, '{$encryption_key}') AS invoiceno,
                AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
                CAST(AES_DECRYPT(sp.total_amount, '{$encryption_key}') AS DECIMAL(15,2)) AS total
            FROM supplier_payment sp
            LEFT JOIN supplier_information si ON si.supplier_id = sp.supplier
            WHERE {$where}
            ORDER BY sp.date DESC, sp.id DESC
        ")->result_array();

        $_SESSION['supplier_payment_reportdata'] = $rows;
        $_SESSION['spr_istype']   = $this->input->post('istype', TRUE);
        $_SESSION['sprfrom_date'] = $from_date;
        $_SESSION['sprto_date']   = $to_date;

        echo json_encode($rows);
    }

    public function generate_supplierpaymentreport()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Supplier Payment Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $pdf->SetMargins(15, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Supplier Payment Report", $_SESSION['spr_istype'], $_SESSION['sprfrom_date'], $_SESSION['sprto_date']);

        $printHead = function () use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            $pdf->Cell(45, 7, 'Voucher Date', 1, 0, 'L', true);
            $pdf->Cell(45, 7, 'Voucher No', 1, 0, 'L', true);
            $pdf->Cell(52, 7, 'Supplier Name', 1, 0, 'L', true);
            $pdf->Cell(43, 7, 'Amount', 1, 0, 'R', true);
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };
        $printHead();

        $data       = isset($_SESSION['supplier_payment_reportdata']) ? $_SESSION['supplier_payment_reportdata'] : [];
        $lineHeight = 10;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;
        $fill       = false;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Supplier Payment Report", $_SESSION['spr_istype'], $_SESSION['sprfrom_date'], $_SESSION['sprto_date']);
                $printHead();
            }
            $total   += $row['total'];
            $patotal += $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(45, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(45, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(52, 8, $row['supplier_name'], 1, 0, 'L', true);
            $pdf->Cell(43, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(142, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(43, 10, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $filename = "Supplier Payment Report_" . date('Y-m-d') . ".pdf";
        $pdf->Output($filename, 'I');
    }

    // ================= Supplier Due Report =================
    // Mirrors Customer Due Report exactly, but against supplier_due / purchase
    // instead of customer_due / sale.
    public function bdtask_supplier_due_report()
    {
        if (!$this->permission1->method('supplier_due_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] = 1;
        $data['title']  = display('supplier_due_report');
        $data['module'] = "report";
        $data['page']   = "supplier_due_report";
        echo modules::run('template/layout', $data);
    }

    public function supplier_due_reportinvoicewise()
    {
        $encryption_key = Config::$encryption_key;
        $from_date   = $this->input->post('from_date', TRUE);
        $to_date     = $this->input->post('to_date', TRUE);
        $empid       = $this->input->post('empid', TRUE);
        $branch      = (int)$this->input->post('branch', TRUE);
        $supplier_id = (int)$this->input->post('supplier_id', TRUE);
        $status      = $this->input->post('status', TRUE); // '' | 'paid' | 'due'
        $type2       = ($empid === 'B') ? 'B' : 'A';

        $branchResult = $this->db->select('branch.id')
            ->from('sec_branch')
            ->join('branch', 'branch.id = sec_branch.branchid')
            ->where('sec_branch.userid', $this->session->userdata('id'))
            ->group_by('sec_branch.branchid')
            ->get()->result();
        $branchids = array_column($branchResult, 'id');

        $where = "sd.purchase_id > 0 AND AES_DECRYPT(sd.type2, '{$encryption_key}') = '{$type2}'";
        if ($from_date)        $where .= " AND p.date >= '{$from_date}'";
        if ($to_date)          $where .= " AND p.date <= '{$to_date}'";
        if ($supplier_id > 0)  $where .= " AND sd.supplier = {$supplier_id}";
        if ($branch > 0) {
            $where .= " AND IFNULL(p.branch, sd.branch) = {$branch}";
        } elseif ($this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $where .= " AND IFNULL(p.branch, sd.branch) IN (" . implode(',', $branchids) . ")";
        }

        $statusWhere = '';
        if ($status === 'paid') {
            $statusWhere = " WHERE t.due_amount <= 0.0001";
        } elseif ($status === 'due') {
            $statusWhere = " WHERE t.due_amount > 0.0001";
        }

        $rows = $this->db->query("
            SELECT * FROM (
                SELECT
                    sd.purchase_id,
                    IFNULL(AES_DECRYPT(p.purchase_id, '{$encryption_key}'), sd.purchase_id) AS invoiceno,
                    IFNULL(p.date, '') AS date,
                    AES_DECRYPT(si.supplier_name, '{$encryption_key}') AS supplier_name,
                    SUM(CASE
                        WHEN IFNULL(sd.purchase_return_id,0) = 0 AND IFNULL(sd.supplier_payment_id,0) = 0
                        THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END) AS purchase_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(sd.purchase_return_id,0) > 0
                        THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)) AS returned_amount,
                    ABS(SUM(CASE
                        WHEN IFNULL(sd.supplier_payment_id,0) > 0
                        THEN CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))
                        ELSE 0 END)) AS paid_amount,
                    SUM(CAST(AES_DECRYPT(sd.amount, '{$encryption_key}') AS DECIMAL(15,4))) AS due_amount
                FROM supplier_due sd
                LEFT JOIN purchase p ON p.id = sd.purchase_id
                LEFT JOIN supplier_information si ON si.supplier_id = IFNULL(p.supplier_id, sd.supplier)
                WHERE {$where}
                GROUP BY sd.purchase_id
            ) t
            {$statusWhere}
            ORDER BY t.date DESC, t.purchase_id DESC
        ")->result_array();

        foreach ($rows as $k => $r) {
            $rows[$k]['status'] = ((float)$r['due_amount'] > 0.0001) ? 'Not Paid' : 'Paid';
        }

        $_SESSION['supplier_due_reportdata'] = $rows;
        $_SESSION['sdr_istype']   = $this->input->post('istype', TRUE);
        $_SESSION['sdrfrom_date'] = $from_date;
        $_SESSION['sdrto_date']   = $to_date;

        echo json_encode($rows);
    }

    public function generate_supplierduereport()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Supplier Due Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $pdf->SetMargins(15, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Supplier Due Report", $_SESSION['sdr_istype'], $_SESSION['sdrfrom_date'], $_SESSION['sdrto_date']);

        $printHead = function () use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            $pdf->Cell(20, 7, 'Purchase ID',  1, 0, 'L', true);
            $pdf->Cell(20, 7, 'Date',         1, 0, 'L', true);
            $pdf->Cell(24, 7, 'Supplier',     1, 0, 'L', true);
            $pdf->Cell(23, 7, 'Purchase Amt', 1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Returned Amt', 1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Paid Amt',     1, 0, 'R', true);
            $pdf->Cell(23, 7, 'Due Amt',      1, 0, 'R', true);
            $pdf->Cell(29, 7, 'Status',       1, 0, 'C', true);
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };
        $printHead();

        $data          = isset($_SESSION['supplier_due_reportdata']) ? $_SESSION['supplier_due_reportdata'] : [];
        $lineHeight    = 10;
        $maxY          = 270;
        $patotal       = 0; $totalPurchase = 0;
        $patotal_r     = 0; $totalReturned = 0;
        $patotal_p     = 0; $totalPaid     = 0;
        $patotal_d     = 0; $totalDue      = 0;
        $fill          = false;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0; $patotal_r = 0; $patotal_p = 0; $patotal_d = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Supplier Due Report", $_SESSION['sdr_istype'], $_SESSION['sdrfrom_date'], $_SESSION['sdrto_date']);
                $printHead();
            }
            $totalPurchase += $row['purchase_amount']; $patotal   += $row['purchase_amount'];
            $totalReturned += $row['returned_amount']; $patotal_r += $row['returned_amount'];
            $totalPaid     += $row['paid_amount'];     $patotal_p += $row['paid_amount'];
            $totalDue      += $row['due_amount'];      $patotal_d += $row['due_amount'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->SetTextColor(30, 41, 59);
            $pdf->Cell(20, 8, $row['invoiceno'],      1, 0, 'L', true);
            $pdf->Cell(20, 8, $row['date'],           1, 0, 'L', true);
            $pdf->Cell(24, 8, $row['supplier_name'],  1, 0, 'L', true);
            $pdf->Cell(23, 8, number_format($row['purchase_amount'], 2), 1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['returned_amount'], 2), 1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['paid_amount'], 2),     1, 0, 'R', true);
            $pdf->Cell(23, 8, number_format($row['due_amount'], 2),      1, 0, 'R', true);
            if ($row['status'] === 'Paid') { $pdf->SetTextColor(21, 128, 61); } else { $pdf->SetTextColor(220, 38, 38); }
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->Cell(29, 8, $row['status'], 1, 1, 'C', true);
            $pdf->SetTextColor(30, 41, 59);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(64, 10, "Total:", 1, 0, 'L', true);
        $pdf->Cell(23, 10, number_format($totalPurchase, 2), 1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalReturned, 2), 1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalPaid, 2),     1, 0, 'R', true);
        $pdf->Cell(23, 10, number_format($totalDue, 2),      1, 0, 'R', true);
        $pdf->Cell(29, 10, '', 1, 1, 'C', true);
        $pdf->updatePageTotal($patotal);

        $filename = "Supplier Due Report_" . date('Y-m-d') . ".pdf";
        $pdf->Output($filename, 'I');
    }


    public function bdtask_shippingcost_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $sales_report = $this->report_model->retrieve_dateWise_Shippingcost($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {
                $i++;
                $sales_report[$k]['sl'] = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
            }
        }
        $data = array(
            'title'        => display('shipping_cost_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "shippincost_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));

        if (!$this->permission1->method('todays_purchase_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] =   1;
        $data['title']   = display('purchase_report');
        $data['from']   = $from_date;
        $data['to']   = $to_date;
        $data['module']   = "report";
        $data['page']     = "purchase_report";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_report_category_wise()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date   = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $category  = (!empty($this->input->get('category')) ? $this->input->get('category') : '');
        $category_list = $this->report_model->category_list_product();
        $product_list = $this->report_model->product_list();
        $_SESSION['reporttype'] =   1;

        // $purchase_report_category_wise = $this->report_model->purchase_report_category_wise($from_date, $to_date, $category);
        $data = array(
            'title'         => "Purchase Report (Category Wise)",
            'category_list' => $category_list,
            'from'          => $from_date,
            'to'            => $to_date,
            'category_id'   => $category,
            'product_list'   => $product_list,

            // 'purchase_report_category_wise' => $purchase_report_category_wise,
        );
        if (!$this->permission1->method('purchase_report_category_wise', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['module']   = "report";
        $data['page']     = "purchase_report_category_wise";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_sale_report_productwise()
    {
        // $from_date      = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        // $to_date        = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        // $product_id     = (!empty($this->input->get('product_id')) ? $this->input->get('product_id') : '');

        // $product_report = $this->report_model->retrieve_product_sales_report($from_date, $to_date, $product_id);
        if (!$this->permission1->method('product_wise_sales_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $product_list = $this->report_model->product_list();
         $_SESSION['reporttype'] =   1;
        // if (!empty($product_report)) {
        //     $i = 0;
        //     foreach ($product_report as $k => $v) {
        //         $i++;
        //         $product_report[$k]['sl'] = $i;
        //     }
        // }
        // $sub_total = 0;
        // if (!empty($product_report)) {
        //     foreach ($product_report as $k => $v) {
        //         $product_report[$k]['sales_date'] = $this->occational->dateConvert($product_report[$k]['date']);
        //         $sub_total = $sub_total + $product_report[$k]['total_amount'];
        //     }
        // }
        $data = array(
            'title'          => display('sales_report_product_wise'),
            // 'sub_total'      => number_format($sub_total, 2, '.', ','),
            // 'product_report' => $product_report,
            'product_list'   => $product_list,
            // 'product_id'     => $product_id,
            // 'from'           => $from_date,
            // 'to'             => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "product_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_categorywise_sales_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $category = (!empty($this->input->get('category')) ? $this->input->get('category') : '');
        $category_list = $this->report_model->category_list_product();
        $product_list = $this->report_model->product_list();
        if (!$this->permission1->method('sales_report_category_wise', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $_SESSION['reporttype'] =   1;
        // $sales_report_category_wise = $this->report_model->sales_report_category_wise($from_date, $to_date, $category);
        $data = array(
            'title'                      => display('sales_report_category_wise'),
            'category_list'              => $category_list,
            'product_list'   => $product_list,
            // 'sales_report_category_wise' => $sales_report_category_wise,
            'from'                       => $from_date,
            'to'                         => $to_date,
            'category_id'                => $category,
        );
        $data['module']   = "report";
        $data['page']     = "sales_report_category_wise";
        echo modules::run('template/layout', $data);
    }



    public function bdtask_sales_return()
    {
        $from_date = $this->input->post('from_date', TRUE);
        $to_date   = $this->input->post('to_date', TRUE);
        $start     = (!empty($from_date) ? $from_date : date('Y-m-d'));
        $end       = (!empty($to_date) ? $to_date : date('Y-m-d'));
        $return_list = $this->report_model->sales_return_list($start, $end);
        $_SESSION['reporttype'] =   1;
        if (!empty($return_list)) {
            foreach ($return_list as $k => $v) {
                $return_list[$k]['final_date'] = $this->occational->dateConvert($return_list[$k]['date_return']);
            }
        }

        $data = array(
            'title'      => display('invoice_return'),
            'return_list' => $return_list,
            'from_date'  => $start,
            'to_date'    => $end,
        );

        $data['module']   = "report";
        $data['page']     = "sales_return";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_supplier_return()
    {
        $from_date = $this->input->post('from_date', TRUE);
        $to_date   = $this->input->post('to_date', TRUE);
        $start     = (!empty($from_date) ? $from_date : date('Y-m-d'));
        $end       = (!empty($to_date) ? $to_date : date('Y-m-d'));
        $return_list = $this->report_model->supplier_return($start, $end);
        if (!empty($return_list)) {
            foreach ($return_list as $k => $v) {
                $return_list[$k]['final_date'] = $this->occational->dateConvert($return_list[$k]['date_return']);
            }
        }

        $data = array(
            'title'       => display('supplier_return'),
            'return_list' => $return_list,
            'start_date'  => $start,
            'end_date'    => $end,
        );

        $data['module']   = "report";
        $data['page']     = "supplier_return";
        echo modules::run('template/layout', $data);
    }

    public function bdtask_tax_report()
    {
        $from_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $to_date = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $sales_report = $this->report_model->retrieve_dateWise_tax($from_date, $to_date);
        $sales_amount = 0;
        if (!empty($sales_report)) {
            $i = 0;
            foreach ($sales_report as $k => $v) {

                $sales_report[$k]['sl']         = $i;
                $sales_report[$k]['sales_date'] = $this->occational->dateConvert($sales_report[$k]['date']);
                $sales_amount = $sales_amount + $sales_report[$k]['total_amount'];
                $i++;
            }
        }
        $data = array(
            'title'        => display('tax_report'),
            'sales_amount' => $sales_amount,
            'sales_report' => $sales_report,
            'from_date'    => $from_date,
            'to_date'      => $to_date,
        );

        $data['module']   = "report";
        $data['page']     = "tax_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_profit_report()
    {
        $start_date = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        $end_date   = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        $total_profit_report = $this->report_model->total_profit_report($start_date, $end_date);
        $profit_ammount   = 0;
        $SubTotalSupAmnt  = 0;
        $SubTotalSaleAmnt = 0;
        if (!empty($total_profit_report)) {
            $i = 0;
            foreach ($total_profit_report as $k => $v) {
                $total_profit_report[$k]['sl'] = $i;
                $total_profit_report[$k]['prchse_date'] = $this->occational->dateConvert($total_profit_report[$k]['date']);
                $profit_ammount = $profit_ammount + $total_profit_report[$k]['total_profit'];
                $SubTotalSupAmnt = $SubTotalSupAmnt + $total_profit_report[$k]['total_supplier_rate'];
                $SubTotalSaleAmnt = $SubTotalSaleAmnt + $total_profit_report[$k]['total_sale'];
            }
        }

        $data = array(
            'title'               => display('profit_report'),
            'profit_ammount'      => number_format($profit_ammount, 2, '.', ','),
            'total_profit_report' => $total_profit_report,
            'from'                => $start_date,
            'to'                  => $end_date,
            'SubTotalSupAmnt'     => number_format($SubTotalSupAmnt, 2, '.', ','),
            'SubTotalSaleAmnt'    => number_format($SubTotalSaleAmnt, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "profit_report";
        echo modules::run('template/layout', $data);
    }


    public function bdtask_add_closing()
    {

        $this->form_validation->set_rules('opening_bal', display('opening_balance'), 'max_length[100]|required');
        if ($this->form_validation->run()) {
            $createby    = $this->session->userdata('id');
            $check_exist = $this->db->select('')->from('closing_records')->where('user_id', $createby)->where('DATE(datetime)', date('Y-m-d'))->where('head_code', $this->input->post('head_code', true))->get()->num_rows();
            if ($check_exist > 0) {
                $data['status'] = 0;
                $data['message'] = 'Already Closed Today';
                echo json_encode($data);
                exit;
            }
            $createdate = date('Y-m-d H:i:s');
            $postData = array(
                'head_code'       => $this->input->post('head_code', true),
                'opening_balance' => $this->input->post('opening_bal', true),
                'amount_in'       => $this->input->post('total_received', true),
                'amount_out'      => $this->input->post('total_paid', true),
                'closign_balance' => $this->input->post('closing', true),
                'user_id'         => $createby,
                'status'          => 1
            );
            if ($this->report_model->create_opening($postData)) {
                $data['status'] = 1;
                $data['message'] = display('successfully_saved');
            } else {
                $data['status'] = 0;
                $data['message'] = display('please_try_again');
            }
        } else {
            $data['status'] = 0;
            $data['message'] = validation_errors();
        }
        echo json_encode($data);
        exit;
    }

    public function CheckReportList()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->getReportList($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }
    public function getSalesReportList()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->getSalesReportList($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }
    public function get_retrieve_dateWise_DueReports()
    {
        // echo "bb";
        // exit;
        $postData = $this->input->post();
        $data = $this->report_model->get_retrieve_dateWise_DueReports($postData);
        // dd($data);
        // exit;
        echo json_encode($data);
    }

    public function sales_reportinvoicewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');
        $customer_id = $this->input->post('customer_id');
               $incident_type = $this->input->post('incident_type');


        $report_data = $this->report_model->sales_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id,  $incident_type);
        $_SESSION['sale_reportsri'] =  $report_data;
        $_SESSION['sri_istype'] =   $this->input->post('istype');
        $_SESSION['srifrom_date'] = $from_date;
        $_SESSION['srito_date'] =  $to_date;


        echo json_encode($_SESSION['sale_reportsri']);
    }

    public function purchase_reportinvoicewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');
        $supplier_id = $this->input->post('supplier_id');
        $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->bdtask_purchase_report($from_date, $to_date, $empid, $branch, $supplier_id, $incident_type);
        $_SESSION['purchase_reportpri'] =  $report_data;
        $_SESSION['pri_istype'] =   $this->input->post('istype');
        $_SESSION['prifrom_date'] = $from_date;
        $_SESSION['prito_date'] =  $to_date;
        $_SESSION['pri_incident_type'] =  $incident_type;


        echo json_encode($_SESSION['purchase_reportpri']);
    }

    public function sales_reportproductwise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $productid = $this->input->post('productid');
        $branch = $this->input->post('branch');
        $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->retrieve_product_sales_report($from_date, $to_date, $productid, $empid, $branch,$incident_type);
        $_SESSION['sale_reportsrp'] =  $report_data;
        $_SESSION['srp_istype'] =   $this->input->post('istype');
        $_SESSION['srpfrom_date'] = $from_date;
        $_SESSION['srpto_date'] =  $to_date;
        echo json_encode($_SESSION['sale_reportsrp']);
    }



    public function sales_reportcategorywise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $category = $this->input->post('category');
        $product = $this->input->post('product');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->sales_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch);
        $_SESSION['sale_reportsrc'] =  $report_data;
        $_SESSION['src_istype'] =   $this->input->post('istype');
        $_SESSION['srcfrom_date'] = $from_date;
        $_SESSION['srcto_date'] =  $to_date;
        echo json_encode($_SESSION['sale_reportsrc']);
    }

    public function sales_reportemployeewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $employee = $this->input->post('employee');
        $branch = $this->input->post('branch');

        $report_data = $this->report_model->user_sales_report($from_date, $to_date, $employee, $empid, $branch);
        $_SESSION['sale_reportsre'] =  $report_data;
        $_SESSION['sre_istype'] =   $this->input->post('istype');
        $_SESSION['srefrom_date'] = $from_date;
        $_SESSION['sreto_date'] =  $to_date;


        echo json_encode($_SESSION['sale_reportsre']);
    }


    public function purchase_reportcategorywise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $category = $this->input->post('category');
        $product = $this->input->post('product');
        $branch = $this->input->post('branch');


        $report_data = $this->report_model->purchase_report_category_wise($from_date, $to_date, $category, $product, $empid, $branch);
        $_SESSION['purchase_reportprc'] =  $report_data;
        $_SESSION['prc_istype'] =   $this->input->post('istype');
        $_SESSION['prcfrom_date'] = $from_date;
        $_SESSION['prcto_date'] =  $to_date;


        echo json_encode($_SESSION['purchase_reportprc']);
    }


    public function generate_salesreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Invoice Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['sri_istype'], $_SESSION['srifrom_date'], $_SESSION['srito_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(45, 7, 'Sale Date', 1, 0, 'L', true);
        $pdf->Cell(33, 7, 'Invoice No', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Incident Type', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Customer Name', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['sale_reportsri']) ? $_SESSION['sale_reportsri'] : [];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        $fill = false;
        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $page = $page + 1;
                $pdf->AddPage();
                        $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['sri_istype'], $_SESSION['srifrom_date'], $_SESSION['srito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(45, 7, 'Sale Date', 1, 0, 'L', true);
                $pdf->Cell(33, 7, 'Invoice No', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Incident Type', 1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Customer Name', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $total = $total + $row['total'];
            $patotal = $patotal + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell(45, 8, $row['date'], 1, 0, 'L', true);
            $pdf->Cell(33, 8,  $row['invoiceno'], 1, 0, 'L', true);
            $pdf->Cell(40, 8,  $row['incidenttype'], 1, 0, 'L', true);
            $pdf->Cell(35, 8,  $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(153, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(40, 10, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Sales Report(Invoice Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

   public function generate_purchasereportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Report(Invoice Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Report (Invoice Wise)", $_SESSION['pri_istype'], $_SESSION['prifrom_date'], $_SESSION['prito_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(45, 7, 'Purchase Date', 1, 0, 'L', true);
        $pdf->Cell(33, 7, 'Invoice No', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Incident Type', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Supplier Name', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['purchase_reportpri']) ? $_SESSION['purchase_reportpri'] : [];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        $fill = false;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Invoice Wise)", $_SESSION['pri_istype'], $_SESSION['prifrom_date'], $_SESSION['prito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(45, 7, 'Purchase Date', 1, 0, 'L', true);
                $pdf->Cell(33, 7, 'Invoice No', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Incident Type', 1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Supplier Name', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell(45, 8, $row['date'], 1, 0, 'L', true);
            $pdf->Cell(33, 8,  $row['invoiceno'], 1, 0, 'L', true);
            $pdf->Cell(40, 8,  $row['incidenttype'], 1, 0, 'L', true);
            $pdf->Cell(35, 8,  $row['supplier_name'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(153, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(40, 10, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);




        $date = date('Y-m-d');
        $filename = "Purchase Report (Invoice Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }


    public function generate_salesreportproduct()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Product Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Product Wise)", $_SESSION['srp_istype'], $_SESSION['srpfrom_date'], $_SESSION['srpto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(24, 7, 'Sale Date', 1, 0, 'L', true);
        $pdf->Cell(36, 7, 'Product Name', 1, 0, 'L', true);
        $pdf->Cell(23, 7, 'Invoice No', 1, 0, 'L', true);
        $pdf->Cell(22, 7, 'Invoice Type', 1, 0, 'L', true);
        $pdf->Cell(15, 7, 'Customer', 1, 0, 'L', true);
        $pdf->Cell(27, 7, 'Rate', 1, 0, 'R', true);
        $pdf->Cell(15, 7, 'Qty', 1, 0, 'R', true);
        $pdf->Cell(27, 7, 'Total', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['sale_reportsrp']) ? $_SESSION['sale_reportsrp'] : [];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        $fill = false;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Product Wise)", $_SESSION['srp_istype'], $_SESSION['srpfrom_date'], $_SESSION['srpto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(24, 7, 'Sale Date', 1, 0, 'L', true);
                $pdf->Cell(36, 7, 'Product Name', 1, 0, 'L', true);
                $pdf->Cell(23, 7, 'Invoice No', 1, 0, 'L', true);
                $pdf->Cell(22, 7, 'Invoice Type', 1, 0, 'L', true);
                $pdf->Cell(15, 7, 'Customer', 1, 0, 'L', true);
                $pdf->Cell(27, 7, 'Rate', 1, 0, 'R', true);
                $pdf->Cell(15, 7, 'Qty', 1, 0, 'R', true);
                $pdf->Cell(27, 7, 'Total', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->MultiCell(24, 8, $row['date'], 1, 'L', true, 0);
            $pdf->MultiCell(36, 8,  $row['product_name'], 1, 'L', true, 0);
            $pdf->MultiCell(23, 8,  $row['sale_id'], 1, 'L', true, 0);
            $pdf->MultiCell(22, 8,  $row['incidenttype'], 1, 'L', true, 0);
            $pdf->MultiCell(15, 8,  $row['customer_name'], 1, 'L', true, 0);
            $pdf->MultiCell(27, 8, number_format($row['product_rate'], 2), 1, 'R', true, 0);
            $pdf->MultiCell(15, 8,  $row['quantity']." ". $row['unit_name'], 1, 'R', true, 0);
            $pdf->MultiCell(27, 8, number_format($row['total'], 2), 1, 'R', true, 0);
            $pdf->Ln(10);
            $fill = !$fill;

            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(108, 10, "", 1, 0, 'L', true);
        $pdf->Cell(35, 10, number_format($total, 2), 1, 1, 'R', true);

        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Sales Report (Product Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_salesreportcategory()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Category Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(50, 7, 'Category Name', 1, 0, 'L', true);
        $pdf->Cell(60, 7, 'Product Name', 1, 0, 'L', true);
        $pdf->Cell(45, 7, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data =  $_SESSION['sale_reportsrc'];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        $fill = false;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(50, 7, 'Category Name', 1, 0, 'L', true);
                $pdf->Cell(60, 7, 'Product Name', 1, 0, 'L', true);
                $pdf->Cell(45, 7, 'Qty', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total_price'];
            $total = $total + $row['total_price'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->MultiCell(50, 8, $row['category_name'], 1, 'L', true, 0);
            $pdf->MultiCell(60, 8, $row['product_name'], 1, 'L', true, 0);
            $pdf->MultiCell(45, 8, $row['quantity'], 1, 'C', true, 0);
            $pdf->MultiCell(30, 8,  number_format($row['total_price'], 2), 1, 'R', true, 0);
            $pdf->Ln(10);
            $fill = !$fill;

            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(100, 10, "", 1, 0, 'L', true);
        $pdf->Cell(35, 10, number_format($total, 2), 1, 1, 'R', true);

        $pdf->updatePageTotal($patotal);



        $date = date('Y-m-d');
        $filename = "Sales Report (Category Wise)_$date.pdf";
        ob_end_clean();

        $pdf->Output($filename, 'I');
    }


    public function generate_salesreportemployee()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Report(Employee Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Report (Employee Wise)", $_SESSION['sre_istype'], $_SESSION['srefrom_date'], $_SESSION['sreto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(50, 7, 'Employee ID', 1, 0, 'L', true);
        $pdf->Cell(50, 7, 'Employee Name', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Total Sale', 1, 0, 'C', true);
        $pdf->Cell(45, 7, 'Total Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data =  $_SESSION['sale_reportsre'];
        $lineHeight = 10;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        $fill = false;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Report (Employee Wise)", $_SESSION['sre_istype'], $_SESSION['srefrom_date'], $_SESSION['sreto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(50, 7, 'First Name', 1, 0, 'L', true);
                $pdf->Cell(50, 7, 'Last Name', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Total Sale', 1, 0, 'C', true);
                $pdf->Cell(45, 7, 'Total Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['amount'];
            $total = $total + $row['amount'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell(50, 8, $row['first_name'], 1, 0, 'L', true);
            $pdf->Cell(50, 8,  $row['last_name'], 1, 0, 'L', true);
            $pdf->Cell(40, 8,  $row['total_invoice'], 1, 0, 'C', true);
            $pdf->Cell(45, 8, number_format($row['amount'], 2), 1, 0, 'R', true);
            $pdf->Ln(8);
            $fill = !$fill;

            // $pdf->Cell(40, 8, number_format($row['total'], 2), 0, 1, 'R');
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(100, 10, "", 1, 0, 'L', true);
        $pdf->Cell(35, 10, number_format($total, 2), 1, 1, 'R', true);

        $pdf->updatePageTotal($patotal);




        $date = date('Y-m-d');
        $filename = "Sales Report (Employee Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_purchasereportcategory()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Report(Category Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Report (Category Wise)", $_SESSION['prc_istype'], $_SESSION['prcfrom_date'], $_SESSION['prcto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(50, 7, 'Category Name', 1, 0, 'L', true);
        $pdf->Cell(60, 7, 'Product Name', 1, 0, 'L', true);
        $pdf->Cell(45, 7, 'Qty', 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data =  $_SESSION['purchase_reportprc'];
        $lineHeight = 8;
        $maxY = 270;
        $fill = false;

        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Purchase Report (Category Wise)", $_SESSION['src_istype'], $_SESSION['srcfrom_date'], $_SESSION['srcto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(50, 7, 'Category Name', 1, 0, 'L', true);
                $pdf->Cell(60, 7, 'Product Name', 1, 0, 'L', true);
                $pdf->Cell(45, 7, 'Qty', 1, 0, 'C', true);
                $pdf->Cell(30, 7, 'Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total_price'];
            $total = $total + $row['total_price'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->MultiCell(50, 8, $row['category_name'], 1, 'L', true, 0);
            $pdf->MultiCell(60, 8, $row['product_name'], 1, 'L', true, 0);
            $pdf->MultiCell(45, 8, $row['quantity'], 1, 'C', true, 0);
            $pdf->MultiCell(30, 8, number_format($row['total_price'], 2), 1, 'R', true, 0);
            $pdf->Ln(8);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(155, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(30, 10, number_format($total, 2), 1, 1, 'R', true);

        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Sales Report (Category Wise)_$date.pdf";
        ob_end_clean();

        $pdf->Output($filename, 'I');
    }


    public function header($pdf, $page, $head, $type, $from, $to)
    {
        $company_info     = $this->company_info();
        $currency_details = $this->service_model->web_setting();

        $pageWidth = $pdf->GetPageWidth();
        $margins   = $pdf->getMargins();
        $lMargin   = $margins['left'];
        $rMargin   = $margins['right'];
        $contentW  = $pageWidth - $lMargin - $rMargin;

        $pdf->SetTextColor(30, 41, 59);
        $curY = 7;
        $lineH = 6;

        // Logo — left aligned. invoice_logo is now normally a pasted external
        // image URL (not an uploaded local file); only installs with an old
        // local path left over need the file_exists() check, since TCPDF
        // can't cheaply verify a remote URL ahead of time.
        $logoRelPath  = $currency_details[0]['invoice_logo'] ?? '';
        $isRemoteLogo = !empty($logoRelPath) && preg_match('#^https?://#i', $logoRelPath);
        $logoFile     = FCPATH . ltrim($logoRelPath, '/');
        if (!empty($logoRelPath) && ($isRemoteLogo || file_exists($logoFile))) {
            $logoW   = 28;
            $logoSrc = $isRemoteLogo ? $logoRelPath : base_url() . $logoRelPath;
            $pdf->Image($logoSrc, $lMargin, $curY, $logoW, 13, '', '', '', true, 150, '', false, false, 0, false, false, false);
        }

        // Company name
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetXY($lMargin, $curY);
        $pdf->Cell($contentW, $lineH, $company_info[0]['company_name'], 0, 1, 'C');
        $curY += $lineH;

        // Address | Mobile | Email — skip empty parts
        $parts = array_filter([
            trim($company_info[0]['address'] ?? ''),
            trim($company_info[0]['mobile']  ?? ''),
            trim($company_info[0]['email']   ?? ''),
        ]);
        if ($parts) {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($lMargin, $curY);
            $pdf->Cell($contentW, $lineH, implode('   |   ', $parts), 0, 1, 'C');
            $curY += $lineH;
        }

        // Report title
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetXY($lMargin, $curY);
        $pdf->Cell($contentW, $lineH, $head, 0, 1, 'C');
        $curY += $lineH;

        // Date range
        $pdf->SetFont('helvetica', '', 8.5);
        if (isset($type) && $type === "false") {
            $pdf->SetXY($lMargin, $curY);
            $pdf->Cell($contentW, $lineH, "From: " . $from . "   To: " . $to, 0, 1, 'C');
            $curY += $lineH;
        } elseif (!empty($from)) {
            $pdf->SetXY($lMargin, $curY);
            $pdf->Cell($contentW, $lineH, "Date: " . $from, 0, 1, 'C');
            $curY += $lineH;
        }

        $curY += 3;

        $pdf->SetXY($lMargin, $curY);

        // Reset state for table content
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->SetTextColor(30, 41, 59);
    }

    public function company_info()
    {
        $encryption_key = Config::$encryption_key;


        return  $data = $this->db->select("
     company_id,
     AES_DECRYPT(company_name, '{$encryption_key}') AS company_name,
     AES_DECRYPT(email, '{$encryption_key}') AS email,
     AES_DECRYPT(address, '{$encryption_key}') AS address,
     AES_DECRYPT(mobile, '{$encryption_key}') AS mobile,
	AES_DECRYPT(website, '{$encryption_key}') AS website,
    		AES_DECRYPT(vat_no, '{$encryption_key}') AS vat_no,
		 AES_DECRYPT(cr_no, '{$encryption_key}') AS cr_no,
     status
 ")
            ->from('company_information')
            ->where('company_id', $_SESSION['reporttype'])
            ->get()
            ->result_array();
    }


    public function stock_reportdata()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');

        $product       = $this->input->post('product');
        $product_group = (int)$this->input->post('product_group');
        $category      = $this->input->post('category');
        $store         = $this->input->post('store');
        $stocktype     = $this->input->post('stocktype');
        $batch         = $this->input->post('batch');

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

        $sqljoin = "";
        if ($category) {
            $sqljoin .= " And pi.category_id=" . $category;
        }

        if ($product) {
            $sqljoin .= " And pi.id=" . (int)$product;
        } elseif ($product_group) {
            $sqljoin .= " And pi.id IN (SELECT product FROM product_group_details WHERE pid = " . $product_group . ")";
        }

        if ($store) {
            $sqljoin .= " And sd.store=" . $store;
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $inClause = implode(',', array_map('intval', $storeids));
                $sqljoin .= " AND sd.store IN ($inClause) ";
            }
        }

        if ($batch==0) {
            $sqljoin .= " And sd.batch=" . $batch;
        } 

        if ($batch) {
            $sqljoin .= " And sd.batch=" . $batch;
        } 


        $encryption_key = Config::$encryption_key;

        // Unit Cost Logic Specification v1.1 (Phase 6) — UCL-60: this report is date-filtered, so
        // "Stock Value" must reflect the cost as at $to_date, not the batch's current cost — see
        // Report_model::unit_cost_asof_sql(). Same product-level SUM / UNION-branch placement
        // reasoning as Live Stock Report's identical query shape.
        $ucv_join_sql = $this->report_model->unit_cost_product_value_sql('ucv', $to_date);

        if ($stocktype == "all" || $stocktype == "") {
            $sql = "SELECT  id,product_name,
            unit,
            category_name,
            SUM(inqty) AS inqty,
            SUM(outqty) AS outqty,
            SUM(avqty) AS avqty,
            SUM(pinqty) AS pinqty,
            SUM(poutqty) AS poutqty,
            SUM(pavqty) AS pavqty,purchase_price, sale_price,sub,master,conversion_ratio,subpurchase_price,subsale_price,
            SUM(stock_value) AS stock_value from (SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
        SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
        ABS(SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END)) AS outqty,
        SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
            END)) AS avqty, NULL AS pinqty,NULL AS poutqty,NULL AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
        u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
        ucv.stock_value AS stock_value
        from product_information pi
        INNER JOIN product_category pc on pc.category_id=pi.category_id
        INNER JOIN stock_details sd on sd.product=pi.id
        left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
        left JOIN units u1  ON u1.unit_id = sp.unit_id
        INNER JOIN units u2 ON u2.unit_id = pi.unit
        left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
        LEFT JOIN " . $ucv_join_sql . " ON ucv.product = pi.id
        WHERE pi.status=1 and sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
        GROUP By pi.id
        UNION
        SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
         NULL AS inqty,
            NULL AS outqty,
            NULL AS avqty,
        SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
        ABS(SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END)) AS poutqty,
        SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
            END)) AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
        u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
        NULL AS stock_value
        from product_information pi
        INNER JOIN product_category pc on pc.category_id=pi.category_id
        INNER JOIN phystock_details sd on sd.product=pi.id
        left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
        left JOIN units u1  ON u1.unit_id = sp.unit_id
        INNER JOIN units u2 ON u2.unit_id = pi.unit
        left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
        WHERE pi.status=1 and sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
        GROUP By pi.id) AS stock_data
        GROUP BY id;";


            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "actualstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
 SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
            END)) AS avqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
ucv.stock_value AS stock_value
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
left JOIN units u1  ON u1.unit_id = sp.unit_id
INNER JOIN units u2 ON u2.unit_id = pi.unit
left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
LEFT JOIN " . $ucv_join_sql . " ON ucv.product = pi.id
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "physicalstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
SUM(CASE 
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0 
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') 
            ELSE 0 
        END) 
    - 
    ABS(SUM(CASE 
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0 
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') 
                ELSE 0  
            END)) AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
left JOIN units u1  ON u1.unit_id = sp.unit_id 
INNER JOIN units u2 ON u2.unit_id = pi.unit 
left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
WHERE sd.date BETWEEN '$from_date' AND '$to_date'" . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        $empid = $this->input->post('empid');
        $_SESSION['sr_istype'] =   $this->input->post('istype');
        $_SESSION['srfrom_date'] = $from_date;
        $_SESSION['srto_date'] =  $to_date;
        $_SESSION['sr_istype2'] =  $stocktype;
        $_SESSION['header'] = "Comprehensive Stock Report";




        echo json_encode($data);
    }

    public function set_stock_session()
    {
        $stock_report = json_decode($this->input->post('datas'), true) ?: [];
        $_SESSION['stock_report']    = $stock_report;
        $_SESSION['sr_stocktype']    = $this->input->post('stocktype')  ?: 'all';
        $_SESSION['sr_title']        = $this->input->post('title')      ?: 'Stock Report';
        $_SESSION['sr_from_date']    = $this->input->post('from_date')  ?: '';
        $_SESSION['sr_to_date']      = $this->input->post('to_date')    ?: '';
        echo json_encode("");
    }

    public function set_purchase_product_session()
    {
        $_SESSION['purchase_reportsrp'] = $this->input->post('datas');
        echo json_encode("");
    }

    public function set_purchase_category_session()
    {
        $_SESSION['purchase_reportprc'] = $this->input->post('datas');
        echo json_encode("");
    }

    public function set_sales_category_session()
    {
        $_SESSION['sale_reportsrc'] = $this->input->post('datas');
        echo json_encode("");
    }
    public function set_stock_session2()
    {
        $stock_report = json_decode($this->input->post('datas'), true) ?: [];
        $_SESSION['product_batch_summary_report_data'] = $stock_report;
        echo json_encode("");
    }


    public function livestock_reportdata()
    {

        $product       = $this->input->post('product');
        $product_group = (int)$this->input->post('product_group');
        $category      = $this->input->post('category');
        $store         = $this->input->post('store');
        $stocktype     = $this->input->post('stocktype');
        $batch         = $this->input->post('batch');

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

        $sqljoin = "";
        if ($category) {
            $sqljoin .= " And pi.category_id=" . $category;
        }

        if ($product) {
            $sqljoin .= " And pi.id=" . (int)$product;
        } elseif ($product_group) {
            $sqljoin .= " And pi.id IN (SELECT product FROM product_group_details WHERE pid = " . $product_group . ")";
        }

        if ($store) {
            $sqljoin .= " And sd.store=" . $store;
        } else {
            if ($this->session->userdata('user_level2') != 1 && !empty($storeids)) {
                $inClause = implode(',', array_map('intval', $storeids));
                $sqljoin .= " AND sd.store IN ($inClause) ";
            }
        }

        if ($batch==0) {
            $sqljoin .= " And sd.batch=" . $batch;
        } 

        if ($batch) {
            $sqljoin .= " And sd.batch=" . $batch;
        } 


        $encryption_key = Config::$encryption_key;

        // Unit Cost Logic Specification v1.1 (Phase 6) — Stock Value summed across all of a
        // product's batches (this report groups by product, not product+batch, so a single
        // "Unit Cost" isn't meaningful here — only the summed Stock Value is). Joined only onto
        // the Actual-stock-sourced half of the UNION below; the Physical-stock half gets a literal
        // NULL (UCL-04 — Physical stock is never part of the cost chain), and SUM() in the outer
        // query picks up whichever half is non-NULL.
        $ucv_join_sql = $this->report_model->unit_cost_product_value_sql('ucv');

        if ($stocktype == "all" || $stocktype == "") {
            $sql = "SELECT  id,product_name,
            unit,
            category_name,
            SUM(inqty) AS inqty,
            SUM(outqty) AS outqty,
            SUM(avqty) AS avqty,
            SUM(pinqty) AS pinqty,
            SUM(poutqty) AS poutqty,
            SUM(pavqty) AS pavqty,purchase_price, sale_price,sub,master,conversion_ratio,
            MAX(max_stock_level) AS max_stock_level,
            MAX(min_stock_level) AS min_stock_level,
            MAX(reorder_stock_level) AS reorder_stock_level,
            MAX(reserve_stock_level) AS reserve_stock_level,subpurchase_price,subsale_price,
            SUM(stock_value) AS stock_value
            from (SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
        SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
        SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
         SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
            END)) AS avqty, NULL AS pinqty,NULL AS poutqty,NULL AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
            u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,
            pi.max_stock_level,pi.min_stock_level,pi.reorder_stock_level,pi.reserve_stock_level,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
            ucv.stock_value AS stock_value
        from product_information pi
        INNER JOIN product_category pc on pc.category_id=pi.category_id
        INNER JOIN stock_details sd on sd.product=pi.id
        left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
        left JOIN units u1  ON u1.unit_id = sp.unit_id
        INNER JOIN units u2 ON u2.unit_id = pi.unit
        left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
        LEFT JOIN " . $ucv_join_sql . " ON ucv.product = pi.id
        WHERE pi.status=1 " . $sqljoin . "
        GROUP By pi.id
        UNION
        SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
         NULL AS inqty,
            NULL AS outqty,
            NULL AS avqty,
        SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
        SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
        SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
            END)) AS pavqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
            u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,
            pi.max_stock_level,pi.min_stock_level,pi.reorder_stock_level,pi.reserve_stock_level,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
            NULL AS stock_value
        from product_information pi
        INNER JOIN product_category pc on pc.category_id=pi.category_id
        INNER JOIN phystock_details sd on sd.product=pi.id
        left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
        left JOIN units u1  ON u1.unit_id = sp.unit_id
        INNER JOIN units u2 ON u2.unit_id = pi.unit
        left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
        WHERE pi.status=1 " . $sqljoin . "
        GROUP By pi.id) AS stock_data
        GROUP BY id;";


            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "actualstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS inqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS outqty,
   SUM(CASE
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
            ELSE 0
        END)
    -
    ABS(SUM(CASE
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')
                ELSE 0
                END)) AS avqty,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
    u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,
    pi.max_stock_level,pi.min_stock_level,pi.reorder_stock_level,pi.reserve_stock_level,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price,
ucv.stock_value AS stock_value
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN stock_details sd on sd.product=pi.id
left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
left JOIN units u1  ON u1.unit_id = sp.unit_id
INNER JOIN units u2 ON u2.unit_id = pi.unit
left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
LEFT JOIN " . $ucv_join_sql . " ON ucv.product = pi.id
WHERE 1=1 " . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }

        if ($stocktype == "physicalstock") {

            $sql = "SELECT pi.id,pi.product_name,pi.unit,pc.category_name,
SUM(CASE  WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  > 0  THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0 END) AS pinqty,
SUM(CASE WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  < 0 THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "')  ELSE 0  END) AS poutqty,
   SUM(CASE 
            WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') > 0 
            THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') 
            ELSE 0 
        END) 
    - 
    ABS(SUM(CASE 
                WHEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') < 0 
                THEN AES_DECRYPT(sd.stock, '" . $encryption_key . "') 
                ELSE 0  
            END)) AS pavqty
,AES_DECRYPT(pi.cost_price, '" . $encryption_key . "') as purchase_price,AES_DECRYPT(pi.price, '" . $encryption_key . "') as sale_price,pi.category_id,
    u1.unit_name as sub,u2.unit_name as master,cr.conversion_ratio,
    pi.max_stock_level,pi.min_stock_level,pi.reorder_stock_level,pi.reserve_stock_level,AES_DECRYPT(sp.subcost_price, '" . $encryption_key . "') as subpurchase_price,AES_DECRYPT(sp.subsell_price, '" . $encryption_key . "') as subsale_price
from product_information pi
INNER JOIN product_category pc on pc.category_id=pi.category_id
INNER JOIN phystock_details sd on sd.product=pi.id
left JOIN subunit_product sp ON sp.product_id = pi.id && sp.first=1
left JOIN units u1  ON u1.unit_id = sp.unit_id 
INNER JOIN units u2 ON u2.unit_id = pi.unit 
left JOIN conversion_ratio cr ON cr.product = pi.id && u1.unit_id=cr.subunit
WHERE sd.date " . $sqljoin . "
GROUP By pi.id";
            $query = $this->db->query($sql);
            $data  = $query->result_array();
        }
        $empid = $this->input->post('empid');
       // $_SESSION['stock_report'] =  $data;
        $_SESSION['sr_istype'] =   $this->input->post('istype');
        $_SESSION['srfrom_date'] = '';
        $_SESSION['srto_date'] =  '';
        $_SESSION['sr_istype2'] =  $stocktype;
        $_SESSION['header'] = "Live Stock Report";





        echo json_encode($data);
    }



      public function cashbook_reportdata()
    {
        $from_date      = $this->input->post('from_date');
        $to_date        = $this->input->post('to_date');
        $empid          = $this->input->post('empid');
        $payment        = $this->input->post('payment');
        $payment_nature = $this->input->post('payment_nature');
        $branch         = $this->input->post('branch');

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

        // Branch scoping: an explicit branch filter wins; otherwise non-admins
        // are restricted to their sec_branch-assigned branches; admins see all.
        $branchList = '';
        if (!$branch && $this->session->userdata('user_level2') != 1 && !empty($branchids)) {
            $branchList = implode(',', array_map('intval', $branchids));
        }

        // The full UNION ALL query (Payments/Receipts/Transfer/Sale/Purchase/
        // Purchase Return/Sales Return/Customer Receive/Supplier Payment),
        // the Credit-payment-type exclusion, and all these filters now live
        // in the GetCashbookReport stored procedure (see migration
        // 2026-10-09_cash_book_stored_procedure.sql) instead of being rebuilt
        // as a raw SQL string on every request.
        $query = $this->db->query(
            "CALL GetCashbookReport(?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $from_date,
                $to_date,
                $empid !== null ? $empid : 'All',
                $payment ? (int)$payment : 0,
                $payment_nature ? $payment_nature : '',
                $branch ? (int)$branch : 0,
                $branchList,
                Config::$encryption_key,
            ]
        );
        $data = $query ? $query->result_array() : [];

        // A CALL leaves a trailing status result on the mysqli connection;
        // flush it so the next query on this connection doesn't fail with
        // "Commands out of sync".
        while ($this->db->conn_id->more_results() && $this->db->conn_id->next_result()) {
            // discard
        }

        $_SESSION['cashbook'] =  $data;
        $_SESSION['cb_istype'] =   $this->input->post('istype');
        $_SESSION['cbfrom_date'] = $from_date;
        $_SESSION['cbto_date'] =  $to_date;

        echo json_encode($data);
    }

    public function generate_stockreport()
    {
        @ini_set('memory_limit', '512M');

        $data      = isset($_SESSION['stock_report'])  ? $_SESSION['stock_report']  : [];
        $stocktype = isset($_SESSION['sr_stocktype'])  ? $_SESSION['sr_stocktype']  : 'all';
        $title     = isset($_SESSION['sr_title'])      ? $_SESSION['sr_title']      : 'Stock Report';
        $from_date = isset($_SESSION['sr_from_date'])  ? $_SESSION['sr_from_date']  : '';
        $to_date   = isset($_SESSION['sr_to_date'])    ? $_SESSION['sr_to_date']    : '';

        if (empty($data)) {
            http_response_code(400);
            echo 'No data';
            return;
        }

        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle($title);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $hdr_type = (!empty($from_date) || !empty($to_date)) ? 'false' : '';
        $this->header($pdf, 1, $title, $hdr_type, $from_date, $to_date);

        // Column definitions based on stocktype
        // Unit Cost Logic Specification v1.1 (Phase 6, UCL-73) — the old "Purch.Price / Sale
        // Price / Purch.Val / Sale Val" columns (Fixed Purchase/Sale Price x Qty, a static,
        // never-updated valuation) are dropped in favour of "Unit Cost" + "Stock Value", both
        // from the Unit Cost engine (unit_cost table). Only actualstock/all get them —
        // Physical stock is never part of the cost chain (UCL-04), so physicalstock drops the
        // old price columns with no replacement.
        if ($stocktype === 'actualstock') {
            $cols    = [10, 40, 60, 28, 28, 28, 30, 32];
            $headers = ['Sl', 'Category', 'Product Name',
                        'In.Qty', 'Out.Qty', 'Avl.Qty',
                        'Unit Cost', 'Stock Value'];
        } elseif ($stocktype === 'physicalstock') {
            $cols    = [10, 50, 85, 44, 44, 44];
            $headers = ['Sl', 'Category', 'Product Name',
                        'In.Qty', 'Out.Qty', 'Avl.Qty'];
        } else { // all
            $cols    = [8, 30, 48, 20, 20, 22, 20, 20, 22, 27, 28];
            $headers = ['Sl', 'Category', 'Product Name',
                        'Act.In', 'Act.Out', 'Act.Avl',
                        'Phy.In', 'Phy.Out', 'Phy.Avl',
                        'Unit Cost', 'Stock Value'];
        }

        $numTrailingValueCols = ($stocktype === 'physicalstock') ? 0 : 2;
        $drawHeader = function() use ($pdf, $cols, $headers, $numTrailingValueCols) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            foreach ($headers as $i => $h) {
                $align = ($i >= count($headers) - $numTrailingValueCols) ? 'R' : (($i <= 2) ? 'L' : 'C');
                $pdf->Cell($cols[$i], 7, $h, 1, 0, $align, true);
            }
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };

        $drawHeader();

        // Draws one table row using MultiCell so long category/product names wrap
        // onto extra lines instead of being clipped, with the row height grown to fit.
        $calcRowHeight = function ($catText, $prodText) use ($pdf, $cols) {
            $lineH    = 4.2;
            $numLines = max(
                $pdf->getNumLines((string)$catText,  $cols[1]),
                $pdf->getNumLines((string)$prodText, $cols[2]),
                1
            );
            return max($numLines * $lineH, 6);
        };
        $drawRow = function (array $values, array $aligns, $rowH) use ($pdf, $cols) {
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            foreach ($values as $idx => $val) {
                $pdf->MultiCell($cols[$idx], $rowH, (string)$val, 1, $aligns[$idx], true, 0);
            }
            $pdf->SetXY($x0, $y0 + $rowH);
        };

        $fill               = false;
        $maxY               = $pdf->GetPageHeight() - 18;
        $i                  = 1;
        $grand_stock_value  = 0;

        foreach ($data as $row) {
            $avqtymain = is_numeric($row['avqtymain']) ? (float)$row['avqtymain'] : 0;
            // Unit Cost Logic Specification v1.1 (Phase 6) — blank, not 0.00, when this product
            // has never been costed (summed across its batches in the report query already).
            // Unit Cost = Stock Value / Available Qty — this report aggregates at product level
            // (not per batch, where a single batch's own Unit Cost would come straight off its
            // unit_cost row), so a single "Unit Cost" figure for a multi-batch product is this
            // weighted average; dividing by avqtymain (already in whichever unit the screen is
            // displaying, master or sub) also keeps Unit Cost consistent with the Qty columns
            // shown on the same row.
            $has_stock_value = isset($row['stock_value']) && is_numeric($row['stock_value']) && (float)$row['stock_value'] > 0;
            $stock_value     = $has_stock_value ? (float)$row['stock_value'] : 0;
            $unit_cost       = ($has_stock_value && $avqtymain > 0) ? ($stock_value / $avqtymain) : 0;
            $grand_stock_value += $stock_value;

            if ($stocktype === 'actualstock') {
                $values = [$i, $row['category_name'], $row['product_name'], $row['inqty'], $row['outqty'], $row['avqty'],
                           $has_stock_value ? number_format($unit_cost, 2) : '-',
                           $has_stock_value ? number_format($stock_value, 2) : '-'];
                $aligns = ['L', 'L', 'L', 'C', 'C', 'C', 'R', 'R'];
            } elseif ($stocktype === 'physicalstock') {
                $values = [$i, $row['category_name'], $row['product_name'], $row['pinqty'], $row['poutqty'], $row['pavqty']];
                $aligns = ['L', 'L', 'L', 'C', 'C', 'C'];
            } else { // all
                $values = [$i, $row['category_name'], $row['product_name'], $row['inqty'], $row['outqty'], $row['avqty'],
                           $row['pinqty'], $row['poutqty'], $row['pavqty'],
                           $has_stock_value ? number_format($unit_cost, 2) : '-',
                           $has_stock_value ? number_format($stock_value, 2) : '-'];
                $aligns = ['L', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C', 'R', 'R'];
            }

            $rowH = $calcRowHeight($row['category_name'], $row['product_name']);

            if ($pdf->GetY() + $rowH > $maxY) {
                $pdf->AddPage();
                $drawHeader();
                $fill = false;
            }

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            $bgR = $fill ? 248 : 255;
            $bgG = $fill ? 250 : 255;
            $bgB = $fill ? 252 : 255;
            $pdf->SetFillColor($bgR, $bgG, $bgB);

            $drawRow($values, $aligns, $rowH);

            $fill = !$fill;
            $i++;
        }

        // Total row
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);

        if ($stocktype === 'actualstock') {
            // No grand "Unit Cost" total — averaging per-row unit costs together isn't a
            // meaningful figure; only Stock Value sums sensibly.
            $sumW = $cols[0]+$cols[1]+$cols[2]+$cols[3]+$cols[4]+$cols[5]+$cols[6];
            $pdf->Cell($sumW,    7, 'TOTAL',                               1, 0, 'R', true);
            $pdf->Cell($cols[7], 7, number_format($grand_stock_value, 2), 1, 1, 'R', true);
        } elseif ($stocktype === 'physicalstock') {
            $sumW = array_sum($cols);
            $pdf->Cell($sumW, 7, 'TOTAL', 1, 1, 'R', true);
        } else { // all
            $sumW = $cols[0]+$cols[1]+$cols[2]+$cols[3]+$cols[4]+$cols[5]+$cols[6]+$cols[7]+$cols[8]+$cols[9];
            $pdf->Cell($sumW,     7, 'TOTAL',                               1, 0, 'R', true);
            $pdf->Cell($cols[10], 7, number_format($grand_stock_value, 2), 1, 1, 'R', true);
        }

        $date     = date('Y-m-d');
        $filename = str_replace(' ', '_', $title) . "_$date.pdf";
        $pdf->Output($filename, 'I');
        exit;
    }



    public function generate_livestockreport()
    {
        $this->generate_stockreport();
    }








    public function generate_cashbook()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Cash Book');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Cash Book", $_SESSION['cb_istype'], $_SESSION['cbfrom_date'], $_SESSION['cbto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(30, 7, 'Date', 1, 0, 'L', true);
        $pdf->Cell(32, 7, 'Incident', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Payment Method', 1, 0, 'C', true);
        $pdf->Cell(23, 7, 'Nature', 1, 0, 'C', true);
        $pdf->Cell(40, 7, 'Voucher No', 1, 0, 'C', true);
        $pdf->Cell(25, 7, 'Amount', 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data =  $_SESSION['cashbook'];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;

        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Cash Book", $_SESSION['cb_istype'], $_SESSION['cbfrom_date'], $_SESSION['cbto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(30, 7, 'Date', 1, 0, 'L', true);
                $pdf->Cell(32, 7, 'Incident', 1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Payment Method', 1, 0, 'C', true);
                $pdf->Cell(23, 7, 'Nature', 1, 0, 'C', true);
                $pdf->Cell(40, 7, 'Voucher No', 1, 0, 'C', true);
                $pdf->Cell(25, 7, 'Amount', 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['grandTotal'];
            $total = $total + $row['grandTotal'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(30, 8, $row['date'], 1, 0, 'L', true);
            $pdf->Cell(32, 8, $row['incidenttype'], 1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['payment_method'], 1, 0, 'C', true);
            $pdf->Cell(23, 8, $row['payment_method_nature'], 1, 0, 'C', true);
            $pdf->Cell(40, 8, $row['invoice_no'], 1, 0, 'C', true);
            $pdf->Cell(25, 8, number_format($row['grandTotal'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50, 7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(110, 7, "", 1, 0, 'L', true);
        $pdf->Cell(25, 7, number_format($total, 2), 1, 1, 'R', true);

        $pdf->updatePageTotal($patotal);



        $date = date('Y-m-d');
        $filename = "Cash Book_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function bdtask_stock_audit_report()
    {
        $encryption_key = Config::$encryption_key;
        $_SESSION['reporttype'] =   1;

        if (!$this->permission1->method('stock_audit_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $product_list =$this->report_model->product_list_stock();

        $store_list = $this->report_model->store_list();


        $data = array(
            'title'          => display('stock_audit_report'),
            'product_list'   => $product_list,
            'store_list'     => $store_list,


        );
        $data['module']   = "report";
        $data['page']     = "stock_audit_report";
        echo modules::run('template/layout', $data);
    }


    public function audit_stock_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $productid = $this->input->post('productid');
        $encryption_key = Config::$encryption_key;
        $storeid  = $this->input->post('storeid');
        $incident = $this->input->post('incident');
        $scenario = $this->input->post('scenario');

        $this->db->select("
                as.product,
                as.date,
                as.scenario,
                as.incident,
                AES_DECRYPT(as.pvoucher, '$encryption_key') AS pvoucher,
                AES_DECRYPT(as.voucher, '$encryption_key') AS voucher,
                as.pid,
                as.store,
                pi.product_name,
                AES_DECRYPT(as.astockstr, '$encryption_key') AS astockstr,
                AES_DECRYPT(as.pstockstr, '$encryption_key') AS pstockstr,
                AES_DECRYPT(as.astock, '$encryption_key') AS astock,
                AES_DECRYPT(as.pstock, '$encryption_key') AS pstock,
                s.name as storename,
                as.batch,
                CASE WHEN as.batch = 0 THEN '' ELSE CAST(AES_DECRYPT(sb.batchid, '$encryption_key') AS CHAR) END AS batch_name,
                as.lastupdateddate,cr.conversion_ratio,u1.unit_name as sub,u2.unit_name as master
        ", false);
        $this->db->from('audit_stock as');
        $this->db->join('product_information pi', 'pi.id = as.product', 'inner');
        $this->db->join('store s', 's.id = as.store', 'inner');
        $this->db->join('stockbatch sb', 'sb.id = as.batch', 'left');
        $this->db->join('subunit_product sp', 'sp.product_id = pi.id AND sp.first = 1', 'left');
        $this->db->join('units u1', 'u1.unit_id = sp.unit_id', 'left');
        $this->db->join('units u2', 'u2.unit_id = pi.unit', 'inner');
        $this->db->join('conversion_ratio cr', 'cr.product = pi.id AND u1.unit_id = cr.subunit', 'left');

        if (!empty($from_date)) {
            $this->db->where('as.date >=', $from_date);
        }
        
        if (!empty($to_date)) {
            $this->db->where('as.date <=', $to_date);
        }
        
        if (!empty($productid)) {
            $this->db->where('as.product', $productid);
        }
        
        if (!empty($storeid)) {
            $this->db->where('as.store', $storeid);
        }
        
        if (!empty($incident)) {
            $this->db->where('as.incident', $incident);
        }
        
        if (!empty($scenario)) {
            $sce="";
            if($scenario=="purchaseinvoice"){
                $sce="purchase";
            }else if($scenario=="saleinvoice"){
                $sce="sale";
            }else if($scenario=="stock"){
                $sce="Inventory Transaction";
            }else if($scenario=="GRN"){
                $sce="GRN";
            }else if($scenario=="GDN"){
                $sce="GDN";
            }else if($scenario=="purchasereturn"){
                $sce="Purchase Return";
            }
            $this->db->where('as.scenario', $sce);
        }
        $this->db->order_by("as.lastupdateddate", "asc");



        $query = $this->db->get();
        $result1 = $query->result_array();


        $this->db->select("
               u.unit_name,u.unit_id
        ", false);
        $this->db->from('product_information pi');
        $this->db->join('units u', 'u.unit_id = pi.unit', 'inner');
        $this->db->where('pi.id', $productid);

        $query = $this->db->get();
        $result2 = $query->result_array();

        $this->db->select("
                u.unit_name,u.unit_id,cr.conversion_ratio
        ", false);
        $this->db->from('subunit_product sp');
        $this->db->join('units u', 'u.unit_id = sp.unit_id', 'inner');
        $this->db->join('conversion_ratio cr', 'cr.product = sp.product_id AND u.unit_id = cr.subunit', 'left');
        $this->db->where('sp.product_id', $productid);
        $query = $this->db->get();
        $result3 = $query->result_array();


        echo json_encode([
            'audit_stock' => $result1,
            'masterunit'     => $result2,
            'subunit'     => $result3
        ]);
    }

    public function audit_stock_report_sync()
    {
        $encryption_key = Config::$encryption_key;

        $this->db->query("CALL sp_insert_stock_audit(?)", [
            $encryption_key
        ]);
        echo json_encode("Success");
    }

    public function set_audit_session()
    {
        $_SESSION['audit_report'] = [
            'aulist'          => $this->input->post('aulist'),
            'masterunit'      => $this->input->post('masterunit'),
            'subunit'         => $this->input->post('subunit'),
            'fdate'           => $this->input->post('fdate'),
            'tdate'           => $this->input->post('tdate'),
            'astock_totalstr' => $this->input->post('astock_totalstr'),
            'pstock_totalstr' => $this->input->post('pstock_totalstr'),
        ];
        echo json_encode('');
    }

    public function generate_auditreport()
    {
        @ini_set('memory_limit', '512M');

        $sess = isset($_SESSION['audit_report']) ? $_SESSION['audit_report'] : [];

        $data       = json_decode(isset($sess['aulist'])     ? $sess['aulist']     : '[]', true) ?? [];
        $masterunit = json_decode(isset($sess['masterunit']) ? $sess['masterunit'] : '[]', true) ?? [];
        $subunit    = json_decode(isset($sess['subunit'])    ? $sess['subunit']    : '[]', true) ?? [];

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No audit data received']);
            return;
        }

        $fdate           = isset($sess['fdate'])           ? $sess['fdate']           : '';
        $tdate           = isset($sess['tdate'])           ? $sess['tdate']           : '';
        $astock_totalstr = isset($sess['astock_totalstr']) ? $sess['astock_totalstr'] : '';
        $pstock_totalstr = isset($sess['pstock_totalstr']) ? $sess['pstock_totalstr'] : '';

        $incident_map = [
            'localpurchase'         => 'Local Purchase',
            'internationalpurchase' => 'International Purchase',
            'sale'                  => 'Sale',
            'wholesale'             => 'Whole Sale',
            'openingstock'          => 'Opening Stock',
            'opening_stock'         => 'Opening Stock',
            'storetransfer'         => 'Store Transfer',
            'stockdisposal'         => 'Stock Disposal',
            'stockadjustment'       => 'Stock Adjustment',
            'purchase'              => 'Purchase',
            'salesreturn'           => 'Sales Return',
            'Sales Return'          => 'Sales Return',
            'purchasereturn'        => 'Purchase Return',
        ];

        // --- TCPDF setup (A4 Landscape) ---
        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Stock Audit Report');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        // Company header
        $this->header($pdf, 1, 'Stock Audit Report', 'false', $fdate, $tdate);

        // Product / unit info block
        $product_name    = isset($data[0]['product_name'])      ? $data[0]['product_name']      : '';
        $master_unitname = isset($masterunit[0]['unit_name'])   ? $masterunit[0]['unit_name']   : '';

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(30, 41, 59);
        $pdf->Cell(0, 6, 'Product: ' . $product_name, 0, 1, 'L');
        $pdf->Cell(0, 5, 'Master Stock Unit: ' . $master_unitname, 0, 1, 'L');
        foreach ($subunit as $su) {
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell(0, 4, 'Sub Unit: ' . ($su['unit_name'] ?? '') . '   |   Conversion Ratio: ' . ($su['conversion_ratio'] ?? ''), 0, 1, 'L');
        }
        $pdf->Ln(3);

        // Column widths (total = 277mm for A4 landscape with 10mm margins each side)
        $cols    = [20, 30, 28, 22, 22, 20, 20, 28, 28, 29, 30];
        $headers = ['Date', 'Scenario', 'Incident', 'Parent Voucher', 'Voucher No', 'Store', 'Batch',
                    'Actual Stock', 'Physical Stock', 'Actual Stock (Std.)', 'Physical Stock (Std.)'];

        // Helper: draw table header row
        $drawHeader = function() use ($pdf, $cols, $headers) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            foreach ($headers as $i => $h) {
                $align = ($i >= 9) ? 'R' : (($i >= 7) ? 'C' : 'L');
                $pdf->Cell($cols[$i], 7, $h, 1, 0, $align, true);
            }
            $pdf->Ln();
            $pdf->SetTextColor(30, 41, 59);
        };

        $drawHeader();

        // Data rows
        $stocktotal    = 0;
        $phystocktotal = 0;
        $fill          = false;
        $lh            = 6;
        $maxY          = $pdf->GetPageHeight() - 18;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lh > $maxY) {
                $pdf->AddPage();
                $drawHeader();
            }

            $scenario = $row['scenario'] ?? '';
            if ($scenario === 'purchase') $scenario = 'Purchase Invoice';
            if ($scenario === 'sale')     $scenario = 'Sale Invoice';

            $incident = $incident_map[$row['incident'] ?? ''] ?? ($row['incident'] ?? '');
            $astock   = ($row['astock'] !== null && $row['astock'] !== '') ? (float)$row['astock'] : 0;
            $pstock   = ($row['pstock'] !== null && $row['pstock'] !== '') ? (float)$row['pstock'] : 0;
            $stocktotal    += $astock;
            $phystocktotal += $pstock;

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) {
                $pdf->SetFillColor(248, 250, 252);
            } else {
                $pdf->SetFillColor(255, 255, 255);
            }
            $pdf->Cell($cols[0], $lh, $row['date']      ?? '', 1, 0, 'L', true);
            $pdf->Cell($cols[1], $lh, $scenario,           1, 0, 'L', true);
            $pdf->Cell($cols[2], $lh, $incident,            1, 0, 'L', true);
            $pdf->Cell($cols[3], $lh, $row['pvoucher']  ?? '', 1, 0, 'L', true);
            $pdf->Cell($cols[4], $lh, $row['voucher']   ?? '', 1, 0, 'L', true);
            $pdf->Cell($cols[5], $lh, $row['storename'] ?? '', 1, 0, 'L', true);
            $pdf->Cell($cols[6], $lh, $this->fitPdfText($pdf, $row['batch_name'] ?? '', $cols[6]), 1, 0, 'L', true);
            $pdf->Cell($cols[7], $lh, $row['astockstr'] ?? '', 1, 0, 'C', true);
            $pdf->Cell($cols[8], $lh, $row['pstockstr'] ?? '', 1, 0, 'C', true);
            $pdf->Cell($cols[9], $lh, number_format($astock, 2), 1, 0, 'R', true);
            $pdf->Cell($cols[10], $lh, number_format($pstock, 2), 1, 1, 'R', true);
            $fill = !$fill;
        }

        // Total row
        $sumW = $cols[0] + $cols[1] + $cols[2] + $cols[3] + $cols[4] + $cols[5] + $cols[6];
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell($sumW,    7, 'Available Quantity', 1, 0, 'L', true);
        $pdf->Cell($cols[7], 7, $astock_totalstr,     1, 0, 'C', true);
        $pdf->Cell($cols[8], 7, $pstock_totalstr,     1, 0, 'C', true);
        $pdf->Cell($cols[9], 7, number_format($stocktotal,    2), 1, 0, 'R', true);
        $pdf->Cell($cols[10], 7, number_format($phystocktotal, 2), 1, 1, 'R', true);

        $filename = 'Stock_Audit_Report_' . $fdate . '_To_' . $tdate . '.pdf';
        $pdf->Output($filename, 'I');
        exit;
    }




    public function bdtask_sales_order_report()
    { 
        $data['title']      = display('sales_order_report');
        $data['module']     = "report";
        $data['page']       = "sales_order_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_sales_return_report()
    { 
        $data['title']      = display('sales_return_report');
        $data['module']     = "report";
        $data['page']       = "sales_return_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_order_report()
    { 
        $data['title']      = display('purchase_order_report');
        $data['module']     = "report";
        $data['page']       = "purchase_order_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_purchase_return_report()
    { 
        $data['title']      = display('purchase_return_report');
        $data['module']     = "report";
        $data['page']       = "purchase_return_report";
         $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_grn_report()
    { 
        $data['title']      = display('grn_report');
        $data['module']     = "report";
        $data['page']       = "grn_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_gdn_report()
    { 
        $data['title']      = display('gdn_report');
        $data['module']     = "report";
        $data['page']       = "gdn_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_gross_profit_report()
    { 
        $data['title']      = display('gross_profit_report');
        $data['module']     = "report";
        $data['page']       = "gross_profit_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function bdtask_gross_profit_category_report()
    { 
        $data['title']      = display('gross_profit_category_report');
        $data['product_list']  = $this->report_model->product_list();
        $data['category_list']  = $this->report_model->category_list_product();
        $data['module']     = "report";
        $data['page']       = "gross_profit_category_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function getCustomersForReport()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select("customer_id, AES_DECRYPT(customer_name, '{$encryption_key}') AS customer_name");
        $this->db->from('customer_information');
        $this->db->order_by('customer_name', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function getSuppliersForReport()
    {
        $encryption_key = Config::$encryption_key;
        $this->db->select("supplier_id, AES_DECRYPT(supplier_name, '{$encryption_key}') AS supplier_name");
        $this->db->from('supplier_information');
        $this->db->order_by('supplier_name', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        echo json_encode($result);
    }

    public function purchase_return_reportinvoicewise()
    {
        $from_date   = $this->input->post('from_date');
        $to_date     = $this->input->post('to_date');
        $empid       = $this->input->post('empid');
        $branch      = $this->input->post('branch');
        $supplier_id = $this->input->post('supplier_id');

        $report_data = $this->report_model->purchase_return_reportinvoicewise($from_date, $to_date, $empid, $branch, $supplier_id);
        $_SESSION['purchase_return_reportspri'] = $report_data;
        $_SESSION['prri_istype']                = $this->input->post('istype');
        $_SESSION['prrifrom_date']              = $from_date;
        $_SESSION['prrito_date']                = $to_date;

        echo json_encode($_SESSION['purchase_return_reportspri']);
    }

    public function purchase_order_reportinvoicewise()
    {
        $from_date   = $this->input->post('from_date');
        $to_date     = $this->input->post('to_date');
        $empid       = $this->input->post('empid');
        $branch      = $this->input->post('branch');
        $supplier_id   = $this->input->post('supplier_id');
        $status        = $this->input->post('status');
        $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->purchase_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $supplier_id, $status, $incident_type);
        $_SESSION['purchase_order_reportspori'] = $report_data;
        $_SESSION['pori_istype']                = $this->input->post('istype');
        $_SESSION['porifrom_date']              = $from_date;
        $_SESSION['porito_date']                = $to_date;
        $_SESSION['pori_status']                = $status;
        $_SESSION['pori_incident_type']         = $incident_type;

        echo json_encode($_SESSION['purchase_order_reportspori']);
    }

    public function sales_order_reportinvoicewise()
    {
        $from_date   = $this->input->post('from_date');
        $to_date     = $this->input->post('to_date');
        $empid       = $this->input->post('empid');
        $branch      = $this->input->post('branch');
        $customer_id = $this->input->post('customer_id');
        $status      = $this->input->post('status');
        $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->sales_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id, $status, $incident_type);
        $_SESSION['sale_order_reportsori'] = $report_data;
        $_SESSION['sori_istype']           = $this->input->post('istype');
        $_SESSION['sorifrom_date']         = $from_date;
        $_SESSION['sorito_date']           = $to_date;
        $_SESSION['sori_status']           = $status;
        $_SESSION['sori_incident_type']    = $incident_type;

        echo json_encode($_SESSION['sale_order_reportsori']);
    }

    public function sales_return_reportinvoicewise()
    {
        try {
            $from_date   = $this->input->post('from_date');
            $to_date     = $this->input->post('to_date');
            $empid       = $this->input->post('empid');
            $branch      = $this->input->post('branch');
            $customer_id = $this->input->post('customer_id');

            $report_data = $this->report_model->sales_return_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id);
            $_SESSION['sale_return_reportsrri'] = $report_data;
            $_SESSION['srri_istype']            = $this->input->post('istype');
            $_SESSION['srrifrom_date']          = $from_date;
            $_SESSION['srrito_date']            = $to_date;

            echo json_encode($_SESSION['sale_return_reportsrri']);
        } catch (Throwable $e) {
            http_response_code(200);
            echo json_encode(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
        }
    }

    public function generate_salesreturnreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Return Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Return Report", $_SESSION['srri_istype'], $_SESSION['srrifrom_date'], $_SESSION['srrito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(35, 7, 'Return Date',   1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Return ID',     1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Invoice ID',    1, 0, 'L', true);
        $pdf->Cell(55, 7, 'Customer Name', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Amount',        1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data       = isset($_SESSION['sale_return_reportsrri']) ? $_SESSION['sale_return_reportsrri'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Return Report", $_SESSION['srri_istype'], $_SESSION['srrifrom_date'], $_SESSION['srrito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(35, 7, 'Return Date',   1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Return ID',     1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Invoice ID',    1, 0, 'L', true);
                $pdf->Cell(55, 7, 'Customer Name', 1, 0, 'L', true);
                $pdf->Cell(25, 7, 'Amount',        1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $total   += (float)$row['total'];
            $patotal += (float)$row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(35, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['return_id'],     1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(55, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(25, 8, number_format((float)$row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50,  7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(100, 7, "",              1, 0, 'L', true);
        $pdf->Cell(35,  7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date     = date('Y-m-d');
        $filename = "Sales Return Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_salesorderreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Sales Order Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Sales Order Report", $_SESSION['sori_istype'], $_SESSION['sorifrom_date'], $_SESSION['sorito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(45, 7, 'Order Date',    1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Order No',      1, 0, 'L', true);
        $pdf->Cell(45, 7, 'Customer Name', 1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Status',        1, 0, 'L', true);
        $pdf->Cell(25, 7, 'Amount',        1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data       = isset($_SESSION['sale_order_reportsori']) ? $_SESSION['sale_order_reportsori'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Sales Order Report", $_SESSION['sori_istype'], $_SESSION['sorifrom_date'], $_SESSION['sorito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(45, 7, 'Order Date',    1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Order No',      1, 0, 'L', true);
                $pdf->Cell(45, 7, 'Customer Name', 1, 0, 'L', true);
                $pdf->Cell(25, 7, 'Status',        1, 0, 'L', true);
                $pdf->Cell(25, 7, 'Amount',        1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $total   += $row['total'];
            $patotal += $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(45, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(40, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(45, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(25, 8, $row['status_label'],  1, 0, 'L', true);
            $pdf->Cell(25, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50,  7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(100, 7, "",              1, 0, 'L', true);
        $pdf->Cell(35,  7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date     = date('Y-m-d');
        $filename = "Sales Order Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function gdn_reportinvoicewise()
    {
        $from_date     = $this->input->post('from_date');
        $to_date       = $this->input->post('to_date');
        $empid         = $this->input->post('empid');
        $store         = $this->input->post('store');
        $incident_type = $this->input->post('incident_type');
        $customer_id   = $this->input->post('customer_id');

        $report_data = $this->report_model->gdn_reportinvoicewise($from_date, $to_date, $empid, $store, $incident_type, $customer_id);
        
        // Debug: Return query and params if no data
        if (!$report_data) {
            $debug = [
                'error' => 'No data found',
                'params' => [
                    'from_date' => $from_date,
                    'to_date' => $to_date,
                    'empid' => $empid,
                    'store' => $store,
                    'incident_type' => $incident_type,
                    'customer_id' => $customer_id
                ],
                'last_query' => $this->db->last_query()
            ];
            echo json_encode($debug);
            return;
        }
        
        $_SESSION['gdn_reportgri']  = $report_data;
        $_SESSION['gri_istype']     = $this->input->post('istype');
        $_SESSION['grifrom_date']   = $from_date;
        $_SESSION['grito_date']     = $to_date;

        echo json_encode($_SESSION['gdn_reportgri']);
    }

    public function grn_reportinvoicewise()
    {
        $from_date     = $this->input->post('from_date');
        $to_date       = $this->input->post('to_date');
        $empid         = $this->input->post('empid');
        $store         = $this->input->post('store');
        $incident_type = $this->input->post('incident_type');
        $supplier_id   = $this->input->post('supplier_id');

        $report_data = $this->report_model->grn_reportinvoicewise($from_date, $to_date, $empid, $store, $incident_type, $supplier_id);

        $_SESSION['grn_reportgrri'] = $report_data;
        $_SESSION['grri_istype']    = $this->input->post('istype');
        $_SESSION['grrifrom_date']  = $from_date;
        $_SESSION['grrito_date']    = $to_date;

        echo json_encode($_SESSION['grn_reportgrri']);
    }

    public function generate_grnreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->showPageTotal = false;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('GRN Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "GRN Report", $_SESSION['grri_istype'], $_SESSION['grrifrom_date'], $_SESSION['grrito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(28, 7, 'GRN Date',      1, 0, 'L', true);
        $pdf->Cell(25, 7, 'GRN ID',        1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Voucher No',    1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Store',         1, 0, 'L', true);
        $pdf->Cell(32, 7, 'Incident Type', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Supplier',      1, 0, 'L', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data       = isset($_SESSION['grn_reportgrri']) ? $_SESSION['grn_reportgrri'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY       = 270;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "GRN Report", $_SESSION['grri_istype'], $_SESSION['grrifrom_date'], $_SESSION['grrito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(28, 7, 'GRN Date',      1, 0, 'L', true);
                $pdf->Cell(25, 7, 'GRN ID',        1, 0, 'L', true);
                $pdf->Cell(30, 7, 'Voucher No',    1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Store',         1, 0, 'L', true);
                $pdf->Cell(32, 7, 'Incident Type', 1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Supplier',      1, 0, 'L', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(28, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(25, 8, $row['grn_id'],        1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['voucherno'],     1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['store'],         1, 0, 'L', true);
            $pdf->Cell(32, 8, $row['incidenttype'],  1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['supplier_name'], 1, 1, 'L', true);
            $fill = !$fill;
        }

        $date     = date('Y-m-d');
        $filename = "GRN Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function generate_gdnreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->showPageTotal = false;
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('GDN Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "GDN Report", $_SESSION['gri_istype'], $_SESSION['grifrom_date'], $_SESSION['grito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(28, 7, 'GDN Date',      1, 0, 'L', true);
        $pdf->Cell(25, 7, 'GDN ID',        1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Voucher No',    1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Store',         1, 0, 'L', true);
        $pdf->Cell(32, 7, 'Incident Type', 1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Customer',      1, 0, 'L', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data       = isset($_SESSION['gdn_reportgri']) ? $_SESSION['gdn_reportgri'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY       = 270;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "GDN Report", $_SESSION['gri_istype'], $_SESSION['grifrom_date'], $_SESSION['grito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(28, 7, 'GDN Date',      1, 0, 'L', true);
                $pdf->Cell(25, 7, 'GDN ID',        1, 0, 'L', true);
                $pdf->Cell(30, 7, 'Voucher No',    1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Store',         1, 0, 'L', true);
                $pdf->Cell(32, 7, 'Incident Type', 1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Customer',      1, 0, 'L', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(28, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(25, 8, $row['gdn_id'],        1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['voucherno'],     1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['store'],         1, 0, 'L', true);
            $pdf->Cell(32, 8, $row['incidenttype'],  1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['customer_name'], 1, 1, 'L', true);
            $fill = !$fill;
        }

        $date     = date('Y-m-d');
        $filename = "GDN Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

     public function generate_purchaseorderreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Order Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Order Report", $_SESSION['pori_istype'], $_SESSION['porifrom_date'], $_SESSION['porito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(35, 7, 'Order Date',    1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Order No',      1, 0, 'L', true);
        $pdf->Cell(50, 7, 'Supplier Name', 1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Status',        1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Amount',        1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['purchase_order_reportspori']) ? $_SESSION['purchase_order_reportspori'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;

        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Purchase Order Report", $_SESSION['pori_istype'], $_SESSION['porifrom_date'], $_SESSION['porito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(35, 7, 'Order Date',    1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Order No',      1, 0, 'L', true);
                $pdf->Cell(50, 7, 'Supplier Name', 1, 0, 'L', true);
                $pdf->Cell(30, 7, 'Status',        1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Amount',        1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(35, 8, $row['date'],         1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['invoiceno'],    1, 0, 'L', true);
            $pdf->Cell(50, 8, $row['supplier_name'],1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['status_label'], 1, 0, 'L', true);
            $pdf->Cell(35, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(35, 7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(115, 7, "", 1, 0, 'L', true);
        $pdf->Cell(35, 7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Purchase Order Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    private function renderPurchaseReturnReportHeaderRow($pdf)
    {
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(32, 7, 'Return Date', 1, 0, 'L', true);
        $pdf->Cell(33, 7, 'Return ID',   1, 0, 'L', true);
        $pdf->Cell(33, 7, 'Invoice ID',  1, 0, 'L', true);
        $pdf->Cell(57, 7, 'Supplier',    1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Amount',      1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);
    }

    private function fitPdfText($pdf, $text, $width)
    {
        $value = (string) $text;
        if ($pdf->GetStringWidth($value) <= $width) {
            return $value;
        }

        $suffix = '...';
        while ($value !== '' && $pdf->GetStringWidth($value . $suffix) > $width) {
            $value = substr($value, 0, -1);
        }

        return rtrim($value) . $suffix;
    }

    public function generate_purchasereturnreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Return Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Return Report", $_SESSION['prri_istype'], $_SESSION['prrifrom_date'], $_SESSION['prrito_date']);

        $this->renderPurchaseReturnReportHeaderRow($pdf);

        $data       = isset($_SESSION['purchase_return_reportspri']) ? $_SESSION['purchase_return_reportspri'] : [];
        $lineHeight = 8;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Purchase Return Report", $_SESSION['prri_istype'], $_SESSION['prrifrom_date'], $_SESSION['prrito_date']);
                $this->renderPurchaseReturnReportHeaderRow($pdf);
            }

            $amount   = (float) $row['amount'];
            $total   += $amount;
            $patotal += $amount;

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(32, 8, $this->fitPdfText($pdf, $row['return_date'], 30), 1, 0, 'L', true);
            $pdf->Cell(33, 8, $this->fitPdfText($pdf, $row['return_id'], 31), 1, 0, 'L', true);
            $pdf->Cell(33, 8, $this->fitPdfText($pdf, $row['invoice_id'], 31), 1, 0, 'L', true);
            $pdf->Cell(57, 8, $this->fitPdfText($pdf, $row['supplier_name'], 55), 1, 0, 'L', true);
            $pdf->Cell(30, 8, number_format($amount, 2), 1, 1, 'R', true);
            $fill = !$fill;
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(155, 10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(30,  10, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date     = date('Y-m-d');
        $filename = "Purchase Return Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function bdtask_todays_service_report()
    {
        // $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!$this->permission1->method('service_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('service_report'),
            // 'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "servicereport_invoicewise";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function service_reportinvoicewise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');
        $customer_id = $this->input->post('customer_id');

        $report_data = $this->report_model->service_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id);
        $_SESSION['service_reportsri'] =  $report_data;
        $_SESSION['seri_istype'] =   $this->input->post('istype');
        $_SESSION['serifrom_date'] = $from_date;
        $_SESSION['serito_date'] =  $to_date;


        echo json_encode($_SESSION['service_reportsri']);
    }
    public function generate_servicereportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Service Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Service Report", $_SESSION['seri_istype'], $_SESSION['serifrom_date'], $_SESSION['serito_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(30, 7, 'Service Date',  1, 0, 'L', true);
        $pdf->Cell(30, 7, 'EOD Date',      1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Invoice No',    1, 0, 'L', true);
        $pdf->Cell(50, 7, 'Customer Name', 1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Amount',        1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['service_reportsri']) ? $_SESSION['service_reportsri'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;

        $patotal = 0;
        $total = 0;
        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Service Report", $_SESSION['sri_istype'], $_SESSION['srifrom_date'], $_SESSION['srito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(30, 7, 'Service Date',  1, 0, 'L', true);
                $pdf->Cell(30, 7, 'EOD Date',      1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Invoice No',    1, 0, 'L', true);
                $pdf->Cell(50, 7, 'Customer Name', 1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Amount',        1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $total = $total + $row['total'];
            $patotal = $patotal + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(30, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['eod_date'],      1, 0, 'L', true);
            $pdf->Cell(40, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(50, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(40, 8, number_format($row['total'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(40,  7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(110, 7, "", 1, 0, 'L', true);
        $pdf->Cell(40,  7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Service Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function gross_profit_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $encryption_key = Config::$encryption_key;

        $empid   = ($empid === '' || $empid === 'null') ? null : $empid;

        // $report_data = $this->report_model->sales_reportinvoicewise($from_date, $to_date, $empid, $encryption_key);
        $query = $this->db->query("CALL GetGrossProfitReport(?, ?, ?, ?)", [
            $from_date,
            $to_date,
            $encryption_key,
            $empid
        ]);

        if ($query->num_rows() > 0) {
            $report_data = $query->result_array();
        } else {
            $report_data = [];
        }
        $_SESSION['gross_profit_report_data'] =  $report_data;
        $_SESSION['gpr_istype'] =   $this->input->post('istype');
        $_SESSION['gprfrom_date'] = $from_date;
        $_SESSION['gprto_date'] =  $to_date;


        echo json_encode($_SESSION['gross_profit_report_data']);
    }

  
    public function generate_gross_profit_report()
    {
        @ini_set('memory_limit', '512M');

        $data      = isset($_SESSION['gross_profit_report_data']) ? $_SESSION['gross_profit_report_data'] : [];
        $from_date = isset($_SESSION['gprfrom_date']) ? $_SESSION['gprfrom_date'] : '';
        $to_date   = isset($_SESSION['gprto_date'])   ? $_SESSION['gprto_date']   : '';
        $istype    = isset($_SESSION['gpr_istype'])   ? $_SESSION['gpr_istype']   : '';

        $pdf = new StockReport('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Gross Profit Report');
        $pdf->SetMargins(15, 10, 15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $this->header($pdf, 1, 'Gross Profit Report', $istype, $from_date, $to_date);

        // Column widths (A4 portrait 210 - 15 - 15 = 180mm)
        $cW = 100; // detail label
        $cS = 40;  // sub-amount
        $cT = 40;  // total amount
        $lh = 8;

        // ── Table column header ──
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell($cW, $lh, 'Detail',  1, 0, 'L', true);
        $pdf->Cell($cS, $lh, 'Amount',  1, 0, 'R', true);
        $pdf->Cell($cT, $lh, 'Amount',  1, 1, 'R', true);
        $pdf->SetTextColor(30, 41, 59);

        // Unit Cost Logic Specification v1.1 (Phase 6, UCL-75/UCL-76) — looked up by 'type' now
        // rather than positional index, since the procedure's row set changed shape (Opening
        // Stock / Purchase of Product / Closing Stock replaced by the COGS breakdown below).
        $by_type = [];
        foreach ($data as $row) {
            $by_type[$row['type']] = is_numeric($row['grandtotal']) ? (float) $row['grandtotal'] : 0.0;
        }
        $g = function ($type) use ($by_type) { return $by_type[$type] ?? 0.0; };

        $sop           = $g('sale') - $g('sales_return');
        $revenue       = $sop + $g('service');
        $cogs_tracked  = $g('cogs_tracked') - $g('cogs_tracked_return');
        $cogs_disabled = $g('cogs_disabled') - $g('cogs_disabled_return');
        $cos           = $cogs_tracked + $cogs_disabled;
        $final         = $revenue - $cos;

        // ── Revenue rows ──
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetDrawColor(203, 213, 225);
        $pdf->SetFillColor(248, 250, 252);
        $pdf->Cell($cW, $lh, 'Sale of Product', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, '', 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, number_format($sop, 2), 1, 1, 'R', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell($cW, $lh, 'Sale of Service', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, '', 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, number_format($g('service'), 2), 1, 1, 'R', true);

        // Total Revenue (light slate highlight)
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(241, 245, 249);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->Cell($cW, $lh, 'Total Revenue', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, '', 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, number_format($revenue, 2), 1, 1, 'R', true);

        // ── Cost of Sale section header ──
        // UCL-75: Cost of Goods Sold = the cost actually consumed by sales in the period (each
        // sale line's stored moving-average cost), not what was purchased or held in stock.
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->Cell($cW + $cS + $cT, $lh, 'Cost Of Sale (Cost of Goods Sold)', 1, 1, 'L', true);
        $pdf->SetTextColor(30, 41, 59);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetDrawColor(203, 213, 225);
        $pdf->SetFillColor(248, 250, 252);
        $pdf->Cell($cW, $lh, '  Stock-Tracked Products', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, number_format($cogs_tracked, 2), 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, '', 1, 1, 'R', true);

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Cell($cW, $lh, '  Stock=Disable Products (Fixed Price x Qty)', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, number_format($cogs_disabled, 2), 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, '', 1, 1, 'R', true);

        $pdf->SetFillColor(248, 250, 252);
        $pdf->Cell($cW, $lh, '  Total Cost Of Sale', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, '', 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, number_format($cos, 2), 1, 1, 'R', true);

        // ── Gross Profit row (green) ──
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell($cW, $lh, 'Gross Profit', 1, 0, 'L', true);
        $pdf->Cell($cS, $lh, '', 1, 0, 'R', true);
        $pdf->Cell($cT, $lh, number_format($final, 2), 1, 1, 'R', true);

        $date     = date('Y-m-d');
        $filename = "Gross_Profit_Report_$date.pdf";
        $pdf->Output($filename, 'I');
        exit;
    }
    public function gross_profit_categorywise_report()
    {
        $from_date = $this->input->post('from_date');
        $to_date   = $this->input->post('to_date');
        $empid     = $this->input->post('empid');
        $category  = $this->input->post('category');
        $product   = $this->input->post('product');
        $istype    = $this->input->post('istype');
    
        $encryption_key = Config::$encryption_key;
    
        $empid   = ($empid === '' || $empid === 'null') ? null : $empid;
        $category = ($category === '' || $category === 'null') ? null : $category;
        $product  = ($product === '' || $product === 'null') ? null : $product;
    
        $query = $this->db->query("CALL GrossProfitReportCategorywise(?,?,?,?,?,?)", [
            $from_date,
            $to_date,
            $empid,
            $encryption_key,
            $product,
            $category
        ]);
    
        $report_data = [];
    
        if ($query) {
            $report_data = $query->result_array();
        }
    
        $_SESSION['gross_profit_report_category_data'] = $report_data;
        $_SESSION['gprc_istype']   = $istype;
        $_SESSION['gprcfrom_date'] = $from_date;
        $_SESSION['gprcto_date']   = $to_date;
    
        echo json_encode($report_data);
    }

    public function generate_grossprofitreportcategorywise()
    {
        @ini_set('memory_limit', '512M');

        $data      = $_SESSION['gross_profit_report_category_data'] ?? [];
        $from_date = isset($_SESSION['gprcfrom_date']) ? $_SESSION['gprcfrom_date'] : '';
        $to_date   = isset($_SESSION['gprcto_date'])   ? $_SESSION['gprcto_date']   : '';
        $istype    = isset($_SESSION['gprc_istype'])   ? $_SESSION['gprc_istype']   : '';

        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Gross Profit Report (Category Wise)');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $this->header($pdf, 1, 'Gross Profit Report (Category Wise)', $istype, $from_date, $to_date);

        // Unit Cost Logic Specification v1.1 (Phase 6, UCL-75) — Opening Stock / Total Purchase /
        // Closing Stock dropped: they measured what was bought and held, not what was actually
        // sold. COGS now comes straight from GrossProfitReportCategorywise's rewritten formula
        // (SUM(line_cost_value) - SUM(returned_cost_value) per product, or Fixed Purchase Price x
        // Qty for Stock=Disable products, UCL-76) — a single, simpler Cost Of Sales column.
        // Column widths — A4 landscape 297 - 10 - 10 = 277mm. 50+70+50+50+57 = 277
        $c = [50, 70, 50, 50, 57];
        $lh = 7;

        $drawHeader = function() use ($pdf, $c, $lh) {
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetFillColor(51, 65, 85);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetDrawColor(148, 163, 184);
            $pdf->SetLineWidth(0.2);
            $pdf->Cell($c[0], $lh, 'Category',      1, 0, 'C', true);
            $pdf->Cell($c[1], $lh, 'Products',      1, 0, 'C', true);
            $pdf->Cell($c[2], $lh, 'Total Sales',   1, 0, 'C', true);
            $pdf->Cell($c[3], $lh, 'Cost Of Sales',  1, 0, 'C', true);
            $pdf->Cell($c[4], $lh, 'Gross Profit',  1, 1, 'C', true);
            $pdf->SetTextColor(30, 41, 59);
        };

        $drawHeader();

        $data      = $_SESSION['gross_profit_report_category_data'] ?? [];
        $fill      = false;
        $maxY      = $pdf->GetPageHeight() - 18;
        $category  = '';

        $total_sales        = 0;
        $total_cogs         = 0;
        $total_gross_profit = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lh > $maxY) {
                $pdf->AddPage();
                $drawHeader();
                $fill = false;
            }

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }

            $catLabel = ($category === $row['category_name']) ? '' : $row['category_name'];
            $category = $row['category_name'];

            $pdf->Cell($c[0], $lh, $catLabel,                                   1, 0, 'L', true);
            $pdf->Cell($c[1], $lh, $row['product_name'],                         1, 0, 'L', true);
            $pdf->Cell($c[2], $lh, number_format($row['total_sale'], 2),         1, 0, 'R', true);
            $pdf->Cell($c[3], $lh, number_format($row['cogs'], 2),               1, 0, 'R', true);
            $pdf->Cell($c[4], $lh, number_format($row['gross_profit'], 2),       1, 1, 'R', true);

            $total_sales        += $row['total_sale'];
            $total_cogs         += $row['cogs'];
            $total_gross_profit += $row['gross_profit'];
            $fill = !$fill;
        }

        // ── Total row (green) ──
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell($c[0]+$c[1], $lh, 'TOTAL',                               1, 0, 'R', true);
        $pdf->Cell($c[2],       $lh, number_format($total_sales, 2),         1, 0, 'R', true);
        $pdf->Cell($c[3],       $lh, number_format($total_cogs, 2),          1, 0, 'R', true);
        $pdf->Cell($c[4],       $lh, number_format($total_gross_profit, 2),  1, 1, 'R', true);

        $date     = date('Y-m-d');
        $filename = "Gross_Profit_Category_Report_$date.pdf";
        $pdf->Output($filename, 'I');
        exit;
    }

    public function bdtask_service_order_report()
    {
        // $sales_report = $this->report_model->todays_sales_report();
        $sales_amount = 0;
        if (!$this->permission1->method('service_order_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('service_order_report'),
            // 'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['module']   = "report";
        $data['page']     = "service_order_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function service_order_reportinvoicewise()
    {
        $from_date   = $this->input->post('from_date');
        $to_date     = $this->input->post('to_date');
        $empid       = $this->input->post('empid');
        $branch      = $this->input->post('branch');
        $customer_id = $this->input->post('customer_id');
        $status = $this->input->post('status');

        $report_data = $this->report_model->service_order_reportinvoicewise($from_date, $to_date, $empid, $branch, $customer_id,$status);
        if (!$report_data) {
            $report_data = [];
        }
        $_SESSION['service_order_reportsori'] = $report_data;
        $_SESSION['ssori_istype']              = $this->input->post('istype');
        $_SESSION['ssorifrom_date']            = $from_date;
        $_SESSION['ssorito_date']              = $to_date;

        echo json_encode($_SESSION['service_order_reportsori']);
    }

    public function generate_serviceorderreportinvoice()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Service Order Report');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Service Order Report", $_SESSION['ssori_istype'], $_SESSION['ssorifrom_date'], $_SESSION['ssorito_date']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(30, 7, 'Order Date',    1, 0, 'L', true);
        $pdf->Cell(30, 7, 'EOD Date',      1, 0, 'L', true);
        $pdf->Cell(30, 7, 'Order No',      1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Customer Name', 1, 0, 'L', true);
        $pdf->Cell(20, 7, 'Status',        1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Amount',        1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data       = isset($_SESSION['service_order_reportsori']) ? $_SESSION['service_order_reportsori'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY       = 270;
        $patotal    = 0;
        $total      = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Service Order Report", $_SESSION['ssori_istype'], $_SESSION['ssorifrom_date'], $_SESSION['ssorito_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(30, 7, 'Order Date',    1, 0, 'L', true);
                $pdf->Cell(30, 7, 'EOD Date',      1, 0, 'L', true);
                $pdf->Cell(30, 7, 'Order No',      1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Customer Name', 1, 0, 'L', true);
                $pdf->Cell(20, 7, 'Status',        1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Amount',        1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }

            $amount = (float) $row['total'];
            $total += $amount;
            $patotal += $amount;

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(30, 8, $row['date'],          1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['eod_date'],      1, 0, 'L', true);
            $pdf->Cell(30, 8, $row['invoiceno'],     1, 0, 'L', true);
            $pdf->Cell(40, 8, $row['customer_name'], 1, 0, 'L', true);
            $pdf->Cell(20, 8, $row['status_label'],  1, 0, 'L', true);
            $pdf->Cell(40, 8, number_format($amount, 2), 1, 1, 'R', true);
            $fill = !$fill;
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(40,  7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(110, 7, "",              1, 0, 'L', true);
        $pdf->Cell(40,  7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Service Order Report_$date.pdf";
        $pdf->Output($filename, 'I');
    }

    public function bdtask_product_batch_summary_report()
    {
        if (!$this->permission1->method('product_batch_summary_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'         => display('product_batch_summary_report'),
            'category_list' => $this->report_model->category_list_product(),
             'product_list' => $this->report_model->product_list_stock()
        );
        $data['module']   = "report";
        $data['page']     = "product_batch_summary_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

    public function product_batch_summary_report_data()
    {
        $store = $this->input->post('store', TRUE);
        $category = $this->input->post('category', TRUE);
        $product = $this->input->post('product', TRUE);
        $supplier = $this->input->post('supplier', TRUE);
        $batch_type = $this->input->post('batch_type', TRUE);
        $status = $this->input->post('status', TRUE);

        $report_data = $this->report_model->product_batch_summary_report($store, $category, $product, $supplier, $batch_type, $status);

        $_SESSION['product_batch_summary_report_data'] = $report_data;
        $_SESSION['pbsr_store'] = $store;
        $_SESSION['pbsr_category'] = $category;
        $_SESSION['pbsr_product'] = $product;
        $_SESSION['pbsr_supplier'] = $supplier;
        $_SESSION['pbsr_batch_type'] = $batch_type;
        $_SESSION['pbsr_status'] = $status;

        echo json_encode($report_data);
    }

    public function generate_product_batch_summary_report()
    {
        @ini_set('memory_limit', '512M');

        $data = isset($_SESSION['product_batch_summary_report_data']) ? $_SESSION['product_batch_summary_report_data'] : [];

        $pdf = new StockReport('L', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Product Batch Summary Report');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage('L', 'A4');

        $this->header($pdf, 1, 'Product Batch Summary Report', '', '', '');

        $this->renderProductBatchSummaryHeaderRow($pdf);

        $lineHeight = 8;
        $maxY       = $pdf->GetPageHeight() - 18;
        $fill       = false;

        $pdf->SetFont('helvetica', '', 7.5);

        foreach ($data as $row) {
            $supplier  = isset($row['supplier']) && trim((string)$row['supplier']) !== '' ? $row['supplier'] : 'n/a';
            $batchText = (string)$row['batch_id'];
            $rowHeight = max($lineHeight, $pdf->getStringHeight(24, $batchText));

            if ($pdf->GetY() + $rowHeight > $maxY) {
                $pdf->AddPage('L', 'A4');
                $this->header($pdf, 1, 'Product Batch Summary Report', '', '', '');
                $this->renderProductBatchSummaryHeaderRow($pdf);
                $pdf->SetFont('helvetica', '', 7.5);
                $fill = false;
            }

            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }

            $rowY   = $pdf->GetY();
            $batchX = $pdf->GetX() + 8 + 24 + 38 + 24; // X position after first 4 cells

            $pdf->Cell(8,  $rowHeight, $row['sl'],                                       1, 0, 'C', true);
            $pdf->Cell(24, $rowHeight, $this->fitPdfText($pdf, $row['category'], 24),    1, 0, 'L', true);
            $pdf->Cell(38, $rowHeight, $this->fitPdfText($pdf, $row['product_name'], 38),1, 0, 'L', true);
            $pdf->Cell(24, $rowHeight, $this->fitPdfText($pdf, $supplier, 24),           1, 0, 'L', true);

            $pdf->MultiCell(24, $lineHeight, $batchText, 1, 'L', $fill, 0, $batchX, $rowY, true, 0, false, true, $rowHeight, 'T', false);
            $pdf->SetXY($batchX + 24, $rowY);

            $pdf->Cell(20, $rowHeight, $row['manufacture_date'],            1, 0, 'C', true);
            $pdf->Cell(20, $rowHeight, $row['packing_date'],                1, 0, 'C', true);
            $pdf->Cell(20, $rowHeight, $row['expiry_date'],                 1, 0, 'C', true);
            $pdf->Cell(16, $rowHeight, number_format((float)$row['mrp'], 2),1, 0, 'R', true);
            $pdf->Cell(22, $rowHeight, $row['avqty'],                       1, 0, 'R', true);
            // Unit Cost Logic Specification v1.1 (Phase 6) — blank, not 0.00, when this batch has
            // never been costed (zero is a misleading "free" reading; blank reads as "unknown").
            $pdf->Cell(20, $rowHeight, $row['unit_cost'] > 0 ? number_format((float)$row['unit_cost'], 2) : '-', 1, 0, 'R', true);
            $pdf->Cell(22, $rowHeight, $row['unit_cost'] > 0 ? number_format((float)$row['stock_value'], 2) : '-', 1, 0, 'R', true);
            $pdf->Cell(18, $rowHeight, $row['status'],                      1, 1, 'C', true);

            $fill = !$fill;
        }

        if (empty($data)) {
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(276, 10, 'No data available for selected filters.', 1, 1, 'C', true);
        }

        $date     = date('Y-m-d');
        $filename = "Product_Batch_Summary_Report_$date.pdf";
        $pdf->Output($filename, 'I');
        exit;
    }

    private function renderProductBatchSummaryHeaderRow($pdf)
    {
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(8,  7, 'SL',               1, 0, 'C', true);
        $pdf->Cell(24, 7, 'Category',         1, 0, 'C', true);
        $pdf->Cell(38, 7, 'Product Name',     1, 0, 'C', true);
        $pdf->Cell(24, 7, 'Supplier',         1, 0, 'C', true);
        $pdf->Cell(24, 7, 'Batch ID',         1, 0, 'C', true);
        $pdf->Cell(20, 7, 'MFG Date',         1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Packing Date',     1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Expiry Date',      1, 0, 'C', true);
        $pdf->Cell(16, 7, 'MRP',              1, 0, 'C', true);
        $pdf->Cell(22, 7, 'Master Stock Qty', 1, 0, 'C', true);
        $pdf->Cell(20, 7, 'Unit Cost',        1, 0, 'C', true);
        $pdf->Cell(22, 7, 'Stock Value',      1, 0, 'C', true);
        $pdf->Cell(18, 7, 'Status',           1, 1, 'C', true);
        $pdf->SetTextColor(30, 41, 59);
    }

    public function bdtask_purchase_report_product_wise()
    {
        // $from_date      = (!empty($this->input->get('from_date')) ? $this->input->get('from_date') : date('Y-m-d'));
        // $to_date        = (!empty($this->input->get('to_date')) ? $this->input->get('to_date') : date('Y-m-d'));
        // $product_id     = (!empty($this->input->get('product_id')) ? $this->input->get('product_id') : '');

        // $product_report = $this->report_model->retrieve_product_sales_report($from_date, $to_date, $product_id);
        if (!$this->permission1->method('purchase_report_productwise', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $product_list = $this->report_model->product_list();
        // if (!empty($product_report)) {
        //     $i = 0;
        //     foreach ($product_report as $k => $v) {
        //         $i++;
        //         $product_report[$k]['sl'] = $i;
        //     }
        // }
        // $sub_total = 0;
        // if (!empty($product_report)) {
        //     foreach ($product_report as $k => $v) {
        //         $product_report[$k]['sales_date'] = $this->occational->dateConvert($product_report[$k]['date']);
        //         $sub_total = $sub_total + $product_report[$k]['total_amount'];
        //     }
        // }
        $data = array(
            'title'          => display('purchase_report_productwise'),
            // 'sub_total'      => number_format($sub_total, 2, '.', ','),
            // 'product_report' => $product_report,
            'product_list'   => $product_list,
            // 'product_id'     => $product_id,
            // 'from'           => $from_date,
            // 'to'             => $to_date,
        );
        $data['module']   = "report";
        $data['page']     = "purchasereport_productwise";
         $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);
    }

      public function purchase_reportproductwise()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $productid = $this->input->post('productid');
        $branch = $this->input->post('branch');
       $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->retrieve_product_purchase_report($from_date, $to_date, $productid, $empid, $branch,$incident_type);
        $_SESSION['purchase_reportsrp'] =  $report_data;
        $_SESSION['prp_istype'] =   $this->input->post('istype');
        $_SESSION['prpfrom_date'] = $from_date;
        $_SESSION['prpto_date'] =  $to_date;
        echo json_encode($_SESSION['purchase_reportsrp']);
    }
    public function generate_purchasereportproduct()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle('Purchase Report(Product Wise)');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Purchase Report (Product Wise)", $_SESSION['prp_istype'], $_SESSION['prpfrom_date'], $_SESSION['prpto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(24, 7, 'Purchase Date', 1, 0, 'L', true);
        $pdf->Cell(36, 7, 'Product Name',  1, 0, 'L', true);
        $pdf->Cell(23, 7, 'Invoice No',    1, 0, 'L', true);
        $pdf->Cell(22, 7, 'Incident Type', 1, 0, 'L', true);
        $pdf->Cell(20, 7, 'Supplier Name', 1, 0, 'L', true);
        $pdf->Cell(24, 7, 'Rate',          1, 0, 'R', true);
        $pdf->Cell(15, 7, 'Qty',           1, 0, 'R', true);
        $pdf->Cell(27, 7, 'Total',         1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['purchase_reportsrp']) ? $_SESSION['purchase_reportsrp'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;



        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, "Purchase Report (Product Wise)", $_SESSION['prp_istype'], $_SESSION['prpfrom_date'], $_SESSION['prpto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(24, 7, 'Purchase Date', 1, 0, 'L', true);
                $pdf->Cell(36, 7, 'Product Name',  1, 0, 'L', true);
                $pdf->Cell(23, 7, 'Invoice No',    1, 0, 'L', true);
                $pdf->Cell(22, 7, 'Incident Type', 1, 0, 'L', true);
                $pdf->Cell(20, 7, 'Supplier Name', 1, 0, 'L', true);
                $pdf->Cell(24, 7, 'Rate',          1, 0, 'R', true);
                $pdf->Cell(15, 7, 'Qty',           1, 0, 'R', true);
                $pdf->Cell(27, 7, 'Total',         1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['total'];
            $total = $total + $row['total'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->MultiCell(24, 8, $row['date'],         1, 'L', true, 0);
            $pdf->MultiCell(36, 8, $row['product_name'], 1, 'L', true, 0);
            $pdf->MultiCell(23, 8, $row['chalan_no'],    1, 'L', true, 0);
            $pdf->MultiCell(22, 8, $row['incidenttype'], 1, 'L', true, 0);
            $pdf->MultiCell(20, 8, $row['supplier_name'],1, 'L', true, 0);
            $pdf->MultiCell(24, 8, number_format($row['product_rate'], 2), 1, 'R', true, 0);
            $pdf->MultiCell(15, 8, $row['quantity'].' '.$row['unit_name'], 1, 'R', true, 0);
            $pdf->MultiCell(27, 8, number_format($row['total'], 2), 1, 'R', true, 0);
            $pdf->Ln(8);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(164,  10, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(27,  10, number_format($total, 2), 1, 1, 'R', true);

        // $pdf->updatePageTotal($patotal);

        $date = date('Y-m-d');
        $filename = "Purchase Report (Product Wise)_$date.pdf";
        $pdf->Output($filename, 'I');
    }


    public function bdtask_payment_report()
    {
        $sales_amount = 0;
        if (!$this->permission1->method('payment_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('payment_report'),
            // 'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['acc'] = $this->paymenttype_dropdown();
        $data['to'] = "Payments";
    


        $data['module']   = "report";
        $data['type'] = 1;
        $data['page']     = "voucher_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);

    }

    public function bdtask_receipt_report()
    {
        $sales_amount = 0;
        if (!$this->permission1->method('receipt_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('receipt_report'),
            // 'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['acc'] = $this->receipttype_dropdown();
        $data['to'] = "Receipt";

        $data['module']   = "report";
        $data['type'] = 2;
        $data['page']     = "voucher_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);

    }

      public function bdtask_contra_voucher_report()
    {
        $sales_amount = 0;
        if (!$this->permission1->method('contra_voucher_report', 'read')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data = array(
            'title'        => display('contra_voucher_report'),
            // 'sales_amount' => number_format($sales_amount, 2, '.', ','),
        );
        $data['acc'] = $this->contatype_dropdown();
        $data['from'] = "Transferred From";
        $data['to'] = "Transferred To";

        $data['module']   = "report";
        $data['type'] = 3;
        $data['page']     = "voucher_report";
        $_SESSION['reporttype'] =   1;
        echo modules::run('template/layout', $data);

    }


    public function paymenttype_dropdown()
    {
        $this->db->select('id as HeadCode,name as HeadName')
            ->from('payment_receipt_type')
            ->where_in('type', ['Payment', 'Common'])
            ->where('status', '1');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;
    }

     public function receipttype_dropdown()
  {
    $this->db->select('id as HeadCode,name as HeadName')
      ->from('payment_receipt_type')
      ->where_in('type', ['Receipt','Common'])
      ->where('status', '1');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

   public function contatype_dropdown()
  {
    $this->db->select('id as HeadCode,name as HeadName')
      ->from('payment_type')
      ->where('status', '1');
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return false;
  }

  

    public function payment_rep()
    {
        $from_date = $this->input->post('from_date');
        $to_date  = $this->input->post('to_date');
        $empid = $this->input->post('empid');
        $branch = $this->input->post('branch');
        $type = $this->input->post('type');
        $from = $this->input->post('from');
        $to = $this->input->post('to');



        $report_data = $this->report_model->voucher_report($from_date, $to_date, $empid, $branch, $type, $from ,$to);
        $_SESSION['voucher_report'] =  $report_data;
        $_SESSION['v_istype'] =   $this->input->post('istype');
        $_SESSION['vfrom_date'] = $from_date;
        $_SESSION['vto_date'] =  $to_date;
        
        if($type==1){
            $_SESSION['head'] = "Payment Report";
            $_SESSION['head1'] = "Payment Method";
            $_SESSION['head2'] = "Payments";

        }else if($type==2){
            $_SESSION['head'] = "Receipt Report";
            $_SESSION['head1'] = "Payment Method";
            $_SESSION['head2'] = "Receipt";
            
        }else{
            $_SESSION['head'] = "Contra Voucher Report";
            $_SESSION['head1'] = "Transferred From";
            $_SESSION['head2'] = "Transferred To";

        }



        echo json_encode($_SESSION['voucher_report']);
    }

       public function generate_voucherReport()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Your Name');
        $pdf->SetTitle($_SESSION['head']);
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, columns, example');
        $top_margin = 5;
        $pdf->SetMargins(15, $top_margin, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, $_SESSION['head'], $_SESSION['v_istype'], $_SESSION['vfrom_date'], $_SESSION['vto_date']);


        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(45, 7, 'Date',               1, 0, 'L', true);
        $pdf->Cell(33, 7, 'Voucher Id',          1, 0, 'L', true);
        $pdf->Cell(40, 7, $_SESSION['head1'],    1, 0, 'L', true);
        $pdf->Cell(35, 7, $_SESSION['head2'],    1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Amount',              1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['voucher_report']) ? $_SESSION['voucher_report'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;

        $patotal = 0;
        $total = 0;

        foreach ($data as $row) {

            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal);
                $patotal = 0;
                $pdf->AddPage();
                $page = $page + 1;
                $this->header($pdf, $page, $_SESSION['head'], $_SESSION['v_istype'], $_SESSION['vfrom_date'], $_SESSION['vto_date']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(45, 7, 'Date',             1, 0, 'L', true);
                $pdf->Cell(33, 7, 'Voucher Id',        1, 0, 'L', true);
                $pdf->Cell(40, 7, $_SESSION['head1'],  1, 0, 'L', true);
                $pdf->Cell(35, 7, $_SESSION['head2'],  1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Amount',            1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }
            $patotal = $patotal + $row['amount'];
            $total = $total + $row['amount'];

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(45, 8, $row['date'], 1, 0, 'L', true);
            if ($_SESSION['head'] == "Payment Report") {
                $pdf->Cell(33, 8, "PV-" . $row['voucher_id'], 1, 0, 'L', true);
            } elseif ($_SESSION['head'] == "Receipt Report") {
                $pdf->Cell(33, 8, "RV-" . $row['voucher_id'], 1, 0, 'L', true);
            } else {
                $pdf->Cell(33, 8, "CV-" . $row['voucher_id'], 1, 0, 'L', true);
            }
            $pdf->Cell(40, 8, $row['from_name'], 1, 0, 'L', true);
            $pdf->Cell(35, 8, $row['to_name'],   1, 0, 'L', true);
            $pdf->Cell(40, 8, number_format($row['amount'], 2), 1, 1, 'R', true);
            $fill = !$fill;
        }
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(50,  7, "Total Amount:", 1, 0, 'L', true);
        $pdf->Cell(100, 7, "", 1, 0, 'L', true);
        $pdf->Cell(45,  7, number_format($total, 2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal);




        $date = date('Y-m-d');
        $filename = "voucher_report.pdf";
        $pdf->Output($filename, 'I');
    }

    public function bdtask_profit_report_invoicewise()
    {
        $data['title']  = 'Profit Report (Invoice Wise)';
        $data['module'] = "report";
        $data['page']   = "profit_report_invoicewise";
        echo modules::run('template/layout', $data);
    }

    public function getEmployeesForReport()
    {
        $this->db->select("id, CONCAT(first_name, ' ', last_name) AS employee_name");
        $this->db->from('users');
        $this->db->where('status', 1);
        $this->db->order_by('first_name', 'ASC');
        $query = $this->db->get();
        echo json_encode($query->result_array());
    }

    public function profit_reportinvoicewise()
    {
        $_SESSION['reporttype'] = 1;
        $from_date     = $this->input->post('from_date');
        $to_date       = $this->input->post('to_date');
        $employee_id   = $this->input->post('employee_id');
        $empid         = $this->input->post('empid');
        $branch        = $this->input->post('branch');
        $customer_id   = $this->input->post('customer_id');
        $incident_type = $this->input->post('incident_type');

        $report_data = $this->report_model->profit_report_invoicewise($from_date, $to_date, $empid, $branch, $customer_id, $incident_type, $employee_id);
        $_SESSION['profit_inv_data']   = $report_data ? $report_data : [];
        $_SESSION['profit_inv_istype'] = $this->input->post('istype');
        $_SESSION['profit_inv_from']   = $from_date;
        $_SESSION['profit_inv_to']     = $to_date;

        echo json_encode($_SESSION['profit_inv_data']);
    }

    public function generate_profitreportinvoicewise()
    {
        $page = 1;
        $pdf = new SalesReportInvoicewise('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('ERP');
        $pdf->SetTitle('Profit Report (Invoice Wise)');
        $pdf->SetSubject('Profit Report Invoice Wise');
        $pdf->SetKeywords('TCPDF, PDF, profit, invoice');
        $pdf->SetMargins(15, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 20);
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 10);

        $this->header($pdf, $page, "Profit Report (Invoice Wise)", $_SESSION['profit_inv_istype'], $_SESSION['profit_inv_from'], $_SESSION['profit_inv_to']);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetFillColor(51, 65, 85);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(148, 163, 184);
        $pdf->SetLineWidth(0.2);
        $pdf->Cell(45, 7, 'Sales Date',      1, 0, 'L', true);
        $pdf->Cell(40, 7, 'Invoice No',      1, 0, 'L', true);
        $pdf->Cell(35, 7, 'Invoice Total',  1, 0, 'R', true);
        $pdf->Cell(35, 7, 'Cost', 1, 0, 'R', true);
        $pdf->Cell(30, 7, 'Profit',    1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetTextColor(30, 41, 59);

        $data = isset($_SESSION['profit_inv_data']) ? $_SESSION['profit_inv_data'] : [];
        $lineHeight = 10;
        $fill = false;
        $maxY = 270;

        $patotal_supplier = 0;
        $patotal_sale     = 0;
        $patotal_profit   = 0;
        $total_supplier   = 0;
        $total_sale       = 0;
        $total_profit     = 0;

        foreach ($data as $row) {
            if ($pdf->GetY() + $lineHeight > $maxY) {
                $pdf->updatePageTotal($patotal_profit);
                $patotal_supplier = 0;
                $patotal_sale     = 0;
                $patotal_profit   = 0;
                $page++;
                $pdf->AddPage();
                $this->header($pdf, $page, "Profit Report (Invoice Wise)", $_SESSION['profit_inv_istype'], $_SESSION['profit_inv_from'], $_SESSION['profit_inv_to']);
                $pdf->SetFont('helvetica', 'B', 7.5);
                $pdf->SetFillColor(51, 65, 85);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(148, 163, 184);
                $pdf->SetLineWidth(0.2);
                $pdf->Cell(45, 7, 'Sales Date',      1, 0, 'L', true);
                $pdf->Cell(40, 7, 'Invoice No',      1, 0, 'L', true);
                $pdf->Cell(35, 7, 'Invoice Total',  1, 0, 'R', true);
                $pdf->Cell(35, 7, 'Cost', 1, 0, 'R', true);
                $pdf->Cell(30, 7, 'Profit',    1, 0, 'R', true);
                $pdf->Ln();
                $pdf->SetTextColor(30, 41, 59);
            }

            $supplier = (float)($row['cost']          ?? 0);
            $sale     = (float)($row['invoice_total'] ?? 0);
            $profit   = (float)($row['profit']        ?? 0);

            $total_supplier   += $supplier;
            $total_sale       += $sale;
            $total_profit     += $profit;
            $patotal_supplier += $supplier;
            $patotal_sale     += $sale;
            $patotal_profit   += $profit;

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetDrawColor(203, 213, 225);
            if ($fill) { $pdf->SetFillColor(248, 250, 252); } else { $pdf->SetFillColor(255, 255, 255); }
            $pdf->Cell(45, 8, $row['sale_date'] ?? '',         1, 0, 'L', true);
            $pdf->Cell(40, 8, $row['sale_id']   ?? '',         1, 0, 'L', true);
            $pdf->Cell(35, 8, number_format($sale,     2),     1, 0, 'R', true);
            $pdf->Cell(35, 8, number_format($supplier, 2),     1, 0, 'R', true);
            $pdf->Cell(30, 8, number_format($profit,   2),     1, 1, 'R', true);
            $fill = !$fill;
        }

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetFillColor(243, 240, 254);
        $pdf->SetTextColor(139, 92, 246);
        $pdf->SetDrawColor(221, 214, 254);
        $pdf->Cell(85, 7, 'Total:',                          1, 0, 'L', true);
        $pdf->Cell(35, 7, number_format($total_sale,     2), 1, 0, 'R', true);
        $pdf->Cell(35, 7, number_format($total_supplier, 2), 1, 0, 'R', true);
        $pdf->Cell(30, 7, number_format($total_profit,   2), 1, 1, 'R', true);
        $pdf->updatePageTotal($patotal_profit);

        $date = date('Y-m-d');
        $pdf->Output("Profit_Report_Invoice_Wise_$date.pdf", 'I');
    }


}
