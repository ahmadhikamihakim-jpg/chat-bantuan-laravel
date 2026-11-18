<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_keamanan_ada_di_respons(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_dibatasi_setelah_lima_kali_gagal(): void
    {
        User::factory()->create(['email' => 'korban@uji.test', 'password' => 'kata-sandi-benar']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'korban@uji.test',
                'password' => 'tebakan-salah-' . $i,
            ])->assertRedirect();
        }

        // Percobaan ke-6 dalam menit yang sama harus ditolak 429 (Too Many Requests)
        $this->post('/login', [
            'email' => 'korban@uji.test',
            'password' => 'kata-sandi-benar',
        ])->assertStatus(429);
    }

    public function test_login_benar_tetap_bisa_sebelum_batas(): void
    {
        User::factory()->create(['email' => 'aman@uji.test', 'password' => 'kata-sandi-benar']);

        $this->post('/login', [
            'email' => 'aman@uji.test',
            'password' => 'kata-sandi-benar',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }
}
