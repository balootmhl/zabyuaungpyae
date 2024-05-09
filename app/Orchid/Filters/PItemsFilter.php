<?php

namespace App\Orchid\Filters;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;

class PItemsFilter extends Filter {
	/**
	 * @var array
	 */
	public $parameters = ['search', 'type'];

	/**
	 * @return string
	 */
	public function name(): string {
		return 'Purchase Items with';
	}

	/**
	 * @param Builder $builder
	 *
	 * @return Builder
	 */
	public function run(Builder $builder): Builder {
        if ($this->request->get('type') == 'default'){
            return $builder->where('invoice_no', 'LIKE', '%'. $this->request->get('search') .'%');
        } else {
            return $builder->whereHas('purchaseitems', function (Builder $query) {
                $query->where('code', 'LIKE', '%' . $this->request->get('search') . '%')
                    ->orWhere('name', 'LIKE', '%' . $this->request->get('search') . '%')
                    ->orWhere('product_id', 'LIKE', '%' . $this->request->get('search') . '%');
            });
        }

	}

	/**
	 * @return Field[]
	 */
	public function display(): array
	{
		return [
			Input::make('search')
				->type('text')
				->value($this->request->get('search'))
				->placeholder('Type to search')
				->title('Search'),
            Select::make('type')
                ->options([
                    'default' => 'Default',
                    'items' => 'Purchase Items',
                ])
                ->value($this->request->get('type'))
                ->title(__('Search Type')),
		];
	}
}
