@if(session('invoices'))

<div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-12 mt-6 p-4 pe-md-0">

            <h2 class="mt-2 text-dark fw-light">
                Total Sold Amount - <strong>{{ number_format(session('total_income')) }} MMK</strong>
            </h2>

            <p>
                This is total sold amount of all sale invioces on that day.<br>
                {{-- You are minutes away from creativity than ever before. Enjoy! --}}
            </p>
        </div>
        {{-- <div class="d-none d-lg-block col align-self-center text-end text-muted p-4"> --}}
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
            {{-- <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" > --}}
        {{-- </div> --}}
    </div>
</div>
<div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-12 mt-6 p-4 pe-md-0">

            <h2 class="mt-2 text-dark fw-light">
                Total Discount Amount - <strong>{{ number_format(session('total_discount')) }} MMK</strong>
            </h2>

            <p>
                This is total sold amount of all sale invioces on that day.<br>
                {{-- You are minutes away from creativity than ever before. Enjoy! --}}
            </p>
        </div>
        {{-- <div class="d-none d-lg-block col align-self-center text-end text-muted p-4"> --}}
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
            {{-- <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" > --}}
        {{-- </div> --}}
    </div>
</div>

@if(session('customer'))
<div class="rounded bg-white mb-3 p-3">
    <div class="row g-0">
        <div class="col col-lg-12 mt-6 p-4 pe-md-0">

            <h3 class="mt-2 text-dark fw-light">
                Debt Amount of {{session('customer')->name}} - <strong><text class="text-danger">{{ number_format(session('customer')->debt) }} MMK</text></strong>
            </h3>

            <p>
                This is total debt amount of selected customer.<br>
                {{-- You are minutes away from creativity than ever before. Enjoy! --}}
            </p>
        </div>
        {{-- <div class="d-none d-lg-block col align-self-center text-end text-muted p-4"> --}}
            {{-- <x-orchid-icon path="orchid" width="6em" height="100%"/> --}}
            {{-- <img src="{{ asset('custom/img/logo.png') }}" width="auto" height="80" alt="" > --}}
        {{-- </div> --}}
    </div>
</div>
@endif

<div class="bg-white rounded shadow-sm mb-3" >
    <p class="text-dark d-block mb-1" style="padding: 10px 15px;"><strong>Sale invoices</strong></p>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th style="color: #667780 !important;">Invoice No.</th>
					<th style="color: #667780 !important;">Customer</th>
					<th style="color: #667780 !important;">Invoice By</th>
					<th style="color: #667780 !important;">Discount</th>
					<th style="color: #667780 !important;">Grand Total</th>
                    <th style="color: #667780 !important;">Remain to pay</th>
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
                        <td @if($invoice->remained != 0) class="text-danger" @else class="text-success" @endif><strong>{{ $invoice->remained }} MMK</strong></td>
				    </tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
<div class="bg-white rounded shadow-sm mb-3" >
    <p class="text-dark d-block mb-1" style="padding: 10px 15px;"><strong>Product sales</strong></p>
	<div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th style="color: #667780 !important;">Product</th>
					<th style="color: #667780 !important;">Invoices</th>
					{{-- <th style="color: #667780 !important;">Buy Price</th> --}}
					<th style="color: #667780 !important;">Buy Price Total</th>
					{{-- <th style="color: #667780 !important;">Sale Price</th> --}}
					<th style="color: #667780 !important;">Sale Price Total</th>
					<th style="color: #667780 !important;">Difference</th>
				</tr>
			</thead>
			<tbody>
                @foreach(session('invoices') as $invoice)
                    @foreach($invoice->saleitems as $saleitem)
                        <tr>
                            <td style="width: 25%;">
                                [{{ $saleitem->product->id }}][{{ $saleitem->product->code }}][{{ $saleitem->product->name }}]</td>
                            <td style="width: 25%;">
                                {{ $saleitem->sale->invoice_no }}(Qty: {{ $saleitem->quantity}})
                                <br>
                                {{ $saleitem->sale->customer->name }}
                            </td>
                            <!-- <td>{{ number_format($saleitem->product->buy_price) }}</td> -->
                            @php
                                $buy_total = $saleitem->product->buy_price * $saleitem->quantity;
                                $sale_total = $saleitem->product->sale_price * $saleitem->quantity;
                                $difference = $sale_total - $buy_total;
                            @endphp
                            <td>{{ number_format($buy_total) }}</td>
                            <!-- <td>{{ number_format($saleitem->product->sale_price) }}</td> -->
                            <td>{{ number_format($sale_total) }}</td>
                            <td class="@if($difference>= 0) text-success @else text-danger @endif">{{ number_format($difference) }}</td>
                            {{-- <td @if($invoice->remained != 0) class="text-danger" @else class="text-success" @endif><strong>{{ $invoice->remained }} MMK</strong></td> --}}
                        </tr>
                    @endforeach
                @endforeach
			</tbody>
		</table>
	</div>
</div>


@endif
