<div class="row">
	<div class="col-lg-12">
		<table class="table table-bordered">
			<tr><td>{{ _lang('Employee ID') }}</td><td>{{ $award->staff->employee_id }}</td></tr>
			<tr><td>{{ _lang('Employee Name') }}</td><td>{{ $award->staff->name }}</td></tr>
			<tr><td>{{ _lang('Award Date') }}</td><td>{{ $award->award_date }}</td></tr>
			<tr><td>{{ _lang('Award Name') }}</td><td>{{ $award->award_name }}</td></tr>
			<tr><td>{{ _lang('Award Gift / Cash / Others') }}</td><td>{{ $award->award }}</td></tr>
			<tr><td>{{ _lang('Details') }}</td><td>{{ $award->details }}</td></tr>
		</table>
	</div>
</div>

