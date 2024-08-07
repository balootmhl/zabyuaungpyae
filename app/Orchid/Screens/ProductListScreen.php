<?php

namespace App\Orchid\Screens;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Orchid\Filters\QueryFilter;
use App\Orchid\Layouts\ProductFiltersLayout;
use Orchid\Platform\Models\User;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Fields\Select;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductListScreen extends Screen {
	/**
     * The permission required to access this screen.

     * @var string
     */
    public $permission = 'platform.module.product';

    /**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Products';

	/**
	 * Display header description.
	 *
	 * @var string
	 */
	public $description = 'All products for sales or purchase record.';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(): array
	{
		return [
			'products' => Product::where('branch_id', auth()->user()->branch->id)->filtersApply([QueryFilter::class])->orderby('created_at', 'desc')->paginate(20),
		];
	}

	/**
	 * Button commands.
	 *
	 * @return \Orchid\Screen\Action[]
	 */
	public function commandBar(): array
	{
		if(auth()->user()->id == 1) {
			return [
                Link::make('Stock Control')
                    ->icon('wrench')
                    ->route('platform.product.stock-control'),
				// DropDown::make('Manage Stock')
				// 	->icon('loading')
				// 	->list([
				// 		ModalToggle::make('Reset Stock')
				// 			->modal('resetModal')
				// 			->method('resetQuantity')
				// 			->icon('reload'),
				// 		Link::make('Stock Control')
				// 			->icon('wrench')
				// 			->route('platform.product.stock-control'),
				// 		Button::make('Clear Groups')
				// 			->method('clearGroup')
                //             ->confirm('Are you Sure?')
				// 			->icon('wrench'),
				// 		Button::make('Claim')
				// 			->method('claimProducts')
				// 			->icon('wrench'),
				// 		ModalToggle::make('Share to')
				// 			->modal('shareModal')
				// 			->method('duplicate')
				// 			->icon('share-alt'),
				// 		ModalToggle::make('Calibrate qty')
				// 		    ->modal('fixModal')
				// 		    ->method('fix')
				// 		    ->icon('refresh'),
				// 	]),

				DropDown::make('Import/Export')
					->icon('wrench')
					->list([

						ModalToggle::make('Import')
							->modal('importModal')
							->method('import')
							->icon('cloud-upload'),

						// ModalToggle::make('Export')
						// 	->modal('exportModal')
						// 	->method('export')
						// 	->icon('cloud-download'),

						Link::make('Export')
							->route('platform.product.export-custom')
							->icon('cloud-download'),
					]),

				Link::make('Create new')
					->icon('plus')
					->route('platform.product.edit'),
			];
		} else {
			return [
                Link::make('Stock Control')
                    ->icon('wrench')
                    ->route('platform.product.stock-control'),
				// DropDown::make('Manage Stock')
				// 	->icon('loading')
				// 	->list([
				// 		Link::make('Stock Control')
				// 			->icon('wrench')
				// 			->route('platform.product.stock-control'),
				// 	]),
				DropDown::make('Import/Export')
					->icon('wrench')
					->list([
						Link::make('Export')
							->route('platform.product.export-custom')
							->icon('cloud-download'),
					]),
				Link::make('Create new')
				->icon('plus')
				->route('platform.product.edit'),
			];
		}
	}

	/**
	 * Views.
	 *
	 * @return \Orchid\Screen\Layout[]|string[]
	 */
	public function layout(): array
	{
		return [
			ProductFiltersLayout::class,
			// Layout::view('products.filter-box'),
			ProductListLayout::class,

			Layout::modal('importModal', Layout::rows([
				Input::make('excel')
					->type('file')
					->acceptedFiles('.xlsx')
					->title('Upload excel file')
					->help('The data in the excel file will be created as new products.')
					->required(),

			]))->title('Import products from excel file'),

			Layout::modal('resetModal', Layout::rows([
				Input::make('qty')
					->type('number')
					->title('Enter amount to reset all product quantity.')
					->help('The amount submitted will be saved as the quantity of all products.')
					->required(),

			]))->title('Reset the quantity of all products.'),

			Layout::modal('shareModal', Layout::rows([
				Select::make('user_id')
                    ->fromModel(User::class, 'name')
                    ->required()->title('Select branch to share warehouse products.')
                    ->empty('No select')
                    ->placeholder('Choose Branch'),

			]))->title('Share the warehouse products to branches to use separately.'),

			Layout::modal('exportModal', Layout::rows([
				Select::make('user_id')
                    ->fromModel(User::class, 'name')
                    ->title('Select branch to export their products.')
                    ->placeholder('Choose Branch'),

			]))->title('Export/Download any branch products you want.'),

			Layout::modal('fixModal', Layout::rows([
				Input::make('qty')
					->type('hidden')
					->title('Running this function will change product quantities.'),

			]))->title('Are you sure ?'),
		];
	}

	/**
	 * @param Product $product
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function remove(Product $product) {
		$product->delete();

		Toast::info('You have successfully deleted the product.');

		return redirect()->route('platform.product.list');

	}

	/**
	 * @return Export products and download as excel file
	 */
	// public function export(Request $request) {
	// 	$user = User::findOrFail($request->get('user_id'));
	// 	return Excel::download(new ProductsExport($request->get('user_id')), 'products_of_'. $user->name .'_export_'. now() . '.xlsx');
	// }

	/**
	 * @return Export products and download as excel file
	 */
	// public function exportBranch() {
	// 	return Excel::download(new ProductsExport(auth()->user()->id), 'products_of_'. auth()->user()->name .'_export_' . now() . '.xlsx');
	// }

	/**
	 * @return Create products by importing excel file
	 */
	public function import(Request $request) {
		// dd($request);
		if ($request->hasFile('excel')) {
			Excel::import(new ProductsImport, $request->file('excel'), null, \Maatwebsite\Excel\Excel::XLSX);
			Toast::info('You have created new products with excel file.');
		} else {
			Toast::error('Excel file import failed.');
		}

		return redirect()->route('platform.product.list');
	}

	// Not Used
    // public function resetQuantity(Request $request) {
	// 	$products = Product::where('user_id', auth()->user()->id)->orderby('created_at', 'DESC')->get();
	// 	foreach ($products as $product) {
	// 		$product->quantity = $request->get('qty');
	// 		$product->update();
	// 	}
	// 	Toast::info('All products are reset to quantity ' . $request->get('qty') . '.');
	// 	return redirect()->route('platform.product.list');
	// }

	// NOT USED
    // public function fixQuantity() {
	// 	$sales = Sale::all();
	// 	$purchases = Purchase::all();

	// 	foreach ($purchases as $purchase) {
	// 		foreach($purchase->purchaseitems as $purchaseitem){
	// 			$pp = Product::findOrFail($purchaseitem->product_id);
	// 			$pp->quantity = $pp->quantity + $purchaseitem->quantity;
	// 			$pp->update();
	// 		}
	// 	}

	// 	foreach ($sales as $sale) {
	// 		foreach($sale->saleitems as $saleitem){
	// 			$sp = Product::findOrFail($saleitem->product_id);
	// 			$sp->quantity = $sp->quantity - $saleitem->quantity;
	// 			$sp->update();
	// 		}
	// 	}
	// 	Toast::info('Calculated product quantities using invoices.');
	// 	return redirect()->route('platform.product.list');
	// }

	// NOT USED
    // public function clearGroup() {
	// 	$products = Product::all();
	// 	foreach ($products as $product) {
	// 		$product->group_id = null;
	// 		$product->update();
	// 	}
	// 	Toast::info('Cleared product groups.');
	// 	return redirect()->route('platform.product.list');
	// }

	public function claimProducts($value = '') {
		$products = Product::all();
		foreach ($products as $product) {
			$product->user_id = auth()->user()->id;
			$product->update();
		}
		Toast::info('Claimed products as current admin.');
		return redirect()->route('platform.product.list');
	}

	public function duplicate(Request $request)
	{
		$products = Product::where('user_id', 1)->orderby('created_at', 'asc')->get();
		$user = User::findOrFail($request->get('user_id'));
		foreach ($products as $product) {
			$p = new Product();
			$p->code = $product->code;
			$p->name = $product->name;
			$p->user_id = $user->id;
			$p->category_id = $product->category_id;
			$p->group_id = $product->group_id;
			$p->buy_price = $product->buy_price;
			$p->sale_price = $product->sale_price;
			$p->quantity = 0;
			$p->save();
		}
		Toast::success('Shared warehouse products to '.$user->name.'.');
		return redirect()->route('platform.product.list');
	}

	public function fix() {
		$products = Product::where('user_id', 2)->get();
		$purchases = Purchase::where('user_id', 2)->get();
		$sales = Sale::where('user_id', 2)->get();
		foreach($products as $product){
			$product->quantity = 0;
			$product->update();

		}
		foreach($purchases as $purchase){
			foreach($purchase->purchaseitems as $pitem){
				$pp = Product::findOrFail($pitem->product_id);
				$pp->quantity = $pp->quantity + $pitem->quantity;
				$pp->update();
			}
		}
		foreach($sales as $sale){
			foreach($sale->saleitems as $sitem){
				$sp = Product::findOrFail($sitem->product_id);
				$sp->quantity = $sp->quantity - $sitem->quantity;
				$sp->update();
			}
		}

		Toast::success('Calibrated invoices and product quantities.');
		return redirect()->route('platform.product.list');
	}

}
