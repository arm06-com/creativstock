<?php

use App\Models\User;

it('shows contributors, customers, and admins in the correct tabs for super admins', function () {
    User::factory()->create(['role' => User::ROLE_ADMIN, 'name' => 'Admin Person']);
    User::factory()->create(['role' => User::ROLE_SUPER_ADMIN, 'name' => 'Super Admin']);
    $contributor = User::factory()->create(['role' => User::ROLE_CONTRIBUTOR, 'name' => 'Jane Contributor']);
    $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER, 'name' => 'John Customer']);

    $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]))
        ->get(route('admin.users.index', ['tab' => 'admins']))
        ->assertOk()
        ->assertSee('Admin Person')
        ->assertDontSee('Jane Contributor')
        ->assertDontSee('John Customer');

    $this->actingAs(User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]))
        ->get(route('admin.users.index', ['tab' => 'contributors']))
        ->assertOk()
        ->assertSee('Jane Contributor')
        ->assertDontSee('Admin Person')
        ->assertDontSee('John Customer');

    $this->actingAs(User::factory()->create(['role' => User::ROLE_ADMIN]))
        ->get(route('admin.users.index', ['tab' => 'customers']))
        ->assertOk()
        ->assertSee('John Customer')
        ->assertDontSee('Jane Contributor')
        ->assertDontSee('Admin Person');
});
