<?php

namespace EduFlex\Core;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class PaystackApi
{
    private $client;
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/paystack.php';

        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'timeout'  => 15.0,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config['secret_key'],
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ]
        ]);
    }

    /**
     * Initializes a transaction.
     *
     * @param string $email The customer's email.
     * @param int $amount The amount in kobo (NGN) or the lowest currency unit.
     * @param string $reference A unique reference for the transaction.
     * @return array The result from the Paystack API.
     */
    public function initializeTransaction(string $email, int $amount, string $reference): array
    {
        $params = [
            'email' => $email,
            'amount' => $amount,
            'reference' => $reference,
        ];

        return $this->sendRequest('POST', '/transaction/initialize', $params);
    }

    /**
     * Verifies a transaction.
     *
     * @param string $reference The reference of the transaction to verify.
     * @return array The result from the Paystack API.
     */
    public function verifyTransaction(string $reference): array
    {
        return $this->sendRequest('GET', '/transaction/verify/' . $reference);
    }

    /**
     * Sends a request to the Paystack API.
     *
     * @param string $method The HTTP method (e.g., 'POST', 'GET').
     * @param string $uri The API endpoint URI.
     * @param array $params The parameters for the API call.
     * @return array The decoded JSON response.
     */
    private function sendRequest(string $method, string $uri, array $params = []): array
    {
        try {
            $options = [];
            if (!empty($params)) {
                $options['json'] = $params;
            }

            $response = $this->client->request($method, $uri, $options);

            return json_decode($response->getBody()->getContents(), true);

        } catch (GuzzleException $e) {
            // In a real app, log the full error.
            return [
                'status' => false,
                'message' => 'Could not connect to the payment gateway. ' . $e->getMessage(),
            ];
        }
    }
}
