<?php

namespace App\Orchid\Screens;

use App\Imports\ProductsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class ProductImportScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Import Products';

    /**
     * The permission required to access this screen.

     * @var string
     */
    public $permission = 'platform.module.product';

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
        return [
            Link::make('Go Back')
                ->icon('action-undo')
                ->route('platform.product.list'),
            Button::make('Submit Import')
                ->icon('cloud-upload')
                ->method('import'),
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
                Input::make('excel')
                    ->type('file')
                    ->acceptedFiles('.xlsx')
                    ->title('Upload excel file')
                    ->help('The data in the excel file will be created as new products.')
                    ->required(),
            ]),
        ];
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
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
