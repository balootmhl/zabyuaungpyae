<?php

namespace App\Orchid\Screens;

use App\Models\Purchase;
use App\Orchid\Layouts\PurchaseListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class PurchaseListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Purchase Invoices';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All purchase invoice list of the company.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'purchases' => Purchase::filters()->orderby('created_at', 'desc')->paginate(20),
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

            Button::make('Export')
                ->method('export')
                ->icon('cloud-download')
                ->rawClick()
                ->novalidate(),

            Link::make('Create beta')
                ->icon('plus')
                ->route('platform.purchase.create-custom'),

            Link::make('Create new')
                ->icon('plus')
                ->route('platform.purchase.edit'),
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
            Layout::view('plugins.filter-box'),
            PurchaseListLayout::class,
            // Layout::modal('importModal', Layout::rows([
            //     Input::make('excel')
            //         ->type('file')
            //         ->acceptedFiles('.xlsx')
            //         ->title('Upload excel file')
            //         ->help('The data in the excel file will be created as new sales invoice.')
            //         ->required(),

            // ]))->title('Import Purchase invoices from excel file'),
        ];
    }

    /**
     * @param Purchase $purchase
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Purchase $purchase)
    {
        $purchaseitems = $purchase->saleitems;
        if ($purchaseitems) {
            foreach ($purchaseitems as $purchaseitem) {
                $product = Product::findOrFail($purchaseitem->product_id);
                $product->quantity = $product->quantity + $purchaseitem->quantity;
                $product->update();
                $purchaseitem->delete();
            }
        }
        $purchase->delete();

        Alert::info('Purchase Invoice is deleted successfully. Product Quantity are returning back.');

        return redirect()->route('platform.purchase.list');
    }

    /**
     * @return Export products and download as excel file
     */
    public function export()
    {
        // return Excel::download(new ProductsExport, 'products_' . now() . '.xlsx');
    }
}
