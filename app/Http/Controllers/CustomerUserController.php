<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class CustomerUserController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user();
        abort_unless($customer->isCustomer(), 403);
        abort_unless($customer->customer_id === null, 403);

        return view('customer.users.index', [
            'activeSubscription' => $this->activeSubscription($customer),
            'teamMembers' => $customer->teamMembers()->latest()->get(),
            'userCount' => 1 + $customer->teamMembers()->count(),
        ]);
    }

    public function create(Request $request): View
    {
        $customer = $request->user();
        abort_unless($customer->isCustomer(), 403);
        abort_unless($customer->customer_id === null, 403);
        abort_unless($this->canAddUser($customer), 403);

        return view('customer.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $customer = $request->user();
        abort_unless($customer->isCustomer(), 403);
        abort_unless($customer->customer_id === null, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::transaction(function () use ($customer, $validated): void {
            abort_unless($this->canAddUser($customer, true), 403);

            User::create([
                'customer_id' => $customer->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_CUSTOMER,
                'active' => true,
                'email_verified_at' => now(),
            ]);
        });

        return redirect()->route('customer.users.index')->with('status', __('User added successfully.'));
    }

    private function activeSubscription(User $customer, bool $lock = false): ?CustomerSubscription
    {
        $query = $customer->customerSubscriptions()->with('plan')->where('status', CustomerSubscription::STATUS_ACTIVE)->latest();

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    private function canAddUser(User $customer, bool $lock = false): bool
    {
        $subscription = $this->activeSubscription($customer, $lock);
        $limit = $subscription?->plan?->user_limit;

        return $subscription !== null
            && ($limit === null || 1 + $customer->teamMembers()->count() < $limit);
    }
}
