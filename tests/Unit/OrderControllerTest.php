<?php

namespace EduFlex\Tests\Unit;

use EduFlex\Controllers\OrderController;
use PHPUnit\Framework\TestCase;

class OrderControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        $this->controller = new OrderController();
    }

    /**
     * @covers \EduFlex\Controllers\OrderController::checkDomain
     * @runInSeparateProcess
     */
    public function testCheckDomainReturnsErrorForEmptyDomain()
    {
        // Simulate empty request body
        $this->expectOutputString(json_encode(['result' => 'error', 'message' => 'Domain name is required.']));

        $this->controller->checkDomain();
    }

    /**
     * @covers \EduFlex\Controllers\OrderController::checkDomain
     * @runInSeparateProcess
     */
    public function testCheckDomainReturnsErrorForInvalidDomain()
    {
        // To test this properly, we need to simulate the file_get_contents('php://input')
        // This is difficult in a standard unit test.
        // A better approach would be a feature test that makes a real request.
        // For now, we will skip the deeper implementation of this test.
        $this->markTestSkipped('Requires simulating php://input which is better suited for a feature test.');
    }
}
