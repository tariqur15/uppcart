<?php

use App\Models\Notification;
use App\Models\User;

test('notification index groups order and chat notifications', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Notification::create([
        'type' => 'new_order',
        'title' => 'New Order Received',
        'message' => 'Order #12345 - Tk 500.00',
        'url' => '/admin/orders/1',
    ]);

    Notification::create([
        'type' => 'chat',
        'title' => 'New Live Chat',
        'message' => 'Rahim sent a new message',
        'url' => '/admin/customers/1',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.notifications.index'));

    $response->assertOk()
        ->assertJsonStructure([
            'orders' => [],
            'messages' => [],
            'unread_count',
            'pending_orders',
        ]);

    $data = $response->json();

    $this->assertCount(1, $data['orders']);
    $this->assertSame('new_order', $data['orders'][0]['type']);

    $this->assertCount(1, $data['messages']);
    $this->assertSame('chat', $data['messages'][0]['type']);
});

test('notification index returns an unread count', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    Notification::create([
        'type' => 'chat',
        'title' => 'New Live Chat',
        'message' => 'Rahim wants to talk to an agent',
        'url' => '/admin/customers/1',
    ]);

    $response = $this->actingAs($admin)->getJson(route('admin.notifications.index'));

    $response->assertOk()
        ->assertJsonPath('unread_count', 1);
});
