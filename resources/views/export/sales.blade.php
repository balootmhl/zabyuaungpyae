<table>
    <thead>
        <tr>
            <th><strong>Invoice No</strong></th>
            <th><strong>Customer Name</strong></th>
            <th><strong>Branch</strong></th>
            <th><strong>Issue Date</strong></th>
            <th><strong>Status</strong></th>
            <th><strong>Is Cash</strong></th>
            <th><strong>Sub Total</strong></th>
            <th><strong>Discount</strong></th>
            <th><strong>Grand Total</strong></th>
            <th><strong>Received</strong></th>
            <th><strong>Remained</strong></th>
            <th><strong>Issuer</strong></th>
            <th><strong>Product Code</strong></th>
            <th><strong>Product Name</strong></th>
            <th><strong>Quantity</strong></th>
            <th><strong>Unit Price</strong></th>
            <th><strong>Item Total</strong></th>
            <th><strong>Remarks</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
            @if($sale->saleitems->count() > 0)
                @foreach($sale->saleitems as $index => $item)
                    <tr>
                        @if($index == 0)
                            <!-- Sale info only on first row of each invoice -->
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->custom_name ?? ($sale->customer ? $sale->customer->name : 'N/A') }}</td>
                            <td>{{ $sale->branch ? $sale->branch->name : 'N/A' }}</td>
                            <td>{{ $sale->date }}</td>
                            <td>{{ $sale->status }}</td>
                            <td>{{ $sale->is_cash ? 'Yes' : 'No' }}</td>
                            <td>{{ $sale->sub_total }}</td>
                            <td>{{ $sale->discount }}</td>
                            <td>{{ $sale->grand_total }}</td>
                            <td>{{ $sale->received }}</td>
                            <td>{{ $sale->remained }}</td>
                            <td>{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                        @else
                            <!-- Empty cells for additional item rows -->
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        @endif

                        <!-- Item info on every row -->
                        <td>{{ $item->code ?? ($item->product ? $item->product->code : 'N/A') }}</td>
                        <td>{{ $item->name ?? ($item->product ? $item->product->name : 'N/A') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->quantity * $item->price }}</td>

                        @if($index == 0)
                            <td>{{ $sale->remarks }}</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            @else
                <!-- Sale without items -->
                <tr>
                    <td>{{ $sale->invoice_no }}</td>
                    <td>{{ $sale->custom_name ?? ($sale->customer ? $sale->customer->name : 'N/A') }}</td>
                    <td>{{ $sale->branch ? $sale->branch->name : 'N/A' }}</td>
                    <td>{{ $sale->date }}</td>
                    <td>{{ $sale->status }}</td>
                    <td>{{ $sale->is_cash ? 'Yes' : 'No' }}</td>
                    <td>{{ $sale->sub_total }}</td>
                    <td>{{ $sale->discount }}</td>
                    <td>{{ $sale->grand_total }}</td>
                    <td>{{ $sale->received }}</td>
                    <td>{{ $sale->remained }}</td>
                    <td>{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                    <td colspan="5">No Items</td>
                    <td>{{ $sale->remarks }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
