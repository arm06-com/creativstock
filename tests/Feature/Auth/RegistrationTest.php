<?php

use App\Models\User;
use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => User::ROLE_CUSTOMER,
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe(User::ROLE_CUSTOMER);
});

test('customer registration screen can be rendered', function () {
    $response = $this->get(route('register.customer'));

    $response->assertOk();
});

test('contributor registration screen can be rendered', function () {
    $response = $this->get(route('register.contributor'));

    $response->assertOk();
});
