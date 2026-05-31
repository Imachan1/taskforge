<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): SymfonyRedirectResponse
    {
        $frontendUrl = $this->sanitizeFrontendUrl($request->query('frontend_url'));

        $driver = Socialite::driver('google')->stateless();

        if ($frontendUrl) {
            $driver->with([
                'state' => $this->encodeState([
                    'frontend_url' => $frontendUrl,
                ]),
            ]);
        }

        return $driver->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = $this->findOrCreateUser($googleUser);
        $token = $user->createToken('taskforge-google')->plainTextToken;
        $state = $this->decodeState($request->query('state'));
        $frontendUrl = $this->sanitizeFrontendUrl($state['frontend_url'] ?? null);

        return redirect()->away($this->frontendCallbackUrl($token, $user, $frontendUrl));
    }

    private function findOrCreateUser(SocialiteUser $googleUser): User
    {
        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user && $googleUser->getEmail()) {
            $user = User::where('email', $googleUser->getEmail())->first();
        }

        if ($user) {
            $user->forceFill([
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();

            return $user;
        }

        return User::create([
            'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
            'email' => $googleUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'google_id' => $googleUser->getId(),
            'avatar_url' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
        ]);
    }

    private function frontendCallbackUrl(string $token, User $user, ?string $frontendUrl = null): string
    {
        $frontendUrl = rtrim($frontendUrl ?: config('app.frontend_url'), '/');
        $encodedUser = rtrim(strtr(base64_encode($user->toJson()), '+/', '-_'), '=');

        return "{$frontendUrl}/auth/google/callback?token={$token}&user={$encodedUser}";
    }

    private function sanitizeFrontendUrl(?string $url): ?string
    {
        if (! is_string($url) || $url === '') {
            return null;
        }

        $trimmedUrl = rtrim(trim($url), '/');

        if (! filter_var($trimmedUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parsed = parse_url($trimmedUrl);

        if (! $parsed || ! isset($parsed['scheme'], $parsed['host'])) {
            return null;
        }

        if (! in_array($parsed['scheme'], ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower($parsed['host']);

        // Restrict dynamic callback hosts to local development targets.
        if (! in_array($host, ['localhost', '127.0.0.1'], true)) {
            return null;
        }

        return $trimmedUrl;
    }

    private function encodeState(array $payload): string
    {
        return rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');
    }

    private function decodeState(?string $state): array
    {
        if (! is_string($state) || $state === '') {
            return [];
        }

        $normalized = strtr($state, '-_', '+/');
        $padded = str_pad($normalized, strlen($normalized) + ((4 - (strlen($normalized) % 4)) % 4), '=');
        $decoded = base64_decode($padded, true);

        if ($decoded === false) {
            return [];
        }

        $json = json_decode($decoded, true);

        return is_array($json) ? $json : [];
    }
}
