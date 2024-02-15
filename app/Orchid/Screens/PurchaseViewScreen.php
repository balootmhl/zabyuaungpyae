<?php

namespace App\Orchid\Screens;

use App\Models\Purchase;
use Barryvdh\DomPDF\PDF;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class PurchaseViewScreen extends Screen {
	/**
	 * Display header name.
	 *
	 * @var string
	 */
	public $name = 'Purchase Invoice Preview';

	/**
	 * Query data.
	 *
	 * @return array
	 */
	public function query(Purchase $purchase): array
	{
		return [
			'purchase' => $purchase,
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
			Layout::view('purchases.preview'),
		];
	}

	public function edit(Purchase $purchase) {
		return redirect()->route('platform.purchase.edit', $purchase->id);
	}

	public function download(Purchase $purchase) {

		$pdf = PDF::loadView('export.purchasepdf', compact('purchase'));

		// return $pdf->download('invoice_' . $purchase->invoice_no . '.pdf');
		return $pdf->download('invoice.pdf');
	}
}
