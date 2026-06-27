<div class="row px-2">
	<div class="col-md-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Date') }}</td><td>{{ $transaction->trans_date }}</td></tr>
			<tr>
				<td>{{ _lang('Category') }}</td>
				@if ($transaction->ref_id != null && $transaction->ref_type == 'invoice')
					<td>{{ _lang('Invoice Payment') . ' #' . $transaction->invoice->invoice_number }}</td>
				@elseif($transaction->ref_id != null && $transaction->ref_type == 'purchase')
					<td>{{ _lang('Purchase / Bill') . ' #' . $transaction->purchase->bill_no }}</td>
				@elseif ($transaction->ref_id == null && $transaction->transaction_category_id == null)
					<td>{{ _lang('Uncategorized') }}</td>
				@else
					<td>{{ $transaction->category->name }}</td>
				@endif
			</tr>
			<tr><td>{{ _lang('Account') }}</td><td>{{ $transaction->account->account_name }}</td></tr>
			<tr><td>{{ _lang('Method') }}</td><td>{{ $transaction->method }}</td></tr>
			<tr><td>{{ _lang('Type') }}</td><td>{{ ucwords($transaction->type) }}</td></tr>
			<tr><td>{{ _lang('Amount') }}</td><td>{{ formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) }}</td></tr>
			<tr><td>{{ _lang('Reference') }}</td><td>{{ $transaction->reference }}</td></tr>
			<tr><td>{{ _lang('Description') }}</td><td>{{ $transaction->description }}</td></tr>
			<tr>
				<td>{{ _lang('Attachment') }}</td>
				<td>
					@if($transaction->attachment != '')
					<a href="{{ asset('public/uploads/media/'.$transaction->attachment) }}" target="_blank">{{ $transaction->attachment }}</a>
					@endif
				</td>
			</tr>
			<tr><td>{{ _lang('Created By') }}</td><td>{{ $transaction->created_by->name }} ({{ $transaction->created_at }})</td></tr>
			<tr><td>{{ _lang('Updated By') }}</td><td>{{ $transaction->updated_by->name }} ({{ $transaction->updated_at }})</td></tr>
		</table>
	</div>
</div>

