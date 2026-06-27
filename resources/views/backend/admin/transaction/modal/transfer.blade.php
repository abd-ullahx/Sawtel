<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('transactions.transfer') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="row px-2">
	    <div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Date') }}</label>						
				<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ old('trans_date', now()) }}" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Debit Account') }}</label>	
				<select class="form-control auto-select select2-ajax" data-selected="{{ old('debit_account') }}" name="debit_account"
				data-table="accounts" data-value="id" data-display="account_name" data-divider=" - " data-display2="currency" data-where="3" data-title="{{ _lang('Add New Account') }}" 
				data-href="{{ route('accounts.create') }}" required>
					<option value="">{{ _lang('Select One') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Credit Account') }}</label>	
				<select class="form-control auto-select select2-ajax" data-selected="{{ old('credit_account') }}" name="credit_account"
				data-table="accounts" data-value="id" data-display="account_name" data-divider=" - " data-display2="currency" data-where="3" data-title="{{ _lang('Add New Account') }}" 
				data-href="{{ route('accounts.create') }}" required>
					<option value="">{{ _lang('Select One') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount') }}</label>	
				<input type="text" class="form-control float-field" name="amount" value="{{ old('amount') }}" aria-describedby="amount-addon" required>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Reference') }}</label>						
				<input type="text" class="form-control" name="reference" value="{{ old('reference') }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>						
				<textarea class="form-control" name="description">{{ old('description') }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Attachment') }}</label></br>						
				<input type="file" class="dropify" name="attachment">
			</div>
		</div>
	
		<div class="col-md-12 mt-2">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Submit') }}</button>
		    </div>
		</div>
	</div>
</form>
