<?php

declare(strict_types=1);

namespace App\Support;

final class RolePermissions
{
    private const GUARD = 'web';

    private const NAMES = [
        // Menu visibility
        'menu.dashboard',
        'menu.users',
        'menu.roles',
        'menu.inventory',
        'menu.category',
        'menu.brand',
        'menu.phone-model',
        'menu.product',
        'menu.device',
        'menu.direct-sale',
        'menu.order',
        'menu.installment',
        'menu.installment-rate',
        'menu.supplier',
        'menu.blog',
        'menu.customer',
        'menu.trade-in',
        'menu.warranty-detail',

        // Module Management Permissions (used in routes)
        'users.manage',
        'roles.manage',
        'brand.manage',
        'category.manage',
        'product.manage',
        'phone_model.manage',
        'installment_rate.manage',
        'device.manage',
        'direct_sale.manage',
        'order.manage',
        'order.view',
        'installment.manage',
        'blog.manage',

        // Dashboard
        'dashboard.view',

        // User Details
        'users.view',
        'users.create',
        'users.update',
        'users.delete',

        // Role Details
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',

        // Categories
        'categories.view',
        'categories.create',
        'categories.update',
        'categories.delete',

        // Brands
        'brands.view',
        'brands.create',
        'brands.update',
        'brands.delete',

        // Phone Models
        'phone_models.view',
        'phone_models.create',
        'phone_models.update',
        'phone_models.delete',

        // Products
        'products.view',
        'products.create',
        'products.update',
        'products.delete',

        // Devices
        'devices.view',
        'devices.create',
        'devices.update',
        'devices.delete',

        // Orders
        'orders.view',
        'orders.create',
        'orders.update',
        'orders.delete',

        // Installments
        'installments.view',
        'installments.create',
        'installments.update',
        'installments.delete',

        // Blogs
        'blogs.view',
        'blogs.create',
        'blogs.update',
        'blogs.delete',

        // POS / Direct Sale
        'direct_sales.view',
        'direct_sales.create',
        'direct_sales.update',
        'direct_sales.delete',

        // Warranty Details
        'warranty_detail.view',
        'warranty_details.view',
        'warranty_details.create',
        'warranty_details.update',
        'warranty_details.delete',
    ];

    public static function allNames(): array
    {
        return self::NAMES;
    }

    public static function permissionNamesForRole(string $roleCode): array
    {
        if ($roleCode === 'superadmin') {
            return self::NAMES;
        }

        if ($roleCode === 'manager') {
            $exclude = ['users.delete', 'roles.delete'];
            return array_values(array_diff(self::NAMES, $exclude));
        }

        if ($roleCode === 'staff') {
            return [
                'menu.dashboard',
                'menu.inventory',
                'menu.device',
                'menu.direct-sale',
                'menu.order',
                'menu.installment',
                'menu.warranty-detail',
                'dashboard.view',
                'devices.view',
                'devices.manage',
                'orders.view',
                'direct_sale.manage',
                'direct_sales.create',
                'installment.manage',
                'installments.view',
                'warranty_detail.view',
                'warranty_details.view',
            ];
        }

        return [];
    }

    public static function guardName(): string
    {
        return self::GUARD;
    }
}
