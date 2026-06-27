<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('transaction_methods.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="alert alert-warning">
				<strong><i class="fas fa-info-circle mr-2"></i>{{ _lang('Update will be not applied in existing transactions') }}</strong>
			</div>
		</div>
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Name') }}</label>						
				<input type="text" class="form-control" name="name" value="{{ $transactionmethod->name }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Status') }}</label>						
				<select class="form-control auto-select" data-selected="{{ $transactionmethod->status }}" name="status"  required>
					<option value="1">{{ _lang('Active') }}</option>
					<option value="0">{{ _lang('Disabled') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-12 mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-1"></i>{{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

