<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetPasswordRequest;
use App\Services\PasswordSetupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordSetupController extends Controller
{
    public function __construct(
        private readonly PasswordSetupService $passwordSetupService,
    ) {}

    public function show(string $token): View|RedirectResponse
    {
        $tokenModel = $this->passwordSetupService->validateToken($token);

        if (! $tokenModel) {
            abort(404, 'This password setup link is invalid or has expired.');
        }

        return view('auth.set-password', ['token' => $token]);
    }

    public function store(SetPasswordRequest $request, string $token): RedirectResponse
    {
        $tokenModel = $this->passwordSetupService->validateToken($token);

        if (! $tokenModel) {
            abort(404, 'This password setup link is invalid or has expired.');
        }

        $user = $tokenModel->user;
        $user->update([
            'password' => Hash::make($request->validated('password')),
            'password_set' => true,
        ]);

        $this->passwordSetupService->markUsed($tokenModel);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Your password has been set successfully. Welcome!');
    }
}
