<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('accounts.store') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
	    <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Account Name') }}</label>						
				<input type="text" class="form-control" name="account_name" value="{{ old('account_name') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Opening Date') }}</label>						
				<input type="text" class="form-control datepicker" name="opening_date" value="{{ old('opening_date') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Account Number') }}</label>						
				<input type="text" class="form-control" name="account_number" value="{{ old('account_number') }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Currency') }}</label>						
				<select class="form-control auto-select select2" data-selected="{{ old('currency') }}" name="currency"  required>
					<option value="">{{ _lang('Select One') }}</option>
					{!! xss_clean(get_currency_list()) !!}
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Opening Balance') }}</label>						
				<input type="text" class="form-control" name="opening_balance" value="{{ old('opening_balance', 0) }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>						
				<textarea class="form-control" name="description">{{ old('description') }}</textarea>
			</div>
		</div>
	
		<div class="col-md-12 mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Save Changes') }}</button>
		    </div>
		</div>
	</div>
</form>
