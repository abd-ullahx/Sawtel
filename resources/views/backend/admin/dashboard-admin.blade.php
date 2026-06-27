@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 success-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Month Income') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($current_month_income->total,currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 danger-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Current Month Expense') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($current_month_expense->total, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 primary-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Due Invoice Amount') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($invoice->total_amount - $invoice->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-xl-3 col-md-6">
		<div class="card mb-4 warning-card dashboard-card">
			<div class="card-body">
				<div class="d-flex">
					<div class="flex-grow-1">
						<h5>{{ _lang('Due Purchase Amount') }}</h5>
						<h4 class="pt-1 mb-0"><b>{{ formatAmount($purchase->total_amount - $purchase->total_paid, currency_symbol(request()->activeBusiness->currency)) }}</b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-12 mb-4">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Cash Flow').' - '._lang('Year of').' '.date('Y')  }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="transactionAnalysis" style="height: 400px"></canvas>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-lg-4 col-md-6 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Income By Category').' - '._lang('Year of').' '.date('Y') }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="incomeOverview"></canvas>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-md-6 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Expense By Category').' - '._lang('Year of').' '.date('Y') }}</span>
			</div>
			<div class="card-body">
				<h5 class="text-center loading-chart"><i class="fas fa-spinner fa-spin"></i> {{ _lang('Loading Chart') }}</h5>
				<canvas id="expenseOverview"></canvas>
			</div>
		</div>
	</div>

	<div class="col-lg-4 col-md-12 mb-4">
		<div class="card h-100">
			<div class="card-header d-flex align-items-center">
				<span>{{ _lang('Account Balances') }}</span>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="w-100">
						<thead class="bg-light border">
							<th class="p-2 border-right">{{ _lang('Account Name') }}</th>
							<th class="p-2 text-right">{{ _lang('Balance') }}</th>
						</thead>
						<tbody>
							@if(isset($accounts))
							@foreach($accounts as $account)
								<tr class="border">
									<td class="p-2 border-right">{{ $account->account_name }} ({{ $account->currency }})</td>
									<td class='p-2 text-right'>{{ formatAmount($account->balance, currency_symbol($account->currency)) }}</td>											
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

<div class="row">
	<div class="col-lg-12">
		<div class="card mb-4">
			<div class="card-header">
				{{ _lang('Recent Transactions') }}
			</div>
			<div class="card-body">
				<table class="table data-table">
					<thead>
						<tr>
							<th>{{ _lang('Date') }}</th>
							<th>{{ _lang('Account') }}</th>
							<th>{{ _lang('Category') }}</th>
							<th>{{ _lang('Description') }}</th>
							<th class="text-right">{{ _lang('Amount') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($transactions as $transaction)
							<tr>
								@php
								$symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
								$class  = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
								@endphp
								
								<td>{{ $transaction->trans_date }}</td>
								<td>{{ $transaction->account->account_name }} ({{ $transaction->account->currency }})</td>
								<td>
									{{ $transaction->category->name }}
									@if($transaction->ref_id != null && $transaction->ref_type == 'invoice')
									<br><a href="{{ route('invoices.show', $transaction->ref_id) }}" target="_blank"><i class="far fa-eye mr-1"></i>{{ _lang('See Invoice') }}</a>
									@endif
									@if($transaction->ref_id != null && $transaction->ref_type == 'purchase')
									<br><a href="{{ route('purchases.show', $transaction->ref_id) }}" target="_blank"><i class="far fa-eye mr-1"></i>{{ _lang('See Invoice') }}</a>
									@endif
								</td>				
								<td>{{ $transaction->description }}</td>				
								<td class="text-right"><span class="{{ $class }}">{{ $symbol.' '.formatAmount($transaction->amount, currency_symbol($transaction->account->currency)) }}</span></td>
								<td class="text-center">
									<a class="btn btn-xs btn-outline-primary ajax-modal" data-title="{{ _lang('Transaction Details') }}" href="{{ route('transactions.show', $transaction['id']) }}"><i class="far fa-eye mr-1"></i>{{ _lang('Preview') }}</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection

@section('js-script')
<script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script>
<script src="{{ asset('public/backend/assets/js/dashboard.js?v=1.1') }}"></script>
@endsection