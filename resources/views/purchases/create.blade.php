@extends('platform::dashboard')

@section('title','Create Purchase Invoice')
@section('description', '')

@section('navbar')
    <div class="text-center">
    </div>
@stop

@push('head')

	<style>
		.select2 {
			max-width: 100% !important;
		}
	</style>
@endpush

@section('content')

<div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
	<form action="{{ route('platform.purchase.store-custom') }}" method="POST">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="row justify-content-center invoice-form">
			<div class="col-sm-2">
				<div class="form-group">
					<label for="invoice_code">Invoice Code</label>
					<input type="text" name="invoice_code" class="form-control">
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<label for="is_inv_auto">Inv system</label>
					<select class="form-control" name="is_inv_auto" id="is_inv_auto" required>
						<option value="1">Auto</option>
						<option value="0">Manual</option>
					</select>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="form-group">
					<label for="user_id">Admin or Branch</label>
					<select class="form-control user-select2" name="user_id" multiple required>
						@if(auth()->user()->id == 1)
							@foreach ($users as $user)
								<option value="{{ $user->id }}">{{ $user->name }}</option>
							@endforeach
						@else
							<option value="{{ auth()->user()->id }}" selected='true'>{{ auth()->user()->name }}</option>
						@endif
					</select>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="date">Date</label>
					<input type="date" name="date" class="form-control" required>
				</div>
			</div>
			<div class="col-sm-5">
				<div class="form-group">
					<label for="supplier_id">Supplier</label>
					<select class="form-control supplier-select2" name="supplier_id" required multiple >
						@foreach ($suppliers as $supplier)
							<option value="{{ $supplier->name }}">{{ $supplier->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-7">
				<div class="form-group">
					<label for="address">Address</label>
					<input type="text" name="address" class="form-control">
				</div>
			</div>

		</div>

		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12" style="">
				<div class="table-responsive">
					<table class="table table-responsive">
						<thead>
							<tr>
								<th>Products</th>
								<th>Price</th>
								<th>Qty</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td width="60%">
									<select name="product" id="product" class="product-select2 form-control" multiple>
										@foreach($products as $product)
										    <option id="{{ $product->id }}" value="{{ $product->id }}">
												{{ $product->code . '_' . $product->name }}
											</option>
										@endforeach
									</select>
								</td>
								<td width="15%">
									<input type="number" id="price" name="price" min="0" value="0" class="form-control">
								</td>
								<td width="15%">
									<input type="number" id="qty" name="qty" min="0" value="0" class="form-control">
								</td>
								<td width="10%">
									<div class="toolbar">
										<button type="submit" class="btn btn-primary ">Save</button>
									</div>
								</td>
							</tr>
						</tbody>
						<tfoot></tfoot>
					</table>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<label for="discount">Discount</label>
					<input type="number" id="discount" name="discount" class="form-control" min="0" value="0">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="received">Received</label>
					<input type="number" id="received" name="received" class="form-control" min="0" value="0">
				</div>
			</div>
			{{-- <div class="col-sm-6">
				<div class="form-group">
					<label for="remarks">Remarks</label>
					<input type="text" name="remarks" class="form-control">
				</div>
			</div> --}}
		</div>
	</form>
</div>

@stop

@push('scripts')
	<script type="text/javascript">
		// activate select2 plugin
		$(document).ready(function() {
		    $('.user-select2').select2({
		    	placeholder: 'Select User',
            theme: "bootstrap"
		    });
		});
		$(document).ready(function() {
		    $('.supplier-select2').select2({
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
	    $(document).ready(function(){

	      //add to cart
	      var count = 0;

	      $('#add').on('click',function(){

	         var name = $('#product').val();
	         var p_id = $('#product').find(':selected')[0].id;
	         var qty = $('#qty').val();
	         var price = $('#price').text();
	         var discount = $('#discount').val();

	         if(qty == 0)
	         {
	            var erroMsg =  '<span class="alert alert-danger ml-5">Minimum Qty should be 1 or More than 1</span>';
	            $('#errorMsg').html(erroMsg).fadeOut(9000);
	         }
	         else
	         {
	            billFunction(); // Below Function passing here
	         }

	         function billFunction()
	           {
	           var total = 0;
	           var iteration = count+1;
	           $("#receipt_bill").each(function () {
	           var total =  price*qty;
	           var subTotal = 0;
	           subTotal += parseInt(total);

	           var table =   '<tr id="'+ iteration +'"><td>'+ iteration +'</td><td>'+ name + '<input type="hidden" name="products['+count+'][product_id]" value="'+p_id+'"></td><td class="text-center">' + qty + '<input type="hidden" name="products['+count+'][qty]" value="'+qty+'"></td><td class="text-center">' + price + '</td><td class="text-center"><strong><input type="hidden" id="total" value="'+total+'">' +total+ '</strong></td></tr>';
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
	              document.getElementById('price').innerHTML = '0';
	              document.getElementById('qty').value = '0';
	          });
	          count++;
	          $("#product").val(0).trigger("change");
	         }
	        });
	      });

	 </script>
@endpush
