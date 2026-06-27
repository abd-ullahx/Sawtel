<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('product_units.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="form-group row px-3">
		<label class="col-xl-3 col-form-label">{{ _lang('Unit Name') }}</label>
		<div class="col-xl-9">
			<input type="text" class="form-control" name="unit" value="{{ old('unit') }}" required>
		</div>
	</div>

	<div class="form-group row px-3 mt-2">
		<div class="col-xl-9 offset-xl-3">
			<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Create Unit') }}</button>
		</div>
	</div>
</form>
