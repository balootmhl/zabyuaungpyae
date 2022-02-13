@if(session('invoices'))

<div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-7 mt-6 p-4 pe-md-0">

            <h2 class="mt-2 text-dark fw-light">
                Total Sold Amount - <strong>{{ session('total_income') }} MMK</strong>
            </h2>

            <p>
                This is total sold amount of all sale invioces on that day.<br>
                {{-- You are minutes away from creativity than ever before. Enjoy! --}}
            </p>
        </div>
        <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
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
                {{-- You are minutes away from creativity than ever before. Enjoy! --}}
            </p>
        </div>
        <div class="d-none d-lg-block col align-self-center text-end text-muted p-4">
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
            <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" >
        </div>
    </div>
</div>

<div class="bg-white rounded shadow-sm mb-3" >
    <small class="text-dark d-block mb-1" style="padding: 10px 15px;"><strong>Sale invoices on that day</strong></small>
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
				@foreach(session('invoices') as $invoice)
				    <tr>
				    	<td>{{ $invoice->invoice_no }}</td>
				    	<td>{{ $invoice->customer->name }}</td>
				    	<td>{{ $invoice->user->name }}</td>
				    	<td>{{ $invoice->discount }} MMK</td>
				    	<td>{{ $invoice->grand_total }} MMK</td>
				    </tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@endif
