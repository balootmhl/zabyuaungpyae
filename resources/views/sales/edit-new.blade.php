@extends('platform::dashboard')

@section('title')
    @if ($sale->is_inv_auto == 1)
        Sale Invoice (Auto)
    @else
        Sale Invoice (Manual)
    @endif
@stop

@section('description', '')

@section('navbar')
    <div class="text-center">
        <a href="{{ route('platform.sale.view', $sale->id) }}" class="btn btn-primary">View</a>
    </div>
@stop

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2 {
            max-width: 100% !important;
        }
    </style>
@endpush

@section('content')

    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        <form action="{{ route('platform.sale.update-custom') }}" method="POST">
            {{-- <form action="" method="POST"> --}}
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="app_url" value="{{ config('app.url') }}">
            <input type="hidden" id="sale_id" name="sale_id" value="{{ $sale->id }}">
            <input type="hidden" name="items_count" id="items_count" value="{{ $items_count }}">
            <div class="row justify-content-center invoice-form">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="code">Invoice Code</label>
                        <input type="text" name="invoice_code" class="form-control" value="{{ $sale->invoice_code }}">
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="is_inv_auto">Inv system</label>
                        <select class="form-control" name="is_inv_auto" id="is_inv_auto" required>
                            <option value="1" @if ($sale->is_inv_auto == 1) selected @endif>Auto</option>
                            <option value="0" @if ($sale->is_inv_auto == 0) selected @endif>Manual</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="sale[date]">Date</label>
                        <input type="date" name="date" class="form-control" value="{{ $sale->date }}" required>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center invoice-form">
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select class="form-control customer-select2" name="customer_id" multiple="multiple">
                            @if(auth()->user()->id == 2)
                                @php
                                    $customer = \App\Models\Customer::findOrFail(22);
                                @endphp
                                <option value="{{ $customer->name }}" selected="true">{{ $customer->name }}</option>
                            @elseif(auth()->user()->id == 3)
                                @php
                                    $customer_sale_2 = \App\Models\Customer::findOrFail(5);
                                @endphp
                                <option value="{{ $customer_sale_2->name }}" selected="true">{{ $customer_sale_2->name }}</option>
                            @else
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->name }}"
                                        @if ($customer->id == $sale->customer_id) selected="true" @endif>{{ $customer->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" name="address" class="form-control" value="{{ $sale->custom_address }}">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="is_saleprice">Select Price</label>
                        <select class="form-control" name="is_saleprice" id="is_sale">
                            <option value="1" @if ($sale->is_saleprice == 1) selected @endif>Sale Price</option>
                            @if(auth()->user()->id == 1)
                                <option value="0" @if ($sale->is_saleprice == 0) selected @endif>Buy Price</option>
                            @endif
                        </select>
                    </div>
                </div>
                {{-- <div class="col-sm-3">
				<div class="form-group">
					<label for="discount">Discount</label>
					<input type="text" id="discount" name="discount" class="form-control" min="0" value="{{ $sale->discount }}">
				</div>
			</div>

			<div class="col-sm-3">
				<div class="form-group">
					<label for="received">Received</label>
					<input type="text" id="received" name="received" class="form-control" min="0" value="{{ $sale->received }}">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label for="remarks">Remark</label>
					<input type="text" name="remarks" class="form-control" value="{{ $sale->remarks }}">
				</div>
			</div> --}}
            </div>
            {{-- </form> --}}

            <div class="row justify-content-center invoice-form">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    {{-- <th>Price by</th> --}}
                                    <th>Products</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    {{-- <td width="10%">
									<div class="form-group">
										<select class="form-control" name="is_sale_price" id="is_sale" required>
											<option value="1">Sale</option>
											<option value="0">Buy</option>
										</select>
									</div>
								</td> --}}
                                    <td width="60%">
                                        <div class="form-group">
                                            {{-- <label for="product">Select Product</label> --}}
                                            <select name="product" id="product" class="product-select2 form-control"
                                                multiple>
                                                @foreach ($products as $product)
                                                    <option id="{{ $product->id }}" value="{{ $product->id }}" @if($product->quantity <= 0) disabled @endif>
                                                        [{{ $product->id }}] [{{ $product->code }}]
                                                        [{{ $product->name }}] @if($product->quantity <= 0) Out of Stock @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td width="15%">
                                        {{-- <label for="">Price</label> --}}
                                        <input type="hidden" id="price" name="price" min="0" value="0">
                                        <h6 class="mt-1" id="price_text">0</h6>
                                    </td>
                                    <td width="15%">
                                        {{-- <div class="form-group"> --}}
                                        {{-- <label for="qty">Quantity</label> --}}
                                        <input type="number" id="qty" name="qty" min="0" value="0"
                                            class="form-control">
                                        {{-- </div> --}}
                                    </td>
                                    <td width="10%">
                                        <div class="toolbar">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                        {{-- <div class="form-group"> --}}
                                        {{-- <label for="" style="visibility: hidden;">Select Product</label> --}}
                                        {{-- <button type="button" id="add" class="btn btn-primary" onclick="clear()">Add</button> --}}
                                        {{-- </div> --}}
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot></tfoot>
                        </table>
                    </div>
                    {{-- <div role="alert" id="errorMsg" class="mt-5" style="margin-bottom:20px;"> --}}
                </div>
            </div>

            <div class="row justify-content-center invoice-form">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="discount">Discount</label>
                        <input type="text" id="discount" name="discount" class="form-control" min="0"
                            value="{{ $sale->discount }}">
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="received">Received</label>
                        <input type="text" id="received" name="received" class="form-control" min="0"
                            value="{{ $sale->received }}">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="remarks">Remark</label>
                        <input type="text" name="remarks" class="form-control" value="{{ $sale->remarks }}">
                    </div>
                </div>
            </div>

            <div class="row justify-content-center invoice-form">
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table id="receipt_bill" class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center">Group</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Unit Total</th>
                                </tr>
                            </thead>
                            <tbody id="new">
                                @foreach ($sale->saleitems as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            [{{ $item->product_id }}]
                                            @if ($item->code != null)
                                                [{{ $item->code }}]
                                            @else
                                                ??????
                                            @endif
                                            @if ($item->name != null)
                                                [{{ $item->name }}]
                                            @else
                                                [??????]
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($item->price != null)
                                                {{ $item->price }}
                                            @else
                                                ?????
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (!is_null($item->product) && !is_null($item->product->group))
                                                {{ $item->product->group->name }}
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-center">
                                            <strong>
                                                @if ($item->price != null)
                                                    {{ $item->price * $item->quantity }}
                                                @else
                                                    ?????
                                                @endif
                                                &nbsp;
                                                <a href="{{ url('/admin/sales/saleitems/delete/' . $item->id) }}"
                                                    class="delete-btn" onclick="return confirm('Are you sure?')">
                                                    <x-orchid-icon path="trash"
                                                        style="padding-bottom: 5px !important;" />
                                                </a>
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td class="text-right text-dark">
                                        <h5><strong>Subtotal: MMK </strong></h5>
                                        <p><strong>Discount : MMK </strong></p>
                                    </td>
                                    <td class="text-center text-dark">
                                        <h5> <strong><span id="subTotal">{{ $sale->sub_total }}</span></strong></h5>
                                        <input type="hidden" id="sub_total" name="sub_total"
                                            value="{{ $sale->sub_total }}">
                                        <h5> <strong><span id="taxAmount">{{ $sale->discount }}</strong></h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                    <td class="text-right text-dark">
                                        <h5><strong>Grand Total: MMK </strong></h5>
                                        @if ($sale->received != 0)
                                            <p><strong>Received : MMK </strong></p>
                                            <h5><strong>Remain to pay: MMK </strong></h5>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <h5 id="totalPayment" class="text-danger">
                                            <strong>{{ $sale->grand_total }}</strong>
                                        </h5>
                                        <input type="hidden" id="grand_total" name="grand_total"
                                            value="{{ $sale->grand_total }}">
                                        @if ($sale->received != 0)
                                            <h5><strong><span id="taxAmount">{{ $sale->received }}</span></strong></h5>
                                            <h5 id="totalPayment" class="text-danger">
                                                <strong>{{ $sale->remained }}</strong>
                                            </h5>
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center invoice-form">
                <div class="col-sm-12">
                    <div class="toolbar">
                        <input type="submit" class="btn btn-success" value="Save">
                        <a href="{{ route('platform.sale.view', $sale->id) }}" class="btn btn-success">View</a>
                    </div>

                </div>
            </div>
        </form>
    </div>

@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript">
        // activate select2 plugin
        $(document).ready(function() {
            $('.user-select2').select2({
                placeholder: 'Select User',
                theme: "bootstrap"
            });
        });
        $(document).ready(function() {
            $('.customer-select2').select2({
                placeholder: 'Enter to select or create',
                tags: true,
                theme: "bootstrap"
            });
        });
        $(document).ready(function() {
            $('.product-select2').select2({
                placeholder: 'Select Product',
                theme: "bootstrap"
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#product').change(function() {
                var ids = $(this).find(':selected')[0].id;
                var is_sale = $('#is_sale').val();
                var url = $('#app_url').val();
                $.ajax({
                    type: 'GET',
                    url: url + '/admin/getPrice/{id}',
                    data: {
                        id: ids
                    },
                    dataType: 'json',
                    success: function(data) {

                        $.each(data, function(key, resp) {
                            if (is_sale == '1') {
                                $('#price').val(resp.sale_price);
                                $('#price_text').text(resp.sale_price);
                            } else {
                                $('#price').val(resp.buy_price);
                                $('#price_text').text(resp.buy_price);
                            }

                        });
                    }
                });
            });

            //add to cart
            var count = $('#items_count').val();

            $('#add').on('click', function() {

                var name = $('#product').val();
                var p_id = $('#product').find(':selected')[0].id;
                var qty = $('#qty').val();
                var price = $('#price').text();
                var discount = $('#discount').val();

                if (qty == 0) {
                    var erroMsg =
                        '<span class="alert alert-danger ml-5">Minimum Qty should be 1 or More than 1</span>';
                    $('#errorMsg').html(erroMsg).fadeOut(9000);
                } else {
                    billFunction(); // Below Function passing here
                }

                function billFunction() {
                    var total = 0;
                    var iteration = parseInt(count) + 1;
                    $("#receipt_bill").each(function() {
                        var total = price * qty;
                        var subTotal = 0;
                        subTotal += parseInt(total);

                        var table = '<tr><td>' + iteration + '</td><td>' + name +
                            '<input type="hidden" name="products[' + count +
                            '][product_id]" value="' + p_id + '"></td><td class="text-center">' +
                            qty + '<input type="hidden" name="products[' + count +
                            '][qty]" value="' + qty + '"></td><td class="text-center">' + price +
                            '</td><td class="text-center"><strong><input type="hidden" id="total" value="' +
                            total + '">' + total + '</strong></td></tr>';
                        $('#new').append(table)

                        // Code for Sub Total of Vegitables
                        var total = 0;
                        $('tbody tr td:last-child').each(function() {
                            var value = parseInt($('#total', this).val());
                            if (!isNaN(value)) {
                                total += value;
                            }
                        });
                        $('#subTotal').text(total);
                        $('#sub_total').val(total);

                        // Code for calculate tax of Subtoal 5% Tax Applied
                        var Tax = (total * 5) / 100;
                        // $('#taxAmount').text(Tax.toFixed(2));
                        $('#taxAmount').text(discount);

                        // Code for Total Payment Amount

                        var Subtotal = $('#subTotal').text();
                        var taxAmount = $('#taxAmount').text();

                        var totalPayment = parseFloat(Subtotal) - parseFloat(taxAmount);
                        // $('#totalPayment').text(totalPayment.toFixed(2)); // Showing using ID
                        $('#totalPayment').text(totalPayment);
                        $('#grand_total').val(totalPayment);

                    });
                    count++;
                    $("#product").val(0).trigger("change");
                }
            });
            // Code for year

            // var currentdate = new Date();
            //   var datetime = currentdate.getDate() + "/"
            //      + (currentdate.getMonth()+1)  + "/"
            //      + currentdate.getFullYear();
            //      $('#year').text(datetime);



            // Code for extract Weekday
            // function myFunction()
            //  {
            //     var d = new Date();
            //     var weekday = new Array(7);
            //     weekday[0] = "Sunday";
            //     weekday[1] = "Monday";
            //     weekday[2] = "Tuesday";
            //     weekday[3] = "Wednesday";
            //     weekday[4] = "Thursday";
            //     weekday[5] = "Friday";
            //     weekday[6] = "Saturday";

            //     var day = weekday[d.getDay()];
            //     return day;
            //     }
            // var day = myFunction();
            // $('#day').text(day);
        });
    </script>
    <script>
        // window.onload = displayClock();

        //  function displayClock(){
        //    var time = new Date().toLocaleTimeString();
        //    document.getElementById("time").innerHTML = time;
        //     setTimeout(displayClock, 1000);
        //  }
    </script>
@endpush
