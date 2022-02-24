<?php

namespace App\Orchid\Screens;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ProductListScreen extends Screen
{
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
            'products' => Product::filters()->orderby('created_at', 'desc')->paginate(100),
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
                ->route('platform.product.edit'),
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
            // ProductFiltersLayout::class,
            ProductListLayout::class,

            Layout::modal('importModal', Layout::rows([
                Input::make('excel')
                    ->type('file')
                    ->acceptedFiles('.xlsx')
                    ->title('Upload excel file')
                    ->help('The data in the excel file will be created as new products.')
                    ->required(),

            ]))->title('Import products from excel file'),
        ];
    }

    /**
     * @param Product $product
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remove(Product $product)
    {
        $product->delete();

        Alert::info('You have successfully deleted the product.');

        return redirect()->route('platform.product.list');
    }

    /**
     * @return Export products and download as excel file
     */
    public function export()
    {
        return Excel::download(new ProductsExport, 'products_' . now() . '.xlsx');
    }

    /**
     * @return Create products by importing excel file
     */
    public function import(Request $request)
    {
        // dd($request);
        if ($request->hasFile('excel')) {
            Excel::import(new ProductsImport, $request->file('excel'), null, \Maatwebsite\Excel\Excel::XLSX);
            Alert::info('You have created new products with excel file.');
        } else {
            Alert::error('Excel file import failed.');
        }

        return redirect()->route('platform.product.list');
    }

}
