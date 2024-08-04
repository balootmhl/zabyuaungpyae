<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchaseitem;
use App\Models\Sale;
use App\Models\Saleitem;
use App\Orchid\Filters\ItemsFilter;
use App\Orchid\Layouts\SaleitemFiltersLayout;
use App\Orchid\Layouts\SaleListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Modal;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class SaleListScreen extends Screen {
	/**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Sale Invoices';

	/**
	 * Display header description.
	 *
	 * @var string
	 */
	public $description = 'All sale invoices list of the company.';

    /**
     * The permission required to access this screen.

     * @var string
     */
    public $permission = 'platform.module.sale';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(): array
	{

		return [
			'sales' => Sale::where('user_id', auth()->user()->id)->filtersApply([ItemsFilter::class])->orderby('created_at', 'desc')->paginate(50),
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

			DropDown::make('Fix')
				->icon('wrench')
				->list([
					// ModalToggle::make('Import')
					//     ->modal('importModal')
					//     ->method('import')
					//     ->icon('cloud-upload'),

					// Button::make('Fix Prices')
					//     ->method('fixPrice')
					//     ->icon('wrench')
					//     ->novalidate(),

					Button::make('Fix Names')
						->method('itemsName')
						->icon('wrench')
						->novalidate(),

					Button::make('Item Prices')
						->method('itemsPrice')
						->icon('wrench')
						->novalidate(),

					// Button::make('Export')
					// 	->method('export')
					// 	->icon('cloud-download')
					// 	->rawClick()
					// 	->novalidate(),
				]),

			Link::make('Find Invoices')
				->icon('magnifier')
				->route('platform.sale.search'),

			Link::make('Create')
				->icon('plus')
				->route('platform.sale.create-custom'),

			// Link::make('Create new')
			//     ->icon('plus')
			//     ->route('platform.sale.edit'),
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
			// Layout::view('products.livefilter'),
			SaleitemFiltersLayout::class,
			SaleListLayout::class,
			// Layout::modal('importModal', Layout::rows([
			//     Input::make('excel')
			//         ->type('file')
			//         ->acceptedFiles('.xlsx')
			//         ->title('Upload excel file')
			//         ->help('The data in the excel file will be created as new sales invoice.')
			//         ->required(),

			// ]))->title('Import sale invoices from excel file'),
		];
	}

	/**
	 * @param Sale $sale
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function remove(Sale $sale) {
		$saleitems = $sale->saleitems;
		if ($saleitems) {
			foreach ($saleitems as $saleitem) {
				$product = Product::findOrFail($saleitem->product_id);
				$product->quantity = $product->quantity + $saleitem->quantity;
				$product->update();
				$saleitem->delete();
			}
		}
		$sale->delete();

		Alert::info('Sale Invoice is deleted successfully. Product Quantity are returning back.');

		return redirect()->route('platform.sale.list');
	}

	/**
	 * @return Export products and download as excel file
	 */
	public function export() {
		// return Excel::download(new ProductsExport, 'products_' . now() . '.xlsx');
	}

	/**
	 * @return Fix prices of each sale invoices
	 */
	public function fixPrice() {
		$sales = Sale::all();
		foreach ($sales as $sale) {
			$subtotal = 0;
			foreach ($sale->saleitems as $sitem) {
				$item_total = $sitem->product->buy_price * $sitem->quantity;
				$subtotal = $subtotal + $item_total;
			}

			$sale->sub_total = $subtotal;
			$sale->grand_total = $subtotal - $sale->discount;
			// if ($sale->invoice_no == null) {
			//     $sale->invoice_no = '#01' . str_replace("-", "", $sale->date) . $sale->id;
			// }
			$sale->update();

		}

		Alert::info('You have updated prices of sale invoices.');

		return redirect()->route('platform.sale.list');
	}

	public function customName($value = '') {
		$sales = Sale::all();
		foreach ($sales as $sale) {
			if ($sale->custom_name == null || $sale->custom_name == '') {
				$customer = Customer::findOrFail($sale->customer->id);
				$sale->custom_name = $customer->name;
				$sale->save();
			}
		}
		Alert::success('The names are fixed.');
		return redirect()->route('platform.sale.list');
	}

	public function itemsName($value = '') {
		$sitems = Saleitem::all();
		$pitems = Purchaseitem::all();
		foreach ($sitems as $s) {
			// $sp = Product::findOrFail($s->product_id);
			if ($s->product->code != null) {
				$s->name = $s->product->name;
				$s->code = $s->product->code;
				$s->update();
			}

		}
		foreach ($pitems as $p) {
			// $pp = Product::findOrFail($p->product_id);
			if ($p->product->code != null) {
				$p->name = $p->product->name;
				$p->code = $p->product->code;
				$p->update();
			}
		}
		Toast::success('Fixed the name of sale items & purchase items.');
		return redirect()->route('platform.sale.list');
	}

	public function itemsPrice($value = '') {
		$sitems = Saleitem::all();
		$pitems = Purchaseitem::all();
		foreach ($sitems as $s) {
			// $sp = Product::findOrFail($s->product_id);
			if ($s->product) {
				if ($s->sale->is_saleprice == 1) {
					$s->price = $s->product->sale_price;
				} elseif ($s->sale->is_saleprice == 0) {
					$s->price = $s->product->buy_price;
				}
				$s->update();
			}

		}
		foreach ($pitems as $p) {
			if ($p->product) {
				if ($p->price == NULL || $p->price == 0) {
					$p->price = $p->product->buy_price;
				}
				$p->update();
			}
		}
		Toast::success('Fixed the prices of sale & purchase items.');
		return redirect()->route('platform.sale.list');
	}
}
