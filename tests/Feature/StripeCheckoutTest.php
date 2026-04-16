<?php

use App\Models\User;
use App\Models\Products;
use App\Models\CartApi;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can initiate stripe checkout', function () {
    // Arrange
    $user = User::factory()->create();
    $product = Products::factory()->create(['price' => 100]);

    CartApi::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 100
    ]);

    // Act
    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.checkout'));

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'url',
            'order_id'
        ]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'payment_status' => 'pending',
        'status' => 'pending'
    ]);
});

test('guest cannot initiate checkout', function () {
    $response = $this->postJson(route('api.checkout'));
    $response->assertStatus(401);
});

test('empty cart cannot checkout', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user, 'sanctum')
        ->postJson(route('api.checkout'));
    
    $response->assertStatus(400);
});
