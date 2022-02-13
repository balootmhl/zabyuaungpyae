<table>
    <thead>
    <tr>
        <th>Code</th>
        <th>Customer Name</th>
        <th>Phone</th>
        <th>Address</th>
        {{-- <th>Registered Date</th> --}}
        <th>Debt</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customers as $customer)
        <tr>
            <td>{{ $customer->code }}</td>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->phone }}</td>
            <td>{{ $customer->address }}</td>
            {{-- <td>{{ $customer->created_at->toDateString() }}</td> --}}
            <td>{{ $customer->debt }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
