<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /** @test */
    public function it_should_login_user()
    {
        $user = factory(User::class)->create([
            'password' => bcrypt($password = '123456'),
        ]);

        $this->post('/login', [
                'email' => $user->email,
                'password' => $password,
            ])
            ->assertRedirect('/');

        $this->assertAuthenticated();
    }

    /** @test */
    public function it_should_register_user()
    {
        $user = factory(User::class)->make();

        $this->post('/register', array_merge($user->toArray(), [
                'password' => $password = '12345678',
                'password_confirmation' => $password,
            ]))
            ->assertRedirect('/')
            ->assertSessionHasNoErrors();

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => $user->email,
        ]);
    }
}
