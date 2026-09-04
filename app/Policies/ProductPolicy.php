<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        return $user->id === $product->user_id || $user->hasPermission('manage-products');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->id === $product->user_id || $user->hasPermission('manage-products');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage-products');
    }
}
