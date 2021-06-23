<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    const PASSWORD = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * CSRF接続テスト
     * 
     * @return void
     */
    public function test_csrfCookie()
    {
        $response = $this->get('/api/csrf-cookie');

        $response->assertStatus(204);
    }

    /**
     * アカウント登録
     * 
     * @return void
     */
    public function test_register()
    {
        $form = [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => $this::PASSWORD,
            'password_confirmation' => $this::PASSWORD,
        ];

        $response = $this->postJson('/api/register', $form);
        $response->assertStatus(201);
    }

    /**
     * ログイン成功
     * 
     * @return void
     */
    public function test_login_ok()
    {
        $user = User::factory()->create();

        $credentials = [
            'email' => $user['email'],
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJson([
                'two_factor' => false,
            ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * ログイン失敗(アカウントなし)
     * 
     * @return void
     */
    public function test_login_ng1()
    {
        $credentials = [
            'email' => $this->faker->unique()->safeEmail(),
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus(422);

        $this->assertSame('The given data was invalid.', $response['message']);
        $this->assertSame('認証に失敗しました', $response['errors']['email'][0]);
    }

    /**
     * ログイン失敗(アカウントあり + メール未確認)
     * 
     * @return void
     */
    public function test_login_ng2()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $credentials = [
            'email' => $user['email'],
            'password' => 'password',
        ];

        // loginはverifyミドルウェアがないので成功する
        $response = $this->postJson('/api/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJson([
                'two_factor' => false,
            ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->getJson('/api/user');
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Your email address is not verified.',
            ]);
    }

    /**
     * ログイン失敗(ログイン済み)
     * 
     * @return void
     */
    public function test_login_ng3()
    {
        $user = User::factory()->create();

        $credentials = [
            'email' => $user['email'],
            'password' => 'password',
        ];

        $response = $this->postJson('/api/login', $credentials);
        $response->assertStatus(200);

        $this->assertAuthenticatedAs($user);

        $response = $this->postJson('/api/login', $credentials);
        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Already authenticated.',
            ]);

        // すでに認証済みなのにUserが認証できてないっていう
        $response = $this->getJson('/api/user');
        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * メール確認
     * 
     * @return void
     */
    public function test_email_verify()
    {
        // $response = $this->postJson('/api/email/verification-notification');
        $user = User::factory()->create(['email_verified_at' => null]);
        $uri = URL::temporarySignedRoute(
                'custom.verify',
                Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

        $this->assertSame(null, $user->email_verified_at);
        $this->actingAs($user)->get($uri);
        $this->assertNotNull($user->email_verified_at);

        $this->assertAuthenticatedAs($user);

        $response = $this->getJson('/api/user');
        $response->assertStatus(200);
    }

    /**
     * response debug
     */
    private function debug($response) {
        $data = json_decode($response->getContent(), true);
        \Log::debug(print_r($data, true));
    }
}
