<?php

namespace EduFlex\Tests\Feature;

use PHPUnit\Framework\TestCase;

class SuperAdminAuthTest extends TestCase
{
    /**
     * This test simulates a user visiting the login page and checks if the
     * main elements of the page are rendered.
     *
     * @coversNothing
     * @runInSeparateProcess
     */
    public function testLoginPageRendersCorrectly()
    {
        // Simulate a GET request to the login page
        $_SERVER['REQUEST_URI'] = '/super-admin/login';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Super Admin Login', $output);
        $this->assertStringContainsString('<form action="/super-admin/login/submit" method="POST">', $output);
    }

    /**
     * @coversNothing
     * @runInSeparateProcess
     */
    public function testCreateSchoolPageRendersCorrectly()
    {
        // Note: This doesn't test authentication, just that the route loads the view.
        // In a real app, this should redirect to login if not authenticated.

        $_SERVER['REQUEST_URI'] = '/super-admin/schools/create';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        require __DIR__ . '/../../public/index.php';
        $output = ob_get_clean();

        $this->assertStringContainsString('Create a New School', $output);
        $this->assertStringContainsString('<form action="/super-admin/schools/store" method="POST">', $output);
    }
}
