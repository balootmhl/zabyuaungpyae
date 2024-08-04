<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\Dashboard;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    /**
     * @param Dashboard $dashboard
     */
    public function boot(Dashboard $dashboard)
    {
        $module_permissions = ItemPermission::group('Modules')
            ->addPermission('platform.module.product', 'Product')
            ->addPermission('platform.module.sale', 'Sale')
            ->addPermission('platform.module.purchase', 'Purchase')
            ->addPermission('platform.module.branch', 'Branch')
            ->addPermission('platform.module.warehouse', 'Warehouse');

        $people_permissions = ItemPermission::group('People')
            ->addPermission('platform.people.customer', 'Customer')
            ->addPermission('platform.people.supplier', 'Supplier');

        $extra_permissions = ItemPermission::group('Extra')
            ->addPermission('platform.module.category', 'Category')
            ->addPermission('platform.module.group', 'Group')
            ->addPermission('platform.module.calculate-amount', 'Calculate Amount');

        $dashboard->registerPermissions($module_permissions);
        $dashboard->registerPermissions($people_permissions);
        $dashboard->registerPermissions($extra_permissions);
    }
}
