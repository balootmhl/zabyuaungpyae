<?php

namespace App\Orchid\Layouts;

use App\Models\Product;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table {
	/**
	 * Data source.
	 *
	 * The name of the key to fetch it from the query.
	 * The results of which will be elements of the table.
	 *
	 * @var string
	 */
	protected $target = 'products';

	/**
	 * Get the table cells to be displayed.
	 *
	 * @return TD[]
	 */
	protected function columns(): array
	{
		return [
			TD::make('code', 'Code')->sort(),

			TD::make('name', 'Product Name')->sort()
				->render(function (Product $product) {
					return Link::make($product->name)
						->route('platform.product.edit', $product);
				}),

			TD::make('category_code', 'Category Code')->sort()
				->render(function (Product $product) {
					return $product->category->code;
				}),

			TD::make('category_id', 'Category')->sort()
				->render(function (Product $product) {
					return $product->category->name;
				}),

			TD::make('buy_price', 'Buy Price')
				->sort(),

			TD::make('sale_price', 'Sale Price')
				->sort(),

			TD::make('quantity', 'Quantity')
				->sort()
				->render(function (Product $product) {
					if ($product->quantity != null) {
						return $product->quantity;
					} else {
						return 0;
					}

				}),

			TD::make('group_id', 'Group')->sort()
				->render(function (Product $product) {
					if ($product->group) {
						return $product->group->name;
					} else {
						return 'None';
					}

				}),

			// TD::make('created_at', 'Created'),

			// TD::make('updated_at', 'Last edited'),

			TD::make(__('Actions'))
				->align(TD::ALIGN_CENTER)
				->width('100px')
				->render(function (Product $product) {
					if (auth()->user()->name == 'admin') {
						return DropDown::make()
							->icon('options-vertical')
							->list([

								Link::make(__('Edit'))
									->route('platform.product.edit', $product->id)
									->icon('pencil'),

								Button::make(__('Delete'))
									->icon('trash')
									->confirm(__('Are you sure?'))
									->method('remove', [
										'id' => $product->id,
									]),
							]);
					} else {
						return DropDown::make()
							->icon('options-vertical')
							->list([

								Link::make(__('Edit'))
									->route('platform.product.edit', $product->id)
									->icon('pencil'),

								// Button::make(__('Delete'))
								//     ->icon('trash')
								//     ->confirm(__('Are you sure?'))
								//     ->method('remove', [
								//         'id' => $product->id,
								//     ]),
							]);
					}
				}),
		];
	}
}
