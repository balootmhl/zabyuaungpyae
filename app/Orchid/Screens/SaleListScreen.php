<?php

namespace App\Orchid\Screens;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Orchid\Layouts\SaleListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class SaleListScreen extends Screen
{
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
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'sales' => Sale::orderby('created_at', 'desc')->get(),
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
            // ModalToggle::make('Import')
            //     ->modal('importModal')
            //     ->method('import')
            //     ->icon('cloud-upload'),

            // Button::make('Fix Prices')
            //     ->method('fixPrice')
            //     ->icon('wrench')
            //     ->novalidate(),

            Button::make('Fix Names')
                ->method('customName')
                ->icon('wrench')
                ->novalidate(),

            Button::make('Export')
                ->method('export')
                ->icon('cloud-download')
                ->rawClick()
                ->novalidate(),

            Link::make('Find Invoices')
                ->icon('magnifier')
                ->route('platform.sale.search'),

            Link::make('Create beta')
                ->icon('plus')
                ->route('platform.sale.create-custom'),

            Link::make('Create new')
                ->icon('plus')
                ->route('platform.sale.edit'),
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
            Layout::view('products.filter-box'),
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
    public function remove(Sale $sale)
    {
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
    public function export()
    {
        // return Excel::download(new ProductsExport, 'products_' . now() . '.xlsx');
    }

    /**
     * @return Fix prices of each sale invoices
     */
    public function fixPrice()
    {
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

    public function customName($value = '')
    {
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
}
