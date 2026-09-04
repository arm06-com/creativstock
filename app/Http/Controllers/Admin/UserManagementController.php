<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-users');

        $activeTab = $request->query('tab', auth()->user()?->isSuperAdmin() ? 'admins' : 'contributors');

        if ($activeTab === 'customers') {
            $role = User::ROLE_CUSTOMER;
            $label = __('Customers');
        } elseif ($activeTab === 'admins') {
            $role = User::ROLE_ADMIN;
            $label = __('Admins');
        } else {
            $role = User::ROLE_CONTRIBUTOR;
            $label = __('Contributors');
        }

        $users = User::query()
            ->where('role', $role)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['tab' => $activeTab]);

        return view('admin.users.index', compact('users', 'activeTab', 'label'));
    }

    public function create(): View
    {
        Gate::authorize('create-users');

        $roles = [
            User::ROLE_ADMIN => __('Admin'),
            User::ROLE_CONTRIBUTOR => __('Contributor'),
            User::ROLE_CUSTOMER => __('Customer'),
        ];

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create-users');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:'.implode(',', [User::ROLE_ADMIN, User::ROLE_CONTRIBUTOR, User::ROLE_CUSTOMER])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', __('User created successfully.'));
    }

    public function edit(User $user): View
    {
        Gate::authorize('edit-users');

        $roles = [
            User::ROLE_ADMIN => __('Admin'),
            User::ROLE_CONTRIBUTOR => __('Contributor'),
            User::ROLE_CUSTOMER => __('Customer'),
        ];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('edit-users');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:'.implode(',', [User::ROLE_ADMIN, User::ROLE_CONTRIBUTOR, User::ROLE_CUSTOMER])],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ] + ($validated['password'] ? ['password' => Hash::make($validated['password'])] : []));

        return redirect()->route('admin.users.index')
            ->with('status', __('User updated successfully.'));
    }

    public function toggleActive(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $user->update([
            'active' => ! $user->active,
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', $user->active ? __('User activated successfully.') : __('User deactivated successfully.'));
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete-users');

        if ($user->isSuperAdmin()) {
            return back()->withErrors(['user' => __('The super admin account cannot be deleted.')]);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', __('User deleted successfully.'));
    }
}
