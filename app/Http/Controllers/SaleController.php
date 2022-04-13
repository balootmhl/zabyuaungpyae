<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Saleitem;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Alert;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::all();
        $customers = Customer::all();
        $users = User::all();

        return view('sales.create', compact('products', 'customers', 'users'));
    }

    public function store(Request $request)
    {
        $year = str_replace("20", "", date('Y'));
        $month = date('m');
        $sale = new Sale();
        $customer = firstOrCreate(['code' => '0000-0000', 'name' => $request->get('customer_id')]);
        $sale->invoice_code = $request->get('invoice_code');
        $sale->invoice_no = '#' . $year . $month . $request->get('invoice_code');
        $sale->user_id = $request->get('user_id');
        $sale->customer_id = $customer->id;
        $sale->date = $request->get('date');
        $sale->custom_name = $customer->name;
        $sale->custom_address = $request->get('address');
        $sale->is_saleprice = $request->get('is_saleprice');
        $sale->discount = $request->get('discount');
        $sale->remarks = $request->get('remarks');
        $sale->sub_total = $request->get('sub_total');
        $sale->grand_total = $request->get('grand_total');
        $sale->save();

        if ($request->has('products')) {
            $items = $request->get('products');
            foreach ($items as $item) {
                $saleitem = new Saleitem();
                $saleitem->product_id = $item['product_id'];
                $saleitem->sale_id = $sale->id;
                $saleitem->quantity = $item['qty'];
                $saleitem->save();
                $product = Product::findOrFail($saleitem->product_id);
                $product->quantity = $product->quantity - $saleitem->quantity;
                $product->update();
            }
        }
        Alert::success('Sale Invoice has been created successfully!');

        return redirect()->route('platform.sale.view', $sale->id);
    }

    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        $items_count = count($sale->saleitems);
        $products = Product::all();
        $customers = Customer::all();
        $users = User::all();

        return view('sales.edit', compact('products', 'customers', 'users', 'sale', 'items_count'));
    }

    public function update(Request $request)
    {
        $sale = Sale::findOrFail($request->get('sale_id'));
        // $sale->invoice_code = $request->get('invoice_code');
        // $sale->invoice_no = '#' . $year . $month . $request->get('invoice_code');
        $sale->user_id = $request->get('user_id');
        $sale->customer_id = $request->get('customer_id');
        $sale->date = $request->get('date');
        $sale->custom_name = $request->get('customer_name');
        $sale->custom_address = $request->get('address');
        $sale->is_saleprice = $request->get('is_saleprice');
        $sale->discount = $request->get('discount');
        $sale->remarks = $request->get('remarks');
        $sale->sub_total = $request->get('sub_total');
        $sale->grand_total = $request->get('grand_total');
        $sale->save();

        if ($request->has('products')) {
            $items = $request->get('products');
            foreach ($items as $item) {
                $saleitem = new Saleitem();
                $saleitem->product_id = $item['product_id'];
                $saleitem->sale_id = $sale->id;
                $saleitem->quantity = $item['qty'];
                $saleitem->save();
                $product = Product::findOrFail($saleitem->product_id);
                $product->quantity = $product->quantity - $saleitem->quantity;
                $product->update();
            }
        }
        Alert::success('Sale Invoice has been updated successfully!');

        return redirect()->route('platform.sale.view', $sale->id);
    }

}
