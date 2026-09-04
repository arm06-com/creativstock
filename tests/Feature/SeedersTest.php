<?php

use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserSeeder;

it('seeds default users and products', function () {
    $this->artisan('db:seed', ['--class' => UserSeeder::class])->assertOk();
    $this->artisan('db:seed', ['--class' => ProductSeeder::class])->assertOk();

    expect(User::query()->whereIn('role', [
        User::ROLE_SUPER_ADMIN,
        User::ROLE_ADMIN,
        User::ROLE_CONTRIBUTOR,
        User::ROLE_CUSTOMER,
    ])->count())->toBeGreaterThanOrEqual(4)
        ->and(Product::count())->toBeGreaterThanOrEqual(5);
});
