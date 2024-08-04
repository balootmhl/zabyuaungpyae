<?php

namespace App\Orchid\Screens;

use App\Exports\CustomersExport;
use App\Imports\CustomersImport;
use App\Models\Customer;
use App\Orchid\Layouts\CustomerListLayout;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class CustomerListScreen extends Screen {
	/**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Customers';

	/**
	 * Display header description.
	 *
	 * @var string
	 */
	public $description = 'All customers for sales record.';

    /**
     * @var string
     */
    public $permission = 'platform.people.customer';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(): array
	{
		return [
			'customers' => Customer::defaultSort('id')->get(),
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
			ModalToggle::make('Import')
				->modal('importModal')
				->method('import')
				->icon('cloud-upload'),

			Button::make('Export')
				->method('export')
				->icon('cloud-download')
				->rawClick()
				->novalidate(),

			Link::make('Create new')
				->icon('plus')
				->route('platform.customer.edit'),
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
			CustomerListLayout::class,

			Layout::modal('importModal', Layout::rows([
				Input::make('excel')
					->type('file')
					->acceptedFiles('.xlsx')
					->title('Upload excel file')
					->help('The data in the excel file will be created as new customers.')
					->required(),

			]))->title('Import customers from excel file'),
		];
	}

	/**
	 * @param Customer $customer
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 * @throws \Exception
	 */
	public function remove(Customer $customer) {
		$customer->delete();

		Alert::info('You have successfully deleted a customer.');

		return redirect()->route('platform.customer.list');
	}

	/**
	 * @return Export products and download as excel file
	 */
	public function export() {
		return Excel::download(new CustomersExport, 'customers_' . now() . '.xlsx');
	}

	/**
	 * @return Create products by importing excel file
	 */
	public function import(Request $request) {
		// dd($request);
		if ($request->hasFile('excel')) {
			Excel::import(new CustomersImport, $request->file('excel'), null, \Maatwebsite\Excel\Excel::XLSX);
			Alert::info('You have created new customers with excel file.');
		} else {
			Alert::error('Excel file import failed.');
		}

		return redirect()->route('platform.product.list');
	}
}
