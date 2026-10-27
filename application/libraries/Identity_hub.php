<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Client for the Fexten Identity Hub (authorization-code flow).
 *
 *   1. authorize_url()   - send the browser to the hub to sign in
 *   2. exchange_code()   - hub redirects back with ?code=&state=; swap the code for the user
 */
class Identity_hub
{
    const STATE_TTL = 600;   // seconds a pending sign-in stays valid (10 minutes)

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->config->load('identity', TRUE, TRUE);
    }

    protected function cfg($key, $default = '')
    {
        $v = $this->CI->config->item($key, 'identity');
        return ($v === NULL) ? $default : $v;
    }

    public function enabled()
    {
        return (bool)$this->cfg('enabled', FALSE)
            && $this->cfg('hub_url') !== ''
            && $this->cfg('product_id') !== ''
            && $this->cfg('client_id') !== ''
            && $this->cfg('client_secret') !== '';
    }

    public function allow_local_login()
    {
        return (bool)$this->cfg('allow_local_login', TRUE);
    }

    public function redirect_uri()
    {
        $u = $this->cfg('redirect_uri');
        return $u !== '' ? $u : base_url();
    }

    /**
     * Asks the hub whether this client + product can be used right now. FALSE when the
     * client/product is missing, switched off, not mapped to each other, misconfigured,
     * or the hub cannot be reached - the caller should then use the local login page.
     */
    public function available()
    {
        $ch = curl_init(rtrim($this->cfg('hub_url'), '/') . '/status.php?' . http_build_query(array(
            'client_id'    => $this->cfg('client_id'),
            'product_id'   => $this->cfg('product_id'),
            'redirect_uri' => $this->redirect_uri(),
        )));
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT        => 4,
        ));
        $body = curl_exec($ch);
        $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === FALSE || $http !== 200) {
            return FALSE;
        }
        $res = json_decode($body, TRUE);
        return is_array($res) && !empty($res['available']);
    }

    /** Builds the hub login URL and remembers a one-time state value in the session. */
    public function authorize_url($retry = FALSE)
    {
        // A state value that ends in 'r' marks the one automatic retry (see is_retry_state()).
        $state = (function_exists('random_bytes') ? bin2hex(random_bytes(16)) : bin2hex(openssl_random_pseudo_bytes(16)))
               . ($retry ? 'r' : '');

        // Remember several pending sign-ins, not just the latest. Opening the ERP login address twice
        // (second tab, refresh, browser pre-loading, double click) must not invalidate the first one.
        $now    = time();
        $states = $this->CI->session->userdata('sso_states');
        $states = is_array($states) ? $states : array();
        foreach ($states as $s => $t) {
            if ($now - $t > self::STATE_TTL) {
                unset($states[$s]);
            }
        }
        $states[$state] = $now;
        if (count($states) > 8) {
            $states = array_slice($states, -8, NULL, TRUE);
        }
        $this->CI->session->set_userdata('sso_states', $states);

        return rtrim($this->cfg('hub_url'), '/') . '/authorize.php?' . http_build_query(array(
            'client_id'    => $this->cfg('client_id'),
            'product_id'   => $this->cfg('product_id'),
            'redirect_uri' => $this->redirect_uri(),
            'state'        => $state,
        ));
    }

    /**
     * Hub URL that ends this client's single-sign-on session, then sends the browser back
     * to redirect_uri(). Call it on logout - otherwise the next visit would be signed in
     * again silently by the hub.
     */
    public function logout_url()
    {
        return rtrim($this->cfg('hub_url'), '/') . '/end_session.php?' . http_build_query(array(
            'client_id'    => $this->cfg('client_id'),
            'redirect_uri' => $this->redirect_uri(),
        ));
    }

    /** Seconds between checks of the hub session (config check_interval). 0 = never check. */
    public function check_interval()
    {
        return (int)$this->cfg('check_interval', 60);
    }

    /**
     * Ask the hub to send the Pusher "force-logout" event with this product's saved Pusher details.
     * TRUE = sent, FALSE = not sent (hub off/unreachable, no Pusher details saved, rejected) - callers fall back to local Pusher.
     */
    public function force_logout($reason = '')
    {
        if ( ! $this->enabled()) {
            return FALSE;
        }
        $ch = curl_init(rtrim($this->cfg('hub_url'), '/') . '/force_logout.php');
        curl_setopt_array($ch, array(
            CURLOPT_POST           => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_POSTFIELDS     => http_build_query(array(
                'client_id'     => $this->cfg('client_id'),
                'client_secret' => $this->cfg('client_secret'),
                'product_id'    => $this->cfg('product_id'),
                'reason'        => $reason,
            )),
        ));
        $body = curl_exec($ch);
        curl_close($ch);
        $res = ($body === FALSE) ? NULL : json_decode($body, TRUE);
        return is_array($res) && ! empty($res['sent']);
    }

    /**
     * Is the hub sign-in session behind this login still alive?
     *   TRUE  alive   FALSE ended (timeout, sign-out elsewhere, ended by an admin)
     *   NULL  could not be determined (hub unreachable) - callers should NOT log the user out
     * $touch = TRUE counts as user activity (resets the hub idle timer); FALSE only looks.
     */
    public function session_active($sid, $touch = TRUE)
    {
        if ($sid === '') {
            return NULL;
        }
        $ch = curl_init(rtrim($this->cfg('hub_url'), '/') . '/session_status.php');
        curl_setopt_array($ch, array(
            CURLOPT_POST           => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_TIMEOUT        => 4,
            CURLOPT_POSTFIELDS     => http_build_query(array(
                'client_id'     => $this->cfg('client_id'),
                'client_secret' => $this->cfg('client_secret'),
                'sid'           => $sid,
                'touch'         => $touch ? '1' : '0',
            )),
        ));
        $body = curl_exec($ch);
        $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res = ($body === FALSE) ? NULL : json_decode($body, TRUE);
        if (!is_array($res) || !array_key_exists('active', $res) || ($http !== 200 && $http !== 401)) {
            return NULL;
        }
        return (bool)$res['active'];
    }

    /** True when the state returned by the hub matches the one we issued (single use). */
    public function check_state($state)
    {
        if (!is_string($state) || $state === '') {
            return FALSE;
        }
        $states = $this->CI->session->userdata('sso_states');
        if (!is_array($states) || !isset($states[$state])) {
            return FALSE;
        }
        $issued = (int)$states[$state];
        unset($states[$state]);                       // single use
        $this->CI->session->set_userdata('sso_states', $states);
        return (time() - $issued) <= self::STATE_TTL;
    }

    /** True for the state value of the one automatic retry, so a failing retry can't loop. */
    public function is_retry_state($state)
    {
        return is_string($state) && substr($state, -1) === 'r';
    }

    /**
     * Server-to-server: trades the one-time code for the signed-in user.
     *
     * @return array ['ok'=>bool, 'error'=>string, 'user'=>['user_id','username','name','user_type']]
     */
    public function exchange_code($code)
    {
        $fail = function ($msg) { return array('ok' => FALSE, 'error' => $msg); };

        $ch = curl_init(rtrim($this->cfg('hub_url'), '/') . '/token.php');
        curl_setopt_array($ch, array(
            CURLOPT_POST           => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POSTFIELDS     => http_build_query(array(
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'client_id'     => $this->cfg('client_id'),
                'client_secret' => $this->cfg('client_secret'),
                'redirect_uri'  => $this->redirect_uri(),
            )),
        ));
        $body = curl_exec($ch);
        $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === FALSE) {
            return $fail('Cannot reach the identity hub (' . $err . ').');
        }
        $res = json_decode($body, TRUE);
        if ($http !== 200 || !is_array($res) || empty($res['access_token'])) {
            $why = (is_array($res) && isset($res['error'])) ? $res['error'] : ('HTTP ' . $http);
            return $fail('The identity hub rejected the login (' . $why . ').');
        }

        // The token came straight from the hub over the authenticated back-channel;
        // still confirm it is for this product/client and has not expired.
        $parts   = explode('.', $res['access_token']);
        $payload = (count($parts) === 3) ? json_decode(base64_decode(strtr($parts[1], '-_', '+/')), TRUE) : NULL;
        if (!is_array($payload)
            || ($payload['aud'] ?? '') !== $this->cfg('client_id')
            || ($payload['product_id'] ?? '') !== $this->cfg('product_id')
            || (int)($payload['exp'] ?? 0) < time()) {
            return $fail('The identity hub returned an invalid token.');
        }

        $user = isset($res['user']) ? $res['user'] : array();
        if (empty($user['user_id']) || empty($user['username'])) {
            return $fail('The identity hub did not return a user.');
        }

        return array('ok' => TRUE, 'user' => $user, 'sso_sid' => (string)($res['sso_sid'] ?? ''));
    }
}
