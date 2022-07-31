<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Models\Group;
use App\Models\Product;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ProductEditScreen extends Screen {
	/**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Create Product';

	/**
	 * Display header description.
	 *
	 * @var string
	 */
	public $description = 'Product data for sale & purchase records.';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(Product $product): array
	{
		$this->exists = $product->exists;

		if ($this->exists) {
			$this->name = 'Edit Product';
		}

		return [
			'product' => $product,
		];
	}

	/**
	 * Button commands.
	 *
	 * @return \Orchid\Screen\Action[]
	 */
	public function commandBar(): array
	{
		return [
			Button::make('Save product')
				->icon('pencil')
				->method('createOrUpdate')
				->canSee(!$this->exists),

			Button::make('Update')
				->icon('note')
				->method('createOrUpdate')
				->canSee($this->exists),

			// Button::make('Delete')
			// 	->icon('trash')
			// 	->confirm(__('Are you sure?'))
			// 	->method('remove')
			// 	->canSee($this->exists),
		];
	}

	/**
	 * Views.
	 *
	 * @return \Orchid\Screen\Layout[]|string[]
	 */
	public function layout(): array
	{
		return [
			Layout::rows([
				Input::make('product.code')->horizontal()
					->title('Code')
					->required()
					->placeholder('Code')
					->help('Code of the Product.'),
				Input::make('product.name')->horizontal()
					->title('Product Name')
					->required()
					->placeholder('Product Name')
					->help('Name of a product to buy or sell.'),
				Relation::make('product.category_id')->horizontal()
					->fromModel(Category::class, 'name')
					->title('Choose category')
					->required()
					->help('Choose category for the product.'),
				Input::make('product.buy_price')->horizontal()
					->title('Buy Price')
					->required()
					->placeholder('Buy Price')
					->help('Price of a product bought from supplier.'),
				Input::make('product.sale_price')->horizontal()
					->title('Sale Price')
					->required()
					->placeholder('Sale Price')
					->help('Price of a product to sell.'),
				// Input::make('product.quantity')->type('number')->horizontal()
				//     ->title('Quantity')
				//     ->required()
				//     ->placeholder('Quantity')
				//     ->help('Set stock amount of the warehouse.'),
				Relation::make('product.group_id')->horizontal()
					->fromModel(Group::class, 'name')
					->title('Choose group')
					->required()
					->help('Choose group for the product.'),
				Button::make(__('Save Product'))
					->icon('pencil')
					->method('createOrUpdate'),
			]),

		];
	}

	/**
	 * @param Product $product
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function createOrUpdate(Product $product, Request $request) {
		$product->fill($request->get('product'))->save();

		Alert::info('You have updated a product.');

		return redirect()->route('platform.product.list');
	}

	/**
	 * @param Product $product
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function remove(Product $product) {
		$product->delete();

		Alert::info('You have successfully deleted the product.');

		return redirect()->route('platform.product.list');
	}
}
