<div class="row px-2">
	<div class="col-md-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Account Name') }}</td><td>{{ $account->account_name }}</td></tr>
			<tr><td>{{ _lang('Opening Date') }}</td><td>{{ $account->opening_date }}</td></tr>
			<tr><td>{{ _lang('Account Number') }}</td><td>{{ $account->account_number }}</td></tr>
			<tr><td>{{ _lang('Currency') }}</td><td>{{ $account->currency }}</td></tr>
			<tr><td>{{ _lang('Current Balance') }}</td><td>{{ formatAmount(get_account_balance($account->id), currency_symbol($account->currency)) }}</td></tr>
			<tr><td>{{ _lang('Description') }}</td><td>{{ $account->description }}</td></tr>
		</table>
	</div>
</div>

