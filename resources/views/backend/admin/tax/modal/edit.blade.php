<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('taxes.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ $tax->name }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Tax Rate') }} (%)</label>						
				<input type="text" class="form-control" name="rate" value="{{ $tax->rate }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Tax Number') }}</label>						
				<input type="text" class="form-control" name="tax_number" value="{{ $tax->tax_number }}">
			</div>
		</div>
	
		<div class="col-md-12 mt-2">
			<div class="form-group">    
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-1"></i>{{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

