<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();

        $user = $this->findOrCreateUser($googleUser);
        $token = $user->createToken('taskforge-google')->plainTextToken;

        return redirect()->away($this->frontendCallbackUrl($token, $user));
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

    private function frontendCallbackUrl(string $token, User $user): string
    {
        $frontendUrl = rtrim(config('app.frontend_url'), '/');
        $encodedUser = rtrim(strtr(base64_encode($user->toJson()), '+/', '-_'), '=');

        return "{$frontendUrl}/auth/google/callback?token={$token}&user={$encodedUser}";
    }
}
