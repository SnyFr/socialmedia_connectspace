<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_failed_login_does_not_flash_password_back_to_the_session(): void
    {
        $email = 'unknown@example.test';

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $email,
                'password' => 'private-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHas('_old_input.email', $email)
            ->assertSessionMissing('_old_input.password');
    }
}
