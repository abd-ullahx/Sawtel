<div class="row p-2">
	<div class="col-lg-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Employee ID') }}</td><td>{{ $leave->staff->employee_id }}</td></tr>
			<tr><td>{{ _lang('Leave Type') }}</td><td>{{ $leave->leave_type }}</td></tr>
			<tr>
				<td>{{ _lang('Leave Duration') }}</td>
				<td>{{ $leave->leave_duration == 'full_day' ? _lang('Full Day') : _lang('Half Day') }}</td>
			</tr>
			<tr><td>{{ _lang('Start Date') }}</td><td>{{ $leave->start_date }}</td></tr>
			<tr><td>{{ _lang('End Date') }}</td><td>{{ $leave->end_date }}</td></tr>
			<tr><td>{{ _lang('Total Days') }}</td><td>{{ $leave->total_days }}</td></tr>
			<tr><td>{{ _lang('Leave Details') }}</td><td>{{ $leave->description }}</td></tr>
			<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(leave_status($leave->status)) !!}</td></tr>
		</table>
	</div>
</div>

