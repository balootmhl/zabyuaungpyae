@push('head')
    <link rel="stylesheet" href="{{ asset('custom/css/invoice.css') }}" media="all" />
@endpush

<div id="invoice" style="">

    <div class="toolbar hidden-print">
        <div class="text-right">
            <button id="printInvoice" class="btn btn-info">
                <x-orchid-icon path="printer" />&nbsp;Print
            </button>
            <a href="{{ route('platform.sale.edit-custom', $sale->id) }}"
                class="btn btn-info">
                <x-orchid-icon path="pencil" />&nbsp; Edit
            </a>
        </div>
        <hr>
    </div>
    <div class="invoice overflow-auto">
        <div style="min-width: 600px;">
            <header>
                <div class="row">
                    <div class="col-2">

                        <a target="_blank" href="https://zabyuaungpyae.com">
                            @if(auth()->user()->id == 1)
                                <img src="{{ asset('custom/img/logo.png') }}" class="img-fluid">
                            @else
                                <img src="{{ asset('custom/img/zabyuaungpyae-logo.jpeg') }}" class="img-fluid">
                            @endif
                        </a>
                    </div>
                    <div class="col-7">

                        <h5 class="name" style="padding-top: 10px;">
                            <a target="_blank" href="https://zabyuaungpyae.com">
                                <strong>{{ auth()->user()->branch->name }}</strong>
                            </a>
                        </h5>
                        <div><x-orchid-icon path="pointer" />{{ auth()->user()->branch->address }}
                        </div>
                        <div><x-orchid-icon path="phone" />{{ auth()->user()->branch->phone }}</div>
                        <div><x-orchid-icon path="globe" />https://zabyuaungpyae.com</div>
                    </div>
                    <div class="col-3">
                        <img src="{{ asset('custom/img/kubota-logo.png') }}" class="img-fluid"
                            style="padding-top: 10px;"> <br>
                        <h6 style="padding-left: 5px;font-weight: bold;">လယ်ယာသုံး စက်ပစ္စည်းအရောင်းဆိုင်</h6>
                    </div>
                    <div class="col-7 company-details"></div>
                </div>
            </header>
            <main>
                <div class="row contacts">
                    <div class="col invoice-to">
                        <div class="text-gray-light">CUSTOMER:</div>
                        <h5 class="to">{{ $sale->customer->name }}</h5>
                        <div class="address">
                            @if ($sale->customer->address)
                                {{ $sale->customer->address }}
                            @else
                                {{ $sale->customer->custom_address }}
                            @endif
                        </div>
                    </div>
                    <div class="col invoice-details">
                        <h5 class="invoice-id">Invoice No. {{ $sale->invoice_no }}</h5>
                        <div class="date">Date: {{ $sale->date }}</div>
                        <div class="date">Issuer: {{ $sale->user->name }}</div>
                    </div>
                </div>
                <table class="table table-striped">
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
                        @foreach ($sale->saleitems as $item)
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
                            <td>{{ $sale->sub_total }}</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3">DISCOUNT</td>
                            <td>{{ $sale->discount }}</td>
                        </tr>
                        <tr>
                            <td colspan="4"></td>
                            <td colspan="3">GRAND TOTAL</td>
                            <td>{{ $sale->grand_total }}</td>
                        </tr>
                        @if ($sale->received != 0)
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3">RECEIPT</td>
                                <td>{{ $sale->received }}</td>
                            </tr>
                            <tr>
                                <td colspan="4"></td>
                                <td colspan="3">REMAINING <br>AMOUNT</td>
                                <td>{{ $sale->remained }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
                <div class="thanks">Thank You!</div>
                <div class="notices">
                    <div>NOTICE: A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
                    <div>REMARKS: {{ $sale->remarks }}</div>
                </div>
            </main>
        </div>
        <!--DO NOT DELETE THIS div. IT is responsible for showing footer always at the bottom-->
        <div></div>
    </div>
</div>

@push('scripts')
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
