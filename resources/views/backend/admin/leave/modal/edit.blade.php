<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('leaves.update', $id) }}" enctype="multipart/form-data">
	@csrf
	<input name="_method" type="hidden" value="PATCH">
	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Employee ID') }}</label>						
				<select class="form-control auto-select" data-selected="{{ $leave->employee_id }}" name="employee_id"  required>
					<option value="">{{ _lang('Select One') }}</option>
					@foreach(\App\Models\Employee::all() as $employee)
					<option value="{{ $employee->id }}">{{ $employee->employee_id }} ({{ $employee->name }})</option>
					@endforeach
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Leave Type') }}</label>						
				<select class="form-control select2-ajax auto-select" data-selected="{{ $leave->leave_type }}" data-table="leave_types" 
				data-value="title" data-display="title" data-where="3" name="leave_type" data-title="{{ _lang('New Leave Type') }}" data-href="{{ route('leave_types.create') }}" required>
					<option value="">{{ _lang('Select One') }}</option>
					<option value="{{ $leave->leave_type }}">{{ $leave->leave_type }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Leave Duration') }}</label>						
				<select class="form-control auto-select" data-selected="{{ $leave->leave_duration }}" name="leave_duration"  required>
					<option value="">{{ _lang('Select One') }}</option>
					<option value="full_day">{{ _lang('Full Day') }}</option>
					<option value="half_day">{{ _lang('Half Day') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
			<label class="control-label">{{ _lang('Start Date') }}</label>						
			<input type="text" class="form-control datepicker" name="start_date" value="{{ $leave->getRawOriginal('start_date') }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
			<label class="control-label">{{ _lang('End Date') }}</label>						
			<input type="text" class="form-control datepicker" name="end_date" value="{{ $leave->getRawOriginal('end_date') }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
			<label class="control-label">{{ _lang('Total Days') }}</label>						
			<input type="text" class="form-control" name="total_days" id="total_days" value="{{ $leave->total_days }}" readonly>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
			<label class="control-label">{{ _lang('Leave Details') }}</label>						
			<textarea class="form-control" name="description">{{ $leave->description }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Status') }}</label>						
				<select class="form-control auto-select" data-selected="{{ $leave->status }}" name="status" required>
					<option value="">{{ _lang('Select One') }}</option>
					<option value="0">{{ _lang('Pending') }}</option>
					<option value="1">{{ _lang('Approved') }}</option>
					<option value="2">{{ _lang('Cancelled') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-12 mt-2">
			<div class="form-group">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box mr-2"></i> {{ _lang('Update') }}</button>
		    </div>
		</div>
	</div>
</form>

