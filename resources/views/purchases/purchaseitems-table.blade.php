<div class="bg-white rounded shadow-sm mb-3" >
	<p style="padding: 10px 20px 0px 20px !important;"><strong>Manage Items</strong></p>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th style="color: #667780 !important;">Code</th>
					<th style="color: #667780 !important;">Name</th>
					<th style="color: #667780 !important;">Price</th>
					<th style="color: #667780 !important;">Quantity</th>
					<th style="color: #667780 !important;">Action</th>
				</tr>
			</thead>
			<tbody>
				@foreach($purchase->purchaseitems as $purchaseitem)
				    <tr>
				    	<td>{{ $purchaseitem->product->code }}<input type="hidden" name="olditems[{{ $loop->index }}][id]" value="{{ $purchaseitem->id }}"></td>
				    	<td>{{ $purchaseitem->product->name }}</td>
				    	<td>{{ $purchaseitem->product->sale_price }}</td>
				    	<td><input type="number" name="olditems[{{ $loop->index }}][qty]" value="{{ $purchaseitem->quantity }}"></td>
				    	<td>
				    		<a data-controller="button" data-turbo="true" data-action="button#confirm" data-button-confirm="Are you sure?" class="btn btn-link" href="{{ url('/admin/purchases/purchaseitems/delete/'. $purchaseitem->id) }}">
				    			{{-- http://maharshin.test/admin/products/remove?id={{ $saleitem->id }} --}}
								<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor" componentName="orchid-icon">
    								<path d="M28.025 4.97l-7.040 0v-2.727c0-1.266-1.032-2.265-2.298-2.265h-5.375c-1.267 0-2.297 0.999-2.297 2.265v2.727h-7.040c-0.552 0-1 0.448-1 1s0.448 1 1 1h1.375l2.32 23.122c0.097 1.082 1.019 1.931 2.098 1.931h12.462c1.079 0 2-0.849 2.096-1.921l2.322-23.133h1.375c0.552 0 1-0.448 1-1s-0.448-1-1-1zM13.015 2.243c0-0.163 0.133-0.297 0.297-0.297h5.374c0.164 0 0.298 0.133 0.298 0.297v2.727h-5.97zM22.337 29.913c-0.005 0.055-0.070 0.11-0.105 0.11h-12.463c-0.035 0-0.101-0.055-0.107-0.12l-2.301-22.933h17.279z"></path>
								</svg>
        						Delete
						    </a>
				    	</td>
				    </tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
