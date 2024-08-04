<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class SaleViewScreen extends Screen
{

    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Sale Invoice';

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
    public function query(Sale $sale): array
    {
        // $this->name = 'Sales_'.$sale->invoice_no;

        return [
            'sale' => $sale,
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
            // Button::make('Download')
            //     ->icon('cloud-download')
            //     ->method('download'),

            // Button::make('Edit')
            //     ->icon('pencil')
            //     ->method('edit'),
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
            Layout::view('sales.preview'),
        ];
    }

    public function edit(Sale $sale)
    {
        return redirect()->route('platform.sale.edit', $sale->id);
    }

    // function printDownload(Sale $sale) {

    //     $pdf = PDF::loadView(utf8_decode('export.salepdf'), compact('sale'));

    //     return $pdf->stream('invoice_' . $sale->invoice_no . '.pdf', array('Attachment' => 0, 'compress' => 1));

    // }
}
