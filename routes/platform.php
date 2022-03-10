<?php

declare (strict_types = 1);

use App\Orchid\Screens\CategoryEditScreen;
use App\Orchid\Screens\CategoryListScreen;
use App\Orchid\Screens\CustomerEditScreen;
use App\Orchid\Screens\CustomerListScreen;
use App\Orchid\Screens\EmailSenderScreen;
use App\Orchid\Screens\Examples\ExampleCardsScreen;
use App\Orchid\Screens\Examples\ExampleChartsScreen;
use App\Orchid\Screens\Examples\ExampleFieldsAdvancedScreen;
use App\Orchid\Screens\Examples\ExampleFieldsScreen;
use App\Orchid\Screens\Examples\ExampleLayoutsScreen;
use App\Orchid\Screens\Examples\ExampleScreen;
use App\Orchid\Screens\Examples\ExampleTextEditorsScreen;
use App\Orchid\Screens\FindInvoiceScreen;
use App\Orchid\Screens\GroupEditScreen;
use App\Orchid\Screens\GroupListScreen;
use App\Orchid\Screens\IncomeCalculatorScreen;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\ProductEditScreen;
use App\Orchid\Screens\ProductImportScreen;
use App\Orchid\Screens\StockControlScreen;
use App\Orchid\Screens\ProductListScreen;
use App\Orchid\Screens\PurchaseEditScreen;
use App\Orchid\Screens\PurchaseListScreen;
use App\Orchid\Screens\PurchaseViewScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\SaleEditScreen;
use App\Orchid\Screens\SaleListScreen;
use App\Orchid\Screens\SaleViewScreen;
use App\Orchid\Screens\SupplierEditScreen;
use App\Orchid\Screens\SupplierListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
 */

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Profile'), route('platform.profile'));
    });

// Platform > System > Users
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(function (Trail $trail, $user) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('User'), route('platform.systems.users.edit', $user));
    });

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.users')
            ->push(__('Create'), route('platform.systems.users.create'));
    });

// Platform > System > Users > User
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Users'), route('platform.systems.users'));
    });

// Platform > System > Roles > Role
Route::screen('roles/{roles}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(function (Trail $trail, $role) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Role'), route('platform.systems.roles.edit', $role));
    });

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.systems.roles')
            ->push(__('Create'), route('platform.systems.roles.create'));
    });

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push(__('Roles'), route('platform.systems.roles'));
    });

// Example...
Route::screen('/home', ExampleScreen::class)
    ->name('platform.example')
    ->breadcrumbs(function (Trail $trail) {
        return $trail
            ->parent('platform.index')
            ->push('Welcome to Mahar Shin');
    });

Route::screen('example-fields', ExampleFieldsScreen::class)->name('platform.example.fields');
Route::screen('example-layouts', ExampleLayoutsScreen::class)->name('platform.example.layouts');
Route::screen('example-charts', ExampleChartsScreen::class)->name('platform.example.charts');
Route::screen('example-editors', ExampleTextEditorsScreen::class)->name('platform.example.editors');
Route::screen('example-cards', ExampleCardsScreen::class)->name('platform.example.cards');
Route::screen('example-advanced', ExampleFieldsAdvancedScreen::class)->name('platform.example.advanced');
Route::screen('email', EmailSenderScreen::class)->name('platform.email')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Email sender');
});

// Product Routes
Route::screen('product/{product?}', ProductEditScreen::class)
    ->name('platform.product.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.product.list')
        ->push('Manage Product');
});

Route::screen('products', ProductListScreen::class)
    ->name('platform.product.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Product List');
});

Route::screen('products/import-products', ProductImportScreen::class)
    ->name('platform.product.import-products')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.product.list')
        ->push('Import Products');
});

// Route::screen('products/stock-control', StockControlScreen::class)
//     ->name('platform.product.stock-control')->breadcrumbs(function (Trail $trail) {
//     return $trail
//         ->parent('platform.product.list')
//         ->push('Import Products');
// });

Route::get('products/stock-control', 'App\Http\Controllers\CustomController@stockControl')->name('platform.product.stock-control');
Route::post('products/stock-control/save', 'App\Http\Controllers\CustomController@saveStock')->name('platform.product.stock.save');

// Category Routes
Route::screen('category/{category?}', CategoryEditScreen::class)
    ->name('platform.category.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.category.list')
        ->push('Manage Category');
});

Route::screen('categories', CategoryListScreen::class)
    ->name('platform.category.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Category List');
});

// Group Routes
Route::screen('group/{group?}', GroupEditScreen::class)
    ->name('platform.group.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.group.list')
        ->push('Manage Group');
});

Route::screen('groups', GroupListScreen::class)
    ->name('platform.group.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Group List');
});

// Sales Routes

// Customer Routes
Route::screen('customer/{customer?}', CustomerEditScreen::class)
    ->name('platform.customer.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.customer.list')
        ->push('Manage Customer');
});
Route::screen('customers', CustomerListScreen::class)
    ->name('platform.customer.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Customer List');
});

// Sale Record Routes
Route::screen('sale/{sale?}', SaleEditScreen::class)
    ->name('platform.sale.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.sale.list')
        ->push('Manage Sale Invoice');
});
Route::screen('sale/view/{sale?}', SaleViewScreen::class)
    ->name('platform.sale.view')
    ->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.sale.list')
        ->push('View Invoice');
});
Route::screen('sales', SaleListScreen::class)
    ->name('platform.sale.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Sale Invoices List');
});
Route::get('sales/saleitems/delete/{id}', 'App\Http\Controllers\CustomController@deleteSaleItems');
Route::get('sales/invoice/print/{id}', 'App\Http\Controllers\CustomController@downloadInvoice')->name('platform.sale.print');
// Find Sale Invoices
Route::screen('sales/search', FindInvoiceScreen::class)
    ->name('platform.sale.search')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.sale.list')
        ->push('Search Invoices');
});

// Purchase Routes
// Supplier Routes
Route::screen('supplier/{supplier?}', SupplierEditScreen::class)
    ->name('platform.supplier.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.supplier.list')
        ->push('Manage Supplier');
});
Route::screen('suppliers', SupplierListScreen::class)
    ->name('platform.supplier.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Supplier List');
});

// Purchase Record Routes
Route::screen('purchase/{purchase?}', PurchaseEditScreen::class)
    ->name('platform.purchase.edit')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.purchase.list')
        ->push('Manage Purchase Invoice');
});
Route::screen('purchase/view/{purchase?}', PurchaseViewScreen::class)
    ->name('platform.purchase.view')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.purchase.list')
        ->push('View Invoice');
});
Route::screen('purchases', PurchaseListScreen::class)
    ->name('platform.purchase.list')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Purchase Invoices List');
});
Route::get('purchases/purchaseitems/delete/{id}', 'App\Http\Controllers\CustomController@deletePurchaseItems');
Route::get('purchases/invoice/print/{id}', 'App\Http\Controllers\CustomController@downloadPInvoice')->name('platform.purchase.print');

// Total Income & Discount Calculator
Route::screen('incomes', IncomeCalculatorScreen::class)
    ->name('platform.income.discount')->breadcrumbs(function (Trail $trail) {
    return $trail
        ->parent('platform.index')
        ->push('Income & Discount Calculator');
});

//Route::screen('idea', 'Idea::class','platform.screens.idea');
