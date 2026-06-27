@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-8 offset-lg-2">
		<div class="card">
			<div class="card-header d-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Payslip') }}</span>
				<button class="btn btn-primary btn-xs print" type="button" data-print="payslip-report"><i class="fas fa-print"></i> {{ _lang('Print Payslip') }}</button>
			</div>
			<div class="card-body" id="payslip-report">
				<div class="text-center">
					<h4 class="mb-2">{{ _lang('Payslip') }}</h4>
					<h5>{{ request()->activeBusiness->name }}</h5>
					<p>{{ request()->activeBusiness->address }}</p>
				</div>

				<table class="w-100 payslip-table mt-4" border="1">
					<tr>
						<td>{{ _lang('Date of Joining') }}</td>
						<td>{{ $payroll->staff->joining_date }}</td>
						<td>{{ _lang('Employee ID') }}</td>
						<td>{{ $payroll->staff->employee_id }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Pay Period') }}</td>
						<td>{{ date('F', mktime(0, 0, 0, $payroll->month, 10)) }}, {{ $payroll->year }}</td>
						<td>{{ _lang('Employee Name') }}</td>
						<td>{{ $payroll->staff->name }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Working Days') }}</td>
						<td>{{ sprintf("%02d", $working_days) }}</td>
						<td>{{ _lang('Designation') }}</td>
						<td>{{ $payroll->staff->designation->name }}</td>
					</tr>
					<tr>
						<td>{{ _lang('Absent') }}</td>
						<td>{{ is_decimal($absence) ? $absence : sprintf("%02d", $absence) }}</td>
						<td>{{ _lang('Department') }}</td>
						<td>{{ $payroll->staff->department->name }}</td>
					</tr>
				</table>

			    <table class="payslip-table w-100 mt-4" border="1">
					<thead class="bg-light">
						<th class="text-dark">{{ _lang('Allowances') }}</th>
						<th class="text-dark text-right wp-150">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@php $total_allowances = 0; @endphp
						<tr><td>{{ _lang('Basic Salary') }}</td><td class="text-right">{{ formatAmount($payroll->current_salary, $currency_symbol) }}</td></tr>
						<tr><td>{{ _lang('Expense Claim') }}</td><td class="text-right">{{ formatAmount($payroll->expense, $currency_symbol) }}</td></tr>
						@foreach($payroll->payroll_benefits()->where('type','add')->get() as $allowances)
						<tr><td>{{ $allowances->name }}</td><td class="text-right">{{ formatAmount($allowances->amount, $currency_symbol) }}</td></tr>
						@php $total_allowances += $allowances->amount; @endphp
						@endforeach
						<tr><td><b>{{ _lang('Total Earnings') }}</b></td><td class="text-right"><b>{{ formatAmount($payroll->current_salary + $payroll->expense + $total_allowances, $currency_symbol) }}</b></td></tr>
					</tbody>
				</table>

				<table class="payslip-table w-100 mt-4" border="1">
					<thead class="bg-light">
						<th class="text-dark">{{ _lang('Deductions') }}</th>
						<th class="text-dark text-right wp-150">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@php $total_deducations = 0; @endphp
						<tr><td>{{ _lang('Absence Fine') }}</td><td class="text-right">{{ formatAmount($payroll->absence_fine, $currency_symbol) }}</td></tr>
						@foreach($payroll->payroll_benefits()->where('type','deduct')->get() as $deduction)
						<tr><td>{{ $deduction->name }}</td><td class="text-right">{{ formatAmount($deduction->amount, $currency_symbol) }}</td></tr>
						@php $total_deducations += $deduction->amount; @endphp
						@endforeach
						<tr><td><b>{{ _lang('Total Deductions') }}</b></td><td class="text-right text-danger"><b>{{ formatAmount($payroll->absence_fine + $total_deducations, $currency_symbol) }}</b></td></tr>
						<tr><td class="text-success"><b>{{ _lang('NET SALARY') }}</b></td><td class="text-right text-success"><b>{{ formatAmount($payroll->net_salary, $currency_symbol) }}</b></td></tr>
					</tbody>
				</table>
			</div>
	    </div>
	</div>
</div>
@endsection

@section('js-script')
<script type="text/javascript">
	document.title = "{{ _lang('Payslip').'_'.$payroll->staff->name.'_'.date('F', mktime(0, 0, 0, $payroll->month, 10)) }},{{ $payroll->year }}";
</script>
@endsection


