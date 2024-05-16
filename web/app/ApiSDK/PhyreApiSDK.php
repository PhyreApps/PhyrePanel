<?php
namespace App\ApiSDK;

use function AlibabaCloud\Client\json;

class PhyreApiSDK
{
    public $ip;
    public $port = 8443;
    public $password;
    public $username;

    public function __construct($ip, $port, $username, $password)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->password = $password;
        $this->username = $username;
    }

    public function getHostingSubscriptions()
    {
        $response = $this->sendRequest('hosting-subscriptions', [], 'GET');

        return $response;
    }

    public function getCustomers()
    {
        $response = $this->sendRequest('customers', [], 'GET');

        return $response;
    }
    public function createCustomer($data)
    {
        $response = $this->sendRequest('customers', $data, 'POST');

        return $response;
    }

    public function healthCheck()
    {
        $response = $this->sendRequest('health', [], 'GET');

        return $response;
    }

    public function sendRequest($resource, $params, $requestType)
    {
        $url = 'http://' . $this->ip . ':' . $this->port . '/api/' . $resource;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.base64_encode($this->username.':'.$this->password)
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        return $response;
    }

}
