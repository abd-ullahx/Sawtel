@extends('layouts.app')

@section('content')
<div class="row">
	@if(!isset($employees))
	<div class="col-lg-4 offset-lg-4">
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Manage Attendance') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('attendance.create') }}" enctype="multipart/form-data">
					@csrf
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Date') }}</label>
								<input type="text" class="form-control datepicker" name="date" value="{{ old('date') }}" required>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary btn-block">{{ _lang('Next') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@else
	<div class="col-lg-10 offset-lg-1">
		@if($message != null)
		<div class="alert alert-danger">
			<strong>{{ $message }}</strong>
		</div>
		@endif
		<div class="card">
			<div class="card-header text-center">
				<span class="panel-title">{{ _lang('Manage Attendance') }}</span>
			</div>
			<div class="card-body">
			    <form method="post" class="validate" autocomplete="off" action="{{ route('attendance.store') }}" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="date" value="{{ $date }}">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<th>{{ _lang('Employee ID') }}</th>
										<th>{{ _lang('Name') }}</th>
										<th>{{ _lang('Status') }}</th>
										<th>{{ _lang('Leave/Absent Duration') }}</th>
										<th>{{ _lang('Remarks') }}</th>
									</thead>
									<tbody>
										@foreach($employees as $employee)
										<tr>
											<td>{{ $employee->employee_id }}</td>
											<td>{{ $employee->name }}</td>
											<td>
												<input type="hidden" name="employee_id[]" value="{{ $employee->id }}">
												<select class="form-control" name="status[]" required>
													<option value="0" {{ $employee->attendance_status == 0 ? 'selected' : '' }}>{{ _lang('Absent') }}</option>
													<option value="1" {{ $employee->attendance_status == 1 ? 'selected' : '' }}>{{ _lang('Present') }}</option>
													<option value="2" {{ $employee->leave_type != null ? 'selected' : '' }}>{{ _lang('Leave') }}</option>
												</select>
											</td>
											<td>
												<select class="form-control" name="leave_duration[]">
													<option value="">{{ _lang('Select One') }}</option>
													<option value="full_day" {{ $employee->leave_duration == 'full_day' || $employee->attendance_leave_duration == 'full_day' ? 'selected' : '' }}>{{ _lang('Full Day') }}</option>
													<option value="half_day" {{ $employee->leave_duration == 'half_day' || $employee->attendance_leave_duration == 'half_day' ? 'selected' : '' }}>{{ _lang('Half Day') }}</option>
												</select>
											</td>
											<td>
												<input type="hidden" name="leave_type[]" value="{{ $employee->leave_type }}">
												<textarea name="remarks[]" class="form-control">{{ $employee->attendance_remarks == null ? $employee->leave_description : $employee->attendance_remarks }}</textarea>
											</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>

						<div class="col-md-12 mt-2">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Submit') }}</button>
							</div>
						</div>
					</div>
			    </form>
			</div>
		</div>
    </div>
	@endif
</div>
@endsection


