<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->category = Category::factory()->create();
});

it('guest cannot access admin products', function () {
    $this->get(route('admin.products.index'))->assertRedirect(route('login'));
});

it('non-admin cannot access admin products', function () {
    $user = User::factory()->create(['role' => 'customer']);

    $this->actingAs($user)
        ->get(route('admin.products.index'))
        ->assertForbidden();
});

it('admin can list products', function () {
    $this->actingAs($this->admin);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Admin Product A',
    ]);
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Admin Product B',
    ]);

    $this->get(route('admin.products.index'))
        ->assertSuccessful()
        ->assertSee('Admin Product A')
        ->assertSee('Admin Product B');
});

it('admin can create product', function () {
    $this->actingAs($this->admin);

    $this->post(route('admin.products.store'), [
        'category_id' => $this->category->id,
        'name' => 'New Product',
        'slug' => 'new-product',
        'type' => 'physical',
        'description' => 'A great product.',
        'price' => 29.99,
        'stock' => 50,
        'status' => 'active',
    ])->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'name' => 'New Product',
        'slug' => 'new-product',
        'price' => 29.99,
    ]);
});

it('validates product creation', function () {
    $this->actingAs($this->admin);

    $this->post(route('admin.products.store'), [])
        ->assertSessionHasErrors(['name', 'category_id', 'type', 'price', 'stock', 'status']);
});

it('admin can update product', function () {
    $this->actingAs($this->admin);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Old Name',
        'price' => 10.00,
    ]);

    $this->patch(route('admin.products.update', $product), [
        'category_id' => $this->category->id,
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'type' => 'physical',
        'description' => 'Updated description.',
        'price' => 19.99,
        'stock' => 25,
        'status' => 'active',
    ])->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
        'slug' => 'updated-name',
        'price' => 19.99,
    ]);
});

it('admin can delete product', function () {
    $this->actingAs($this->admin);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'To Delete',
    ]);

    $this->delete(route('admin.products.destroy', $product))
        ->assertRedirect(route('admin.products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

it('admin can toggle product status', function () {
    $this->actingAs($this->admin);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'status' => 'active',
    ]);

    $this->patch(route('admin.products.toggle-status', $product));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'status' => 'draft',
    ]);
});

it('admin can search products via ajax', function () {
    $this->actingAs($this->admin);

    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Searchable Item',
        'sku' => 'SRC-001',
    ]);
    Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Other Item',
    ]);

    $response = $this->getJson(route('admin.products.search-ajax', ['q' => 'Searchable']));

    $response->assertSuccessful();
    expect($response->json('products'))->toHaveCount(1);
    expect($response->json('products')[0]['name'])->toBe('Searchable Item');
});

it('admin can bulk delete products', function () {
    $this->actingAs($this->admin);

    $products = Product::factory(3)->create([
        'category_id' => $this->category->id,
    ]);

    $ids = $products->pluck('id')->toArray();

    $this->post(route('admin.products.bulk-delete'), ['ids' => $ids])
        ->assertRedirect(route('admin.products.index'));

    foreach ($ids as $id) {
        $this->assertDatabaseMissing('products', ['id' => $id]);
    }
});

it('admin can bulk update category', function () {
    $this->actingAs($this->admin);

    $newCategory = Category::factory()->create();
    $products = Product::factory(3)->create([
        'category_id' => $this->category->id,
    ]);

    $ids = $products->pluck('id')->toArray();

    $this->patch(route('admin.products.bulk-update'), [
        'ids' => $ids,
        'category_id' => $newCategory->id,
    ])->assertRedirect(route('admin.products.index'));

    foreach ($ids as $id) {
        $this->assertDatabaseHas('products', ['id' => $id, 'category_id' => $newCategory->id]);
    }
});

it('admin can bulk toggle status', function () {
    $this->actingAs($this->admin);

    $products = Product::factory(3)->create([
        'category_id' => $this->category->id,
        'status' => 'active',
    ]);

    $ids = $products->pluck('id')->toArray();

    $this->patch(route('admin.products.bulk-toggle-status'), ['ids' => $ids, 'status' => 'draft']);

    foreach ($ids as $id) {
        $this->assertDatabaseHas('products', ['id' => $id, 'status' => 'draft']);
    }
});

it('product create page shows form', function () {
    $this->actingAs($this->admin);

    $this->get(route('admin.products.create'))
        ->assertSuccessful()
        ->assertSee('name="name"', false);
});

it('product edit page shows existing data', function () {
    $this->actingAs($this->admin);

    $product = Product::factory()->create([
        'category_id' => $this->category->id,
        'name' => 'Editable Product',
        'price' => 49.99,
    ]);

    $this->get(route('admin.products.edit', $product))
        ->assertSuccessful()
        ->assertSee('Editable Product')
        ->assertSee('49.99');
});
