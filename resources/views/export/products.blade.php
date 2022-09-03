<table>
    <thead>
    <tr>
        <th><strong>Code</strong></th>
        <th><strong>Product Name</strong></th>
        <th><strong>Category Name</strong></th>
        <th><strong>Buy Price</strong></th>
        <th><strong>Sale Price</strong></th>
        <th><strong>Quantity</strong></th>
        <th><strong>Group</strong></th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->code }}</td>
            <td style="width:auto !important;">{{ $product->name }}</td>
            <td>{{ $product->category->name }}</td>
            <td>{{ $product->buy_price }}</td>
            <td>{{ $product->sale_price }}</td>
            <td>{{ $product->quantity }}</td>
            <td>
                @if($product->group)
                {{ $product->group->name }}
                @else
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
