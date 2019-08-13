<?php

class IWD_Ces_Helper_Curl extends Mage_Core_Helper_Abstract
{
    const TYPE_GET = 'get';
    const TYPE_POST = 'post';

    public function post($url, $body = []) {
        return $this->send($url, self::TYPE_POST, $body);
    }
    public function get($url, $body = []) {
        return $this->send($url, self::TYPE_GET, $body);
    }

    private function send($url, $type, $body = []) {
        if ($curl = curl_init()) {
            if ($type == self::TYPE_GET) {
                $url .= http_build_query($body);
            }
            curl_setopt($curl, CURLOPT_URL, $url);
            if ($type == self::TYPE_POST) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
        }
        return $code != 200 ? null : json_decode($data, true);
    }
}