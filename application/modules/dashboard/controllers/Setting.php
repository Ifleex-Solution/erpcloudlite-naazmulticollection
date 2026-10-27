<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Setting extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->db->query('SET SESSION sql_mode = ""');
		$this->load->model(array(
			'setting_model'
		));
		$this->load->library('ciqrcode');

		if (!$this->session->userdata('isLogIn'))
			redirect('login');
	}


	public function index()
	{
		$data['title'] = display('setting');
		#-------------------------------#
		//check setting table row if not exists then insert a row
		$this->check_setting();


		#-------------------------------#
		$data['languageList']  = $this->languageList();
		$data['sdata']         = $this->setting_model->read();
		$data['currency_list'] = $this->setting_model->currency_list();
		$data['timezonelist']  = array(
			'Africa/Casablanca'   => 'Africa/Casablanca',
			'Africa/Lagos'        => 'Africa/Lagos',
			'Africa/Cairo'        => 'Africa/Cairo',
			'Africa/Harare'       => 'Africa/Harare',
			'Africa/Johannesburg' => 'Africa/Johannesburg',
			'Africa/Monrovia'     => 'Africa/Monrovia',
			'America/Anchorage'   => 'America/Anchorage',
			'America/Los_Angeles' => 'America/Los_Angeles',
			'America/Tijuana'     => 'America/Tijuana',
			'America/Chihuahua'   => 'America/Chihuahua',
			'America/Mazatlan'    => 'America/Mazatlan',
			'America/Denver'      => 'America/Denver',
			'America/Managua'     => 'America/Managua',
			'America/Chicago'     => 'America/Chicago',
			'America/Mexico_City' => 'America/Mexico_City',
			'America/Monterrey'   => 'America/Monterrey',
			'America/New_York'    => 'America/New_York',
			'America/Lima'        => 'America/Lima',
			'America/Bogota'      => 'America/Bogota',
			'America/Caracas'     => 'America/Caracas',
			'America/La_Paz'      => 'America/La_Paz',
			'America/Santiago'    => 'America/Santiago',
			'America/St_Johns'    => 'America/St_Johns',
			'America/Sao_Paulo'   => 'America/Sao_Paulo',
			'America/Argentina'   => 'America/Argentina',
			'America/Godthab'     => 'America/Godthab',
			'America/Noronha'     => 'America/Noronha',
			'Asia/Jerusalem'      => 'Asia/Jerusalem',
			'Asia/Baghdad'        => 'Asia/Baghdad',
			'Asia/Kuwait'         => 'Asia/Kuwait',
			'Africa/Nairobi'      => 'Africa/Nairobi',
			'Asia/Riyadh'         => 'Asia/Riyadh',
			'Asia/Tehran'         => 'Asia/Tehran',
			'Asia/Baku'           => 'Asia/Baku',
			'Asia/Muscat'         => 'Asia/Muscat',
			'Asia/Tbilisi'        => 'Asia/Tbilisi',
			'Asia/Yerevan'        => 'Asia/Yerevan',
			'Asia/Kabul'          => 'Asia/Kabul',
			'Asia/Karachi'        => 'Asia/Karachi',
			'Asia/Tashkent'       => 'Asia/Tashkent',
			'Asia/Kolkata'        => 'Asia/Kolkata',
			'Asia/Katmandu'       => 'Asia/Katmandu',
			'Asia/Almaty'         => 'Asia/Almaty',
			'Asia/Dhaka'          => 'Asia/Dhaka',
			'Asia/Yekaterinburg'  => 'Asia/Yekaterinburg',
			'Asia/Rangoon'        => 'Asia/Rangoon',
			'Asia/Bangkok'        => 'Asia/Bangkok',
			'Asia/Jakarta'        => 'Asia/Jakarta',
			'Asia/Hong_Kong'      => 'Asia/Hong_Kong',
			'Asia/Chongqing'      => 'Asia/Chongqing',
			'Asia/Krasnoyarsk'    => 'Asia/Krasnoyarsk',
			'Asia/Kuala_Lumpur'   => 'Asia/Kuala_Lumpur',
			'Australia/Perth'     => 'Australia/Perth',
			'Asia/Singapore'      => 'Asia/Singapore',
			'Asia/Taipei'         => 'Asia/Taipei',
			'Asia/Ulan_Bator'     => 'Asia/Ulan_Bator',
			'Asia/Urumqi'         => 'Asia/Urumqi',
			'Asia/Irkutsk'        => 'Asia/Irkutsk',
			'Asia/Tokyo'          => 'Asia/Tokyo',
			'Asia/Seoul'          => 'Asia/Seoul',
			'Asia/Yakutsk'        => 'Asia/Yakutsk',
			'Asia/Vladivostok'    => 'Asia/Vladivostok',
			'Asia/Kamchatka'      => 'Asia/Kamchatka',
			'Asia/Magadan'        => 'Asia/Magadan',
			'Atlantic/Azores'     => 'Atlantic/Azores',
			'Atlantic/Cape_Verde' => 'Atlantic/Cape_Verde',
			'Australia/Adelaide'  => 'Australia/Adelaide',
			'Australia/Darwin'    => 'Australia/Darwin',
			'Australia/Brisbane'  => 'Australia/Brisbane',
			'Australia/Canberra'  => 'Australia/Canberra',
			'Australia/Sydney'    => 'Australia/Sydney',
			'Australia/Hobart'    => 'Australia/Hobart',
			'Australia/Melbourne' => 'Australia/Melbourne',
			'Canada/Atlantic'     => 'Canada/Atlantic',
			'Europe/Helsinki'     => 'Europe/Helsinki',
			'Europe/London'       => 'Europe/London',
			'Europe/Dublin'       => 'Europe/Dublin',
			'Europe/Lisbon'       => 'Europe/Lisbon',
			'Europe/Belgrade'     => 'Europe/Belgrade',
			'Europe/Berlin'       => 'Europe/Berlin',
			'Europe/Bratislava'   => 'Europe/Bratislava',
			'Europe/Brussels'     => 'Europe/Brussels',
			'Europe/Budapest'     => 'Europe/Budapest',
			'Europe/Copenhagen'   => 'Europe/Copenhagen',
			'Europe/Ljubljana'    => 'Europe/Ljubljana',
			'Europe/Madrid'       => 'Europe/Madrid',
			'Europe/Paris'        => 'Europe/Paris',
			'Europe/Prague'       => 'Europe/Prague',
			'Europe/Sarajevo'     => 'Europe/Sarajevo',
			'Europe/Skopje'       => 'Europe/Skopje',
			'Europe/Stockholm'    => 'Europe/Stockholm',
			'Europe/Vienna'       => 'Europe/Vienna',
			'Europe/Warsaw'       => 'Europe/Warsaw',
			'Europe/Zagreb'       => 'Europe/Zagreb',
			'Europe/Athens'       => 'Europe/Athens',
			'Europe/Bucharest'    => 'Europe/Bucharest',
			'Europe/Riga'         => 'Europe/Riga',
			'Europe/Sofia'        => 'Europe/Sofia',
			'Europe/Tallinn'      => 'Europe/Tallinn',
			'Europe/Vilnius'      => 'Europe/Vilnius',
			'Europe/Minsk'        => 'Europe/Minsk',
			'Europe/Istanbul'     => 'Europe/Istanbul',
			'Europe/Moscow'       => 'Europe/Moscow',
			'Pacific/Port_Moresby' => 'Pacific/Port_Moresby',
			'Pacific/Fiji'        => 'Pacific/Fiji',
			'Pacific/Kwajalein'   => 'Pacific/Kwajalein',
			'Pacific/Midway'      => 'Pacific/Midway',
			'Pacific/Samoa'       => 'Pacific/Samoa',
			'Pacific/Honolulu'    => 'Pacific/Honolulu',
			'Pacific/Auckland'    => 'Pacific/Auckland',
			'Pacific/Tongatapu'   => 'Pacific/Tongatapu',
			'Pacific/Guam'        => 'Pacific/Guam',
		);
		if (!$this->permission1->method('soft_setting', 'create')->access()) {
			$previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
			redirect($previous_url);
		}
		$data['module'] = "dashboard";
		$data['page']   = "home/setting";
		echo Modules::run('template/layout', $data);
	}

	public function bdtask_create_settings()
	{
		$data['title'] = display('application_setting');
		#-------------------------------#
		$this->check_setting();
		$this->form_validation->set_rules('currency', display('currency'), 'required|max_length[50]');
		$this->form_validation->set_rules('footer_text', display('footer_text'), 'max_length[255]');
		$this->form_validation->set_rules('language', display('language'), 'required|max_length[50]');
		$this->form_validation->set_rules('discount_type', display('discount_type'), 'required|max_length[4]');;
		$this->form_validation->set_rules('expiry_alert_days', 'Expiry Alert Days', 'required|integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('credit_reminder_days', 'Credit Reminder Days', 'required|integer|greater_than_equal_to[0]');
		// No 'permit_empty' — this CI3 version's Form_validation already skips
		// non-required rules on an empty value (see _execute() in
		// system/libraries/Form_validation.php), and 'permit_empty' isn't a
		// rule this version recognizes at all (it shows as a failed rule
		// with no error-message mapping instead of being a no-op).
		$this->form_validation->set_rules('sale_credit_limit', 'Sale Credit Limit', 'decimal|greater_than_equal_to[0]');
		$this->form_validation->set_rules('service_credit_limit', 'Service Credit Limit', 'decimal|greater_than_equal_to[0]');

		#-------------------------------#
		// Logo / Invoice (Sale) Logo / Favicon — pasted image URLs, not
		// uploaded files (e.g. a Google Drive direct-image link). Stored
		// as-is; logo_url() (application/helpers/lang_helper.php) decides
		// at display time whether a value needs base_url() or is already
		// a full external URL.
		$logo         = trim($this->input->post('logo', true));
		$invoice_logo = trim($this->input->post('invoice_logo', true));
		$favicon      = trim($this->input->post('favicon', true));
		if ($this->input->post('is_qr', true) == 1) {
			$is_qr = 1;
		} else {
			$is_qr = 0;
		}
		if ($this->input->post('is_autoapprove_v', true) == 1) {
			$is_autoapprove_v = 1;
		} else {
			$is_autoapprove_v = 0;
		}
		$vk_enable = ($this->input->post('vk_enable', true) == 1) ? 1 : 0;
		$product_image_upload = ($this->input->post('product_image_upload', true) == 1) ? 1 : 0;
		$pos_print_type = (int)$this->input->post('pos_print_type', true);

		// Login Alert Pop-ups (Preference section) — company-wide on/off toggles.
		$credit_alert_enable          = ($this->input->post('credit_alert_enable', true) == 1) ? 1 : 0;
		$credit_alert_customer_enable = ($this->input->post('credit_alert_customer_enable', true) == 1) ? 1 : 0;
		$credit_alert_supplier_enable = ($this->input->post('credit_alert_supplier_enable', true) == 1) ? 1 : 0;
		$expiry_alert_enable          = ($this->input->post('expiry_alert_enable', true) == 1) ? 1 : 0;
		$stock_alert_enable           = ($this->input->post('stock_alert_enable', true) == 1) ? 1 : 0;

		#-------------------------------#

		$data['setting'] = (object)$postData = [
			'setting_id'        => $this->input->post('id', true),
			'logo'              => $logo,
			'invoice_logo'      => $invoice_logo,
			'favicon'           => $favicon,
			'currency'          => $this->input->post('currency', true),
			'currency_position' => $this->input->post('currency_position', true),
			'footer_text'       => $this->input->post('footer_text', true),
			'expiry_alert_days' => $this->input->post('expiry_alert_days', true),
			'credit_reminder_days' => $this->input->post('credit_reminder_days', true),
			'sale_credit_limit'    => ($this->input->post('sale_credit_limit', true) !== '' ? $this->input->post('sale_credit_limit', true) : 0),
			'service_credit_limit' => ($this->input->post('service_credit_limit', true) !== '' ? $this->input->post('service_credit_limit', true) : 0),
			'language'          => $this->input->post('language', true),
			'rtr'               => $this->input->post('rtr', true),
			'timezone'          => $this->input->post('timezone', true),
			'discount_type'     => $this->input->post('discount_type', true),
			'is_qr'             => $is_qr,
			'is_autoapprove_v'  => $is_autoapprove_v,
			'vk_enable'              => $vk_enable,
			'product_image_upload'   => $product_image_upload,
			'pos_print_type'         => $pos_print_type,
			'terms_conditions'  => $this->input->post('terms_conditions', FALSE),
			'service_terms_conditions' => $this->input->post('service_terms_conditions', FALSE),
			'credit_alert_enable'          => $credit_alert_enable,
			'credit_alert_customer_enable' => $credit_alert_customer_enable,
			'credit_alert_supplier_enable' => $credit_alert_supplier_enable,
			'expiry_alert_enable'          => $expiry_alert_enable,
			'stock_alert_enable'           => $stock_alert_enable,
		];
		#-------------------------------#
		if ($this->form_validation->run() === true) {

			#if empty $id then insert data
			if (empty($postData['setting_id'])) {
				if ($this->setting_model->create($postData)) {
					#set success message
					$this->session->set_flashdata('message', display('save_successfully'));
				} else {
					#set exception message
					$this->session->set_flashdata('exception', display('please_try_again'));
				}
			} else {
				if ($this->setting_model->update($postData)) {
					#set success message
					$this->session->set_flashdata('message', display('update_successfully'));
				} else {
					#set exception message
					$this->session->set_flashdata('exception', display('please_try_again'));
				}
			}

			redirect('settings');
		} else {
			$data['languageList'] = $this->languageList();
			$data['module']       = "dashboard";
			$data['page']         = "home/setting";
			echo Modules::run('template/layout', $data);
		}
	}

	//check setting table row if not exists then insert a row
	public function check_setting()
	{
		if ($this->db->count_all('web_setting') == 0) {
			$this->db->insert('web_setting', [
				'footer_text' => '2020&copy;Copyright',
			]);
		}
	}


	public function languageList()
	{
		if ($this->db->table_exists("language")) {

			$fields = $this->db->field_data("language");

			$i = 1;
			foreach ($fields as $field) {
				if ($i++ > 2)
					$result[$field->name] = ucfirst($field->name);
			}

			if (!empty($result)) return $result;
		} else {
			return false;
		}
	}


	public function bdtask_company_info()
	{
		$data['title'] 	        = display('company_list');
		$data['companys']       = $this->setting_model->company_info();
		$data['total_companies'] = $this->db->where('company_id !=', 1)->where('status', 1)->count_all_results('company_information');
		file_put_contents(FCPATH . 'logs/debug_total_companies.log', date('Y-m-d H:i:s') . ' total_companies=' . var_export($data['total_companies'], true) . ' last_query=' . $this->db->last_query() . PHP_EOL, FILE_APPEND);
		$data['module']         = "dashboard";
		$data['page']           = "home/company_list";
		if (!$this->permission1->method('manage_company', 'read')->access()) {
			$previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
			redirect($previous_url);
		}
		echo Modules::run('template/layout', $data);
	}


	public function bdtask_edit_company($id = null)
	{
		$data['title'] 	  =$id? display('edit_company'):display('add_company');
		$data['companys'] = $this->setting_model->company_details($id);
		$data['module']   = "dashboard";
		$data['page']     = "home/company_form";
		echo Modules::run('template/layout', $data);
	}

	public function delete_company($id = null)
	{
		$base_url = base_url();

		$this->db->where('company_id', $id)
		->delete("company_information");
		echo '<script type="text/javascript">
		alert("Company Details deleted successfully");
		window.location.href = "' . $base_url . 'company_list";
	   </script>';
	}


	//company edit form
	public function company_update()
{
  
    // ...existing code...
    $company_id  = $this->input->post('company_id', true);
	$base_url = base_url();

        $encryption_key = Config::$encryption_key;

			if ($company_id) {
				$query = "
			UPDATE company_information 
			SET 
				company_name = AES_ENCRYPT('{$this->input->post('company_name', TRUE)}', '{$encryption_key}'),
				email = AES_ENCRYPT('{$this->input->post('email', TRUE)}', '{$encryption_key}'),
				address = AES_ENCRYPT('{$this->input->post('address', TRUE)}', '{$encryption_key}'),
				mobile = AES_ENCRYPT('{$this->input->post('mobile', TRUE)}', '{$encryption_key}'),
				website =  AES_ENCRYPT('{$this->input->post('website', TRUE)}', '{$encryption_key}'),
				vat_no = AES_ENCRYPT('{$this->input->post('vat_no', TRUE)}', '{$encryption_key}'),
				cr_no =  AES_ENCRYPT('{$this->input->post('cr_no', TRUE)}', '{$encryption_key}'),
				instance_type = '{$this->input->post('instance_type', TRUE)}',
				status = '{$this->input->post('status', TRUE)}',
				password_enable = '{$this->input->post('password_enable', TRUE)}',
				header_text =  AES_ENCRYPT('{$this->input->post('header_text', TRUE)}', '{$encryption_key}'),
				footer_text =  AES_ENCRYPT('{$this->input->post('footer_text', TRUE)}', '{$encryption_key}'),
				password =  AES_ENCRYPT('{$this->input->post('password', TRUE)}', '{$encryption_key}')
			WHERE company_id = '{$company_id}';
		";

				$this->setting_model->update_company($query);

			if ($company_id == 1) {
				$query = "
			UPDATE company_information 
			SET 
				company_name = AES_ENCRYPT('{$this->input->post('company_name', TRUE)}', '{$encryption_key}'),
				email = AES_ENCRYPT('{$this->input->post('email', TRUE)}', '{$encryption_key}'),
				address = AES_ENCRYPT('{$this->input->post('address', TRUE)}', '{$encryption_key}'),
				mobile = AES_ENCRYPT('{$this->input->post('mobile', TRUE)}', '{$encryption_key}'),
				website =  AES_ENCRYPT('{$this->input->post('website', TRUE)}', '{$encryption_key}'),
				vat_no = AES_ENCRYPT('{$this->input->post('vat_no', TRUE)}', '{$encryption_key}'),
				cr_no =  AES_ENCRYPT('{$this->input->post('cr_no', TRUE)}', '{$encryption_key}'),
				instance_type = '{$this->input->post('instance_type', TRUE)}',
				status = '{$this->input->post('status', TRUE)}',
				header_text =  AES_ENCRYPT('{$this->input->post('header_text', TRUE)}', '{$encryption_key}'),
				footer_text =  AES_ENCRYPT('{$this->input->post('footer_text', TRUE)}', '{$encryption_key}'),
				password =  AES_ENCRYPT('{$this->input->post('password', TRUE)}', '{$encryption_key}'),
				password_enable = '{$this->input->post('password_enable', TRUE)}'
			WHERE company_id = 2;
		";

				$this->setting_model->update_company($query);
			}

				echo '
				<script type="text/javascript">
				alert("Company Details Updated successfully");
				window.location.href = "' . $base_url . 'company_list";
			   </script>';
			}else{
				$query = "
INSERT INTO company_information (
    company_name,
    email,
    address,
    mobile,
    website,
    vat_no,
    cr_no,
	password_enable,
    instance_type,
    status,
	header_text,
    footer_text,
    password
) VALUES (
    AES_ENCRYPT('{$this->input->post('company_name', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('email', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('address', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('mobile', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('website', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('vat_no', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('cr_no', TRUE)}', '{$encryption_key}'),
	'{$this->input->post('password_enable', TRUE)}',
    '{$this->input->post('instance_type', TRUE)}',
    '{$this->input->post('status', TRUE)}',
	AES_ENCRYPT('{$this->input->post('header_text', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('footer_text', TRUE)}', '{$encryption_key}'),
    AES_ENCRYPT('{$this->input->post('password', TRUE)}', '{$encryption_key}')
);
";

				$this->setting_model->update_company($query);
				echo '
		   <script type="text/javascript">
		   alert("Company Details Saved successfully");
		   window.location.href = "' . $base_url . 'company_list";
		  </script>';
		}

		
			// redirect(base_url('company_list'));
		
	}



	//currency add edit form
	public function bdtask_currencyform($id = null)
	{
		$data['title']    = display('add_currency');
		/*-----------------------------------*/
		$this->form_validation->set_rules('currency_name', display('currency_name'), 'required|max_length[50]');
		$this->form_validation->set_rules('currency_icon', display('currency_icon'), 'required|max_length[50]');

		/*-----------------------------------*/
		$data['currency_data'] = (object)$currencyData = array(
			'id'            => $this->input->post('id'),
			'currency_name' => $this->input->post('currency_name'),
			'icon'          => $this->input->post('currency_icon'),
		);
		if (!$this->permission1->method('add_currency', 'create')->access()) {
			$previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
			redirect($previous_url);
		}

		/*-----------------------------------*/
		if ($this->form_validation->run()) {
			if (empty($id)) {
				if ($this->setting_model->add_currency($currencyData)) {
					$this->session->set_flashdata('message', display('save_successfully'));
				} else {
					$this->session->set_flashdata('exception', display('please_try_again'));
				}
				redirect("currency_list");
			} else {
				if ($this->setting_model->update_currency($currencyData)) {
					$this->session->set_flashdata('message', display('update_successfully'));
				} else {
					$this->session->set_flashdata('exception', display('please_try_again'));
				}

				redirect("currency_form/$id");
			}
		} else {
			if (!empty($id)) {
				$data['title']    = display('edit_currency');
				$data['currency_data'] = $this->setting_model->currency_editdata($id);
			}
			$data['module']   = "dashboard";
			$data['page']     = "currency/currency_form";

			echo Modules::run('template/layout', $data);
		}
	}


	public function bdtask_currency_list()
	{
		$data['title'] 	       = display('currency_list');
		$data['currency_list'] = $this->setting_model->currency_list();
		$data['module']        = "dashboard";
		$data['page']          = "currency/list";
		echo Modules::run('template/layout', $data);
	}


	public function bdtask_deletecurrency($id = null)
	{
		if ($this->setting_model->delete_currency($id)) {
			$this->session->set_flashdata('message', display('delete_successfully'));
		} else {
			$this->session->set_flashdata('exception', display('please_try_again'));
		}

		redirect("currency_list");
	}


	public function bdtask_mail_config()
	{
		if (!$this->permission1->method('print_setting', 'create')->access()) {
			$previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
			redirect($previous_url);
		}

		$this->check_mail_config();
		$data['title']        = display('mail_setting');
		$data['module']       = "dashboard";
		$data['mail_setting'] = $this->setting_model->mail_settingdata();
		$data['page']         = "currency/mail_setting";
		echo Modules::run('template/layout', $data);
	}


	public function mail_config_update()
	{
		$this->form_validation->set_rules('protocol', 'Protocol', 'required|max_length[200]');
		$this->form_validation->set_rules('smtp_host', 'Host', 'required|max_length[250]');
		#------------------------#
		$this->form_validation->set_rules('smtp_port', 'Port', "required|max_length[100]");
		$this->form_validation->set_rules('smtp_user', 'smtp_user', 'required|max_length[250]');
		$this->form_validation->set_rules('smtp_pass', 'smtp_pass', 'required|max_length[250]');
		$protocol  = $this->input->post('protocol', true);
		$smtp_host = $this->input->post('smtp_host', true);
		$smtp_port = $this->input->post('smtp_port', true);
		$smtp_user = $this->input->post('smtp_user', true);
		$smtp_pass = $this->input->post('smtp_pass', true);
		$mailtype  = $this->input->post('mailtype', true);
		$invoice   = $this->input->post('isinvoice', true);
		$service   = $this->input->post('isservice', true);
		$quotation = $this->input->post('isquotation', true);

		$mail_data = array(
			'protocol'  => $protocol,
			'smtp_host' => $smtp_host,
			'smtp_port' => $smtp_port,
			'smtp_user' => $smtp_user,
			'smtp_pass' => $smtp_pass,
			'mailtype'  => $mailtype,
			'isinvoice' => $invoice,
			'isservice' => $service,
			'isquotation' => $quotation,
		);
		if ($this->form_validation->run()) {
			$this->db->where('id', $this->input->post('id', true))->update('email_config', $mail_data);
			$this->session->set_flashdata('message', display('update_successfully'));
			redirect(base_url('mail_setting'));
		} else {
			$this->session->set_flashdata(array('exception' => validation_errors()));
			redirect(base_url('mail_setting'));
		}
	}

	public function check_mail_config()
	{
		if ($this->db->count_all('email_config') == 0) {
			$this->db->insert('email_config', [
				'protocol'   => 'ssmtp',
				'smtp_host'  => 'ssl://ssmtp.gmail.com',
				'smtp_port'  => 25,
				'smtp_user'  => 'demo@gmail.com',
				'smtp_pass'  => 'demo12345',
				'mailtype'   => 'html',
				'isinvoice'  => 0,
				'isservice'  => 0,
				'isquotation' => 0,
			]);
		}
	}


	public function bdtask_sms_config()
	{

		$this->check_sms_config();
		$data['title']        = display('sms_configure');
		$data['module']       = "dashboard";
		$data['configdata']   = $this->setting_model->sms_settingdata();
		$data['page']         = "currency/sms_setting";
		echo Modules::run('template/layout', $data);
	}

	public function check_sms_config()
	{
		if ($this->db->count_all('sms_settings') == 0) {
			$this->db->insert('sms_settings', [
				'api_key'    => 'sdfsdfd',
				'api_secret' => 'dsfsdfss',
				'from'       => 345645,
				'isinvoice'  => 0,
				'isservice'  => 0,
				'isreceive'  => 0,
			]);
		}
	}


	public function update_sms_configure()
	{
		$this->form_validation->set_rules('api_key', 'api_key', 'required|max_length[200]');
		$this->form_validation->set_rules('api_secret', 'api_secret', 'required|max_length[250]');
		#------------------------#
		$this->form_validation->set_rules('from', 'from', "required|max_length[100]");
		$id = $this->input->post('id');
		$data = array(
			'api_key' 	  => $this->input->post('api_key', true),
			'api_secret'  => $this->input->post('api_secret', true),
			'from'        => $this->input->post('from', true),
			'isinvoice'   => $this->input->post('isinvoice', true),
			'isservice'   => $this->input->post('isservice', true),
			'isreceive'   => $this->input->post('isreceive', true),

		);
		if ($this->form_validation->run()) {
			$this->db->where('id', $id);
			$this->db->update('sms_settings', $data);
			$this->session->set_flashdata('message', display('update_successfully'));
			redirect(base_url('sms_setting'));
		} else {
			$this->session->set_flashdata(array('exception' => validation_errors()));
			redirect(base_url('sms_setting'));
		}
	}

	public function bdtask_app_setting()
	{
		$this->check_app_config();
		$data['qr_image']     = "";
		$data['server_image'] = "";
		$data['hotspotqrimg'] = "";
		$app_settingdata      = $this->setting_model->app_settingsdata();
		$qr_image             = rand() . '.png';
		$params['data']       = $app_settingdata[0]['localhserver'];
		$params['level']      = 'L';
		$params['size']       = 8;
		$params['savename']   = FCPATH . "my-assets/image/qr/" . $qr_image;
		if ($this->ciqrcode->generate($params)) {
			$localqr = $qr_image;
		}


		$serverqr = rand() . '.png';
		$params['data']     = $app_settingdata[0]['onlineserver'];
		$params['level']    = 'O';
		$params['size']     = 8;
		$params['savename'] = FCPATH . "my-assets/image/qr/" . $serverqr;
		if ($this->ciqrcode->generate($params)) {
			$server_qrimg = $serverqr;
		}



		$hotspotqr = rand() . '.png';
		$params['data']     = $app_settingdata[0]['hotspot'];
		$params['level']    = 'U';
		$params['size']     = 8;
		$params['savename'] = FCPATH . "my-assets/image/qr/" . $hotspotqr;
		if ($this->ciqrcode->generate($params)) {
			$hotspot_qrimg = $hotspotqr;
		}

		$data['module']          = "dashboard";
		$data['title']           = display('app_setting');
		$data['qr_image']        = $localqr;
		$data['server_image']    = $server_qrimg;
		$data['hotspotqrimg']    = $hotspot_qrimg;
		$data['localhserver']    = $app_settingdata[0]['localhserver'];
		$data['onlineserver']    = $app_settingdata[0]['onlineserver'];
		$data['hotspot']         = $app_settingdata[0]['hotspot'];
		$data['id']              = $app_settingdata[0]['id'];
		$data['page']            = "currency/app_setting";
		echo Modules::run('template/layout', $data);
	}

	public function check_app_config()
	{
		if ($this->db->count_all('app_setting') == 0) {
			$this->db->insert('app_setting', [
				'localhserver' => 'localhost/url',
				'onlineserver' => 'https://yoururl',
				'hotspot'      => '192.12.12',
			]);
		}
	}


	public function update_app_setting()
	{
		$this->form_validation->set_rules('localurl', 'localurl', 'required|max_length[200]');
		$this->form_validation->set_rules('onlineurl', 'onlineurl', 'required|max_length[250]');
		#------------------------#
		$this->form_validation->set_rules('hotspoturl', 'hotspoturl', "required|max_length[100]");
		$id = $this->input->post('id', TRUE);
		$data  = array(
			'localhserver' => $this->input->post('localurl', true),
			'onlineserver' => $this->input->post('onlineurl', true),
			'hotspot'      => $this->input->post('hotspoturl', true),

		);
		if ($this->form_validation->run()) {
			$this->db->where('id', $id)
				->update('app_setting', $data);

			$this->session->set_flashdata('message', display('update_successfully'));
			redirect(base_url('app_setting'));
		} else {
			$this->session->set_flashdata(array('exception' => validation_errors()));
			redirect(base_url('app_setting'));
		}
	}



	public function commission()
	{
		$customer_info    = $this->setting_model->customer_info();
		$data = array(
			'title'         => display('commission'),
			'customer_info' => $customer_info,
			'product_info'  => "",
		);
		$data['module']       = "dashboard";
		$data['page']         = "commission/commission_generate";
		echo Modules::run('template/layout', $data);
	}

	public function commission_generate()
	{

		$customer_info = $this->setting_model->customer_info();
		$product_info = $this->setting_model->product_info();

		$commission_rate = $this->input->post('commission_rate', true);

		$subTotalCtn = 0;
		$subTotalQnty = 0;
		$subTotalComs = 0;
		if ($product_info) {
			foreach ($product_info as $k => $product) {

				$product_info[$k]['per_cartoon'] = $product_info[$k]['quantity'];

				$product_info[$k]['total_commission_rate'] = $product_info[$k]['quantity'] * $commission_rate;

				$product_info[$k]['commission'] = $commission_rate;

				$product_info[$k]['subTotalQnty'] = $subTotalQnty + $product_info[$k]['quantity'];
				$subTotalQnty = $product_info[$k]['subTotalQnty'];

				$product_info[$k]['subTotalComs'] = $subTotalComs + $product_info[$k]['total_commission_rate'];
				$subTotalComs = $product_info[$k]['subTotalComs'];
			}
		}


		$data = array(
			'title' => display('commission'),
			'customer_info' => $customer_info,
			'product_info'  => $product_info,
			'subTotalCtn'   => $subTotalCtn,
			'subTotalQnty'  => $subTotalQnty,
			'subTotalComs'  => $subTotalComs,
		);

		$data['module']       = "dashboard";
		$data['page']         = "commission/commission_generate";
		echo Modules::run('template/layout', $data);;
	}

	public function retrive_product_info()
	{
		$customer_id = $this->input->post('customer_id', true);
		$product_info = $this->db->select('
                                invoice_details.*,
                                product_information.product_model
                                ')
			->from('invoice')
			->join('invoice_details', 'invoice_details.invoice_id = invoice.invoice_id')
			->join('product_information', 'invoice_details.product_id = product_information.product_id')
			->where('invoice.customer_id', $customer_id)
			->group_by('invoice_details.product_id')
			->get()
			->result();

		if ($product_info) {
			echo "<select class=\"form-control\" name=\"product_model\" id=\"product_model\">";
			echo "<option>" . display('select_one') . "</option>";
			foreach ($product_info as $product) {
				echo "<option value='" . $product->product_id . "'>" . $product->product_model . "</option>";
			}
			echo "</select>";
		}
	}

	public function checkpassword()
	{
		$encryption_key = Config::$encryption_key;

		echo json_encode($this->db->from('company_information c')
		->where('c.company_id', $this->session->userdata('user_level2'))
		->where("AES_DECRYPT(c.password, '{$encryption_key}') =", $this->db->escape($this->input->post('password')), false)
		->count_all_results());

	}

	public function checkpasswordReport()
	{
		$encryption_key = Config::$encryption_key;
		$password = $this->input->post('password', true);


        $data = $this->db->select("
		 AES_DECRYPT(password, '{$encryption_key}') AS password
	 ")
            ->from('company_information')
			->where_in('company_id', [1,3])
            ->get()
            ->result_array();

			if($data[0]['password']==$password){
				echo json_encode("A");
				$_SESSION['reporttype'] =   1;

			}elseif($data[1]['password']==$password){
				echo json_encode("B");
				$_SESSION['reporttype'] =   3;

			}elseif($data[0]['password'].$data[1]['password']==$password){
				echo json_encode("All");
				$_SESSION['reporttype'] =   $this->session->userdata('user_level2');

			}else{
				echo json_encode("wrong password");

			}

	}

    public function heartbeat()
    {
        if (!$this->session->userdata('isLogIn')) {
            echo json_encode(['status' => 'not_logged_in']);
            return;
        }

        date_default_timezone_set('Asia/Colombo');

        $this->db->query("CREATE TABLE IF NOT EXISTS `user_online` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` int(11) NOT NULL,
            `fullname` varchar(150) DEFAULT NULL,
            `email` varchar(150) DEFAULT NULL,
            `last_seen` datetime NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `user_id` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        // Add is_online column to users table if it doesn't exist
        $col = $this->db->query("SHOW COLUMNS FROM `users` LIKE 'is_online'")->num_rows();
        if (!$col) {
            $this->db->query("ALTER TABLE `users` ADD COLUMN `is_online` tinyint(1) NOT NULL DEFAULT 0");
        }

        $user_id = $this->session->userdata('id');
        $now     = date('Y-m-d H:i:s');

        $data = [
            'user_id'  => $user_id,
            'fullname' => $this->session->userdata('fullname'),
            'email'    => $this->session->userdata('email'),
            'last_seen'=> $now,
        ];

        $exists = $this->db->where('user_id', $user_id)->count_all_results('user_online');
        if ($exists) {
            $this->db->where('user_id', $user_id)->update('user_online', $data);
        } else {
            $this->db->insert('user_online', $data);
        }

        // Mark user as online in users table
        $this->db->where('user_id', $user_id)->update('users', ['is_online' => 1]);

        echo json_encode(['status' => 'ok']);
    }

    public function go_offline()
    {
        if (!$this->session->userdata('isLogIn')) {
            return;
        }

        $user_id = $this->session->userdata('id');

        // Mark user as offline in users table
        $this->db->where('user_id', $user_id)->update('users', ['is_online' => 0]);

        // Remove from active online list
        $this->db->where('user_id', $user_id)->delete('user_online');

        echo json_encode(['status' => 'ok']);
    }

}