<?php

namespace Tests\Feature;

use Tests\TestCase;

class RoleBasedAuthTest extends TestCase
{
    public function test_unified_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('Email Address');
        $response->assertSee('Password');
        $response->assertDontSee('Customer Portal');
        $response->assertDontSee('Admin Portal');
    }

    public function test_admin_login_route_redirects_to_login(): void
    {
        $response = $this->get('/admin/login');
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_user_accessing_admin_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }
}
