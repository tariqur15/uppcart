<?php

use App\Models\CustomerMessage;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('live chat start creates a customer when phone is new', function () {
    $response = $this->post(route('live-chat.start'), [
        'name' => 'Rahim Uddin',
        'phone' => '+8801877774081',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true]);

    $this->assertDatabaseHas('users', [
        'name' => 'Rahim Uddin',
        'phone' => '+8801877774081',
        'role' => 'customer',
    ]);

    $customer = User::where('phone', '+8801877774081')->first();

    $this->assertTrue(session('live_chat_user_id') === $customer->id);
});

test('live chat start reuses an existing customer by phone', function () {
    $existing = User::factory()->create([
        'name' => 'Existing Customer',
        'phone' => '+8801877774081',
    ]);

    $response = $this->post(route('live-chat.start'), [
        'name' => 'New Name',
        'phone' => '+8801877774081',
    ]);

    $response->assertOk()
        ->assertJson(['ok' => true, 'name' => 'Existing Customer']);

    $this->assertSame(1, User::where('phone', '+8801877774081')->count());

    $this->assertSame('Existing Customer', $existing->fresh()->name);
});

test('live chat start requires name and phone', function () {
    $this->post(route('live-chat.start'), [])->assertSessionHasErrors(['name', 'phone']);
});

test('customer can store a message and it appears as incoming', function () {
    $customer = User::factory()->create();

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->post(route('live-chat.message'), ['message' => 'I need help with my order.'])
        ->assertOk();

    $this->assertDatabaseHas('customer_messages', [
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'I need help with my order.',
    ]);
});

test('customer can send an attachment with a message', function () {
    Storage::fake('public');

    $customer = User::factory()->create();

    $response = $this->withSession(['live_chat_user_id' => $customer->id])
        ->post(route('live-chat.message'), [
            'message' => 'Here is the file.',
            'attachment' => UploadedFile::fake()->create('receipt.txt', 100),
        ])
        ->assertOk()
        ->assertJsonPath('attachment.name', 'receipt.txt');

    $message = $customer->messages()->first();

    $this->assertSame('incoming', $message->direction);
    $this->assertNotNull($message->attachment_path);
    $this->assertSame('receipt.txt', $message->attachment_name);
    $this->assertNotNull($response->json('attachment.url'));

    Storage::disk('public')->assertExists($message->attachment_path);
});

test('customer message requires a message or an attachment', function () {
    $customer = User::factory()->create();

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->post(route('live-chat.message'), [])
        ->assertStatus(422);
});

test('messages endpoint includes attachment data for outgoing messages', function () {
    Storage::fake('public');

    $customer = User::factory()->create();

    $path = UploadedFile::fake()->create('offer.txt', 50)->store('messages', 'public');

    $message = CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'outgoing',
        'admin_id' => User::factory()->create(['role' => 'admin'])->id,
        'message' => 'Check this offer!',
        'attachment_path' => $path,
        'attachment_name' => 'offer.txt',
        'attachment_mime' => 'text/plain',
    ]);

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->get(route('live-chat.messages'))
        ->assertOk()
        ->assertJsonPath('messages.0.attachment.name', 'offer.txt')
        ->assertJsonPath('messages.0.attachment.mime', 'text/plain')
        ->assertJsonPath('messages.0.attachment.url', Storage::disk('public')->url($path));
});

test('connecting to an agent creates a message and an admin notification', function () {
    $customer = User::factory()->create();

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->post(route('live-chat.connect'))
        ->assertOk();

    $this->assertDatabaseHas('customer_messages', [
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'Customer requested to connect with a customer agent.',
    ]);

    $this->assertDatabaseHas('notifications', [
        'type' => 'chat',
        'title' => 'New Live Chat',
    ]);
});

test('picking an faq stores context for the admin', function () {
    $customer = User::factory()->create();

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->post(route('live-chat.faq'), [
            'question' => 'How long does delivery take?',
            'answer' => '2-4 days.',
        ])
        ->assertOk();

    $this->assertDatabaseHas('customer_messages', [
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'Asked: How long does delivery take?'."\n".'Bot answer: 2-4 days.',
    ]);
});

test('messages endpoint returns only new messages after the given id', function () {
    $customer = User::factory()->create();

    $first = CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'Hello',
    ]);

    CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'outgoing',
        'admin_id' => User::factory()->create(['role' => 'admin'])->id,
        'message' => 'Hi there!',
    ]);

    $this->withSession(['live_chat_user_id' => $customer->id])
        ->get(route('live-chat.messages', ['after' => $first->id]))
        ->assertOk()
        ->assertJsonPath('messages.0.message', 'Hi there!')
        ->assertJsonPath('messages.0.direction', 'outgoing');
});

test('admin can download an attachment sent by a customer', function () {
    Storage::fake('public');

    $customer = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $path = UploadedFile::fake()->create('receipt.jpg', 50)->store('messages', 'public');

    $message = CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'admin_id' => $admin->id,
        'message' => 'Here is my receipt.',
        'attachment_path' => $path,
        'attachment_name' => 'receipt.jpg',
        'attachment_mime' => 'image/jpeg',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.customers.message.attachment', $message))
        ->assertOk()
        ->assertDownload('receipt.jpg');
});

test('account activity shows a single started-conversation log instead of every message', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create();

    CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'hi there',
    ]);

    CustomerMessage::create([
        'user_id' => $customer->id,
        'direction' => 'incoming',
        'message' => 'another message',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.customers.show', $customer));

    $response->assertOk()
        ->assertSee('Started a conversation')
        ->assertDontSee('Sent a message');
});

test('account activity does not show order status changes', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create();

    $order = Order::create([
        'user_id' => $customer->id,
        'status' => 'shipped',
        'total_price' => 500,
        'payment_status' => 'unpaid',
        'address' => '123 Test St, Dhaka',
    ]);

    OrderStatusHistory::create([
        'order_id' => $order->id,
        'status' => 'shipped',
        'changed_at' => now(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.customers.show', $customer));

    $response->assertOk()
        ->assertSee('Placed order #'.$order->order_number)
        ->assertDontSee('Status changed to Shipped');
});

test('state is inactive without a chat session', function () {
    $this->get(route('live-chat.state'))->assertJson(['active' => false]);
});
