<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class PItemsFilter extends Filter {
	/**
	 * @var array
	 */
	public $parameters = ['code'];

	/**
	 * @return string
	 */
	public function name(): string {
		return 'Items with';
	}

	/**
	 * @param Builder $builder
	 *
	 * @return Builder
	 */
	public function run(Builder $builder): Builder {
		return $builder->whereHas('purchaseitems', function (Builder $query) {
			$query->where('code', 'LIKE', '%' . $this->request->get('code') . '%');
		});
	}

	/**
	 * @return Field[]
	 */
	public function display(): array
	{
		return [
			Input::make('code')
				->type('text')
				->value($this->request->get('code'))
				->placeholder('Type code')
				->title('Search items'),
		];
	}
}
