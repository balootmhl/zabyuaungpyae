@extends('platform::dashboard')

@section('title','Search Results')
@section('description', '')

@section('navbar')
    <div class="text-center">
        {{-- <button type="button" class="btn btn-warning" onclick="window.location.reload();">Refresh</button> --}}
        {{-- <a href="{{ route('platform.sale.create-custom') }}" class="btn btn-link">Create</a> --}}
    </div>
@stop

@push('head')
@endpush

@section('content')

	<div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
		<div class="table-responsive">
    		<table class="table table-bordered">
    			<thead>
    				<tr>
    					<th style="color: #667780 !important;">Invoice Info</th>
    					<th style="color: #667780 !important;">Actions</th>
    				</tr>
    			</thead>
    			<tbody>
    				@foreach($items as $item)
    				   <tr>
    				    	<td>
    				    		Invoice No. : {{ $item->sale->invoice_no }}
    				    		Date : {{ $item->sale->date }}, Customer : {{ $item->sale->customer->name }}
    				    		<hr>
    				    		<p class="text-warning">Code : {{ $item->code }} - Name : {{ $item->name }} - Quantity : {{ $item->quantity }}</p>
    				    	</td>
    				    	<td></td>
    				   </tr>
    				@endforeach
    			</tbody>
    		</table>
    	</div>
	</div>

@stop

@push('scripts')
@endpush
