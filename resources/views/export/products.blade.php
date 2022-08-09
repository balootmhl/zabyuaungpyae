<table>
    <thead>
    <tr>
        <th>Code</th>
        <th>Product Name</th>
        <th>Category Code</th>
        <th>Category Name</th>
        <th>Buy Price</th>
        <th>Sale Price</th>
        <th>Quantity</th>
        <th>Group</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->code }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->code }}</td>
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
