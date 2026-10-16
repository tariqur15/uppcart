<?php

namespace App\Services;

use App\Models\PasswordSetupToken;
use App\Models\User;
use Illuminate\Support\Str;

class PasswordSetupService
{
    public function generateToken(User $user): PasswordSetupToken
    {
        $user->passwordSetupTokens()->delete();

        return $user->passwordSetupTokens()->create([
            'token' => Str::random(64),
            'expires_at' => now()->addMinutes(30),
        ]);
    }

    public function validateToken(string $token): ?PasswordSetupToken
    {
        $tokenModel = PasswordSetupToken::where('token', $token)->first();

        if (! $tokenModel || ! $tokenModel->isValid()) {
            return null;
        }

        return $tokenModel;
    }

    public function markUsed(PasswordSetupToken $token): void
    {
        $token->update(['used_at' => now()]);
    }
}
