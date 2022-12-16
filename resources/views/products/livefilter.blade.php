@push('head')
	<style>
		.toolbar input {
			display: inline-block !important;
		}
		#fffilter input {
			transition: .3s;
		}

		#fffilter input:focus {
			background-color: #fff !important;
			border: 1px solid #ccc;
		}
	</style>
@endpush

<div class="mb-3" style="">
	<div class="row">
		<div class="col">
			<form action="" class="form form-inline">
				<div class="form-group"><input type="text" class="form-control" placeholder="Search Products"> <input type="submit"></div>
			</form>
		</div>
		<div class="col"></div>
		<div class="col-sm-4" >
			<div class="form-group" id="fffilter"><input type="text" class="form-control" placeholder="Filter..."></div>
		</div>
	</div>
</div>


@push('scripts')
	<script src="{{ asset('custom/js/jquery.livefilter.js') }}"></script>
	<script>
        $(function() {
            $("#fffilter input").liveFilterOf("tbody tr");
        });
    </script>
@endpush
