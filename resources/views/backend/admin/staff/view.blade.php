@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
		    <div class="card-header text-center">
				<span class="panel-title">{{ _lang('Staff Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
				    <tr><td colspan="2" class="bg-light"><b>{{ _lang('Personal Details') }}</b></td></tr>
				    <tr><td>{{ _lang('Employee ID') }}</td><td>{{ $employee->employee_id }}</td></tr>
					<tr><td>{{ _lang('First Name') }}</td><td>{{ $employee->first_name }}</td></tr>
					<tr><td>{{ _lang('Last Name') }}</td><td>{{ $employee->last_name }}</td></tr>
					<tr><td>{{ _lang('Fathers Name') }}</td><td>{{ $employee->fathers_name }}</td></tr>
					<tr><td>{{ _lang('Mothers Name') }}</td><td>{{ $employee->mothers_name }}</td></tr>
					<tr><td>{{ _lang('Date Of Birth') }}</td><td>{{ $employee->date_of_birth }}</td></tr>
					<tr><td>{{ _lang('Email') }}</td><td>{{ $employee->email }}</td></tr>
					<tr><td>{{ _lang('Phone') }}</td><td>{{ $employee->phone }}</td></tr>
					<tr><td>{{ _lang('City') }}</td><td>{{ $employee->city }}</td></tr>
					<tr><td>{{ _lang('State') }}</td><td>{{ $employee->state }}</td></tr>
					<tr><td>{{ _lang('Zip') }}</td><td>{{ $employee->zip }}</td></tr>
					<tr><td>{{ _lang('Country') }}</td><td>{{ $employee->country }}</td></tr>
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Company Details') }}</b></td></tr>
					<tr><td>{{ _lang('Department') }}</td><td>{{ $employee->department->name }}</td></tr>
					<tr><td>{{ _lang('Designation') }}</td><td>{{ $employee->designation->name }}</td></tr>
					<tr><td>{{ _lang('Salary Grade') }}</td><td class="d-flex align-items-center justify-content-between"><span>{{ $employee->salary_scale->salary_grade }}</span><a href="{{ route('salary_scales.show', $employee->salary_scale->id) }}" target="_blank" class="btn btn-outline-primary btn-xs">{{ _lang('View Details') }}</a></td></tr>
					<tr><td>{{ _lang('Basic Salary') }}</td><td>{{ formatAmount($employee->salary_scale->basic_salary, currency_symbol(request()->activeBusiness->currency)) }}</td></tr>
					<tr><td>{{ _lang('Joining Date') }}</td><td>{{ $employee->joining_date }}</td></tr>
					<tr><td>{{ _lang('End Date') }}</td><td>{{ $employee->end_date }}</td></tr>
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Bank Details') }}</b></td></tr>
					<tr><td>{{ _lang('Bank Name') }}</td><td>{{ $employee->bank_name }}</td></tr>
					<tr><td>{{ _lang('Branch Name') }}</td><td>{{ $employee->branch_name }}</td></tr>
					<tr><td>{{ _lang('Account Name') }}</td><td>{{ $employee->account_name }}</td></tr>
					<tr><td>{{ _lang('Account Number') }}</td><td>{{ $employee->account_number }}</td></tr>
					<tr><td>{{ _lang('Swift Code') }}</td><td>{{ $employee->swift_code }}</td></tr>
					<tr><td>{{ _lang('Remarks') }}</td><td>{{ $employee->remarks }}</td></tr>

					@if($employee->documents->count() > 0)
					<tr><td colspan="2" class="bg-light"><b>{{ _lang('Documents') }}</b></td></tr>
					@endif
					@foreach($employee->documents as $document)
					<tr>
						<td>{{ $document->name }}</td>
						<td><a href="{{ asset('public/uploads/documents/'.$document->document) }}" class="btn btn-xs btn-light"><i class="fas fa-download mr-2"></i>{{ _lang('Download') }}</a></td>
					</tr>
					@endforeach
			    </table>
			</div>
	    </div>

		<div class="card">
		    <div class="card-header text-center">
				<span class="panel-title">{{ _lang('Company Details Update History') }}</span>
			</div>
			
			<div class="card-body">
				<table class="table table-bordered">
					<thead>
						<th>{{ _lang('Date') }}</th>
						<th>{{ _lang('Deaprtment') }}</th>
						<th>{{ _lang('Designation') }}</th>
						<th>{{ _lang('Salary Scale') }}</th>
					</thead>
					<tbody>
					@foreach($employee->department_history as $history)
						<tr>
							<td>{{ $history->created_at }}</td>
							<td>{{ $history->details->department->name }}</td>
							<td>{{ $history->details->designation->name }}</td>
							<td>
								{{ $history->details->salary_scale->salary_grade }}</br>
								{{ _lang('Basic Salary').': '.formatAmount($history->details->salary_scale->basic_salary, currency_symbol(request()->activeBusiness->currency)) }}
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


