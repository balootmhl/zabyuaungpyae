<?php

namespace App\Orchid\Screens;

use App\Models\Supplier;
use App\Orchid\Layouts\SupplierListLayout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SupplierListScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Suppliers';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'All supplier for purchase record.';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'suppliers' => Supplier::filters()->defaultSort('id')->paginate(),
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
                ->route('platform.supplier.edit'),
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
            SupplierListLayout::class,

            Layout::modal('importModal', Layout::rows([
                Input::make('excel')
                    ->type('file')
                    ->acceptedFiles('.xlsx')
                    ->title('Upload excel file')
                    ->help('The data in the excel file will be created as new customers.')
                    ->required(),

            ]))->title('Import suppliers from excel file'),
        ];
    }
}
