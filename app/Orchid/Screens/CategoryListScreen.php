<?php

namespace App\Orchid\Screens;

use App\Models\Category;
use App\Orchid\Layouts\CategoryListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CategoryListScreen extends Screen {
	/**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Categories';

	/**
	 * Display header description.
	 *
	 * @var string
	 */
	public $description = 'All categories of product';

    /**
     * @var string
     */
    public $permission = 'platform.module.category';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(): array
	{
		return [
			'categories' => Category::orderby('created_at', 'desc')->get(),
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
			Link::make('Create new')
				->icon('plus')
				->route('platform.category.edit'),
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
			Layout::view('products.livefilter'),
			CategoryListLayout::class,
		];
	}

	/**
	 * @param Category $category
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function remove(Category $category) {
		$category->delete();

		Alert::info('You have successfully deleted the category.');

		return redirect()->route('platform.category.list');
	}
}
