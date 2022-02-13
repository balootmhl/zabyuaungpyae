@if(session('items'))

{{-- <div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-7 mt-6 p-4 pe-md-0">

            <h2 class="mt-2 text-dark fw-light">
                Total Sold Amount - <strong>{{ session('total_income') }} MMK</strong>
            </h2>

            <p>
                This is total sold amount of all sale invioces on that day.<br>
            </p>
        </div>
        <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
            <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" >
        </div>
    </div>
</div>
<div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-7 mt-6 p-4 pe-md-0">

            <h2 class="mt-2 text-dark fw-light">
                Total Discount Amount - <strong>{{ session('total_discount') }} MMK</strong>
            </h2>

            <p>
                This is total sold amount of all sale invioces on that day.<br>
            </p>
        </div>
        <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
            <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" >
        </div>
    </div>
</div> --}}

    <div class="bg-white rounded shadow-sm mb-3" >
        <small class="text-dark d-block mb-1" style="padding: 10px 15px;"><strong>Search Results</strong></small>
    	<div class="table-responsive">
    		<table class="table table-bordered">
    			<thead>
    				<tr>
    					<th style="color: #667780 !important;">Invoice No.</th>
    					<th style="color: #667780 !important;">Customer</th>
    					<th style="color: #667780 !important;">Invoice By</th>
    					<th style="color: #667780 !important;">Discount</th>
    					<th style="color: #667780 !important;">Grand Total</th>
    				</tr>
    			</thead>
    			<tbody>
    				@foreach(session('items') as $item)
                      @if($item->sale->date == session('search_date'))
    				    <tr>
    				    	<td>{{ $item->sale->invoice_no }}</td>
    				    	<td>{{ $item->sale->customer->name }}</td>
    				    	<td>{{ $item->sale->user->name }}</td>
    				    	<td>{{ $item->sale->discount }} MMK</td>
    				    	<td>{{ $item->sale->grand_total }} MMK</td>
    				    </tr>
                      @endif
    				@endforeach
    			</tbody>
    		</table>
    	</div>
    </div>
@endif
