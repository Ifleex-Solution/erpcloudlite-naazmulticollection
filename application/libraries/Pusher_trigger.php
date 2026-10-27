<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pusher_trigger
{
    private $app_id;
    private $key;
    private $secret;
    private $cluster;
    public  $application;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('pusher', true);
        $this->app_id      = $CI->config->item('pusher_app_id',  'pusher');
        $this->key         = $CI->config->item('pusher_key',     'pusher');
        $this->secret      = $CI->config->item('pusher_secret',  'pusher');
        $this->cluster     = $CI->config->item('pusher_cluster', 'pusher');
        $this->application = $CI->config->item('application',    'pusher');
    }

    // Trigger an event on a Pusher channel
    public function trigger($channel, $event, $data = [])
    {
        $body      = json_encode(['name' => $event, 'channel' => $channel, 'data' => json_encode($data)]);
        $timestamp = time();
        $path      = '/apps/' . $this->app_id . '/events';

        $string_to_sign = implode("\n", [
            'POST',
            $path,
            $this->_query_string($body, $timestamp),
        ]);

        $signature = hash_hmac('sha256', $string_to_sign, $this->secret);

        $url = 'https://api-' . $this->cluster . '.pusher.com' . $path
             . '?' . $this->_query_string($body, $timestamp)
             . '&auth_signature=' . $signature;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function _query_string($body, $timestamp)
    {
        return http_build_query([
            'auth_key'       => $this->key,
            'auth_timestamp' => $timestamp,
            'auth_version'   => '1.0',
            'body_md5'       => md5($body),
        ]);
    }
}
