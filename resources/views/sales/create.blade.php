@push('head')

@endpush

<div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
	<div class="row">
		<div class="col-sm-3">
			<div class="form-group">
				<label for="sale[code]">Enter Invoice Code</label>
				<input type="text" name="sale[code]" class="form-control">
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label for="sale[user_id]">Select Admin or Branch</label>
				<select class="form-control hyper-select" name="sale[user_id]" multiple>
					@foreach ($users as $user)
						<option value="{{ $user->id }}">{{ $user->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label for="sale[customer_id]">Select Customer</label>
				<select class="form-control hyper-select" name="sale[customer_id]" multiple>
					@foreach ($customers as $customer)
						<option value="{{ $customer->id }}">{{ $customer->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="form-group">
				<label for="sale[date]">Select Date</label>
				<input type="date" name="sale[date]" class="form-control" value="YYYY/MM/DD">
			</div>
		</div>
	</div>		
</div>

@push('scripts')
	<script type="text/javascript">
		// activate select2 plugin
		$(document).ready(function() {
		    $('.hyper-select').select2();
		});
	</script>
@endpush