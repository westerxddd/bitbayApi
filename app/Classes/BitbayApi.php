<?php


namespace App\Classes;


class BitbayApi
{
    private $pubkey = '';
    private $privkey = '';

    public function __construct($pubkey, $privkey)
    {
        $this->pubkey = $pubkey;
        $this->privkey = $privkey;
    }

    public function GetUUID($data)
    {
        assert(strlen($data) == 16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data) , 4));
    }

    public function callApi($method, $params = null, $type = 'GET')
    {
        $post = null;
        if (($type == 'GET') && is_array($params)):
            $method = $method . '?query=' . urlencode(json_encode($params));
        elseif (($type == 'POST' || $type == 'PUT' || $type == 'DELETE') && is_array($params) && (count($params) > 0)):
            $post = json_encode($params);
        endif;
        $time = time();
        $sign = hash_hmac("sha512", $this->pubkey . $time . $post, $this->privkey);
        $headers = array(
            'API-Key: ' . $this->pubkey,
            'API-Hash: ' . $sign,
            'operation-id: ' . $this->GetUUID(random_bytes(16)) ,
            'Request-Timestamp: ' . $time,
            'Content-Type: application/json'
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://api.bitbay.net/rest/' . $method);
        if ($type == 'POST' || $type == 'PUT' || $type == 'DELETE') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $ret = curl_exec($curl);
        $info = curl_getinfo($curl);
        return $ret;
    }
}
