@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Payroll Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">

				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.payroll_report') }}" autocomplete="off">
						<div class="row">
              				@csrf
							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Month') }}</label>						
									<select type="text" class="form-control auto-select" name="month" data-selected="{{ isset($month) ? $month : old('month', date('m')) }}" required>
										@for($m = 1; $m <=12; $m++)
										<option value="{{ date('m', mktime(0, 0, 0, $m, 10)) }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
										@endfor
									</select>
								</div>
							</div>

							<div class="col-xl-3 col-lg-4">
								<div class="form-group">
									<label class="control-label">{{ _lang('Year') }}</label>						
									<select type="text" class="form-control auto-select" name="year" data-selected="{{ isset($year) ? $year : old('year', date('Y')) }}" required>
										@for($y = 2020; $y <=date('Y'); $y++)
										<option value="{{ $y }}">{{ $y }}</option>
										@endfor
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

				<div id="report">
					<div class="report-header">
						<h4>{{ request()->activeBusiness->name }}</h4>
						<p>{{ _lang('Payroll Report') }}</p>
						<p>{{ isset($month) && isset($year) ? date('F', mktime(0, 0, 0, $month, 10)) .', '. $year : '' }}</p>
					</div>

					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>{{ _lang('Employee ID') }}</th>
								<th>{{ _lang('Name') }}</th>
								<th>{{ _lang('Month') }}</th>
								<th>{{ _lang('Year') }}</th>
								<th class="text-center">{{ _lang('Status') }}</th>
								<th class="text-right">{{ _lang('Net Salary') }}</th>
							</thead>
							<tbody>
							@if(isset($report_data))
								@foreach($report_data as $payslip)
									<tr>
										<td>{{ $payslip->staff->employee_id }}</td>		
										<td>{{ $payslip->staff->name }}</td>		
										<td>{{ date('F', mktime(0, 0, 0, $payslip->month, 10)) }}</td>
										<td>{{ $payslip->year }}</td>
										<td class="text-center">{!! xss_clean(payroll_status($payslip->status)) !!}</td>
										<td class="text-right">{{ formatAmount($payslip->net_salary, currency_symbol($currency)) }}</td>
									</tr>
								@endforeach
								<tr>
									<td colspan="5"><b>{{ _lang('Total Amount') }}</b></td>		
									<td class="text-right">{{ formatAmount($report_data->sum('net_salary'), currency_symbol($currency)) }}</td>
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