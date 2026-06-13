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
use Illuminate\Support\Facades\DB;

class SaleController extends Controller {
	public function index() {
		$sales = Sale::orderby('created_at', 'DESC')->get();

		return view('sales.index', compact('sales'));
	}


	public function create() {
		/** @var \App\Models\User $user */
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
        /** @var \App\Models\User $user */
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
		try {
			$sale = DB::transaction(function () use ($request) {
				$year = str_replace("20", "", date('Y'));
				$month = date('m');
				$customer = Customer::firstOrCreate(['name' => $request->get('customer_id')]);
				
				$sale = new Sale();
				$sale->invoice_code = $request->get('invoice_code');
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
				$sale->sub_total = 0;
				$sale->grand_total = 0;
				$sale->remained = 0;

				if ($request->get('is_inv_auto') == 0) {
					$sale->invoice_no = '#' . $year . $month . $request->get('invoice_code');
				}
				
				$sale->save();

				if ($request->get('is_inv_auto') == 1) {
					$sale->invoice_no = '#01' . str_replace("-", "", $sale->date) . $sale->id;
				}

				$subtotal = 0;
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
					$subtotal += $saleitem->price * $saleitem->quantity;
				}

				$sale->sub_total = $subtotal;
				$sale->grand_total = $subtotal - $sale->discount;
				if ($sale->received != 0) {
					$sale->remained = $sale->grand_total - $sale->received;
				} else {
					$sale->remained = $sale->grand_total;
				}
				$sale->update();

				if ($request->get('received') != 0) {
					$customer->debt = $customer->debt + $sale->remained;
					$customer->update();
				}

				return $sale;
			});

			Toast::success('Invoice Saved.');
			return redirect()->route('platform.sale.edit-custom', $sale->id);

		} catch (\Exception $e) {
			Toast::error('Failed to save invoice: ' . $e->getMessage());
			return redirect()->back()->withInput();
		}
	}

	public function update(Request $request) {
		try {
			$sale = DB::transaction(function () use ($request) {
				$sale = Sale::findOrFail($request->get('sale_id'));
				
				$oldCustomer = Customer::findOrFail($sale->customer_id);
				$oldCustomer->debt = $oldCustomer->debt - $sale->remained;
				$oldCustomer->update();

				$cus = Customer::firstOrCreate(['name' => $request->get('customer_id')]);
				$sale->invoice_code = $request->get('invoice_code');
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

				$sale->unsetRelation('saleitems');
				$subtotal = 0;
				foreach ($sale->saleitems as $sitem) {
					$subtotal += $sitem->price * $sitem->quantity;
				}

				$sale->sub_total = $subtotal;
				$sale->grand_total = $subtotal - $sale->discount;
				$sale->remained = $sale->grand_total - $sale->received;
				$sale->update();

				$newCustomer = Customer::findOrFail($sale->customer_id);
				$newCustomer->debt = $newCustomer->debt + $sale->remained;
				$newCustomer->update();

				return $sale;
			});

			Toast::success('Invoice Saved.');
			return redirect()->route('platform.sale.edit-custom', $sale->id);

		} catch (\Exception $e) {
			Toast::error('Failed to update invoice: ' . $e->getMessage());
			return redirect()->back()->withInput();
		}
	}

	public function delete(Request $request) {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        if ($user->hasAccess('platform.module.sale')) {
			try {
				DB::transaction(function () use ($request) {
					$sale = Sale::findOrFail($request->get('id'));
					
					$customer = Customer::findOrFail($sale->customer_id);
					$customer->debt = $customer->debt - $sale->remained;
					$customer->update();

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
				});

				Toast::info('Sale Invoice is deleted successfully. Product Quantity are returning back.');
				return redirect()->route('platform.sale.list');

			} catch (\Exception $e) {
				Toast::error('Failed to delete invoice: ' . $e->getMessage());
				return redirect()->back();
			}
        } else {
            abort(403);
        }
	}

}
