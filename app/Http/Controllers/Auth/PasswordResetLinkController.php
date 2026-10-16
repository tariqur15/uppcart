<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\PasswordSetupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function __construct(
        private readonly PasswordSetupService $passwordSetupService,
    ) {}

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => ['required', 'string'],
        ]);

        $user = User::where('phone', $request->input('phone'))->first();

        if (! $user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        if ($user->password_set && $user->password) {
            $order = Order::where('user_id', $user->id)->latest()->first();

            if (! $order) {
                return back()->withErrors(['phone' => 'No orders found. Please contact support.']);
            }

            session([
                'forgot_user_id' => $user->id,
                'forgot_question_amount' => (int) round($order->total_price, -1),
                'forgot_question_date' => $order->created_at->format('d'),
                'forgot_question_month' => $order->created_at->format('M'),
            ]);

            return redirect()->route('password.verify');
        }

        $token = $this->passwordSetupService->generateToken($user);

        return redirect()->route('password.setup', $token->token)
            ->with('success', 'Set your new password.');
    }

    public function verify(): View|RedirectResponse
    {
        if (! session('forgot_user_id')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-identity');
    }

    public function verifyAnswers(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'string'],
            'month' => ['required', 'string'],
        ]);

        $correctAmount = session('forgot_question_amount');
        $correctDate = session('forgot_question_date');
        $correctMonth = session('forgot_question_month');

        if (
            abs((int) $request->input('amount') - $correctAmount) > 50
            || $request->input('date') !== $correctDate
            || $request->input('month') !== $correctMonth
        ) {
            return back()->withErrors(['amount' => 'Incorrect answer. Please try again or contact support.']);
        }

        $user = User::find(session('forgot_user_id'));

        $token = $this->passwordSetupService->generateToken($user);

        session()->forget(['forgot_user_id', 'forgot_question_amount', 'forgot_question_date', 'forgot_question_month']);

        return redirect()->route('password.reset-form', $token->token);
    }
}
