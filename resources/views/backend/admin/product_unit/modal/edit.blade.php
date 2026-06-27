<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('product_units.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="form-group row px-3">
		<label class="col-xl-3 col-form-label">{{ _lang('Unit Name') }}</label>
		<div class="col-xl-9">
			<input type="text" class="form-control" name="unit" value="{{ $productunit->unit }}" required>
		</div>
	</div>

	<div class="form-group row px-3 mt-2">
		<div class="col-xl-9 offset-xl-3">
			<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update Unit') }}</button>
		</div>
	</div>
</form>

