<?php

require_once 'vendor/autoload.php';

use GuzzleHttp\Client;

class ApiClient
{
    private const JWT_TOKEN = 'JWT_TOKEN';

    /**
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAvailablePaymenentMethods(): \Psr\Http\Message\StreamInterface
    {
        $client = $this->getApiClient();

        $response = $client->request('GET', 'instrument-settings/payment-methods/available-for-application/{ID}');

        return $response->getBody();
    }

    /**
     * @return Client
     */
    private function getApiClient(): Client
    {
       return new Client([
            'base_uri' => 'https://payop.com/v1',
            'timeout' => 300.0,
            'headers' => ['Content-Type' => 'application/json', "Accept" => "application/json", 'Authorization' => "Bearer " . self::JWT_TOKEN],
            'http_errors' => false,
            'verify' => false
        ]);
    }
}
