<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Sale;
use App\Models\Saleitem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class CustomController extends Controller {

	public function deleteSaleItems($id) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.sale')){
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
            $customer = Customer::findOrFail($sale->customer_id);
            $customer->debt = $customer->debt - $sale->remained;
            $customer->update();
            // $sale->remained = $sale->grand_total - $sale->received;
            // $sale->update();
            $sale->remained = $sale->grand_total - $sale->received;
            $sale->update();
            $customer->debt = $customer->debt + $sale->remained;
            $customer->update();
            Toast::info('Item deleted successfully.');
            return redirect()->route('platform.sale.edit-custom', $sale_id);
        } else {
            abort(403);
        }

	}

	public function deletePurchaseItems($id) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.purchase')){
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
        } else {
            abort(403);
        }

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
		$products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();

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

	public function exportProduct(Request $request)
	{
		return Excel::download(new ProductsExport(auth()->user()->id), 'products_'. auth()->user()->branch->slug .'_export_' . now() . '.xlsx');
	}

    public function fixNullUserGroup()
    {
        Group::whereNull('user_id')->update(['user_id' => 1]);

        return 'success';
    }
}
