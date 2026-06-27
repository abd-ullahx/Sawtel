@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Profit & Loss Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">

				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.profit_and_loss') }}" autocomplete="off">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Report Type') }}</label>
									<select class="form-control auto-select" data-selected="{{ isset($report_type) ? $report_type : old('report_type', 'paid_unpaid') }}" name="report_type" required>
										<option value="paid_unpaid">{{ _lang('Paid & Unpaid') }}</option>
										<option value="paid">{{ _lang('Only Paid') }}</option>										
									</select>
								</div>
							</div>

							<div class="col-xl-2 col-lg-4">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter mr-1"></i>{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp
				@php $total_income = 0; @endphp
				@php $total_expense = 0; @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Profit & Loss Report') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>
					
					<div class="row">
						<div class="col-md-6 pr-md-0">
							<table class="table">
								<thead>
									<th>{{ _lang('Income') }}</th>
									<th class="text-right">{{ _lang('Amount') }}</th>
								</thead>
								<tbody>
								@if(isset($report_data))
									@php $date_format = get_date_format(); @endphp
									@foreach($invoices as $invoice)
										<tr>
											<td>{{ $invoice['category'] }} ({{ _lang('Invoice') }})</td>
											<td class="text-right">{{ formatAmount($invoice['amount'], currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_income += $invoice['amount']; @endphp
									@endforeach
									@if($sales_discount > 0)
										<tr>
											<td>{{ _lang('Sales Discount') }}</td>
											<td class="text-right">-{{ formatAmount($sales_discount, currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_income -= $sales_discount; @endphp
									@endif
									@foreach($othersIncome as $otherIncome)
										<tr>
											<td>{{ $otherIncome->category->name }}</td>
											<td class="text-right">{{ formatAmount($otherIncome->amount, currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_income += $otherIncome->amount; @endphp
									@endforeach
								@endif
								</tbody>
							</table>
						</div>

						<div class="col-md-6 pl-md-0">
							<table class="table">
								<thead>
									<th>{{ _lang('Expenses') }}</th>
									<th class="text-right">{{ _lang('Amount') }}</th>
								</thead>
								<tbody>
								@if(isset($report_data))
									@php $date_format = get_date_format(); @endphp
									@foreach($purchases as $purchase)
										<tr>
											<td>{{ $purchase['category'] }} ({{ _lang('Purchase') }})</td>
											<td class="text-right">{{ formatAmount($purchase['amount'], currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_expense += $purchase['amount']; @endphp
									@endforeach
									@if($purchase_discount > 0)
										<tr>
											<td>{{ _lang('Purchase Discount') }}</td>
											<td class="text-right">-{{ formatAmount($purchase_discount, currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_expense -= $purchase_discount; @endphp
									@endif
									@foreach($othersExpense as $otherExpense)
										<tr>
											<td>{{ $otherExpense->category->name }}</td>
											<td class="text-right">{{ formatAmount($otherExpense->amount, currency_symbol($currency)) }}</td>							
										</tr>
										@php $total_expense += $otherExpense->amount; @endphp
									@endforeach
								@endif
								</tbody>
							</table>
						</div>
					</div>

					@if(isset($report_data))
					<div id="report-summary">
						<div class="row mt-3">
							<div class="col-md-4">
								<div class="border">
									<div class="p-4 text-success">
										<h5>{{ _lang('Income') }}</h5>
										<h5 class="mt-2"><b>{{ formatAmount($total_income, currency_symbol($currency)) }}</b></h5>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="border">
									<div class="p-4 text-danger">
										<h5>{{ _lang('Expense') }}</h5>
										<h5 class="mt-2"><b>{{ formatAmount($total_expense, currency_symbol($currency)) }}</b></h5>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="border">
									<div class="p-4 {{ $total_income >= $total_expense ? 'text-success' : 'text-danger' }}">
										<h5>{{ _lang('Net Profit') }}</h5>
										<h5 class="mt-2"><b>{{ formatAmount($total_income - $total_expense, currency_symbol($currency)) }}</b></h5>
									</div>
								</div>
							</div>
						</div>
					</div>
					@endif

				</div>
			</div>
		</div>
	</div>
</div>
@endsection