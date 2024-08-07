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

            $suppliers = Supplier::all();
            $users = User::all();
            return view('purchases.create', compact('products', 'suppliers', 'users'));
        } else {
            abort(403);
        }

	}

	public function store(Request $request) {
        $purchase = $this->createPurchase($request);

        if ($this->isProductValid($request)) {
            $this->createPurchaseItem($request, $purchase);
        }

        $this->updateTotals($purchase);

        Toast::success('Invoice Saved.');
        return redirect()->route('platform.purchase.edit-custom', $purchase->id);
    }

    public function update(Request $request) {
        $purchase = Purchase::findOrFail($request->get('purchase_id'));

        $this->updatePurchase($purchase, $request);

        if ($this->isProductValid($request)) {
            $this->createPurchaseItem($request, $purchase);
        }

        $this->updateTotals($purchase);

        Toast::success('Invoice Saved.');
        return redirect()->route('platform.purchase.edit-custom', $purchase->id);
    }

    private function updatePurchase(Purchase $purchase, Request $request) {
        $supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);

        $purchase->update([
            'invoice_code' => $request->get('invoice_code'),
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch->id,
            'supplier_id' => $supplier->id,
            'is_inv_auto' => $request->get('is_inv_auto'),
            'date' => $request->get('date'),
            'custom_name' => $supplier->name,
            'custom_address' => $request->get('address'),
            'discount' => $request->get('discount'),
            'received' => $request->get('received'),
        ]);
    }

    private function isProductValid(Request $request) {
        return $request->filled('product') && $request->get('price') != 0 && $request->get('qty') != 0;
    }

    private function createPurchaseItem(Request $request, Purchase $purchase) {
        $product = Product::findOrFail($request->get('product'));

        $purchaseItem = new Purchaseitem([
            'product_id' => $product->id,
            'purchase_id' => $purchase->id,
            'code' => $product->code,
            'name' => $product->name,
            'quantity' => $request->get('qty'),
            'price' => $request->get('price'),
        ]);

        $purchaseItem->save();

        $product->increment('quantity', $purchaseItem->quantity);
    }

    private function updateTotals(Purchase $purchase) {
        $subtotal = $purchase->purchaseitems->sum(fn($item) => $item->price * $item->quantity);

        $purchase->update([
            'sub_total' => $subtotal,
            'grand_total' => $subtotal - $purchase->discount,
            'remained' => $purchase->received ? $subtotal - $purchase->discount - $purchase->received : 0,
        ]);
    }


    private function createPurchase(Request $request) {
        $yearMonth = str_replace("20", "", date('Y')) . date('m');
        $supplier = Supplier::firstOrCreate(['name' => $request->get('supplier_id')]);

        $purchase = new Purchase([
            'invoice_code' => $request->get('invoice_code'),
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch->id,
            'supplier_id' => $supplier->id,
            'is_inv_auto' => $request->get('is_inv_auto'),
            'date' => $request->get('date'),
            'custom_name' => $supplier->name,
            'custom_address' => $request->get('address'),
            'discount' => $request->get('discount'),
            'received' => $request->get('received'),
        ]);

        if ($request->get('is_inv_auto') == 0) {
            $purchase->invoice_no = "#{$yearMonth}{$request->get('invoice_code')}";
        }

        $purchase->save();

        if ($request->get('is_inv_auto') == 1) {
            $purchase->invoice_no = '#02' . str_replace("-", "", $purchase->date) . $purchase->id;
            $purchase->save();
        }

        return $purchase;
    }

	public function edit($id) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.purchase')){
            $purchase = Purchase::findOrFail($id);
            $items_count = count($purchase->purchaseitems);
            $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();
            $suppliers = Supplier::all();
            $users = User::all();

            return view('purchases.edit', compact('products', 'suppliers', 'users', 'purchase', 'items_count'));
        } else {
            abort(403);
        }

	}
}
