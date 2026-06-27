<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('accounts.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Account Name') }}</label>						
				<input type="text" class="form-control" name="account_name" value="{{ $account->account_name }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Opening Date') }}</label>						
				<input type="text" class="form-control datepicker" name="opening_date" value="{{ $account->getRawOriginal('opening_date') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Account Number') }}</label>						
				<input type="text" class="form-control" name="account_number" value="{{ $account->account_number }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Currency') }}</label>						
				<select class="form-control auto-select select2" data-selected="{{ $account->currency }}" name="currency" disabled>
					<option value="">{{ _lang('Select One') }}</option>
					{!! xss_clean(get_currency_list()) !!}
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>						
				<textarea class="form-control" name="description">{{ $account->description }}</textarea>
			</div>
		</div>
	
		<div class="col-md-12 mt-2">
			<div class="form-group">    
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

