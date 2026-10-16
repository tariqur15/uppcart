<?php

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
});

it('guest cannot access suppliers', function () {
    $this->get(route('admin.suppliers.index'))->assertRedirect(route('login'));
});

it('admin can list suppliers', function () {
    Supplier::factory()->create(['name' => 'Acme Traders']);

    $this->actingAs($this->admin)
        ->get(route('admin.suppliers.index'))
        ->assertSuccessful()
        ->assertSee('Acme Traders');
});

it('admin can search suppliers', function () {
    Supplier::factory()->create(['name' => 'UniqRad Traders']);
    Supplier::factory()->create(['name' => 'Something Else']);

    $this->actingAs($this->admin)
        ->get(route('admin.suppliers.index', ['search' => 'UniqRad']))
        ->assertSuccessful()
        ->assertSee('UniqRad Traders')
        ->assertDontSee('Something Else');
});

it('supplier create page shows form', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.suppliers.create'))
        ->assertSuccessful()
        ->assertSee('name="name"', false);
});

it('admin can create supplier', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.suppliers.store'), [
            'name' => 'Green Valley Imports',
            'contact_person' => 'Rahim Uddin',
            'phone' => '01712345678',
            'email' => 'rahim@greenvalley.test',
            'address' => 'Dhaka, Bangladesh',
            'note' => 'Preferred wholesaler',
            'is_active' => 1,
        ])
        ->assertRedirect(route('admin.suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'name' => 'Green Valley Imports',
        'contact_person' => 'Rahim Uddin',
        'phone' => '01712345678',
        'email' => 'rahim@greenvalley.test',
        'note' => 'Preferred wholesaler',
        'is_active' => 1,
    ]);
});

it('validates supplier creation', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.suppliers.store'), [])
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseCount('suppliers', 0);
});

it('admin can update supplier', function () {
    $supplier = Supplier::factory()->create(['name' => 'Old Name', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->patch(route('admin.suppliers.update', $supplier), [
            'name' => 'New Name',
            'contact_person' => 'Karim',
            'phone' => '01812345678',
            'email' => 'karim@example.test',
            'address' => 'Chattogram',
            'note' => null,
            'is_active' => 0,
        ])
        ->assertRedirect(route('admin.suppliers.index'));

    $this->assertDatabaseHas('suppliers', [
        'id' => $supplier->id,
        'name' => 'New Name',
        'is_active' => 0,
    ]);
});

it('admin can deactivate supplier without deleting it', function () {
    $supplier = Supplier::factory()->create(['name' => 'To Toggle', 'is_active' => true]);

    $this->actingAs($this->admin)
        ->patch(route('admin.suppliers.update', $supplier), [
            'name' => 'To Toggle',
            'is_active' => 0,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'is_active' => 0]);
});

it('admin can delete supplier with no purchase orders', function () {
    $supplier = Supplier::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('admin.suppliers.destroy', $supplier))
        ->assertRedirect(route('admin.suppliers.index'));

    $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
});

it('supplier with purchase orders cannot be deleted', function () {
    $supplier = Supplier::factory()->create();
    PurchaseOrder::factory()->create(['supplier_id' => $supplier->id]);

    $this->actingAs($this->admin)
        ->delete(route('admin.suppliers.destroy', $supplier))
        ->assertRedirect()
        ->assertSessionHas('error');

    $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
});