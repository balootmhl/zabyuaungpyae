@extends('platform::dashboard')

@section('title','Stock Control')
@section('description', 'Stock Control')

@section('navbar')
    {{-- <div class="text-center">
        Navbar
    </div> --}}
@stop

@section('content')
    {{-- <div class="text-center mt-5 mb-5">
        <h1>Edit Product Prices</h1>
    </div> --}}
    <div class="bg-white rounded shadow-sm mb-3" >
		<p style="padding: 10px 20px 0px 20px !important;"><strong>Edit Stock Prices</strong></p>
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="color: #667780 !important;">No.</th>
						<th style="color: #667780 !important;">Code</th>
						<th style="color: #667780 !important;">Name</th>
						<th style="color: #667780 !important;">Buy Price</th>
						<th style="color: #667780 !important;">Sale Price</th>
						<th style="color: #667780 !important;">Action</th>
						{{-- <td>{{ $loop->iteration }}</td>
				    	<td>{{ $product->code }}<input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}"></td>
				    	<td>{{ $product->name }}</td>
				    	<td><input type="number" name="products[{{ $loop->index }}][buy_price]" value="{{ $product->buy_price }}"></td>
				    	<td><input type="number" name="products[{{ $loop->index }}][sale_price]" value="{{ $product->sale_price }}"></td> --}}
					</tr>
				</thead>
				<tbody>
					@foreach($products as $product)
					    <tr>
					    	<form action="{{ route('platform.product.stock.save') }}" method="POST">
					    		<input type="hidden" name="_token" value="{{ csrf_token() }}">
						    	<td>{{ $loop->iteration }}</td>
						    	<td>{{ $product->code }}<input type="hidden" name="product_id" value="{{ $product->id }}"></td>
						    	<td>{{ $product->name }}</td>
						    	<td><input type="number" name="buy_price" value="{{ $product->buy_price }}"></td>
						    	<td><input type="number" name="sale_price" value="{{ $product->sale_price }}"></td>
						    	<td class="text-center"><input type="submit" class="btn btn-primary btn-small" value="Save"></td>
						    </form>
					    </tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@stop



	
