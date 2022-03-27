@push('head')
	{{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> --}}
	<link rel="stylesheet" href="{{ asset('custom/css/invoice.css') }}" media="all" />
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
                    <div class="col-2">

                        <a target="_blank" href="http://zabyuaungpyae.com">
                            <img src="{{ asset('custom/img/logo.png') }}" class="img-fluid">
                        </a>
                    </div>
                    <div class="col-7">

                        <h5 class="name" style="padding-top: 10px;">
                            <a target="_blank" href="http://zabyuaungpyae.com">
                            <strong>MAHARSHIN Co., Ltd. (Head Office)</strong>
                            </a>
                        </h5>
                        {{-- <div><strong><font style="font-size: 1.2rem;">Kubota</font> လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</strong></div> --}}
                        <div><x-orchid-icon path="pointer"/>Yangon, Myanmar.</div>
                        <div><x-orchid-icon path="phone"/>09 428 936 585, 09 428 936 585</div>
                        <div><x-orchid-icon path="globe"/>http://zabyuaungpyae.com</div>
                    </div>
                    <div class="col-3">
                            <img src="{{ asset('custom/img/kubota-logo.png') }}" class="img-fluid" style="padding-top: 10px;"> <br>
                            <h6 style="padding-left: 5px;font-weight: bold;">လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</h6>
                        {{-- </div> --}}
                    </div>
                    <div class="col-7 company-details">
                        {{-- <h5 class="name">
                            <a target="_blank" href="https://lobianijs.com">
                            <strong>MAHARSHIN Co., Ltd. (Head Office)</strong>
                            </a>
                        </h5>
                        <div><strong><font style="font-size: 1.2rem;">Kubota</font> လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</strong></div>
                        <div><x-orchid-icon path="pointer"/>Yangon, Myanmar.</div>
                        <div><x-orchid-icon path="phone"/>09 428 936 585, 09 428 936 585</div>
                        <div><x-orchid-icon path="globe"/>http://zabyuaungpyae.com</div> --}}
                    </div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">INVOICE TO:</div>
                        <h5 class="to">{{ $sale->customer->name }}</h5>
                        <div class="address">{{ $sale->customer->address }}</div>
                        {{-- <div class="email"><a href="mailto:john@example.com">john@example.com</a></div> --}}
                    </div>
                    <div class="col invoice-details">
                        <h5 class="invoice-id">Invoice No. {{ $sale->invoice_no }}</h5>
                        <div class="date">Date of Invoice: {{ $sale->date }}</div>
                        <div class="date">Invoice By: {{ $sale->user->name }}</div>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-right" style="padding:0 !important;">#</th>
                            <th class="text-left">Code</th>
                            <th class="text-left">Description</th>
                            <th class="text-center">Check</th>
                            <th style="text-align: right !important;" >Unit Price</th>
                            <th style="text-align: right !important;" >Quantity</th>
                            <th style="text-align: right !important;" >Unit Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@foreach($sale->saleitems as $saleitem)
                            @if($loop->index == 15)
                              <div class="page-break"></div>
                            @endif
                    		<tr>
	                            <td class="no" style="padding:0 !important;width: auto !important;">{{ $loop->iteration }}</td>
	                            <td class="text-left">
	                               {{ $saleitem->product->code }}
	                            </td>
	                            <td class="text-left">
	                               {{ $saleitem->product->name }}
	                            </td>
	                            <td class="text-center"><input type="checkbox" unchecked></td>
	                            <td class="unit">{{ $saleitem->product->buy_price }} MMK</td>
	                            <td class="qty">{{ $saleitem->quantity }}</td>
	                            <td class="total">{{ $saleitem->product->buy_price * $saleitem->quantity }} MMK</td>
	                        </tr>
                    	@endforeach

                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="2">SUBTOTAL</td>
                            <td>{{ $sale->sub_total }} MMK</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="2">DISCOUNT</td>
                            <td>{{ $sale->discount }} MMK</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="2">GRAND TOTAL</td>
                            <td>{{ $sale->grand_total }} MMK</td>
                        </tr>
                        @if($sale->received != 0)
			          	  <tr>
				            <td colspan="4"></td>
				            <td colspan="2">RECEIPT</td>
				            <td>{{ $sale->received }} MMK</td>
				          </tr>
				          <tr>
				            <td colspan="4"></td>
				            <td colspan="2">REMAINING <br>AMOUNT</td>
				            <td>{{ $sale->remained }} MMK</td>
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
