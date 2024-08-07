<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Saleitem;
use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Support\Facades\Toast;

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
        $sale = $this->createSale($request);

        if ($this->isProductValid($request)) {
            $this->createSaleItem($request, $sale);
        }

        $this->updateSaleTotals($sale);

        if ($request->get('received') != 0) {
            $this->updateCustomerDebt($sale);
        }

        Toast::success('Invoice Saved.');
        return redirect()->route('platform.sale.edit-custom', $sale->id);
    }

    public function update(Request $request) {
        $sale = Sale::findOrFail($request->get('sale_id'));

        $this->updateSale($sale, $request);

        if ($this->isProductValid($request)) {
            $this->createSaleItem($request, $sale);
        }

        $this->updateSaleTotals($sale);
        $this->updateCustomerDebt($sale);

        Toast::success('Invoice Saved.');
        return redirect()->route('platform.sale.edit-custom', $sale->id);
    }

    private function createSale(Request $request) {
        $yearMonth = str_replace("20", "", date('Y')) . date('m');
        $customer = Customer::firstOrCreate(['name' => $request->get('customer_id')]);

        $sale = new Sale([
            'invoice_code' => $request->get('invoice_code'),
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch->id,
            'customer_id' => $customer->id,
            'date' => $request->get('date'),
            'custom_name' => $customer->name,
            'custom_address' => $request->get('address'),
            'is_saleprice' => $request->get('is_saleprice'),
            'is_inv_auto' => $request->get('is_inv_auto'),
            'discount' => $request->get('discount'),
            'remarks' => $request->get('remarks'),
            'received' => $request->get('received'),
        ]);

        if ($request->get('is_inv_auto') == 0) {
            $sale->invoice_no = "#{$yearMonth}{$request->get('invoice_code')}";
        }

        $sale->save();

        if ($request->get('is_inv_auto') == 1) {
            $sale->invoice_no = '#01' . str_replace("-", "", $sale->date) . $sale->id;
            $sale->save();
        }

        return $sale;
    }

    private function updateSale(Sale $sale, Request $request) {
        $customer = Customer::firstOrCreate(['name' => $request->get('customer_id')]);

        $sale->update([
            'invoice_code' => $request->get('invoice_code'),
            'user_id' => auth()->id(),
            'branch_id' => auth()->user()->branch->id,
            'customer_id' => $customer->id,
            'date' => $request->get('date'),
            'custom_name' => $customer->name,
            'custom_address' => $request->get('address'),
            'is_saleprice' => $request->get('is_saleprice'),
            'is_inv_auto' => $request->get('is_inv_auto'),
            'discount' => $request->get('discount'),
            'remarks' => $request->get('remarks'),
            'received' => $request->get('received'),
        ]);
    }

    private function isProductValid(Request $request) {
        return $request->filled('product') && $request->get('price') != 0 && $request->get('qty') != 0;
    }

    private function createSaleItem(Request $request, Sale $sale) {
        $product = Product::findOrFail($request->get('product'));

        $saleItem = new Saleitem([
            'product_id' => $product->id,
            'sale_id' => $sale->id,
            'code' => $product->code,
            'name' => $product->name,
            'quantity' => $request->get('qty'),
            'price' => $request->get('price'),
        ]);

        $saleItem->save();

        $product->decrement('quantity', $saleItem->quantity);
    }

    private function updateSaleTotals(Sale $sale) {
        $subtotal = $sale->saleitems->sum(fn($item) => $item->price * $item->quantity);

        $sale->update([
            'sub_total' => $subtotal,
            'grand_total' => $subtotal - $sale->discount,
            'remained' => $sale->grand_total - $sale->received,
        ]);
    }

    private function updateCustomerDebt(Sale $sale) {
        $customer = Customer::findOrFail($sale->customer_id);
        $customer->debt += $sale->remained;
        $customer->update();
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
