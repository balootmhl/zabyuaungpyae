@extends('platform::dashboard')

@section('title')
	@if($purchase->is_inv_auto == 1)
		Purchase Invoice (Auto)
	@else
		Purchase Invoice (Manual)
	@endif
@stop

@section('description', '')

@section('navbar')
    <div class="text-center">
        <a href="{{ route('platform.purchase.view', $purchase->id) }}" class="btn btn-primary" >View</a>
    </div>
@stop

@push('head')
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

	<style>
		.select2 {
			max-width: 100% !important;
		}
	</style>
@endpush

@section('content')

<div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
	<form action="{{ route('platform.purchase.update-custom') }}" method="POST">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<input type="hidden" id="purchase_id" name="purchase_id" value="{{ $purchase->id }}">
		<div class="row justify-content-center invoice-form">
			<div class="col-sm-2">
				<div class="form-group">
					<label for="invoice_code">Invoice Code</label>
					<input type="text" name="invoice_code" class="form-control" value="{{ $purchase->invoice_code }}">
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<label for="is_inv_auto">Inv system</label>
					<select class="form-control" name="is_inv_auto" id="is_inv_auto" required>
						<option value="1" @if($purchase->is_inv_auto == 1) selected @endif>Auto</option>
						<option value="0" @if($purchase->is_inv_auto == 0) selected @endif>Manual</option>
					</select>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="form-group">
					<label for="user_id">Admin or Branch</label>
					<select class="form-control user-select2" name="user_id" multiple required>
						@foreach ($users as $user)
							<option value="{{ $user->id }}" @if($user->id == $purchase->user_id) selected="true" @endif>{{ $user->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="date">Date</label>
					<input type="date" name="date" class="form-control" value="{{ $purchase->date }}" required>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="form-group">
					<label for="supplier_id">Supplier</label>
					<select class="form-control supplier-select2" name="supplier_id" required multiple >
						@foreach ($suppliers as $supplier)
							<option value="{{ $supplier->name }}" @if($supplier->id == $purchase->supplier_id) selected="true" @endif>{{ $supplier->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-7">
				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" name="address" class="form-control" value="{{ $purchase->custom_address }}">
				</div>
			</div>

		</div>

		{{-- <div class="row justify-content-center invoice-form">
			<div class="col-sm-12" style="">
				<div class="table-responsive">
					<table class="table table-responsive">
						<thead>
							<tr>
								<th>Products</th>
								<th>Price</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="60%">
									<select name="product" id="product" class="product-select2 form-control" multiple>
										@foreach($products as $product)
										    <option id="{{ $product->id }}" value="{{ $product->id }}">{{ $product->code . ' [' . $product->name . '] ' }}</option>
										@endforeach
									</select>
								</td>
								<td width="15%">
									<input type="number" id="price" name="price" min="0" value="0" class="form-control">
								</td>
								<td width="15%">
									<input type="number" id="qty" name="qty" min="0" value="0" class="form-control">
								</td>
								<td width="10%">
									<div class="toolbar">
										<button type="submit" class="btn btn-primary ">Save</button>
									</div>
								</td>
							</tr>
						</tbody>
						<tfoot></tfoot>
					</table>
				</div>
			</div>
		</div> --}}

		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<div class="table-responsive">
					<table class="table table-responsive">
						<thead>
							<tr>
								<th>Products</th>
								<th>Price</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="60%">
									<div class="form-group">
										{{-- <label for="product">Select Product</label> --}}
										<select name="product" id="product" class="product-select2 form-control" multiple>
											@foreach($products as $product)
											    <option id="{{ $product->id }}" value="{{ $product->id }}">{{ $product->code . ' [' . $product->name . '] ' }}</option>
											@endforeach
										</select>
									</div>
								</td>
								<td width="15%">
									{{-- <label for="">Price</label> --}}
									<input type="number" id="price" name="price" min="0" value="0" class="form-control">
									{{-- <h6 class="mt-1" id="price_text" >0</h6> --}}
								</td>
								<td>
									<div class="form-group">
										{{-- <label for="qty">Quantity</label> --}}
										<input type="number" id="qty" name="qty" min="0" value="0" class="form-control">
									</div>
								</td>
								<td>
									<div class="form-group">
										<button type="submit" class="btn btn-primary">Save</button>
									</div>
								</td>
							</tr>
						</tbody>
						<tfoot></tfoot>
					</table>
				</div>
			</div>
		</div>

		<div class="row invoice-form">
			<div class="col-sm-3">
				<div class="form-group">
					<label for="discount">Discount</label>
					<input type="number" id="discount" name="discount" class="form-control" min="0" value="{{ $purchase->discount }}">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="received">Received</label>
					<input type="number" id="received" name="received" class="form-control" min="0" value="{{ $purchase->received }}">
				</div>
			</div>
			{{-- <div class="col-sm-6">
				<div class="form-group">
					<label for="remarks">Remarks</label>
					<input type="text" name="remarks" class="form-control" value="{{ $purchase->remarks }}">
				</div>
			</div> --}}
		</div>

		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<div class="table-responsive">
					<table id="receipt_bill" class="table table-responsive">
						<thead>
							<tr>
								<th>No.</th>
								<th>Product</th>
								<th class="text-center">Price</th>
								<th class="text-center">Quantity</th>
								<th class="text-center">Unit Total</th>
							</tr>
						</thead>
						<tbody id="new">
							@foreach($purchase->purchaseitems as $item)
								<tr>
									<td>{{ $loop->iteration }}</td>
									<td>
										@if($item->code != NULL)
	                                        {{ $item->code }}
	                                    @else
	                                        ??????
	                                    @endif
	                                    @if($item->name != NULL)
	                                        _[{{ $item->name }}]
	                                    @else
	                                        _[??????]
	                                    @endif
									</td>
	                       			<td class="text-center">
	                       				@if($item->price != NULL)
	                                        {{ $item->price }}
	                                    @else
	                                        ?????
	                                    @endif
	                       			</td>
									<td class="text-center">{{ $item->quantity }}</td>
			                        <td class="text-center">
			                        	<strong>
				                        	@if($item->price != NULL)
					                        	{{ $item->price * $item->quantity }}
					                        @else
					                        	?????
					                        @endif
				                        	&nbsp;
				                        	<a href="{{ url('/admin/purchases/purchaseitems/delete/'. $item->id) }}" class="delete-btn" onclick="return confirm('Are you sure?')">
												<x-orchid-icon path="trash" style="padding-bottom: 5px !important;" />
											</a>
				                        </strong>

			                        </td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td> </td>
								<td> </td>
								<td> </td>
								<td class="text-right text-dark">
									<h5><strong>Subtotal: MMK </strong></h5>
									<p><strong>Discount : MMK </strong></p>
								</td>
								<td class="text-center text-dark" >
	                        <h5> <strong><span id="subTotal">{{ $purchase->sub_total }}</span></strong></h5>
	                        <input type="hidden" id="sub_total" name="sub_total" value="{{ $purchase->sub_total }}">
	                        <h5> <strong><span id="taxAmount">{{ $purchase->discount }}</strong></h5>
	                     </td>
							</tr>
							<tr>
	                           <td> </td>
	                           <td> </td>
	                           <td> </td>
	                           <td class="text-right text-dark">
	                              <h5><strong>Grand Total: MMK </strong></h5>
	                              @if ($purchase->received != 0)
		                              <p><strong>Received : MMK </strong></p>
		                              <h5><strong>Remain to pay: MMK </strong></h5>
	                              @endif
	                           </td>
	                           <td class="text-center">
	                              <h5 id="totalPayment" class="text-danger"><strong>{{ $purchase->grand_total }}</strong></h5>
	                              <input type="hidden" id="grand_total" name="grand_total" value="{{ $purchase->grand_total }}">
	                              @if ($purchase->received != 0)
		                              <h5><strong><span id="taxAmount">{{ $purchase->received }}</span></strong></h5>
		                              <h5 id="totalPayment" class="text-danger"><strong>{{ $purchase->remained }}</strong></h5>
		                           @endif
	                           </td>
	                        </tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<div class="toolbar">
					<input type="submit" class="btn btn-success" value="Save">
					<a href="{{ route('platform.purchase.view', $purchase->id) }}" class="btn btn-success" >View</a>
				</div>

			</div>
		</div>
	</form>
</div>

@stop

@push('scripts')
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
	<script type="text/javascript">
		// activate select2 plugin
		$(document).ready(function() {
		    $('.user-select2').select2({
		    	placeholder: 'Select User',
            theme: "bootstrap"
		    });
		});
		$(document).ready(function() {
		    $('.supplier-select2').select2({
		    	placeholder: 'Enter to select or create',
		    	tags: true,
            theme: "bootstrap"
		    });
		});
		$(document).ready(function() {
		    $('.product-select2').select2({
		    	placeholder: 'Select Product',
		    	theme: "bootstrap"
		    });
		});
	</script>
@endpush
