<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <link rel="stylesheet" href="{{ asset('custom/font/sale-invoice.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('custom/font/dejavu-sans.css') }}" media="all" />
    <style>
      body {
        /*font-family: 'dejavu_sansbook' !important;*/
      }
    </style>
  </head>

  <body>
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
          <div class="to">INVOICE TO:</div>
          <h2 class="name">{{ $sale->customer->name }}</h2>
          <div class="address">{{ $sale->customer->address }}</div>
          <div class="email">baloot.mhl@gmail.com</div>
        </div>
        <div id="invoice">
          <h1>Invoice No. : {{ $sale->invoice_no }}</h1>
          <div class="date">Date: {{ $sale->date }}</div>
          <div class="date">Invoice By: {{ $sale->user->name }}</div>
        </div>
      </div>
      <table border="0" cellspacing="0" cellpadding="0">
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
        	@foreach($sale->saleitems as $saleitem)
        		<tr>
        			<td class="no">{{ $loop->iteration }}</td>
        			<td class="desc"><h3>{{ $saleitem->product->name }}</h3></td>
        			<td class="unit">{{ $saleitem->product->sale_price }} MMK</td>
        			<td class="qty">{{ $saleitem->quantity }}</td>
        			<td class="total">{{ $saleitem->product->sale_price * $saleitem->quantity }} MMK</td>
        		</tr>
        	@endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">SUBTOTAL</td>
            <td>{{ $sale->sub_total }} MMK</td>
          </tr>
          <tr>
            <td colspan="2"></td>
            <td colspan="2">DISCOUNT</td>
            <td>{{ $sale->discount }} MMK</td>
          </tr>
          @if($sale->received != 0)
          	  <tr>
	            <td colspan="2"></td>
	            <td colspan="2">RECEIPT</td>
	            <td>{{ $sale->received }} MMK</td>
	          </tr>
	          <tr>
	            <td colspan="2"></td>
	            <td colspan="2">REMAINING <br>AMOUNT</td>
	            <td>{{ $sale->remained }} MMK</td>
	          </tr>
          @endif
          <tr>
            <td colspan="2"></td>
            <td colspan="2">GRAND TOTAL</td>
            <td>{{ $sale->grand_total }} MMK</td>
          </tr>
        </tfoot>
      </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div>NOTICE:</div>
        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
      </div>
    </main>
    <footer>
      Invoice was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>
