@push('head')
	<style>
		.invoice-body {
		  position: relative;
		  width: 80%;
		  /*height: 29.7cm;*/
		  padding-bottom: 30px;
		  margin: 0 auto;
		  color: #555555;
		  background: #FFFFFF;
		  font-family: Arial, sans-serif;
		  font-size: 12px !important;
		}

		.invoice-body header {
		  padding: 10px 0;
		  margin-bottom: 20px;
		  border-bottom: 1px solid #AAAAAA;
		}

		.invoice-body #logo {
		  float: left;
		  margin-top: 8px;
		}

		.invoice-body #logo img {
		  height: 70px;
		}

		.invoice-body #company {
		  padding-top:20px;
		  float: right;
		  text-align: right;
		}


		.invoice-body #details {
		  margin-bottom: 50px;
		}

		.invoice-body #client {
		  padding-left: 6px;
		  border-left: 6px solid rgba(0,0,0,0.5);
		  float: left;
		}

		.invoice-body #client .to {
		  font-size: 0.8em;
		  color: #777777;
		}

		.invoice-body h2.name {
		  font-size: 0.8em;
		  font-weight: normal;
		  margin: 0;
		}

		.invoice-body #company h2.name {
			font-size: 2em;
		}

		.invoice-body div.address {
			font-size: 0.8em;
		}

		.invoice-body #invoice {
		  float: right;
		  text-align: right;
		}

		.invoice-body #invoice h1 {
		  color: #143862;
		  font-size: 0.8em;
		  line-height: 1em;
		  font-weight: bold;
		  margin: 0  0 10px 0;
		}

		.invoice-body #invoice .date {
		  font-size: 0.8em;
		  color: #777777;
		}

		.invoice-body table {
		  width: 100%;
		  border-collapse: collapse;
		  border-spacing: 0;
		  margin-bottom: 20px;
		}

		.invoice-body table th,
		.invoice-body table td {
		  padding: 10px;
		  background: #EEEEEE;
		  text-align: center;
		  border-bottom: 1px solid #FFFFFF;
		}

		.invoice-body .table tbody tr td {
			font-size: 10px !important;
		}

		.invoice-body table th {
		  white-space: nowrap;
		  font-weight: normal;
		}

		.invoice-body table td {
		  	/*text-align: right;*/
		}

		.invoice-body table td h3{
		  	color: #143862;
		  	font-size: 1em;
		  	font-weight: normal;
		  	margin: 0;
		}

		.invoice-body table .no {
		  	color: #FFFFFF;
		  	font-size: 0.7em;
		  	background: #F58020;
		  	text-align: center;
		}

		.invoice-body table .desc {
			font-size: 0.7em;
		  	text-align: left;
		}

		.invoice-body table .unit {
		  	font-size: 0.7em;
		  	background: #DDDDDD;
		}

		.invoice-body table .qty {
		  	font-size: 0.7em;
		  	text-align: center;
		}

		.invoice-body table .total {
			font-size: 0.7em;
		  	background: #F58020;
		  	color: #FFFFFF;
		}

		.invoice-body table td.unit,
		.invoice-body table td.qty,
		.invoice-body table td.total {
		  font-size: 0.7em;
		}

		.invoice-body table tbody tr:last-child td {
		  border: none;
		}

		.invoice-body table tfoot td {
		  padding: 10px 20px;
		  background: #FFFFFF;
		  border-bottom: none;
		  font-size: 0.8em;
		  white-space: nowrap;
		  border-top: 1px solid #AAAAAA;
		}

		.invoice-body table tfoot tr:first-child td {
		  border-top: none;
		}

		.invoice-body table tfoot tr:last-child td {
		  color: rgba(0,0,0,0.5);
		  font-size: 1.1em;
		  font-weight: bold;
		  border-top: 1px solid rgba(0,0,0,0.8);

		}

		.invoice-body table tfoot tr td:first-child {
		  border: none;
		}

		.invoice-body #thanks{
		  font-size: 1.6em;
		  margin-bottom: 50px;
		}

		.invoice-body #notices{
		  padding-left: 6px;
		  border-left: 6px solid rgba(0,0,0,0.5);
		}

		.invoice-body #notices .notice {
		  font-size: 0.8em;
		}

		/*footer {
		  color: #777777;
		  width: 100%;
		  height: 30px;
		  position: absolute;
		  bottom: 0;
		  border-top: 1px solid #AAAAAA;
		  padding: 8px 0;
		  text-align: center;
		}*/

		.invoice-body th, .invoice-body td {
			padding: 5px 1em !important; 
		}

		.table thead tr th:first-child {
			padding-left: 1em !important;
		}

		.table tbody tr td:first-child {
			padding-left: 1em !important;
		}

		.table thead tr th:last-child {
			padding-right: 1em !important;
		}

		.table tbody tr td:last-child {
			padding-right: 1em !important;
		}

		.desc-col {
			text-align: left !important;
		}

		.invoice-body thead th {
			color: rgba(0,0,0, 0.5) !important;
		}

	</style>
@endpush

    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        <div class="invoice-body">
        	<div class="form-group mb-0">
        		<a href="{{ url('/admin/purchases/invoice/print/'. $purchase->id) }}" class="btn btn-link" style="text-decoration: none;" target="_blank" ><strong><x-orchid-icon path="printer"/> Print / Download</strong></a>
        	</div>

        	<header class="clearfix">
		      <div id="logo">
		        <img src="{{ asset('custom/img/logo.png') }}" style="height: 150px;width:auto;">
		      </div>
		      <div id="company">

		        <h2 class="name"><strong>Maharshin</strong></h2>
		        <div>Agricultural Equipment Sales</div>
		        <div><x-orchid-icon path="pointer"/>Yangon, Myanmar.</div>
		        <div><x-orchid-icon path="phone"/> 09 428 936 585</div>
		        <div><x-orchid-icon path="phone"/>09 428 936 585</div>
		        <div>maharshin@maharshin.com</div>
		      </div>
		    </header>
		    <main>
		      <div id="details" class="clearfix">
		        <div id="client">
		          <div class="to">SUPPLIER INFO:</div>
		          <h2 class="name">{{ $purchase->supplier->name }}</h2>
		          <div class="address">{{ $purchase->supplier->address }}</div>
		          {{-- <div class="email">baloot.mhl@gmail.com</div> --}}
		        </div>
		        <div id="invoice">
		          <h1>Invoice No. : {{ $purchase->invoice_no }}</h1>
		          <div class="date">Date: {{ $purchase->date }}</div>
		          <div class="date">Purchased By: {{ $purchase->user->name }}</div>
		        </div>
		      </div>
		      <table class="table table-stripe">
		      	<thead>
		      		<tr>
		      			<th width="5%">Sr</th>
		      			<th class="desc-col" width="auto">Description</th>
		      			<th width="15%">Unit Price</th>
		      			<th width="5%">Quantity</th>
		      			<th width="10%">Total</th>
		      		</tr>
		      	</thead>
		      	<tbody>
		      		@foreach($purchase->purchaseitems as $purchaseitem)
		      			<tr>
		        			<td>{{ $loop->iteration }}</td>
		        			<td class="desc-col"><h3>{{ $purchaseitem->product->code }} [{{ $purchaseitem->product->name }}]</h3></td>
		        			<td>{{ $purchaseitem->product->buy_price }} MMK</td>
		        			<td>{{ $purchaseitem->quantity }}</td>
		        			<td>{{ $purchaseitem->product->sale_price * $purchaseitem->quantity }} MMK</td>
		        		</tr>
		      		@endforeach
		      	</tbody>
		      	<tfoot>
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">SUBTOTAL</td>
		            <td>{{ $purchase->sub_total }} MMK</td>
		          </tr>
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">DISCOUNT</td>
		            <td>{{ $purchase->discount }} MMK</td>
		          </tr>
		          @if($purchase->received != 0)
		          	  <tr>
			            <td colspan="2"></td>
			            <td colspan="2">RECEIPT</td>
			            <td>{{ $purchase->received }} MMK</td>
			          </tr>
			          <tr>
			            <td colspan="2"></td>
			            <td colspan="2">REMAINING <br>AMOUNT</td>
			            <td>{{ $purchase->remained }} MMK</td>
			          </tr>
		          @endif
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">GRAND TOTAL</td>
		            <td>{{ $purchase->grand_total }} MMK</td>
		          </tr>
		        </tfoot>
		      </table>
		      {{-- <table border="0" cellspacing="0" cellpadding="0">
		        <thead>
		          <tr>
		            <th class="no" width="5%">#</th>
		            <th class="desc" width="auto"><strong>DESCRIPTION</strong></th>
		            <th class="unit" width="15%"><strong>UNIT PRICE</strong></th>
		            <th class="qty" width="5%"><strong>QUANTITY</strong></th>
		            <th class="total" width="10%"><strong>TOTAL</strong></th>
		          </tr>
		        </thead>
		        <tbody>
		        	@foreach($purchase->purchaseitems as $purchaseitem)
		        		<tr>
		        			<td class="no">{{ $loop->iteration }}</td>
		        			<td class="desc"><h3>{{ $purchaseitem->product->code }} [{{ $purchaseitem->product->name }}]</h3></td>
		        			<td class="unit">{{ $purchaseitem->product->sale_price }} MMK</td>
		        			<td class="qty">{{ $purchaseitem->quantity }}</td>
		        			<td class="total">{{ $purchaseitem->product->sale_price * $purchaseitem->quantity }} MMK</td>
		        		</tr>
		        	@endforeach
		        </tbody>
		        <tfoot>
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">SUBTOTAL</td>
		            <td>{{ $purchase->sub_total }} MMK</td>
		          </tr>
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">DISCOUNT</td>
		            <td>{{ $purchase->discount }} MMK</td>
		          </tr>
		          @if($purchase->received != 0)
		          	  <tr>
			            <td colspan="2"></td>
			            <td colspan="2">RECEIPT</td>
			            <td>{{ $purchase->received }} MMK</td>
			          </tr>
			          <tr>
			            <td colspan="2"></td>
			            <td colspan="2">REMAINING <br>AMOUNT</td>
			            <td>{{ $purchase->remained }} MMK</td>
			          </tr>
		          @endif
		          <tr>
		            <td colspan="2"></td>
		            <td colspan="2">GRAND TOTAL</td>
		            <td>{{ $purchase->grand_total }} MMK</td>
		          </tr>
		        </tfoot>
		      </table> --}}
		      <div id="thanks">Thank you!</div>
		      <div id="notices">
		        <div>NOTICE:</div>
		        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
		      </div>
		    </main>
        </div>
    </div>

@push('scripts')
@endpush
