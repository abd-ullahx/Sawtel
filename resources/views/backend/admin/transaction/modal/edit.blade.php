<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('transactions.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Date') }}</label>						
				<input type="text" class="form-control datetimepicker" name="trans_date" value="{{ $transaction->getRawOriginal('trans_date') }}" required>
			</div>
		</div>

		@if($transaction->type == 'income' && $transaction->ref_id == null)		
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Income Category') }}</label>						
				<select class="form-control auto-select select2-ajax" data-selected="{{ $transaction->transaction_category_id }}" name="transaction_category_id"
				data-table="transaction_categories" data-value="id" data-display="name" data-where="4" data-title="{{ _lang('New Category') }}" 
				data-href="{{ route('transaction_categories.create') }}?type=income" required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\TransactionCategory::income()->get() as $category)
					<option value="{{ $category->id }}">{{ $category->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		@elseif($transaction->type == 'expense' && $transaction->ref_id == null)
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Expense Category') }}</label>						
				<select class="form-control auto-select select2-ajax" data-selected="{{ $transaction->transaction_category_id }}" name="transaction_category_id"
				data-table="transaction_categories" data-value="id" data-display="name" data-where="5" data-title="{{ _lang('New Category') }}" 
				data-href="{{ route('transaction_categories.create') }}?type=expense" required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\TransactionCategory::expense()->get() as $category)
					<option value="{{ $category->id }}">{{ $category->name }}</option>
					@endforeach
				</select>
			</div>
		</div>
		@endif

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Account') }}</label>	
				<select class="form-control auto-select select2-ajax" data-selected="{{ $transaction->account_id }}" name="account_id"
				data-table="accounts" data-value="id" data-display="account_name" data-divider=" - " data-display2="currency" data-where="3" data-title="{{ _lang('Add New Account') }}" 
				data-href="{{ route('accounts.create') }}" required>
					@foreach(\App\Models\Account::where('id', $transaction->account_id)->get() as $account)
					<option value="{{ $account->id }}">{{ $account->account_name }} - {{ $account->currency }}</option>
					@endforeach
				</select>
			</div>
		</div>


		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>						
				<select class="form-control auto-select select2-ajax" data-selected="{{ $transaction->method }}" name="method"
				data-table="transaction_methods" data-value="name" data-display="name" data-where="8" data-title="{{ _lang('New Method') }}" 
				data-href="{{ route('transaction_methods.create') }}" required>
					<option value="{{ $transaction->method }}">{{ $transaction->method }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount') }}</label>	
				<input type="text" class="form-control float-field" name="amount" value="{{ $transaction->amount }}" aria-describedby="amount-addon" required>	
			</div>
		</div>

		<div class="{{ $transaction->ref_id == null ? 'col-md-6' : 'col-md-12' }}">
			<div class="form-group">
				<label class="control-label">{{ _lang('Reference') }}</label>						
				<input type="text" class="form-control" name="reference" value="{{ $transaction->reference }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Description') }}</label>						
				<textarea class="form-control" name="description">{{ $transaction->description }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Attachment') }}</label>						
				<input type="file" class="dropify" name="attachment">
			</div>
		</div>
	
		<div class="form-group">
		    <div class="col-md-12">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i>{{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

