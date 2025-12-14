<?php 
class Curl {
    private $system;
    private $traffic;
    public function __construct() {
        $this->system = new System;
        $this->traffic = new Traffic();
    }

    public function getHeaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public function request($url, $data = null, $method = 'POST', $ssl = false, $contentType = 'json') {

        if($contentType == 'json') {
            $type = "Content-Type: application/json";
        } else if ($contentType == 'urlencoded') {
            $type = "Content-type: application/x-www-form-urlencoded";
        } else if ($contentType == "multipart") {
            $type = "Content-Type: multipart/form-data";
        } else {
            $type = "";
        }

        $headers = [
            "Origin: ".$this->system->App->url,
            $type,
        ];
        
         // Use cURL to send the POST request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $ssl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        $result === false ? $status = 'false' : $status = 'true';
        $parsed = (object)parse_url($url);
        $requestURL = $parsed->scheme . '://' . $parsed->host;

        $this->system->App->url == $requestURL  ? $traffic = 'inbound' : $traffic = 'outbound';

        return array(
            'response' => $result,
            'headers' => json_encode(curl_getinfo($ch)),
            'body' => json_encode($data),
            'request_method' => $method,
            'url' => $url,
            'traffic' => $traffic,
            'status' => $status
        );
    }

    public function callback($url, $headers, $body, $response = NULL, $method = 'POST', $status= false) {
        $body = str_replace(["\n", " "], "", json_encode(json_decode($body), JSON_UNESCAPED_SLASHES));
        $parsed = (object)parse_url($url);
        $requestURL = $parsed->scheme . '://' . $parsed->host;

        $this->system->App->url == $requestURL  ? $traffic = 'inbound' : $traffic = 'outbound';
        return $this->traffic->requestAPI($traffic, $url, $method, $headers, $body, $response, $status);
    }
}