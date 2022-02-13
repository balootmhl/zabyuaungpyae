<?php

namespace App\Orchid\Screens;

use App\Models\Sale;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
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

            Button::make('Print')
                ->icon('printer')
                ->method('print'),

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

    public function download(Sale $sale)
    {

        $pdf = PDF::loadView('export.salepdf', compact('sale'));

        // return $pdf->download('invoice_' . $sale->invoice_no . '.pdf');
        return $pdf->download('invoice.pdf');
    }

    function print(Sale $sale) {

        $pdf = PDF::loadView('export.salepdf', compact('sale'));
        $pdf->save(storage_path('app/public/invoices/Sales' . $sale->invoice_no . '.pdf'));

        $printerId = Printing::defaultPrinterId();
        $printJob = Printing::newPrintTask()
            ->printer(71018000)
        // ->printer($printerId)
            ->file(storage_path('app/public/invoices/Sales' . $sale->invoice_no . '.pdf'))
            ->send();

        // $printJob->id(); // the id number returned from the print server

    }
}
