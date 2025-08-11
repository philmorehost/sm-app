<?php

namespace EduFlex\Tests\Unit;

use EduFlex\Core\WhmcsApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WhmcsApiTest extends TestCase
{
    /**
     * @covers \EduFlex\Core\WhmcsApi::domainCheck
     */
    public function testDomainCheckSendsCorrectParameters()
    {
        // 1. Create a mock handler for Guzzle
        $mock = new MockHandler([
            new Response(200, ['Content-Type' => 'application/json'], json_encode(['result' => 'success', 'status' => 'available']))
        ]);
        $handlerStack = HandlerStack::create($mock);
        $mockClient = new Client(['handler' => $handlerStack]);

        // 2. Create an instance of our API class
        $whmcsApi = new WhmcsApi();

        // 3. Use Reflection to replace the private client with our mock client
        $reflection = new \ReflectionClass($whmcsApi);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($whmcsApi, $mockClient);

        // 4. Call the method we want to test
        $domain = 'example.com';
        $whmcsApi->domainCheck($domain);

        // 5. Assert that the request was made with the correct parameters
        $transaction = $mock->getLastRequest();
        $this->assertNotNull($transaction, "No request was made.");

        $this->assertEquals('POST', $transaction->getMethod());

        parse_str($transaction->getBody()->getContents(), $formParams);

        $config = require __DIR__ . '/../../config/whmcs.php';

        $this->assertEquals('DomainWhois', $formParams['action']);
        $this->assertEquals($domain, $formParams['domain']);
        $this->assertEquals($config['identifier'], $formParams['identifier']);
        $this->assertEquals($config['secret'], $formParams['secret']);
        $this->assertEquals('json', $formParams['responsetype']);
    }
}
