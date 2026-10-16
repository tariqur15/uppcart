<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedEntry;
use App\Models\CustomerMessage;
use App\Models\Order;
use App\Models\ProductView;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $view = $request->query('view', 'ordering');
        $view = in_array($view, ['all', 'ordering', 'chat']) ? $view : 'ordering';

        $baseQuery = fn ($query) => $query
            ->withCount(['orders as total_orders'])
            ->withCount(['orders as cancelled_orders' => fn ($q) => $q->where('status', 'cancelled')])
            ->withCount(['orders as pending_orders' => fn ($q) => $q->where('status', 'pending')])
            ->withCount(['messages as unread_message_count' => fn ($q) => $q->where('direction', 'incoming')->whereNull('read_at')])
            ->addSelect(['total_spent' => Order::selectRaw('COALESCE(SUM(total_price), 0)')
                ->whereColumn('user_id', 'users.id')
                ->where('status', '!=', 'cancelled'),
            ])
            ->with('latestOrder');

        $applyView = function ($query) use ($view) {
            return match ($view) {
                'chat' => $query->whereHas('messages'),
                'all' => $query->where(fn ($q) => $q->whereHas('orders')->orWhereHas('messages')),
                default => $query->whereHas('orders'),
            };
        };

        $customers = $applyView($baseQuery(User::query()))
            ->latest()
            ->paginate(20);

        $tagFor = fn ($user) => match (true) {
            $user->total_orders > 0 && $user->cancelled_orders / $user->total_orders > 0.4 => 'flagged',
            ($user->total_spent ?? 0) >= 10000 => 'vip',
            $user->total_orders > 1 => 'repeat',
            $user->total_orders > 0 => 'new',
            default => 'chat',
        };

        $customers->getCollection()->transform(function ($user) use ($tagFor) {
            $lastOrder = $user->latestOrder;

            $user->tag = $tagFor($user);

            $user->last_order_iso = $lastOrder?->created_at?->format('Y-m-d');
            $user->last_order_date = $lastOrder?->created_at?->format('M d, Y');
            $user->last_order_time = $lastOrder?->created_at?->format('g:i A');
            $user->city = $lastOrder?->division;
            $user->district = $lastOrder?->district;
            $user->thana = $lastOrder?->thana;

            return $user;
        });

        $geoData = json_decode(File::get(storage_path('app/bangladesh-geo-lean.json')), true);

        $cities = collect($geoData)->pluck('name')->values();

        $districts = collect($geoData)->flatMap(fn ($d) => collect($d['districts'])->pluck('name'))->values();

        $thanas = collect($geoData)->flatMap(
            fn ($d) => collect($d['districts'])->flatMap(fn ($dist) => $dist['upazilas'])
        )->values();

        $allUsers = $applyView($baseQuery(User::query()))
            ->get()
            ->map(function ($u) use ($tagFor) {
                $u->tag = $tagFor($u);

                return $u;
            });

        $stats = [
            'all' => $allUsers->count(),
            'chat' => $allUsers->filter(fn ($u) => $u->tag === 'chat')->count(),
            'new' => $allUsers->filter(fn ($u) => $u->tag === 'new')->count(),
            'repeat' => $allUsers->filter(fn ($u) => $u->tag === 'repeat' || ($u->tag === 'vip' && $u->total_orders > 1))->count(),
            'vip' => $allUsers->filter(fn ($u) => $u->tag === 'vip')->count(),
            'flagged' => $allUsers->filter(fn ($u) => $u->tag === 'flagged')->count(),
        ];

        $blockedPhones = BlockedEntry::where('type', 'phone')->pluck('value')->toArray();
        $blockedIps = BlockedEntry::where('type', 'ip')->pluck('value')->toArray();

        $stats['blocked'] = $allUsers->filter(fn ($u) => in_array($u->phone, $blockedPhones))->count();

        return view('admin.customers.index', compact('customers', 'stats', 'cities', 'districts', 'thanas', 'blockedPhones', 'blockedIps', 'view'));
    }

    public function show(User $user): View
    {
        $orders = $user->orders()->latest()->paginate(20);
        $latestIp = $user->orders()->whereNotNull('ip_address')->latest()->value('ip_address');
        $isBlocked = BlockedEntry::where('type', 'phone')->where('value', $user->phone)->exists();

        $totalSpent = $user->orders()->where('status', '!=', 'cancelled')->sum('total_price');
        $totalOrders = $user->orders()->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $pendingOrders = $user->orders()->whereIn('status', ['pending', 'confirmed'])->count();
        $avgOrder = $completedOrders > 0 ? $totalSpent / $completedOrders : 0;

        $addresses = $user->orders()
            ->whereNotNull('address')
            ->selectRaw('MAX(id) as id, first_name, last_name, phone, address, division, district, thana, address_type')
            ->groupBy('first_name', 'last_name', 'phone', 'address', 'division', 'district', 'thana', 'address_type')
            ->get();

        $paymentBreakdown = $user->orders()
            ->whereNotNull('payment_method')
            ->selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        $loyaltyTiers = [
            'Platinum' => 50000,
            'Gold' => 20000,
            'Silver' => 5000,
            'Bronze' => 0,
        ];

        $loyaltyTier = 'Bronze';
        $nextTierName = null;
        $nextTierAmount = 0;

        foreach ($loyaltyTiers as $tier => $threshold) {
            if ($totalSpent >= $threshold) {
                $loyaltyTier = $tier;
                break;
            }

            $nextTierName = $tier;
            $nextTierAmount = $threshold;
        }

        $loyaltyPoints = (int) floor($totalSpent / 25);
        $pointsToNext = $nextTierName ? max(0, (int) ceil(($nextTierAmount - $totalSpent) / 25)) : 0;

        $tierFlow = collect(array_reverse($loyaltyTiers, true))->map(
            fn ($threshold, $tier) => [
                'name' => $tier,
                'threshold' => $threshold,
                'reached' => $totalSpent >= $threshold,
                'current' => $tier === $loyaltyTier,
            ]
        )->values();

        $currentTierIndex = $tierFlow->search(fn ($tier) => $tier['current']);
        $tierSegmentCount = $tierFlow->count() - 1;

        if ($nextTierName && $currentTierIndex !== false && $tierSegmentCount > 0) {
            $currentThreshold = $tierFlow[$currentTierIndex]['threshold'];
            $nextThreshold = $tierFlow[$currentTierIndex + 1]['threshold'];
            $withinSegment = $nextThreshold > $currentThreshold
                ? (($totalSpent - $currentThreshold) / ($nextThreshold - $currentThreshold))
                : 0;

            $tierProgress = (($currentTierIndex + min(1, max(0, $withinSegment))) / $tierSegmentCount) * 100;
        } else {
            $tierProgress = 100;
        }

        $user->messages()
            ->where('direction', 'incoming')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $user->messages()->latest()->limit(50)->get()->reverse()->values();

        $activity = collect();

        $activity->push([
            'time' => $user->created_at,
            'type' => 'registered',
            'label' => 'Account created',
            'detail' => 'Joined the store',
        ]);

        $user->orders()->latest()->get()->each(function ($order) use ($activity) {
            $activity->push([
                'time' => $order->created_at,
                'type' => 'order',
                'label' => 'Placed order #'.$order->order_number,
                'detail' => ucfirst($order->status).' · ৳'.number_format($order->total_price, 2),
            ]);
        });

        $firstMessage = $user->messages()
            ->where('direction', 'incoming')
            ->oldest('created_at')
            ->first();

        if ($firstMessage) {
            $activity->push([
                'time' => $firstMessage->created_at,
                'type' => 'message',
                'label' => 'Started a conversation',
                'detail' => null,
            ]);
        }

        $user->reviews()->latest()->get()->each(function ($review) use ($activity) {
            $activity->push([
                'time' => $review->created_at,
                'type' => 'review',
                'label' => 'Reviewed a product',
                'detail' => str_repeat('★', $review->rating).' '.$review->product?->name,
            ]);
        });

        DB::table('sessions')->where('user_id', $user->id)->get()->each(function ($session) use ($activity) {
            $browser = ProductView::parseUserAgent($session->user_agent ?? '')['browser'];

            $activity->push([
                'time' => Carbon::createFromTimestamp($session->last_activity),
                'type' => 'login',
                'label' => 'Logged in',
                'detail' => $browser.' · '.($session->ip_address ?? 'Unknown IP'),
            ]);
        });

        $activity = $activity->sortByDesc('time')->values();

        $lastLogin = DB::table('sessions')->where('user_id', $user->id)->max('last_activity');
        $lastLogin = $lastLogin ? Carbon::createFromTimestamp($lastLogin) : null;

        return view('admin.customers.show', compact(
            'user', 'orders', 'latestIp', 'isBlocked',
            'totalSpent', 'totalOrders', 'completedOrders', 'pendingOrders', 'avgOrder', 'addresses',
            'paymentBreakdown', 'loyaltyTier', 'loyaltyPoints', 'pointsToNext', 'nextTierName', 'nextTierAmount',
            'messages', 'activity', 'lastLogin', 'tierFlow', 'tierProgress',
        ));
    }

    public function sendMessage(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:20480'],
        ]);

        if (! $validated['message'] && ! $request->hasFile('attachment')) {
            return back()->withErrors(['message' => 'Type a message or attach a file.']);
        }

        $messageData = [
            'admin_id' => $request->user()->id,
            'direction' => 'outgoing',
            'message' => $validated['message'] ?? '',
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

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $message->id,
                'message' => $message->message,
                'direction' => $message->direction,
                'created_at' => $message->created_at->format('M d, g:i A'),
                'attachment' => $message->attachment_path ? [
                    'name' => $message->attachment_name,
                    'mime' => $message->attachment_mime,
                    'url' => Storage::disk('public')->url($message->attachment_path),
                    'download_url' => route('admin.customers.message.attachment', $message),
                ] : null,
            ], 201);
        }

        return back()->with('success', 'Message sent to '.$user->name.'.');
    }

    public function pollMessages(Request $request, User $user): JsonResponse
    {
        $after = (int) $request->input('after', 0);

        $messages = $user->messages()
            ->where('id', '>', $after)
            ->orderBy('id')
            ->limit(100)
            ->get();

        $user->messages()
            ->where('direction', 'incoming')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages->map(function (CustomerMessage $message) {
                return [
                    'id' => $message->id,
                    'direction' => $message->direction,
                    'message' => $message->message,
                    'created_at' => $message->created_at->format('M d, g:i A'),
                    'attachment' => $message->attachment_path ? [
                        'name' => $message->attachment_name,
                        'mime' => $message->attachment_mime,
                        'url' => Storage::disk('public')->url($message->attachment_path),
                        'download_url' => route('admin.customers.message.attachment', $message),
                    ] : null,
                ];
            }),
            'last_id' => $messages->last()?->id,
        ]);
    }

    public function downloadAttachment(CustomerMessage $message)
    {
        abort_if(! $message->attachment_path, 404);

        if (! Storage::disk('public')->exists($message->attachment_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('public')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }

    public function edit(User $user): View
    {
        return view('admin.customers.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'required|string|max:20|unique:users,phone,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        return to_route('admin.customers.show', $user)->with('success', 'Customer profile updated.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        BlockedEntry::blockPhone($user->phone, $request->user()->id);

        $latestIp = $user->orders()->whereNotNull('ip_address')->latest()->value('ip_address');
        if ($latestIp) {
            BlockedEntry::blockIp($latestIp, $request->user()->id);
        }

        return back()->with('success', "{$user->name} has been blocked.");
    }

    public function restore(User $user): RedirectResponse
    {
        BlockedEntry::unblockPhone($user->phone);

        $latestIp = $user->orders()->whereNotNull('ip_address')->latest()->value('ip_address');
        if ($latestIp) {
            BlockedEntry::unblockIp($latestIp);
        }

        return back()->with('success', "{$user->name} has been unblocked.");
    }

    public function export(): StreamedResponse
    {
        $users = User::whereHas('orders')
            ->withCount(['orders as total_orders', 'orders as cancelled_orders' => fn ($q) => $q->where('status', 'cancelled')])
            ->withSum(['orders' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'total_price', 'total_spent')
            ->with('latestOrder')
            ->latest()
            ->get();

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Email', 'Phone', 'City', 'District', 'Thana', 'Total Orders', 'Cancelled', 'Total Spent', 'Last Order', 'Joined']);

            foreach ($users as $user) {
                $last = $user->latestOrder;
                fputcsv($handle, [
                    $user->name,
                    $user->email,
                    $user->phone,
                    $last?->division,
                    $last?->district,
                    $last?->thana,
                    $user->total_orders,
                    $user->cancelled_orders,
                    number_format($user->total_spent ?? 0, 2),
                    $last?->created_at?->format('M d, Y'),
                    $user->created_at->format('M d, Y'),
                ]);
            }

            fclose($handle);
        };

        $filename = 'customers-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv']);
    }
}
