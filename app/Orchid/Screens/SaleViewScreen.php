<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use PDF;
use Rawilk\Printing\Facades\Printing;

class SaleViewScreen extends Screen
{

    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Sale Invoice Preview';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Sale $sale): array
    {
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

            

            // Link::make(__('Print/Download'))
            //     ->icon('printer')
            //     ->route('platform.sale.print')
            //     ->target('_blank'),

            Button::make('Edit')
                ->icon('pencil')
                ->method('edit'),
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
