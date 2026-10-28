<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_screen(): void
    {
        $this->get('/orders')->assertRedirect(route('login'));
    }

    public function test_the_root_path_redirects_into_the_order_list(): void
    {
        $this->get('/')->assertRedirect('/orders');
    }
}