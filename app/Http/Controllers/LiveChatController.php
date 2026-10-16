<?php

namespace App\Http\Controllers;

use App\Models\CustomerMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LiveChatController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $this->normalizePhone($validated['phone']);
        $name = trim($validated['name']);

        $user = $this->findOrCreateCustomer($name, $phone);

        session(['live_chat_user_id' => $user->id]);

        return response()->json([
            'ok' => true,
            'name' => $user->name,
            'user_id' => $user->id,
        ]);
    }

    public function faq(Request $request): JsonResponse
    {
        $user = $this->chatCustomer();

        if (! $user) {
            return response()->json(['error' => 'Please start the chat first.'], 422);
        }

        $validated = $request->validate([
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        $user->messages()->create([
            'direction' => 'incoming',
            'message' => "Asked: {$validated['question']}\nBot answer: {$validated['answer']}",
        ]);

        return response()->json(['ok' => true]);
    }

    public function connect(Request $request): JsonResponse
    {
        $user = $this->chatCustomer();

        if (! $user) {
            return response()->json(['error' => 'Please start the chat first.'], 422);
        }

        $user->messages()->create([
            'direction' => 'incoming',
            'message' => 'Customer requested to connect with a customer agent.',
        ]);

        $this->notifyAdmin($user, 'wants to talk to an agent');

        return response()->json(['ok' => true]);
    }

    public function message(Request $request): JsonResponse
    {
        $user = $this->chatCustomer();

        if (! $user) {
            return response()->json(['error' => 'Please start the chat first.'], 422);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        if (! $request->filled('message') && ! $request->hasFile('attachment')) {
            return response()->json(['error' => 'Type a message or attach a file.'], 422);
        }

        $messageData = [
            'direction' => 'incoming',
            'message' => trim((string) $request->input('message', '')),
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('messages', 'public');

            $messageData += [
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_mime' => $file->getMimeType(),
                'attachment_size' => $file->getSize(),
            ];
        }

        $message = $user->messages()->create($messageData);

        $this->notifyAdmin($user, 'sent a new message');

        return response()->json([
            'ok' => true,
            'id' => $message->id,
            'attachment' => $message->attachment_path ? [
                'name' => $message->attachment_name,
                'mime' => $message->attachment_mime,
                'url' => Storage::disk('public')->url($message->attachment_path),
                'download_url' => Storage::disk('public')->url($message->attachment_path),
            ] : null,
        ]);
    }

    public function state(Request $request): JsonResponse
    {
        $user = $this->chatCustomer();

        if (! $user) {
            return response()->json(['active' => false]);
        }

        return response()->json(['active' => true, 'name' => $user->name]);
    }

    public function messages(Request $request): JsonResponse
    {
        $user = $this->chatCustomer();

        if (! $user) {
            return response()->json(['messages' => [], 'last_id' => null]);
        }

        $query = $user->messages()->orderBy('id');

        if ($after = (int) $request->input('after')) {
            $query->where('id', '>', $after);
        }

        $messages = $query->limit(100)->get();

        return response()->json([
            'messages' => $messages->map(fn (CustomerMessage $message) => [
                'id' => $message->id,
                'direction' => $message->direction,
                'message' => $message->message,
                'created_at' => $message->created_at->format('M d, g:i A'),
                'attachment' => $message->attachment_path ? [
                    'name' => $message->attachment_name,
                    'mime' => $message->attachment_mime,
                    'url' => Storage::disk('public')->url($message->attachment_path),
                    'download_url' => Storage::disk('public')->url($message->attachment_path),
                ] : null,
            ]),
            'last_id' => $messages->last()?->id,
        ]);
    }

    private function chatCustomer(): ?User
    {
        $userId = auth()->id() ?? session('live_chat_user_id');

        if (! $userId) {
            return null;
        }

        return User::find($userId);
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^\d+]/', '', $phone);
    }

    private function findOrCreateCustomer(string $name, string $phone): User
    {
        return DB::transaction(function () use ($name, $phone) {
            $user = User::where('phone', $phone)->first();

            if ($user) {
                return $user;
            }

            return User::create([
                'name' => $name,
                'phone' => $phone,
                'email' => null,
                'password' => null,
                'password_set' => false,
                'role' => 'customer',
            ]);
        });
    }

    private function notifyAdmin(User $user, string $action): void
    {
        $phone = $user->phone ? ' · '.substr($user->phone, -4) : '';

        Notification::create([
            'type' => 'chat',
            'title' => 'New Live Chat',
            'message' => $user->name.$phone.' '.$action,
            'url' => route('admin.customers.show', $user),
        ]);
    }
}
