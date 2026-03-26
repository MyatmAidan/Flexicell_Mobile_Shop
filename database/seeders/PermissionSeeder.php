<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * All available permissions in the system.
     */
    private array $permissions = [
        ['name' => 'dashboard.view',          'label' => 'View Dashboard'],
        ['name' => 'users.manage',            'label' => 'Manage Users'],
        ['name' => 'brand.manage',            'label' => 'Manage Brands'],
        ['name' => 'category.manage',         'label' => 'Manage Categories'],
        ['name' => 'phone_model.manage',      'label' => 'Manage Phone Models'],
        ['name' => 'product.manage',          'label' => 'Manage Products'],
        ['name' => 'device.manage',           'label' => 'Manage Devices'],
        ['name' => 'direct_sale.manage',      'label' => 'Process Direct Sales (POS)'],
        ['name' => 'order.view',              'label' => 'View Orders'],
        ['name' => 'installment.manage',      'label' => 'Manage Installments'],
        ['name' => 'installment_rate.manage', 'label' => 'Manage Installment Rates'],
        ['name' => 'blog.manage',             'label' => 'Manage Blogs'],
    ];

    /**
     * Role → list of permission names they are granted.
     */
    private array $rolePermissions = [
        'superadmin' => [
            'dashboard.view',
            'users.manage',
            'brand.manage',
            'category.manage',
            'phone_model.manage',
            'product.manage',
            'device.manage',
            'direct_sale.manage',
            'order.view',
            'installment.manage',
            'installment_rate.manage',
            'blog.manage',
        ],
        'manager' => [
            'dashboard.view',
            'brand.manage',
            'category.manage',
            'phone_model.manage',
            'product.manage',
            'device.manage',
            'direct_sale.manage',
            'order.view',
            'installment.manage',
            'installment_rate.manage',
            'blog.manage',
        ],
        'staff' => [
            'dashboard.view',
            'device.manage',
            'direct_sale.manage',
            'order.view',
            'installment.manage',
        ],
    ];

    public function run(): void
    {
        // Insert all permissions
        $now = now();
        foreach ($this->permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'label'      => $permission['label'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Map permission names to IDs
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // Assign permissions per role
        foreach ($this->rolePermissions as $role => $permNames) {
            foreach ($permNames as $permName) {
                DB::table('role_permissions')->updateOrInsert(
                    [
                        'role'          => $role,
                        'permission_id' => $permissionIds[$permName],
                    ],
                    []
                );
            }
        }
    }
}
