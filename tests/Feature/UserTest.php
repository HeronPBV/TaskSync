<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

uses(RefreshDatabase::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Test: /user Endpoint
|--------------------------------------------------------------------------
|
| These tests ensure that the /user endpoint, protected by auth middleware,
| returns 401 when not authenticated, and the correct user data when authenticated.
|
*/

it('returns 401 for unauthenticated access to /user', function () {
    $response = $this->getJson('/user');
    $response->assertStatus(401);
});

it('returns the authenticated user for /user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->getJson('/user');
    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
        ]);
});
