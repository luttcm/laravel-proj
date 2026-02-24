<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SingleSessionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function second_login_is_blocked_if_user_is_already_online()
    {
        config(['session.driver' => 'file']);
        config(['cache.default' => 'file']);

        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response1 = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response1->assertRedirect('/users');
        
        $this->actingAs($user)->get('/users');

        $this->flushSession();
        
        $response2 = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        
        $response2->assertStatus(302);
        $response2->assertSessionHasErrors(['email' => 'Пользователь уже находится в системе с другого устройства.']);

        $this->actingAs($user)->post('/logout');

        $response3 = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response3->assertRedirect('/users');
    }

    /** @test */
    public function old_session_is_logged_out_if_new_login_occurs_after_timeout()
    {
        config(['session.driver' => 'file']);
        config(['cache.default' => 'file']);

        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $responseA = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $sessionA = session()->getId();
        $responseA->assertRedirect('/users');

        $this->actingAs($user);
        \Illuminate\Support\Facades\Cache::forget('user_session_' . $user->id);

        $this->flushSession();
        $responseB = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $sessionB = session()->getId();
        $this->assertNotEquals($sessionA, $sessionB);
        $responseB->assertRedirect('/users');

        session()->setId($sessionA);
        $response = $this->actingAs($user)->get('/users');

        $response->assertRedirect('/auth');
        $response->assertSessionHasErrors(['email' => 'Вы вышли из системы, так как выполнен вход с другого устройства.']);
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }
}
