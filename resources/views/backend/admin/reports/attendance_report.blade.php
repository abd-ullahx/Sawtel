@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-10 offset-lg-1">
		<div class="card">
			<div class="card-header d-sm-flex align-items-center justify-content-between">
				<span class="panel-title">{{ _lang('Attendance Report') }}</span>
				<button class="btn btn-outline-primary btn-xs print" data-print="report" type="button" id="report-print-btn"><i class="fas fa-print mr-1"></i>{{ _lang('Print Report') }}</button>
			</div>

			<div class="card-body">

				<div class="report-params">
					<form class="validate" method="post" action="{{ route('reports.attendance_report') }}" autocomplete="off">
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
						<p>{{ _lang('Attendance Report') }}</p>
						<p>{{ isset($month) && isset($year) ? date('F', mktime(0, 0, 0, $month, 10)) .', '. $year : '' }}</p>
					</div>

					@if(isset($report_data))
					<div class="table-responsive">
						<table class="payslip-table w-100" border="1">
							<thead class="bg-light">
								<th>{{ _lang('Employee ID') }}</th>
								<th>{{ _lang('Name') }}</th>
								@for($day = 1; $day <= $calendar; $day++)
								<th>{{ $day }}</th>
								@endfor
							</thead>
							<tbody>
								@foreach($employees as $employee)
									<tr>
										<td>{{ $employee->employee_id }}</td>
										<td>{{ $employee->name }}</td>
										@for($day = 1; $day <= $calendar; $day++)
											@if(isset($report_data[$employee->id]))
											<td>{{ isset($report_data[$employee->id][$day]) ? $report_data[$employee->id][$day] : '' }}</td>
											@else
											<td></td>
											@endif
										@endfor				
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>

					<!--Legend-->
					<div class="row">
						<div class="col-md-6">
							<table class="payslip-table mt-4" border="1">
								<thead class="bg-light">
									<th>A</th>
									<th>P</th>
									<th>L</th>
									<th>W</th>
									<th>H</th>
								</thead>
								<tbody>
									<tr>
										<td>{{ _lang('Absent') }}</td>
										<td>{{ _lang('Present') }}</td>
										<td>{{ _lang('Leave') }}</td>
										<td>{{ _lang('Weekend') }}</td>
										<td>{{ _lang('Holiday') }}</td>
									</tr>
								</thead>
							</table>
						</div>
						<div class="col-md-6">
							<table class="payslip-table mt-4 float-md-right" border="1">
								<thead class="bg-light">
									<th>{{ _lang('Working Days') }}</th>
								</thead>
								<tbody>
									<tr>
										<td class="text-center">{{ $attendance_list->groupBy('date')->count() }}</td>
									</tr>
								</thead>
							</table>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection