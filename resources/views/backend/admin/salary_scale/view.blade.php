@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
		    <div class="card-header">
				<span class="panel-title">{{ _lang('Salary Scale Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td>{{ _lang('Department') }}</td><td>{{ $salaryscale->department->name }}</td></tr>
					<tr><td>{{ _lang('Designation') }}</td><td>{{ $salaryscale->designation->name}}</td></tr>
					<tr><td>{{ _lang('Grade Number') }}</td><td>{{ _lang('Grade').' '.$salaryscale->grade_number }}</td></tr>
					<tr><td>{{ _lang('Basic Salary') }}</td><td>{{ formatAmount($salaryscale->basic_salary, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Full Day Absence Fine') }}</td><td>{{ formatAmount($salaryscale->full_day_absence_fine, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Full Day Absence Fine') }}</td><td>{{ formatAmount($salaryscale->half_day_absence_fine, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
			    </table>
			</div>
	    </div>
	</div>

	<div class="col-lg-5 offset-lg-1">
		<div class="card">
			<div class="card-header">
				<span class="panel-title text-success font-weight-bold">{{ _lang('Allowances') }}</span>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="allowances">
					<thead class="bg-white">
						<th class="text-dark">{{ _lang('Name') }}</th>
						<th class="text-dark text-right">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@foreach($salaryscale->salary_benefits()->where('type','add')->get() as $allowances)
						<tr>
							<td>{{ $allowances->name }}</td>
							<td class="text-right">{{ formatAmount($allowances->amount, currency_symbol(request()->activeBusiness->currency)) }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div class="col-lg-5">
		<div class="card">
			<div class="card-header d-flex justify-content-between">
				<span class="panel-title text-danger font-weight-bold">{{ _lang('Deductions') }}</span>
			</div>
			<div class="card-body">
				<table class="table table-bordered" id="deductions">
					<thead class="bg-white">
						<th class="text-dark">{{ _lang('Name') }}</th>
						<th class="text-dark text-right">{{ _lang('Amount') }}</th>
					</thead>
					<tbody>
						@foreach($salaryscale->salary_benefits()->where('type','deduct')->get() as $deductions)
						<tr>
							<td>{{ $deductions->name }}</td>
							<td class="text-right">{{ formatAmount($deductions->amount, currency_symbol(request()->activeBusiness->currency)) }}</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endsection


