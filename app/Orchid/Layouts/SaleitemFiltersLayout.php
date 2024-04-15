<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\ItemsFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class SaleitemFiltersLayout extends Selection {
	/**
	 * @return Filter[]
	 */
	public function filters(): array
	{
		return [
			ItemsFilter::class,
		];
	}
}
