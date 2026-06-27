@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Income By Customers') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>
			<div class="card-body">
				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.income_by_customer') }}">
						<div class="row">
              				{{ csrf_field() }}

							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Start Date') }}</label>
									<input type="text" class="form-control datepicker" name="date1" id="date1" value="{{ isset($date1) ? $date1 : old('date1', \Carbon\Carbon::now()->startOfMonth()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('End Date') }}</label>
									<input type="text" class="form-control datepicker" name="date2" id="date2" value="{{ isset($date2) ? $date2 : old('date2', \Carbon\Carbon::now()) }}" readOnly="true" required>
								</div>
							</div>

							<div class="col-lg-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Customer') }}</label>
									<select class="form-control select2 auto-select" data-selected="{{ isset($customer_id) ? $customer_id : old('customer_id') }}" name="customer_id">
										<option value="">{{ _lang('All Customer') }}</option>
										@foreach(\App\Models\Customer::all() as $customer)
											<option value="{{ $customer->id }}">{{ $customer->name }}</option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="col-lg-3">
								<button type="submit" class="btn btn-light btn-xs btn-block mt-26"><i class="ti-filter"></i>&nbsp;{{ _lang('Filter') }}</button>
							</div>
						</form>

					</div>
				</div><!--End Report param-->

				@php $date_format = get_date_format(); @endphp

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Income By Customer') }}</p>
						<p>{{ isset($date1) ? date($date_format, strtotime($date1)).' '._lang('to').' '.date($date_format, strtotime($date2)) : '----------  '._lang('to').'  ----------' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Customer') }}</th>
								<th class="text-right">{{ _lang('Income Amount') }}</th>
								<th class="text-right">{{ _lang('Paid Amount') }}</th>
								<th class="text-right">{{ _lang('Due Amount') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@foreach($report_data as $invoice)
									<tr>
										<td>{{ $invoice->customer->name }}</td>
										<td class="text-right">{{ formatAmount($invoice->total_income, currency_symbol($currency)) }}</td>
										<td class="text-right">{{ formatAmount($invoice->total_paid, currency_symbol($currency)) }}</td>
										<td class="text-right">{{ formatAmount($invoice->total_income - $invoice->total_paid, currency_symbol($currency)) }}</td>
									</tr>
								@endforeach
								<tr>
									<td><b>{{ _lang('Total') }}</b></td>
									<td class="text-right font-weight-bold">{{ formatAmount($report_data->sum('total_income'), currency_symbol($currency)) }}</td>
									<td class="text-right font-weight-bold">{{ formatAmount($report_data->sum('total_paid'), currency_symbol($currency)) }}</td>
									<td class="text-right font-weight-bold">{{ formatAmount($report_data->sum('total_income') - $report_data->sum('total_paid'), currency_symbol($currency)) }}</td>
								</tr>
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