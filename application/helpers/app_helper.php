<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('format_rupiah')) {
    function format_rupiah($nominal)
    {
        return 'Rp ' . number_format((int)$nominal, 0, ',', '.');
    }
}

if (!function_exists('send_telegram_notification')) {
    function send_telegram_notification($chat_id, $message)
    {
        $token = getenv('CAFEEID_TELEGRAM_BOT_TOKEN');
        if (empty($token) || empty($chat_id) || empty($message) || !function_exists('curl_init')) {
            return false;
        }

        $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage');
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            )),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
