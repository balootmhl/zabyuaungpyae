<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\QueryFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class ProductFiltersLayout extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): array
    {
        return [
            QueryFilter::class,
        ];
    }
}
