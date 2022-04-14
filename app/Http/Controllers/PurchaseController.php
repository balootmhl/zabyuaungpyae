<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Purchaseitem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Alert;

class PurchaseController extends Controller
{
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $users = User::all();

        return view('purchases.create', compact('products', 'suppliers', 'users'));
    }

    public function store(Request $request)
    {
        $year = str_replace("20", "", date('Y'));
        $month = date('m');
        $purchase = new Purchase();
        $supplier = firstOrCreate(['name' => $request->get('supplier_id')]);
        $purchase->invoice_code = $request->get('invoice_code');
        $purchase->invoice_no = '#' . $year . $month . $request->get('invoice_code');
        $purchase->user_id = $request->get('user_id');
        $purchase->supplier_id = $supplier->id;
        $purchase->date = $request->get('date');
        $purchase->custom_name = $supplier->name;
        $purchase->custom_address = $request->get('address');
        // $purchase->is_Purchaseprice = $request->get('is_Purchaseprice');
        $purchase->discount = $request->get('discount');
        $purchase->remarks = $request->get('remarks');
        $purchase->sub_total = $request->get('sub_total');
        $purchase->grand_total = $request->get('grand_total');
        $purchase->save();

        if ($request->has('products')) {
            $items = $request->get('products');
            foreach ($items as $item) {
                $purchaseitem = new Purchaseitem();
                $purchaseitem->product_id = $item['product_id'];
                $purchaseitem->purchase_id = $purchase->id;
                $purchaseitem->quantity = $item['qty'];
                $purchaseitem->save();
                $product = Product::findOrFail($purchaseitem->product_id);
                $product->quantity = $product->quantity + $purchaseitem->quantity;
                $product->update();
            }
        }
        Alert::success('Purchase Invoice has been created successfully!');

        return redirect()->route('platform.purchase.view', $purchase->id);
    }

    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        $items_count = count($purchase->purchaseitems);
        $products = Product::all();
        $suppliers = Supplier::all();
        $users = User::all();

        return view('purchases.edit', compact('products', 'suppliers', 'users', 'purchase', 'items_count'));
    }

    public function update(Request $request)
    {
        $purchase = Purchase::findOrFail($request->get('purchase_id'));
        $purchase->invoice_code = $request->get('invoice_code');
        // $purchase->invoice_no = '#' . $year . $month . $request->get('invoice_code');
        $supplier = firstOrCreate(['name' => $request->get('supplier_id')]);
        $purchase->user_id = $request->get('user_id');
        $purchase->supplier_id = $supplier->id;
        $purchase->date = $request->get('date');
        $purchase->custom_name = $supplier->name;
        $purchase->custom_address = $request->get('address');
        // $purchase->is_Purchaseprice = $request->get('is_purchaseprice');
        $purchase->discount = $request->get('discount');
        $purchase->remarks = $request->get('remarks');
        $purchase->sub_total = $request->get('sub_total');
        $purchase->grand_total = $request->get('grand_total');
        $purchase->save();

        if ($request->has('products')) {
            $items = $request->get('products');
            foreach ($items as $item) {
                $purchaseitem = new Purchaseitem();
                $purchaseitem->product_id = $item['product_id'];
                $purchaseitem->purchase_id = $purchase->id;
                $purchaseitem->quantity = $item['qty'];
                $purchaseitem->save();
                $product = Product::findOrFail($purchaseitem->product_id);
                $product->quantity = $product->quantity - $purchaseitem->quantity;
                $product->update();
            }
        }
        Alert::success('Purchase Invoice has been updated successfully!');

        return redirect()->route('platform.purchase.view', $purchase->id);
    }
}
