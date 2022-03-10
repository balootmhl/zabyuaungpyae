@push('head')
	{{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> --}}
	<style>
		#invoice{
		    /*padding: 30px;*/
		}

		.invoice {
		    position: relative;
		    background-color: #FFF;
		    min-height: 680px;
		    padding: 15px
		}

		.invoice header {
		    padding: 10px 0;
		    margin-bottom: 20px;
		    border-bottom: 1px solid #143862;
		}

		.invoice .company-details {
		    text-align: right
		}

		.invoice .company-details .name {
		    margin-top: 0;
		    line-height: 2;
		    margin-bottom: 0
		}

		.invoice .contacts {
		    margin-bottom: 20px
		}

		.invoice .invoice-to {
		    text-align: left
		}

		.invoice .invoice-to .to {
		    margin-top: 0;
		    margin-bottom: 0
		}

		.invoice .invoice-details {
		    text-align: right
		}

		.invoice .invoice-details .invoice-id {
		    margin-top: 0;
		    color: #143862;
		}

		.invoice main {
		    padding-bottom: 50px
		}

		.invoice main .thanks {
		    margin-top: -100px;
		    font-size: 2em;
		    margin-bottom: 50px
		}

		.invoice main .notices {
		    padding-left: 6px;
		    border-left: 6px solid #143862;
		}

		.invoice main .notices .notice {
		    font-size: 1.2em
		}

		.invoice table {
		    width: 100%;
		    border-collapse: collapse;
		    border-spacing: 0;
		    margin-bottom: 20px
		}

		.invoice table td,.invoice table th {
		    padding: 15px;
		    /*background: #eee;*/
		    border-bottom: 1px solid #fff
		}

		.invoice table th {
		    white-space: nowrap;
		    font-weight: 400;
		    font-size: 16px
		}

		.invoice table td h3 {
		    margin: 0;
		    font-weight: 400;
		    color: #3989c6;
		    font-size: 1.2em
		}

		.invoice table .qty,.invoice table .total,.invoice table .unit {
		    text-align: right;
		    /*font-size: 1.2em*/
		}

		.invoice table .no {
		    /*color: #fff;*/
		    /*font-size: 1.1em;*/
		    /*background: #3989c6*/
		}

		.invoice table .unit {
		    /*background: #ddd*/
		}

		.invoice table .total {
		    /*background: #3989c6;*/
		    /*color: #fff*/
		}

		.invoice table tbody tr:last-child td {
		    border: none
		}

		.invoice table tfoot td {
		    background: 0 0;
		    border-bottom: none;
		    white-space: nowrap;
		    text-align: right;
		    padding: 10px 20px;
		    font-size: 1.2em;
		    border-top: 1px solid #aaa
		}

		.invoice table tfoot tr:first-child td {
		    border-top: none
		}

		.invoice table tfoot tr:last-child td {
		    color: #143862;
		    font-size: 1.4em;
		    border-top: 1px solid #143862;
		}

		.invoice table tfoot tr td:first-child {
		    border: none
		}

		.invoice footer {
		    width: 100%;
		    text-align: center;
		    color: #777;
		    border-top: 1px solid #aaa;
		    padding: 8px 0
		}

		.table thead tr th:first-child {
			padding-left: 1rem !important;
		}

		.table tbody tr td:first-child {
			padding-left: 1rem !important;
		}

		.table thead tr th:last-child {
			padding-right: 1rem !important;
		}

		.table tbody tr td:last-child {
			padding-right: 1rem !important;
		}

		.table tbody tr td, .table thead tr th {
			padding: 10px 1rem !important;
		}

		@media print {
		    
		    .invoice {
		        font-size: 11px!important;
		    }

		    /*.invoice footer {
		        position: absolute;
		        bottom: 10px;
		        page-break-after: always
		    }*/

		    /*.invoice>div:last-child {
		        page-break-before: always
		    }*/

		    #printInvoice, .layout, .aside, nav, .alert, .alert-info {
		    	display: none !important;
		    }

		    .min-vh-100 {
		    	background-color: #fff !important;
		    }

		    .page-break {
		        page-break-after: always;
		    }
		}

	</style>
@endpush



<div id="invoice">

    <div class="toolbar hidden-print">
        <div class="text-right">
            <button id="printInvoice" class="btn btn-info">{{-- <i class="fa fa-print"></i> --}}<x-orchid-icon path="printer"/>&nbsp; Print</button>
            {{-- <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button> --}}
        </div>
        <hr>
    </div>
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col-6">
                    	<div class="row">
                    		<div class="col-6">
                    			<a target="_blank" href="http://zabyuaungpyae.com">
	                            {{-- <img src="http://lobianijs.com/lobiadmin/version/1.0/ajax/img/logo/lobiadmin-logo-text-64.png" data-holder-rendered="true" /> --}}
	                            <img src="{{ asset('custom/img/logo.png') }}" style="height: 120px;width:auto;">
	                            </a>
                    		</div>
                    		<div class="col-6">
                    			<img src="{{ asset('custom/img/kubota-logo.png') }}" style="width: 100%;height: auto;text-align: right;padding-top: 20px;">
                    		</div>
                    	</div>
                        
                    </div>
                    <div class="col-6 company-details">
                        <h2 class="name">
                            <a target="_blank" href="http://zabyuaungpyae.com">
                            Zabyu Aung Pyae
                            </a>
                        </h2>
                        <div>လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</div>
                        <div><x-orchid-icon path="pointer"/>Yangon, Myanmar.</div>
				        <div><x-orchid-icon path="phone"/> 09 428 936 585</div>
				        <div><x-orchid-icon path="phone"/>09 428 936 585</div>
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">SUPPLIER INFO:</div>
                        <h5 class="to">{{ $purchase->supplier->name }}</h5>
                        <div class="address">{{ $purchase->supplier->address }}</div>
                        {{-- <div class="email"><a href="mailto:john@example.com">john@example.com</a></div> --}}
                    </div>
                    <div class="col invoice-details">
                        <h5 class="invoice-id">Invoice No. {{ $purchase->invoice_no }}</h5>
                        <div class="date">Date of Invoice: {{ $purchase->date }}</div>
                        <div class="date">Purchased By: {{ $purchase->user->name }}</div>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-right" width="5%">#</th>
                            <th class="text-left" width="auto">Description</th>
                            <th style="text-align: right !important;" width="18%">Unit Price</th>
                            <th style="text-align: right !important;" width="5%">Quantity</th>
                            <th style="text-align: right !important;" width="18%">Unit Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@foreach($purchase->purchaseitems as $purchaseitem)
                    		<tr>
	                            <td class="no">{{ $loop->iteration }}</td>
	                            <td class="text-left">
	                               {{ $purchaseitem->product->code }} [{{ $purchaseitem->product->name }}]
	                            </td>
	                            <td class="unit">{{ $purchaseitem->product->buy_price }} MMK</td>
	                            <td class="qty">{{ $purchaseitem->quantity }}</td>
	                            <td class="total">{{ $purchaseitem->product->buy_price * $purchaseitem->quantity }} MMK</td>
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
                        <tr>
                            <td colspan="2"></td>
                            <td colspan="2">GRAND TOTAL</td>
                            <td>{{ $purchase->grand_total }} MMK</td>
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
                    </tfoot>
                </table>
                <div class="thanks">Thank you!</div>
                <div class="notices">
                    <div>NOTICE:</div>
                    <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
                </div>
            </main>
            <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>

@push('scripts')
	{{-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script> --}}
	{{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
	<script>
		$('#printInvoice').click(function(){
            Popup($('.invoice')[0].outerHTML);
            function Popup(data) 
            {
                window.print();
                return true;
            }
        });
	</script>
@endpush
