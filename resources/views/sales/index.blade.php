@extends('platform::dashboard')

@section('title','Sale Invoices')
@section('description', '')

@section('navbar')
    <div class="text-center">
        <a href="{{ route('platform.sale.create-custom') }}" class="btn btn-link">Create</a>
    </div>
@stop

@push('head')
	<style>
		.toolbar .btn {
			display: inline-block !important;
		}
	</style>
@endpush

@section('content')

<div class="mb-3" style="">
	<div class="row">
		<div class="col-sm-5 align-items-center">
			<form class="" action="{{ route('platform.sale.search-custom') }}" method="POST">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="row">
					<div class="col-8">
						<input type="text" name="search" class="form-control" placeholder="Search by product code">
					</div>
					<div class="col-4">
						<input type="submit" class="btn btn-info btn-sm" value="Search" style="margin:1px 0;padding:5px 7px;">{{-- {{ auth()->user()->name }} --}}
					</div>
				</div>

			</form>

		</div>
		<div class="col"></div>
		<div class="col-sm-4" >
			<div class="form-group" id="sale_filter"><input type="text" class="form-control" placeholder="Filter..."></div>
		</div>
	</div>
</div>

<div class="bg-white rounded shadow-sm p-4 py-4">
		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<table id="receipt_bill" class="table table-responsive">
					<thead>
						<tr>
							<th>Invoice No.</th>
							<th>Customer</th>
							<th>Date</th>
							<th class="text-center">Grand Total</th>
							<th class="text-center">Items</th>
							<th class="text-center" style="width:10% !important;">Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach($sales as $sale)
							<tr>
								<td>{{ $sale->invoice_no }}</td>
								<td>{{ $sale->customer->name }}</td>
								<td>{{ $sale->date }}</td>
								<td class="text-center">{{ $sale->grand_total }}</td>
								<td class="text-center">{{ count($sale->saleitems) }}</td>
								<td class="text-center">
									<div>
								      <div class="form-group mb-0">
											<button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
								            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="1em" height="1em" viewBox="0 0 32 32" class="1" role="img" fill="currentColor" componentname="orchid-icon">
								   				<path d="M12.15 28.012v-0.85c0.019-0.069 0.050-0.131 0.063-0.2 0.275-1.788 1.762-3.2 3.506-3.319 1.95-0.137 3.6 0.975 4.137 2.787 0.069 0.238 0.119 0.488 0.181 0.731v0.85c-0.019 0.056-0.050 0.106-0.056 0.169-0.269 1.65-1.456 2.906-3.081 3.262-0.125 0.025-0.25 0.063-0.375 0.094h-0.85c-0.056-0.019-0.113-0.050-0.169-0.056-1.625-0.262-2.862-1.419-3.237-3.025-0.037-0.156-0.081-0.3-0.119-0.444zM20.038 3.988l-0 0.85c-0.019 0.069-0.050 0.131-0.056 0.2-0.281 1.8-1.775 3.206-3.538 3.319-1.944 0.125-3.588-1-4.119-2.819-0.069-0.231-0.119-0.469-0.175-0.7v-0.85c0.019-0.056 0.050-0.106 0.063-0.162 0.3-1.625 1.244-2.688 2.819-3.194 0.206-0.069 0.425-0.106 0.637-0.162h0.85c0.056 0.019 0.113 0.050 0.169 0.056 1.631 0.269 2.863 1.419 3.238 3.025 0.038 0.15 0.075 0.294 0.113 0.437zM20.037 15.575v0.85c-0.019 0.069-0.050 0.131-0.063 0.2-0.281 1.794-1.831 3.238-3.581 3.313-1.969 0.087-3.637-1.1-4.106-2.931-0.050-0.194-0.094-0.387-0.137-0.581v-0.85c0.019-0.069 0.050-0.131 0.063-0.2 0.275-1.794 1.831-3.238 3.581-3.319 1.969-0.094 3.637 1.1 4.106 2.931 0.050 0.2 0.094 0.394 0.137 0.588z"></path>
												</svg>
									      </button>
											<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow bg-white" x-placement="bottom-end">
								            <div class="form-group mb-0">
													<a data-turbo="true" class="btn btn-info" href="{{ route('platform.sale.edit-custom', $sale->id) }}">
								                  <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor" componentname="orchid-icon">
								   						<path d="M30.133 1.552c-1.090-1.044-2.291-1.573-3.574-1.573-2.006 0-3.47 1.296-3.87 1.693-0.564 0.558-19.786 19.788-19.786 19.788-0.126 0.126-0.217 0.284-0.264 0.456-0.433 1.602-2.605 8.71-2.627 8.782-0.112 0.364-0.012 0.761 0.256 1.029 0.193 0.192 0.45 0.295 0.713 0.295 0.104 0 0.208-0.016 0.31-0.049 0.073-0.024 7.41-2.395 8.618-2.756 0.159-0.048 0.305-0.134 0.423-0.251 0.763-0.754 18.691-18.483 19.881-19.712 1.231-1.268 1.843-2.59 1.819-3.925-0.025-1.319-0.664-2.589-1.901-3.776zM22.37 4.87c0.509 0.123 1.711 0.527 2.938 1.765 1.24 1.251 1.575 2.681 1.638 3.007-3.932 3.912-12.983 12.867-16.551 16.396-0.329-0.767-0.862-1.692-1.719-2.555-1.046-1.054-2.111-1.649-2.932-1.984 3.531-3.532 12.753-12.757 16.625-16.628zM4.387 23.186c0.55 0.146 1.691 0.57 2.854 1.742 0.896 0.904 1.319 1.9 1.509 2.508-1.39 0.447-4.434 1.497-6.367 2.121 0.573-1.886 1.541-4.822 2.004-6.371zM28.763 7.824c-0.041 0.042-0.109 0.11-0.19 0.192-0.316-0.814-0.87-1.86-1.831-2.828-0.981-0.989-1.976-1.572-2.773-1.917 0.068-0.067 0.12-0.12 0.141-0.14 0.114-0.113 1.153-1.106 2.447-1.106 0.745 0 1.477 0.34 2.175 1.010 0.828 0.795 1.256 1.579 1.27 2.331 0.014 0.768-0.404 1.595-1.24 2.458z"></path>
														</svg>
														Edit
								   				</a>
								   			</div>
												<div class="form-group mb-0">
													<form action="{{ route('platform.sale.delete-custom') }}" method="POST">
														<input type="hidden" name="_token" value="{{ csrf_token() }}">
														<input type="hidden" name="id" value="{{ $sale->id }}">
														<button class="btn btn-danger" type="submit" onclick="return confirm('Are you sure?')">
															<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="1em" height="1em" viewBox="0 0 32 32" class="me-2" role="img" fill="currentColor" componentname="orchid-icon">
									   						<path d="M28.025 4.97l-7.040 0v-2.727c0-1.266-1.032-2.265-2.298-2.265h-5.375c-1.267 0-2.297 0.999-2.297 2.265v2.727h-7.040c-0.552 0-1 0.448-1 1s0.448 1 1 1h1.375l2.32 23.122c0.097 1.082 1.019 1.931 2.098 1.931h12.462c1.079 0 2-0.849 2.096-1.921l2.322-23.133h1.375c0.552 0 1-0.448 1-1s-0.448-1-1-1zM13.015 2.243c0-0.163 0.133-0.297 0.297-0.297h5.374c0.164 0 0.298 0.133 0.298 0.297v2.727h-5.97zM22.337 29.913c-0.005 0.055-0.070 0.11-0.105 0.11h-12.463c-0.035 0-0.101-0.055-0.107-0.12l-2.301-22.933h17.279z"></path>
															</svg>
															Delete
									   				</button>
													</form>

												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endforeach

					</tbody>
					<tfoot>

					</tfoot>
				</table>
			</div>
		</div>
	</form>
</div>

@stop

@push('scripts')
	<script src="{{ asset('custom/js/jquery.livefilter.js') }}"></script>
	<script>
        $(function() {
            $("#sale_filter input").liveFilterOf("tbody tr");
        });
    </script>
@endpush