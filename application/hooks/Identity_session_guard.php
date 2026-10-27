<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ties the ERP session to the identity hub session it was created from.
 *
 * Runs after the controller is constructed (post_controller_constructor, see config/hooks.php).
 * For a user who signed in through the hub it asks the hub - at most once every
 * check_interval seconds - whether that hub session is still alive. If the hub says it has
 * ended (timeout, sign-out from another app, ended by an admin) the ERP session is destroyed
 * too. If the hub cannot be reached, the user is left alone: a hub outage must not log
 * everyone out. Password (local) logins have no hub session and are never touched.
 */
class Identity_session_guard
{
    public function check()
    {
        $CI =& get_instance();
        if (!isset($CI->session) || !$CI->session->userdata('isLogIn')) {
            return;
        }
        if ($CI->router->fetch_method() === 'session_status') {
            return;                                   // the page-side poll looks without touching
        }
        $sid = (string)$CI->session->userdata('sso_sid');
        if ($sid === '') {
            return;                                   // not a hub login
        }

        $CI->load->library('identity_hub');
        $interval = $CI->identity_hub->check_interval();
        if (!$CI->identity_hub->enabled() || $interval <= 0) {
            return;
        }
        if (time() - (int)$CI->session->userdata('sso_checked') < $interval) {
            return;                                   // checked recently
        }

        $active = $CI->identity_hub->session_active($sid, TRUE);   // a real request = user activity
        if ($active === NULL) {
            return;                                   // hub unreachable: fail open
        }
        if ($active === TRUE) {
            $CI->session->set_userdata('sso_checked', time());
            return;
        }

        // The hub session is gone: end the ERP session as well
        $user_id = $CI->session->userdata('id');
        if ($user_id) {
            $CI->db->where('user_id', $user_id)->update('users', array('is_online' => 0));
            $CI->db->where('user_id', $user_id)->delete('user_online');
        }
        $CI->session->sess_destroy();

        if ($CI->input->is_ajax_request()) {
            $CI->output->set_status_header(401);
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'session_expired', 'redirect' => base_url('login')));
            exit;
        }
        redirect('login');
    }
}
