<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Saleitem;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;

class SaleController extends Controller {
	public function index() {
		$sales = Sale::orderby('created_at', 'DESC')->get();

		return view('sales.index', compact('sales'));
	}

    public function search(Request $request) {
        $key = $request->searchkey;
        $sales_query = Sale::query();
        if($request->has('searchkey') && $request->type == 'product'){
            $sales = $sales_query->whereHas('saleitems', function ($query) use ($key) {
                $query->where('code', 'like', '%'.$key.'%');
            });
        }

        return view('sales.search-results', compact('sales'));
    }

	public function create() {
		$user = auth()->user();
        if($user->hasAccess('platform.module.sale')){
            $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();
            $customers = Customer::all();
            $users = User::all();
            return view('sales.create', compact('products', 'customers', 'users'));
        } else {
            abort(403);
        }

	}

    public function edit($id) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.sale')){
            $sale = Sale::findOrFail($id);
            // Check if today's date is more than 3 days after the sale's created_at date
			if (Carbon::now()->diffInDays($sale->created_at) > 3) {
				// Abort with a 403 forbidden status
				abort(403, 'You cannot edit this sale because it was created more than 3 days ago.');
			}
            $items_count = count($sale->saleitems);
            $products = Product::where('branch_id', auth()->user()->branch->id)->orderby('created_at', 'DESC')->get();
            $customers = Customer::all();
            $users = User::all();

            return view('sales.edit-new', compact('products', 'customers', 'users', 'sale', 'items_count'));
        } else {
            abort(403);
        }

	}

	public function store(Request $request) {
		$year = str_replace("20", "", date('Y'));
		$month = date('m');
		$sale = new Sale();
		$customer = Customer::firstOrCreate(['name' => $request->get('customer_id')]);
		$sale->invoice_code = $request->get('invoice_code');
		if ($request->get('is_inv_auto') == 0) {
			$sale->invoice_no = '#' . $year . $month . $request->get('invoice_code');
		}
		$sale->user_id = auth()->user()->id;
		$sale->branch_id = auth()->user()->branch->id;
		$sale->customer_id = $customer->id;
		$sale->date = $request->get('date');
		$sale->custom_name = $customer->name;
		$sale->custom_address = $request->get('address');
		$sale->is_saleprice = $request->get('is_saleprice');
		$sale->is_inv_auto = $request->get('is_inv_auto');
		$sale->discount = $request->get('discount');
		$sale->remarks = $request->get('remarks');
		$sale->received = $request->get('received');
		$sale->save();
		if ($request->get('is_inv_auto') == 1) {
			$sale->invoice_no = '#01' . str_replace("-", "", $sale->date) . $sale->id;
			$sale->update();
		}
		if ($request->get('product') != null && $request->get('price') != 0 && $request->get('qty') != 0) {
			$product = Product::findOrFail($request->get('product'));
			$saleitem = new Saleitem();
			$saleitem->product_id = $request->get('product');
			$saleitem->sale_id = $sale->id;
			$saleitem->code = $product->code;
			$saleitem->name = $product->name;
			$saleitem->quantity = $request->get('qty');
			$saleitem->price = $request->get('price');
			$saleitem->save();
			$product->quantity = $product->quantity - $saleitem->quantity;
			$product->update();
		}
		$subtotal = 0;
		foreach ($sale->saleitems as $sitem) {
			$item_total = $sitem->price * $sitem->quantity;
			$subtotal = $subtotal + $item_total;
		}
		$sale->sub_total = $subtotal;
		$sale->grand_total = $subtotal - $sale->discount;
		if ($sale->received != 0) {
			$sale->remained = $sale->grand_total - $sale->received;
		}
		$sale->update();
		if($request->get('received') != 0){
			$customer->debt = $customer->debt + $sale->remained;
			$customer->update();
		}
		$sale->update();
		Toast::success('Invoice Saved.');
		return redirect()->route('platform.sale.edit-custom', $sale->id);
	}

	public function update(Request $request) {
		$sale = Sale::findOrFail($request->get('sale_id'));
		$sale->invoice_code = $request->get('invoice_code');
		$cus = Customer::firstOrCreate(['name' => $request->get('customer_id')]);
		$sale->user_id = auth()->user()->id;
		$sale->branch_id = auth()->user()->branch->id;
		$sale->customer_id = $cus->id;
		$sale->date = $request->get('date');
		$sale->custom_name = $cus->name;
		$sale->custom_address = $request->get('address');
		$sale->is_saleprice = $request->get('is_saleprice');
		$sale->is_inv_auto = $request->get('is_inv_auto');
		$sale->discount = $request->get('discount');
		$sale->remarks = $request->get('remarks');


		$sale->received = $request->get('received');
		$sale->update();

		if ($request->get('product') != null && $request->get('price') != 0 && $request->get('qty') != 0) {
			$product = Product::findOrFail($request->get('product'));
			$saleitem = new Saleitem();
			$saleitem->product_id = $request->get('product');
			$saleitem->sale_id = $sale->id;
			$saleitem->code = $product->code;
			$saleitem->name = $product->name;
			$saleitem->quantity = $request->get('qty');
			$saleitem->price = $request->get('price');
			$saleitem->save();
			$product->quantity = $product->quantity - $saleitem->quantity;
			$product->update();
		}

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
		$sale->remained = $sale->grand_total - $request->get('received');
		$sale->update();
		$customer->debt = $customer->debt + $sale->remained;
		$customer->update();
		Toast::success('Invoice Saved.');

		return redirect()->route('platform.sale.edit-custom', $sale->id);
	}


	public function delete(Request $request) {
        $user = auth()->user();
        if($user->hasAccess('platform.module.sale')){
            $sale = Sale::findOrFail($request->get('id'));
            $saleitems = $sale->saleitems;
            if ($saleitems) {
                foreach ($saleitems as $saleitem) {
                    $product = Product::findOrFail($saleitem->product_id);
                    $product->quantity = $product->quantity + $saleitem->quantity;
                    $product->update();
                    $saleitem->delete();
                }
            }
            $sale->delete();

            Toast::info('Sale Invoice is deleted successfully. Product Quantity are returning back.');

            return redirect()->route('platform.sale.list');
        } else {
            abort(403);
        }

	}

}
