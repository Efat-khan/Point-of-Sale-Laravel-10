<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions =[
            // "role-list",
            // "role-create",
            // "role-edit",
            // "role-delete",
            // "user-list",
            // "user-create",
            // "user-edit",
            // "user-delete",
            // "product-list",
            // "product-create",
            // "product-edit",
            // "product-delete",
            // "category-list",
            // "category-create",
            // "category-edit",
            // "category-delete",
            // "brand-list",
            // "brand-create",
            // "brand-edit",
            // "brand-delete",
            // "unit-list",
            // "unit-create",
            // "unit-edit",
            // "unit-delete",
            // "tax-list",
            // "tax-create",
            // "tax-edit",
            // "tax-delete",
            // "product-price-code-list",
            // "product-price-code-create",
            // "product-price-code-edit",
            // "product-price-code-delete",
            // "product-label-list",
            // "product-label-create",
            // "product-label-edit",
            // "product-label-delete",
            // "invoice-list",
            // "invoice-create",
            // "invoice-edit",
            // "invoice-delete",
            // "invoice-view",
            // "quotation-view",
            // "challan-view",
            // "purchase-list",
            // "purchase-create",
            // "purchase-edit",
            // "purchase-delete",
            // "supplier-list",
            // "supplier-create",
            // "supplier-edit",
            // "supplier-delete",
            // "supplier-transaction-list",
            // "supplier-transaction-create",
            // "supplier-transaction-edit",
            // "supplier-transaction-delete",
            // "customer-list",
            // "customer-create",
            // "customer-edit",
            // "customer-delete",
            // "customer-transaction-list",
            // "customer-transaction-create",
            // "customer-transaction-edit",
            // "customer-transaction-delete",
            // "stock-list",
            // "setting",
            // "support",
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
