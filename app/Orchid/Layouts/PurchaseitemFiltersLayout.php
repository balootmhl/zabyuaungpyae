<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\PItemsFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class PurchaseitemFiltersLayout extends Selection {
	/**
	 * @return Filter[]
	 */
	public function filters(): array
	{
		return [
			PItemsFilter::class,
		];
	}
}
