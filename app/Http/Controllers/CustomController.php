<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Sale;
use App\Models\Saleitem;
// use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use PDF;

class CustomController extends Controller {
	public function deleteSaleItems($id) {
		$saleitem = Saleitem::findOrFail($id);
		$sale_id = $saleitem->sale_id;
		$product = Product::findOrFail($saleitem->product_id);
		$product->quantity = $product->quantity + $saleitem->quantity;
		$product->update();
		$saleitem->delete();
		$sale = Sale::findOrFail($sale_id);
		$subtotal = 0;

		foreach ($sale->saleitems as $sitem) {
			$item_total = $sitem->price * $sitem->quantity;
			$subtotal = $subtotal + $item_total;
		}

		$sale->sub_total = $subtotal;
		$sale->grand_total = $subtotal - $sale->discount;
		$sale->update();
		Toast::info('Item deleted successfully.');
		return redirect()->route('platform.sale.edit-custom', $sale_id);
	}

	public function deletePurchaseItems($id) {
		$purchaseitem = Purchaseitem::findOrFail($id);
		$purchase_id = $purchaseitem->purchase_id;
		$product = Product::findOrFail($purchaseitem->product_id);
		$product->quantity = $product->quantity - $purchaseitem->quantity;
		$product->update();
		$purchaseitem->delete();
		$purchase = Purchase::findOrFail($purchase_id);
		$subtotal = 0;

		foreach ($purchase->purchaseitems as $sitem) {
			$item_total = $sitem->price * $sitem->quantity;
			$subtotal = $subtotal + $item_total;
		}

		$purchase->sub_total = $subtotal;
		$purchase->grand_total = $subtotal - $purchase->discount;
		$purchase->update();
		Toast::info('Item deleted successfully.');
		return redirect()->route('platform.purchase.edit-custom', $purchase_id);
	}

	public function downloadInvoice($id) {
		$sale = Sale::findOrFail($id);
		$pdf = PDF::loadView('export.salepdf', compact('sale'))->setPaper('a4');

		return $pdf->stream('invoice_' . $sale->invoice_no . '.pdf');

	}

	public function downloadPInvoice($id) {
		$purchase = Purchase::findOrFail($id);
		$pdf = PDF::loadView('export.purchasepdf', compact('purchase'))->setPaper('a4');

		return $pdf->stream('invoice_' . $purchase->invoice_no . '.pdf');
	}

	public function stockControl() {
		$products = Product::all();

		return view('products.stock-control', compact('products'));
	}

	public function saveStock(Request $request) {
		// dd($request->all());
		foreach ($request->get('products') as $product_id) {
			$product = Product::findOrFail($product_id);
			$product->buy_price = $request->get('buy_price');
			$product->sale_price = $request->get('sale_price');
			$product->save();
		}

		// Alert::info('Product Saved!');
		Toast::success($request->get('toast', 'Prices Saved!'));
		return redirect()->route('platform.product.stock-control');
	}
}
