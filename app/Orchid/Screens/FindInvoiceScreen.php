<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\Saleitem;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class FindInvoiceScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Find Invoices';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Search invoices with specific date and products.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [];
    }

    /**
     * Button commands.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [];
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
                Group::make([
                    DateTimer::make('date')
                        ->title('Select Invoice Date')
                        ->format('Y-m-d')
                        ->required()
                        ->help("Choose date to search invoices."),
                    Select::make('product_id')
                        ->title('Select Product')
                        ->fromModel(Product::class, 'name')
                        ->required()
                        ->empty('No select')
                        ->placeholder('Select Product')
                        ->help("Select product to search invoices."),

                ])->fullwidth(),
                Button::make(__('Submit'))
                    ->title('')
                    ->icon('magnifier')
                    ->method('search'),
            ]),
            Layout::view('sales.search-results'),

        ];
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function search(Request $request)
    {
        $items = Saleitem::where('product_id', $request->get('product_id'))->get();
        $search_date = $request->get('date');
        session(['items' => $items, 'search_date' => $search_date]);
        // Alert::info('Sale Invoice is deleted successfully. Product Quantity are returning back.');

        return redirect()->route('platform.sale.search')->with(['items' => $items, 'search_date' => $search_date])->withInput();
    }
}
