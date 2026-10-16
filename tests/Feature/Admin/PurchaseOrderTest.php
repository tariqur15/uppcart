<?php

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->supplier = Supplier::factory()->create();
});

it('guest cannot access purchase orders', function () {
    $this->get(route('admin.purchase-orders.index'))->assertRedirect(route('login'));
});

it('admin can list purchase orders', function () {
    $purchaseOrder = PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => PurchaseOrder::STATUS_PENDING,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.purchase-orders.index'))
        ->assertSuccessful()
        ->assertSee($purchaseOrder->order_number)
        ->assertSee($this->supplier->name);
});

it('admin can filter purchase orders by status', function () {
    PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => PurchaseOrder::STATUS_PENDING,
    ]);
    $cancelled = PurchaseOrder::factory()->cancelled()->create([
        'supplier_id' => $this->supplier->id,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.purchase-orders.index', ['status' => PurchaseOrder::STATUS_CANCELLED]))
        ->assertSuccessful()
        ->assertSee($cancelled->order_number)
        ->assertDontSee(PurchaseOrder::firstWhere('status', PurchaseOrder::STATUS_PENDING)->order_number);
});

it('admin can create a purchase order with items', function () {
    $product = Product::factory()->create([
        'name' => 'Fancy Widget',
        'cost_price' => 100,
        'stock' => 10,
    ]);

    $response = $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'expected_date' => now()->addDays(7)->format('Y-m-d'),
            'notes' => 'Urgent restock',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3, 'unit_cost' => 100],
                ['product_id' => $product->id, 'quantity' => 2, 'unit_cost' => 90],
            ],
        ]);

    $this->assertDatabaseHas('purchase_orders', [
        'supplier_id' => $this->supplier->id,
        'status' => PurchaseOrder::STATUS_PENDING,
        'total_amount' => 480,
        'notes' => 'Urgent restock',
    ]);

    $purchaseOrder = PurchaseOrder::where('supplier_id', $this->supplier->id)->firstOrFail();

    expect($purchaseOrder->order_number)->toStartWith('PO-');
    expect($purchaseOrder->items)->toHaveCount(2);
    expect($purchaseOrder->items->first()->product_name)->toBe('Fancy Widget');

    $this->assertDatabaseHas('purchase_order_items', [
        'purchase_order_id' => $purchaseOrder->id,
        'product_id' => $product->id,
        'quantity' => 3,
        'unit_cost' => 100,
        'subtotal' => 300,
    ]);

    $response->assertRedirect(route('admin.purchase-orders.show', $purchaseOrder));
});

it('validates purchase order creation', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.store'), [
            'items' => [],
        ])
        ->assertSessionHasErrors(['supplier_id', 'items']);

    $this->assertDatabaseCount('purchase_orders', 0);
});

it('admin can search products via ajax', function () {
    Product::factory()->create(['name' => 'Searchable Widget', 'sku' => 'SW-001']);
    Product::factory()->create(['name' => 'Other Thing']);

    $response = $this->actingAs($this->admin)
        ->getJson(route('admin.purchase-orders.search-products', ['q' => 'Searchable']));

    $response->assertSuccessful();
    expect($response->json())->toHaveCount(1);
    expect($response->json()[0]['name'])->toBe('Searchable Widget');
});

it('admin can view a purchase order', function () {
    $product = Product::factory()->create(['name' => 'Viewable Product']);
    $purchaseOrder = PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
        'total_amount' => 500,
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 5,
        'unit_cost' => 100,
        'subtotal' => 500,
    ]);

    $this->actingAs($this->admin)
        ->get(route('admin.purchase-orders.show', $purchaseOrder))
        ->assertSuccessful()
        ->assertSee($purchaseOrder->order_number)
        ->assertSee($this->supplier->name)
        ->assertSee('Viewable Product');
});

it('creating a purchase order does not change stock while pending', function () {
    $product = Product::factory()->create(['stock' => 10, 'cost_price' => 50]);

    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.store'), [
            'supplier_id' => $this->supplier->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 50],
            ],
        ])
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
});

it('pending purchase order can be cancelled without touching stock', function () {
    $product = Product::factory()->create(['stock' => 7]);
    $purchaseOrder = PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => PurchaseOrder::STATUS_PENDING,
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 3,
        'unit_cost' => 20,
        'subtotal' => 60,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.cancel', $purchaseOrder))
        ->assertSessionHas('success');

    $this->assertEquals(PurchaseOrder::STATUS_CANCELLED, $purchaseOrder->fresh()->status);
    $this->assertEquals(7, $product->fresh()->stock);
});

it('receiving a purchase order increments product stock and records received_at', function () {
    $product = Product::factory()->create(['stock' => 10, 'cost_price' => 100]);
    $purchaseOrder = PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
        'status' => PurchaseOrder::STATUS_PENDING,
    ]);
    $purchaseOrder->items()->create([
        'product_id' => $product->id,
        'product_name' => $product->name,
        'quantity' => 5,
        'unit_cost' => 100,
        'subtotal' => 500,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.receive', $purchaseOrder))
        ->assertSessionHas('success');

    $purchaseOrder->refresh();

    $this->assertEquals(PurchaseOrder::STATUS_RECEIVED, $purchaseOrder->status);
    $this->assertNotNull($purchaseOrder->received_at);
    $this->assertEquals(15, $product->fresh()->stock);
});

it('cannot receive or cancel an order that is no longer pending', function () {
    $purchaseOrder = PurchaseOrder::factory()->received()->create([
        'supplier_id' => $this->supplier->id,
    ]);

    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.receive', $purchaseOrder))
        ->assertSessionHas('error');

    $this->actingAs($this->admin)
        ->post(route('admin.purchase-orders.cancel', $purchaseOrder))
        ->assertSessionHas('error');

    $this->assertEquals(PurchaseOrder::STATUS_RECEIVED, $purchaseOrder->fresh()->status);
});

it('received purchase order cannot be deleted', function () {
    $purchaseOrder = PurchaseOrder::factory()->received()->create([
        'supplier_id' => $this->supplier->id,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.purchase-orders.destroy', $purchaseOrder))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('purchase_orders', ['id' => $purchaseOrder->id]);
});

it('pending purchase order can be deleted', function () {
    $purchaseOrder = PurchaseOrder::factory()->create([
        'supplier_id' => $this->supplier->id,
    ]);

    $this->actingAs($this->admin)
        ->delete(route('admin.purchase-orders.destroy', $purchaseOrder))
        ->assertRedirect(route('admin.purchase-orders.index'));

    $this->assertDatabaseMissing('purchase_orders', ['id' => $purchaseOrder->id]);
});