<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable, HasUlids, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'password',
        'phone',
        'address',
        'nrc',
        'profile_photo',
        'status',
        'is_primary_account',
        'email_verified_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'integer',
            'is_primary_account' => 'boolean',
        ];
    }

    public function guardName(): array
    {
        return ['web'];
    }

    public function assignedRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Customer profile for registered storefront users (linked at registration).
     */
    public function customerProfile(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Backward-compatible role slug (superadmin, manager, staff, user).
     */
    public function getRoleAttribute(): ?string
    {
        return $this->assignedRole?->code;
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Get all permissions with their status relative to this user.
     * Returns an array of permission data including role-based and user-override info.
     */
    public function getAllPermissionsWithStatus(): array
    {
        $allPermissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $role = $this->assignedRole;
        $rolePermissionIds = [];
        if ($role) {
            $spatieRole = \Spatie\Permission\Models\Role::where('name', $role->name)
                ->where('guard_name', 'web')
                ->first();
            if ($spatieRole) {
                $rolePermissionIds = $spatieRole->permissions->pluck('id')->toArray();
            }
        }

        $userOverrides = DB::table('user_permissions')
            ->where('user_id', $this->id)
            ->pluck('type', 'permission_id')
            ->toArray();

        $result = [];
        foreach ($allPermissions as $perm) {
            $roleHas = in_array($perm->id, $rolePermissionIds);
            $override = $userOverrides[$perm->id] ?? null;

            if ($override === 'grant') {
                $effective = true;
            } elseif ($override === 'revoke') {
                $effective = false;
            } else {
                $effective = $roleHas;
            }

            $result[] = [
                'id'        => $perm->id,
                'name'      => $perm->name,
                'label'     => $perm->label ?? $perm->name,
                'role_has'  => $roleHas,
                'override'  => $override,
                'effective' => $effective,
            ];
        }

        return $result;
    }
}
