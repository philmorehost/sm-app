<?php

namespace EduFlex\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class WhmcsApi
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/whmcs.php';

        $this->client = new Client([
            'base_uri' => $this->config['url'],
            'timeout'  => 5.0,
        ]);
    }

    /**
     * Adds a new client to WHMCS.
     *
     * @param array $clientData The client's details (firstname, lastname, email, etc.).
     * @return array The result from the WHMCS API.
     */
    public function addClient(array $clientData): array
    {
        $params = array_merge(['action' => 'AddClient'], $clientData);
        return $this->sendRequest($params);
    }

    /**
     * Adds a new order to WHMCS.
     *
     * @param array $orderData The order details (clientid, pid, domain, etc.).
     * @return array The result from the WHMCS API.
     */
    public function addOrder(array $orderData): array
    {
        $params = array_merge(['action' => 'AddOrder'], $orderData);
        return $this->sendRequest($params);
    }

    /**
     * Checks the availability of a domain name using the WHMCS API.
     *
     * @param string $domain The domain name to check.
     * @return array The result from the WHMCS API.
     */
    public function domainCheck(string $domain): array
    {
        $params = [
            'action' => 'DomainWhois', // DomainWhois is a common action name for this, equivalent to a check
            'domain' => $domain,
        ];

        return $this->sendRequest($params);
    }

    /**
     * Sends a request to the WHMCS API.
     *
     * @param array $params The parameters for the API call.
     * @return array The decoded JSON response.
     */
    private function sendRequest(array $params): array
    {
        // Add authentication details to every request
        $all_params = array_merge($params, [
            'identifier' => $this->config['identifier'],
            'secret' => $this->config['secret'],
            'responsetype' => $this->config['responsetype'],
        ]);

        try {
            $response = $this->client->post('', [
                'form_params' => $all_params
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            // In a real app, you would log this error.
            // For now, return an error structure that the frontend can handle.
            return [
                'result' => 'error',
                'message' => 'Could not connect to the domain registrar. ' . $e->getMessage(),
            ];
        }
    }
}
