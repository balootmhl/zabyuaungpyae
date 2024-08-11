<?php

declare (strict_types = 1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider {
	/**
	 * @param Dashboard $dashboard
	 */
	public function boot(Dashboard $dashboard): void{
		parent::boot($dashboard);

		// ...
	}

	/**
	 * @return Menu[]
	 */
	public function registerMainMenu(): array
	{
		return [
			// Menu::make('Example screen')
			//     ->icon('monitor')
			//     ->route('platform.example')
			//     ->title('Navigation')
			//     ->badge(function () {
			//         return 6;
			//     }),

			Menu::make('Products')
				->icon('modules')
				->route('platform.product.list')
				->title('Sale & Purchase'),
			Menu::make('Sales Invoices')
				->icon('notebook')
				->route('platform.sale.list')
                ->permission('platform.module.sale'),
			Menu::make('Purchase Invoices')
				->icon('notebook')
				->route('platform.purchase.list')
                ->permission('platform.module.purchase'),

			Menu::make('Customers')
				->icon('people')
				->route('platform.customer.list')
                ->permission('platform.people.customer')
				->title('People'),
			Menu::make('Suppliers')
				->icon('people')
				->route('platform.supplier.list')
                ->permission('platform.people.supplier'),

			Menu::make(__('Users'))
				->icon('user')
				->route('platform.systems.users')
				->permission('platform.systems.users')
				->title(__('Access rights')),
			Menu::make(__('Roles'))
				->icon('lock')
				->route('platform.systems.roles')
				->permission('platform.systems.roles'),

			Menu::make('Categories')
				->icon('layers')
				->route('platform.category.list')
                ->permission('platform.module.category')
				->title('Others'),
			Menu::make('Groups')
				->icon('drawer')
				->route('platform.group.list')
                ->permission('platform.module.group'),
			Menu::make('Calculate Amount')
				->icon('dollar')
				->route('platform.income.discount')
                ->permission('platform.module.calculate-amount'),

			// Menu::make('Suppliers')
			//     ->icon('modules')
			//     ->route('platform.customer.list')
			//     ->title('Sales'),

			// Menu::make('Purchase Records')
			//     ->icon('modules')
			//     ->route('platform.sale.list'),

			// Menu::make('Dropdown menu')
			//     ->icon('code')
			//     ->list([
			//         Menu::make('Sub element item 1')->icon('bag'),
			//         Menu::make('Sub element item 2')->icon('heart'),
			//     ]),

			// Menu::make('Basic Elements')
			//     ->title('Form controls')
			//     ->icon('note')
			//     ->route('platform.example.fields'),

			// Menu::make('Advanced Elements')
			//     ->icon('briefcase')
			//     ->route('platform.example.advanced'),

			// Menu::make('Text Editors')
			//     ->icon('list')
			//     ->route('platform.example.editors'),

			// Menu::make('Overview layouts')
			//     ->title('Layouts')
			//     ->icon('layers')
			//     ->route('platform.example.layouts'),

			// Menu::make('Chart tools')
			//     ->icon('bar-chart')
			//     ->route('platform.example.charts'),

			// Menu::make('Cards')
			//     ->icon('grid')
			//     ->route('platform.example.cards')
			//     ->divider(),

			// Menu::make('Documentation')
			//     ->title('Docs')
			//     ->icon('docs')
			//     ->url('https://orchid.software/en/docs'),

			// Menu::make('Changelog')
			//     ->icon('shuffle')
			//     ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
			//     ->target('_blank')
			//     ->badge(function () {
			//         return Dashboard::version();
			//     }, Color::DARK()),

			// Menu::make('Email sender')
			//     ->icon('envelope-letter')
			//     ->route('platform.email')
			//     ->title('Tools'),
		];
	}

	/**
	 * @return Menu[]
	 */
	public function registerProfileMenu(): array
	{
		return [
			Menu::make('Profile')
				->route('platform.profile')
				->icon('user'),
		];
	}

	/**
	 * @return ItemPermission[]
	 */
	public function registerPermissions(): array
	{
		return [
			ItemPermission::group(__('System'))
				->addPermission('platform.systems.roles', __('Roles'))
				->addPermission('platform.systems.users', __('Users')),
		];
	}
}
