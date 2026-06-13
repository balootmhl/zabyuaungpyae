<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Toast;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller {
	public function create() {
        /** @var \App\Models\User $user */
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
		try {
			$purchase = DB::transaction(function () use ($request) {
				$year = str_replace("20", "", date('Y'));
				$month = date('m');
				$supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);
				
				$purchase = new Purchase();
				$purchase->invoice_code = $request->get('invoice_code');
				$purchase->user_id = auth()->user()->id;
				$purchase->branch_id = auth()->user()->branch->id;
				$purchase->supplier_id = $supplier->id;
				$purchase->is_inv_auto = $request->get('is_inv_auto');
				$purchase->date = $request->get('date');
				$purchase->custom_name = $supplier->name;
				$purchase->custom_address = $request->get('address');
				$purchase->discount = $request->get('discount');
				$purchase->received = $request->get('received');
				$purchase->sub_total = 0;
				$purchase->grand_total = 0;
				$purchase->remained = 0;

				if ($request->get('is_inv_auto') == 0) {
					$purchase->invoice_no = '#' . $year . $month . $request->get('invoice_code');
				}
				
				$purchase->save();

				if ($request->get('is_inv_auto') == 1) {
					$purchase->invoice_no = '#02' . str_replace("-", "", $purchase->date) . $purchase->id;
				}

				$subtotal = 0;
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
					$subtotal += $purchaseitem->price * $purchaseitem->quantity;
				}

				$purchase->sub_total = $subtotal;
				$purchase->grand_total = $subtotal - $purchase->discount;
				if ($purchase->received != 0) {
					$purchase->remained = $purchase->grand_total - $purchase->received;
				} else {
					$purchase->remained = $purchase->grand_total;
				}

				$purchase->update();
				return $purchase;
			});

			Toast::success('Invoice Saved.');
			return redirect()->route('platform.purchase.edit-custom', $purchase->id);

		} catch (\Exception $e) {
			Toast::error('Failed to save purchase invoice: ' . $e->getMessage());
			return redirect()->back()->withInput();
		}
	}

	public function edit($id) {
        /** @var \App\Models\User $user */
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
		try {
			$purchase = DB::transaction(function () use ($request) {
				$purchase = Purchase::findOrFail($request->get('purchase_id'));
				$supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);
				
				$purchase->invoice_code = $request->get('invoice_code');
				$purchase->user_id = auth()->user()->id;
				$purchase->branch_id = auth()->user()->branch->id;
				$purchase->supplier_id = $supplier->id;
				$purchase->is_inv_auto = $request->get('is_inv_auto');
				$purchase->date = $request->get('date');
				$purchase->custom_name = $supplier->name;
				$purchase->custom_address = $request->get('address');
				$purchase->discount = $request->get('discount');

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

				$purchase->unsetRelation('purchaseitems');
				$subtotal = 0;
				foreach ($purchase->purchaseitems as $pitem) {
					$subtotal += $pitem->price * $pitem->quantity;
				}
				
				$purchase->sub_total = $subtotal;
				$purchase->grand_total = $subtotal - $purchase->discount;
				if ($purchase->received != 0) {
					$purchase->remained = $purchase->grand_total - $purchase->received;
				} else {
					$purchase->remained = $purchase->grand_total;
				}
				$purchase->update();
				
				return $purchase;
			});

			Toast::success('Invoice Saved.');
			return redirect()->route('platform.purchase.edit-custom', $purchase->id);

		} catch (\Exception $e) {
			Toast::error('Failed to update purchase invoice: ' . $e->getMessage());
			return redirect()->back()->withInput();
		}
	}
}
