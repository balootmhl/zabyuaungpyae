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
				</tr>
			</thead>
			<tbody>
				@foreach($products as $product)
				    <tr>
				    	<td>{{ $loop->iteration }}</td>
				    	<td>{{ $product->code }}<input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}"></td>
				    	<td>{{ $product->name }}</td>
				    	<td><input type="number" name="products[{{ $loop->index }}][buy_price]" value="{{ $product->buy_price }}"></td>
				    	<td><input type="number" name="products[{{ $loop->index }}][sale_price]" value="{{ $product->sale_price }}"></td>
				    </tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
