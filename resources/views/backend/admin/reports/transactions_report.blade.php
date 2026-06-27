@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Transactions Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>
			<div class="card-body">
				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.transactions_report') }}">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-lg-2 col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-lg-2 col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-lg-2 col-md-6">
								<div class="form-group">
								<label class="control-label">{{ _lang('Type') }}</label>
									<select class="form-control auto-select" data-selected="{{ isset($transaction_type) ? $transaction_type : old('transaction_type') }}" name="transaction_type">
										<option value="">{{ _lang('All') }}</option>
										<option value="income">{{ _lang('Income') }}</option>
										<option value="expense">{{ _lang('Expense') }}</option>
									</select>
								</div>
							</div>

							<div class="col-lg-4 col-md-6">
								<div class="form-group">
									<label class="control-label">{{ _lang('Account') }}</label>
									<select class="form-control auto-select" data-selected="{{ isset($account_id) ? $account_id : old('account_id') }}" name="account_id">
										<option value="">{{ _lang('All Account') }}</option>
										@foreach(\App\Models\Account::all() as $acc)
											<option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->currency }})</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-lg-2 col-md-12">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter"></i>&nbsp;{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Transactions Report') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Account') }}</th>
								<th>{{ _lang('Category') }}</th>
								<th>{{ _lang('Type') }}</th>
								<th class="text-right">{{ _lang('Amount') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@foreach($report_data as $transaction)
									@php
									$symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
									$class  = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
									@endphp
									<tr>
										<td>{{ $transaction->trans_date }}</td>
										<td>{{ $transaction->account->account_name }} ({{ $transaction->account->currency }})</td>
										<td>{{ $transaction->category->name }}</td>				
										<td>{{ ucwords($transaction->type) }}</td>
										<td class="text-right"><span class="{{ $class }}">{{ $symbol.' '.formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) }}</span></td>
									</tr>
								@endforeach
							@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection