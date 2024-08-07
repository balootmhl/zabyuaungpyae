<?php

namespace App\Orchid\Screens;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\Purchaseitem;
use App\Orchid\Layouts\ProductListLayout;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Attachment\File;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\DropDown;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class StockControlScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Stock Control';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('id', 'asc')->get();
        return [
            'products' => $products,
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
            Button::make('Submit')
                ->icon('pencil')
                ->method('updateStock'),
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
            Layout::view('products.products-formtable'),
        ];
    }


    public function updateStock(Request $request)
    {
        $items = $request->get('products');
        foreach ($items as $item) {
            $product = Product::findOrFail($item['id']);
            if($product->buy_price && $product->buy_price != $item['buy_price']){
                $product->buy_price = $item['buy_price'];
                $product->update();
                Alert::info('Batch Operation Success.');
            }
            if($product->sale_price && $product->sale_price != $item['sale_price']){
                $product->sale_price = $item['sale_price'];
                $product->update();
                Alert::info('Batch Operation Success.');
            }
            // $product->update();
        }
        // Alert::info('Batch Operation Success.');

        return redirect()->route('platform.product.stock-control');
    }
}
