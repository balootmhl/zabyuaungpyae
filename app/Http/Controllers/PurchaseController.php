<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Toast;

class PurchaseController extends Controller {
	public function create() {
        $user = auth()->user();
        if($user->hasAccess('platform.module.purchase')){
            $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();
            // if(auth()->user()->id == 1){
            // 	$products = Product::orderby('created_at', 'DESC')->get();
            // } else {
            // 	$products = Product::where('user_id', auth()->user()->id)->orderby('created_at', 'DESC')->get();
            // }

            $suppliers = Supplier::all();
            $users = User::all();
            return view('purchases.create', compact('products', 'suppliers', 'users'));
        } else {
            abort(403);
        }

	}

	public function store(Request $request) {
		$year = str_replace("20", "", date('Y'));
		$month = date('m');
		$purchase = new Purchase();
		$supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);
		$purchase->invoice_code = $request->get('invoice_code');
		if ($request->get('is_inv_auto') == 0) {
			$purchase->invoice_no = '#' . $year . $month . $request->get('invoice_code');
		}
		$purchase->user_id = auth()->user()->id;
		$purchase->branch_id = auth()->user()->branch->id;
		$purchase->supplier_id = $supplier->id;
		$purchase->is_inv_auto = $request->get('is_inv_auto');
		$purchase->date = $request->get('date');
		$purchase->custom_name = $supplier->name;
		$purchase->custom_address = $request->get('address');
		$purchase->discount = $request->get('discount');
		$purchase->received = $request->get('received');
		$purchase->save();
		if ($request->get('is_inv_auto') == 1) {
			$purchase->invoice_no = '#02' . str_replace("-", "", $purchase->date) . $purchase->id;
			$purchase->update();
		}
		if ($request->get('product') != null && $request->get('price') != 0 && $request->get('qty') != 0) {
			$product = Product::findOrFail($request->get('product'));
			$purchaseitem = new Purchaseitem();
			$purchaseitem->product_id = $request->get('product');
			$purchaseitem->purchase_id = $purchase->id;
			$purchaseitem->code = $product->code;
			$purchaseitem->name = $product->name;
			$purchaseitem->quantity = $request->get('qty');
			$purchaseitem->price = $request->get('price');
			$purchaseitem->save();
			$product->quantity = $product->quantity + $purchaseitem->quantity;
			$product->update();
		}
		$subtotal = 0;
		foreach ($purchase->purchaseitems as $pitem) {
			$item_total = $pitem->price * $pitem->quantity;
			$subtotal = $subtotal + $item_total;
		}
		$purchase->sub_total = $subtotal;
		$purchase->grand_total = $subtotal - $purchase->discount;
		if ($purchase->received != 0) {
			$purchase->remained = $purchase->grand_total - $purchase->received;
		}

		$purchase->update();
		Toast::success('Invoice Saved.');
		return redirect()->route('platform.purchase.edit-custom', $purchase->id);
	}

	public function edit($id) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.purchase')){
            $purchase = Purchase::findOrFail($id);
            $items_count = count($purchase->purchaseitems);
            $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();
            // if(auth()->user()->id == 1){
            // 	$products = Product::orderby('created_at', 'DESC')->get();
            // } else {
            // 	$products = Product::where('user_id', auth()->user()->id)->orderby('created_at', 'DESC')->get();
            // }
            $suppliers = Supplier::all();
            $users = User::all();

            return view('purchases.edit', compact('products', 'suppliers', 'users', 'purchase', 'items_count'));
        } else {
            abort(403);
        }

	}

	public function update(Request $request) {
		$purchase = Purchase::findOrFail($request->get('purchase_id'));
		$purchase->invoice_code = $request->get('invoice_code');
		$supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);
		$purchase->user_id = auth()->user()->id;
		$purchase->branch_id = auth()->user()->branch->id;
		$purchase->supplier_id = $supplier->id;
		$purchase->is_inv_auto = $request->get('is_inv_auto');
		$purchase->date = $request->get('date');
		$purchase->custom_name = $supplier->name;
		$purchase->custom_address = $request->get('address');
		$purchase->discount = $request->get('discount');
		// $purchase->remarks = $request->get('remarks');
		$purchase->save();

		if ($request->get('product') != null && $request->get('price') != 0 && $request->get('qty') != 0) {
			$product = Product::findOrFail($request->get('product'));
			$purchaseitem = new Purchaseitem();
			$purchaseitem->product_id = $request->get('product');
			$purchaseitem->purchase_id = $purchase->id;
			$purchaseitem->code = $product->code;
			$purchaseitem->name = $product->name;
			$purchaseitem->quantity = $request->get('qty');
			$purchaseitem->price = $request->get('price');
			$purchaseitem->save();
			// $product = Product::findOrFail($purchaseitem->product_id);
			$product->quantity = $product->quantity + $purchaseitem->quantity;
			$product->update();
		}
		$subtotal = 0;
		foreach ($purchase->purchaseitems as $pitem) {
			$item_total = $pitem->price * $pitem->quantity;
			$subtotal = $subtotal + $item_total;
		}
		$purchase->sub_total = $subtotal;
		$purchase->grand_total = $subtotal - $purchase->discount;
		if ($purchase->received != 0) {
			$purchase->remained = $purchase->grand_total - $purchase->received;
		}
		$purchase->update();
		Toast::success('Invoice Saved.');

		return redirect()->route('platform.purchase.edit-custom', $purchase->id);
	}
}
