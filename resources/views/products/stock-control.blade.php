@extends('platform::dashboard')

@section('title', 'Stock Control')
@section('description', 'Edit Product Prices')

@section('navbar')
    {{-- <div class="text-center">
        Navbar
    </div> --}}
@stop

@section('content')
    {{-- <div class="text-center mt-5 mb-5">
        <h4>Edit Product Prices</h4>
    </div> --}}
    <div class="bg-white rounded shadow-sm mb-3" style="padding: 30px 40px;">
        <form action="{{ route('platform.product.stock.save') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-sm-5">
                    <label for="products[]">Select Product (Total: {{$products->count()}})</label>
                    <select class="hyper-select form-control" name="products[]" multiple required>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">
                                [{{ $product->id }}][{{ $product->code }}][{{ $product->name }}]
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="buy_price">Buy Price</label>
                    <input type="number" class="form-control" name="buy_price" value="0">
                </div>
                <div class="col-sm-3">
                    <label for="sale_price">Sale Price</label>
                    <input type="number" class="form-control" name="sale_price" value="0">
                </div>
                <div class="col-sm-1 text-center">
                    <label for="">&nbsp;</label>
                    <input type="submit" class="btn btn-small btn-primary" value="Save">
                </div>
            </div>
        </form>
        {{-- <p style="padding: 10px 20px 0px 20px !important;"><strong>Edit Stock Prices</strong></p> --}}
        {{-- <div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="color: #667780 !important;">No.</th>
						<th style="color: #667780 !important;">Code</th>
						<th style="color: #667780 !important;">Name</th>
						<th style="color: #667780 !important;">Buy Price</th>
						<th style="color: #667780 !important;">Sale Price</th>
						<th style="color: #667780 !important;">Action</th>
						<td>{{ $loop->iteration }}</td>
				    	<td>{{ $product->code }}<input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}"></td>
				    	<td>{{ $product->name }}</td>
				    	<td><input type="number" name="products[{{ $loop->index }}][buy_price]" value="{{ $product->buy_price }}"></td>
				    	<td><input type="number" name="products[{{ $loop->index }}][sale_price]" value="{{ $product->sale_price }}"></td>
					</tr>
				</thead>
				<tbody>
					@foreach ($products as $product)
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
		</div> --}}
    </div>
@stop

@push('scripts')
    <script type="text/javascript">
        // activate select2 plugin
        $(document).ready(function() {
            $('.hyper-select').select2();
        });
    </script>
@endpush
