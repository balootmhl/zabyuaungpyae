@extends('platform::dashboard')

@section('title','Create Sale Invoice')
@section('description', '')

@section('navbar')
    {{-- <div class="text-center">
        Navbar
    </div> --}}
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
	<form action="{{ route('platform.sale.store-custom') }}" method="POST">
		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		<div class="row justify-content-center invoice-form">
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[code]">Enter Invoice Code</label>
					<input type="text" name="invoice_code" class="form-control">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[user_id]">Select Admin or Branch</label>
					<select class="form-control hyper-select" name="user_id" multiple>
						@foreach ($users as $user)
							<option value="{{ $user->id }}">{{ $user->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[customer_id]">Select Customer</label>
					<select class="form-control hyper-select" name="customer_id" multiple>
						@foreach ($customers as $customer)
							<option value="{{ $customer->id }}">{{ $customer->name }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[date]">Select Date</label>
					<input type="date" name="date" class="form-control">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[customer_name]">Customer Name</label>
					<input type="text" name="customer_name" class="form-control">
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label for="sale[address]">Address</label>
					<input type="text" name="address" class="form-control">
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label for="sale[customer_id]">Select Price</label>
					<select class="form-control" name="is_saleprice">
						<option value="1">Sale Price</option>
						<option value="0">Buy Price</option>
					</select>
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Continue">
				</div>
			</div>
		</div>

		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<table class="table table-responsive">
					<thead></thead>
					<tbody>
						<tr>
							<td width="70%">
								<div class="form-group">
									<label for="product">Select Product</label>
									<select name="product" id="product" class="hyper-select form-control" multiple>
										@foreach($products as $product)
										    <option id="{{ $product->id }}" value="{{ $product->code . ' [' . $product->name . '] ' }}"></option>
										@endforeach
									</select>
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for="qty">Select Product</label>
									<input type="number" id="qty" min="0" value="0" class="form-control">
								</div>
							</td>
							<td>
								<div class="form-group">
									<label for="" style="visibility: hidden;">Select Product</label>
									<button id="add" class="btn btn-primary">Add</button>
								</div>
							</td>
						</tr>
					</tbody>
					<tfoot></tfoot>
				</table>
			</div>
		</div>

		<div class="row justify-content-center invoice-form">
			<div class="col-sm-12">
				<table id="receipt_bill" class="table table-responsive">
					<thead>
						<tr>
							<th>No.</th>
							<th>Product</th>
							<th class="text-center">Quantity</th>
							<th class="text-center">Price</th>
							<th class="text-center">Unit Total</th>
						</tr>
					</thead>
					<tbody id="new">

					</tbody>
					<tfoot>
						<tr>
							<td> </td>
							<td> </td>
							<td> </td>
							<td class="text-right text-dark">
								<h5><strong>Subtotal: Ks </strong></h5>
								<p><strong>Tax : Ks </strong></p>
							</td>
							<td class="text-center text-dark" >
                              <h5> <strong><span id="subTotal"></strong></h5>
                              <h5> <strong><span id="taxAmount"></strong></h5>
                           </td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</form>
</div>

@stop

@push('scripts')
	<script type="text/javascript">
		// activate select2 plugin
		$(document).ready(function() {
		    $('.hyper-select').select2();
		});
	</script>
	<script>
	    $(document).ready(function(){
	      $('#product').change(function() {
	       var ids =   $(this).find(':selected')[0].id;
	        $.ajax({
	           type:'GET',
	           url:'getPrice/{id}',
	           data:{id:ids},
	           dataType:'json',
	           success:function(data)
	             {

	                 $.each(data, function(key, resp)
	                 {
	                  $('#price').text(resp.sale_price);
	                });
	             }
	        });
	      });

	      //add to cart
	      var count = 1;
	      $('#add').on('click',function(){

	         var name = $('#product').val();
	         var qty = $('#qty').val();
	         var price = $('#price').text();

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

	           $("#receipt_bill").each(function () {
	           var total =  price*qty;
	           var subTotal = 0;
	           subTotal += parseInt(total);

	           var table =   '<tr><td>'+ count +'</td><td>'+ name + '</td><td>' + qty + '</td><td>' + price + '</td><td><strong><input type="hidden" id="total" value="'+total+'">' +total+ '</strong></td></tr>';
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

	             // Code for calculate tax of Subtoal 5% Tax Applied
	               var Tax = (total * 5) / 100;
	               $('#taxAmount').text(Tax.toFixed(2));

	              // Code for Total Payment Amount

	              var Subtotal = $('#subTotal').text();
	              var taxAmount = $('#taxAmount').text();

	              var totalPayment = parseFloat(Subtotal) + parseFloat(taxAmount);
	              $('#totalPayment').text(totalPayment.toFixed(2)); // Showing using ID

	          });
	          count++;
	         }
	        });
	            // Code for year

	            var currentdate = new Date();
	              var datetime = currentdate.getDate() + "/"
	                 + (currentdate.getMonth()+1)  + "/"
	                 + currentdate.getFullYear();
	                 $('#year').text(datetime);



	            // Code for extract Weekday
	                 function myFunction()
	                  {
	                     var d = new Date();
	                     var weekday = new Array(7);
	                     weekday[0] = "Sunday";
	                     weekday[1] = "Monday";
	                     weekday[2] = "Tuesday";
	                     weekday[3] = "Wednesday";
	                     weekday[4] = "Thursday";
	                     weekday[5] = "Friday";
	                     weekday[6] = "Saturday";

	                     var day = weekday[d.getDay()];
	                     return day;
	                     }
	                 var day = myFunction();
	                 $('#day').text(day);
	      });
	 </script>
	 <script>
	    window.onload = displayClock();

	     function displayClock(){
	       var time = new Date().toLocaleTimeString();
	       document.getElementById("time").innerHTML = time;
	        setTimeout(displayClock, 1000);
	     }
	</script>
@endpush
