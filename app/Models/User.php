<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // -------------------------------------------------------
    // Role helpers
    // -------------------------------------------------------

    /**
     * Check if the user has one of the given roles.
     */
    public function isRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // -------------------------------------------------------
    // Permission helpers  (3-tier: user revoke > user grant > role)
    // -------------------------------------------------------

    /** Cached role permissions */
    protected ?array $permissionCache = null;

    /** Cached user-level overrides [ name => 'grant'|'revoke' ] */
    protected ?array $userPermissionCache = null;

    /**
     * Load role-level permissions for this user.
     */
    protected function loadRolePermissions(): array
    {
        if ($this->permissionCache === null) {
            $this->permissionCache = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role', $this->role)
                ->pluck('permissions.name')
                ->toArray();
        }
        return $this->permissionCache;
    }

    /**
     * Load user-level overrides → [ 'brand.manage' => 'grant', 'order.view' => 'revoke', ... ]
     */
    protected function loadUserPermissions(): array
    {
        if ($this->userPermissionCache === null) {
            $this->userPermissionCache = DB::table('user_permissions')
                ->join('permissions', 'permissions.id', '=', 'user_permissions.permission_id')
                ->where('user_permissions.user_id', $this->id)
                ->pluck('user_permissions.type', 'permissions.name')
                ->toArray();
        }
        return $this->userPermissionCache;
    }

    /**
     * 3-tier resolution:
     *  1. User-level REVOKE → deny
     *  2. User-level GRANT  → allow
     *  3. Role-level default
     */
    public function hasPermission(string $permission): bool
    {
        $overrides = $this->loadUserPermissions();

        if (isset($overrides[$permission])) {
            return $overrides[$permission] === 'grant';
        }

        return in_array($permission, $this->loadRolePermissions());
    }

    /**
     * Check if the user has ANY of the given permissions.
     */
    public function hasAnyPermission(string ...$permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Flush the permission caches (call after saving user_permissions).
     */
    public function flushPermissionCache(): void
    {
        $this->permissionCache     = null;
        $this->userPermissionCache = null;
    }

    /**
     * Get all permissions with their effective status for this user.
     * Returns array: [ ['permission' => ..., 'role_has' => bool, 'override' => null|'grant'|'revoke', 'effective' => bool] ]
     */
    public function getAllPermissionsWithStatus(): array
    {
        $rolePerms  = $this->loadRolePermissions();
        $overrides  = $this->loadUserPermissions();
        $allPerms   = DB::table('permissions')->orderBy('name')->get();

        return $allPerms->map(function ($perm) use ($rolePerms, $overrides) {
            $roleHas  = in_array($perm->name, $rolePerms);
            $override = $overrides[$perm->name] ?? null;
            $effective = $override !== null ? ($override === 'grant') : $roleHas;
            return [
                'id'        => $perm->id,
                'name'      => $perm->name,
                'label'     => $perm->label,
                'role_has'  => $roleHas,
                'override'  => $override,   // null | 'grant' | 'revoke'
                'effective' => $effective,
            ];
        })->toArray();
    }
}

