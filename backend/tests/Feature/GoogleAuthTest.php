<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_sends_user_to_google(): void
    {
        Config::set('services.google.client_id', 'client-id');
        Config::set('services.google.client_secret', 'client-secret');
        Config::set('services.google.redirect', 'http://127.0.0.1:8000/auth/google/callback');

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_google_callback_creates_new_user_and_redirects_with_token(): void
    {
        Config::set('app.frontend_url', 'http://localhost:5173');

        $this->mockGoogleUser('google-123', 'new@example.com', 'New User', 'https://example.com/avatar.jpg');

        $response = $this->get('/auth/google/callback');

        $response->assertRedirectContains('http://localhost:5173/auth/google/callback?token=');

        $this->assertDatabaseHas('users', [
            'email' => 'new@example.com',
            'google_id' => 'google-123',
            'avatar_url' => 'https://example.com/avatar.jpg',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_google_callback_updates_existing_user_by_email(): void
    {
        Config::set('app.frontend_url', 'http://localhost:5173');

        User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
            'avatar_url' => null,
        ]);

        $this->mockGoogleUser('google-456', 'existing@example.com', 'Existing User', 'https://example.com/avatar2.jpg');

        $this->get('/auth/google/callback')
            ->assertRedirectContains('http://localhost:5173/auth/google/callback?token=');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'existing@example.com',
            'google_id' => 'google-456',
            'avatar_url' => 'https://example.com/avatar2.jpg',
        ]);
    }

    public function test_google_callback_reuses_existing_google_user(): void
    {
        Config::set('app.frontend_url', 'http://localhost:5173');

        User::factory()->create([
            'email' => 'google@example.com',
            'google_id' => 'google-789',
        ]);

        $this->mockGoogleUser('google-789', 'google@example.com', 'Google User', 'https://example.com/avatar3.jpg');

        $this->get('/auth/google/callback')
            ->assertRedirectContains('http://localhost:5173/auth/google/callback?token=');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    private function mockGoogleUser(string $id, string $email, string $name, string $avatar): void
    {
        $socialiteUser = new SocialiteUser;
        $socialiteUser->id = $id;
        $socialiteUser->email = $email;
        $socialiteUser->name = $name;
        $socialiteUser->avatar = $avatar;

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('stateless')->andReturnSelf();
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        $socialite = Mockery::mock(SocialiteFactory::class);
        $socialite->shouldReceive('driver')->with('google')->andReturn($provider);

        $this->app->instance(SocialiteFactory::class, $socialite);
    }
}
