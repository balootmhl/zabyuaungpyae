@push('head')
    {{-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> --}}
    <link rel="stylesheet" href="{{ asset('custom/css/invoice.css') }}" media="all" />
@endpush



<div id="invoice">

    <div class="toolbar hidden-print">
        <div class="text-right">
            <button id="printInvoice" class="btn btn-info">{{-- <i class="fa fa-print"></i> --}}<x-orchid-icon path="printer" />&nbsp;
                Print</button>
            <a href="{{ route('platform.purchase.edit-custom', $purchase->id) }}"
                class="btn btn-info">{{-- <i class="fa fa-print"></i> --}}<x-orchid-icon path="pencil" />&nbsp; Edit</a>
            {{-- <button class="btn btn-info"><i class="fa fa-file-pdf-o"></i> Export as PDF</button> --}}
        </div>
        <hr>
    </div>
    <div class="invoice overflow-auto">
        <div style="min-width: 600px">
            <header>
                <div class="row">
                    <div class="col-2">

                        <a target="_blank" href="https://zabyuaungpyae.com">
                            <img src="{{ asset('custom/img/logo.png') }}" class="img-fluid">
                        </a>
                    </div>
                    <div class="col-7">

                        <h5 class="name" style="padding-top: 10px;">
                            <a target="_blank" href="https://zabyuaungpyae.com">
                                <strong>MAHARSHIN Co., Ltd. (Head Office)</strong>
                            </a>
                        </h5>
                        {{-- <div><strong><font style="font-size: 1.2rem;">Kubota</font> လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</strong></div> --}}
                        <div><x-orchid-icon path="pointer" />Thongwa Township, Pale Village, No(6) Main Road, Yangon.
                        </div>
                        <div><x-orchid-icon path="phone" />09420250449</div>
                        <div><x-orchid-icon path="globe" />https://zabyuaungpyae.com</div>
                    </div>
                    <div class="col-3">
                        <img src="{{ asset('custom/img/kubota-logo.png') }}" class="img-fluid"
                            style="padding-top: 10px;"> <br>
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
                    {{-- <thead>
                        <tr>
                            <th class="text-right" style="padding:0 !important;">#</th>
                            <th class="text-left">Code</th>
                            <th class="text-left">Description</th>
                            <th class="text-center" style="width: 16px !important;"></th>
                            <th style="text-align: right !important;width: 10% !important;" >Price</th>
                            <th style="text-align: right !important;" >Qty</th>
                            <th style="text-align: right !important;width: 12% !important;" >Total</th>
                        </tr>
                    </thead> --}}
                    <thead style="display: table-row-group">
                        <tr>
                            <th class="text-right" style="padding:0 !important;">#</th>
                            <th class="text-left">Code</th>
                            <th class="text-left">Description</th>
                            <th style="text-align: right !important;">Price</th>
                            <th style="text-align: right !important;">Group</th>
                            <th style="text-align: right !important;">Qty</th>
                            <th class="text-center"></th>
                            <th style="text-align: right !important;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->purchaseitems as $item)
                            <tr>
                                <td class="no" style="padding:0 !important;width: 4% !important;">
                                    {{ $loop->iteration }}</td>
                                <td class="text-left code">
                                    @if ($item->code != null)
                                        {{ $item->code }}
                                    @else
                                        ??????
                                    @endif
                                </td>
                                <td class="text-left">
                                    @if ($item->name != null)
                                        {{ $item->name }}
                                    @else
                                        ??????
                                    @endif
                                </td>
                                <td class="unit">
                                    @if ($item->price != null)
                                        {{ $item->price }}
                                    @else
                                        ?????
                                    @endif
                                </td>
                                <td class="qty">
                                    @if (!is_null($item->product) && !is_null($item->product->group))
                                        {{ $item->product->group->name }}
                                    @endif
                                </td>
                                <td class="qty">{{ $item->quantity }}</td>
                                <td class="text-center"><input type="checkbox" unchecked></td>
                                <td class="total">
                                    @if ($item->price != null)
                                        {{ $item->price * $item->quantity }}
                                    @else
                                        ?????
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                    <tfoot style="display: table-row-group">
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3">SUBTOTAL</td>
                            <td>{{ $purchase->sub_total }} Ks</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3">DISCOUNT</td>
                            <td>{{ $purchase->discount }} Ks</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3">GRAND TOTAL</td>
                            <td>{{ $purchase->grand_total }} Ks</td>
                        </tr>
                        @if ($purchase->received != 0)
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3">RECEIPT</td>
                                <td>{{ $purchase->received }} Ks</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3">REMAINING <br>AMOUNT</td>
                                <td>{{ $purchase->remained }} Ks</td>
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
            {{-- <footer>
                Invoice was created on a computer and is valid without the signature and seal.
            </footer> --}}
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>

@push('scripts')
    {{-- <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script> --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> --}}
    <script>
        $('#printInvoice').click(function() {
            Popup($('.invoice')[0].outerHTML);

            function Popup(data) {
                window.print();
                return true;
            }
        });
    </script>
@endpush
