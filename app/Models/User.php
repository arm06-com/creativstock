<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $profile_photo_path
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'email', 'profile_photo_path', 'password', 'role', 'active', 'customer_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    public const ROLE_SUPER_ADMIN = 'super-admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_CONTRIBUTOR = 'contributor';

    public const ROLE_CUSTOMER = 'customer';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(self::ROLE_SUPER_ADMIN);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isContributor(): bool
    {
        return $this->hasRole(self::ROLE_CONTRIBUTOR);
    }

    public function isCustomer(): bool
    {
        return $this->hasRole(self::ROLE_CUSTOMER);
    }

    public function isActive(): bool
    {
        return $this->active === true;
    }

    public function isInactive(): bool
    {
        return ! $this->isActive();
    }

    public function customerSubscriptions(): HasMany
    {
        return $this->hasMany(CustomerSubscription::class);
    }

    public function customerOwner(): BelongsTo
    {
        return $this->belongsTo(self::class, 'customer_id');
    }

    public function customerAccount(): self
    {
        return $this->customer_id === null
            ? $this
            : $this->customerOwner()->firstOrFail();
    }

    /**
     * @return array<int, int>
     */
    public function customerAccountUserIds(): array
    {
        $owner = $this->customerAccount();

        return self::query()
            ->where('id', $owner->id)
            ->orWhere('customer_id', $owner->id)
            ->pluck('id')
            ->all();
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(self::class, 'customer_id');
    }

    public function assetDownloads(): HasMany
    {
        return $this->hasMany(AssetDownload::class);
    }

    public function contributorEarnings(): HasMany
    {
        return $this->hasMany(ContributorEarning::class, 'contributor_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(ContributorPaymentMethod::class, 'contributor_id');
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(ContributorWithdrawal::class, 'contributor_id');
    }

    public function permissions(): array
    {
        $permissions = [
            self::ROLE_SUPER_ADMIN => [
                'manage-users',
                'create-users',
                'edit-users',
                'delete-users',
                'assign-roles',
                'manage-admins',
                'manage-contributors',
                'manage-customers',
                'manage-categories',
                'manage-products',
                'approve-products',
                'manage-product-files',
                'manage-orders',
                'view-payments',
                'view-paddle-transactions',
                'manage-subscriptions',
                'manage-settings',
                'view-reports',
                'view-sales',
                'view-contributor-earnings',
                'manage-payouts',
                'view-audit-logs',
                'manage-permissions',
                'manage-system-configuration',
            ],
            self::ROLE_ADMIN => [
                'manage-users',
                'create-users',
                'edit-users',
                'delete-users',
                'assign-roles',
                'manage-contributors',
                'manage-customers',
                'manage-categories',
                'manage-products',
                'approve-products',
                'manage-product-files',
                'manage-orders',
                'view-payments',
                'view-paddle-transactions',
                'manage-subscriptions',
                'manage-settings',
                'view-reports',
                'view-sales',
                'view-contributor-earnings',
                'manage-payouts',
            ],
            self::ROLE_CONTRIBUTOR => [
                'manage-products',
                'manage-product-files',
                'view-sales',
            ],
            self::ROLE_CUSTOMER => [],
        ];

        return $permissions[$this->role] ?? [];
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions(), true);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function profilePhotoUrl(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return Storage::disk('public')->exists($this->profile_photo_path)
            ? '/storage/'.ltrim((string) $this->profile_photo_path, '/')
            : null;
    }
}
