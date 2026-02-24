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
    public function only_one_active_session_is_allowed_per_user()
    {

        config(['session.driver' => 'file']);

        $password = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $response1 = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response1->assertRedirect('/users');
        
        $session1Id = session()->getId();
        $cookieName = config('session.cookie');
        $session1Cookie = $response1->getCookie($cookieName)->getValue();

        $this->flushSession();
        
        $response2 = $this->post('/auth', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response2->assertRedirect('/users');
        
        $session2Id = session()->getId();
        $this->assertNotEquals($session1Id, $session2Id);

        $response3 = $this->withCookie($cookieName, $session1Cookie)
                          ->get('/users');

        $response3->assertRedirect('/login');
    }
}
